<?php

namespace App\DataFixtures;

use App\Entity\Avis;
use App\Entity\Commande;
use App\Entity\CommandeHistorique;
use App\Entity\Menu;
use App\Entity\Role;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    public function __construct(
        private UserPasswordHasherInterface $passwordHasher
    ) {}

    public function load(ObjectManager $manager): void
    {
        // Utilise des données françaises et une génération reproductible.
        $faker = Factory::create('fr_FR');
        $faker->seed(20261107);

        $roleClient = $manager->getRepository(Role::class)->findOneBy([
            'nom' => 'Utilisateur',
        ]);

        if (!$roleClient) {
            throw new \RuntimeException('Le rôle Utilisateur doit déjà exister.');
        }

        // Réutilise uniquement les menus actifs déjà présents dans la base.
        $menus = $manager->getRepository(Menu::class)->findBy(
            ['isActive' => true],
            ['id' => 'ASC']
        );

        if (empty($menus)) {
            throw new \RuntimeException('Au moins un menu actif doit exister.');
        }

        // --------------------------------------------- CLIENTS ----------------------------------------------------------

        $clients = [];

        for ($i = 1; $i <= 3; $i++) {
            $email = 'vitegourmand.demo2026.client' . $i . '@yopmail.com';

            // Évite de recréer les comptes lors d'un nouveau chargement.
            $client = $manager->getRepository(User::class)->findOneBy([
                'email' => $email,
            ]);

            if (!$client) {
                $client = new User();
                $client->setEmail($email);
                $client->setPrenom($faker->firstName());
                $client->setNom($faker->lastName());
                $client->setTelephone('060000000' . $i);
                $client->setAdressePostale('10 rue Sainte-Catherine');
                $client->setCodePostal('33000');
                $client->setVille('Bordeaux');
                $client->setRole($roleClient);
                $client->setIsActive(true);

                // Enregistre uniquement le hash du mot de passe.
                $client->setPassword(
                    $this->passwordHasher->hashPassword($client, 'DemoClient2026!')
                );

                $manager->persist($client);
            }

            $clients[] = $client;
        }

        // --------------------------------------------- SCÉNARIOS DE COMMANDES ----------------------------------------------------------

        // Seize prestations terminées réparties sur l'année 2026.
        $datesTerminees = [
            '2026-01-17',
            '2026-02-14',
            '2026-03-07',
            '2026-03-21',
            '2026-04-11',
            '2026-05-09',
            '2026-05-23',
            '2026-06-06',
            '2026-06-20',
            '2026-07-04',
            '2026-07-18',
            '2026-08-08',
            '2026-08-22',
            '2026-09-12',
            '2026-09-26',
            '2026-10-17',
        ];

        $scenarios = [];

        foreach ($datesTerminees as $index => $date) {
            $scenarios[] = [
                'date' => new \DateTime($date . ' 12:00:00'),
                'statut' => 7,
                'materiel' => $index % 2 === 0,
            ];
        }

        // Les autres situations sont placées autour du jour du chargement.
        $aujourdhui = new \DateTime('today');

        $scenarios[] = [
            'date' => (clone $aujourdhui)->modify('-1 day')->setTime(12, 0),
            'statut' => 5,
            'materiel' => false,
        ];

        $scenarios[] = [
            'date' => (clone $aujourdhui)->modify('-2 days')->setTime(12, 0),
            'statut' => 6,
            'materiel' => true,
        ];

        $scenarios[] = [
            'date' => (clone $aujourdhui)->modify('+40 days')->setTime(12, 0),
            'statut' => 1,
            'materiel' => false,
        ];

        $scenarios[] = [
            'date' => (clone $aujourdhui)->modify('+45 days')->setTime(12, 0),
            'statut' => 1,
            'materiel' => true,
        ];

        $scenarios[] = [
            'date' => (clone $aujourdhui)->modify('+35 days')->setTime(12, 0),
            'statut' => 2,
            'materiel' => false,
        ];

        $scenarios[] = [
            'date' => (clone $aujourdhui)->modify('+38 days')->setTime(12, 0),
            'statut' => 2,
            'materiel' => true,
        ];

        $scenarios[] = [
            'date' => (clone $aujourdhui)->modify('+1 day')->setTime(12, 0),
            'statut' => 3,
            'materiel' => true,
        ];

        $scenarios[] = [
            'date' => (clone $aujourdhui)->setTime(18, 0),
            'statut' => 4,
            'materiel' => false,
        ];

        // --------------------------------------------- AVIS ----------------------------------------------------------

        // Les dix premiers scénarios terminés recevront un avis.
        // Les six autres resteront disponibles pour les dépôts manuels.
        $avisFictifs = [
            ['note' => 5, 'statut' => 2, 'contenu' => 'Un repas délicieux et une présentation très soignée. Nos invités se sont régalés !'],
            ['note' => 4, 'statut' => 2, 'contenu' => 'Une très bonne prestation et des portions généreuses. Nous avons particulièrement apprécié le dessert.'],
            ['note' => 5, 'statut' => 2, 'contenu' => 'Tout était parfait pour notre repas de famille. Une équipe disponible et des plats savoureux.'],
            ['note' => 5, 'statut' => 2, 'contenu' => 'Une belle découverte ! La commande était conforme à nos attentes et les produits étaient de qualité.'],
            ['note' => 4, 'statut' => 2, 'contenu' => 'Nous avons passé un excellent moment. Les plats étaient très bons, avec une petite préférence pour l’entrée.'],
            ['note' => 5, 'statut' => 2, 'contenu' => 'Livraison ponctuelle et repas apprécié par tous nos invités. Nous referons appel à vous avec plaisir.'],
            ['note' => 4, 'statut' => 2, 'contenu' => 'Une prestation sérieuse et un menu gourmand. Une expérience très agréable pour notre réception.'],
            ['note' => 5, 'statut' => 1, 'contenu' => 'Merci pour cette belle prestation. Tout le monde a apprécié le repas et la qualité du service.'],
            ['note' => 4, 'statut' => 1, 'contenu' => 'Un très bon repas partagé entre amis. Les quantités étaient adaptées et les saveurs au rendez-vous.'],
            ['note' => 5, 'statut' => 3, 'contenu' => 'Message publicitaire sans rapport avec la prestation : découvrez notre offre commerciale sur un autre site.'],
        ];

        // --------------------------------------------- CRÉATION DES COMMANDES ----------------------------------------------------------

        foreach ($scenarios as $index => $scenario) {
            // Un numéro fixe permet de reconnaître chaque commande de démonstration.
            $numero = 'VG-DEMO-2026-' . str_pad(
                (string) ($index + 1),
                3,
                '0',
                STR_PAD_LEFT
            );

            // Ne recrée pas les commandes ni leurs historiques lors d'une relance.
            $commandeExistante = $manager->getRepository(Commande::class)->findOneBy([
                'numero_commande' => $numero,
            ]);

            if ($commandeExistante) {
                continue;
            }

            $dateLivraison = clone $scenario['date'];

            // Retient les menus disponibles à cette période et ayant encore du stock.
            $menusDisponibles = [];

            foreach ($menus as $menu) {
                if ($menu->getStock() > 0 && $this->menuDisponible($menu, $dateLivraison)) {
                    $menusDisponibles[] = $menu;
                }
            }

            if (empty($menusDisponibles)) {
                throw new \RuntimeException(
                    'Aucun menu disponible avec du stock pour la commande ' . $numero
                        . ' du ' . $dateLivraison->format('d/m/Y') . '.'
                );
            }

            // Alterne les menus et les clients.
            $menu = $menusDisponibles[$index % count($menusDisponibles)];
            $client = $clients[$index % count($clients)];

            // Retrouve le délai minimum prévu dans les conditions du menu.
            $delaiMinimum = 0;

            foreach ($menu->getConditions() as $condition) {
                if ($condition->getDelaiMin() > $delaiMinimum) {
                    $delaiMinimum = $condition->getDelaiMin();
                }
            }

            $dateCommande = clone $dateLivraison;
            $dateCommande->modify('-' . ($delaiMinimum + 7) . ' days');
            $dateCommande->setTime(10, 0);

            // Pour une prestation à venir, la commande existe déjà aujourd'hui.
            if ($dateCommande > $aujourdhui) {
                $dateCommande = (clone $aujourdhui)->modify('-2 days')->setTime(10, 0);
            }

            $nombreConvives = $menu->getNbreMin() + $faker->numberBetween(0, 15);

            // Effectue les calculs en centimes pour conserver des montants à deux décimales.
            $prixPersonne = (int) round((float) $menu->getPrixPerPers() * 100);
            $prixMenu = $prixPersonne * $nombreConvives;
            $reduction = 0;

            if ($nombreConvives >= $menu->getNbreMin() + 5) {
                $reduction = (int) round($prixMenu * 0.1);
            }

            $sousTotal = $prixMenu - $reduction;
            $prixMateriel = 0;

            if ($scenario['materiel']) {
                $prixMateriel = 2000;
            }

            // Les livraisons à Bordeaux sont gratuites dans le service actuel.
            $prixLivraison = 0;
            $prixTotal = $sousTotal + $prixMateriel + $prixLivraison;

            $commande = new Commande();
            $commande->setNumeroCommande($numero);
            $commande->setUser($client);
            $commande->setMenu($menu);
            $commande->setMenuNom($menu->getNom());

            $commande->setMenuComposition(
                'Entrée : ' . $menu->getEntree()->getNom()
                    . ', Plat : ' . $menu->getPlat()->getNom()
                    . ', Dessert : ' . $menu->getDessert()->getNom() . '.'
            );

            $commande->setDateCommande(clone $dateCommande);
            $commande->setHeureCommande(clone $dateCommande);
            $commande->setDateLivraison(clone $dateLivraison);
            $commande->setHeureLivraison(clone $dateLivraison);

            $commande->setAdresseLivraison(
                $client->getNom() . ' ' . $client->getPrenom()
                    . ' - ' . $client->getAdressePostale()
                    . ', ' . $client->getCodePostal() . ' ' . $client->getVille()
            );

            $commande->setNbrePers($nombreConvives);
            $commande->setPrixPerPers($this->formaterMontant($prixPersonne));
            $commande->setMontantReduction($this->formaterMontant($reduction));
            $commande->setPrixSousTotal($this->formaterMontant($sousTotal));
            $commande->setPrixLivraison($this->formaterMontant($prixLivraison));
            $commande->setPrixMateriel($this->formaterMontant($prixMateriel));
            $commande->setPrixTotal($this->formaterMontant($prixTotal));
            $commande->setStatut($scenario['statut']);
            $commande->setPretMateriel($scenario['materiel']);

            // Le matériel a été rendu lorsque la commande avec prêt est terminée.
            $commande->setRestitutionMateriel(
                $scenario['materiel'] && $scenario['statut'] === 7
            );

            $manager->persist($commande);

            // Applique le même retrait de stock que lors d'une création réelle.
            $menu->setStock($menu->getStock() - 1);

            // --------------------------------------------- HISTORIQUE ----------------------------------------------------------

            $statuts = [1, 2, 3, 4, 5];

            if ($scenario['materiel']) {
                $statuts[] = 6;
            }

            $statuts[] = 7;
            $dateDernierStatut = clone $dateCommande;

            foreach ($statuts as $statut) {
                if ($statut > $scenario['statut']) {
                    break;
                }

                $dateStatut = clone $dateLivraison;

                if ($statut === 1) {
                    $dateStatut = clone $dateCommande;
                } elseif ($statut === 2) {
                    $dateStatut = (clone $dateCommande)->modify('+1 day');
                } elseif ($statut === 3) {
                    $dateStatut->modify('-1 day')->setTime(9, 0);
                } elseif ($statut === 4) {
                    $dateStatut->modify('-1 hour');
                } elseif ($statut === 5) {
                    $dateStatut->modify('+30 minutes');
                } elseif ($statut === 6) {
                    $dateStatut->modify('+1 hour');
                } elseif ($statut === 7) {
                    $dateStatut->modify('+1 day');
                }

                // Un statut déjà atteint ne doit pas avoir une date future.
                $maintenant = new \DateTime();

                if ($dateStatut > $maintenant) {
                    $dateStatut = clone $maintenant;
                }

                $historique = new CommandeHistorique();
                $historique->setCommande($commande);
                $historique->setStatut($statut);
                $historique->setDateModification(clone $dateStatut);
                $historique->setHeureModification(clone $dateStatut);

                $manager->persist($historique);
                $dateDernierStatut = clone $dateStatut;
            }

            // --------------------------------------------- AVIS DE LA COMMANDE ----------------------------------------------------------

            if (isset($avisFictifs[$index])) {
                $donneesAvis = $avisFictifs[$index];

                $avis = new Avis();
                $avis->setCommande($commande);
                $avis->setNote($donneesAvis['note']);
                $avis->setContenu($donneesAvis['contenu']);
                $avis->setStatut($donneesAvis['statut']);

                // L'avis est déposé après la fin de la prestation.
                $avis->setDateCreation(
                    \DateTimeImmutable::createFromMutable($dateDernierStatut)->modify('+2 days')
                );

                $manager->persist($avis);
            }
        }

        // Enregistre l'ensemble sans appeler les contrôleurs ni le service de mails.
        $manager->flush();
    }

    // Convertit les centimes en chaîne décimale pour les champs DECIMAL de Doctrine.
    private function formaterMontant(int $centimes): string
    {
        return number_format($centimes / 100, 2, '.', '');
    }

    // Vérifie la période annuelle de disponibilité d'un menu.
    private function menuDisponible(Menu $menu, \DateTime $date): bool
    {
        $debut = $menu->getDisponibiliteDebut();
        $fin = $menu->getDisponibiliteFin();

        if (!$debut || !$fin) {
            return true;
        }

        // Transforme JJ-MM en MM-JJ pour comparer les périodes.
        [$jourDebut, $moisDebut] = explode('-', $debut);
        [$jourFin, $moisFin] = explode('-', $fin);

        $debut = $moisDebut . '-' . $jourDebut;
        $fin = $moisFin . '-' . $jourFin;
        $jourLivraison = $date->format('m-d');

        if ($debut <= $fin) {
            return $jourLivraison >= $debut && $jourLivraison <= $fin;
        }

        // Prend aussi en compte une période à cheval sur deux années.
        return $jourLivraison >= $debut || $jourLivraison <= $fin;
    }
}
