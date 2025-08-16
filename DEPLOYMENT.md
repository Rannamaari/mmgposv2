# MMG POS System - Production Deployment Guide

## Overview
This guide covers deploying the MMG POS system to a production environment with proper security, performance, and monitoring configurations.

## Prerequisites

### System Requirements
- Ubuntu 20.04+ or CentOS 8+
- PHP 8.2+
- PostgreSQL 14+
- Redis 6+
- Nginx 1.18+
- Node.js 18+ (for asset compilation)
- Supervisor (for queue workers)

### PHP Extensions
```bash
sudo apt install php8.2-{cli,fpm,mysql,pgsql,redis,gd,xml,mbstring,curl,zip,intl,bcmath,soap}
```

## Step 1: Server Setup

### 1.1 Create Application User
```bash
sudo adduser mmgpos --disabled-password --gecos ""
sudo usermod -aG www-data mmgpos
```

### 1.2 PostgreSQL Setup
```bash
sudo -u postgres createuser mmgpos
sudo -u postgres createdb mmgpos_production --owner=mmgpos
sudo -u postgres psql -c "ALTER USER mmgpos PASSWORD 'your_secure_password';"
```

### 1.3 Redis Setup
```bash
sudo systemctl enable redis-server
sudo systemctl start redis-server
```

## Step 2: Application Deployment

### 2.1 Clone Repository
```bash
cd /var/www
sudo git clone https://github.com/Rannamaari/mmgpos.git
sudo chown -R mmgpos:www-data mmgpos
cd mmgpos
```

### 2.2 Install Dependencies
```bash
sudo -u mmgpos composer install --no-dev --optimize-autoloader
sudo -u mmgpos npm ci --production
sudo -u mmgpos npm run build
```

### 2.3 Environment Configuration
```bash
sudo -u mmgpos cp .env.production .env
sudo -u mmgpos php artisan key:generate
```

Edit `.env` with production values:
```bash
sudo -u mmgpos nano .env
```

### 2.4 Set Permissions
```bash
sudo chown -R mmgpos:www-data storage bootstrap/cache
sudo chmod -R 775 storage bootstrap/cache
```

### 2.5 Database Migration
```bash
sudo -u mmgpos php artisan migrate --force
sudo -u mmgpos php artisan db:seed --class=DemoSeeder
```

### 2.6 Storage Link
```bash
sudo -u mmgpos php artisan storage:link
```

## Step 3: Nginx Configuration

### 3.1 Create Nginx Site Config
```bash
sudo nano /etc/nginx/sites-available/mmgpos
```

```nginx
server {
    listen 80;
    listen [::]:80;
    server_name your-domain.com;
    return 301 https://$server_name$request_uri;
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name your-domain.com;
    root /var/www/mmgpos/public;

    # SSL Configuration
    ssl_certificate /etc/ssl/certs/your-domain.crt;
    ssl_certificate_key /etc/ssl/private/your-domain.key;
    ssl_protocols TLSv1.2 TLSv1.3;
    ssl_ciphers ECDHE-RSA-AES256-GCM-SHA512:DHE-RSA-AES256-GCM-SHA512:ECDHE-RSA-AES256-GCM-SHA384:DHE-RSA-AES256-GCM-SHA384;
    ssl_prefer_server_ciphers off;
    ssl_session_cache shared:SSL:10m;

    # Security Headers
    add_header X-Frame-Options "SAMEORIGIN" always;
    add_header X-Content-Type-Options "nosniff" always;
    add_header X-XSS-Protection "1; mode=block" always;
    add_header Referrer-Policy "no-referrer-when-downgrade" always;
    add_header Content-Security-Policy "default-src 'self' http: https: data: blob: 'unsafe-inline'" always;
    add_header Strict-Transport-Security "max-age=31536000; includeSubDomains" always;

    # Gzip Compression
    gzip on;
    gzip_vary on;
    gzip_min_length 1024;
    gzip_proxied expired no-cache no-store private must-revalidate;
    gzip_types text/plain text/css text/xml text/javascript application/javascript application/xml+rss application/json;

    index index.php;

    charset utf-8;

    # Handle Laravel routing
    location / {
        try_files $uri $uri/ /index.php?$query_string;
    }

    # PHP Processing
    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.2-fpm.sock;
        fastcgi_param SCRIPT_FILENAME $realpath_root$fastcgi_script_name;
        include fastcgi_params;
        fastcgi_hide_header X-Powered-By;
    }

    # Static Assets Caching
    location ~* \.(js|css|png|jpg|jpeg|gif|ico|svg|woff|woff2|ttf|eot)$ {
        expires 1y;
        add_header Cache-Control "public, immutable";
        access_log off;
    }

    # Security
    location ~ /\.(?!well-known).* {
        deny all;
    }

    # Rate Limiting
    location /admin {
        limit_req zone=admin burst=5 nodelay;
        try_files $uri $uri/ /index.php?$query_string;
    }
}
```

