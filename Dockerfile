# 🧱 Image PHP + Apache
FROM php:8.2-apache

# 🌍 Variables d'environnement
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV COMPOSER_ALLOW_SUPERUSER=1

# ⚙️ Installer dépendances système + extensions PHP
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libicu-dev libzip-dev libxml2-dev libonig-dev zlib1g-dev mariadb-client \
    g++ make autoconf pkg-config libsodium-dev \
    && docker-php-ext-install intl mbstring pdo pdo_mysql zip opcache sodium \
    && a2enmod rewrite ssl headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ⚙️ Copier fichiers de configuration Apache si présents
COPY 000-default.conf /etc/apache2/sites-available/000-default.conf
COPY default-ssl.conf /etc/apache2/sites-available/default-ssl.conf

RUN if [ -f /etc/apache2/sites-available/000-default.conf ]; then a2ensite 000-default.conf; fi \
    && if [ -f /etc/apache2/sites-available/default-ssl.conf ]; then a2ensite default-ssl.conf; fi

# 🚀 OPCache
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=256\n\
opcache.interned_strings_buffer=16\n\
opcache.max_accelerated_files=20000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache.ini

# 📦 Copier Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 📁 Dossier de travail
WORKDIR /var/www/html

# 🔥 Copier le projet
COPY . .

# 🗂️ Créer les dossiers Symfony + certbot challenge
RUN mkdir -p var/cache var/log var/sessions /var/www/certbot/.well-known/acme-challenge \
    && chown -R www-data:www-data var /var/www/certbot

# ⚠️ Vérifier que public/.htaccess existe
RUN test -f public/.htaccess || echo -e "RewriteEngine On\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteRule ^(.*)$ index.php [QSA,L]" > public/.htaccess

# 🧰 Installer dépendances Symfony
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

EXPOSE 80 443

CMD ["apache2-foreground"]
