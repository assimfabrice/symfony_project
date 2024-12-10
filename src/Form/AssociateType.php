<?php

namespace App\Form;

use App\Entity\Associate;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\NotBlank;

class AssociateType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('civility', ChoiceType::class, [
                'choices' => $this->getChoices(),
            ])
            ->add('firstname', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('lastname', TextType::class, [
                'constraints' => [
                    new NotBlank()
                ]
            ])
            ->add('address')
            ->add('city')
            ->add('country')
            ->add('dateOfBirth', null, [
                'widget' => 'single_text',
                'empty_data' => (new \DateTime())->format('Y-m-d'),
            ])
            ->add('nationality')
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Associate::class,
            'translation_domain' => 'forms',
        ]);
    }

    private function getChoices()
    {
        $choices = Associate::CIVILITY;
        $output = [];

        foreach($choices as $k => $v)
        {
            $output[$v] = $k;
        }

        return $output;
    }
}
