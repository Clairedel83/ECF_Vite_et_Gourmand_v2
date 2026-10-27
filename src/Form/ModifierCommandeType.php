<?php

namespace App\Form;

use App\Entity\Commande;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\DateType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\Extension\Core\Type\TimeType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints as Assert;

class ModifierCommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('dateLivraison', DateType::class, [
                'label' => 'Date de livraison',
                'widget' => 'single_text',
                'input' => 'datetime',
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Veuillez renseigner la date de livraison.'
                    ),
                ],
                'attr' => [
                    'class' => 'nouvelleDate',
                ],
            ])

            ->add('heureLivraison', TimeType::class, [
                'label' => 'Heure de livraison',
                'widget' => 'single_text',
                'input' => 'datetime',
                'with_seconds' => false,
                'required' => true,
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Veuillez renseigner l’heure de livraison.'
                    ),
                ],
            ])

            ->add('adresseLivraison', TextareaType::class, [
                'label' => 'Adresse de livraison',
                'required' => true,
                'trim' => true,
                'attr' => [
                    'rows' => 2,
                    'class' => 'nouvelleAdresse'
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Veuillez renseigner l’adresse de livraison.'
                    ),
                    new Assert\Length(
                        max: 255,
                        maxMessage: 'L’adresse ne doit pas dépasser {{ limit }} caractères.'
                    ),
                ],
            ])

            ->add('nbrePers', IntegerType::class, [
                'label' => 'Nombre de convives',
                'required' => true,
                'attr' => [
                    'min' => $options['minimum_convives'],
                    'step' => 1,
                    'class' => 'nouveauNbre'
                ],
                'constraints' => [
                    new Assert\NotBlank(
                        message: 'Veuillez renseigner le nombre de convives.'
                    ),
                    new Assert\GreaterThanOrEqual(
                        value: $options['minimum_convives'],
                        message: 'Le nombre de convives doit être au minimum de {{ compared_value }}.'
                    ),
                ],
            ])

            ->add('pretMateriel', CheckboxType::class, [
                'label' => 'Prêt de matériel',
                'required' => false,
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => Commande::class,
        ]);

        $resolver->setRequired('minimum_convives');
        $resolver->setAllowedTypes('minimum_convives', 'int');
    }
}
