<?php

namespace App\Controller;

use App\Controller\Services\DistanceService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class TestController extends AbstractController
{
    #[Route('/test-distance', name: 'test_distance')]
    public function test(DistanceService $distanceService): Response
    {
        $adresse_test = '3 rue des cordeliers, 33000 Bordeaux';

        $distance = $distanceService->calculDistance($adresse_test);

        dd($distance);
    }
}
