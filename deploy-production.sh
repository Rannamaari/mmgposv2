#!/bin/bash

echo "🚀 Starting Production Deployment Preparation..."

# Clear all caches
echo "📦 Clearing Laravel caches..."
php artisan config:clear
php artisan cache:clear
php artisan view:clear
php artisan route:clear

# Build assets
echo "🔨 Building frontend assets..."
npm install
npm run build

# Optimize for production
echo "⚡ Optimizing for production..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

# Test PostgreSQL connection
echo "🔍 Testing PostgreSQL connection..."
DB_CONNECTION=pgsql \
DB_HOST=micronetdb-do-user-24249606-0.d.db.ondigitalocean.com \
DB_PORT=25060 \
DB_DATABASE=mmgpos \
DB_USERNAME=doadmin \
DB_PASSWORD=${DB_PASSWORD} \
DB_SSLMODE=require \
php artisan tinker --execute="try { DB::connection()->getPdo(); echo '✅ PostgreSQL connection successful!'; } catch (Exception \$e) { echo '❌ PostgreSQL connection failed: ' . \$e->getMessage(); exit(1); }"

# Run migrations on production database
echo "🗄️ Running migrations on production database..."
DB_CONNECTION=pgsql \
DB_HOST=micronetdb-do-user-24249606-0.d.db.ondigitalocean.com \
DB_PORT=25060 \
DB_DATABASE=mmgpos \
DB_USERNAME=doadmin \
DB_PASSWORD=${DB_PASSWORD} \
DB_SSLMODE=require \
php artisan migrate --force

# Verify admin user exists
echo "👤 Verifying admin user..."
DB_CONNECTION=pgsql \
DB_HOST=micronetdb-do-user-24249606-0.d.db.ondigitalocean.com \
DB_PORT=25060 \
DB_DATABASE=mmgpos \
DB_USERNAME=doadmin \
DB_PASSWORD=${DB_PASSWORD} \
DB_SSLMODE=require \
php artisan tinker --execute="try { \$user = App\Models\User::where('email', 'admin@mmgpos.com')->first(); if(\$user && \$user->hasRole('admin')) { echo '✅ Admin user verified: ' . \$user->name; } else { echo '❌ Admin user not found or missing admin role'; exit(1); } } catch (Exception \$e) { echo '❌ Error verifying admin user: ' . \$e->getMessage(); exit(1); }"

echo "✅ Production deployment preparation completed!"
echo "🎯 Ready to deploy to DigitalOcean App Platform!"
echo "📋 Next steps:"
echo "   1. Commit and push your changes to GitHub"
echo "   2. DigitalOcean App Platform will automatically deploy"
echo "   3. Monitor the deployment logs"
echo "   4. Test the live application"
