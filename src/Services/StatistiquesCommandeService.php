<?php

namespace App\Services;

use App\Entity\Commande;
use MongoDB\Client;
use MongoDB\Collection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;
use MongoDB\BSON\Decimal128;
use MongoDB\BSON\UTCDateTime;

class StatistiquesCommandeService
{
    private Collection $collection;

    public function __construct(Client $client, #[Autowire('%env(MONGODB_DATABASE)%')] string $databaseName)
    {
        $this->collection = $client->selectDatabase($databaseName)->selectCollection('commandes');
    }

    // convertis l'entité Commande en document Mongodb
    public function enregistrerCommande(Commande $commande): void
    {
        $document = [
            'commandeId' => $commande->getId(),
            'menuId' => $commande->getMenu()->getId(),
            'menuNom' => $commande->getMenuNom(),
            'statut' => $commande->getStatut(),

            // Convertit les dates PHP en dates BSON pour MongoDB : cela permettra de les manipuler ensuite
            'dateCommande' => new UTCDateTime($commande->getDateCommande()),
            'dateLivraison' => new UTCDateTime($commande->getDateLivraison()),

            // Decimal128 = onserve les montants décimaux sans passer par un float
            'montantMenu' => new Decimal128($commande->getPrixSousTotal()),
            'montantTotal' => new Decimal128($commande->getPrixTotal()),
        ];

        // Recherche le document de cette commande, puis le crée ou le met à jour.
        $this->collection->updateOne(
            ['_id' => $commande->getId()],
            ['$set' => $document],
            ['upsert' => true]
        );
    }

    // Compte les commandes non annulées pour chaque menu.
    public function compterCommandesParMenu(array $filtres = []): array
    {
        // Ajoute le critère de statut aux éventuels filtres de date et de menu.
        $filtres['statut'] = ['$ne' => 8];

        $resultats = $this->collection->aggregate([
            ['$match' => $filtres],

            [
                '$group' => [
                    '_id' => '$menuId',
                    'menuNom' => ['$first' => '$menuNom'],
                    'nombreCommandes' => ['$sum' => 1],
                ],
            ],

            // Le nom sert à départager les menus ayant le même nombre de commandes.
            [
                '$sort' => [
                    'nombreCommandes' => -1,
                    'menuNom' => 1,
                ],
            ],
        ]);

        return $resultats->toArray();
    }

    // Calcule le CA global par mois de livraison.
    public function calculerCaParMois(array $filtres = []): array
    {
        $filtres['statut'] = ['$in' => [5, 6, 7]];

        $resultats = $this->collection->aggregate([
            ['$match' => $filtres],

            [
                '$group' => [
                    '_id' => [
                        '$dateToString' => [
                            'format' => '%Y-%m',
                            'date' => '$dateLivraison',
                            'timezone' => 'Europe/Paris',
                        ],
                    ],
                    'chiffreAffaires' => ['$sum' => '$montantTotal'],
                ],
            ],

            ['$sort' => ['_id' => 1]],
        ]);

        $caParMois = [];

        // Transmet des valeurs simples au JavaScript.
        foreach ($resultats as $resultat) {
            $caParMois[] = [
                'mois' => $resultat['_id'],
                'chiffreAffaires' => (string) $resultat['chiffreAffaires'],
            ];
        }

        return $caParMois;
    }

    // Calcule les deux CA affichés dans les cartes d'indicateurs.
    public function calculerCaTotal(array $filtres = []): array
    {
        $filtres['statut'] = ['$in' => [5, 6, 7]];

        $resultats = $this->collection->aggregate([
            ['$match' => $filtres],

            // Regroupe toutes les commandes retenues dans un seul résultat.
            [
                '$group' => [
                    '_id' => null,
                    'caMenus' => ['$sum' => '$montantMenu'],
                    'caGlobal' => ['$sum' => '$montantTotal'],
                ],
            ],
        ])->toArray();

        // Affiche zéro lorsqu'aucune prestation ne correspond aux filtres.
        if (empty($resultats)) {
            return [
                'caMenus' => '0',
                'caGlobal' => '0',
            ];
        }

        return [
            'caMenus' => (string) $resultats[0]['caMenus'],
            'caGlobal' => (string) $resultats[0]['caGlobal'],
        ];
    }

    // Récupère les menus présents dans les statistiques pour le filtre
    public function recupererMenus(): array
    {
        $resultats = $this->collection->aggregate([
            [
                '$group' => [
                    '_id' => '$menuId',
                    'menuNom' => ['$first' => '$menuNom'],
                ],
            ],

            ['$sort' => ['menuNom' => 1]],
        ]);

        return $resultats->toArray();
    }
}
