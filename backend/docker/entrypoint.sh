#!/usr/bin/env sh
set -e

if [ ! -f .env ]; then
  cp .env.example .env
fi

if [ ! -f database/database.sqlite ]; then
  mkdir -p database
  touch database/database.sqlite
fi

php artisan key:generate --force --no-interaction
php artisan migrate --force --no-interaction
php artisan config:clear --no-interaction
php artisan config:cache --no-interaction

exec "$@"
