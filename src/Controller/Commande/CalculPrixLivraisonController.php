<?php

namespace App\Controller\Commande;

use App\Services\VerificationAdresseService;
use App\Services\CalculPrixLivraison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Contracts\HttpClient\Exception\ExceptionInterface;

// Fournit au JavaScript les frais calculés ou leur caractère provisoire.
final class CalculPrixLivraisonController extends AbstractController
{
    #[Route('/calcul/prix/livraison', name: 'app_calcul_prix_livraison', methods: ['POST'])]
    public function index(
        Request $request,
        CalculPrixLivraison $calculPrixLivraison,
        VerificationAdresseService $verificationAdresseService
    ): JsonResponse {
        // Récupère les informations transmises par JavaScript.
        $adresseGoogle = $request->request->get('adresse');
        $codePostal = $request->request->get('codePostal');
        $ville = $request->request->get('ville');

        // Refuse une demande incomplète avant d'appeler les services.
        if (
            !is_string($adresseGoogle) || trim($adresseGoogle) === ''
            || !is_string($codePostal) || trim($codePostal) === ''
            || !is_string($ville) || trim($ville) === ''
        ) {
            return $this->json([
                'erreur' => 'Veuillez renseigner l’adresse, le code postal et la ville.',
            ], 400);
        }

        try {
            // Vérifie l'adresse principale.
            $resultatAdresse = $verificationAdresseService->verifierAdresse(
                $adresseGoogle,
                $codePostal,
                $ville
            );

            // Calcule les frais à partir de l'adresse reconnue.
            $prixCalcule = $calculPrixLivraison->calculPrixLivraison(
                $resultatAdresse['ville'],
                $resultatAdresse['adresse']
            );

            return $this->json([
                'prixLivraison' => $prixCalcule,
                'livraisonAVerifier' => false,
            ]);
        } catch (
            \InvalidArgumentException
            | ExceptionInterface
            | \UnexpectedValueException $e
        ) {
            // Adresse non reconnue ou calcul indisponible :
            // permet au client de poursuivre avec des frais provisoires.
            return $this->json([
                'prixLivraison' => 0,
                'livraisonAVerifier' => true,
                'message' => 'Les frais de livraison n’ont pas pu être calculés. Ils sont provisoirement fixés à 0 €. Notre équipe vous contactera pour vérifier la livraison et ajuster ces frais si nécessaire. Vous pouvez confirmer votre commande.',
            ]);
        }
    }
}
