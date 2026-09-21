<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use App\Repository\RegimeRepository;
use App\Repository\ThemeRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenusController extends AbstractController
{
    #[Route('/nos_menus', name: 'app_menus')]
    public function index(MenuRepository $menuRepository, ThemeRepository $themeRepository, RegimeRepository $regimeRepository): Response
    {

        $menus = $menuRepository->findAll();
        $themes = $themeRepository->findAll();
        $regimes = $regimeRepository->findAll();

        return $this->render('menus/index.html.twig', [
            'menus' => $menus,
            'themes' => $themes,
            'regimes' => $regimes,
        ]);
    }

}
