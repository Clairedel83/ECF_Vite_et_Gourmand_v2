# Reprise ECF — actualisation prioritaire du 28 octobre 2026

Cette actualisation remplace les anciens points ci-dessous, conservés comme archives. Le FormType d'annulation, la modale et son JS sont réalisés : ne pas demander de les recréer.

## Progression actuelle

Modification et annulation des commandes implémentées côté client et employé et relues ; l'utilisatrice indique que l'affichage dynamique fonctionne. Transactions et verrou pessimiste avant vérifications ; annulation partagée dans GestionCommandeService ; interventions employé et informations d'annulation dans historique admin. Bouton Réessayer client réutilise le formulaire d'adresse ouvert. Dernier calculSousTotal JS client fourni avec arrondis intermédiaires : dernière copie à confirmer lors des tests.

Ne lancer aucun test : séances de tests distinctes demandées. Ne pas confondre implémenté et validé par tests. Ne pas modifier le code applicatif.

Choix : modifications successives autorisées selon statut, ancienne règle de modification unique abandonnée. Sous-total après réduction. Après erreur employé : erreur visible et champs fermés, nouveau clic sur Modifier. InterventionCommande conservée ; contact préalable obligatoire ; contact et motif enregistrés pour annulation. Client statut 1 ; modification employé 1/2, annulation 1/2/3 (choix du projet).

## Avancement indicatif

Estimation 28 octobre : environ 65 % du projet ECF global, environ 75 % de l'application hors livraison/documentation. Estimation indicative, pas mesure exacte : fonctionnalités obligatoires, tests, déploiement et livrables comptent dans le global. Les documents externes et le déploiement ne sont pas inspectés ; confirmer leur état avant de les déclarer absents. Énoncé local relu intégralement (12 pages).

## Tâches ECF restantes

