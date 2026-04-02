#!/usr/bin/env sh
set -eu

cd /var/www/html

rm -f bootstrap/cache/*.php

if [ ! -f vendor/autoload.php ]; then
  echo "Installing Composer dependencies..."
  composer install --no-interaction --prefer-dist
fi

php artisan migrate --force
php artisan db:seed --class=ShieldSeeder --force

seed_count="$(php artisan tinker --execute="echo \App\Models\Team::count() + \App\Models\Player::count() + \App\Models\Applicant::count();" | tr -d '\r\n')"
if [ "${seed_count}" = "0" ]; then
  echo "Seeding demo data (teams, players, applicants)..."
  php artisan db:seed --class=DatabaseSeeder --force
fi

if [ ! -L public/storage ]; then
  php artisan storage:link || true
fi

exec php artisan serve --host=0.0.0.0 --port=8000 --no-reload
