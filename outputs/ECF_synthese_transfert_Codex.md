# Contexte ECF et apprentissage du code — transmission à Codex

Synthèse préparée le 2 novembre 2026 à la demande de l’utilisatrice.

## 1. Objet et portée de ce fichier

Ce document permet à un autre chat Codex de reprendre l’accompagnement de l’ensemble de l’ECF « Vite & Gourmand », depuis le front démarré en janvier 2026 (date rappelée par l’utilisatrice), en respectant le travail et les décisions déjà prises. Il couvre la conception visuelle, l’intégration HTML/CSS, le responsive, les interactions JavaScript, les données, le back, les tests et les livrables. Il rassemble aussi le parcours d’apprentissage et la manière de collaborer.

Sources : consignes AGENTS.md fournies dans la conversation ; notes `outputs/ECF_reprise.md` et `outputs/ECF_choix_conception.md` lues intégralement ; échanges récents accessibles des chats « ECF » dans Codex et ChatGPT ; un échange de révision des bases du développement. Il ne s’agit pas d’un export exhaustif de toutes les conversations. Les anciennes captures et l’intégralité de l’historique n’ont pas été examinées. Aucun audit du code ni test n’a été réalisé pour produire cette synthèse.

Complément après précision de l’utilisatrice : recherche dans l’historique ancien et lecture d’échanges sur le responsive de janvier, l’apprentissage HTML/CSS/JS et la stratégie ECF de mars, l’organisation du CSS, les cartes et animations ECF d’avril, puis l’affichage interactif des menus en juin. Les exemples d’apprentissage « Le Quai Antique » et les idées de projets portfolio ne doivent pas être confondus avec des réalisations de « Vite & Gourmand ». Les anciennes réponses de l’assistant ne constituent pas des preuves de réalisation, des conseils techniques à reprendre sans examen ou des exigences du diplôme.

Les informations de progression fonctionnelle correspondent principalement aux mises à jour des 28–31 octobre 2026. Vérifier les changements ultérieurs avec l’utilisatrice avant de les considérer comme actuels. En cas de contradiction, privilégier sa décision explicite la plus récente, puis les actualisations des notes, plutôt que les archives.

## 1 bis. Parcours depuis le front : chronologie et périmètre global

### Janvier 2026 — intégration et responsive

L’utilisatrice situe le démarrage du front en janvier. Les échanges de cette période montrent un travail sur la structure HTML, les images, les dimensions des éléments, CSS Grid et l’adaptation des pages aux différentes largeurs d’écran. Fin janvier, elle explique que son CSS de base est conçu pour desktop et souhaite des versions mobile, tablette, desktop et grand écran. Elle cherche à comprendre où placer les media queries et comment adapter une mise en page déjà commencée.

Ces échanges font notamment référence à « Le Quai Antique ». Ils documentent son apprentissage et ses acquis mobilisés ensuite ; ils ne prouvent pas que ces fichiers ou ces choix sont ceux du dépôt ECF actuel. Ne pas imposer rétroactivement les anciens points de rupture proposés par l’assistant ni une réécriture mobile-first.

### Mars 2026 — JavaScript et articulation avec l’ECF complet

Dans « ECF Développeuse Web Aide », elle explique suivre une formation de développeuse web chez Studi : elle a alors travaillé HTML/CSS depuis septembre, apprend JavaScript et n’a pas encore abordé le back. Ce repère concerne le début de sa formation ; janvier concerne le démarrage du front rappelé pour ce projet.

Elle envisage un apprentissage ciblé du JavaScript utile à l’ECF, puis un approfondissement jusqu’à l’examen. Son temps disponible déclaré à cette période est le vendredi et un peu en semaine, environ une à deux heures supplémentaires. Ces données sont historiques, pas un planning actuel à présumer.

Elle découvre l’étendue du projet : front, données SQL/NoSQL, métier serveur, comptes, droits, documentation et déploiement. Les premières pistes PHP/PDO précèdent l’implémentation Symfony actuelle. Les anciennes dates de rendu évoquées en mars ne sont plus une échéance fiable : une conversation ultérieure concerne un examen en mars/avril 2027 ; la date officielle actuelle reste à confirmer.

Un autre échange porte sur un projet portfolio pour pratiquer le DOM : bulles à cliquer avec compteur, quiz interactif et liste de tâches. Il illustre sa démarche d’apprentissage, sans faire partie du périmètre ECF.

