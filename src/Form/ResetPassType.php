<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\FormError;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ResetPassType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('plainPassword', RepeatedType::class, [
                'type' => PasswordType::class, 

                'invalid_message' => 'Les mots de passe saisis ne correspondent pas.',

                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Veuillez renseigner un nouveau mot de passe.'
                    ),

                    new Assert\Length(
                        min:10,
                        minMessage:'Le mot de passe doit contenir au moins 10 caractères.'
                    ),

                    new Assert\Regex(
                        pattern: '/^(?=.*[A-Z])(?=.*[a-z])(?=.*[0-9])(?=.*[+!@#$%^&*]).+$/',
                        message:'Le mot de passe doit contenir au moins une majuscule, une minuscule, un chiffre et caractère spécial.'
                    ),
                    ],
                'first_options' => [
                    'label' => 'Mot de passe :', 
                    'hash_property_path' => 'password',
                    // permet de dire à symfony que le mot de passe doit être hashé et stocké dans la propriété "password" de l'entité User
                    'attr' => [
                        'placeholder' => "**************",
                        'class' => 'form_user_content'
                    ]
                    
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe :',
                    'attr' => [
                        'placeholder' => "**************",
                        'class' => 'form_user_content'
                    ]
                ], 
                'mapped' => false,
            ])
            
            ->add('submit', SubmitType::class, [
                'label' => 'Modifier le mot de passe',
                'attr' => [
                    'class' => 'btn2 nav_item form_footer'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
        ]);
    }
}
