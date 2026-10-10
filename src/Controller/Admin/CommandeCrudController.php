<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
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
        return $actions
        // rend impossible de supprimer ou ajouter manuellement un historique
        ->remove(Crud::PAGE_INDEX, Action::NEW)
        ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('numero_commande')->setLabel('N° de commande'),
            DateTimeField::new('date_commande')->setLabel('Date de création'),
            DateTimeField::new('heure_commande')->setLabel('Heure de création'),
            DateTimeField::new('date_livraison')->setLabel('Date de livraison'),
            DateTimeField::new('heure_livraison')->setLabel('Heure de livraison'),
            TextField::new('adresse_livraison')->setLabel('Adresse de livraison'),
            NumberField::new('prix_livraison')->setLabel('Prix de la livraison'),
            TextField::new('prix_total')->setLabel('Total TTC'),
            NumberField::new('nbre_pers')->setLabel('Nombre de convives'),
            NumberField::new('statut')->setLabel('Statut de la commande'),
            BooleanField::new('pret_materiel')->setLabel('Prêt de matériel'),
            BooleanField::new('restitution_materiel')->setLabel('Restitution de matériel'),
            AssociationField::new('commandeHistorique')->setLabel('Historique')
        ];
    }
    
}
