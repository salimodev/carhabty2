# 🧱 Image de base PHP + Apache
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

# ⚙️ Configuration Apache HTTP et HTTPS
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf

# HTTP
RUN echo "<VirtualHost *:80>
    DocumentRoot /var/www/html/public
    <Directory /var/www/html/public>
        AllowOverride All
        Options FollowSymLinks
        Require all granted
    </Directory>

    Alias /.well-known/acme-challenge /var/www/certbot/.well-known/acme-challenge
    <Directory /var/www/certbot/.well-known/acme-challenge>
        AllowOverride None
        Options MultiViews Indexes SymLinksIfOwnerMatch
        Require all granted
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# HTTPS (certificat temporaire pour build)
RUN mkdir -p /etc/ssl/certs /etc/ssl/private \
    && touch /etc/ssl/certs/fullchain.pem /etc/ssl/private/privkey.pem \
    && echo "<IfModule mod_ssl.c>
<VirtualHost *:443>
    DocumentRoot /var/www/html/public
    SSLEngine on
    SSLCertificateFile /etc/ssl/certs/fullchain.pem
    SSLCertificateKeyFile /etc/ssl/private/privkey.pem
    <Directory /var/www/html/public>
        AllowOverride All
        Options FollowSymLinks
        Require all granted
    </Directory>
    ErrorLog \${APACHE_LOG_DIR}/error_ssl.log
    CustomLog \${APACHE_LOG_DIR}/access_ssl.log combined
</VirtualHost>
</IfModule>" > /etc/apache2/sites-available/default-ssl.conf \
    && a2ensite default-ssl.conf

# 🚀 OPCache pour prod
RUN echo "opcache.enable=1
opcache.memory_consumption=256
opcache.interned_strings_buffer=16
opcache.max_accelerated_files=20000
opcache.revalidate_freq=0
opcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache.ini

# 📦 Copier Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# 📁 Travailler dans le dossier projet
WORKDIR /var/www/html
COPY . .

# 🗂 Créer dossiers Symfony nécessaires
RUN mkdir -p var/cache var/log var/sessions /var/www/certbot/.well-known/acme-challenge

# ⚠️ Vérifier que .htaccess existe
RUN test -f public/.htaccess || echo "RewriteEngine On\nRewriteCond %{REQUEST_FILENAME} !-f\nRewriteRule ^(.*)$ index.php [QSA,L]" > public/.htaccess

# 🧰 Installer dépendances Symfony (sans scripts pour éviter crash si DB inaccessible)
RUN composer install --no-dev --optimize-autoloader --no-scripts --no-interaction

# 🔑 Permissions
RUN chown -R www-data:www-data var /var/www/certbot

EXPOSE 80 443

CMD ["apache2-foreground"]
