<?php

namespace App\Controller\Admin;

use App\Entity\Role;
use App\Entity\User;
use App\Services\MailService;
use Doctrine\ORM\QueryBuilder;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\BooleanField;
use EasyCorp\Bundle\EasyAdminBundle\Field\EmailField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Doctrine\ORM\EntityManagerInterface;
use Override;
use Symfony\Component\Form\Extension\Core\Type\PasswordType;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[IsGranted('ROLE_ADMIN')]
class UserCrudController extends AbstractCrudController
{
    // Service qui permet de hacher les mots de passe
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

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
            TextField::new('nom')->setLabel('Nom')->hideWhenUpdating(),
            TextField::new('prenom')->setLabel('Prénom')->hideWhenUpdating(),
            EmailField::new('email')->setLabel('Email')->hideWhenUpdating(),
            TextField::new('password')->setLabel('Mot de passe')->setFormType(PasswordType::class)->onlyWhenCreating(),
            TextField::new('telephone')->setLabel('Téléphone')->onlyOnDetail(),
            TextField::new('adresse_postale')->setLabel('Adresse')->onlyOnDetail(),
            TextField::new('code_postal')->setLabel('Code postal')->onlyOnDetail(),
            TextField::new('ville')->setLabel('Ville')->onlyOnDetail(),
            AssociationField::new('Role')->setLabel('Rôle')->setQueryBuilder(
                // La création concerne uniquement les employés
                fn(QueryBuilder $qb) => $qb
                    ->andWhere('entity.code = :employe')
                    ->setParameter('employe', 'ROLE_EMPLOYE')
            )->hideWhenUpdating(),
            BooleanField::new('isActive')->setLabel('Compte actif'),
        ];
    }

    // PERMET DE CRÉER UN COMPTE EMPLOYÉ
    #[Override]
    public function persistEntity(EntityManagerInterface $entityManager, $entityInstance): void
    {
        // Récupère l'utilisateur créé depuis le formulaire EasyAdmin.
        /** @var User $user */
        $user = $entityInstance;

        // Récupère les éléments par l'administrateur
        $motDePasse = $user->getPassword();
        $nom = $user->getNom();
        $prenom = $user->getPrenom();

        // Hache le mot de passe
        $motDePasseHashe = $this->passwordHasher->hashPassword($user, $motDePasse);

        // remplace le mot de passe de l'employé créé par le mot de passe haché
        $user->setPassword($motDePasseHashe);

        // Récupère le rôle employé enregistré en base.
        $roleEmploye = $entityManager->getRepository(Role::class)->findOneBy([
            'code' => 'ROLE_EMPLOYE',
        ]);

        // Attribue automatiquement le rôle employé au nouveau compte.
        $user->setRole($roleEmploye);

        // Enregistre le nouvel utilisateur avec le fonctionnement d'EasyAdmin.
        parent::persistEntity($entityManager, $user);

        // Envoi du mail automatique de bienvenue
        $mail = new MailService();
        $adresseMail = $user->getEmail();
        $variables = [
            'prenom' => $prenom . ' ' . $nom,
        ];

        try {
            $mail->envoyerMail($adresseMail, $prenom, '', 8484212, $variables);
        } catch (\Exception $exception) {
            $this->addFlash(
                'warning',
                'Le compte employé a bien été crée mais le mail de confirmation n\'a pas pu être envoyé. L\'employé peut toutefois se connecter.'
            );
        }

        $this->addFlash(
            'success',
            'Ce compte a bien été créé ! L\'employé peut maintenant se connecter.'
        );
    }
}
