<?php

namespace App\Controller\Commande;

use App\Controller\Services\AdresseService;
use App\Controller\Services\PrixLivraison;
use App\Form\AdresseLivraisonType;
use App\Repository\CommandeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

final class ModifierCommandeController extends AbstractController
{
    // AFFICHE LA PAGE DE MODIFICATION DE LA DERNIERE COMMANDE
    #[Route('/compte/commandes/details/{id}/modifier', name: 'app_commandes_modifier', methods: ['GET'])]
    public function index(CommandeRepository $commandeRepository, int $id, Request $request): Response
    {
        // Récupère la commande appartenant à l'utilisateur
        $commande = $commandeRepository->findOneBy([
            'id' => $id,
            'user' => $this->getUser()
        ]);

        // Vérifie que la commande existe
        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        // Vérifie que le statut de la commande permet une modification
        $statut = $commande->getStatut();

        if ($statut !== 1){
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        }
        
        
        // AFFICHE LE FORMULAIRE DE MODIFICATION D'ADRESSE (livraison)
        // crée le formulaire temporaire (non sauvegardé en BDD) de l'adresse de livraison
        $formAdresse = $this->createForm(AdresseLivraisonType::class);


        return $this->render('commande/modifierCommande.html.twig', [
            'commande' => $commande,
            'adresseLivraisonForm' =>$formAdresse->createView()
        ]);
    }

