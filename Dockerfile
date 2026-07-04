FROM php:8.2-apache

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

RUN a2enmod rewrite && \
    a2dismod mpm_event && \
    a2enmod mpm_prefork

COPY . /var/www/html

RUN chown -R www-data:www-data /var/www/html \
    && chmod -R 755 /var/www/html/storage \
    && chmod -R 755 /var/www/html/bootstrap/cache

RUN composer install --no-dev --no-interaction --optimize-autoloader

RUN php artisan storage:link

EXPOSE 80

CMD php artisan migrate --force && php artisan config:cache && php artisan route:cache && php artisan view:cache && apache2-foreground
