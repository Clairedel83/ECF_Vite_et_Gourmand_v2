<?php

namespace App\Controller\Commande;

use App\Repository\CommandeRepository;
use App\Services\GestionCommandeService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsCsrfTokenValid;

final class AnnulerCommandeController extends AbstractController
{
    // Sécurisation du formulaire avec utilisation d'un jeton CSRF
    #[IsCsrfTokenValid('annulation_commande', tokenKey: 'token', methods: ['POST'])]
    #[Route('compte/commandes/details/{id}/supprimer', name: 'app_commandes_supprimer', methods: ['POST'])]
    public function index(CommandeRepository $commandeRepository, int $id, EntityManagerInterface $entityManager, GestionCommandeService $gestionCommandeService, Request $request): Response
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

        if ($statut !== 1) {
            $this->addFlash(
                'warning',
                'Cette commande ne peut plus être annulée. Contactez-nous si vous souhaitez l\'annuler avant préparation.'
            );
            return $this->redirectToRoute('app_commandes_details', ['id' => $id]);
        }

        // appelle le service pour annuler la commande
        try {
            // précise au service que le client ne peut intervenir que si le statut == 1
            $gestionCommandeService->annulerCommande($commande, $entityManager, [1]);
        } catch (\DomainException $exception) {
            // affiche le message d'erreur
            $this->addFlash('warning', $exception->getMessage());

            // redirige vers la page précédente = depuis laquelle la demande a été faite (referer)
            $referer = $request->headers->get('referer');

            // vérifie que la dernère page consultée correspond à une page du domaine
            if (is_string($referer) && str_starts_with($referer, $request->getSchemeAndHttpHost() . '/')) {
                return $this->redirect($referer);
            }

            // attention, en cas d'erreur, referer peut être vide donc prévoir redirection fiable
            return $this->redirectToRoute('app_commandes');
        }

        $this->addFlash(
            'success',
            'Votre commande a bien été annulée'
        );

        return $this->redirectToRoute('app_commandes_details', [
            'id' => $id
        ]);
    }
}
