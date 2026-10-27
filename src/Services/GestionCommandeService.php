<?php

namespace App\Services;

use App\Entity\Commande;
use App\Entity\CommandeHistorique;
use Doctrine\ORM\EntityManagerInterface;
use App\Entity\InterventionCommande;
use Doctrine\DBAL\LockMode;

class GestionCommandeService
{
    // PERMET D'ANNULER UNE COMMANDE
    // void = ne return rien, effectue des actions
    public function annulerCommande(Commande $commande, EntityManagerInterface $entityManager, array $statutsAutorises,
    ?InterventionCommande $interventionCommande = null): void
    {
        // Sécurisation : pour éviter que deux employés annulent la même commande
        $connexion = $entityManager->getConnection();
        $connexion->beginTransaction();

        try{
            // VERROUILLE LA COMMANDE
            // il ne pourra y avoir de modification tant que la transaction n'est pas terminée
            $entityManager->refresh($commande, LockMode::PESSIMISTIC_WRITE);

            // VERIFIE QUE L'ANNULATION EST POSSIBLE SELON LE STATUT
            // compare (true) le statut de la commande et la liste des statuts autorisés (fournie dans le controller pour le client, dans le crudcontroller pour l'employé)
            if (!in_array($commande->getStatut(), $statutsAutorises, true)){
                throw new \DomainException(
                    'L\'annulation de commande est impossible : statut incompatible.'
                );
            }

            // VERROUILLE LE MENU
            // deux commandes annulées en même temps modifient le stock d'un menu (peut générer une erreur)
            $menu = $commande->getMenu();

            // si le menu existe toujours, rafraîchit le stock et l'incrémente
            if($menu !== null){
                $entityManager->refresh($menu, LockMode::PESSIMISTIC_WRITE);

                $menu->setStock($menu->getStock() + 1);
            }

            // Passer la commande au statut annulé.
            $commande->setStatut(8);

            // Ajouter l’historique
            $historiqueCommande = new CommandeHistorique();

            // crée la date et l'heure de modification du statut
            $date_actuelle = new \DateTime();

            // attribue le statut à l'historique de commande
            $historiqueCommande->setStatut($commande->getStatut());

            // attribue la date et l'heure de modification du statut
            $historiqueCommande->setDateModification($date_actuelle);
            $historiqueCommande->setHeureModification($date_actuelle);

            // lie l'historique à la commande
            $commande->addCommandeHistorique($historiqueCommande);

            // enregistre les modifications en BDD
            $entityManager->persist($historiqueCommande);


            // Enregistre aussi l’intervention pour le parcours employé.
            if ($interventionCommande !== null) {
                $commande->addInterventionCommande($interventionCommande);
                $entityManager->persist($interventionCommande);
            }

            // Envoie toutes les modifications, puis valide la transaction.
            $entityManager->flush();
            $connexion->commit();

        } catch(\Throwable $exception){
            // Annule les opérations de la transaction en cours
            if ($connexion->isTransactionActive()) {
                $connexion->rollBack();
            }

            // retire de la mémoire les objets modifiés
            $entityManager->clear();

            // Laisse le controller ou symfony gérer l'erreur (affichage de message, redirection etc...)
            throw $exception;
        }

    }

    // fonctions nécessaires pour le service



}
