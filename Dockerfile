FROM php:8.2-cli

RUN apt-get update && apt-get install -y \
    git \
    unzip \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    curl \
    && docker-php-ext-install pdo_mysql mbstring zip gd intl xml

# Install Node.js & npm
RUN curl -fsSL https://deb.nodesource.com/setup_20.x | bash - \
    && apt-get install -y nodejs

COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

WORKDIR /app
COPY . .

RUN chmod -R 755 storage bootstrap/cache

RUN COMPOSER_ALLOW_SUPERUSER=1 composer install --no-dev --no-interaction --optimize-autoloader
RUN php artisan storage:link

# Build Vite assets
RUN npm install && npm run build && rm -rf node_modules

EXPOSE $PORT

CMD ["/bin/sh", "start.sh"]
