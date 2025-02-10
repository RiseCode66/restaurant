FROM php:8.2-apache  

# Installer les extensions PHP nécessaires
RUN docker-php-ext-install pdo pdo_mysql  


# Exposer le port 80
EXPOSE 80

