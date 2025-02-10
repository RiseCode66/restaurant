FROM php:8.2-apache  

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql  

# Copier les fichiers du projet dans le répertoire /
COPY . /

# Changer les permissions des fichiers du répertoire public
RUN chmod -R 755 /public

# Définir le répertoire de travail
WORKDIR /

# Exposer le port 80
EXPOSE 80

# Démarrer Apache en mode premier plan
CMD ["apache2-foreground"]
RUN ls > arborescence.txt