### 3.2 Enable Site
```bash
sudo ln -s /etc/nginx/sites-available/mmgpos /etc/nginx/sites-enabled/
sudo nginx -t
sudo systemctl reload nginx
```

## Step 4: PHP-FPM Configuration

### 4.1 Optimize PHP-FPM Pool
```bash
sudo nano /etc/php/8.2/fpm/pool.d/mmgpos.conf
```

```ini
[mmgpos]
user = mmgpos
group = www-data
listen = /var/run/php/php8.2-fpm-mmgpos.sock
listen.owner = www-data
listen.group = www-data
listen.mode = 0660

pm = dynamic
pm.max_children = 20
pm.start_servers = 4
pm.min_spare_servers = 2
pm.max_spare_servers = 6
pm.max_requests = 1000

php_admin_value[error_log] = /var/log/php8.2-fpm-mmgpos.log
php_admin_flag[log_errors] = on
```

Update Nginx config to use this pool:
```nginx
fastcgi_pass unix:/var/run/php/php8.2-fpm-mmgpos.sock;
```

## Step 5: Supervisor Configuration

### 5.1 Install Supervisor
```bash
sudo apt install supervisor
```

### 5.2 Configure Queue Workers
```bash
sudo cp supervisor.conf /etc/supervisor/conf.d/mmgpos.conf
```

Update paths in the config:
```bash
sudo sed -i 's|/path/to/mmgpos|/var/www/mmgpos|g' /etc/supervisor/conf.d/mmgpos.conf
```

### 5.3 Start Workers
```bash
sudo supervisorctl reread
sudo supervisorctl update
sudo supervisorctl start mmgpos:*
```

## Step 6: Cron Jobs

### 6.1 Laravel Scheduler
```bash
sudo -u mmgpos crontab -e
```

Add:
```cron
* * * * * cd /var/www/mmgpos && php artisan schedule:run >> /dev/null 2>&1
```

## Step 7: SSL Certificate

### 7.1 Let's Encrypt (Recommended)
```bash
sudo apt install certbot python3-certbot-nginx
sudo certbot --nginx -d your-domain.com
```

### 7.2 Auto-renewal
```bash
sudo crontab -e
```

Add:
```cron
0 12 * * * /usr/bin/certbot renew --quiet
```

## Step 8: Monitoring & Logging

### 8.1 Log Rotation
```bash
sudo nano /etc/logrotate.d/mmgpos
```

```logrotate
/var/www/mmgpos/storage/logs/*.log {
    daily
    missingok
    rotate 14
    compress
    delaycompress
    notifempty
    create 644 mmgpos www-data
    postrotate
        sudo systemctl reload php8.2-fpm
    endscript
}
```

### 8.2 System Health Monitoring
Add to crontab:
```bash
0 */6 * * * cd /var/www/mmgpos && php artisan system:health-check --notify
```

## Step 9: Backup Configuration

### 9.1 Install Backup Package
```bash
sudo -u mmgpos composer require spatie/laravel-backup
```

### 9.2 Configure Backup Storage
Create backup directory:
```bash
sudo mkdir -p /var/backups/mmgpos
sudo chown mmgpos:www-data /var/backups/mmgpos
sudo chmod 775 /var/backups/mmgpos
```

