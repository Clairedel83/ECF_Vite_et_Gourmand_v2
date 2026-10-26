<?php

namespace App\Controller\Commande;

use App\Services\VerificationAdresseService;
use App\Services\CalculPrixLivraison;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;

// reçoit la demande de JavaScript, appelle le service et renvoie le prix au format JSON pour affichage
final class CalculPrixLivraisonController extends AbstractController
{
    #[Route('/calcul/prix/livraison', name: 'app_calcul_prix_livraison', methods: ['POST'])]
    public function index(Request $request, CalculPrixLivraison $calculPrixLivraison, VerificationAdresseService $verificationAdresseService): JsonResponse
    {
        // Récupère l'adresse de livraison et le code postal donnés par l'utilisateur depuis JS
        $adresseGoogle = $request->request->get('adresse');
        $codePostal = $request->request->get('codePostal');
        $ville = $request->request->get('ville');

        // appelle verifierAdresse de AdresseService pour vérifier cette adresse par géocodage
        try{
            $resultatAdresse = $verificationAdresseService->verifierAdresse($adresseGoogle, $codePostal, $ville);
        // en cas d'erreur (400 = données invalides : renvoie message)
        } catch (\InvalidArgumentException $e){
            return $this ->json([
                'erreur'=> $e->getMessage()
             ], 400);
        }

        
        $villeVerifiee = $resultatAdresse['ville'];
        $adresseCompleteVerifiee = $resultatAdresse['adresse'];

        $prixCalcule = $calculPrixLivraison->calculPrixLivraison($villeVerifiee, $adresseCompleteVerifiee);

        return $this->json([
            'prixLivraison' => $prixCalcule
        ]);
    }

    
}
