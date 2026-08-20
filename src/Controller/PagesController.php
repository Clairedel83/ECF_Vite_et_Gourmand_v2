<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AccueilController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function accueil(): Response
    {
        return $this->render('pages/accueil.html.twig', [
            'controller_name' => 'AccueilController',
        ]);
    }

    #[Route('/a_propos', name: 'app_a_propos')]
    public function aPropos(): Response
    {
        return $this->render('pages/a_propos.html.twig', [
            'controller_name' => 'AProposController',
        ]);
    }
}
