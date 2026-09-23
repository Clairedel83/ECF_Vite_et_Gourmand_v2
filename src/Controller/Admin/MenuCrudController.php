<?php

namespace App\Controller\Admin;

use App\Entity\Menu;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\NumberField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use phpDocumentor\Reflection\Types\Integer;

class MenuCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Menu::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Menu')
            ->setEntityLabelInPlural('Menus')
        ;
    }

    public function configureFields(string $pageName): iterable
    {

        $required = true;
        if ($pageName == 'edit'){
            $required = false;
        }

        return [
            TextField::new('nom')->setLabel('Nom'),
            TextField::new('description')->setLabel('Description'),
            IntegerField::new('nbre_min')->setLabel('Nombre de personnes minimum'),
            NumberField::new('prix_per_pers')->setLabel('Prix par personne'),
            IntegerField::new('stock')->setLabel('Stock'),
            AssociationField::new('entree')->setLabel('Entrée'),
            AssociationField::new('plat')->setLabel('Plat'),
            AssociationField::new('dessert')->setLabel('Dessert'),
            AssociationField::new('regimes')->setLabel('Régime'),
            AssociationField::new('theme')->setLabel('Thème'),
            AssociationField::new('condition_stockage')->setLabel('Condition'),
            ImageField::new('illustration')->setLabel('Image')->setHelp('Image du menu')->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')->setBasePath('/uploads')->setUploadDir('public/uploads')->setRequired($required)
        ];
    }
    
}
