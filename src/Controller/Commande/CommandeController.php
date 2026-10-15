<?php

namespace App\Controller\Commande;

use App\Controller\Classe\Panier;
use App\Controller\Services\AdresseService;
use App\Controller\Services\DistanceService;
use App\Controller\Services\PrixLivraison;
use App\Entity\Commande;
use App\Entity\CommandeHistorique;
use App\Repository\MenuRepository;
use DateTime;
use Doctrine\DBAL\LockMode;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\HttpExceptionInterface;

final class CommandeController extends AbstractController
{
    // Sécurisation du formulaire avec utilisation d'un jeton CSRF
    #[IsCsrfTokenValid('valider_commande', tokenKey: 'token', methods: ['POST'])]
    #[Route('/commande', name: 'app_commande')]
    public function index(MenuRepository $menuRepository, Panier $panier, Request $request, EntityManagerInterface $entityManager, DistanceService $distanceService, PrixLivraison $prixLivraison, AdresseService $adresseService): Response
    {
        // La page ne peut être appelée que par l'envoi du formulaire
        // Si l'utilisateur essaie d'y accéder par l'URL (GET), il sera redirigé vers le panier
        if($request->getMethod() != 'POST'){
            return $this->redirectToRoute('app_panier');
        }

    // ---------------------------------RECUPERE LES ELEMENTS NECESSAIRES A LA CONSTRUCTION DE COMMANDE-----------------------------------
        // RECUPERE L'UTILISATEUR CONNECTE
        $user = $this->getUser();

        // récupère l'id du menu présent dans le panier
        $menuId = $panier->getPanier();

        // si le panier contient un menu, récupère l'objet entier dans la BDD
        $menu = null;

        if ($menuId) {
            $menu = $menuRepository->find($menuId);
        }

        // si le panier est vide mais que l'utilisateur clique sur Commander, il est redirigé
        if (!$menu) {
            return $this->redirectToRoute('app_panier');
        }
        
        // VERIFIE LE STOCK DU MENU
        $stockMenu = $menu->getStock();

        if ($stockMenu <= 0){
            return $this->redirectToRoute('app_panier');
        }

        // RECUPERE LE NOMBRE DE CONVIVES : et transforme en int pour enregistrer en format compatible en BDD
        $nbreConvives = (int) $request->request->get('form_nbre_convives');

        // RECUPERE LA DATE DE LIVRAISON, vérifie son format et vérifie si elle est valide (> au délai min)
        $date_choisie = $request->request->get('form_date');

        // Sécurisation : Vérifie qu'une date a bien été transmise (et non une injection POST)
        if (!is_string($date_choisie) || $date_choisie === '') {
            return $this->redirectToRoute('app_panier');
        }
        
        // Transforme la date en objet DateTime
        $date_livraison = DateTime::createFromFormat('!Y-m-d', $date_choisie);

        // si le formatage ne réussit pas OU si la date conversion est inexacte :
        if(!$date_livraison || $date_livraison->format('Y-m-d') !== $date_choisie){
            return $this->redirectToRoute('app_panier');
        }

        // obtenir le délai des menus POUR CEUX qui ont un délai
        $delai_min = null;

        // pour toutes les conditions du menu
            // si le delai_min de la condition n'est pas null
        foreach($menu->getConditions() as $condition){
            if ($condition->getDelaiMin() !== null &&  $condition->getDelaiMin() > 0){
                $delai_min = $condition->getDelaiMin();
            }
        }

        if ($delai_min === null) {
            return $this->redirectToRoute('app_panier');
        }

        $dateJour = new DateTime(('today'));
        $date_min = $dateJour->modify('+'.$delai_min.' days');

        if($date_livraison < $date_min){
            return $this->redirectToRoute('app_panier');
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
                return $this->redirectToRoute('app_panier');
            }
        }



        // RECUPERE L'HEURE DE LIVRAISON : et transforme en objet datetime pour enregistrer en format compatible en BDD
        $heure_choisie = $request->request->get('form_heure');
        $heure_dispo = ['10:00', '10:30', '11:00', '11:30', '17:00', '17:30', '18:00'];

        // si l'heure choisie n'est pas compatible avec les horaires disponibles :
        if(!in_array($heure_choisie, $heure_dispo, true)){
            return $this->redirectToRoute('app_panier');
        }
        
        // transforme l'heure validée en DateTime
        $heure_livraison = new \DateTime($heure_choisie);