### Avril 2026 — interface « Vite & Gourmand », catalogue et interactions prévues

Des extraits fournis par l’utilisatrice montrent une intégration HTML/CSS « VITE & Gourmand - Traiteur » : navigation Accueil / À propos / Nos menus / Contact, lien Connexion, logo, image de fond de hero, overlay, titre et boutons d’action.

Elle travaille notamment sur :

- La différence entre un hero d’accueil occupant toute la hauteur et des en-têtes intérieurs moins hauts, laissant apparaître la suite de la page.
- La page catalogue : zone de filtres et grille de cartes menus.
- La composition des cartes : image, thème, titre, description, prix, nombre minimum de personnes et bouton « Voir le menu ».
- CSS Grid, la signification de `1fr`, les largeurs et les sélecteurs de classes imbriquées.
- L’organisation et la réutilisation des styles entre les pages.
- Le contenu des menus et l’affichage des allergènes, avec le souci de varier les propositions pour ne pas bloquer tous les choix d’un utilisateur concerné par un allergène.

Le 17 avril environ, elle retient explicitement ces idées d’interaction : carrousel d’avis avec swipe manuel ; compteur animé dans À propos ; léger agrandissement des boutons au survol ; soulignement animé des liens ; filtres dynamiques. Elle avait aussi envisagé une apparition de titres du bas vers le haut. Ce sont des choix ou pistes de conception : leur implémentation actuelle n’a pas été vérifiée pour cette synthèse. Certaines interactions peuvent relever du CSS ; ne pas les attribuer toutes automatiquement au JavaScript.

### Juin 2026 — passage de l’affichage statique aux données

Dans « Affichage menu interactif », elle décrit une interaction souhaitée : au clic sur « Voir le menu », la carte sélectionnée passerait en première position dans la grille et s’ouvrirait en grand. Elle demande d’abord des explications sans code et réfléchit au conteneur et à l’éventuel contenu masqué.

Elle apprend alors le rôle des bases de données et souhaite alimenter les détails des menus depuis celles-ci, plutôt que produire un travail statique à remplacer ensuite. Son interrogation porte autant sur la qualité de conception et le travail évité que sur l’animation. Cette intention historique ne permet pas d’affirmer que l’interaction finale actuelle reproduit exactement ce comportement.

### Ensuite — intégration dans Symfony et développement de l’application complète

Les échanges suivants portent sur le lien entre PHP, BDD et front, puis Symfony, Twig, les données dynamiques, les comptes, les commandes, EasyAdmin et la sécurité. Les décisions et réalisations récentes sont détaillées dans les sections suivantes. Le back prolonge un travail front déjà engagé : il ne représente pas à lui seul le projet ni l’apprentissage.

### Comment reprendre le front aujourd’hui

Considérer comme objets de suivi à part entière : identité visuelle et maquettes ; structure et navigation ; accueil et À propos ; catalogue et détails des menus ; images et galerie ; filtres et interactions ; formulaires et messages d’erreur ; espace client ; interface employé/admin ; responsive ; accessibilité ; cohérence entre données et affichage.

Les anciens échanges attestent des travaux et intentions décrits, sans constituer un inventaire vérifié de chaque écran actuel. Si la demande exige un bilan précis, lire les templates, styles et scripts actuels ou examiner les maquettes fournies, dans la limite de l’autorisation donnée. Ne pas classer l’ensemble du front « à faire » faute de notes récentes, ni « terminé et testé » parce qu’une maquette ou un extrait existe.

## 2. Consignes impératives de collaboration

- Ne pas modifier le code applicatif : l’utilisatrice applique elle-même les corrections expliquées dans le chat.
- Ne lancer aucun test sans demande explicite. Les séances de tests sont organisées séparément.
- Ne pas entreprendre d’action supplémentaire sans accord. Une demande d’explication ou de relecture ne vaut pas autorisation de modifier, refactoriser, déployer ou ajouter des fonctionnalités.
- Lire le code pertinent lorsqu’il est accessible avant de proposer une correction ; ne pas inventer les noms de méthodes, propriétés ou fichiers.
- Distinguer systématiquement : implémenté, relu, fonctionnement rapporté par l’utilisatrice et validé par des tests identifiés.
- Conserver les décisions déjà prises et expliquer les éventuels problèmes concrets avant de proposer de les changer.

