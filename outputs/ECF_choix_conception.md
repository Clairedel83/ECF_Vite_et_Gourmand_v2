# Choix de conception ECF — actualisés le 26 octobre 2026

Référence de reprise : outputs/ECF_reprise.md.

Le choix initial des champs directement dans Commande a été abandonné au profit d'une entité InterventionCommande. Elle permet de conserver une modification puis une annulation sans écraser leur trace. Le projet ne prévoit pas de journal détaillé des anciennes et nouvelles valeurs.

Une seule modification employé est autorisée, puis les modifications client et employé sont bloquées. Une annulation ultérieure reste possible ; les changements de statut restent indépendants. Cette limite est un choix de conception, pas une exigence explicite de l'énoncé.

Contact préalable pour les deux actions ; moyen de contact et motif à conserver obligatoirement pour l'annulation employé. Aucun motif de modification imposé par le sujet.

À rappeler lors de la documentation et de la préparation de l'oral. Ne considérer aucune fonctionnalité comme testée sans preuve. Ne modifier aucun code et ne lancer aucun test sans demande explicite.
