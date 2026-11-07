# Reprise de la conversation MongoDB — 7 novembre 2026

## Consignes de collaboration à conserver

- L'utilisatrice apprend en codant : expliquer chaque notion nouvelle et chaque morceau de code, progressivement. Éviter de livrer une implémentation complète sans explications.
- Ne pas modifier le code : elle applique elle-même les corrections présentées dans le chat.
- Ne lancer aucun test sans demande explicite. Aucun test applicatif exécuté par l'assistant dans cette conversation.
- Ce récapitulatif est créé à sa demande pour être transmis dans une autre conversation. Il ne contient aucun identifiant de connexion ni mot de passe.
- Lire aussi outputs/ECF_reprise.md et outputs/ECF_choix_conception.md pour le contexte général. Le FormType d'annulation, la modale et son JS sont déjà réalisés : ne pas les recréer.
- InterventionCommande reste le choix retenu. Modifications successives selon statut autorisées ; interventions conservées ; changements de statut indépendants.

## Objectif en cours

Intégrer MongoDB au projet Symfony Vite et Gourmand pour les statistiques de José. Prochaine fonctionnalité demandée : créer automatiquement un document MongoDB à chaque création réussie de commande SQL, puis gérer plus tard l'actualisation lors des modifications, changements de statut et annulations.

## Apprentissages déjà effectués

- Différence entre JSON (déjà rencontré avec les API), BSON, document, collection et base de données.
- Atlas héberge les données ; Compass est l'interface pour les manipuler.
- Création dans Compass d'une base apprentissage et d'une collection essais ; insertion manuelle de documents fictifs.
- MongoDB génère automatiquement un _id ; cet identifiant est distinct de l'identifiant de la commande SQL.
- Filtres compris et manipulés : statut 5, statut 7, puis { "statut": { "$in": [5, 6, 7] } }. Filtrer ne supprime pas les documents.
- Dates saisies en Extended JSON avec $date ; Z indique UTC.
- L'utilisatrice estime avoir compris les bases et veut désormais travailler directement sur l'ECF.

## Choix de données et règles validées

- Un document par commande ; conserver toutes les commandes avec leur statut, y compris les annulées. Appliquer les filtres lors des calculs.
- Nombre de commandes par menu : compter les commandes non annulées, en cours ou réalisées. Une commande pour dix convives compte une fois, pas dix.
- Chiffre d'affaires par menu : sous-total du menu après réduction, hors livraison et matériel.
- Chiffre d'affaires global : indicateur supplémentaire demandé par l'utilisatrice pour José, fondé sur le total de commande incluant les frais annexes.
- La prestation est considérée réalisée dès livraison. Pour le CA, inclure les statuts 5 (livrée), 6 (attente restitution matériel), 7 (terminée), et exclure préparation et annulation. Ne pas attendre le retour du matériel pour compter la prestation.
- L'utilisatrice précise qu'une pénalité de 600 euros, si effectivement appliquée pour retard de restitution, doit augmenter le CA global et non celui du menu. Aucun mécanisme de pénalité n'a été développé ici ; ne pas l'ajouter automatiquement ni prétendre qu'il existe. Sa représentation et sa synchronisation restent à concevoir.
- Ne pas confondre cet indicateur de tableau de bord avec un total d'encaissements vérifiés : pas de suivi de paiement inspecté ici.
- Le choix de date pour le filtre du CA reste à préciser (date commande ou prestation). dateLivraison est une date prévue et ne prouve pas à elle seule la réalisation.
- Proposition de champs : commandeId, menuId, menuNom, dateCommande, dateLivraison, statut, montantMenuCentimes, montantTotalCentimes. Le nom est celui conservé dans la commande ; l'ID menu permet de regrouper malgré un renommage.
- Centimes entiers proposés pour éviter les imprécisions des nombres flottants. C'est un choix technique courant, pas une obligation MongoDB ; Decimal128 est une alternative exacte. Le DECIMAL SQL existant reste adapté et n'est pas à remplacer. Aucun convertisseur écrit à ce stade.
- Pas de données personnelles client nécessaires aux statistiques.

