<?php

namespace App\Controller\Admin;

use App\Entity\Commande;
use App\Entity\CommandeHistorique;
use App\Entity\InterventionCommande;
use App\Form\InterventionCommandeType;
use App\Form\ModifierCommandeType;
use App\Form\StatutType;
use App\Services\CalculPrixLivraison;
use App\Services\GestionCommandeService;
use App\Services\VerificationAdresseService;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Attribute\AdminRoute;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Config\Filters;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Filter\ChoiceFilter;
use EasyCorp\Bundle\EasyAdminBundle\Filter\EntityFilter;
use Override;
use Symfony\Component\Form\FormError;
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

    // PERMET D'UTILISER DES FILTRES
    #[Override]
    public function configureFilters(Filters $filters): Filters
    {
        return $filters
            ->add(ChoiceFilter::new('statut', 'Statut')->setChoices([
                '1 - En attente de validation' => 1,
                '2 - Commande acceptée' => 2,
                '3 - En préparation' => 3,
                '4 - En cours de livraison' => 4,
                '5 - Commande livrée' => 5,
                '6 - En attente du retour de matériel' => 6,
                '7 - Commande terminée' => 7,
                '8 - Commande annulée' => 8,
            ]))
            ->add(EntityFilter::new('user', 'Client'));
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
            ChoiceField::new('statut')->setLabel('Statut de commande')
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

    // PERMET D'AFFICHER L'ENSEMBLE DES DETAILS DE COMMANDES (form compris)
    #[AdminRoute(path: '/{entityId}/details', name: 'details', options: ['methods' => ['GET']])]
    public function details(AdminContext $context)
    {
        // permet d'afficher l'ensemble des éléments de l'entité Commande
        $commande = $context->getEntity()->getInstance();

        // permet de rattacher le formulaire de modification du statut et de prévoir sa destination (methode modifierStatut)
        $form = $this->createForm(StatutType::class, $commande, [
            'action' => $this->generateUrl('admin_commande_statut', [
                'entityId' => $commande->getId(),
            ]),
            'method' => 'POST',
        ]);

        // permet de rattercher le formulaire d'annulation
        $interventionCommande = new InterventionCommande();
        // donne la destination du formulaire (method annulerCommande en POST, identification avec id)
        $formAnnulation = $this->createForm(InterventionCommandeType::class, $interventionCommande, [
            'action' => $this->generateUrl('admin_commande_annuler', [
                'entityId' => $commande->getId(),
            ]),
            'method' => 'POST',
        ]);

        // permet de rattacher le formulaire de modification de commande et donne la destination du formulaire
        $formModification = $this->createForm(ModifierCommandeType::class, $commande, [
            'action' => $this->generateUrl('admin_commande_modifier', [
                'entityId' => $commande->getId(),
            ]),
            'method' => 'POST',
            'minimum_convives' => $commande->getMenu()->getNbreMin(),
        ]);

        // concernant la modification des informations (livraison)
        // Sépare le destinataire de l'adresse enregistrée : conserve toute l'adresse après l" - ".
        $elementsAdresse = explode(' - ', $commande->getAdresseLivraison(), 2);

        // Si aucun séparateur n'est présent, conserve l'adresse entière.
        $adresseLivraison = $elementsAdresse[1] ?? $elementsAdresse[0];

        // Affiche uniquement l'adresse dans le champ du formulaire : permettra un recalcul plus facile
        $formModification->get('adresseLivraison')->setData($adresseLivraison);

        // crée la vue de cet affichage
        return $this->render('admin/commande_details.html.twig', [
            'commande' => $commande,
            'statutForm' => $form->createView(),
            'InterventionCommandeForm' => $formAnnulation->createView(),
            'modificationForm' => $formModification->createView(),
            'adresseInitialeLivraison' => $adresseLivraison,
        ]);
    }


    // PERMET LE CHANGEMENT DE STATUT
    #[AdminRoute(path: '/{entityId}/statut', name: 'statut', options: ['methods' => 'POST'])]
    public function modifierStatut(AdminContext $context, Request $request, EntityManagerInterface $entityManager)
    {
        // Récupère la commande 
        $commande = $context->getEntity()->getInstance();

        // Sécurisation : ouvre une transaction
        $connexion = $entityManager->getConnection();
        $connexion->beginTransaction();

        try {
            // recharge et verrouille : évite plusieurs modifications simultanées
            $entityManager->refresh($commande, LockMode::PESSIMISTIC_WRITE);

            $ancienStatut = $commande->getStatut();

            // Reconstruit le formulaire envoyé depuis details(vue twig)
            $formStatut = $this->createForm(StatutType::class, $commande, [
                'action' => $this->generateUrl('admin_commande_statut', [
                    'entityId' => $commande->getId(),
                ]),
                'method' => 'POST',
            ]);

            // Récupère le statut envoyé
            $formStatut->handleRequest($request);

            if (!$formStatut->isSubmitted() || !$formStatut->isValid()) {
                $this->addFlash(
                    'warning',
                    'Le statut n’a pas pu être enregistré. Veuillez réessayer.'
                );

                return $this->redirectToRoute('admin_commande_details', [
                    'entityId' => $commande->getId(),
                ]);
            }

            $nouveauStatut = $commande->getStatut();

            // Une commande annulée ne reprend pas son parcours.
            if ($ancienStatut === 8) {
                throw new \DomainException(
                    'Cette commande est annulée : son statut ne peut plus être modifié.'
                );
            }

            // L'annulation doit passer par le formulaire dédié.
            if ($nouveauStatut === 8) {
                throw new \DomainException(
                    'Utilisez le bouton Annuler la commande pour renseigner le contact et le motif.'
                );
            }

            // Évite un enregistrement et un historique inutiles
            if ($ancienStatut === $nouveauStatut) {
                $this->addFlash(
                    'info',
                    'La commande possède déjà ce statut.'
                );

                return $this->redirectToRoute('admin_commande_details', [
                    'entityId' => $commande->getId(),
                ]);
            }

            // uniquement si prêt de matériel
            if ($nouveauStatut === 6 && !$commande->isPretMateriel()) {
                throw new \DomainException(
                    'Cette commande ne comporte pas de prêt de matériel.'
                );
            }

            // Terminer une commande (statut = 7) avec prêt de matériel confirme sa restitution
            if ($nouveauStatut === 7 && $commande->isPretMateriel()) {
                $commande->setRestitutionMateriel(true);
            }

            // ------------------------------------------------ HISTORIQUE ------------------------------------------------

            $historiqueCommande = new CommandeHistorique();

            $historiqueCommande->setStatut($nouveauStatut);

            // crée la date de modification
            $dateModification = new \DateTime();

            $historiqueCommande->setDateModification($dateModification);
            $historiqueCommande->setHeureModification($dateModification);

            // Lie l'historique à la commande.
            $commande->addCommandeHistorique($historiqueCommande);

            // ------------------------------------------------ ENREGISTREMENT ------------------------------------------------

            $entityManager->persist($historiqueCommande);
            $entityManager->flush();

            // Valide ensemble le nouveau statut et son historique.
            $connexion->commit();

            $this->addFlash(
                'success',
                'Le statut de la commande a bien été mis à jour.'
            );
        } catch (\DomainException $exception) {
            // Affiche le message d'erreur
            $this->addFlash('warning', $exception->getMessage());
        } finally {
            // Ferme la transaction en cas de return ou d'erreur
            if ($connexion->isTransactionActive()) {
                $connexion->rollBack();
                $entityManager->clear();
            }
        }

        return $this->redirectToRoute('admin_commande_details', [
            'entityId' => $commande->getId(),
        ]);
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

        $formStatut = $this->createForm(StatutType::class, $commande, [
            'action' => $this->generateUrl('admin_commande_statut', [
                'entityId' => $commande->getId(),
            ]),
            'method' => 'POST',
        ]);

        // Reconstruit le formulaire de modification, également présent sur la page.
        $formModification = $this->createForm(ModifierCommandeType::class, $commande, [
            'action' => $this->generateUrl('admin_commande_modifier', [
                'entityId' => $commande->getId(),
            ]),
            'method' => 'POST',
            'minimum_convives' => $commande->getMenu()->getNbreMin(),
        ]);

        // Sépare le destinataire de l'adresse de livraison.
        $elementsAdresse = explode(' - ', $commande->getAdresseLivraison(), 2);

        // Conserve l'adresse entière si aucun séparateur n'est présent.
        $adresseLivraison = $elementsAdresse[1] ?? $elementsAdresse[0];

        // Prépare le champ avec uniquement l'adresse géographique.
        $formModification->get('adresseLivraison')->setData($adresseLivraison);

        return $this->render('admin/commande_details.html.twig', [
            'commande' => $commande,
            'statutForm' => $formStatut->createView(),
            'InterventionCommandeForm' => $formAnnulation->createView(),
            'ouvrirModaleAnnulation' => true,
            'modificationForm' => $formModification->createView(),
            'adresseInitialeLivraison' => $adresseLivraison,
        ]);
    }


    // PERMET LA MODIFICATION D'UNE COMMANDE PAR UN EMPLOYE VIA UN FORM (POST)
    #[AdminRoute(path: '/{entityId}/modifier', name: 'modifier', options: ['methods' => ['POST']])]
    public function modifierCommande(AdminContext $context, Request $request, EntityManagerInterface $entityManager, VerificationAdresseService $verificationAdresseService, CalculPrixLivraison $calculPrixLivraison)
    {
        // RECUPERE LES ELEMENTS INITIAUX (avant modification éventuelle)
        // récupère la commande concernée
        $commande = $context->getEntity()->getInstance();

        // récupère l'auteur 
        $auteur = $this->getUser();

        // Sécurisation : pour éviter que la même commande subisse plusieurs modifications en même temps
        $connexion = $entityManager->getConnection();
        $connexion->beginTransaction();

        // Valeurs initiales avant la préparation du formulaire
        $formModification = null;
        $adresseInitialeLivraison = '';

        try {
            // Recharge et verrouille la commande AVANT de traiter le formulaire.
            $entityManager->refresh($commande, LockMode::PESSIMISTIC_WRITE);

            // Vérifie que le statut permet la modification (= 1 ou 2)
            if (!in_array($commande->getStatut(), [1, 2], true)) {
                throw new \DomainException(
                    'Le statut de cette commande ne permet plus sa modification.'
                );
            }

            // récupère les éléments du menu
            $menu = $commande->getMenu();

            // if ($menu === null) {
            //     throw new \DomainException(
            //         'Le menu associé à cette commande ne permet plus sa modification.'
            //     );
            // }

            // récupère l'adresse de livraison, la date de livraison et le prix avant modification
            $adresseCompleteInitiale = $commande->getAdresseLivraison();
            $prixLivraisonInitial = $commande->getPrixLivraison();
            $dateLivraisonInitiale = $commande->getDateLivraison()->format('Y-m-d');


            // Sépare le destinataire de l'adresse géographique pour vérifier l'adresse et calculer les frais de livraison. Le destinataire sera réintroduit lors de l'enregistrement en BDD.
            $elementsAdresse = explode(' - ', $adresseCompleteInitiale, 2);

            $destinataire = '';
            $adresseInitialeLivraison = $elementsAdresse[0];

            if (isset($elementsAdresse[1])) {
                $destinataire = $elementsAdresse[0];
                $adresseInitialeLivraison = $elementsAdresse[1];
            }



            // ------------------------------------------------ FORMULAIRE ------------------------------------------------

            // Reconstruit le formulaire envoyé depuis la page détails.
            $formModification = $this->createForm(ModifierCommandeType::class, $commande, [
                'action' => $this->generateUrl('admin_commande_modifier', [
                    'entityId' => $commande->getId(),
                ]),
                'method' => 'POST',
                'minimum_convives' => $commande->getMenu()->getNbreMin(),
            ]);

            // Prépare la valeur initiale du champ sans le destinataire.
            $formModification->get('adresseLivraison')->setData($adresseInitialeLivraison);

            // récupère les données envoyées dans le formulaire
            $formModification->handleRequest($request);

            if ($formModification->isSubmitted() && $formModification->isValid()) {

                // --------------- DATE DE LIVRAISON ---------------
                $dateLivraison = $commande->getDateLivraison();

                // Récupère le délai de préparation enregistré dans les conditions
                $delaiMinimum = 0;

                foreach ($menu->getConditions() as $condition) {
                    if ($condition->getDelaiMin() !== null) {
                        $delaiMinimum = $condition->getDelaiMin();
                    }
                }

                $dateMinimum = new \DateTime('today');
                $dateMinimum->modify('+' . $delaiMinimum . ' days');

                // Comme dans le controller client : applique le délai si la date change
                if ($dateLivraison->format('Y-m-d') !== $dateLivraisonInitiale && $dateLivraison < $dateMinimum) {
                    throw new \DomainException(
                        'La date choisie ne respecte pas le délai de préparation du menu.'
                    );
                }

                // Vérifie les disponibilités du menu
                $disponibiliteDebut = $menu->getDisponibiliteDebut();
                $disponibiliteFin = $menu->getDisponibiliteFin();

                if ($disponibiliteDebut && $disponibiliteFin) {
                    // Transforme JJ-MM en MM-JJ pour comparer les dates.
                    [$jourDebut, $moisDebut] = explode('-', $disponibiliteDebut);
                    [$jourFin, $moisFin] = explode('-', $disponibiliteFin);

                    $debutFormate = $moisDebut . '-' . $jourDebut;
                    $finFormatee = $moisFin . '-' . $jourFin;

                    $dateChoisie = $dateLivraison->format('m-d');

                    if ($dateChoisie < $debutFormate || $dateChoisie > $finFormatee) {
                        throw new \DomainException(
                            'Ce menu n’est pas disponible à la date choisie.'
                        );
                    }
                }

                // --------------- HEURE DE LIVRAISON ---------------

                // Reprend les créneaux proposés au client (controller et vue twig)
                $heuresDisponibles = ['10:00', '10:30', '11:00', '11:30', '17:00', '17:30', '18:00'];
                $heureLivraison = $commande->getHeureLivraison()->format('H:i');

                if (!in_array($heureLivraison, $heuresDisponibles, true)) {
                    throw new \DomainException(
                        'Veuillez choisir un créneau de livraison autorisé.'
                    );
                }



                // --------------- ADRESSE DE LIVRAISON ---------------
                // récupère l'adresse saisie
                $adresseSaisie = trim($formModification->get('adresseLivraison')->getData());

                // si l'adresse saisie et l'adresse initiale sont les mêmes :
                if ($adresseSaisie === trim($adresseInitialeLivraison)) {
                    // Conserve l'adresse complète et les frais initiaux.
                    $adresseComplete = $adresseCompleteInitiale;
                    $prixLivraison = (float) $prixLivraisonInitial;

                    // si l'adresse saisie est différente :
                } else {
                    // Récupère les champs non mappés du formulaire.
                    $codePostal = trim((string) $formModification->get('codePostalLivraison')->getData());

                    $ville = trim((string) $formModification->get('villeLivraison')->getData());

                    // récupère le code postal et la ville (utiles pour vérifier l'adresse si la nouvelle est différente)
                    if ($codePostal === '' || $ville === '') {
                        throw new \DomainException(
                            'Veuillez renseigner le code postal et la ville.'
                        );
                    }

                    // Appelle le service de vérification d'adresse
                    $resultatAdresse = $verificationAdresseService->verifierAdresse($adresseSaisie, $codePostal, $ville);

                    $adresseVerifiee = $resultatAdresse['adresse'];
                    $villeVerifiee = $resultatAdresse['ville'];

                    // Recalcule les frais de livraison
                    $prixLivraison = $calculPrixLivraison->calculPrixLivraison($villeVerifiee, $adresseVerifiee);

                    // Réintroduit le destinataire devant l'adresse vérifiée.
                    $adresseComplete = $adresseVerifiee;

                    if ($destinataire !== '') {
                        $adresseComplete = $destinataire . ' - ' . $adresseVerifiee;
                    }
                }

                $commande->setAdresseLivraison($adresseComplete);

                // --------------- FACTURATION ---------------

                // Récupère le prix/personne et le nombre de convives
                $prixPersonne = (float) $commande->getPrixPerPers();
                $nombreConvives = $commande->getNbrePers();
                $prixMenu = ($prixPersonne * $nombreConvives);

                // calcul du montant de réduction (s'il s'applique, de base = 0)
                $montantReduction = 0;

                if ($nombreConvives >= $menu->getNbreMin() + 5) {
                    $montantReduction = round($prixMenu * 0.10, 2);
                }

                // calcul du sous total
                $sousTotal = round($prixMenu - $montantReduction, 2);

                // calcul du prix de location matériel (de base = 0, passe à 20 si checked)
                $prixMateriel = 0;

                if ($commande->isPretMateriel()) {
                    $prixMateriel = 20;
                }

                // calcul du prix total
                $prixTotal = round($sousTotal + $prixLivraison + $prixMateriel, 2);

                // met à jour les prix dans l'entité commande
                $commande->setPrixSousTotal(number_format($sousTotal, 2, '.', ''));

                $commande->setMontantReduction(number_format($montantReduction, 2, '.', ''));

                $commande->setPrixLivraison(number_format($prixLivraison, 2, '.', ''));

                $commande->setPrixMateriel(number_format($prixMateriel, 2, '.', ''));

                $commande->setPrixTotal(number_format($prixTotal, 2, '.', ''));

                // Statuts 1 ou 2 donc le matériel n'a pas été restitué
                $commande->setRestitutionMateriel(false);

                // --------------- INTERVENTION ---------------
                // crée l'intervention
                $interventionCommande = new InterventionCommande();

                // attribue l'auteur, le type et la date
                $interventionCommande->setAuteur($auteur);
                $interventionCommande->setType('modification');
                $interventionCommande->setDate(new \DateTimeImmutable());

                // attribue l'intervention à la commande
                $commande->addInterventionCommande($interventionCommande);

                // --------------- Enregistrement en BDD ---------------

                $entityManager->persist($interventionCommande);
                $entityManager->flush();

                // valide la transaction
                $connexion->commit();

                $this->addFlash(
                    'success',
                    'Cette commande a bien été modifiée.'
                );

                return $this->redirectToRoute('admin_commande_details', [
                    'entityId' => $commande->getId(),
                ]);
            }

            // fin du if (isSubmitted && isValid)
            // si le fomulaire est invalide : termine la transaction et rafraîchit les données
            $connexion->rollBack();
            $entityManager->refresh($commande);
        } catch (\DomainException | \InvalidArgumentException $exception) {
            // stop transaction si toujours active
            if ($connexion->isTransactionActive()) {
                $connexion->rollBack();
            }

            // rafraîchit les données
            $entityManager->refresh($commande);


            if ($formModification !== null) {
                // si le formulaire existe, affiche le message d'erreur
                $formModification->addError(
                    new FormError($exception->getMessage())
                );
            } else {
                // si le formulaire ne s'est pas ouvert, affiche le message d'erreur associé
                $this->addFlash('warning', $exception->getMessage());
            }
        } catch (\Throwable $exception) {
            // stop transaction si toujours active
            if ($connexion->isTransactionActive()) {
                $connexion->rollBack();
            }

            // retire de la mémoire les modifications effectuées
            $entityManager->clear();

            throw $exception;
        }


        // Si le formulaire est vide, redirige vers la page de details
        if ($formModification === null) {
            return $this->redirectToRoute('admin_commande_details', [
                'entityId' => $commande->getId(),
            ]);
        }

        // --------------- REAFFICHAGE APRES ERREUR ---------------

        $formStatut = $this->createForm(StatutType::class, $commande, [
            'action' => $this->generateUrl('admin_commande_statut', [
                'entityId' => $commande->getId(),
            ]),
            'method' => 'POST',
        ]);

        // L'annulation utilise son propre objet InterventionCommande.
        $interventionAnnulation = new InterventionCommande();

        $formAnnulation = $this->createForm(InterventionCommandeType::class, $interventionAnnulation, [
            'action' => $this->generateUrl('admin_commande_annuler', [
                'entityId' => $commande->getId(),
            ]),
            'method' => 'POST',
        ]);

        return $this->render('admin/commande_details.html.twig', [
            'commande' => $commande,
            'statutForm' => $formStatut->createView(),
            'InterventionCommandeForm' => $formAnnulation->createView(),
            'modificationForm' => $formModification->createView(),
            'adresseInitialeLivraison' => $adresseInitialeLivraison,
        ]);
    }
}
