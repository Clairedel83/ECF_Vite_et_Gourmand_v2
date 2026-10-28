# Choix de conception ECF — actualisés le 26 octobre 2026

Référence de reprise : outputs/ECF_reprise.md.

Le choix initial des champs directement dans Commande a été abandonné au profit d'une entité InterventionCommande. Elle permet de conserver une modification puis une annulation sans écraser leur trace. Le projet ne prévoit pas de journal détaillé des anciennes et nouvelles valeurs.

Une seule modification employé est autorisée, puis les modifications client et employé sont bloquées. Une annulation ultérieure reste possible ; les changements de statut restent indépendants. Cette limite est un choix de conception, pas une exigence explicite de l'énoncé.

Contact préalable pour les deux actions ; moyen de contact et motif à conserver obligatoirement pour l'annulation employé. Aucun motif de modification imposé par le sujet.

À rappeler lors de la documentation et de la préparation de l'oral. Ne considérer aucune fonctionnalité comme testée sans preuve. Ne modifier aucun code et ne lancer aucun test sans demande explicite.

## Menus — règle à implémenter ultérieurement

Décision du 27 octobre 2026 : interdire la suppression d'un menu associé à une commande en cours. Rendre le menu indisponible pour les nouvelles commandes tout en conservant sa fiche pour honorer les commandes existantes. Cette règle reste à implémenter ; préciser alors les statuts considérés comme « en cours ».

La relation actuelle autorise un menu null après suppression. Conserver une protection pour les anciennes commandes sans menu : leur détail et leur historique doivent rester consultables, mais leur modification nécessitant les données du menu doit être refusée proprement. Dans CommandeCrudController, adapter aussi la construction du formulaire de modification sur les vues de détail et les réaffichages d'erreur, qui appellent actuellement getMenu()->getNbreMin() sans vérifier le menu. Aucun changement du code applicatif effectué pour cette décision.
