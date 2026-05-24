#!/usr/bin/env bash

set -euo pipefail

APP_DIR="/var/www/html/accounting-laravel"

cd "${APP_DIR}"

php artisan down --retry=60 || true

composer install --no-dev --prefer-dist --optimize-autoloader --no-interaction

php artisan migrate --force

php artisan storage:link --force || true

php artisan config:cache
php artisan route:cache
php artisan view:cache

chmod -R ug+rwx storage bootstrap/cache

php artisan up || true
