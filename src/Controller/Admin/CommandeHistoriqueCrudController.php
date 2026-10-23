<?php

namespace App\Controller\Admin;

use App\Entity\CommandeHistorique;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Override;

class CommandeHistoriqueCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return CommandeHistorique::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Historique de commande')
            ->setEntityLabelInPlural('Historiques de commande')
        ;
    }

    #[Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions
        // rend impossible de supprimer ou ajouter manuellement une commande
        ->remove(Crud::PAGE_INDEX, Action::NEW)
        ->remove(Crud::PAGE_INDEX, Action::DELETE);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('commande.numeroCommande')->setLabel('N° de commande')->onlyOnIndex(),
            DateTimeField::new('date_modification')->setLabel('Date de modification')->setFormat('dd/MM/yyyy HH:mm'),
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
        ];
    }
}
