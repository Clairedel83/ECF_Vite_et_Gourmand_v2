<?php

namespace App\Controller\Utilisateurs;

use App\Form\InformationsUserType;
use App\Form\PassUserType;
use App\Repository\CommandeRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CompteController extends AbstractController
{
    // PAGE COMPTE
    #[Route('/compte', name: 'app_compte')]
    public function index(CommandeRepository $commandeRepository): Response
    {
        // permet d'afficher la dernière commande effectuée
        $derniereCommande = $commandeRepository->findOneBy(
            ['user' => $this->getUser()],
            ['date_commande' => 'DESC']
        );

        return $this->render('compte/index.html.twig', [
            'derniereCommande' => $derniereCommande
        ]);
    }

    // MODIFIER LE MOT DE PASSE
    #[Route('/compte/modifier-mot-de-passe', name: 'app_compte_modifier_pass')]
    public function modifiePass(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        // permet de récupérer les données de l'utilisateur connecté
        $user = $this->getUser();

        $form = $this->createForm(PassUserType::class, $user, [
            'passwordHasher' => $passwordHasher
        ]);

        // Récupère les données transmises par l'utilisateur et les associe au formulaire
        $form->handleRequest($request);

        // permet de mettre à jour la BDD 
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();

            // ajoute une notification dont le design est noté dans base.html.twig
            $this->addFlash(
                'success',
                'Votre mot de passe a bien été modifié !'
            );
        }

        return $this->render('compte/pass.html.twig', [
            // permet d'afficher le formulaire sur la vue twig associée
            'modifierPass' => $form->createView()
        ]);
    }

    // MES INFORMATIONS
    #[Route('/compte/informations', name: 'app_compte_informations')]
    public function informations(): Response
    {
        return $this->render('compte/informations.html.twig', [
            'user' => $this->getUser()
        ]);
    }

    // MODIFIER MES INFORMATIONS
    #[Route('/compte/informations/modifier', name: 'app_compte_informations_form')]
    public function informationsForm(Request $request, EntityManagerInterface $entityManager): Response
    {
        // récupère l'utilisateur identifié
        $user = $this->getUser();
        // rattache $user au formulaire
        $form = $this->createForm(InformationsUserType::class, $user);

        // Récupère les données envoyées par l'utilisateur et les associe au formulaire
        $form->handleRequest($request);


        // permet de mettre à jour la BDD 
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            
            $this->addFlash(
                'success',
                'Vos informations ont bien été modifiées.'
            );

            return $this->redirectToRoute('app_compte_informations');
        };

        return $this->render('compte/informationsForm.html.twig', [
            'informationsForm' => $form->createView(),
        ]);
    }

    // PAGE MES COMMANDES
    #[Route('/compte/commandes', name: 'app_commandes')]
    public function show(CommandeRepository $commandeRepository): Response
    {
        // récupère toutes les commandes de l'utilisateur, classées de la plus récente à la plus ancienne
        $commandes = $commandeRepository->findBy(
            ['user' => $this->getUser()],
            ['date_commande' => 'DESC']
        );

        // Réserve cette page aux clients connectés
        $this->denyAccessUnlessGranted('ROLE_USER');

        if ($this->isGranted('ROLE_EMPLOYE')) {
            throw $this->createAccessDeniedException(
                'Cette page est réservée aux clients.'
            );
        }

        return $this->render('compte/commandes.html.twig', [
            'commandes' => $commandes
        ]);
    }

    // DETAIL DE MES COMMANDES
    #[Route('/compte/commandes/details/{id}', name: 'app_commandes_details')]
    public function details(CommandeRepository $commandeRepository, int $id): Response
    {
        // récupère la commande de l'utilisateur et la retrouve en fonction de son id
        $commande = $commandeRepository->findOneBy([
            'id' => $id,
            'user' => $this->getUser()]
        );


        return $this->render('compte/details.html.twig', [
            'commande' => $commande
        ]);
    }



}
    
