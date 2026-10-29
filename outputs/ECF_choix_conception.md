# Choix de conception ECF — actualisés le 26 octobre 2026

Référence de reprise : outputs/ECF_reprise.md.

Le choix initial des champs directement dans Commande a été abandonné au profit d'une entité InterventionCommande. Elle permet de conserver une modification puis une annulation sans écraser leur trace. Le projet ne prévoit pas de journal détaillé des anciennes et nouvelles valeurs.

Décision actualisée le 28 octobre 2026 : modifications successives autorisées tant que le statut le permet. Ancienne limite d'une modification employé et blocage du client abandonnés. Les interventions conservent la trace des actions ; une annulation ultérieure reste possible. Les changements de statut sont indépendants.

Sous-total après réduction ; total = sous-total + livraison + matériel. Après erreur de modification employé : champs fermés, erreur visible, nouveau clic sur Modifier. Tests lors de séances distinctes, uniquement sur demande explicite.

Contact préalable pour les deux actions ; moyen de contact et motif à conserver obligatoirement pour l'annulation employé. Aucun motif de modification imposé par le sujet.

À rappeler lors de la documentation et de la préparation de l'oral. Ne considérer aucune fonctionnalité comme testée sans preuve. Ne modifier aucun code et ne lancer aucun test sans demande explicite.

## Menus — règle à implémenter ultérieurement

## Emails — argumentaire dossier et oral (29 octobre 2026)

Choix envisagé : Symfony Mailer associé à Mailjet pour les emails automatiques. L'éditeur visuel et les newsletters constituent une ouverture possible pour l'autonomie de Julie, pas une fonctionnalité à ajouter à l'ECF. Ne pas présenter cette évolution comme réalisée. L'autonomie sur les modèles transactionnels dépendra de leur hébergement dans Mailjet plutôt que dans Twig.

Terme demandé par l'utilisatrice, cité par son professeur : « logique microservices ». Le lui rappeler lors du dossier de projet et de l'oral sur ce choix technique. Préciser que déléguer l'envoi à Mailjet est une externalisation via un service spécialisé ; cela ne suffit pas à qualifier l'application Symfony d'architecture microservices. Formulation possible : séparation des responsabilités et recours à un service externe spécialisé dans l'envoi d'emails.

## Menus — règle à implémenter ultérieurement (suite)

Décision du 27 octobre 2026 : interdire la suppression d'un menu associé à une commande en cours. Rendre le menu indisponible pour les nouvelles commandes tout en conservant sa fiche pour honorer les commandes existantes. Cette règle reste à implémenter ; préciser alors les statuts considérés comme « en cours ».

La relation actuelle autorise un menu null après suppression. Conserver une protection pour les anciennes commandes sans menu : leur détail et leur historique doivent rester consultables, mais leur modification nécessitant les données du menu doit être refusée proprement. Dans CommandeCrudController, adapter aussi la construction du formulaire de modification sur les vues de détail et les réaffichages d'erreur, qui appellent actuellement getMenu()->getNbreMin() sans vérifier le menu. Aucun changement du code applicatif effectué pour cette décision.
