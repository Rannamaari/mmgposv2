#!/bin/bash
set -e

echo "🚀 Starting Laravel application initialization..."

# Wait for database to be ready (optional but recommended)
echo "⏳ Waiting for database connection..."
until php artisan tinker --execute="try { DB::connection()->getPdo(); echo 'Database connected!'; } catch (Exception \$e) { echo 'Database not ready yet...'; exit(1); }" 2>/dev/null; do
    echo "Database not ready yet, waiting 5 seconds..."
    sleep 5
done

echo "✅ Database connection established"

# Run Laravel optimization commands
echo "📦 Running Laravel optimization commands..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "✅ Laravel optimization completed"

# Run database migrations
echo "🗄️ Running database migrations..."
php artisan migrate --force --no-interaction

echo "✅ Database migrations completed"

# Start supervisor in foreground
echo "🎯 Starting supervisor..."
exec /usr/bin/supervisord -n
