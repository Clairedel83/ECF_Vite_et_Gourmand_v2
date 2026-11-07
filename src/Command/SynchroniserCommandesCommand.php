<?php

namespace App\Command;

use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Style\SymfonyStyle;
use App\Repository\CommandeRepository;
use App\Services\StatistiquesCommandeService;

#[AsCommand(
    name: 'app:synchroniser-commandes',
    description: 'Synchronise les commandes SQL vers MongoDB',
)]
class SynchroniserCommandesCommand extends Command
{
    public function __construct(private CommandeRepository $commandeRepository, private StatistiquesCommandeService $statistiquesCommandeService)
    {
        parent::__construct();
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        // Prépare l'affichage des messages dans le terminal
        // SymfonyStyle facilite l'affichage avec success, warning, error
        $io = new SymfonyStyle($input, $output);

        // Récupère toutes les commandes enregistrées en SQL
        $commandes = $this->commandeRepository->findAll();

        // Compte les synchronisations réussies et les échecs
        $reussites = 0;
        $echecs = 0;

        foreach ($commandes as $commande) {
            try {
                // Crée ou actualise le document MongoDB de la commande
                $this->statistiquesCommandeService->enregistrerCommande($commande);

                // ajoute une reussite au compteur
                $reussites++;
            } catch (\Exception $exception) {
                // Continue avec les autres commandes si une synchronisation échoue et ajoute un échec au compteur
                $echecs++;

                $io->warning(
                    'Échec de la synchronisation de la commande n°' . $commande->getId()
                );
            }
        }

        // Affiche le bilan de la synchronisation
        if ($echecs > 0) {
            $io->error($reussites . ' commande(s) synchronisée(s), ' . $echecs . ' échec(s).');

            return Command::FAILURE;
        }

        $io->success($reussites . ' commande(s) synchronisée(s).');

        return Command::SUCCESS;
    }
}
