FROM php:8.3-fpm

# Install system packages and PHP extensions
RUN apt-get update && apt-get install -y \
    nginx \
    unzip \
    zip \
    git \
    curl \
    libzip-dev \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libicu-dev \
    && docker-php-ext-install \
        pdo \
        pdo_mysql \
        mbstring \
        bcmath \
        exif \
        zip \
    && rm -rf /var/lib/apt/lists/*

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy application
COPY . .

ENV COMPOSER_ALLOW_SUPERUSER=1

RUN composer install \
    --no-dev \
    --optimize-autoloader \
    --no-interaction

# Laravel required directories
RUN mkdir -p \
    storage/framework/cache \
    storage/framework/sessions \
    storage/framework/views \
    bootstrap/cache

# Permissions
RUN chown -R www-data:www-data storage bootstrap/cache
RUN chmod -R 775 storage bootstrap/cache
# Configure PHP-FPM to use a unix socket (avoid TCP port conflicts)
RUN mkdir -p /run/php && chown www-data:www-data /run/php
RUN if grep -q '^\s*listen\s*=.*$' /usr/local/etc/php-fpm.d/www.conf; then \
        sed -ri 's!^\s*listen\s*=.*$!listen = /run/php/php-fpm.sock!' /usr/local/etc/php-fpm.d/www.conf; \
    else \
        echo 'listen = /run/php/php-fpm.sock' >> /usr/local/etc/php-fpm.d/www.conf; \
    fi && \
    if grep -q '^\s*listen\.owner\s*=.*$' /usr/local/etc/php-fpm.d/www.conf; then \
        sed -ri 's!^\s*listen\.owner\s*=.*$!listen.owner = www-data!' /usr/local/etc/php-fpm.d/www.conf; \
    else \
        echo 'listen.owner = www-data' >> /usr/local/etc/php-fpm.d/www.conf; \
    fi && \
    if grep -q '^\s*listen\.group\s*=.*$' /usr/local/etc/php-fpm.d/www.conf; then \
        sed -ri 's!^\s*listen\.group\s*=.*$!listen.group = www-data!' /usr/local/etc/php-fpm.d/www.conf; \
    else \
        echo 'listen.group = www-data' >> /usr/local/etc/php-fpm.d/www.conf; \
    fi && \
    if grep -q '^\s*listen\.mode\s*=.*$' /usr/local/etc/php-fpm.d/www.conf; then \
        sed -ri 's!^\s*listen\.mode\s*=.*$!listen.mode = 0660!' /usr/local/etc/php-fpm.d/www.conf; \
    else \
        echo 'listen.mode = 0660' >> /usr/local/etc/php-fpm.d/www.conf; \
    fi

# Configure nginx for Laravel
RUN mkdir -p /etc/nginx/sites-enabled
COPY docker/nginx.conf /etc/nginx/sites-enabled/default

EXPOSE 9000

# Start php-fpm in foreground and nginx
CMD ["sh", "-c", "php-fpm -F & nginx -g 'daemon off;'" ]
