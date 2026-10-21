FROM php:8.4.27-apache

RUN a2enmod headers rewrite

COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY public/ /var/www/html/public/

WORKDIR /var/www/html
CMD ["apache2-foreground"]

EXPOSE 80
