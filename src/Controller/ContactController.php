<?php

namespace App\Controller;

use App\Form\ContactType;
use App\Services\MailService;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class ContactController extends AbstractController
{
    #[Route('/contact', name: 'app_contact')]
    public function index(Request $request): Response
    {

        // crée le formulaire de contact
        $form_contact = $this->createForm(ContactType::class);

        // récupère les données saisies par l'utilisateur
        $form_contact->handleRequest($request);

        if ($form_contact->isSubmitted() &&  $form_contact->isValid()) {
            // si le formulaire est valide et soumis :
            $mail = new MailService();
            $adresseMail = 'vite.gourmand2027@gmail.com';
            $variables = [
                'sujetMessage' => $form_contact->get('sujet')->getData(),
                'message' => $form_contact->get('message')->getData(),
                'nom' => $form_contact->get('nom')->getData(),
                'email' => $form_contact->get('email')->getData(),
            ];

            // tente l'envoi du mail
            try {
                $mail->envoyerMail($adresseMail, 'Vite & Gourmand', '', 8498138, $variables);
                // en cas d'erreur :
            } catch (\Exception $exception) {
                // conserve les éléments du formulaire et affiche le message d'erreur pour retenter
                $this->addFlash(
                    'warning',
                    'Votre message n\'a pas pu être envoyé, veuillez réessayer.'
                );

                return $this->render('contact/index.html.twig', [
                    'formContact' => $form_contact->createView()
                ]);
            }

            // si l'envoi a fonctionné :
            $this->addFlash(
                'success',
                'Votre demande a bien été envoyée à notre équipe.'
            );

            // évite un nouvel envoi lors de l'actualisation de la page
            return $this->redirectToRoute('app_contact');
        }

        return $this->render('contact/index.html.twig', [
            'formContact' => $form_contact->createView(),
        ]);
    }
}
