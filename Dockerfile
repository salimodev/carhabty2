# Image PHP avec Apache
FROM php:8.2-apache

# Installer les extensions PHP nécessaires
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

# Configurer OPCache pour production
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=256\n\
opcache.interned_strings_buffer=16\n\
opcache.max_accelerated_files=10000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache.ini

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le projet
COPY . /var/www/html

# Créer les dossiers nécessaires pour Symfony
RUN mkdir -p var/cache var/log var/sessions vendor

# Installer les dépendances PHP
RUN php -d memory_limit=-1 /usr/bin/composer install --no-dev --optimize-autoloader --classmap-authoritative

# Permissions correctes pour Symfony
RUN chown -R www-data:www-data var/cache var/log var/sessions vendor

# Variables d'environnement Symfony
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Exposer le port 80
EXPOSE 80

# Commande par défaut
CMD ["apache2-foreground"]
