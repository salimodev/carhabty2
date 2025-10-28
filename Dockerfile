# Étape 1 : PHP 8.2 avec Apache
FROM php:8.2-apache

# Installer les dépendances nécessaires
RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libicu-dev \
    libzip-dev \
    zip \
    && docker-php-ext-install intl pdo pdo_mysql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Définir le ServerName pour supprimer le warning
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# Copier le projet dans le conteneur
COPY . /var/www/html/

# Définir le dossier racine (Symfony = public/)
ENV APACHE_DOCUMENT_ROOT=/var/www/html/public

# Adapter la configuration Apache pour le dossier public/
RUN sed -ri -e 's!/var/www/html!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/sites-available/*.conf \
    && sed -ri -e 's!/var/www/!${APACHE_DOCUMENT_ROOT}!g' /etc/apache2/apache2.conf /etc/apache2/conf-available/*.conf

# Installer Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le dossier de travail
WORKDIR /var/www/html

# Installer les dépendances PHP Symfony
RUN composer install --no-dev --optimize-autoloader --classmap-authoritative

# Créer les dossiers nécessaires et définir les permissions
RUN mkdir -p var/cache var/log var/sessions \
    && chown -R www-data:www-data var

# Exposer le port 80
EXPOSE 80

# Lancer Apache au démarrage
CMD ["apache2-foreground"]