## Installation réalisée et confirmée par captures

- Compte Atlas personnel de l'utilisatrice, pas un compte au nom de José. José est l'administrateur métier de l'application.
- Cluster gratuit vite-et-gourmand, région Paris. Base exemple sample_mflix présente ; base apprentissage/essais utilisée pour exercices.
- Utilisateur de base Atlas distinct du compte personnel Atlas. Mot de passe changé après exposition dans une ancienne capture ; connexion Compass rétablie avec le nouveau mot de passe.
- Le nouveau mot de passe est composé uniquement de lettres majuscules/minuscules selon l'utilisatrice : pas d'encodage spécial nécessaire dans l'URI. Ne jamais le demander ni l'afficher.
- PHP 8.5.0, x64, Thread Safe (TS), Visual C++ 2022 / VS17.
- Extension MongoDB 2.5.5 installée par l'utilisatrice via DLL dans le dossier ext de PHP 8.5.0, activation extension=mongodb.
- Confirmations de chargement : php --ri mongodb, symfony php --ri mongodb et affichage Wamp des extensions chargées pour Apache et CLI/FCGI. MongoDB apparaît dans ces configurations. Le menu statique Extensions PHP ne l'affichait pas : ne pas conclure à un échec sur cette seule absence.
- L'utilisatrice utilise Wamp ET le serveur Symfony.
- Bibliothèque mongodb/mongodb 2.5.1 installée. Les téléchargements ZIP GitHub ont échoué deux fois (HTTP 504), puis composer install --prefer-source a réussi par Git ; scripts Symfony affichés OK. composer.json lu ensuite : mongodb/mongodb avec contrainte *.

## Configuration d'environnement

- Explication fournie : .env porte les exemples non secrets, .env.local les valeurs locales sensibles, et Symfony donne priorité aux valeurs locales.
- L'utilisatrice a été guidée pour déplacer sa véritable DATABASE_URL SQL dans .env.local et laisser un exemple dans .env. Ces contenus n'ont pas été ouverts ni contrôlés par l'assistant.
- MongoDB ne remplace pas SQL : les deux bases sont utilisées.
- Variables prévues dans .env.local : MONGODB_URI (chaîne Atlas avec utilisateur de base et mot de passe), MONGODB_DATABASE="vite_et_gourmand".
- Ne pas lire, copier dans le récapitulatif, publier ou commiter les secrets. Vérification de l'exclusion Git de .env.local non effectuée.

## Git

- L'utilisatrice veut une branche pour montrer sa maîtrise de Git pendant MongoDB.
- Explications : git branch mongodb crée seulement la branche ; git switch mongodb bascule ; git switch -c mongodb fait les deux. Elle préfère le nom mongodb au préfixe codex proposé initialement.
- Création effective de la branche et commits non vérifiés. Ne pas les présenter comme faits.
- Les changements non commitées restent disponibles lors de la création de branche. Ne pas conseiller git add . sans examiner les fichiers sensibles.
- outputs est un dossier de notes de travail, pas un composant nécessaire à Symfony.

## État exact du code au point d'arrêt — lecture du 7 novembre

L'assistant n'a modifié aucun code applicatif. Les fichiers suivants ont été créés/modifiés par l'utilisatrice.

### config/services.yaml : correction à expliquer en premier

Le contenu enregistré présente actuellement une erreur d'indentation :

```yaml
    MongoDB\Client:
    arguments:
        $uri: '%env(MONGODB_URI)%'
```

arguments doit être imbriqué sous MongoDB\Client. Forme attendue sous services :

```yaml
    MongoDB\Client:
        arguments:
            $uri: '%env(MONGODB_URI)%'
```

Erreur découverte lors de la préparation de ce récapitulatif ; non corrigée par l'assistant. Aucun lancement de Symfony pour la vérifier.

### src/Services/StatistiquesCommandeService.php : présent, relu

