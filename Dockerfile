FROM php:8.4-apache

RUN a2enmod headers rewrite

COPY apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY public/ /var/www/html/public/
COPY docker-entrypoint.sh /usr/local/bin/docker-entrypoint.sh

RUN chmod 755 /usr/local/bin/docker-entrypoint.sh \
    && mkdir -p /mirror

WORKDIR /var/www/html

ENTRYPOINT ["docker-entrypoint.sh"]
CMD ["apache2-foreground"]

EXPOSE 80
