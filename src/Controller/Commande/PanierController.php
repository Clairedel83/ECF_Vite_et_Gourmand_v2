<?php

namespace App\Controller\Commande;

use App\Form\AdresseLivraisonType;
use App\Repository\MenuRepository;
use App\Services\PanierService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PanierController extends AbstractController
{
    #[Route('/panier', name: 'app_panier')]
    public function index(PanierService $panier, MenuRepository $menuRepository, Request $request): Response
    {
        // AFFICHE LE MENU SELECTIONNE
        // récupère l'id du menu présent dans le panier
        $menuId = $panier->getPanier();

        // si le panier contient un menu, récupère l'objet entier dans la BDD
        $menu = null;

        if ($menuId) {
            $menu = $menuRepository->find($menuId);

            // Retire un menu devenu indisponible depuis son ajout au panier
            if (!$menu || !$menu->isActive()) {
                $panier->remove();
                $menu = null;

                $this->addFlash(
                    'warning',
                    'Le menu de votre panier n’est plus disponible. Veuillez choisir un autre menu.'
                );

                return $this->redirectToRoute('app_menus');
            };
        };

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
    public function add(int $id, PanierService $panier, MenuRepository $menuRepository): Response
    {
        // dans une session : ajoute le menu choisi par l'utilisateur au panier
        // création de la function add dans panier.php
        $menu = $menuRepository->find($id);
        
        // SECURITE : Refuse un menu inexistant ou désactivé, même depuis une ancienne URL
        if (!$menu || !$menu->isActive()) {
            $this->addFlash(
                'warning',
                'Ce menu n’est plus disponible.'
            );

            return $this->redirectToRoute('app_menus');
        }

        $panier->add($menu);
        return $this->redirectToRoute('app_panier');
    }

    // Retirer un menu du panier
    #[Route('/panier/retirer', name: 'app_panier_retirer')]
    public function remove(PanierService $panier): Response
    {
        $panier->remove();
        return $this->redirectToRoute('app_panier');
    }
}
