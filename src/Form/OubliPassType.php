<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;

class OubliPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Votre adresse email :',
                'help' => 'Un lien permettant de choisir un nouveau mot de passe vous sera envoyé par email.',
                'attr' => [
                        'placeholder' => "exemple@email.com",
                        'class' => 'form_user_content'
                    ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Réinitialiser',
                'attr' => [
                    'class' => 'btn2'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            // Configure your form options here
        ]);
    }
}
