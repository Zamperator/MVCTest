# Use the official PHP 8 image as the base image
FROM php:8.3-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
    nginx \
    libpq-dev \
    libmemcached-dev \
    zlib1g-dev \
    libpng-dev \
    libjpeg-dev \
    libfreetype6-dev \
    libmcrypt-dev \
    libmongodb-dev \
    libcurl4-openssl-dev \
    libonig-dev

RUN docker-php-ext-install mysqli pdo pdo_mysql mongodb bcmath gd mbstring curl
RUN pecl install memcached
RUN pecl install mongodb
RUN docker-php-ext-enable pdo pdo_mysql memcached mongodb curl gd mbstring bcmath

RUN pecl install xdebug
RUN docker-php-ext-enable xdebug