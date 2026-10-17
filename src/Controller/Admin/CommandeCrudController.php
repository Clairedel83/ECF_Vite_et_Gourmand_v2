<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
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
        $afficher = Action::new('Afficher')->linkToCrudAction('afficher');

        return $actions
        // rend impossible de supprimer ou ajouter manuellement un historique
        ->remove(Crud::PAGE_INDEX, Action::NEW)
        ->remove(Crud::PAGE_INDEX, Action::DELETE)

        // ajoute la possibilité d'afficher les détails de commande
        ->add(Crud::PAGE_INDEX, $afficher);
    }

    // pour afficher la vue des détails de commande :
    #[AdminRoute('/{numero_commande/afficher}')]
    public function afficher(AdminContext $context)
    {
        // permet d'afficher l'ensemble des éléments de l'entité Commande
        $commande = $context->getEntity()->getInstance();

        // crée la vue de cet affichage
        return $this->render('admin/commande_details.html.twig', [
            'commande' => $commande
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
            ChoiceField::new('statut')->setLabel('Statut de commande')->setTemplatePath('admin/statut.html.twig'),
            TextField::new('menu_nom')->setLabel('Menu')->hideOnForm(),
            // TextField::new('adresse_livraison')->setLabel('Adresse de livraison')->onlyOnDetail(),
            // NumberField::new('prix_livraison')->setLabel('Prix de la livraison')->onlyOnDetail(),
            // TextField::new('prix_total')->setLabel('Total TTC')->hideOnForm(),
            // NumberField::new('nbre_pers')->setLabel('Nombre de convives')->onlyOnDetail(),
            // BooleanField::new('pret_materiel')->setLabel('Prêt de matériel')->onlyOnDetail(),
            // BooleanField::new('restitution_materiel')->setLabel('Restitution de matériel')->onlyOnDetail()
        ];
    }
    
}
