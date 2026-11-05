<?php

namespace App\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Component\Validator\Constraints as Assert;

class ContactType extends AbstractType
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
                    ),
                    new Assert\NotBlank(
                        message: 'Veuillez renseigner votre nom.'
                    )],
                'attr' => [
                    'placeholder' => "ex : Dupont",
                    'minlength' => 2,
                    'maxlength' => 30
                ]
            ])
            ->add('sujet', TextType::class, [
                'label' => 'Sujet :',
                'constraints' => [
                    new Assert\Length(
                        min:2,
                        max:30,
                        minMessage:"Le sujet doit contenir au moins {{ limit }} caractères.",
                        maxMessage:"Le sujet doit contenir au maximum {{ limit }} caractères."
                    ),
                    new Assert\NotBlank(
                        message: 'Veuillez renseigner le sujet de votre demande.'
                    )],
                'attr' => [
                    'placeholder' => "ex: Réception baptême",
                    'minlength' => 2,
                    'maxlength' => 30
                ]
            ])
            ->add('email', EmailType::class, [
                'label' => 'Adresse email :',
                'attr' => [
                    'placeholder' => "exemple@email.com"
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Veuillez renseigner votre adresse email.'
                    ),
                    new Assert\Email(
                        message: 'Veuillez renseigner une adresse email valide.'
                    ),
                ],
            ])
            ->add('message', TextareaType::class, [
                'label' => 'Message :',
                'attr' => [
                    'placeholder' => 'Décrivez votre demande ou posez votre question.',
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Veuillez préciser votre demande.'
                    )],
                    new Assert\Length(
                        min: 30,
                        max: 5000,
                        minMessage: 'Le message doit contenir au moins {{ limit }} caractères.',
                        maxMessage: 'Le message doit contenir au maximum {{ limit }} caractères.'
                    ),
                ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn2 btn_menu'
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
