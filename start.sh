#!/bin/sh
echo "=== Railway Debug ==="
echo "PORT=$PORT"
echo "APP_ENV=$APP_ENV"
echo "DB_HOST=$DB_HOST"

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Start Laravel
exec php artisan serve --host=0.0.0.0 --port=$PORT
