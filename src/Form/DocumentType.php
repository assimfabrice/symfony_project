<?php

namespace App\Form;

use App\Entity\Associate;
use App\Entity\Document;
use App\Entity\Societe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\CollectionType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use FOS\CKEditorBundle\Form\Type\CKEditorType;
use App\Entity\CompanyType;

class DocumentType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title', TextType::class, ['label' => 'Titre du document'])
            ->add('companyType', EntityType::class, [
                'class' => CompanyType::class,
                'choice_label' => 'name',
                'multiple' => false,

            ])
            ->add('paragraphe', CKEditorType::class, [
                'label' => 'Paragraphe principal',
                'attr' => [
                    'class' => 'form-control',
                    'placeholder' => 'Saisissez le contenu principal ici'
                ],
                'required' => false,
            ])
            ->add('paragraphes', CollectionType::class, [
                'entry_type' => CKEditorType::class,
                'entry_options' => [
                    'label' => 'Paragraphe dynamique',
                    'attr'  => [
                        'class' => 'ckeditor-dynamic',
                    ],
                ],
                'label' => false,
                'allow_add' => true,
                'allow_delete' => true,
                'by_reference' => false,
                'prototype' => true,
                'prototype_name' => '__name__',
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Document::class,
        ]);
    }
}
