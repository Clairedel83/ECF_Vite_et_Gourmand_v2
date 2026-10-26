<?php

namespace App\Services;

use Symfony\Component\String\Slugger\AsciiSlugger;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class VerificationAdresseService
{
    public function __construct(private HttpClientInterface $client)
    {

    }

    public function verifierAdresse(string $adresse, string $codePostal, string $ville): array
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
            $codePostalVerifie = $data['features'][0]['properties']['postcode'];
            $villeVerifiee = $data['features'][0]['properties']['city'];

            $villeFormatee = $this->formaterVille($ville);
            $villeVerifieeFormatee = $this->formaterVille($villeVerifiee);

            // si  l'adresse est valide (type N° de rue + score élevé + codes postaux identiques + villes identiques)
            if(($typeAdresse === 'housenumber' || $typeAdresse === 'street') && $scoreAdresse >= 0.90 && $codePostalVerifie === $codePostal && $villeVerifieeFormatee === $villeFormatee){
                $adresseVerifiee = $data['features'][0]['properties']['label'];
                $codeInsee = $data['features'][0]['properties']['citycode'];
                return [
                    'ville' => $villeVerifiee,
                    'adresse' => $adresseVerifiee,
                    'codeInsee' => $codeInsee,
                    'codePostal' => $codePostalVerifie
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

    // permet de formater les villes afin de pouvoir les comparer strictement
    private function formaterVille(string $ville) : string
    {
        $slugger = new AsciiSlugger('fr');

        // trim : supprime les espaces avant et après
        // slug : transforme les accents et uniformise les séparateurs
        // lower :met en minuscules
        // toString : retourne une chaîne de caractères
        return $slugger->slug(trim($ville))->lower()->toString();
    }
}