<?php

namespace App\Services;

use App\Entity\Commande;
use App\Entity\CommandeHistorique;
use Doctrine\ORM\EntityManagerInterface;

class GestionCommandeService
{
    // permet de créer une commande
    public function creerCommande() {}

    //permet de modifier une commande
    public function modifierCommande() {}

    // permet d'annuler une commande
    // void = ne return rien, effectue des actions
    public function annulerCommande(Commande $commande, EntityManagerInterface $entityManager): void
    {
        // Vérifier que l’annulation reste possible.
        $statutCommande = $commande->getStatut();
        if ($statutCommande > 3) {
            throw new \DomainException(
                'L\'annulation de commande est impossible : statut incompatible.'
            );
        }

        // Passer la commande au statut annulé.
        $commande->setStatut(8);

        // restitue le stock d'origine au menu
        $stockMenu = $commande->getMenu()->getStock() + 1;
        $commande->getMenu()->setStock($stockMenu);

        // Ajouter l’historique
        $historiqueCommande = new CommandeHistorique();

        // attribue le statut à l'historique de commande
        $historiqueCommande->setStatut($commande->getStatut());

        // attribue la date et l'heure de modification du statut
        $date_actuelle = new \DateTime();

        $historiqueCommande->setDateModification($date_actuelle);
        $historiqueCommande->setHeureModification($date_actuelle);

        // lie l'historique à la commande
        $commande->addCommandeHistorique($historiqueCommande);

        // enregistre les modifications en BDD
        $entityManager->persist($historiqueCommande);
        $entityManager->flush();
    }

    // fonctions nécessaires pour le service



}
