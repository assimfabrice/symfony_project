<?php

namespace App\Form;

use App\Entity\CompanyType;
use App\Entity\Societe;
use App\Form\Custom\CompanyTypeChoiceType;
use App\Repository\CompanyTypeRepository;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class SocieteType extends AbstractType
{
    public function __construct(private readonly CompanyTypeRepository $companyTypeRepository)
    {

    }

    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $companyTypes = $this->companyTypeRepository->findAll();
        $dataCompanyType = [];

        foreach($companyTypes as $key => $value) {
            if(is_object($value)) {
                $dataCompanyType[$value->getName()] = $value->getId();
            }
        }
        $builder
            ->add('companyType',ChoiceType::class, [
                'choices' => $dataCompanyType,
            ])
            ->add('theNameOfTheCompany')
            ->add('addressOfTheCompany')
            ->add('postalCode')
            ->add('city')
            ->add('rcsNumber')
            ->add('siren')
            ->add('siretOfTheCompany')
            ->add('activity')
            ->add('nameOfTheAbsorbingCompany')
            ->add('IdentificationNumber')
            ->add('representativeOfTheCompany')
            ->add('AddressOfTheLegalRepresentative')
            ->add('postalCodeOfTheLegalRepresentative')
            ->add('cityOfTheLegalRepresentative')
            ->add('countryOfTheLegalRepresentative')
            ->add('dateOfBirthOfTheLegalRepresentative', null, [
                'widget' => 'single_text',
                'html5' => false,           // Désactive le datepicker HTML5
                'attr' => ['class' => 'js-flatpickr'],
            ])
            ->add('nationalityOfTheLegalRepresentative')
            ->add('dayMonthYearOfDissolution', null, [
                'widget' => 'single_text',
                'html5' => false,           // Désactive le datepicker HTML5
                'attr' => ['class' => 'js-flatpickr'],
            ])
            ->add('numberOfShares')
            ->add('amountOfAShare')
            ->add('amountOfPassiveSociete')
            ->add('amountOfActiveSociete')
            ->add('signaturePath', HiddenType::class, [
                'required' => true,
                'attr' => [
                    'id' => 'societe_signaturePath',
                ],
            ]);
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Societe::class,
            'translation_domain' => 'forms',
        ]);
    }
}
