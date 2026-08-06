FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

FROM php:8.3-cli-alpine
WORKDIR /var/www/html

RUN apk add --no-cache \
    bash \
    icu-dev \
    libzip-dev \
    oniguruma-dev \
    sqlite \
    sqlite-dev \
    wget \
    zip \
    unzip \
    && docker-php-ext-install \
        bcmath \
        intl \
        pdo_sqlite \
        zip

COPY . .
COPY --from=vendor /app/vendor ./vendor

RUN chmod +x docker/entrypoint.sh \
    && chown -R www-data:www-data storage bootstrap/cache database

EXPOSE 8000

ENTRYPOINT ["/var/www/html/docker/entrypoint.sh"]
CMD ["sh", "-c", "cd /var/www/html/public && exec php -S 0.0.0.0:8000 /var/www/html/vendor/laravel/framework/src/Illuminate/Foundation/resources/server.php"]
