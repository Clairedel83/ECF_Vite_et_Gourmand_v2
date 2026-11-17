# Evaluation en Cours de Formation TP – Developpeur Web et Web Mobile
## Application web responsive réalisée dans le cadre d'une évaluation de fin d'études pour le titre professionnel **_ Développeur web et web mobile _**


# Présentation du projet : Vite & Gourmand
Vite & Gourmand est une entreprise de restauration située à Bordeaux depuis 25 ans. L’application demandée permet de consulter les menus du moment, de commander un menu et de suivre l'historique de ses commandes. Des espaces employé et administrateur permettent de gérer le catalogue, les commandes, les avis et les statistiques. 


## Liens
- Application déployée : https://vite-gourmand.claire-delmau.fr/
Les identifiants sont disponibles dans la documentation (cf dossier de rendu de l'ECF).
- Gestion de projet : https://trello.com/b/i7f7NKVa/vite-gourmand
- Livrables (charte graphique, copie ECF, documentation technique, gestion de projet, manuel d'utilisation, export SQL de la BDD) : https://drive.google.com/drive/folders/1ntFZfuAD4Cl80xBsnG_FO6uRKOrUsgEW?usp=sharing


## Technologies
- Trello pour la gestion de projet
- Figma pour le maquettage
- draw.io pour réaliser les diagrammes UML
- PHP 8.5 et Symfony 8.1
- Twig, HTML, CSS et JavaScript
- Doctrine et MySQL 8.4 pour la base de données relationnelle
- MongoDB 8.0 pour la base de données non relationnelle
- EasyAdmin, Bootstrap et Chart.js
- Docker et Docker Compose

Les e-mails sont envoyés avec Mailjet. La vérification des adresses utilise l'API Géoplateforme et le calcul des distances utilise l'API Google Routes.


## Installation locale

### Prérequis
Installer Git et Docker avec Docker Compose.
PHP, Composer, Apache, MySQL et MongoDB sont fournis par les conteneurs.

### 1. Cloner le dépôt
```bash
git clone https://github.com/Clairedel83/ECF_Vite_et_Gourmand_v2.git
cd ECF_Vite_et_Gourmand_v2
```

### 2. Configurer l’environnement
Créer un fichier `.env.local` à la racine du projet :

```dotenv
APP_ENV=dev
APP_DEBUG=1
APP_SECRET=VOTRE_SECRET_ALEATOIRE
DEFAULT_URI=http://localhost:8080

MYSQL_PASSWORD=VOTRE_MOT_DE_PASSE_LOCAL
MYSQL_ROOT_PASSWORD=VOTRE_MOT_DE_PASSE_ROOT_LOCAL

MONGODB_URI=mongodb://mongodb:27017
MONGODB_DATABASE=vite_et_gourmand

GOOGLE_ROUTES_API_KEY=VOTRE_CLE_GOOGLE
MJ_APIKEY_PUBLIC=VOTRE_CLE_PUBLIQUE_MAILJET
MJ_APIKEY_PRIVATE=VOTRE_CLE_PRIVEE_MAILJET
```

Remplacer les valeurs par celles de votre environnement. La connexion MySQL du conteneur web est configurée par Docker Compose.
Les parcours utilisant les services externes nécessitent des clés valides. Pour Mailjet, l’expéditeur et les identifiants des modèles définis dans le code doivent également correspondre à votre compte.

Ne pas publier le fichier `.env.local`.

### 3. Démarrer l’environnement
```bash
docker compose --env-file .env.local up -d --build
```

Attendre la fin de l’initialisation de MySQL, puis installer les dépendances :

```bash
docker compose --env-file .env.local exec web composer install
```

Les ressources front-end sont gérées par AssetMapper et Importmap. Aucune installation npm n’est nécessaire.

### 4. Initialiser les données
Importer les fichiers SQL de création et d’insertion des données dans la base MySQL `vite_et_gourmand`.
Ce fichier est disponible dans le dossier de rendu de l'ECF. 

Les données initiales comprennent les rôles, le catalogue et les comptes de démonstration.

Alimenter ensuite MongoDB pour les statistiques :

```bash
docker compose --env-file .env.local exec web php bin/console app:synchroniser-commandes
```

### 5. Accéder à l’application
Ouvrir http://localhost:8080 dans un navigateur.

## Arrêter l’environnement

```bash
docker compose --env-file .env.local down
```

Les données sont conservées dans les volumes Docker.