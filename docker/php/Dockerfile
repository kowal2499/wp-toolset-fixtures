FROM php:7.4-cli

MAINTAINER Roman Kowalski <roman@erla.pl>

RUN apt-get update && apt-get install -y  \
    --no-install-recommends \
    libfreetype6-dev \
    libicu-dev \
    libjpeg-dev \
    libmagickwand-dev \
    libpng-dev \
    libwebp-dev \
    libzip-dev \
    zip \
    less \
    vim; \
    docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp; \
    docker-php-ext-install bcmath exif gd intl zip;

# Install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer && chmod +x /usr/local/bin/composer
ARG GITHUB_TOKEN
RUN composer config -g github-oauth.github.com ${GITHUB_TOKEN}

VOLUME /usr/src/app
COPY ../../src /usr/src/app
WORKDIR /usr/src/app