FROM php:7.4-fpm-bullseye

LABEL maintainer="me"

WORKDIR /var/www/html

ENV DEBIAN_FRONTEND noninteractive
ENV TZ=UTC

# install symfony https://symfony.com/doc/current/setup.html
RUN ln -snf /usr/share/zoneinfo/$TZ /etc/localtime && echo $TZ > /etc/timezone \
	&& apt-get update && apt-get install -y git unzip zip zlib1g-dev libpng-dev libzip-dev libicu-dev vim wget gnupg default-mysql-client apt-transport-https \
    && docker-php-ext-install mysqli pdo pdo_mysql gd bcmath zip intl opcache \
    && pecl install xdebug-3.1.6 \
    && docker-php-ext-enable xdebug \
    # install composer
    && php -r "readfile('http://getcomposer.org/installer');" | php -- --install-dir=/usr/local/bin/ --filename=composer \
    # install symfony cli https://symfony.com/download
    && echo 'deb [trusted=yes] https://repo.symfony.com/apt/ /' | tee /etc/apt/sources.list.d/symfony-cli.list \
    && apt update \
    && apt install symfony-cli
