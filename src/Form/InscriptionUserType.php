<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\Form\Extension\Core\Type\RepeatedType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Bridge\Doctrine\Validator\Constraints\UniqueEntity;

class InscriptionUserType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('email', EmailType::class, [
                'label' => 'Adresse email :',
                'attr' => [
                    'placeholder' => "Adresse email"
                ]
            ])
            ->add('password', RepeatedType::class, [
                'type' => PasswordType::class, 
                'first_options' => [
                    'label' => 'Mot de passe :', 
                    'hash_property_path' => 'password',
                    'attr' => [
                        'placeholder' => "**************"
                    ]
                ],
                'second_options' => [
                    'label' => 'Confirmez le mot de passe :',
                    'attr' => [
                        'placeholder' => "**************"
                    ]
                ], 
                'mapped' => false,
            ])

            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                'constraints' => [
                    new Assert\Length(
                        min:2,
                        max:30,
                        minMessage:"Le nom doit contenir au moins {{ limit }} caractères.",
                        maxMessage:"Le nom doit contenir au moins {{ limit }} caractères."
                    )],
                'attr' => [
                    'placeholder' => "Nom"
                ]
            ])
            ->add('prenom', TextType::class, [
                'label' => 'Prénom :',
                'constraints' => [
                    new Assert\Length(
                        min:2,
                        max:30,
                        minMessage:"Le prénom doit contenir au moins {{ limit }} caractères.",
                        maxMessage:"Le prénom doit contenir au moins {{ limit }} caractères."
                    )],
                'attr' => [
                    'placeholder' => "Prénom"
                ]
            ])
            ->add('telephone', TelType::class, [
                'label' => 'Téléphone :',
                'attr' => [
                    'placeholder' => "Téléphone"
                ]
            ])
            ->add('ville', TextType::class, [
                'label' => 'Ville :',
                'attr' => [
                    'placeholder' => "Ville",
                    'class' => 'input'
                ]
            ])
            ->add('adresse_postale', TextType::class, [
                'label' => 'Adresse postale :',
                'attr' => [
                    'placeholder' => "Adresse postale"
                ]
            ])

            ->add('submit', SubmitType::class, [
                'label' => 'S\'inscrire',
                'attr' => [
                    'class' => 'btn2 btn-menu'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,
            'contraints' => [
                new UniqueEntity([
                    'entityClass' => User::class,
                    'fields' => 'email'
                    // l'email ne pourra pas être réutilisé lors d'une autre inscription
                ])
            ],
        ]);
    }
}
