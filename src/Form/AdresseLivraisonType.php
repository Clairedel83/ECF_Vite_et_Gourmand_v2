<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Validator\Constraints as Assert;
use Symfony\Component\OptionsResolver\OptionsResolver;

class AdresseLivraisonType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('nom', TextType::class, [
                'label' => 'Nom :',
                'constraints' => [
                    new Assert\Length(
                        min:2,
                        max:30,
                        minMessage:"Le nom doit contenir au moins {{ limit }} caractères.",
                        maxMessage:"Le nom doit contenir au maximum {{ limit }} caractères."
                    )],
                'attr' => [
                    'placeholder' => "Nom",
                    'minlength' => 2,
                    'maxlength' => 30
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
                    'placeholder' => "Prénom",
                    'minlength' => 2,
                    'maxlength' => 30
                ]
            ])

            ->add('ville', TextType::class, [
                'label' => 'Ville :',
                'attr' => [
                    'placeholder' => "Ville",
                    'class' => 'input'
                ]
            ])

            ->add('code_postal', TextType::class, [
                'label' => 'Code postal :',
                'attr' => [
                    'placeholder' => "33000",
                    'pattern' => '[0-9]{5}',
                    'maxlength' => 5
                ],
                'constraints' => [
                    new Assert\Regex(
                        pattern: '/^[0-9]{5}$/',
                        message: 'Le code postal doit contenir 5 chiffres.'
                    )
                ]
            ])
            
            ->add('adresse_postale', TextType::class, [
                'label' => 'Adresse postale :',
                'attr' => [
                    'placeholder' => "Adresse postale"
                ]
            ])

            ->add('submit', SubmitType::class, [
                'label' => "Sauvegarder les modifications",
                'attr' => [
                    'class' => 'btn2',
                    'id' => 'valider_adresse'
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
