<?php

namespace App\Form;

use App\Entity\FieldColor;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class FieldColorType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('fieldName', TextType::class, [
                'label' => 'Nom du champ',
                'attr' => ['readonly' => true], // Rendre ce champ non modifiable
            ])
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
            'data_class' => FieldColor::class,
        ]);
    }
}
