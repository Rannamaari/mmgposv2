#!/bin/bash
set -e

echo "Starting post-build deployment..."

# Wait for database connection
echo "Waiting for database connection..."
until php artisan tinker --execute="DB::connection()->getPdo();" > /dev/null 2>&1; do
  echo "Database not ready, waiting..."
  sleep 2
done

echo "Database connected! Running migrations..."
php artisan migrate --force

echo "Seeding database..."
php artisan db:seed --class=DemoSeeder --force

echo "Creating storage link..."
php artisan storage:link

echo "Caching configuration..."
php artisan config:cache
php artisan route:cache
php artisan view:cache

echo "Fixing nginx configuration..."
# Fix nginx configuration for Laravel routing
if [ -f "/workspace/vendor/heroku/heroku-buildpack-php/conf/nginx/default_include.conf" ]; then
    cat > /workspace/vendor/heroku/heroku-buildpack-php/conf/nginx/default_include.conf << 'EOF'
location / {
    try_files $uri $uri/ /index.php?$query_string;
}

location ~ \.php$ {
    try_files @heroku-fcgi @heroku-fcgi;
}

location ~ ^/(composer\.(json|lock|phar)$|Procfile$|vendor/bin/) {
    deny all;
}
EOF
    echo "✅ Nginx configuration updated for Laravel routing"
else
    echo "⚠️  Nginx config file not found, will be fixed after deployment"
fi

echo "Deployment complete!"