À la reprise du projet dans le dépôt, lire les deux notes de suivi. Attention : la partie « Archives » de la note de reprise contient des décisions dépassées. Le FormType d’annulation, la modale et son JS sont réalisés ; ne pas demander de les recréer. Bootstrap peut gérer l’ouverture/fermeture standard d’une modale ; un JS spécifique répond à des besoins supplémentaires, par exemple une réouverture après erreur serveur. Cette distinction a déjà fait partie de l’accompagnement.

## 3. Apprentissage et manière d’accompagner

L’utilisatrice apprend le développement en construisant son ECF. Son objectif est de comprendre et d’appliquer elle-même les changements, et de pouvoir expliquer ses choix dans le dossier et à l’oral.

Exemples observés dans les échanges : elle demande de l’aide pour terminer un contrôleur, demande une relecture après avoir appliqué les modifications, ou demande seulement le début d’une fonction qu’elle souhaite compléter. Elle rapproche aussi des mécanismes connus en HTML/CSS de ceux générés par EasyAdmin : par exemple, comment placer des liens en bas d’une barre latérale sans créer directement une div.

Approche conseillée pour respecter cette autonomie :

1. Répondre précisément à la question posée, en français clair.
2. Expliquer le mécanisme et son rôle dans son propre code.
3. Indiquer le fichier, la méthode, les imports et l’emplacement des changements lorsque nécessaire.
4. Fournir des extraits adaptés au besoin : un début de fonction si elle veut continuer seule, une correction complète si elle la demande.
5. Expliquer les différences qui peuvent prêter à confusion : structure BDD et validation de formulaire ; saisie et hachage ; entité et repository ; contrôle côté navigateur et contrôle côté serveur.
6. Après ses modifications, relire sur demande sans annoncer de validation fonctionnelle en l’absence de tests.

Les échanges consultés abordent notamment HTML sémantique, classes et identifiants, modèle de boîte, dimensions et images, Grid/Flexbox, responsive et media queries, effets visuels, DOM et interactions, puis PHP/Symfony, Doctrine et les relations d’entités, Twig et les boucles, EasyAdmin, formulaires, rôles et hachage. Un échange de révision porte aussi sur HTTP GET/POST, les index et SQL/NoSQL. Elle cherche à comprendre avant de recevoir du code et à éviter de refaire inutilement un travail provisoire. Ces sujets ne constituent pas une évaluation précise de sa maîtrise actuelle.

## 4. Projet et environnement

- Projet ECF : application de traiteur « Vite & Gourmand ».
- Dépôt local : `C:\wamp64\www\ECF_vite_et_gourmand_v2` ; environnement Windows/PowerShell et Wamp.
- Application Symfony/PHP, Doctrine pour les données relationnelles, Twig, JavaScript et CSS ; EasyAdmin pour la gestion.
- Profils métier : client, employé et administrateur José. Julie est également évoquée dans le contexte métier et dans une éventuelle autonomie future sur les emails.
- Envoi de mails déjà implémenté avec le SDK Mailjet. Ne pas confondre ce constat avec l’ancienne piste « Symfony Mailer associé à Mailjet ».
- Le besoin NoSQL pour les statistiques est encore à réaliser selon les notes.

Les versions exactes des composants et la base relationnelle réellement configurée doivent être vérifiées dans le dépôt si le travail en dépend. Le cahier des charges reste la référence pour les exigences : cette synthèse ne remplace pas sa lecture.

## 5. Décisions métier à préserver

### Commandes et interventions

L’entité `InterventionCommande` est retenue. L’ancien choix de placer les informations d’intervention directement dans `Commande` est abandonné. Les interventions permettent de conserver les traces de plusieurs actions sans les écraser ; aucun journal détaillé des anciennes/nouvelles valeurs n’est prévu.

Décision du 28 octobre : les modifications successives sont autorisées tant que le statut le permet. L’ancienne limite d’une seule modification employé et le blocage ultérieur du client sont abandonnés. Une intervention employé n’empêche donc pas, à elle seule, une nouvelle modification autorisée par le statut. Les changements de statut sont indépendants des interventions.

