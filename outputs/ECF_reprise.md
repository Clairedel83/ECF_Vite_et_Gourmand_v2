# Reprise ECF — actualisation prioritaire du 28 octobre 2026

Cette actualisation remplace les anciens points ci-dessous, conservés comme archives. Le FormType d'annulation, la modale et son JS sont réalisés : ne pas demander de les recréer.

## Priorités pour le 6 novembre 2026 — préparation demandée le 5 novembre

Cette mise à jour prévaut sur les anciennes tâches ci-dessous. Aucun code applicatif modifié ni test exécuté pendant la revue du 5 novembre. Priorités demandées : points 1 à 3 puis 6 de la liste donnée dans le chat. Responsive (point 5) à finaliser également. NoSQL était le point 4 : apprentissage nécessaire, rechercher ultérieurement des supports surtout vidéos avec l'utilisatrice ; aucune recherche effectuée pour cette préparation.

### 1. Avis clients

- Déjà présents : Avis (commande OneToOne, note, contenu, dateCreation, statut int par défaut 1), AvisCommandeController, AvisClientType, template commande/avisCommande.html.twig vide, AvisCrudController encore généré sans modération. Migration Version20261105101113 du statut entier présente, application non vérifiée.
- Corriger en premier Commande.avis : attribut OneToOne, mais propriété encore Collection, initialisation ArrayCollection, getter Collection et addAvi/removeAvi hérités de l'ancien OneToMany. Représenter un Avis unique nullable et harmoniser les deux côtés ; vérifier ensuite la migration nécessaire.
- FormType : remplacer TextareaField EasyAdmin par TextareaType Symfony, conserver note ChoiceType 1 à 5 et contenu obligatoire, prévoir limites et bouton d'envoi. Aucun prénom saisi : récupérer celui du client de la commande.
- Contrôleur : identifier la commande dans le lien, chercher une commande appartenant au client connecté, refuser absence/statut différent de 7/avis déjà existant. Ne pas confondre objet champ et valeur : get('note') est actuellement passé directement à setNote(int). Formulaire lié à Avis mappe déjà note/contenu.
- Enregistrer commande, date et statut initial en attente côté serveur ; persist/flush, confirmation et redirection après succès. Conserver saisie et erreurs sur formulaire invalide. Passer la vue du formulaire au Twig et construire la page.
- CRUD : consultation et choix statut 1 attente / 2 validé / 3 refusé ; auteur/commande/note/contenu non modifiables, pas de création d'avis par l'employé.
- Accueil : récupérer avis statut 2 et remplacer les témoignages Bruno/Johanna/Laurie écrits en dur. Prévoir cas sans avis. Tests différés.

### 2. Mails des statuts de commande

- modifierStatut() gère transaction et historique, mais aucun envoi MailService aux statuts 6/7 lors de la revue.
- Après enregistrement et commit : statut 7 invitation avis, modèle déjà créé selon l'utilisatrice ; obtenir son ID et confirmer les noms de variables (prénom et lien absolu vers formulaire lié à la commande). Le mail invite au formulaire du site : note et avis sont saisis ensuite.
- Statut 6 avec prêt matériel : mail de restitution dix jours ouvrés, information 600 euros, numéro de commande et informations utiles selon modèle. Ne pas ajouter d'automatisation de pénalité non demandée.
- Éviter un envoi sur simple affichage ou statut identique ; garder le changement de statut enregistré si le mail échoue. Définir si un retour ultérieur au statut doit renvoyer l'invitation avant d'ajouter des champs anti-doublon.

### 3. Gestion des erreurs de MailService

- Service : signature array variables, retour void, success() contrôlé, RuntimeException en cas de refus. Contact : try/catch, formulaire conservé, flash et redirection, contraintes NotBlank/Email/Length correctes au 5 novembre ; désormais implémenté, à tester.
- Ajouter les blocs try/catch proposés autour du seul envoi dans inscription, confirmation commande, création employé et demande de réinitialisation (non appliqués à la revue).
- Compte client/employé : création conservée, avertissement mail distinct du succès de création, redirection normale.
- Commande : envoi après commit, échec mail n'empêche ni vidage panier ni confirmation ; ne pas réexécuter création.
- Réinitialisation : réponse générique identique que le compte existe ou non ; pas d'avertissement conditionnel révélant son existence. Journalisation sans secret/token à prévoir dans sécurité, ne pas présenter catch silencieux comme suivi complet.
- Vérifier correspondance de chaque clé avec Mailjet ; ne pas envoyer de mails ni tester sans demande. Aucun bouton renvoi supplémentaire demandé.

### 6. Corrections de cohérence

