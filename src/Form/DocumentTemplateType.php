<?php

namespace App\Form;

use App\Entity\Associate;
use App\Entity\Document;
use App\Entity\Societe;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class DocumentTemplateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('title')
            ->add('file')
            ->add('description')
            ->add('signatureSociete')
            ->add('fields')
            ->add('societe', EntityType::class, [
                'class' => Societe::class,
                'choice_label' => 'id',
            ])
            ->add('associate', EntityType::class, [
                'class' => Associate::class,
                'choice_label' => 'id',
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
