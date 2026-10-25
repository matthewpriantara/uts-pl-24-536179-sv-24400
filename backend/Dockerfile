# Base Image FrankenPHP PHP 8.4 Alpine (Versi spesifik, bukan :latest)
FROM dunglas/frankenphp:1.4-php8.4-alpine AS runner

# Salin Composer binary resmi (versi pinned 2.8.9)
COPY --from=composer:2.8.9 /usr/bin/composer /usr/bin/composer

# Pasang ekstensi PHP yang dibutuhkan untuk MySQL, optimasi, dan Laravel
RUN install-php-extensions pdo_mysql bcmath pcntl opcache zip intl

WORKDIR /app

# Layer Caching: Salin dependensi composer terlebih dahulu
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-scripts --no-autoloader --prefer-dist

# Salin seluruh kode aplikasi backend
COPY . .

# Dump autoloader teroptimasi untuk production
RUN composer dump-autoload --optimize --no-dev

# Konfigurasi Caddyfile untuk FrankenPHP
COPY Caddyfile /etc/caddy/Caddyfile

# Atur perizinan folder storage dan cache Laravel
RUN chown -R www-data:www-data /app/storage /app/bootstrap/cache \
    && chmod -R 775 /app/storage /app/bootstrap/cache \
    && chmod +x /app/docker-entrypoint.sh

EXPOSE 8000

ENTRYPOINT ["/app/docker-entrypoint.sh"]
