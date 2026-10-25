#!/bin/sh
set -e

echo "==> Menunggu koneksi basis data MySQL..."
until php -r "
try {
    \$host = getenv('DB_HOST') ?: 'db';
    \$port = getenv('DB_PORT') ?: 3306;
    \$db   = getenv('DB_DATABASE') ?: 'kepl_inventaris';
    \$user = getenv('DB_USERNAME') ?: 'kepl_user';
    \$pass = getenv('DB_PASSWORD') ?: 'kepl_secret_password';
    new PDO(\"mysql:host={\$host};port={\$port};dbname={\$db}\", \$user, \$pass);
    exit(0);
} catch (Exception \$e) {
    exit(1);
}"; do
    echo "Database belum siap, menunggu 2 detik..."
    sleep 2
done

echo "==> Basis data siap! Menjalankan migrasi dan seeder..."
php artisan migrate --force
php artisan db:seed --force

echo "==> Mengoptimalkan cache konfigurasi dan rute..."
php artisan config:cache
php artisan route:cache

echo "==> Memulai FrankenPHP Application Server pada port 8000..."
exec frankenphp run --config /etc/caddy/Caddyfile
