FROM php:8.2-apache-buster

# Mettre à jour et installer paquets système nécessaires
RUN apt-get update && \
    apt-get install -y --no-install-recommends \
        apt-transport-https \
        ca-certificates \
        libicu-dev \
        libzip-dev \
        zlib1g-dev \
        unzip \
        git \
        libonig-dev \
        libxml2-dev \
        g++ \
        make \
        autoconf \
        pkg-config \
    && rm -rf /var/lib/apt/lists/*

# Installer extensions PHP
RUN docker-php-ext-install intl pdo pdo_mysql mbstring zip opcache

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le projet
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative

# Créer les dossiers Symfony si absents et donner les permissions
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var

EXPOSE 80
CMD ["apache2-foreground"]
