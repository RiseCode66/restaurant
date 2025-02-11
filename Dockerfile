FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite (nécessaire pour Symfony)
RUN a2enmod rewrite

# Copier les fichiers du projet dans /var/www/html/
COPY . /var/www/html/
# Installer Composer
RUN apt-get update && apt-get install -y curl unzip \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer
# Définir la racine du projet comme répertoire de travail
WORKDIR /var/www/html
RUN composer require symfony/debug-bundle --dev
#RUN composer install --no-dev --optimize-autoloader

# Modifier le DocumentRoot d'Apache pour qu'il pointe vers public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf
# Changer les permissions des fichiers du répertoire public
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Exposer le port 80
EXPOSE 80

# Démarrer Apache en mode premier plan
CMD ["apache2-foreground"]

RUN ls -l /var/www/html/ > /var/www/html/public/arborescence.txt
