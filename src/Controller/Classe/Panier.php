<?php

namespace App\Controller\Classe;

use App\Entity\Menu;
use Symfony\Component\HttpFoundation\RequestStack;

class Panier
{
    // permet d'accéder à la requête HTTP et de la conserver (nécessaire pour la fonction suivante)
    public function __construct(private RequestStack $requestStack)
    {

    }

    // Récupère le panier dans la session
    // S'il n'existe pas encore, retourne null
    public function getPanier()
    {
        return $this->requestStack->getSession()->get('panier');
    }

    // permet d'ajouter un menu au panier
    public function add(Menu $menu)
    {
        // met à jour le panier en y enregistrant l'id du menu choisi par l'utilisateur
        $this->requestStack->getSession()->set('panier', $menu->getId());
    }


    // permet de retirer un menu du panier
    public function remove()
    {
        // supprime le menu enregistré dans le panier
        $this->requestStack->getSession()->remove('panier');
    }

}