    // ENREGISTRE LA COMMANDE MODIFIEE
    // Sécurisation du formulaire avec utilisation d'un jeton CSRF
    #[IsCsrfTokenValid('confirmation_modification_commande', tokenKey: 'token', methods: ['POST'])]
    #[Route('/commande/{id}/confirmation_modification', name: 'app_commande_confirmation_modification', methods: ['POST'])]
    public function modifier(Request $request, CommandeRepository $commandeRepository, int $id, AdresseService $adresseService, PrixLivraison $prixLivraison, EntityManagerInterface $entityManager): Response
    {
        // methods : [POST]
        // La page ne peut être appelée que par l'envoi du formulaire
        // Si l'utilisateur essaie d'y accéder par l'URL (GET), il sera redirigé

        // --------------------------RECUPERE LES ELEMENTS NECESSAIRES A LA MODIFICATION DE COMMANDE---------------------------------
        // Récupère la commande appartenant à l'utilisateur
        $commande = $commandeRepository->findOneBy([
            'id' => $id,
            'user' => $this->getUser()
        ]);

        // Vérifie que la commande existe
        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        // Vérifie que le statut de la commande permet une modification
        $statut = $commande->getStatut();

        if ($statut !== 1){
            return $this->redirectToRoute('app_commandes_details', ['id' => $id]);
        }

        // RECUPERE LE NOMBRE DE CONVIVES
        // Récupère le nombre saisi
        $nbreConvivesSaisi = $request->request->get('form_nbre_convives');
        // Vérifie que le nombre saisi est bien un int
        if(filter_var($nbreConvivesSaisi, FILTER_VALIDATE_INT) === false){
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        }
        // Convertis ce qui est saisi en int pour enregistrer en format compatible en BDD
        $nbreConvives = (int) $nbreConvivesSaisi;

        // RECUPERE LA DATE DE LIVRAISON, vérifie son format et vérifie si elle est valide (> au délai min)
        $date_choisie = $request->request->get('form_date');

        // Sécurisation : Vérifie qu'une date a bien été transmise (et non une injection POST)
        if (!is_string($date_choisie) || $date_choisie === '') {
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        }
        
        // Transforme la date en objet DateTime
        $date_livraison = DateTime::createFromFormat('!Y-m-d', $date_choisie);

        // si le formatage ne réussit pas OU si la date conversion est inexacte :
        if(!$date_livraison || $date_livraison->format('Y-m-d') !== $date_choisie){
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        }

        // obtenir le délai des menus POUR CEUX qui ont un délai
        $delai_min = null;

        // pour toutes les conditions du menu
            // si le delai_min de la condition n'est pas null
        $menu = $commande->getMenu();
        foreach($menu->getConditions() as $condition){
            if ($condition->getDelaiMin() !== null &&  $condition->getDelaiMin() > 0){
                $delai_min = $condition->getDelaiMin();
            }
        }

        // Vérifie si la date de livraison enregistrée dans la commande est modifiée ($date_livraison du formulaire)
        $date_enregistree = $commande->getDateLivraison();

        if ($delai_min === null) {
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        }

        $dateJour = new DateTime(('today'));
        $date_min = $dateJour->modify('+'.$delai_min.' days');


        if(($date_livraison->format('Y-m-d') !== $date_enregistree->format('Y-m-d'))  && ($date_livraison < $date_min)){
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        } 

        // VERIFICATION DE LA DISPONIBILITE DU MENU 
        $disponibilite_debut = $menu->getDisponibiliteDebut();
        $disponibilite_fin = $menu->getDisponibiliteFin();

        // si menu est saisonnier
        if($disponibilite_debut && $disponibilite_fin){
            // les disponibilités sont au format JJ-MM. Pour les comparer à la date_livraison, il faut les transformer en MM-JJ
            [$jourDebut, $moisDebut] = explode('-', $disponibilite_debut);
            $disponibilite_debut_formatee = $moisDebut.'-'.$jourDebut;

            [$jourFin, $moisFin] = explode('-', $disponibilite_fin);
            $disponibilite_fin_formatee = $moisFin.'-'.$jourFin;

            $date_livraison_formatee = $date_livraison->format('m-d');

            if (($disponibilite_debut_formatee > $date_livraison_formatee) || ($date_livraison_formatee > $disponibilite_fin_formatee)){
                return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
            }
        }

        // RECUPERE L'HEURE DE LIVRAISON : et transforme en objet datetime pour enregistrer en format compatible en BDD
        $heure_choisie = $request->request->get('form_heure');
        $heure_dispo = ['10:00', '10:30', '11:00', '11:30', '17:00', '17:30', '18:00'];

        // si l'heure choisie n'est pas compatible avec les horaires disponibles :
        if(!in_array($heure_choisie, $heure_dispo, true)){
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        }
        
        // transforme l'heure validée en DateTime
        $heure_livraison = new \DateTime($heure_choisie);


        // RECUPERE LE CHOIX DU MATERIEL (avec ou sans location) : et transforme en booleen pour enregistrer en format compatible en BDD
        $choix_pret_materiel = $request->request->get('form_materiel');

        // si l'input de pret du materiel indique autre chose que 'oui' ou 'non':
        if(!in_array($choix_pret_materiel, ['oui', 'non'], true)){
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        }

        $pret_materiel = $request->request->get('form_materiel') == 'oui';


        // RECUPERE L'ACCEPTATION DES CGV
        $cgv = $request->request->get('cgv');

        // si les CGV ne sont pas cochées :
        if($cgv !== 'on'){
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        }

        // calcul du prix et possibilité de promo
        $prixPerPers = (float) $commande->getPrixPerPers();
        $nbre_min = $menu->getNbreMin();
        $nbrePromo = $nbre_min + 5;

        $prixMenu = $nbreConvives * $prixPerPers;
        $montant_reduction = 0;

        if($nbreConvives >= $nbrePromo) {
            $montant_reduction = 0.1 * $prixMenu;
        }

        $sous_total = $prixMenu - $montant_reduction;

        // sécurise le nombre de convives (doit être > au nombre min)
        if ($nbreConvives < $nbre_min) {
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        }

        // SI MODIFICATION DE LA LIVRAISON :
        $adresseModifiee = $request->request->get('form_adresse_modifiee');
        // adresseModifier doit renvoyer uniquement 'oui' ou 'non
        if (!in_array($adresseModifiee, ['oui', 'non'], true)){
            return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
        }


        if($adresseModifiee == 'oui'){
            // RECUPERE LE NOM FOURNI POUR LIVRAISON (user ou modifié)
            $nom = $request->request->get('form_nom');

            // RECUPERE LE PRENOM FOURNI POUR LIVRAISON (user ou modifié)
            $prenom = $request->request->get('form_prenom');

            // RECUPERE L'ADRESSE DE LIVRAISON (user OU adresse modifiée)
            $adresse_livraison = $request->request->get('form_adresse_choisie');

            // RECUPERE L'ADRESSE FORMAT GOOGLE
            $adresseGoogle = $request->request->get('form_adresse_google');

            // RECUPERE LE CODE POSTAL DE LIVRAISON
            $codePostal = $request->request->get('form_code_postal');

            // RECUPERE LA VILLE DE LIVRAISON
            $ville = $request->request->get('form_ville');

            // VERIFICATION DE L'ADRESSE
            // les adresses, code postal et villes doivent être des chaînes de caractères et non vides
            if(
                (!is_string($adresseGoogle) || trim($adresseGoogle) === '') || 
                (!is_string($codePostal) || trim($codePostal)=== '')  || 
                (!is_string($ville) || trim($ville) === '') ||
                (!is_string($nom) || trim($nom) === '') ||
                (!is_string($prenom) || trim($prenom) === '')
            )
            {
                return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]); 
            }

