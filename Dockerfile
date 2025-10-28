# Image PHP avec Apache
FROM php:8.2-apache

# Installer les extensions nécessaires
RUN apt-get update && apt-get install -y \
    libicu-dev libzip-dev zlib1g-dev unzip git libonig-dev libxml2-dev \
    && docker-php-ext-install intl pdo pdo_mysql mbstring zip opcache \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Copier Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copier le projet Symfony
COPY . /var/www/html/

# Définir le répertoire de travail
WORKDIR /var/www/html/

# Variables d'environnement Symfony
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Installer les dépendances
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative || true

# Permissions correctes pour Symfony
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var

# Exposer le port 80 pour Apache
EXPOSE 80

# Définir un nom de serveur global pour Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Lancer Apache
CMD ["apache2-foreground"]
