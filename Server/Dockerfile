# ============================================
# Laravel Production Dockerfile for Render
# Optimized for: PHP 8.3 + nginx + php-fpm
# ============================================

FROM php:8.3-fpm-alpine AS base

# Install system dependencies (minimal)
RUN apk add --no-cache \
    nginx \
    supervisor \
    ca-certificates \
    curl \
    icu-dev \
    libxml2-dev \
    oniguruma-dev \
    linux-headers

# Install essential PHP extensions only
# pdo_mysql: Database connection
# opcache: Performance (pre-compiled bytecode)
# intl: String handling (Laravel dependency)
RUN docker-php-ext-install \
    pdo_mysql \
    opcache \
    intl

# ============================================
# Builder Stage - Composer dependencies
# ============================================
FROM base AS builder

# Install Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Copy composer files first for optimal layer caching
COPY composer.json composer.lock ./

# Install production dependencies
RUN composer install \
    --no-dev \
    --no-interaction \
    --optimize-autoloader \
    --no-scripts \
    --prefer-dist \
    --no-progress \
    --ignore-platform-reqs

# ============================================
# Production Stage
# ============================================
FROM base AS production

WORKDIR /var/www/html

# Create non-root user for running the application
RUN addgroup -g 1000 -S www && \
    adduser -u 1000 -S www -G www

# Copy configs
COPY docker/php/php.ini-production /usr/local/etc/php/php.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/www.conf /usr/local/etc/php-fpm.d/www.conf
COPY docker/nginx/nginx.conf /etc/nginx/nginx.conf
COPY docker/nginx/site.conf /etc/nginx/conf.d/default.conf
COPY docker/supervisord.conf /etc/supervisor/conf.d/supervisord.conf

# Copy application (vendor excluded by .dockerignore)
COPY --chown=www:www . .

# Copy vendor from builder
COPY --chown=www:www --from=builder /var/www/html/vendor ./vendor

# Create required directories with proper permissions
RUN mkdir -p storage/framework/cache/data \
    storage/framework/sessions \
    storage/framework/views \
    storage/logs \
    bootstrap/cache \
    && chown -R www:www storage bootstrap/cache \
    && chmod -R 775 storage bootstrap/cache

# Pre-optimize Laravel at build time (zero runtime overhead)
RUN php artisan config:cache && \
    php artisan route:cache && \
    php artisan view:cache && \
    chown -R www:www bootstrap/cache storage/framework/views

# Copy and setup Aiven MySQL CA certificate for SSL connection
COPY docker/mysql/aiven-ca.pem /etc/ssl/certs/aiven-ca.pem
RUN cat /etc/ssl/certs/aiven-ca.pem >> /etc/ssl/certs/ca-certificates.crt

# Run database migrations (requires DB connection during build)
RUN php artisan migrate --force

EXPOSE 8080

# Health check for Render
HEALTHCHECK --interval=30s --timeout=5s --start-period=30s --retries=3 \
    CMD curl -f http://localhost:8080/health || exit 1

# Supervisor manages nginx + php-fpm (runs as root)
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisor/conf.d/supervisord.conf"]
