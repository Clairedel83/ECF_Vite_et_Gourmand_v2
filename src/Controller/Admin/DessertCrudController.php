<?php

namespace App\Controller\Admin;

use App\Entity\Dessert;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class DessertCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Dessert::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Dessert')
            ->setEntityLabelInPlural('Desserts')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom')->setLabel('Nom'),
            TextField::new('description')->setLabel('Description'),
            AssociationField::new('allergene')->setLabel('Allergène'),
            ImageField::new('illustration')->setLabel('Image')->setHelp('Image du dessert')->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')->setBasePath('/uploads')->setUploadDir('public/uploads')
        ];
    }
    
}
