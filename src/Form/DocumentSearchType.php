<?php

namespace App\Form;

use App\Entity\DocumentSearch;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use App\Entity\CompanyType;
use App\Entity\Societe;

class DocumentSearchType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, [
                'label' => 'Titre du document',
                'required' => false,
                'attr' => ['placeholder' => 'Rechercher par titre']
            ])
            ->add('companyType', EntityType::class, [
                'class' => CompanyType::class,
                'placeholder' => 'Tous les types de société',
                'attr' => [
                    'placeholder' => 'Sélectionnez un type de société',
                    'class' => 'js-tom-select', // Classe spécifique pour Tom Select
                ],
                'label' => 'Type de société',
                'mapped' => true,
                'choice_label' => 'name',
                'required' => false,
                'multiple' => true,
                'expanded' => false,
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => DocumentSearch::class,
            'method' => 'GET',
            'csrf_protection' => false
        ]);
    }

    public function getBlockPrefix()
    {
        return '';
    }
}
