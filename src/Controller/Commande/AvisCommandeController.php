<?php

namespace App\Controller\Commande;

use App\Entity\Avis;
use App\Form\AvisClientType;
use App\Repository\CommandeRepository;
use DateTimeImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class AvisCommandeController extends AbstractController
{
    #[Route('/commande/{id}/avis', name: 'app_avis')]
    public function index(Request $request, CommandeRepository $commandeRepository, int $id, EntityManagerInterface $entityManager): Response
    {
        // récupère la commande et l'utilisateur concernés
        $commande = $commandeRepository->findOneBy([
            'id' => $id,
            'user' => $this->getUser()
        ]);

        // Vérifie que la commande existe
        if (!$commande) {
            throw $this->createNotFoundException('Commande introuvable.');
        }

        // Vérifie que le statut de commande permet d'ajouter un avis
        $statutCommande = $commande->getStatut();

        if($statutCommande !== 7){
            $this->addFlash(
                'warning',
                'Cette commande est toujours en cours. Attendez qu\'elle soit terminée pour envoyer votre avis.'
            );
            return $this->redirectToRoute('app_accueil');
        }

        // Vérifie que cette commande n'a pas déjà un avis
        if($commande->getAvis() !== null){
            $this->addFlash(
                'warning',
                'Cette commande possède déjà un avis.'
            );
            return $this->redirectToRoute('app_accueil');
        }



        // crée un nouvel avis
        $avis = new Avis();

        // crée le formulaire
        $formAvis = $this->createForm(AvisClientType::class, $avis);

        // récupère les données saisies par l'utilisateur et les associe au formulaire
        $formAvis->handleRequest($request);


        if($formAvis->isSubmitted() && $formAvis->isValid()){
            // rattache l'avis à la commande
            $commande->setAvis($avis);

            // crée et rattache la date du jour
            $avis->setDateCreation(new DateTimeImmutable());

            // conserve le statut initial de l'avis
            $avis->setStatut(1);

            // enregistre en BDD
            $entityManager->persist($avis);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre avis a bien été envoyé à notre équipe. Il sera publié après validation par notre équipe.'
            );

            return $this->redirectToRoute('app_accueil');
        }

        return $this->render('commande/avisCommande.html.twig', [
            'formAvis' => $formAvis->createView()
        ]);
    }
}
