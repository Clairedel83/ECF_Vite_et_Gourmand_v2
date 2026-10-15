<?php

namespace App\Controller\Commande;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;

final class ConfirmationController extends AbstractController
{
    // PAGE CONFIRMATION DE COMMANDE
    #[Route('/commande/confirmation', name: 'app_confirmation')]
    public function index(Request $request): Response
    {
        // récupère le numéro de la commande pour l'afficher dans twig
        $numeroCommande = $request->getSession()->get('numero_commande');

        // supprime le numéro pour ne pas le stocker indéfiniment
        $request->getSession()->remove('numero_commande');

        return $this->render('commande/confirmation.html.twig', [
            'numero_commande' => $numeroCommande
        ]);
    }    
}


