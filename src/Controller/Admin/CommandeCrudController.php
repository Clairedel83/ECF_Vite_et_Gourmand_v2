<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Entity\CommandeHistorique;
use App\Entity\InterventionCommande;
use App\Form\InterventionCommandeType;
use App\Form\StatutType;
use App\Repository\CommandeRepository;
use App\Services\GestionCommandeService;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

use Override;
use Symfony\Component\HttpFoundation\Request;

class CommandeCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Commande::class;
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud
            ->setEntityLabelInSingular('Commande')
            ->setEntityLabelInPlural('Commandes')
        ;
    }

    #[Override]
    public function configureActions(Actions $actions): Actions
    {
        $details = Action::new('Détails')->linkToCrudAction('details');

        return $actions
            // rend impossible de supprimer ou ajouter manuellement un historique
            ->remove(Crud::PAGE_INDEX, Action::NEW)
            ->remove(Crud::PAGE_INDEX, Action::DELETE)
            ->remove(Crud::PAGE_INDEX, Action::EDIT)

            // ajoute la possibilité d'afficher les détails de commande
            ->add(Crud::PAGE_INDEX, $details);
    }

    // pour afficher la vue des détails de commande :
    #[AdminRoute(path: '/{entityId}/details', name: 'details', options: ['methods' => ['GET']])]
    public function details(AdminContext $context)
    {
        // permet d'afficher l'ensemble des éléments de l'entité Commande
        $commande = $context->getEntity()->getInstance();

        // permet de rattacher le formulaire de modification du statut
        $form = $this->createForm(StatutType::class, $commande);

        // permet de rattercher le formulaire d'annulation
        $interventionCommande = new InterventionCommande();
        // donne la destination du formulaire (method annulerCommande en POST, identification avec id)
        $formAnnulation = $this->createForm(InterventionCommandeType::class, $interventionCommande, [
            'action' => $this->generateUrl('admin_commande_annuler', [
                'entityId' => $commande->getId(),
            ]),
            'method' => 'POST',
        ]);

        // crée la vue de cet affichage
        return $this->render('admin/commande_details.html.twig', [
            'commande' => $commande,
            'statutForm' => $form->createView(),
            'InterventionCommandeForm' => $formAnnulation->createView()
        ]);
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            TextField::new('numero_commande')->setLabel('N° de commande')->hideOnForm(),
            AssociationField::new('user')->setLabel('Client')->hideOnForm(),
            // DateTimeField::new('date_commande')->setLabel('Date de création')->setFormat('dd/MM/yyyy HH:mm')->onlyOnDetail(),
            DateField::new('dateLivraison', 'Livraison')->hideOnForm()
                // associe heure et date de livraison dans la même colonne
                ->formatValue(function ($value, $commande) {
                    return $commande->getDateLivraison()->format('d/m/Y')
                        . ' à '
                        . $commande->getHeureLivraison()->format('H:i');
                }),
            ChoiceField::new('statut')
                ->setLabel('Statut de commande')
                ->setChoices([
                    '1 - En attente de validation' => 1,
                    '2 - Commande acceptée' => 2,
                    '3 - En préparation' => 3,
                    '4 - En cours de livraison' => 4,
                    '5 - Commande livrée' => 5,
                    '6 - En attente du retour de matériel' => 6,
                    '7 - Commande terminée' => 7,
                    '8 - Commande annulée' => 8,
                ])
                ->setTemplatePath('admin/statut.html.twig'),
            TextField::new('menu_nom')->setLabel('Menu')->hideOnForm(),
        ];
    }

    // pour permettre le changement de statut
    /** @var Commande $entityInstance */
    #[Override]
    public function updateEntity(EntityManagerInterface $entityManager, object $entityInstance): void
    {
        // Récupère les données de la commande avant modification
        $donneesEnregistrees = $entityManager->getUnitOfWork()->getOriginalEntityData($entityInstance);

        // Récupère l'ancien et le nouveau statut
        $ancienStatut = $donneesEnregistrees['statut'];
        $nouveauStatut = $entityInstance->getStatut();

        if ($ancienStatut !== $nouveauStatut) {
            // CREATION D'UN NOUVEL HISTORIQUE DE COMMANDE
            $historiqueCommande = new CommandeHistorique();

            // attribue le statut à l'historique de commande
            $historiqueCommande->setStatut($nouveauStatut);

            // attribue la date et l'heure de modification du statut
            $date_actuelle = new \DateTime();

            $historiqueCommande->setDateModification($date_actuelle);
            $historiqueCommande->setHeureModification($date_actuelle);

            // lie l'historique à la commande
            $entityInstance->addCommandeHistorique($historiqueCommande);

            // enregistre les modifications en BDD
            $entityManager->persist($historiqueCommande);
        }

        // Enregistre les modifications
        parent::updateEntity($entityManager, $entityInstance);
    }


    // PERMET L'ANNULATION D'UNE COMMANDE PAR UN EMPLOYE VIA UN FORM (POST)
    #[AdminRoute(path: '/{entityId}/annuler', name: 'annuler', options: ['methods' => ['POST']])]
    public function annulerCommande(AdminContext $context, Request $request, EntityManagerInterface $entityManager, GestionCommandeService $gestionCommandeService)
    {
        // RECUPERE LES ELEMENTS
        // récupère l'auteur 
        $auteur = $this->getUser();

        // récupère la commande concernée
        $commande = $context->getEntity()->getInstance();

        // crée l'intervention
        $interventionCommande = new InterventionCommande();

        // Reconstruit le formulaire envoyé depuis la modale
        $formAnnulation = $this->createForm(InterventionCommandeType::class, $interventionCommande, [
            'action' => $this->generateUrl('admin_commande_annuler', [
                'entityId' => $commande->getId(),
            ]),
            'method' => 'POST',
        ]);

        // récupère les données envoyées dans le formulaire
        $formAnnulation->handleRequest($request);

        // crée la date du jour
        $dateAnnulation = new \DateTimeImmutable();


        // ENREGISTRE EN BDD
        if ($formAnnulation->isSubmitted() && $formAnnulation->isValid()) {
            // le mode de contact et le motif sont déjà renseignés par handlerequest()

            // attribue l'auteur
            $interventionCommande->setAuteur($auteur);

            // attribue le type d'intervention
            $interventionCommande->setType('annulation');

            // attribue la date de l'intervention
            $interventionCommande->setDate($dateAnnulation);

            // appelle le service pour annuler la commande
            try {
                $gestionCommandeService->annulerCommande($commande, $entityManager, [1, 2, 3], $interventionCommande);
            } catch (\DomainException $exception) {
                // affiche le message d'erreur
                $this->addFlash('warning', $exception->getMessage());

                // redirige vers la page précédente = depuis laquelle la demande a été faite (referer)
                $referer = $request->headers->get('referer');

                // vérifie que la dernère page consultée correspond à une page du domaine
                if (is_string($referer) && str_starts_with($referer, $request->getSchemeAndHttpHost() . '/')) {
                    return $this->redirect($referer);
                }

                // attention, en cas d'erreur, referer peut être vide donc prévoir redirection fiable
                return $this->redirectToRoute('admin_commande_details', [
                    'entityId' => $commande->getId(),
                ]);
            }

            $this->addFlash(
                'success',
                'Cette commande a bien été annulée.'
            );

            return $this->redirectToRoute('admin_commande_details', [
                'entityId' => $commande->getId(),
            ]);
        }

        // si le formulaire est invalide, affiche la page détails et rouvre la modale
        $formStatut = $this->createForm(StatutType::class, $commande);

        return $this->render('admin/commande_details.html.twig', [
            'commande' => $commande,
            'statutForm' => $formStatut->createView(),
            'InterventionCommandeForm' => $formAnnulation->createView(),
            'ouvrirModaleAnnulation' => true,
        ]);
    }
}
