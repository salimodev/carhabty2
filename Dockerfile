# Étape 1 : Image de base PHP avec Apache
FROM php:8.2-apache-buster

# Étape 2 : Installation des extensions PHP requises
RUN apt-get update && apt-get install -y \
    libicu-dev \
    libzip-dev \
    unzip \
    git \
    && docker-php-ext-install intl zip \
    && a2enmod rewrite

# Étape 3 : Installation de Composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# Étape 4 : Configuration du répertoire de travail
WORKDIR /var/www/html

# Étape 5 : Copie du code source dans le conteneur
COPY . .

# Étape 6 : Installation des dépendances PHP
RUN composer install --no-dev --optimize-autoloader

# Étape 7 : Configuration des permissions
RUN chown -R www-data:www-data /var/www/html/var

# Étape 8 : Exposition du port 80
EXPOSE 80