- CompteController.details : findOneBy(id,user) existe ; traiter commande absente par 404 avant Twig.
- InformationsUserType : depuis coordonnées nullable pour employés, ajouter NotBlank aux coordonnées du parcours client pour empêcher leur effacement lors d'une modification ; conserver champs admin employés facultatifs. Ce formulaire est aussi accessible via le compte : adapter au public réellement autorisé si nécessaire.
- Footer : remplacer contact@vite&gourmand.fr par vite.gourmand2027@gmail.com et lien mailto correspondant.
- Pages légales : identité/responsables, URL/hébergeur après déploiement, durées conservation et cookies, prestataires/transferts à compléter ; tenir compte avis/contact/réinitialisation. Ne pas inventer informations ni refaire toutes les pages.
- Menus : règle différée à concrétiser, bloquer suppression pour commandes en cours, définir statuts et indisponibilité nouvelles commandes tout en conservant anciens détails. Protéger appels getMenu()->getNbreMin() lorsqu'ancienne commande sans menu. Ne pas ajouter d'entité/journal inutile.

### Suite

- Responsive : double import app.css retiré de assets/app.js ; CSS responsive et lien base présents. Résultat global à vérifier. Burger proposé mais absent du code relu. Tests visuels uniquement sur demande explicite.
- Statistiques/NoSQL : aucun raccordement trouvé, préparer une recherche de vidéos pédagogiques avant implémentation ; à faire dans un prochain échange.
- Sécurité déjà différée : sessions désactivées, limitations reset, condensat tokens et journalisation. Petit correctif expiration à reprendre : null avant comparaison puis >=. Pas de nouvelle obligation ECF inférée.

## Progression actuelle

Modification et annulation des commandes implémentées côté client et employé et relues ; l'utilisatrice indique que l'affichage dynamique fonctionne. Transactions et verrou pessimiste avant vérifications ; annulation partagée dans GestionCommandeService ; interventions employé et informations d'annulation dans historique admin. Bouton Réessayer client réutilise le formulaire d'adresse ouvert. Dernier calculSousTotal JS client fourni avec arrondis intermédiaires : dernière copie à confirmer lors des tests.

Ne lancer aucun test : séances de tests distinctes demandées. Ne pas confondre implémenté et validé par tests. Ne pas modifier le code applicatif.

Choix : modifications successives autorisées selon statut, ancienne règle de modification unique abandonnée. Sous-total après réduction. Après erreur employé : erreur visible et champs fermés, nouveau clic sur Modifier. InterventionCommande conservée ; contact préalable obligatoire ; contact et motif enregistrés pour annulation. Client statut 1 ; modification employé 1/2, annulation 1/2/3 (choix du projet).

## Avancement indicatif

Estimation actualisée le 28 octobre après suivi/statuts, filtres et horaires : environ 80 % du développement fonctionnel de l'application. À la demande de l'utilisatrice, exclure les livrables et documents de ce calcul. Estimation indicative des fonctionnalités implémentées, pas proportion de temps restant ni validation par tests. Tests et mise en ligne à suivre séparément. Anciennes estimations 65 % global / 75 % application utilisaient un autre périmètre ; ne pas les comparer directement. Documents externes et déploiement non inspectés : confirmer leur état. Énoncé local relu intégralement (12 pages).

## Dernières réalisations — 28 octobre 2026

- Suivi du statut implémenté dans modifierStatut(), route POST dédiée, formulaire raccordé dans details() et les réaffichages, historique daté, transaction et verrou pessimiste. Ancienne surcharge updateEntity remplacée. Annulation conservée dans le parcours dédié ; choix 8 retiré de StatutType.
- Restitution : passage au statut 7 avec prêt de matériel confirme la restitution (restitutionMateriel = true). Aucun formulaire supplémentaire requis. Mails des statuts 6 et 7 encore à faire.
- Filtres commandes par statut et client : l'utilisatrice confirme avoir corrigé configureFilters avec les propriétés directes et les types adaptés. Marqués réalisés sur sa confirmation ; dernière version non relue ni testée dans ce tour.
- Horaires du pied de page reliés aux données : l'utilisatrice demande de les marquer réalisés. Lundi = 1, créneaux absents non affichés, quatre heures nulles = Fermé. Dernière intégration non relue ni testée dans ce tour.
- Modifications de commande : arrondis JS client et transaction/finally relus. Aucun test exécuté.

## Tâches ECF restantes

### Progression et sécurité des comptes — 31 octobre 2026

- Création employé : UserCrudController relu le 31 octobre. persistEntity() hache le mot de passe choisi par José, impose ROLE_EMPLOYE côté serveur, enregistre puis appelle MailService avec le modèle 8484212 sans transmettre le mot de passe. Rôle masqué à la création ; CRUD réservé à ROLE_ADMIN. Coordonnées téléphone/adresse/code postal/ville nullable dans User et setters ?string ; NotBlank conservés dans InscriptionUserType pour les clients. Implémentation relue, aucun test exécuté. Confirmation de l'utilisatrice : migration et configuration du mail terminées ; seuls les tests restent pour les comptes employés. Ne pas conserver le mot de passe en clair en BDD.
- Mails bienvenue et confirmation commande : implémentés via SDK Mailjet et réception confirmée par l'utilisatrice. Ne plus les lister à développer.
- Réinitialisation de mot de passe : parcours implémenté et relu ; validations fonctionnelles restantes lors des séances dédiées, renforcements ci-dessous.
- Désactivation employé : BooleanField isActive, état actif par défaut, UserChecker et configuration firewall en place ; interdit une nouvelle authentification. Protection ROLE_ADMIN sur UserCrudController et affichage de l'erreur de connexion désormais présents et relus.
- Sécurité différée à la demande de l'utilisatrice : rendre inopérante une session déjà ouverte d'un compte ensuite désactivé, à sa prochaine requête. UserChecker seul ne garantit pas cette révocation. Pas d'implémentation maintenant ; ne pas présenter la désactivation comme révoquant déjà les sessions actives. Vérifier ce scénario pendant les tests sécurité.

