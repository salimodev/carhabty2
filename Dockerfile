# Image PHP avec Apache
FROM php:8.2-fpm-alpine

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Configuration OPCache pour production
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=10000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache.ini

# Installer les extensions PHP nécessaires par étapes pour réduire la RAM
RUN apt-get update && apt-get install -y --no-install-recommends libonig-dev libzip-dev zlib1g-dev \
    && docker-php-ext-install pdo pdo_mysql mbstring zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Installer les utilitaires légers séparément
RUN apt-get update && apt-get install -y --no-install-recommends zip unzip git \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copier le projet dans le conteneur
COPY . /var/www/html/

# Définir le répertoire de travail
WORKDIR /var/www/html/

# Variables d'environnement Symfony
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative

# Créer les dossiers nécessaires et définir les permissions
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var

# Exposer le port 80 pour Apache
EXPOSE 80

# Commande par défaut
CMD ["apache2-foreground"]
