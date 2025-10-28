# Image de base PHP avec Apache
FROM php:8.2-apache-buster

# Installation des extensions et utilitaires requis
RUN apt-get update && apt-get install -y --no-install-recommends \
        libicu-dev \
        libzip-dev \
        zlib1g-dev \
        unzip \
        git \
        libonig-dev \
        libxml2-dev \
        gnupg \
        dirmngr \
    && docker-php-ext-install intl pdo pdo_mysql mbstring zip opcache \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Répertoire de travail
WORKDIR /var/www/html

# Copier le code source
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative

# Permissions Symfony
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var

# Exposer le port 80
EXPOSE 80

# Commande par défaut
CMD ["apache2-foreground"]
