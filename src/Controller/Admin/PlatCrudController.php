<?php

namespace App\Controller\Admin;

use App\Entity\Plat;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class PlatCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Plat::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Plat')
            ->setEntityLabelInPlural('Plats')
        ;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom')->setLabel('Nom')->setHelp('Nom court du plat'),
            TextField::new('description')->setLabel('Description')->setHelp('Description complète du plat'),
            AssociationField::new('allergene')->setLabel('Allergène'),
            ImageField::new('illustration')->setLabel('Image')->setHelp('Image du plat')->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')->setBasePath('/uploads')->setUploadDir('public/uploads')
        ];
    }
    
}
