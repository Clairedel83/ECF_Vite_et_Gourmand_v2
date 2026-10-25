<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Entity\CommandeHistorique;
use App\Form\StatutType;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use Override;

class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
        ;
    }

    #[Override]
    public function configureActions(Actions $actions): Actions
    {
        $details = Action::new('Détails')->linkToCrudAction('details');

        return $actions
            // rend impossible de supprimer ou ajouter manuellement un historique
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)

            // ajoute la possibilité d'afficher les détails de commande
            ->add(Crud::PAGE_INDEX, $details);
    }

    // pour afficher la vue des détails de commande :
    #[AdminRoute('/{numero_commande/details}')]
    public function details(AdminContext $context)
    {
        // permet d'afficher l'ensemble des éléments de l'entité Commande
        $commande = $context->getEntity()->getInstance();

        // permet de rattacher le formulaire de modification du statut
        $form = $this->createForm(StatutType::class, $commande);

        // crée la vue de cet affichage
        return $this->render('admin/commande_details.html.twig', [
            'commande' => $commande,
            'statutForm' => $form->createView(),
        ]);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('numero_commande')->setLabel('N° de commande')->hideOnForm(),
            AssociationField::new('user')->setLabel('Client')->hideOnForm(),
            // DateTimeField::new('date_commande')->setLabel('Date de création')->setFormat('dd/MM/yyyy HH:mm')->onlyOnDetail(),
            DateField::new('dateLivraison', 'Livraison')->hideOnForm()
                // associe heure et date de livraison dans la même colonne
                ->formatValue(function ($value, $commande) {
                    return $commande->getDateLivraison()->format('d/m/Y')
                        . ' à '
                        . $commande->getHeureLivraison()->format('H:i');
                }),
            ChoiceField::new('statut')
                ->setLabel('Statut de commande')
                ->setChoices([
                    '1 - En attente de validation' => 1,
                    '2 - Commande acceptée' => 2,
                    '3 - En préparation' => 3,
                    '4 - En cours de livraison' => 4,
                    '5 - Commande livrée' => 5,
                    '6 - En attente du retour de matériel' => 6,
                    '7 - Commande terminée' => 7,
                    '8 - Commande annulée' => 8,
                ])
                ->setTemplatePath('admin/statut.html.twig'),
            TextField::new('menu_nom')->setLabel('Menu')->hideOnForm(),
        ];
    }

    // pour permettre le changement de statut
    /** @var Commande $entityInstance */
    #[Override]
    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        // Récupère les données de la commande avant modification
        $donneesEnregistrees = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);

        // Récupère l'ancien et le nouveau statut
        $ancienStatut = $donneesEnregistrees['statut'];
        $nouveauStatut = $entityInstance->getStatut();

        if ($ancienStatut !== $nouveauStatut) {
            // CREATION D'UN NOUVEL HISTORIQUE DE COMMANDE
            $historiqueCommande = new CommandeHistorique();

            // attribue le statut à l'historique de commande
            $historiqueCommande->setStatut($nouveauStatut);

            // attribue la date et l'heure de modification du statut
            $date_actuelle = new \DateTime();

            $historiqueCommande->setDateModification($date_actuelle);
            $historiqueCommande->setHeureModification($date_actuelle);

            // lie l'historique à la commande
            $entityInstance->addCommandeHistorique($historiqueCommande);

            // enregistre les modifications en BDD
            $entityManager->persist($historiqueCommande);
        }

        // Enregistre les modifications
        parent::updateEntity($entityManager, $entityInstance);
    }
}
