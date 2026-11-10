# Choix de conception ECF — actualisés le 26 octobre 2026

Référence de reprise : outputs/ECF_reprise.md.

## Responsive et thèmes administration — 10 novembre 2026

Ces décisions prévalent sur l'ancien report du responsive : adaptation par page couvrant mobile, tablette et grand écran, CSS regroupé dans assets/styles/responsive/ et imports centralisés dans responsive.css. Conserver le design ordinateur et ses marges centrées ; pas de header fixe sur ordinateur, header beige clair et menu superposé sur les tailles inférieures. Compte et panier sous forme d'icônes, sans compteur ; téléphone supprimable. Décorations réajustables/masquées sur mobile. Détail des menus : plats en trois colonnes dès 768 px, une colonne en dessous. Filtres repliables sur mobile.

EasyAdmin possède ses propres imports de thème et de responsive, séparés du site public. Boutons principaux beige foncé dans les deux thèmes ; panneau latéral suit le thème, lien Retour au site lisible. Tables larges défilent à l'intérieur de leur conteneur. Les champs de modification de commande doivent recevoir le focus et être amenés dans la zone visible à leur ouverture ; bouton désactivé hors statuts autorisés 1/2.

Prochain chantier demandé : carrousel d'avis, choix de présentation du 6 novembre conservés. Non implémenté au point d'arrêt. Ne pas confondre les tests de présentation réalisés avec les tests de parcours métier restant à mener.

## MongoDB — synchronisation et tableau de bord (7 novembre 2026)

Montants stockés en BSON Decimal128 depuis les chaînes DECIMAL SQL, dates BSON UTCDateTime. Un document par commande, _id égal à l'ID SQL, updateOne avec $set et upsert. Première écriture réelle confirmée par l'utilisatrice ; commande console app:synchroniser-commandes exécutée avec bilan de 7 commandes synchronisées sans échec signalé. Raccordements création, modifications client/employé, statuts et annulations client/employé relus après corrections de placement, sans tests complets de ces mises à jour. Reprise automatique différée explicitement : ouverture à mentionner dans le dossier pour une évolution future, pas tâche requise pour finir cet ECF. La commande de synchronisation complète manuelle reste disponible ; journalisation seule ne relance pas les échecs. Prochain chantier demandé : tableau de bord José ; Chart.js proposé pour l'affichage des graphiques, choix à préciser avec l'utilisatrice.

## Affichage des avis — choix du 6 novembre 2026

Ordre confirmé : implémenter le carrousel uniquement après création de 2 à 3 comptes fictifs ayant parcouru le tunnel complet de commande et d'avis, prévue pendant les tests. Finalisation responsive différée également à cette phase. Prochain chantier : apprentissage et intégration NoSQL.

Conserver le style actuel des cartes, sans ajouter Bootstrap au site public. Carrousel en CSS avec JavaScript minimal pour les flèches si nécessaire : trois avis visibles sur ordinateur, un sur mobile. Sur mobile, navigation tactile horizontale par glissement de droite à gauche ; sur ordinateur, flèches sous les cartes. Avis validés uniquement, triés du plus récent au plus ancien, sans défilement automatique. Ne pas masquer la section en absence d'avis : l'utilisatrice prévoit un à deux comptes fictifs de test pour alimenter les avis. Implémentation à différer jusqu'après les priorités de la feuille de route du 5 novembre.

## Comptes employés — 31 octobre 2026

## Menus — décision prioritaire du 6 novembre 2026

Le retrait du catalogue est une désactivation via Menu.isActive (true à la création), réversible. Suppression définitive désactivée dans EasyAdmin ; menus actifs uniquement sur accueil/catalogue, contrôle à l'ajout/affichage panier et à la création de commande, y compris après verrouillage. Les commandes existantes conservent la relation et restent modifiables selon leurs règles de statut même si le menu est inactif. Ce choix remplace l'ancienne proposition de bloquer la suppression selon les commandes en cours. Aucune ancienne commande avec menu supprimé selon confirmation de l'utilisatrice : traitement supplémentaire non demandé.

José choisit le mot de passe à la création ; seule sa version hachée est enregistrée. Le mail annonce la création et demande de contacter José pour obtenir le mot de passe, sans le contenir. Création EasyAdmin réservée à ROLE_ADMIN, rôle ROLE_EMPLOYE imposé dans persistEntity(). Coordonnées téléphone/adresse/code postal/ville facultatives pour les employés : colonnes nullable, validation NotBlank dans le FormType d'inscription client. Migration et configuration du mail terminées selon l'utilisatrice ; seuls les tests restent pour ce chantier.

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
