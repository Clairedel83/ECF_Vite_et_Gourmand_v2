<?php

namespace App\Controller\Commande;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Response;


final class ConfirmationController extends AbstractController
{
     #[Route('/commande/succes', name: 'app_commande_success')]
    public function success(): Response
    {
        return $this->render('commande/success.html.twig');
    }
}

