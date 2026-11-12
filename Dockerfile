FROM php:8.5.0-apache

# Installe unzip pour extraire les dépendances téléchargées par Composer
# apt-get update : actualise la liste des paquets disponibles.
RUN apt-get update \
    && apt-get install -y --no-install-recommends unzip \
    && rm -rf /var/lib/apt/lists/*

# pars de l'image php+apache et ajoute le pilote php nécessaire à la connexion mySQL
RUN docker-php-ext-install pdo_mysql

# pars de l'image php+apache et ajoute le pilote php nécessaire à la connexion mongoDB
RUN pecl install mongodb-2.5.0 \
    && docker-php-ext-enable mongodb

# Copie la configuration Apache du projet dans l’image pour servir uniquement le dossier public de Symfony au navigateur (sécurité)
COPY docker/apache.conf /etc/apache2/sites-available/000-default.conf

# Ajoute Composer pour gérer les dépendances PHP du projet
COPY --from=composer:2 /usr/bin/composer /usr/local/bin/composer