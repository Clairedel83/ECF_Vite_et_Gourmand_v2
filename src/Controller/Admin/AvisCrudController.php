<?php

namespace App\Controller\Admin;

use App\Entity\Avis;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Override;

class AvisCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Avis::class;
    }


    #[Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions
            // rend impossible de supprimer ou ajouter manuellement un avis
            ->disable(Action::NEW, Action::DELETE);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            AssociationField::new('commande')->setLabel('Commande')->hideOnForm(),
            DateField::new('dateCreation')->setLabel('Date de création')->setDisabled(),
            IntegerField::new('note')->setLabel('Note')->setDisabled(),
            TextField::new('contenu')->setLabel('Commentaire')->hideOnIndex()->setDisabled(),
            ChoiceField::new('statut')->setLabel('Statut de l\'avis')->setChoices([
                '1 - En attente' => 1,
                '2 - Validé' => 2,
                '3 - Refusé' => 3,
            ])
                ->setTemplatePath('admin/statutAvis.html.twig'),
        ];
    }
}
