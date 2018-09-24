FROM php:7.1

MAINTAINER Andrew Gilman <a.gilman@massey.ac.nz>

# Massey 159.339 Internet Programming PHP container
# version 0.1

RUN apt-get update && apt-get install -y graphviz \
    && rm -rf /var/lib/apt/lists/*

# Install mysqli extension
RUN docker-php-ext-install mysqli
# Install xdebug and php extension for xdebug
RUN pecl install xdebug && docker-php-ext-enable xdebug