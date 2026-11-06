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
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;

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

    // Empêche la suppression définitive des menus.
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            ->disable(Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {

        $required = true;
        if ($pageName == 'edit'){
            $required = false;
        }

        return [
            TextField::new('nom')->setLabel('Nom'),
            BooleanField::new('isActive')->setLabel('Menu actif')->setHelp('Décochez pour retirer ce menu du site. Les commandes existantes sont conservées.')->renderAsSwitch(false)->hideWhenCreating(),
            TextField::new('description')->setLabel('Description')->onlyOnForms(),
            IntegerField::new('nbre_min')->setLabel('Nombre de pers. min'),
            NumberField::new('prix_per_pers')->setLabel('Prix / pers.'),
            IntegerField::new('stock')->setLabel('Stock'),
            TextField::new('disponibilite_debut')
                ->setLabel('Début de période')
                ->setHelp('Format JJ-MM (exemple : 15-03). Laisser vide si disponible toute l’année.')
                ->onlyOnForms()
                ->formatValue(function ($value) {
                    if ($value === null) {
                        return 'Toute l\'année';
                    }
                    return $value;
                }),
            TextField::new('disponibilite_fin')
                ->setLabel('Fin de période')
                ->setHelp('Format JJ-MM (exemple : 15-03). Laisser vide si disponible toute l’année.')
                ->onlyOnForms()
                ->formatValue(function ($value) {
                    if ($value === null) {
                        return 'Toute l\'année';
                    }
                    return $value;
                }),
            AssociationField::new('entree')->setLabel('Entrée'),
            AssociationField::new('plat')->setLabel('Plat'),
            AssociationField::new('dessert')->setLabel('Dessert'),
            AssociationField::new('regimes')->setLabel('Régime')->onlyOnForms(),
            AssociationField::new('theme')->setLabel('Thème')->onlyOnForms(),
            AssociationField::new('conditions')->setLabel('Condition')->setHelp('Sélectionnez obligatoirement une condition de délai de commande.')->onlyOnForms(),
            ImageField::new('illustration')->setLabel('Image')->setHelp('Image du menu')->setUploadedFileNamePattern('[year]-[month]-[day]-[contenthash].[extension]')->setBasePath('/uploads')->setUploadDir('public/uploads')->setRequired($required)->onlyOnForms()
        ];
    }
    
}
