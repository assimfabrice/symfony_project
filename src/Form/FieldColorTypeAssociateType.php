<?php

namespace App\Form;

use App\Entity\FieldColorAssociate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldColorTypeAssociateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('emptyColor', TextType::class, [
                'label' => 'Couleur pour champ vide',
                'attr' => ['class' => 'jscolor'], // Classe jscolor
            ])
            ->add('filledColor', TextType::class, [
                'label' => 'Couleur pour champ rempli',
                'attr' => ['class' => 'jscolor'], // Classe jscolor
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
            'data_class' => FieldColorAssociate::class,
        ]);
    }
}
