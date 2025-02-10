FROM php:8.2-apache  
# Démarrer Apache en mode premier plan
CMD ["apache2-foreground"]
RUN ls -la  > arborescence.txt
