FROM php:8.2-fpm-alpine

RUN apk update && apk add \
    git \
    curl \
    freetype-dev \
    libpng-dev \
    jpeg-dev \
    oniguruma-dev \
    libxml2-dev \
    zip \
    unzip \
    php82-openssl \
    openssl openssl-dev

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS
RUN apk add --update linux-headers
RUN pecl install openssh-devel mongodb xdebug-3.2.2

RUN docker-php-ext-configure gd  --enable-gd --with-freetype --with-jpeg
RUN docker-php-ext-install -j "$(nproc)" gd

RUN docker-php-ext-install mbstring exif pcntl bcmath
RUN docker-php-ext-enable mongodb xdebug
RUN apk del -f .build-deps



RUN echo "xdebug.start_with_request=yes" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.mode=debug" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.log=/var/www/writer/var/xdebug/xdebug.log" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.discover_client_host=1" >> /usr/local/etc/php/conf.d/xdebug.ini \
    && echo "xdebug.client_port=9000" >> /usr/local/etc/php/conf.d/xdebug.ini

WORKDIR /var/www/writer

COPY --chown=www-data:www-data . /var/www/writer

USER www-data