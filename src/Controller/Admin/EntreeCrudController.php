<?php

namespace App\Controller\Admin;

use App\Entity\Entree;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class EntreeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Entree::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Entrée')
            ->setEntityLabelInPlural('Entrées')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom')->setLabel('Nom'),
            TextField::new('description')->setLabel('Description'),
            AssociationField::new('allergene')->setLabel('Allergène'),
        ];
    }
    
}
