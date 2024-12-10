<?php

namespace App\Command;

use App\Entity\FieldColor;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;

#[AsCommand(
    name: 'app:init-field-colors',
    description: 'Add a short description for your command',
)]
class InitFieldColorsCommand extends Command
{
    public function __construct(private readonly EntityManagerInterface $entityManager)
    {
        parent::__construct();
    }


    protected function execute(InputInterface $input, OutputInterface $output): int
    {
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
            $this->entityManager->persist($fieldColor);
        }

        $this->entityManager->flush();

        $output->writeln('Champs initialisés avec leurs couleurs.');
        return Command::SUCCESS;
    }
}
