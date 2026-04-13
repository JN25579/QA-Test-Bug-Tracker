FROM php:8.3-apache

WORKDIR /var/www/html

COPY php-basics/ /var/www/html/

RUN mkdir -p /var/www/html/data \
    && chown -R www-data:www-data /var/www/html
