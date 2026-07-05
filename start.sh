#!/bin/sh
echo "=== Railway Debug ==="
echo "PORT=$PORT"
echo "APP_ENV=$APP_ENV"
echo "RAILWAY_PUBLIC_DOMAIN=$RAILWAY_PUBLIC_DOMAIN"

# Auto-set APP_URL from Railway if not provided
if [ -z "$APP_URL" ] && [ -n "$RAILWAY_PUBLIC_DOMAIN" ]; then
    export APP_URL="https://$RAILWAY_PUBLIC_DOMAIN"
    echo "APP_URL=$APP_URL"
fi

# Generate APP_KEY if not set
if [ -z "$APP_KEY" ]; then
    php artisan key:generate --force
fi

# Run migrations
php artisan migrate --force

# Start Laravel
exec php artisan serve --host=0.0.0.0 --port=$PORT
