<?php

namespace App\Form;

use App\Entity\Commande;
use App\Entity\InterventionCommande;
use App\Entity\User;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\TextareaType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\Validator\Constraints\IsTrue;
use Symfony\Component\Validator\Constraints\NotBlank;

class InterventionCommandeType extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options): void
    {
        $builder
            ->add('modeContact', ChoiceType::class, [
                'label' => 'Moyen de contact :',
                'required' => true,
                'choices' => [
                    'Téléphone' => 'telephone',
                    'Email' => 'email',
                ],
                'placeholder' => 'Sélectionnez un moyen de contact',
                'constraints' => [
                    new NotBlank(
                        message: 'Veuillez sélectionner un moyen de contact.'
                    ),
                ],
            ])

            ->add('motif', TextareaType::class, [
                'label' => 'Motif de l\'annulation',
                'required' => true,
                'attr' => [
                    'placeholder' => 'Précisez la raison de l\'annulation'
                ],
                'constraints' => [
                    new NotBlank(message:'Veuillez renseigner le motif de l\'annulation.'),
                ],
            ])

            ->add('clientContacte', CheckboxType::class, [
                'label' => 'Je confirme avoir contacté le client.',
                'required' => true,
                // la case n’est pas liée à une propriété de l'entité
                'mapped' => false,
                // vérifie côté serveur qu’elle est cochée
                'constraints' => [
                    new IsTrue(message: 'Vous devez confirmer avoir contacté le client.'),
                ],
            ])
        ;
    }

    public function configureOptions(OptionsResolver $resolver): void
    {
        $resolver->setDefaults([
            'data_class' => InterventionCommande::class,
        ]);
    }
}
