<?php

namespace App\Controller\Commande;

use App\Entity\CommandeHistorique;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

final class AnnulerCommandeController extends AbstractController
{
    // Sécurisation du formulaire avec utilisation d'un jeton CSRF
    #[IsCsrfTokenValid('annulation_commande', tokenKey: 'token', methods: ['POST'])]    
    #[Route('compte/commandes/details/{id}/supprimer', name: 'app_commandes_supprimer', methods: ['POST'])]
    public function index(CommandeRepository $commandeRepository, int $id, EntityManagerInterface $entityManager): Response
    {

        // Récupère la commande appartenant à l'utilisateur
        $commande = $commandeRepository->findOneBy([
            'id' => $id,
            'user' => $this->getUser()
        ]);

        // Vérifie que la commande existe
        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        // Vérifie que le statut de la commande permet une modification
        $statut = $commande->getStatut();

        if ($statut !== 1){
            return $this->redirectToRoute('app_commandes_details', ['id' => $id]);
        }

        // ANNULATION DE LA COMMANDE
        // attribue le statut
        $commande->setStatut(8);

        // restitue le stock d'origine au menu
        $stockMenu = $commande->getMenu()->getStock() + 1;
        $commande->getMenu()->setStock($stockMenu);


        // CREATION D'UN NOUVEL HISTORIQUE DE COMMANDE
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

        $this->addFlash(
            'success',
            'Votre commande a bien été annulée'
        );

        return $this->redirectToRoute('app_commandes_details', [
            'id' => $id
        ]);


    }
}
