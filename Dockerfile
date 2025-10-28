FROM php:8.2-apache-buster

# Installer les dépendances système
RUN apt-get update && apt-get install -y --no-install-recommends \
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
    && docker-php-ext-install intl pdo pdo_mysql mbstring zip opcache \
    && a2enmod rewrite \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail
WORKDIR /var/www/html

# Copier le code source
COPY . .

# Installer les dépendances PHP
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative

# Créer et donner les permissions pour Symfony
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var

EXPOSE 80

CMD ["apache2-foreground"]
