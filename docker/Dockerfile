FROM php:8.4-fpm-alpine

RUN apk add --no-cache \
    libzip-dev \
    libjpeg-turbo-dev \
    libpng-dev \
    libwebp-dev \
    freetype-dev \
    mysql-client \
    git \
    supervisor \
    && docker-php-ext-install pdo pdo_mysql zip exif pcntl \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j$(nproc) gd

COPY --from=composer/composer:latest /usr/bin/composer /usr/bin/composer

ARG WWWGROUP
ARG WWWUSER

RUN set -eux; \
    addgroup -g "${WWWGROUP}" --system sail; \
    adduser -G sail -u "${WWWUSER}" --system -D sail;

USER laravel

WORKDIR /var/www/html
USER sail
