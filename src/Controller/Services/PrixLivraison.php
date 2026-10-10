<?php

namespace App\Controller\Services;

use Symfony\Component\HttpFoundation\Request;

class PrixLivraison
{
    // fait appel au service DistanceService
    public function __construct(
        private DistanceService $distance_service
    )
    {
    }

    public function calculPrixLivraison(string $villeLivraison, string $adresseGoogle): float
    {

        // Si la ville de livraison est Bordeaux
        $prixLivraison = 0;

        // Si la ville de livraison n'est pas Bordeaux :
        // trim() supprime les espaces autour / strtolower() : convertis en minuscules
        if(strtolower(trim($villeLivraison)) !== 'bordeaux'){
            // récupère la distance avec Google
            $distanceLivraison = $this->distance_service->calculDistance($adresseGoogle);
            $distanceKm = $distanceLivraison / 1000;

            // Calcule le prix de livraison : round() arrondit à 2 décimales
            $prixLivraison = round(5 + (0.59 * $distanceKm), 2);
        }
        
        return $prixLivraison;
    }
}