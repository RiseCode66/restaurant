FROM php:8.2-apache  

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql  

# Copier les fichiers du projet dans le répertoire /var/www/html/
COPY . /var/www/html/

# Changer les permissions des fichiers du répertoire public
RUN chmod -R 755 /var/www/html/public

# Définir le répertoire de travail
WORKDIR /var/www/html/

# Exposer le port 80
EXPOSE 80

# Démarrer Apache en mode premier plan
CMD ["apache2-foreground"]
RUN ls -la /var/www/html/ > /var/www/html/arborescence.txt
