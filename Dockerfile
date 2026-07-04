FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-install pdo_mysql mbstring zip gd intl xml

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN chown -R www-data:www-data . \
    && chmod -R 755 storage bootstrap/cache

RUN composer install --no-dev --no-interaction --optimize-autoloader
RUN php artisan storage:link

EXPOSE 8000

ENV PORT=8000
EXPOSE 8000

CMD php artisan serve --host=0.0.0.0 --port=$PORT
