#!/bin/sh
set -e

export APP_ENV=prod
export APP_DEBUG=0

echo "🚀 Starting app..."

mkdir -p var/cache var/log var/sessions/prod

echo "🧨 Reset database..."

php bin/console doctrine:database:drop --force --if-exists --env=prod || true
php bin/console doctrine:database:create --env=prod

# IMPORTANT : reset complet du schéma Doctrine
php bin/console doctrine:schema:drop --force --full-database --env=prod || true
php bin/console doctrine:schema:create --env=prod

echo "🧱 Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

echo "🧹 Cache..."
php bin/console cache:clear --env=prod --no-debug || true
php bin/console cache:warmup --env=prod --no-debug || true

chown -R www-data:www-data var || true

exec php-fpm -F