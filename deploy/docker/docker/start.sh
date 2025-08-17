#!/bin/sh
set -e

echo "🚀 Starting MMG POS application..."

# Fix permissions
chown -R www-data:www-data /var/www/html
chmod -R 755 /var/www/html
chmod -R 777 /var/www/html/storage
chmod -R 777 /var/www/html/bootstrap/cache

# Ensure public directory is accessible
chmod -R 755 /var/www/html/public
chmod 644 /var/www/html/public/index.php

# Create required directories
mkdir -p /var/log/supervisor
mkdir -p /var/run
mkdir -p /var/log/nginx
mkdir -p /run/nginx

# Test nginx configuration
echo "🧪 Testing nginx configuration..."
nginx -t

# Test PHP-FPM
echo "🧪 Testing PHP-FPM..."
php-fpm8.2 -t

echo "✅ Starting PHP-FPM..."
php-fpm8.2 -D

echo "✅ Starting nginx..."
nginx

echo "✅ Services started, checking if port 8080 is listening..."
sleep 2
netstat -tlnp | grep :8080 || echo "⚠️ Port 8080 not listening"

echo "✅ Testing health endpoint..."
curl -f http://localhost:8080/health || echo "⚠️ Health check failed"

echo "✅ Application is ready!"

# Keep container running
tail -f /var/log/nginx/access.log /var/log/nginx/error.log