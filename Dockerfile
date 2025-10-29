# Image PHP avec Apache
FROM php:8.2-apache

# Variables d'environnement Symfony
ENV APP_ENV=prod
ENV APP_DEBUG=0

# Installer les dépendances système nécessaires pour Symfony et extensions PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libicu-dev libonig-dev libzip-dev libxml2-dev zlib1g-dev \
    mariadb-client g++ make autoconf pkg-config \
    && docker-php-ext-install intl mbstring pdo pdo_mysql zip opcache \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Activer mod_rewrite pour Symfony
RUN a2enmod rewrite

# Configuration OPCache pour la production
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=128\n\
opcache.interned_strings_buffer=8\n\
opcache.max_accelerated_files=10000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache.ini

# Configuration Apache explicite pour Symfony
RUN echo "<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog \${APACHE_LOG_DIR}/error.log\n\
    CustomLog \${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Définir le répertoire de travail et copier le projet
WORKDIR /var/www/html
COPY . /var/www/html

# Créer les dossiers nécessaires et installer les dépendances Symfony
RUN mkdir -p var/cache var/log var/sessions vendor \
    && php -d memory_limit=-1 /usr/bin/composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Définir les permissions correctes
RUN chown -R www-data:www-data var vendor

# Exposer le port 80 pour Apache
EXPOSE 80

# Lancer Apache au premier plan
CMD ["apache2-foreground"]
