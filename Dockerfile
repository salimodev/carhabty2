FROM php:8.2-fpm-alpine

# Installer les extensions PHP nécessaires et paquets système essentiels
RUN apk update && apk add --no-cache \
    icu-dev \
    libzip-dev \
    zlib-dev \
    unzip \
    git \
    oniguruma-dev \
    libxml2-dev \
    g++ \
    make \
    autoconf \
    pkgconfig \
    bash \
    && docker-php-ext-install intl pdo pdo_mysql mbstring zip opcache

# Copier Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le projet
WORKDIR /var/www/html
COPY . .

# Installer dépendances PHP
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative

# Créer dossiers Symfony si absents et donner les permissions
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var

EXPOSE 9000
CMD ["php-fpm"]
