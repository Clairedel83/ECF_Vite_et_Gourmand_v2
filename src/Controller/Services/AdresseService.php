<?php

namespace App\Controller\Services;

use Symfony\Contracts\HttpClient\HttpClientInterface;

class AdresseService
{
    public function __construct(private HttpClientInterface $client)
    {

    }

    public function verifierAdresse(string $adresse): array
    {
        $response = $this->client->request(
            // méthode requise par l'API
            'GET',
            // adresse de l'API où envoyer la requête
            'https://data.geopf.fr/geocodage/search',
            [
                'query' => [
                    'q' => $adresse,
                    'index' => 'address',
                    // limmite le nombre de résultat à 1
                    'limit' => 1,
                    // évite les erreurs ou corrections automatiques
                    'autocomplete' => 0
                ]
            ]
        );

        $data = $response->toArray();

        // si l'API renvoie une adresse existante (not null) 
        if(isset($data['features'][0])){
            $typeAdresse = $data['features'][0]['properties']['type'];
            $scoreAdresse = $data['features'][0]['properties']['score'];

            // si  le type d'adresse est valide (rue + N° de rue + score élevé)
            if($typeAdresse === 'housenumber' && $scoreAdresse >= 0.90){
                $villeVerifiee = $data['features'][0]['properties']['city'];
                $adresseVerifiee = $data['features'][0]['properties']['label'];
                $codeInsee = $data['features'][0]['properties']['citycode'];
                return [
                    'ville' => $villeVerifiee,
                    'adresse' => $adresseVerifiee,
                    'codeInsee' => $codeInsee
                ];
            } else {
                // sigale qu'une erreur empêche la méthode
            throw new \InvalidArgumentException('Merci de fournir une adresse valide.');
            }

        } else{
            // sigale qu'une erreur empêche la méthode
            throw new \InvalidArgumentException('Merci de fournir une adresse valide.');
        }
        
        
    }
}