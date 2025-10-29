# Image PHP + Apache
FROM php:8.2-apache

# Variables d'environnement
ENV APP_ENV=prod
ENV APP_DEBUG=0
ENV COMPOSER_ALLOW_SUPERUSER=1

# Installer les dépendances système
RUN apt-get update && apt-get install -y --no-install-recommends \
    git unzip libicu-dev libzip-dev libxml2-dev libonig-dev zlib1g-dev mariadb-client \
    g++ make autoconf pkg-config \
    && docker-php-ext-install intl mbstring pdo pdo_mysql zip opcache \
    && a2enmod rewrite ssl \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Configuration d’Apache HTTP (port 80)
RUN echo "ServerName localhost" >> /etc/apache2/apache2.conf && \
    echo "<VirtualHost *:80>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    # Redirection automatique vers HTTPS\n\
    RewriteEngine On\n\
    RewriteCond %{HTTPS} !=on\n\
    RewriteRule ^/?(.*) https://%{SERVER_NAME}/\$1 [R=301,L]\n\
    ErrorLog \${APACHE_LOG_DIR}/error.log\n\
    CustomLog \${APACHE_LOG_DIR}/access.log combined\n\
</VirtualHost>" > /etc/apache2/sites-available/000-default.conf

# Configuration SSL (port 443)
RUN echo "<IfModule mod_ssl.c>\n\
<VirtualHost *:443>\n\
    ServerAdmin webmaster@localhost\n\
    DocumentRoot /var/www/html/public\n\
    SSLEngine on\n\
    SSLCertificateFile /etc/letsencrypt/live/carahba.cloud/fullchain.pem\n\
    SSLCertificateKeyFile /etc/letsencrypt/live/carahba.cloud/privkey.pem\n\
    <Directory /var/www/html/public>\n\
        AllowOverride All\n\
        Require all granted\n\
    </Directory>\n\
    ErrorLog \${APACHE_LOG_DIR}/error_ssl.log\n\
    CustomLog \${APACHE_LOG_DIR}/access_ssl.log combined\n\
</VirtualHost>\n\
</IfModule>" > /etc/apache2/sites-available/default-ssl.conf

RUN a2ensite default-ssl

# Configuration OPCache pour accélérer Symfony
RUN echo "opcache.enable=1\n\
opcache.memory_consumption=256\n\
opcache.interned_strings_buffer=16\n\
opcache.max_accelerated_files=20000\n\
opcache.revalidate_freq=0\n\
opcache.validate_timestamps=0" > /usr/local/etc/php/conf.d/opcache.ini

# Copier Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Répertoire de travail
WORKDIR /var/www/html

# Copier le projet Symfony
COPY . /var/www/html

# Installer les dépendances PHP
RUN php -d memory_limit=-1 /usr/bin/composer install --no-dev --optimize-autoloader --ignore-platform-reqs

# Donner les bons droits
RUN chown -R www-data:www-data /var/www/html/var /var/www/html/vendor

# Exposer les ports HTTP/HTTPS
EXPOSE 80 443

# Lancer Apache
CMD ["apache2-foreground"]