        // RECUPERE LE CHOIX DU MATERIEL (avec ou sans location) : et transforme en booleen pour enregistrer en format compatible en BDD
        $pret_materiel = $request->request->get('form_materiel') === 'oui';

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
            return $this->redirectToRoute('app_panier'); 
        }

        // Appel à AdresseService pour vérifier
        try{
            $resultatVerification = $adresseService->verifierAdresse($adresseGoogle, $codePostal, $ville);

            $adresseVerifiee = $resultatVerification['adresse'];
            $codePostalVerifie = $resultatVerification['codePostal'];
            $villeVerifiee = $resultatVerification['ville'];

        } catch(\InvalidArgumentException $e){
            return $this->redirectToRoute('app_panier');
        } catch(HttpExceptionInterface $e){
            $this->addFlash(
                'error',
                'Le service de vérification des adresses est temporairement indisponible.'
            );
            return $this->redirectToRoute('app_panier');
        }



        // RECUPERE L'ACCEPTATION DES CGV
        $cgv = $request->request->get('cgv');

        // si les CGV ne sont pas cochées :
        if($cgv !== 'on'){
            return $this->redirectToRoute('app_panier');
        }
        
        
    // -------------------------------------------CREATION DE LA COMMANDE--------------------------------------------------------
        $commande = new Commande();

        // attribue un utilisateur à la commande
        $commande->setUser($user);

        // attribue un menu à la commande (permettra d'obtenir les statistiques)
        $commande->setMenu($menu);

        // attribue un nom de menu
        $commande->setMenuNom($menu->getNom());

        // attribue la composition du menu
        $menu_entree = $menu->getEntree()->getNom();
        $menu_plat = $menu->getPlat()->getNom();
        $menu_dessert = $menu->getDessert()->getNom();
        $menu_composition = 'Entrée :'.$menu_entree.', '.'Plat :'.$menu_plat.', '.'Dessert :'.$menu_dessert.'.';
        $commande->setMenuComposition($menu_composition);

        // attribut un numéro de commande
        $commande->setNumeroCommande('VG-'.date('Ymd').'-'.random_int(100000, 999999));

        // attribue la date de commande
        $commande->setDateCommande(new \DateTime());

        // attribue l'heure de commande
        $commande->setHeureCommande(new \DateTime());

        // attribue la date de livraison
        $commande->setDateLivraison($date_livraison);

        // attribue l'heure de livraison
        $commande->setHeureLivraison($heure_livraison);

        // attribue l'adresse de livraison
        $commande->setAdresseLivraison($nom.' '.$prenom.' - '.$adresseVerifiee);

        // attribue le prix de la commande 
        // calcul du prix et possibilité de promo
        $prixPerPers = (float) $menu->getPrixPerPers();
        $nbre_min = $menu->getNbreMin();
        $nbrePromo = $nbre_min + 5;

        $prixMenu = $nbreConvives * $prixPerPers;
        $montant_reduction = 0;

        if($nbreConvives >= $nbrePromo) {
            $montant_reduction = 0.1 * $prixMenu;
        }

        $sous_total = $prixMenu - $montant_reduction;

        // attribue le nombre de convives
        $commande->setNbrePers($nbreConvives);

        // sécurise le nombre de convives (doit être > au nombre min)
        if ($nbreConvives < $nbre_min) {
            return $this->redirectToRoute('app_panier');
        }

        // // attribue le prix de livraison
        $prixCalcule = $prixLivraison->calculPrixLivraison($villeVerifiee, $adresseVerifiee);
        $commande->setPrixLivraison(number_format($prixCalcule, 2, '.', ''));

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

        // attribue le statut
        $commande->setStatut(1);

        // attribue le booleen de prêt de matériel
        $commande->setPretMateriel($pret_materiel);

        // attribue le booleen de restitution de matériel
        if($pret_materiel === false){
            $commande->setRestitutionMateriel(null);
        } else {
            $commande->setRestitutionMateriel(false);
        }

        // ---------------------------------------------------------SECURISATION--------------------------------------------------------
        // Sécurise l'enregistrement de la commande et la modification du stock
        // pour éviter que deux commandes utilisent la dernière disponibilité

        $connexion = $entityManager->getConnection();
        $connexion->beginTransaction();

        try{
            // verrouille le menu
            $entityManager->lock($menu, LockMode::PESSIMISTIC_WRITE);

            // recharge les propriétés du menu depuis la BDD
            $entityManager->refresh($menu);
            
            $stockMenu = $menu->getStock();

            if ($stockMenu <= 0){
                // annule la transaction avant de rediriger
                $connexion->rollBack();
                return $this->redirectToRoute('app_panier');
            }

            // réduit le stock du menu de 1
            $menu->setStock($stockMenu - 1);

        // CREATION DE L'HISTORIQUE DE COMMANDE ASSOCIE
            // Crée un historique de commande pour chaque commande créée
            $historiqueCommande = new CommandeHistorique();

            // attribue la date de création/modification du statut
            $historiqueCommande->setDateModification($commande->getDateCommande());

            // attribue l'heure de modification du statut
            $historiqueCommande->setHeureModification($commande->getHeureCommande());

            // attribue le statut
            $historiqueCommande->setStatut($commande->getStatut());

            // lie l'historique à la commande
            $commande->addCommandeHistorique($historiqueCommande);


            $entityManager->persist($commande);
            $entityManager->persist($historiqueCommande);
            $entityManager->flush();
            // valide définitivement la transaction
            $connexion->commit();

        } catch(\Throwable $e) {
            // vérifie qu'une transaction est encore ouverte
            if($connexion->isTransactionActive()){
                // annule la transaction
                $connexion->rollBack();                
            }
            // donne l'erreur
            throw $e;
            // return $this->redirectToRoute('app_panier');
            
        }

        // Vide le panier pour éviter un rechargement de page et une seconde commande créée (si transaction effectuée)
        $panier->remove();
        return $this->redirectToRoute('app_commande_success');
    }

   

    
}
