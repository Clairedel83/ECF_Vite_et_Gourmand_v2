<?php

namespace App\Controller;

use App\Controller\Classe\Panier;
use App\Form\AdresseLivraisonType;
use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(Panier $panier, MenuRepository $menuRepository, Request $request): Response
    {
        // AFFICHE LE MENU SELECTIONNE
        // récupère l'id du menu présent dans le panier
        $menuId = $panier->getPanier();

        // si le panier contient un menu, récupère l'objet entier dans la BDD
        $menu = null;

        if ($menuId) {
            $menu = $menuRepository->find($menuId);
        }

        // AFFICHE LE FORMULAIRE DE MODIFICATION D'ADRESSE (livraison)
        // crée le formulaire temporaire (non sauvegardé en BDD) de l'adresse de livraison
        $form = $this->createForm(AdresseLivraisonType::class);

        // Récupère les données envoyées par l'utilisateur et les associe au formulaire
        $form->handleRequest($request);

        return $this->render('panier/index.html.twig', [
            'menu' => $menu,
            'adresseLivraisonForm' => $form->createView(),
            'user' => $this->getUser()
        ]);

    }

    // Ajoute un menu au panier
    #[Route('/panier/ajouter/{id}', name: 'app_panier_ajouter')]
    public function add(int $id, Panier $panier, MenuRepository $menuRepository): Response
    {
        // dans une session : ajoute le menu choisi par l'utilisateur au panier
        // création de la function add dans panier.php
        $menu = $menuRepository->find($id);
        
        // SECURITE : si l'utilisateur fourni un id (dans l'url) qui ne contient aucun menu, redirige
        if (!isset($menu)) {
            return $this->redirectToRoute('app_menus');
        };

        $panier->add($menu);
        return $this->redirectToRoute('app_panier');
    }

    // Retirer un menu du panier
    #[Route('/panier/retirer', name: 'app_panier_retirer')]
    public function remove(Panier $panier): Response
    {
        $panier->remove();
        return $this->redirectToRoute('app_panier');
    }
}
