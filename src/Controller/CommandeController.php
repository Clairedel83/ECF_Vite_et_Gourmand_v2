<?php

namespace App\Controller;

use App\Controller\Classe\Panier;
use App\Entity\Commande;
use App\Entity\CommandeHistorique;
use App\Repository\MenuRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class CommandeController extends AbstractController
{
    #[Route('/commande', name: 'app_commande')]
    public function index(MenuRepository $menuRepository, Panier $panier, Request $request, EntityManagerInterface $entityManager): Response
    {
        // La page ne peut être appelée que par l'envoi du formulaire
        // Si l'utilisateur essaie d'y accéder par l'URL (GET), il sera redirigé vers le panier
        if($request->getMethod() != 'POST'){
            return $this->redirectToRoute('app_panier');
        }

    // RECUPERE LES ELEMENTS NECESSAIRES A LA CONSTRUCTION DE COMMANDE
        // RECUPERE L'UTILISATEUR CONNECTE
        $user = $this->getUser();

        // RECUPERE LE MENU
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

        // RECUPERE LE NOMBRE DE CONVIVES : et transforme en int pour enregistrer en format compatible en BDD
        $nbreConvives = (int) $request->request->get('form_nbre_convives');

        // RECUPERE LA DATE DE LIVRAISON : et transforme en objet datetime pour enregistrer en format compatible en BDD
        $date_livraison = new \DateTime(
            $request->request->get('form_date'));

        // RECUPERE L'HEURE DE LIVRAISON : et transforme en objet datetime pour enregistrer en format compatible en BDD
        $heure_livraison = new \DateTime(
            $request->request->get('form_heure'));

        // RECUPERE LE CHOIX DU MATERIEL (avec ou sans location) : et transforme en booleen pour enregistrer en format compatible en BDD
        $pret_materiel = $request->request->get('form_materiel') === 'oui';

        // RECUPERE L'ADRESSE DE LIVRAISON (user OU adresse modifiée)
        $adresse_livraison = $request->request->get('form_adresse_choisie');
        
    // CREATION DE LA COMMANDE
        $commande = new Commande();

        // attribue la commande à un utilisateur
        $commande->setUser($user);

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
        $commande->setAdresseLivraison($adresse_livraison);

        // attribue le prix de la commande 
        // calcul du prix et possibilité de promo
        $prixPerPers = (float) $menu->getPrixPerPers();
        $nbre_min = $menu->getNbreMin();
        $nbrePromo = $nbre_min + 5;

        $totalPrix = $nbreConvives * $prixPerPers;

        if($nbreConvives >= $nbrePromo) {
            $totalPrix = ($totalPrix) - (0.1 * $totalPrix);
        }

        $commande->setPrixMenu($totalPrix);

        // attribue le nombre de convives
        $commande->setNbrePers($nbreConvives);

        // attribue le prix de livraison

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

        // Vide le panier pour éviter un rechargement de page et une seconde commande créée 
        $panier->remove();

        return $this->redirectToRoute('app_commande_success');
    }

    #[Route('/commande/succes', name: 'app_commande_success')]
    public function success(): Response
    {
        return $this->render('commande/success.html.twig');
    }
}