### Sécurité — réinitialisation de mot de passe (31 octobre 2026)

- Prévoir une limitation des demandes répétées de lien (anti-abus), tout en conservant une réponse générique que le compte existe ou non.
- Protéger le stockage des tokens de réinitialisation : envisager un condensat stocké plutôt que le token utilisable en clair ; adapter la recherche et conserver expiration et invalidation après utilisation.
- Traiter les refus et erreurs de Mailjet dans le service partagé sans divulguer l'existence d'un compte ; réponse API actuellement ignorée.
- Parcours de réinitialisation relu le 31 octobre : génération aléatoire, durée 10 minutes, URL absolue, formulaire associé à User avec hash_property_path, NotBlank/Length/Regex, effacement token/date après succès et redirection connexion implémentés. Aucun test exécuté. Petit ajustement conseillé : vérifier date null avant comparaison et utiliser >= pour l'expiration. Option passwordHasher inutilisée encore présente dans ResetPassType : nettoyage facultatif.

### Pages légales — compléments à effectuer (30 octobre 2026)

Les trois templates CGV, politique de confidentialité et mentions légales ont été relus : fichiers complets et règles métier cohérentes comme base ECF. Ne pas les réécrire intégralement à la reprise. Compléments demandés à conserver dans les tâches :
- Uniformiser l'email de contact du pied de page dans templates/base.html.twig avec vite.gourmand2027@gmail.com, déjà utilisé dans les trois pages.
- Compléter l'identité de l'éditeur, du responsable de publication et du responsable effectif du traitement, selon la réalité du site déployé ; ne pas inventer d'identifiants légaux.
- Renseigner l'URL finale et les coordonnées de l'hébergeur après choix du déploiement.
- Définir et appliquer les durées de conservation des comptes, commandes/interventions, échanges et journaux ; compléter la politique.
- Vérifier la localisation des traitements et les garanties de transferts éventuels hors EEE des prestataires ; compléter la politique.
- Identifier les cookies et leurs durées sur l'environnement déployé ; compléter l'information.
- Adapter les bases légales de la politique aux traitements réels de la démonstration, distincts du scénario commercial décrit par l'ECF. Ne pas imposer de reformulation de toutes les pages en simulation : ce n'est pas une exigence de l'énoncé.
- Pour une exploitation commerciale réelle : identifier le médiateur effectivement désigné et ses coordonnées dans les CGV ; point non résolu par une simple mention générique de la médiation.
- Mettre en œuvre le mail de restitution déjà décrit dans les CGV (statut 6, dix jours ouvrés, 600 euros) ; garder le lien avec le chantier emails ci-dessous.
- Actualiser la politique lors de la mise en service des avis, du formulaire de contact et de la réinitialisation de mot de passe, ainsi que de nouveaux traitements éventuels.
- Paiement retenu pour le scénario ECF : à la livraison par carte bancaire ou virement, sans acompte ni paiement intégré au site ; choix proposé et repris dans les CGV, pas exigence de l'énoncé. Aucun développement de paiement à ajouter.

### Fonctionnalités et livraison

- Mails restant à développer : commande terminée invitant à donner avis, attente matériel (10 jours ouvrés, 600 euros). Mail compte employé désormais raccordé au modèle 8484212, configuration terminée selon l'utilisatrice ; réception à tester. Bienvenue, confirmation et lien de réinitialisation déjà implémentés via Mailjet.
- Avis depuis commande terminée, note 1 à 5/commentaire, validation/refus employé, accueil affichant les vrais avis validés. Entité et CRUD existent mais parcours restant ; harmoniser mapping Avis OneToOne / collection Commande.
- Gestion employés par José : création avec hachage, rôle employé imposé et notification implémentée et relue. Chantier terminé selon l'utilisatrice, hors tests de création, connexion, réception du mail et désactivation. Vérifier que le compte José est préparé hors application et les droits lors des tests. Désactivation et protection ROLE_ADMIN du CRUD déjà implémentées ; révocation des sessions actives suivie en sécurité.
- Statistiques José : graphique comparatif commandes/menu depuis NoSQL, chiffre d'affaires/menu avec filtres menu/période. Aucun raccordement NoSQL/statistiques trouvé.
- Contact : formulaire titre/description/email et envoi entreprise ; contrôleur actuel affiche seulement vue.
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