```php
<?php

namespace App\Services;

use MongoDB\Client;
use MongoDB\Collection;
use Symfony\Component\DependencyInjection\Attribute\Autowire;

class StatistiquesCommandeService
{
    private Collection $collection;

    public function __construct(Client $client, #[Autowire('%env(MONGODB_DATABASE)%')] string $databaseName)
    {
        $this->collection = $client->selectDatabase($databaseName)->selectCollection('commandes');
    }
}
```

- Le dossier du projet est Services (pluriel), namespace App\Services.
- Explications déjà données : service et responsabilité, use, MongoDB Collection distincte de Doctrine Collection, propriété private typée, constructeur, injection de dépendances, Autowire de variable d'environnement, enchaînement selectDatabase/selectCollection.
- Ce code prépare l'accès à la collection commandes ; il n'insère aucun document. La base/collection peuvent être créées au premier enregistrement.
- Aucun appel au service dans le contrôleur ; aucune méthode d'écriture encore créée ; aucune connexion effective à Atlas depuis l'application confirmée.

## Contrôleur SQL relu pour préparer l'intégration

Chemin : src/Controller/Commande/CommandeController.php, namespace App\Controller\Commande, méthode index().

- Construit une Commande : menu, instantané nom/composition, dates, nombre de personnes, montants formatés à deux décimales, statut initial 1, matériel.
- Champs disponibles dans Commande : getId(), getMenu(), getMenuNom(), getDateCommande(), getDateLivraison(), getStatut(), getPrixSousTotal(), getPrixTotal(). Montants SQL en chaînes DECIMAL ; date et heure livraison séparées.
- Transaction DBAL : beginTransaction, verrou pessimiste du menu, refresh, contrôles disponibilité/stock, historique, persist, flush, commit. Catch Throwable effectue rollback si transaction encore active puis relance.
- Après ce bloc : numéro de commande en session, mail de confirmation avec gestion d'échec, vidage panier, redirection confirmation.
- Future synchronisation MongoDB : APRÈS la réussite du commit SQL et HORS du try/catch de transaction SQL. Une panne MongoDB ne doit pas faire croire que la commande SQL a échoué ou empêcher le parcours de confirmation.
- SQL reste la référence. Il faudra définir un mécanisme de reprise des synchronisations échouées : pas encore conçu ni implémenté. Un simple catch/log ne garantit pas la reprise.
- Les changements de statut, annulations et modifications des montants devront ultérieurement actualiser le document via le même service. Ne pas les présenter comme raccordés.

## Prochaine reprise conseillée

1. Expliquer et faire corriger l'indentation de config/services.yaml.
2. Ajouter progressivement dans StatistiquesCommandeService une méthode recevant une Commande et construisant les champs décidés. Expliquer tableau PHP, méthodes de l'entité, conversion exacte des montants et dates BSON.
3. Choisir une écriture évitant les doublons : upsert possible avec identifiant unique. Cette piste a été recherchée mais pas encore enseignée ni arrêtée ; prévoir une véritable unicité (index unique commandeId, ou _id déterministe) plutôt que prétendre qu'un simple filtre suffit sous concurrence.
4. Injecter le service dans le contrôleur puis l'appeler après commit avec stratégie d'erreur expliquée. Ne pas déplacer l'écriture dans la transaction SQL en prétendant que SQL et MongoDB partagent une transaction.
5. Plus tard : mises à jour, reprise après échec, filtres et agrégations, tableau de bord José, traitement distinct de la pénalité, date de référence des périodes.
6. Tests uniquement quand l'utilisatrice les demande explicitement.

## Sources officielles utilisées

- https://www.mongodb.com/docs/php-library/current/get-started/
- https://www.php.net/manual/en/mongodb.installation.php
- https://www.mongodb.com/docs/compass/documents/insert/
- https://www.mongodb.com/docs/php-library/current/crud/update/
- https://symfony.com/doc/current/service_container.html
- https://getcomposer.org/doc/03-cli.md

