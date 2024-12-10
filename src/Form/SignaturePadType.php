<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\Extension\Core\Type\HiddenType;
use Symfony\Component\Form\FormInterface;
class SignaturePadType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        // Champ caché pour stocker la signature en base64
        $builder->add('signature', HiddenType::class, [
            'attr' => ['class' => 'signature-field']
        ]);
    }
    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        // Ajouter un identifiant pour le canvas de signature
        $view->vars['canvas_id'] = 'signature-pad-' . uniqid();
        //$view->vars['tags'] = $this->tags->findAll();
    }
    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'mapped' => false, // Si vous mappez manuellement
        ]);
    }
}
