#!/bin/sh
set -e

PORT=${PORT:-8000}

echo "==> Configuring nginx on port $PORT..."
sed -i "s/NGINX_PORT/${PORT}/g" /etc/nginx/sites-available/heroforce

echo "==> Creating storage directories..."
mkdir -p storage/framework/sessions storage/framework/views storage/framework/cache
chmod -R 775 storage bootstrap/cache

echo "==> Clearing config cache..."
php artisan config:clear

echo "==> Running migrations..."
php artisan migrate --force

echo "==> Seeding demo data..."
php artisan db:seed --force

echo "==> Caching config and routes for production..."
php artisan config:cache
php artisan route:cache
# view:cache omitido — API pura sem templates Blade

echo "==> Starting nginx + php-fpm via supervisor..."
exec /usr/bin/supervisord -c /etc/supervisor/conf.d/supervisord.conf