### 9.3 Test Backup
```bash
sudo -u mmgpos php artisan backup:run
```

## Step 10: Firewall Configuration

### 10.1 UFW Setup
```bash
sudo ufw allow ssh
sudo ufw allow 'Nginx Full'
sudo ufw --force enable
```

### 10.2 Fail2Ban
```bash
sudo apt install fail2ban
sudo nano /etc/fail2ban/jail.local
```

```ini
[DEFAULT]
bantime = 3600
findtime = 600
maxretry = 5

[nginx-http-auth]
enabled = true

[nginx-noscript]
enabled = true

[nginx-badbots]
enabled = true

[nginx-noproxy]
enabled = true
```

## Step 11: Performance Optimization

### 11.1 Cache Configuration
```bash
sudo -u mmgpos php artisan config:cache
sudo -u mmgpos php artisan route:cache
sudo -u mmgpos php artisan view:cache
```

### 11.2 OPcache Configuration
```bash
sudo nano /etc/php/8.2/fpm/conf.d/10-opcache.ini
```

```ini
opcache.enable=1
opcache.memory_consumption=128
opcache.interned_strings_buffer=8
opcache.max_accelerated_files=4000
opcache.revalidate_freq=60
opcache.fast_shutdown=1
```

## Step 12: Final Security Checks

### 12.1 Remove Default Files
```bash
sudo rm /var/www/html/index.nginx-debian.html
```

### 12.2 Verify Permissions
```bash
find /var/www/mmgpos -type f -exec chmod 644 {} \;
find /var/www/mmgpos -type d -exec chmod 755 {} \;
chmod -R 775 /var/www/mmgpos/storage
chmod -R 775 /var/www/mmgpos/bootstrap/cache
```

### 12.3 Test Application
```bash
curl -I https://your-domain.com
curl -I https://your-domain.com/admin
```

## Maintenance Commands

### Daily Operations
```bash
# Health Check
sudo -u mmgpos php artisan system:health-check

# Manual Backup
sudo -u mmgpos php artisan db:backup

# System Maintenance
sudo -u mmgpos php artisan system:maintenance

# Clear Cache
sudo -u mmgpos php artisan cache:clear
```

### Update Deployment
```bash
cd /var/www/mmgpos
sudo -u mmgpos git pull
sudo -u mmgpos composer install --no-dev --optimize-autoloader
sudo -u mmgpos npm ci --production
sudo -u mmgpos npm run build
sudo -u mmgpos php artisan migrate --force
sudo -u mmgpos php artisan config:cache
sudo -u mmgpos php artisan route:cache
sudo -u mmgpos php artisan view:cache
sudo supervisorctl restart mmgpos:*
```

## Troubleshooting

### Common Issues

1. **Permission Errors**
   ```bash
   sudo chown -R mmgpos:www-data /var/www/mmgpos
   sudo chmod -R 775 storage bootstrap/cache
   ```

2. **Queue Worker Issues**
   ```bash
   sudo supervisorctl status mmgpos:*
   sudo supervisorctl restart mmgpos:*
   ```

3. **Database Connection**
   ```bash
   sudo -u mmgpos php artisan tinker
   >>> DB::connection()->getPdo();
   ```

4. **Redis Connection**
   ```bash
   redis-cli ping
   ```

### Log Locations
- Application: `/var/www/mmgpos/storage/logs/`
- Nginx: `/var/log/nginx/`
- PHP-FPM: `/var/log/php8.2-fpm-mmgpos.log`
- Supervisor: `/var/log/supervisor/`

## Security Considerations

1. **Regular Updates**: Keep system packages, PHP, and dependencies updated
2. **Strong Passwords**: Use complex passwords for database and application
3. **Backup Encryption**: Consider encrypting backups for sensitive data
4. **Access Control**: Limit SSH access and use key-based authentication
5. **Monitoring**: Set up alerts for failed logins and system issues

## Support

For technical support or deployment assistance, check the project repository:
https://github.com/Rannamaari/mmgpos

This completes the production deployment of the MMG POS system with enterprise-grade security, performance, and monitoring capabilities.