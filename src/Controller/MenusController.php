<?php

namespace App\Controller;

use App\Repository\MenuRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class MenusController extends AbstractController
{
    #[Route('/nos_menus', name: 'app_menus')]
    public function index(MenuRepository $menuRepository): Response
    {

        $menus = $menuRepository->findAll();

        return $this->render('menus/index.html.twig', [
            'menus' => $menus,
        ]);
    }

}
