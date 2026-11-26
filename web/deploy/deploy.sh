#!/usr/bin/env bash
# Chạy trên VPS, trong thư mục /var/www/caycanh, bằng user deploy (có quyền sudo supervisorctl).
set -euo pipefail

APP_DIR="$(cd "$(dirname "${BASH_SOURCE[0]}")/.." && pwd)"
cd "$APP_DIR"

echo "→ Bật chế độ bảo trì"
php artisan down --retry=30 || true

echo "→ Cập nhật mã nguồn"
git -C "$APP_DIR/.." pull --ff-only

echo "→ Cài thư viện PHP"
composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

echo "→ Build giao diện (client + SSR)"
npm ci --no-audit --no-fund
npm run build

echo "→ Migrate database"
php artisan migrate --force

echo "→ Cache cấu hình, route, view, Filament"
php artisan optimize
php artisan filament:optimize
php artisan storage:link || true

echo "→ Khởi động lại queue và SSR"
php artisan queue:restart
sudo supervisorctl restart caycanh-ssr

echo "→ Tạo sitemap"
php artisan sitemap:generate

php artisan up
echo "✓ Triển khai xong"
