<?php

namespace App\Controller\Admin;

use App\Entity\Allergene;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class AllergeneCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Allergene::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Allergène')
            ->setEntityLabelInPlural('Allergènes')
        ;
    }
    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom')->setLabel('Nom'),
        ];
    }
    
}
