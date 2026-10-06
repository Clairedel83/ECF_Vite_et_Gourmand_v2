<?php

namespace App\Controller\Services;

use Symfony\Component\DependencyInjection\Attribute\Autowire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class DistanceService
{
    public function __construct(
        private HttpClientInterface $client,
        // récupère la valeur de la variable d'environnement GOOGLE_ROUTES_API_KEY
        #[Autowire('%env(GOOGLE_ROUTES_API_KEY)%')]
        private string $apiKey
    ) {
    }

    public function calculDistance(string $adresseChoisie): int
    {
        $response = $this->client->request(
        // méthode requise par Google
        'POST',
        // adresse à laquelle Google demande d'envoyer la requête
        'https://routes.googleapis.com/directions/v2:computeRoutes',
            [
                // informations qui accompagnent la requête, mais qui ne constituent pas les données origine/destination elles-mêmes
                'headers' => [
                    'X-Goog-FieldMask' => 'routes.distanceMeters',
                    // besoin d'ajouter une dépendance (Autowire) pour récupérer API_KEY dans .env.local
                    // la clé API est une autorisation d'utilisation
                    'X-Goog-Api-Key' => $this->apiKey
                ],

                'json' => [
                    'origin' => [
                        'address' => '25 rue des Vignerons, 33000 Bordeaux'
                    ],
                    'destination' => [
                        'address' => $adresseChoisie
                    ]
                ]

            ]
        );
        // permet d'obtenir une réponse Google sous forme de tableau
        $data = $response->toArray();

        // permet de sélectionner uniquement la partie distance en mètres (dans la réponse)
        $distanceMetres = $data['routes'][0]['distanceMeters'];

        return $distanceMetres;
    }
}
