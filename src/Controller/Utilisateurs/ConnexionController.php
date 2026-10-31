<?php

namespace App\Controller\Utilisateurs;

use App\Form\OubliPassType;
use App\Form\ResetPassType;
use App\Repository\UserRepository;
use App\Services\MailService;
use DateTime;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

final class ConnexionController extends AbstractController
{
    #[Route('/connexion', name: 'app_connexion')]
    public function index(AuthenticationUtils $authenticationUtils): Response
    {
        // get the login error if there is one
        $error = $authenticationUtils->getLastAuthenticationError();

        // last username (email) entered by the user
        $lastUsername = $authenticationUtils->getLastUsername();

        return $this->render('connexion/index.html.twig', [
            'last_username' => $lastUsername,
            'error'         => $error,
        ]);
    }

    // REINITIALISATION DU MOT DE PASSE
    #[Route('/mot-de-passe-oublie', name: 'app_mdp')]
    public function modifierPass(Request $request, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        // création du formulaire
        $oubliPassForm = $this->createForm(OubliPassType::class);
        $oubliPassForm->handleRequest($request);

        // traitement du formulaire
        if($oubliPassForm->isSubmitted() && $oubliPassForm->isValid()){
            // si email existe en bdd parmis les utilisateurs
            $adresseEmail = $oubliPassForm->get('email')->getData();
            $user = $userRepository->findOneBy(
                ['email' => $adresseEmail]
            );

            // message vague pour éviter faille de sécurité (contenu de la BDD)
            $this->addFlash(
                'success',
                'Si vous êtes inscrits sur notre site, vous recevrez un email pour réinitialiser votre mot de passe.'
            );

            if($user){
                // crée un token stocké en BDD dans l'entity User
                $token = bin2hex(random_bytes(15));
                $user->setToken($token);

                // crée un temps limite pour la réinitialisation (10min)
                $date = new DateTime();
                $date->modify('+10 minutes');
                $user->setTokenExpireDate($date);
                
                $entityManager->flush();

                // génère l'url envoyé par mail
                $lien = $this->generateUrl('app_mdp_nouveau', ['token' => $token], UrlGeneratorInterface::ABSOLUTE_URL);

                // envoie un email
                $mail = new MailService();
                $prenom = $user->getPrenom();
                $mail->envoyerMail($adresseEmail, $prenom, '', 8483968, '', $lien);


            } 

            return $this->redirectToRoute('app_mdp_reinitalise');

        }

        return $this->render('connexion/mot-de-passe-oublie.html.twig', [
            'oubliPassForm' => $oubliPassForm->createView(),
        ]);
    }

    // CONFIRMATION REINITALISATION DU MOT DE PASSE
    #[Route('/mot-de-passe-reinitialise', name: 'app_mdp_reinitalise')]
    public function confirmerPass(): Response
    {
    
        return $this->render('connexion/mot-de-passe-reinitialise.html.twig');
    }

    // NOUVEAU MOT DE PASSE
    #[Route('/mot-de-passe/nouveau/{token}', name: 'app_mdp_nouveau')]
    public function nouveauPass(Request $request, UserRepository $userRepository ,string $token, EntityManagerInterface $entityManager): Response
    {
        // si le token n'existe pas :
        if(!$token){
            return $this->redirectToRoute('app_mdp');
        }

        // récupère le client identifié grâce au token
        $user = $userRepository->findOneBy([
            'token' => $token
            ]);

        // vérifie que l'utilisateur existe, que le token n'a pas expiré et qu'il n'est pas null
        $dateActuelle = new DateTime();
        if(!$user || $dateActuelle > $user->getTokenExpireDate() || $user->getTokenExpireDate() === null){
            $this->addFlash(
                'warning',
                'Ce lien est invalide ou expiré. Veuillez effectuer une nouvelle demande.'
            );

            return $this->redirectToRoute('app_mdp');   
        }

        // création du formulaire et le rattache à User
        $resetPassForm = $this->createForm(ResetPassType::class, $user);

        // récupère le mot de passe saisi
        $resetPassForm->handleRequest($request);

        if($resetPassForm->isSubmitted() && $resetPassForm->isValid()){
            // vide le token
            $user->setToken(null);
            $user->setTokenExpireDate(null);

            // enregistre les modifications
            $entityManager->flush();

            $this->addFlash(
                'success',
                'Votre mot de passe a bien été modifié !'
            );

            return $this->redirectToRoute('app_connexion');
        }

        return $this->render('connexion/mot-de-passe-nouveau.html.twig', [
            'resetPassForm' => $resetPassForm->createView(),
        ]);
    }

}

