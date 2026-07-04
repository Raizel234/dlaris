#!/bin/sh
echo "PORT=$PORT"
echo "Starting Laravel..."
php artisan serve --host=0.0.0.0 --port=${PORT:-8000}
