<?php

namespace App\Controller\Admin;

use App\Entity\CommandeHistorique;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
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
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
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
            TextField::new('numero_commande')->setLabel('N° de commande'),
            DateTimeField::new('date_modification')->setLabel('Date de modification'),
            DateTimeField::new('heure_modification')->setLabel('Heure de modification'),
            TextField::new('statut')->setLabel('Statut de commande')->setTemplatePath('admin/statut.html.twig')
        ];
    }
}
