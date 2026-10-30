<?php

namespace App\Services;

use Mailjet\Client;
use Mailjet\Resources;

class MailService
{
    public function envoyerMail(string $to_email, string $to_nom, string $sujet, int $templateId, string $numero_commande)
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
                    "Variables" => [
                        "numero_commande" => $numero_commande
                        ]
                ]
            ]
        ];

        // Method pour envoyer le mail
        $mj->post(Resources::$Email, ['body' => $body]);

    }






}