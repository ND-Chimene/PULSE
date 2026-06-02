#!/bin/sh
set -e

echo "🚀 Starting app..."

# Create necessary directories
mkdir -p var/cache var/log var/sessions/prod

# Create SQLite database file if it doesn't exist
if [ ! -f var/pulse.db ]; then
  echo "📦 Creating SQLite database file..."
  touch var/pulse.db
fi

# Set proper permissions on the database file
chmod 666 var/pulse.db || true

# Generate schema from entities (creates tables)
echo "🗄️  Creating database schema from entities..."
php bin/console doctrine:schema:create --env=prod || true

# Run migrations
echo "🧱 Running migrations..."
php bin/console doctrine:migrations:migrate --no-interaction --env=prod

# Clear and warmup cache
echo "🧹 Clearing and warming up cache..."
rm -rf var/cache/*
php bin/console cache:clear --env=prod --no-debug || true
php bin/console cache:warmup --env=prod --no-debug || true

# Set proper permissions
echo "🔒 Setting permissions..."
chmod -R 775 var || true
chown -R www-data:www-data var || true

echo "✅ App ready!"

exec php-fpm -F