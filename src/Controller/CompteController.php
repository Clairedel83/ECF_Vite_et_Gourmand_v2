<?php

namespace App\Controller;

use App\Form\PassUserType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Attribute\Route;

final class CompteController extends AbstractController
{
    #[Route('/compte', name: 'app_compte')]
    public function index(): Response
    {
        return $this->render('compte/index.html.twig', [
            'controller_name' => 'CompteController',
        ]);
    }

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
}
    
