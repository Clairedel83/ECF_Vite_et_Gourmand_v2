<?php

namespace App\Controller\Commande;

use App\Controller\Services\PrixLivraison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// reçoit la demande de JavaScript, appelle le service et renvoie le prix au format JSON pour affichage
final class CalculPrixLivraisonController extends AbstractController
{
    #[Route('/calcul/prix/livraison', name: 'app_calcul_prix_livraison', methods: ['POST'])]
    public function index(Request $request, PrixLivraison $prixLivraison): JsonResponse
    {
        // Récupère l'adresse et la ville de livraison depuis JS
        $adresseGoogle = $request->request->get('adresse');
        $villeLivraison = $request->request->get('ville');

        $prixCalcule = $prixLivraison->calculPrixLivraison($villeLivraison, $adresseGoogle);

        return $this->json([
            'prixLivraison' => $prixCalcule
        ]);
    }

    
}
