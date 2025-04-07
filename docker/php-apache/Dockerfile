FROM php:8.2-apache

RUN docker-php-ext-install mysqli 

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

COPY composer.json composer.lock ./

RUN composer install