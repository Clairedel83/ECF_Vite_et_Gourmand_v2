# Choix de conception ECF

## Intervention d'un employé sur une commande — 26 octobre 2026

Décision explicite de l'utilisateur : conserver les informations de l'intervention directement dans l'entité Commande. Ne pas créer d'entité InterventionCommande pour cet ECF.

Justification : garder une solution proportionnée au sujet et éviter une complexité supplémentaire. Hypothèse métier retenue par l'utilisateur : une commande est modifiable une seule fois par un employé, ou annulée. Cette hypothèse est un choix de conception à documenter, pas une exigence explicite de l'énoncé. Les changements normaux de statut restent distincts de cette intervention et conservent leur historique.

Informations envisagées : motif, moyen de contact (téléphone ou email), date et auteur de l'intervention. Nullable pour une commande sans intervention ; motif et moyen de contact obligatoires côté serveur lors d'une intervention employé ; date et auteur renseignés automatiquement.

Les noms définitifs des champs restent à choisir. Si les champs servent aussi à la modification, préférer des noms génériques d'intervention plutôt que des noms limités à l'annulation. Ne pas considérer ce point comme implémenté ou testé.

À rappeler lors de la rédaction de la copie ECF, de la documentation technique et de la préparation de l'oral : expliquer le choix des champs dans Commande, l'absence d'entité dédiée et l'hypothèse d'une seule intervention employé. La règle devra être effectivement contrôlée côté serveur si elle est retenue dans le fonctionnement final.

Ne modifier aucun code et ne lancer aucun test sans demande de l'utilisateur.
