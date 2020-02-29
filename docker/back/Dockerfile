FROM php:7.4-fpm-alpine as app

ENV COMPOSER_ALLOW_SUPERUSER=1

WORKDIR /var/www/html

COPY ./back /var/www/html

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

COPY ./docker/php/php.ini /usr/local/etc/php/conf.d/

RUN composer install

RUN apk add --update --no-cache --virtual .ext-deps bash && docker-php-ext-install pdo_mysql

CMD bash -c "php-fpm"
