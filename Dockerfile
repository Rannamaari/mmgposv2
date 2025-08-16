FROM php:8.2-fpm

# Install system dependencies
RUN apt-get update && apt-get install -y \
  git \
  curl \
  libpng-dev \
  libonig-dev \
  libxml2-dev \
  libpq-dev \
  libzip-dev \
  libicu-dev \
  libsodium-dev \
  zip \
  unzip \
  nodejs \
  npm \
  nginx \
  supervisor \
  pkg-config \
  ca-certificates

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

# Install PHP extensions (same as local)
RUN docker-php-ext-configure zip && \
  docker-php-ext-configure intl && \
  docker-php-ext-install pdo pdo_pgsql mbstring exif pcntl bcmath gd zip intl sodium

# Get latest Composer
COPY --from=composer:latest /usr/bin/composer /usr/bin/composer

# Set working directory
WORKDIR /var/www

# Copy application files
COPY . /var/www

# Set ownership
RUN chown -R www-data:www-data /var/www

# Install PHP dependencies (same as local)
RUN composer install --no-dev --optimize-autoloader

# Install Node.js dependencies and build assets (same as local)
RUN npm install
RUN npm run build

# Create necessary directories
RUN mkdir -p /var/log/nginx /var/log/supervisor
RUN mkdir -p /var/www/storage/logs /var/www/storage/framework/{cache,sessions,views}
RUN mkdir -p /var/www/bootstrap/cache

# Set permissions (same as local)
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache
RUN chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy nginx configuration (tested locally)
COPY docker/nginx.conf /etc/nginx/sites-available/default

# Copy supervisor configuration
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Expose port 80
EXPOSE 80

# Start supervisor
CMD ["/usr/bin/supervisord"]