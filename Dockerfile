# 🧱 Image de base PHP + Apache
FROM php:8.2-apache

# 🌍 Variables d'environnement
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV COMPOSER_ALLOW_SUPERUSER=1

# ⚙️ Installation des dépendances système + extensions PHP nécessaires
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libicu-dev libzip-dev libxml2-dev libonig-dev zlib1g-dev mariadb-client \
    g++ make autoconf pkg-config libsodium-dev \
    && docker-php-ext-install intl mbstring pdo pdo_mysql zip opcache sodium \
    && a2enmod rewrite ssl headers \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# ⚙️ Apache configuration HTTP
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

RUN echo "<VirtualHost *:80>\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Options FollowSymLinks\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog \${APACHE_LOG_DIR}/error.log\n\
    CustomLog \${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# 🔐 Apache configuration HTTPS (factice pour build)
RUN mkdir -p /etc/ssl/certs /etc/ssl/private \
    && touch /etc/ssl/certs/fullchain.pem /etc/ssl/private/privkey.pem \
    && echo "<IfModule mod_ssl.c>\n\
<VirtualHost *:443>\n\
    DocumentRoot /var/www/html/public\n\
    SSLEngine on\n\
    SSLCertificateFile /etc/ssl/certs/fullchain.pem\n\
    SSLCertificateKeyFile /etc/ssl/private/privkey.pem\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Options FollowSymLinks\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog \${APACHE_LOG_DIR}/error_ssl.log\n\
    CustomLog \${APACHE_LOG_DIR}/access_ssl.log combined\n\
</VirtualHost>\n\
</IfModule>" > /etc/apache2/sites-available/default-ssl.conf \
    && a2ensite default-ssl.conf

# 🚀 Optimisation OPCache
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=256\n\
opcache.interned_strings_buffer=16\n\
opcache.max_accelerated_files=20000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache.ini

# 📦 Copier Composer depuis l'image officielle
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 📁 Dossier de travail
WORKDIR /var/www/html

# 🔥 Copie du projet (assure-toi que tu fais le build depuis le dossier racine Symfony)
COPY . .

# 🗂️ Créer les dossiers nécessaires
RUN mkdir -p var /var/www/certbot/.well-known/acme-challenge

# ⚠️ S’assurer que public/.htaccess existe
RUN test -f public/.htaccess || echo "RewriteEngine On\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteRule ^(.*)$ index.php [QSA,L]" > public/.htaccess

# 🧰 Installer les dépendances Symfony
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# 🔑 Permissions correctes
RUN chown -R www-data:www-data var /var/www/certbot

EXPOSE 80 443

CMD ["apache2-foreground"]
