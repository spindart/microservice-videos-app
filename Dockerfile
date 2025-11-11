FROM php:8.1-fpm

RUN apt-get update && apt-get -y install git

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /var/www
