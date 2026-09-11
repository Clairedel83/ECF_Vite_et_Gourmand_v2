<?php

namespace App\Controller;

use App\Form\InscriptionUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;

final class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function index(Request $request, EntityManagerInterface $entityManager): Response
    {
        // permet de créer un nouvel utilisateur
        $user = new User();

        // rattache $user au formulaire InscriptionUserTYpe
        $form = $this->createForm(InscriptionUserType::class, $user);

        // récupère les données saisies par l'utilisateur et les associe au formulaire
        $form->handleRequest($request);

        // met à jour la BDD
        if($form->isSubmitted() && $form->isValid()){
            $entityManager->persist($user);
            $entityManager->flush();

            // redirige vers la page de connexion
            return $this->redirectToRoute('app_connexion');
        }

        // renvoie à la vue générale twig et vue du formulaire d'inscription
        return $this->render('inscription/index.html.twig', [
            'inscriptionForm' => $form->createView()
        ]);
    }
}