- Client : modification/annulation au statut 1, avec contrôle du propriétaire.
- Employé : modification aux statuts 1 et 2 ; annulation aux statuts 1, 2 et 3. Cette limite d’annulation est un choix du projet, pas une limite explicitement imposée par le sujet.
- Contact préalable exigé pour modification et annulation employé.
- Moyen de contact et motif obligatoires pour l’annulation employé. Le sujet n’impose pas de motif de modification.
- Une annulation client ne crée pas nécessairement d’intervention employé : l’affichage doit gérer cette absence.
- Annulation centralisée dans `GestionCommandeService`, avec gestion du statut, du stock et de l’historique.
- Transactions et verrou pessimiste avant les vérifications, selon les relectures enregistrées.
- Sous-total après réduction ; total = sous-total + livraison + matériel.
- Après erreur de modification employé : erreur visible, champs fermés ; nouveau clic sur « Modifier ».

### Statuts et matériel

Suivi via `modifierStatut()`, route POST dédiée et historique daté. L’ancienne surcharge `updateEntity` a été remplacée. Le statut 8 correspond à l’annulation et ne doit plus être proposé dans `StatutType` : l’annulation possède son parcours dédié.

Le passage au statut 7 avec prêt de matériel confirme sa restitution (`restitutionMateriel = true`) ; aucun formulaire supplémentaire n’est demandé. Le mail du statut 6 doit informer du délai de dix jours ouvrés et des 600 € prévus dans le scénario. Le mail de commande terminée invite à laisser un avis.

### Comptes employés

José choisit le mot de passe à la création. Il est saisi en clair mais haché avant l’enregistrement ; ne jamais conserver le mot de passe en clair en BDD. Le mail annonce la création et demande de contacter José pour obtenir ce mot de passe, sans le contenir.

Création réservée à `ROLE_ADMIN`, rôle `ROLE_EMPLOYE` imposé côté serveur dans `persistEntity()`. Coordonnées téléphone/adresse/code postal/ville facultatives en BDD et setters acceptant `?string` ; contraintes `NotBlank` conservées dans le formulaire d’inscription client.

### Autres choix

- Paiement à la livraison par carte bancaire ou virement, sans acompte ni paiement intégré au site : choix du scénario, pas exigence de développement supplémentaire.
- Menus : règle différée visant à interdire la suppression d’un menu lié à une commande en cours, et à le rendre indisponible pour les nouvelles commandes. Les statuts « en cours » restent à préciser.
- Les anciennes commandes dont le menu est absent doivent rester consultables ; leur modification nécessitant ce menu doit être refusée proprement. Prévoir aussi les accès à `getMenu()->getNbreMin()` dans la construction et les réaffichages du formulaire.
- Tableau de bord employé optionnel ; statistiques José obligatoires.

## 6. État connu des réalisations

| Domaine | État enregistré dans les notes |
|---|---|
| Modification et annulation client/employé | Implémentées et relues ; affichage dynamique fonctionnel selon l’utilisatrice ; tests dédiés à faire |
| Historique et interventions | Historique daté, suivi des statuts, informations d’annulation et interventions dans l’historique admin implémentés selon les notes |
| Calculs de modification | Arrondis JS et transaction/finally relus ; dernière copie du calcul client à confirmer lors des tests |
| Filtres commandes par statut/client | Réalisés selon confirmation de l’utilisatrice ; dernière version non relue/testée dans la séance correspondante |
| Horaires du pied de page | Reliés aux données selon l’utilisatrice ; lundi = 1, créneaux absents masqués, quatre heures nulles = fermé |
| Mails bienvenue et confirmation commande | Implémentés ; réception confirmée par l’utilisatrice |
| Réinitialisation du mot de passe | Implémentée et relue ; validations fonctionnelles et renforcements à prévoir |
| Création employé | Développement terminé selon l’utilisatrice, migration et configuration mail comprises ; seuls les tests de ce chantier restent |
| Désactivation employé | `isActive`, `UserChecker`, firewall et protection admin présents ; empêche une nouvelle authentification, sans révocation garantie des sessions déjà ouvertes |
| Pages légales | Trois templates relus comme base cohérente ; compléments à fournir, sans réécriture intégrale |

Estimation du 28 octobre : environ 80 % du développement fonctionnel de l’application, hors livrables. C’est une estimation indicative, ni un taux de réussite des tests ni une mesure du temps restant. Ne pas la présenter comme un audit actualisé au 2 novembre.

## 7. Travail restant selon les notes

### Fonctionnalités

