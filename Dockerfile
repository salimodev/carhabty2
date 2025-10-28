# Image PHP avec Apache
FROM php:8.2-apache

# Installer les extensions PHP et dépendances système pour Symfony
RUN apt-get update && apt-get install -y --no-install-recommends \
    git \
    unzip \
    libicu-dev \
    libonig-dev \
    libzip-dev \
    libxml2-dev \
    zlib1g-dev \
    mariadb-client \
    && docker-php-ext-install intl mbstring pdo pdo_mysql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Copier le fichier de configuration Apache personnalisé
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le projet Symfony
COPY . /var/www/html

# Créer les dossiers nécessaires
RUN mkdir -p var/cache var/log var/sessions vendor

# Installer les dépendances PHP avec Composer
RUN php -d memory_limit=-1 /usr/bin/composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Donner les permissions correctes à Symfony
RUN chown -R www-data:www-data var vendor

# Variables d'environnement Symfony
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Exposer le port 80 pour Apache
EXPOSE 80

# Commande par défaut
CMD ["apache2-foreground"]
