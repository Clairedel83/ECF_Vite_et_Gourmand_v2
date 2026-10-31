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
            TextField::new('nom')->setLabel('Nom'),
            TextField::new('prenom')->setLabel('Prénom'),
            EmailField::new('email')->setLabel('Email'),
            TextField::new('password')->setLabel('Mot de passe')->setFormType(PasswordType::class)->hideOnIndex()->onlyWhenCreating(),
            TextField::new('telephone')->setLabel('Téléphone')->onlyOnDetail(),
            TextField::new('adresse_postale')->setLabel('Adresse')->onlyOnDetail(),
            TextField::new('code_postal')->setLabel('Code postal')->onlyOnDetail(),
            TextField::new('ville')->setLabel('Ville')->onlyOnDetail(),
            AssociationField::new('Role')->setLabel('Rôle')->setQueryBuilder(
                // fonction pour filtrer les rôles et ne proposer que 'Utilisateur' et 'Employé'
                fn(QueryBuilder $qb) => $qb
                    ->andWhere('entity.code != :admin')
                    ->setParameter('admin', 'ROLE_ADMIN')
            )->hideWhenCreating(),
            BooleanField::new('isActive')->setLabel('Compte actif')->hideOnIndex(),
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
        $mail->envoyerMail($adresseMail, $prenom . ' ' . $nom, '', 8484212, '', '');

        $this->addFlash(
            'success',
            'Ce compte a bien été créé ! L\'employé peut maintenant se connecter.'
        );
    }
}
