# Image PHP + Apache
FROM php:8.2-apache

# Variables d'environnement pour production
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer dépendances système et extensions PHP nécessaires
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libicu-dev libzip-dev libxml2-dev libonig-dev zlib1g-dev mariadb-client \
    g++ make autoconf pkg-config libsodium-dev \
    && docker-php-ext-install intl mbstring pdo pdo_mysql zip opcache sodium \
    && a2enmod rewrite ssl headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configurer Apache HTTP et HTTPS
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf
COPY docker/apache-sites/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/apache-sites/default-ssl.conf /etc/apache2/sites-available/default-ssl.conf
RUN a2ensite default-ssl.conf

# Configurer OPCache
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=256\n\
opcache.interned_strings_buffer=16\n\
opcache.max_accelerated_files=20000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache.ini

# Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Travailler dans le dossier projet
WORKDIR /var/www/html

# Copier le projet + dépendances pré-installées
COPY . /var/www/html
COPY ./vendor /var/www/html/vendor

# Permissions
RUN chown -R www-data:www-data var vendor

# Créer le dossier pour Certbot
RUN mkdir -p /var/www/certbot/.well-known/acme-challenge \
    && chown -R www-data:www-data /var/www/certbot

EXPOSE 80 443
CMD ["apache2-foreground"]