- Suivi des statuts et restitution à raccorder : details() actuel GET prépare StatutType sans handleRequest ; updateEntity() ne traite pas automatiquement ce formulaire personnalisé. Traitement POST, historique et règles de clôture à terminer/vérifier.
- Filtres commandes par statut/client : configureFilters absent du CRUD actuel.
- Mails : bienvenue, confirmation commande, commande terminée invitant à donner avis, attente matériel (10 jours ouvrés, 600 euros), compte employé sans mot de passe dans mail. Pas de traitement Mailer trouvé dans src.
- Mot de passe oublié avec lien de réinitialisation.
- Avis depuis commande terminée, note 1 à 5/commentaire, validation/refus employé, accueil affichant les vrais avis validés. Entité et CRUD existent mais parcours restant ; harmoniser mapping Avis OneToOne / collection Commande.
- Gestion employés par José : création email/mot de passe hashé, notification, désactivation empêchant connexion, compte admin créé hors application, protection serveur des actions réservées admin.
- Statistiques José : graphique comparatif commandes/menu depuis NoSQL, chiffre d'affaires/menu avec filtres menu/période. Aucun raccordement NoSQL/statistiques trouvé.
- Contact : formulaire titre/description/email et envoi entreprise ; contrôleur actuel affiche seulement vue.
- Horaires : terminer CRUD et alimenter pied de page depuis BDD (actuellement statique).
- Vérification finale exigences menus, galerie, conditions/allergènes et filtres dynamiques, droits des CRUD ; base présente, pas à refaire.
- Sessions dédiées de tests parcours, droits/CSRF, calculs, erreurs, concurrence ; responsive/accessibilité RGAA.
- Déploiement et contrôle en ligne ; état externe à confirmer.
- Livrables à compléter ou confirmer hors dépôt : copie à rendre, GitHub public et liens application/gestion de projet, bonnes pratiques branches ; README installation (actuel très bref) ; SQL création et données en plus des migrations ; manuel PDF avec comptes ; charte PDF, 3 wireframes/mockups desktop et 3 mobiles ; documentation projet et technique (choix, environnement, sécurité, MCD/classes, cas d'utilisation, séquence, déploiement).

## Annexes

- Règle menus différée enregistrée : interdire suppression si commande en cours, indisponibilité pour nouvelles commandes et consultation des anciennes commandes sans menu.
- Tableau de bord employé optionnel ; statistiques José obligatoires.
- Raffinements graphiques et refactorisation supplémentaire : facultatifs.

---

# Archives — point d'arrêt du 26 octobre 2026 (dépassé par l'actualisation ci-dessus)

À la prochaine reprise, commencer par rappeler ce contexte, sans entreprendre d'action non demandée.

## Travail en cours
Annulation des commandes par un employé depuis la page détail EasyAdmin. Une maquette de modale a été créée ; aucune implémentation complète ni validation fonctionnelle n'est établie. L'entité src/Entity/InterventionCommande.php existe (son contenu et sa migration restent à vérifier sur demande).

Le service GestionCommandeService centralise l'annulation : statut annulé, restitution du stock et historique. Le contrôleur client conserve ses permissions (propriétaire et statut 1). Le parcours employé sera dans CommandeCrudController, sous /admin protégé par ROLE_EMPLOYE. Ne pas dupliquer inutilement ce contrôle de rôle.

## Décisions actuelles
- Utiliser InterventionCommande liée à Commande (ManyToOne obligatoire) et User pour auteur (ManyToOne obligatoire).
- Champs prévus : type, dateCreation (datetime_immutable), modeContact et motif. Les deux derniers sont nullable en base, obligatoires côté serveur pour l'annulation employé.
- Contact préalable exigé pour modification et annulation ; l'énoncé exige explicitement l'enregistrement du moyen de contact et du motif pour l'annulation, pas du motif pour la modification.
- Une seule modification employé est permise. Ensuite, client et employé ne peuvent plus modifier, mais une annulation ultérieure reste possible. Les changements de statut restent indépendants.
- Le service actuel limite l'annulation aux statuts 1 à 3 : choix métier du projet, pas une limite explicite du sujet.
- Les anciens choix consistant à stocker l'intervention dans Commande et à interdire l'annulation après modification sont abandonnés.

## Rappels demandés pour la reprise
1. Créer un FormType dédié à l'annulation employé : moyen de contact, motif et confirmation du contact préalable. Auteur, date, commande et type renseignés automatiquement côté serveur.
2. Construire la modale dans le Twig de détail, hors du formulaire de statut.
3. Revenir sur le fichier JavaScript d'ouverture/fermeture demandé par l'utilisateur : Bootstrap embarqué par EasyAdmin (5.3.8 constaté) gère déjà ces actions via data-bs-toggle, data-bs-target et data-bs-dismiss. Un JS spécifique est utile seulement pour un comportement supplémentaire, notamment rouvrir la modale après une erreur serveur. Expliquer cette distinction avant d'ajouter du code inutile.
4. Relier le traitement POST et CSRF à l'action d'annulation EasyAdmin, puis au service. Enregistrer ensemble intervention, commande, stock et historique.
5. Afficher les erreurs et conserver la saisie ; warning en cas de refus et confirmation en cas de succès.

Aucun test sans demande explicite. Ne jamais modifier le code de l'utilisateur ; expliquer ici les changements pour qu'il les applique. Les seuls fichiers modifiés pour ce point d'arrêt sont les notes et consignes de suivi.

## À prévoir — affichage de l'annulation dans l'historique (27 octobre 2026)

Demande explicite de l'utilisateur : dans l'historique des statuts, lorsque le statut de la ligne vaut 8 (commande annulée), afficher un paragraphe <p> sous le libellé du statut avec les informations sur l'annulation. Pour une annulation employé, utiliser les informations disponibles dans InterventionCommande (motif, moyen de contact, auteur et date selon la présentation à définir). Gérer aussi le cas d'une annulation client, qui ne crée pas d'intervention employé : ne pas supposer qu'une intervention existe. Le contenu exact et les vues concernées restent à préciser lors de l'implémentation. Fonctionnalité à faire, pas encore réalisée. Ne modifier aucun code sans demande.
