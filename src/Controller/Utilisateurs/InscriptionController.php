<?php

namespace App\Controller\Utilisateurs;

use App\Form\InscriptionUserType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\Entity\User;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\RoleRepository;
use App\Services\MailService;

final class InscriptionController extends AbstractController
{
    #[Route('/inscription', name: 'app_inscription')]
    public function index(Request $request, EntityManagerInterface $entityManager, RoleRepository $roleRepository): Response
    {
        // permet de créer un nouvel utilisateur
        $user = new User();

        // rattache $user au formulaire InscriptionUserTYpe
        $form = $this->createForm(InscriptionUserType::class, $user);

        // récupère les données saisies par l'utilisateur et les associe au formulaire
        $form->handleRequest($request);


        // met à jour la BDD
        if($form->isSubmitted() && $form->isValid()){

            // attribut par défaut le rôle Utilisateur
            $roleUtilisateur = $roleRepository->findOneBy([
                'nom' => 'Utilisateur'
            ]);

            $user->setRole($roleUtilisateur);

            $entityManager->persist($user);
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été créé ! Vous pouvez maintenant vous connecter.'
            );

            // Envoi du mail automatique de bienvenue
            $mail = new MailService();
            $prenom = $user->getPrenom();
            $adresseMail = $user->getEmail(); 
            $mail->envoyerMail($adresseMail, $prenom, '', 8482760, '');

            // redirige vers la page de connexion
            return $this->redirectToRoute('app_connexion');
        }

        // renvoie à la vue générale twig et vue du formulaire d'inscription
        return $this->render('inscription/index.html.twig', [
            'inscriptionForm' => $form->createView()
        ]);
    }
}