- Avis après commande terminée : note 1 à 5, commentaire, validation/refus employé, affichage des vrais avis validés à l’accueil. Entité et CRUD existent ; parcours à terminer et mapping `Avis` OneToOne / collection `Commande` à harmoniser.
- Mails de commande terminée/invitation à donner un avis et d’attente de restitution du matériel.
- Statistiques José : NoSQL, graphique comparatif des commandes par menu, chiffre d’affaires avec filtres menu/période.
- Contact : formulaire titre/description/email et envoi à l’entreprise ; le contrôleur affiche actuellement seulement la vue selon les notes.
- Règle de protection/indisponibilité des menus décrite plus haut.
- Vérification finale des exigences menus, galerie, conditions, allergènes, filtres dynamiques et droits des CRUD ; base existante, à ne pas refaire sans raison.

### Sécurité et fiabilité

- Sessions déjà ouvertes d’un compte désactivé : révocation à la prochaine requête différée à la demande de l’utilisatrice ; ne pas l’implémenter spontanément ni la déclarer acquise.
- Réinitialisation : limiter les demandes répétées en conservant une réponse générique ; envisager le stockage d’un condensat du token ; gérer les erreurs/refus Mailjet sans révéler l’existence du compte.
- Parcours déjà relu : token aléatoire, validité dix minutes, URL absolue, validation du formulaire, hachage et effacement token/date après succès. Ajustement conseillé : vérifier une date nulle avant comparaison et considérer l’expiration avec `>=`. Nettoyage facultatif d’une option `passwordHasher` inutilisée dans `ResetPassType`.
- Service de mail partagé : traiter la réponse API et les erreurs ; un appel au service ne garantit pas l’acceptation du mail.

### Pages légales

Uniformiser l’email de pied de page avec `vite.gourmand2027@gmail.com`. Compléter les identités et responsabilités réelles, l’URL finale et l’hébergeur, les durées de conservation réellement appliquées, les prestataires/transferts éventuels et les cookies de l’environnement déployé. Adapter les bases légales aux traitements réels de la démonstration. Ne pas inventer de coordonnées ni d’identifiants légaux. Pour une exploitation commerciale réelle, identifier le médiateur effectivement désigné. Actualiser la politique avec les nouveaux traitements.

### Tests, livraison et documents

Séances distinctes sur demande : parcours, droits/CSRF, calculs, erreurs, concurrence, responsive et accessibilité RGAA. Pour les employés : création, connexion, réception du mail, désactivation, droits et scénario des sessions déjà ouvertes. Vérifier la préparation du compte José hors application.

Déploiement et contrôle en ligne à prévoir ou confirmer. État des documents externes et de l’hébergement non établi.

Livrables à compléter ou confirmer : copie à rendre ; dépôt GitHub public ; liens application et gestion de projet ; bonnes pratiques de branches ; README d’installation ; SQL de création et données en complément des migrations ; manuel PDF avec comptes de démonstration ; charte graphique ; trois wireframes/mockups desktop et trois mobiles ; documentation projet et technique, environnement, sécurité, MCD/classes, cas d’utilisation, séquence et déploiement.

## 8. Dossier, oral et gestion de projet

Conserver la justification des décisions, notamment `InterventionCommande`, les règles de modification et la séparation des responsabilités pour les emails.

L’utilisatrice souhaite se souvenir de l’expression « logique microservices », citée par son professeur. Expliquer avec précision que déléguer l’envoi des emails à Mailjet est le recours à un service externe spécialisé ; cela ne suffit pas à qualifier l’application Symfony d’architecture microservices. L’éditeur visuel et les newsletters pour Julie sont une ouverture possible, pas une fonctionnalité ECF réalisée ni à ajouter automatiquement.

Si cette synthèse sert à construire un outil de gestion de projet : couvrir l’ensemble du parcours depuis janvier, avec conception/maquettes, intégration front, responsive/accessibilité, JavaScript, données, back, tests, livrables et déploiement. Reprendre les états ci-dessus sans transformer les fonctionnalités implémentées en tâches à développer. Séparer les intentions historiques des réalisations confirmées et les développements des validations. Ne pas inventer de dates limites, de durées ou de validations. L’accès à ce fichier ne constitue pas une autorisation de publier des données ou d’envoyer des messages.

## 9. Consigne de reprise prête à utiliser

« Utilise cette synthèse comme contexte pour ma demande. Je développe moi-même mon ECF et j’apprends en le faisant : explique les corrections dans le chat, sans modifier mon code. Ne lance aucun test sans ma demande explicite. Respecte les décisions récentes, distingue le développement des tests et lis les fichiers utiles si tu y as accès. Adapte ton aide à ce que je demande : explication, début de fonction, correction ou relecture. »