            // Appel à AdresseService pour vérifier
            try{
                $resultatVerification = $adresseService->verifierAdresse($adresseGoogle, $codePostal, $ville);
                $adresseVerifiee = $resultatVerification['adresse'];
                $villeVerifiee = $resultatVerification['ville'];

            } catch(\InvalidArgumentException $e){
                return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
            } catch(HttpExceptionInterface $e){
                $this->addFlash(
                    'error',
                    'Le service de vérification des adresses est temporairement indisponible.'
                );
                return $this->redirectToRoute('app_commandes_modifier', ['id' => $id]);
            }

            // attribue l'adresse de livraison
            $commande->setAdresseLivraison($nom.' '.$prenom.' - '.$adresseVerifiee);

            // attribue le prix de livraison
            $prixCalcule = $prixLivraison->calculPrixLivraison($villeVerifiee, $adresseVerifiee);
            $commande->setPrixLivraison(number_format($prixCalcule, 2, '.', ''));
        } else{
            // récupère le prix non modifié de la commande
            $prixCalcule = $commande->getPrixLivraison();
        }

        
    // -------------------------------------------MODIFICATION DE LA COMMANDE--------------------------------------------------------

        // attribue la date de livraison
        $commande->setDateLivraison($date_livraison);

        // attribue l'heure de livraison
        $commande->setHeureLivraison($heure_livraison);

        // attribue le nombre de convives
        $commande->setNbrePers($nbreConvives);

        // attribue le prix du matériel si location
        $prixMateriel = 0;

        if ($pret_materiel === true) {
            $prixMateriel = 20;
        }

        $commande->setPrixMateriel(number_format($prixMateriel, 2, '.', ''));

        // attribue le prix par personne
        $commande->setPrixPerPers(number_format($prixPerPers, 2, '.', ''));

        // attribue le montant de la réduction
        $commande->setMontantReduction(number_format($montant_reduction, 2, '.', ''));

        // attribue le prix après réduction (sous-total)
        $commande->setPrixSousTotal(number_format($sous_total, 2, '.', ''));

        // calcul du prix total
        $prixTotal = $sous_total + $prixCalcule + $prixMateriel;
        $commande->setPrixTotal(number_format($prixTotal, 2, '.', ''));

        // attribue le booleen de prêt de matériel
        $commande->setPretMateriel($pret_materiel);

        // attribue le booleen de restitution de matériel
        if($pret_materiel === false){
            $commande->setRestitutionMateriel(null);
        } else {
            $commande->setRestitutionMateriel(false);
        }

        // enregistre les modifications en BDD
        $entityManager->flush();

        $this->addFlash(
            'success',
            'Votre commande a bien été modifiée'
        );

        return $this->redirectToRoute('app_commandes_details', [
            'id' => $id
        ]);
    }
}
