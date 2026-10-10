<?php

namespace App\Form;

use App\Entity\User;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TelType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class InformationsUserType extends AbstractType
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
                        maxMessage:"Le nom doit contenir au moins {{ limit }} caractères."
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
                        maxMessage:"Le prénom doit contenir au maximum {{ limit }} caractères."
                    )],
                'attr' => [
                    'placeholder' => "Prénom",
                    'minlength' => 2,
                    'maxlength' => 30
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
                    'class' => 'btn2 nav_item form_footer'
                ]
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => User::class,

            // Symfony ajoute un champ caché au formulaire pour vérifier qu'il vient bien de notre site (puisqu'il s'agit de données sensibles)
            // Le réglage par défaut de ce champ empêchait la validation du formulaire (csrf.yaml)
            // Ce nom propre au formulaire (modifier_informations_user) permet à Symfony de faire la vérification avec la session de l'utilisateur sinon il bloquait la modification des données
            'csrf_token_id' => 'modifier_informations_user',
        ]);
    }
}
