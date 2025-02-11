FROM php:8.2-apache

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql

# Activer mod_rewrite (nécessaire pour Symfony)
RUN a2enmod rewrite

# Installer Composer
RUN apt-get update && apt-get install -y curl unzip \
    && curl -sS https://getcomposer.org/installer | php \
    && mv composer.phar /usr/local/bin/composer

# Définir la racine du projet comme répertoire de travail
WORKDIR /var/www/html

# Copier uniquement composer.json et composer.lock pour optimiser le cache Docker
COPY composer.json composer.lock ./

# Installer les dépendances sans DebugBundle
RUN composer install --no-dev --optimize-autoloader --prefer-dist

# Copier le reste du projet
COPY . ./

# Modifier le DocumentRoot d'Apache pour pointer vers public/
RUN sed -i 's|/var/www/html|/var/www/html/public|g' /etc/apache2/sites-available/000-default.conf

# Ajout des permissions correctes
RUN chown -R www-data:www-data /var/www/html && chmod -R 755 /var/www/html

# Correction pour éviter l'erreur DebugBundle
RUN sed -i '/DebugBundle/d' config/bundles.php

# Exposer le port 80
EXPOSE 80

# Démarrer Apache en mode premier plan
CMD ["apache2-foreground"]

# Debug : Vérifier le contenu du dossier vendor
RUN ls -l /var/www/html/vendor/ > /var/www/html/public/arborescence.txt
