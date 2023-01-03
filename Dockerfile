FROM php:8.1-fpm-buster

RUN apt-get update && apt-get install -y \
    git \
    zip \
    icu-devtools libicu-dev \
    libzip-dev \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/* /tmp/* /var/tmp/*

RUN docker-php-ext-configure \
    intl \
    && docker-php-ext-install \
        pdo_mysql \
        opcache \
        intl \
        zip \
        sockets

RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
WORKDIR /var/www
ENV PATH="${PATH}:/var/www:/var/www/vendor/bin"
