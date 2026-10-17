<?php

namespace App\Controller\Admin;

use App\Entity\User;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Override;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return User::class;
    }

    #[Override]
    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Utilisateur')
            ->setEntityLabelInPlural('Utilisateurs')
        ;
    }

    #[Override]
    public function configureActions(Actions $actions): Actions
    {
        return $actions
        // ajoute la possibilité de consulter les détails du compte utilisateur
        ->add(Crud::PAGE_INDEX, Action::DETAIL);
    }

    
    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('nom')->setLabel('Nom')->hideOnForm(),
            TextField::new('prenom')->setLabel('Prénom')->hideOnForm(),
            EmailField::new('email')->setLabel('Email')->hideOnForm(),
            TextField::new('telephone')->setLabel('Téléphone')->onlyOnDetail(),
            TextField::new('adresse_postale')->setLabel('Adresse')->onlyOnDetail(),
            TextField::new('code_postal')->setLabel('Code postal')->onlyOnDetail(),
            TextField::new('ville')->setLabel('Ville')->onlyOnDetail(),
            AssociationField::new('Role')->setLabel('Rôle')->setQueryBuilder(
                // fonction pour filtrer les rôles et ne proposer que 'Utilisateur' et 'Employé'
                fn (QueryBuilder $qb) => $qb
                    ->andWhere('entity.code != :admin')
                    ->setParameter('admin', 'ROLE_ADMIN')
            )
        ];
    }
    
}
