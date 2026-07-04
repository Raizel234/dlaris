#!/bin/sh
echo "=== Railway Debug ==="
echo "PORT=$PORT"
echo "Starting Laravel..."
exec php artisan serve --host=0.0.0.0 --port=$PORT
