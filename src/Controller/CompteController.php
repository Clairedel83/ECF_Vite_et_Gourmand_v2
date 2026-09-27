<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\InformationsUserType;
use App\Form\PassUserType;
use App\Repository\UserRepository;
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
    public function index(): Response
    {
        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
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


}
    
