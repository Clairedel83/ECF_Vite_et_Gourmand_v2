<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class PagesController extends AbstractController
{
    #[Route('/', name: 'app_accueil')]
    public function accueil(): Response
    {
        return $this->render('pages/accueil.html.twig', [
            'controller_name' => 'PagesController',
        ]);
    }

    #[Route('/a_propos', name: 'app_a_propos')]
    public function aPropos(): Response
    {
        return $this->render('pages/a_propos.html.twig', [
            'controller_name' => 'PagesController',
        ]);
    }

    #[Route('/mentions_legales', name: 'app_mentions_legales')]
    public function mentionsLegales(): Response
    {
        return $this->render('pages/mentions_legales.html.twig', [
            'controller_name' => 'PagesController',
        ]);
    }

    #[Route('/politique_confidentialite', name: 'app_politique_confidentialite')]
    public function politiqueConfidentialite(): Response
    {
        return $this->render('pages/politique_confidentialite.html.twig', [
            'controller_name' => 'PagesController',
        ]);
    }

    #[Route('/cgv', name: 'app_cgv')]
    public function cgv(): Response
    {
        return $this->render('pages/cgv.html.twig', [
            'controller_name' => 'PagesController',
        ]);
    }
}
