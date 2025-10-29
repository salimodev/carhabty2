# Image PHP + Apache
FROM php:8.2-apache

# Variables d'environnement
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer les dépendances système et extensions PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libicu-dev libzip-dev libxml2-dev libonig-dev zlib1g-dev mariadb-client \
    g++ make autoconf pkg-config \
    && docker-php-ext-install intl mbstring pdo pdo_mysql zip opcache \
    && a2enmod rewrite \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configuration Apache
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf \
    && echo "<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    # Alias pour Certbot\n\
    Alias /.well-known/acme-challenge /var/www/certbot/.well-known/acme-challenge\n\
    <Directory /var/www/certbot/.well-known/acme-challenge>\n\
        AllowOverride None\n\
        Options MultiViews Indexes SymLinksIfOwnerMatch\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog ${APACHE_LOG_DIR}/error.log\n\
    CustomLog ${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Configuration OPCache
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=256\n\
opcache.interned_strings_buffer=16\n\
opcache.max_accelerated_files=20000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache.ini

# Copier Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Travailler dans le dossier projet
WORKDIR /var/www/html
COPY . /var/www/html

# Installer les dépendances Symfony
RUN php -d memory_limit=-1 /usr/bin/composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Permissions
RUN chown -R www-data:www-data var vendor 

# Créer le dossier pour Certbot et lui donner les permissions
RUN mkdir -p /var/www/certbot/.well-known/acme-challenge \
    && chown -R www-data:www-data /var/www/certbot

EXPOSE 80
CMD ["apache2-foreground"]
