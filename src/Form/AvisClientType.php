<?php

namespace App\Form;

use App\Entity\Avis;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class AvisClientType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('note', ChoiceType::class, [
                'label' => 'Note : ',
                'choices' => [
                    '1' => 1,
                    '2' => 2,
                    '3' => 3,
                    '4' => 4,
                    '5' => 5
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        message : 'Veuillez choisir une note.'
                    ),
                ]
            ])
            ->add('contenu', TextareaType::class, [
                'label' => 'Commentaire',
                'constraints' => [
                    new Assert\NotBlank(
                        message : 'Veuillez écrire un commentaire'
                    ),
                    new Assert\Length(
                        min:20,
                        max:500,
                        minMessage: 'Votre avis doit contenir au moins {{ limit }} caractères.',
                        maxMessage:'Votre avis doit contenir au maximum {{ limit }} caractères.'
                    )
                ]
            ])
            ->add('submit', SubmitType::class, [
                'label' => 'Envoyer',
                'attr' => [
                    'class' => 'btn2 nav_item'
                ]
            ]);
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Avis::class,
        ]);
    }
}
