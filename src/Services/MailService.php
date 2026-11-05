<?php

namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;

class MailService
{
    public function envoyerMail(string $to_email, string $to_nom, string $sujet, int $templateId, array $variables = []): void
    {
        // $mail->envoyerMail(('adresse du client', 'nom du client', 'sujet du mail', 'contenu du mail'))   ; 

        $mj = new Client($_ENV['MJ_APIKEY_PUBLIC'], $_ENV['MJ_APIKEY_PRIVATE'],true,['version' => 'v3.1']);

        // Define your request body
        $body = [
            'Messages' => [
                [
                    'From' => [
                        'Email' => "vite.gourmand2027@gmail.com",
                        'Name' => "Vite & Gourmand"
                    ],
                    'To' => [
                        [
                            'Email' => $to_email,
                            'Name' => $to_nom,
                        ]
                    ],
                    'Subject' => $sujet,
                    'TemplateID' => $templateId,
                    'TemplateLanguage' => true,
                    "Variables" => $variables,
                ]
            ]
        ];

        // Method pour envoyer le mail
        // envoie la demande à mailjet et récupère sa réponse
        $reponse = $mj->post(Resources::$Email, ['body' => $body]);

        // si mailjet refuse l'envoi du mail, signale l'erreur au controller qui affichera le message d'erreur
        if($reponse->success() !== true){
            throw new \RuntimeException('Mailjet a refusé la demande d\'envoi.');
        }

    }






}