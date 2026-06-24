#!/usr/bin/env sh
set -eu

php artisan config:clear
php artisan cache:clear || true
php artisan migrate --force
php artisan db:seed --class=TourismPlacesSeeder --force
php artisan config:cache

exec php artisan serve --host=0.0.0.0 --port="${PORT:-10000}"
