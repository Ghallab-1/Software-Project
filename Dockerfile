FROM php:8.2-apache

RUN a2enmod rewrite

RUN echo "DirectoryIndex index.php index.html" >> /etc/apache2/apache2.conf

RUN docker-php-ext-install mysqli pdo pdo_mysql

COPY . /var/www/html/

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html

ENV PORT=10000
RUN sed -i "s/80/${PORT}/g" /etc/apache2/ports.conf \
    && sed -i "s/:80/:${PORT}/g" /etc/apache2/sites-enabled/000-default.conf

EXPOSE 10000
# Environment / secrets
database/.env.php

# Optional local files
database/ca.pem
