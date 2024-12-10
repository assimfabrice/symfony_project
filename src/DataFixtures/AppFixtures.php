<?php

namespace App\DataFixtures;

use App\Entity\CompanyType;
use App\Entity\Document;
use App\Entity\FieldColor;
use App\Entity\FieldColorAssociate;
use App\Entity\Societe;
use App\Entity\SocieteDocument;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Faker\Factory;

class AppFixtures extends Fixture
{
    public function __construct(private readonly UserPasswordHasherInterface $hasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        //initialisation du faker
        $faker = Factory::create('fr_FR');
        // Création de plusieurs types de sociétés
        $companyTypes = [];
        $typeNames = ['SASU', 'SARL','Startup', 'PME', 'Grande Entreprise', 'Association'];

        foreach ($typeNames as $typeName) {
            $companyType = new CompanyType();
            $companyType->setName($typeName);
            $manager->persist($companyType);
            $companyTypes[] = $companyType; // Stockage pour utilisation ultérieure
            $this->addReference($typeName, $companyType);
        }

        // Création de plusieurs sociétés
        $societes = [];
        for ($i = 1; $i <= 10; $i++) {
            $societe = new Societe();
/*            $societe->setActivity($faker->sentence)
                ->setTheNameOfTheCompany($faker->name())
                ->setAddressOfTheCompany($faker->address())
                ->setPostalCode($faker->postcode())
                ->setCity($faker->city())
                ->setRcsNumber($faker->creditCardNumber())
                ->setSiren($faker->creditCardNumber)
                ->setSiretOfTheCompany($faker->creditCardNumber)
                ->setActivity($faker->sentence(15))
                ->setNameOfTheAbsorbingCompany($faker->name())
                ->setRepresentativeOfTheCompany($faker->name())
                ->setIdentificationNumber($faker->creditCardNumber)
                ->setCountryOfTheLegalRepresentative($faker->country())
                ->setAddressOfTheLegalRepresentative($faker->address())
                ->setPostalCodeOfTheLegalRepresentative($faker->postcode())
                ->setCityOfTheLegalRepresentative($faker->name())
                ->setDateOfBirthOfTheLegalRepresentative($faker->dateTime)
                ->setNationalityOfTheLegalRepresentative($faker->city())
                ->setDayMonthYearOfDissolution($faker->dateTime)
                ->setAmountOfAShare($faker->numberBetween(1000, 20000))
                ->setAmountOfPassiveSociete($faker->numberBetween(1000, 5000))
                ->setSignaturePath($faker->sentence(5000))
                ->setCompanyType($this->getReference($faker->randomElement($typeNames)))
                ->setNumberOfShares($faker->numberBetween(2, 10))
                ->setAmountOfActiveSociete($faker->numberBetween(1000, 200000));

            $manager->persist($societe);
            $societes[] = $societe;*/
        }

        // Création de documents avec des paragraphes très longs
        $documents = [];
        foreach ($companyTypes as $companyType) {
            for ($i = 1; $i <= 3; $i++) {
                $document = new Document();
                $document->setTitle($faker->sentence(6)) // Titre réaliste
                ->setFields($faker->randomElements(['field1', 'field2', 'field3'], 2)) // Champs aléatoires
                ->setParagraphe($faker->paragraph(10)) // Paragraphe très long (10 phrases)
                ->setParagraphes([
                    $faker->paragraph(8),
                    $faker->paragraph(8),
                    $faker->paragraph(8),
                ]) // Tableau de paragraphes longs
                ->setCompanyType($companyType);
                $manager->persist($document);
                $documents[] = $document;
            }
        }

        // Association entre Sociétés et Documents via SocieteDocument
        /*foreach ($societes as $societe) {
            foreach ($documents as $document) {
                // Limite pour éviter un trop grand nombre d'associations
                if ($faker->boolean(30)) { // 30% de chance d'associer un document à une société
                    $societeDocument = new SocieteDocument();
                    $societeDocument->setSociete($societe);
                    $societeDocument->setCreatedAt(new \DateTimeImmutable());
                    $societeDocument->setDocument($document);
                    $manager->persist($societeDocument);
                }
            }
        }*/
        //création d'utilisateur
        $user = new User();
        $user->setRoles(['ROLE_ADMIN'])
             ->setEmail('admin@service.com')
             ->setPassword($this->hasher->hashPassword($user, 'admin'));
        $manager->persist($user);

        //ajouter plusieurs utilisateurs
        for($i = 0; $i < 10; $i++) {
            $user = new User();
            $user->setRoles(['ROLE_USER'])
                 ->setEmail("user{$i}@doe.fr")
                 ->setPassword($this->hasher->hashPassword($user, '0000'));
            $manager->persist($user);
        }
        //couleur champs personnalisés
        $fields = [
            'societe[theNameOfTheCompany]',
            'societe[addressOfTheCompany]',
            'societe[city]',
            'societe[postalCode]',
            'societe[rcsNumber]',
            'societe[siren]',
            'societe[siretOfTheCompany]',
            'societe[activity]',
            'societe[nameOfTheAbsorbingCompany]',
            'societe[IdentificationNumber]',
            'societe[representativeOfTheCompany]',
            'societe[addressOfTheLegalRepresentative]',
            'societe[postalCodeOfTheLegalRepresentative]',
            'societe[cityOfTheLegalRepresentative]',
            'societe[countryOfTheLegalRepresentative]',
            'societe[dateOfBirthOfTheLegalRepresentative]',
            'societe[nationalityOfTheLegalRepresentative]',
            'societe[dayMonthYearOfDissolution]',
            'societe[numberOfShares]',
            'societe[amountOfAShare]',
            'societe[amountOfTheCompany]',
            'societe[amountOfPassiveSociete]',
            'societe[amountOfActiveSociete]'
        ]; // Ajouter les champs à personnaliser

        foreach ($fields as $field) {
            $fieldColor = new FieldColor();
            $fieldColor->setFieldName($field);
            $fieldColor->setEmptyColor('#ff0000'); // Par défaut : rouge pour vide
            $fieldColor->setFilledColor('#00ff00'); // Par défaut : vert pour rempli
            $manager->persist($fieldColor);
        }

        //champ couleurs formulaire associé
        $fieldColorAssociate = new FieldColorAssociate();
        $fieldColorAssociate->setEmptyColor('#ff0000'); // Par défaut : rouge pour vide
        $fieldColorAssociate->setFilledColor('#00ff00'); // Par défaut : vert pour rempli
        $manager->persist($fieldColorAssociate);
        $manager->flush();
    }
}
