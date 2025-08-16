#!/bin/bash

echo "🚀 Deploying MMG POS to DigitalOcean App Platform..."

# Clear all caches
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Install dependencies
composer install --no-dev --optimize-autoloader
npm install
npm run build

# Optimize for production
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Run migrations
php artisan migrate --force

echo "✅ Application ready for deployment!"
echo "📝 Next steps:"
echo "1. Push to GitHub: git push origin main"
echo "2. DigitalOcean App Platform will auto-deploy"
echo "3. Access your app at the provided URL"
