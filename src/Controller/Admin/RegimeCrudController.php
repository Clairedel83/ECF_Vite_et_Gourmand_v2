<?php

namespace App\Controller\Admin;

use App\Entity\Regime;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class RegimeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Regime::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Régime')
            ->setEntityLabelInPlural('Régimes')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom')->setLabel('Nom'),
        ];
    }
    
}
