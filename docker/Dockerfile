FROM php:7.4-apache

RUN apt-get update -y \
    && apt-get install -y zlib1g-dev libpng-dev \
    && docker-php-ext-install gd
