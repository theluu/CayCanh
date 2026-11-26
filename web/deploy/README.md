# Triển khai website Cây Cảnh lên VPS (Ubuntu)

Một VPS chạy tất cả: Nginx + PHP 8.4-FPM + MySQL 8 + Redis + Node (cho SSR) + Supervisor.
Khuyến nghị tối thiểu: 2 vCPU, 4 GB RAM, 40 GB SSD.

## 1. Cài phần mềm (Ubuntu 24.04)

```bash
sudo apt update && sudo apt upgrade -y
sudo add-apt-repository ppa:ondrej/php -y && sudo apt update
sudo apt install -y nginx mysql-server redis-server supervisor git unzip certbot python3-certbot-nginx \
  php8.4-fpm php8.4-cli php8.4-mysql php8.4-redis php8.4-mbstring php8.4-xml php8.4-curl php8.4-zip \
  php8.4-gd php8.4-intl php8.4-bcmath php8.4-exif
curl -fsSL https://deb.nodesource.com/setup_22.x | sudo -E bash - && sudo apt install -y nodejs
curl -sS https://getcomposer.org/installer | php && sudo mv composer.phar /usr/local/bin/composer
```

`mysqldump` (đi kèm mysql-server) là bắt buộc cho sao lưu database.

## 2. Database

```sql
CREATE DATABASE caycanh CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
CREATE USER 'caycanh'@'localhost' IDENTIFIED BY 'MAT_KHAU_MANH';
GRANT ALL PRIVILEGES ON caycanh.* TO 'caycanh'@'localhost';
FLUSH PRIVILEGES;
```

## 3. Lấy mã nguồn và cấu hình

```bash
sudo mkdir -p /var/www && cd /var/www
sudo git clone <URL_REPO> caycanh && sudo chown -R $USER:www-data caycanh
cd caycanh/web
cp .env.example .env
composer install --no-dev --optimize-autoloader
php artisan key:generate
```

Sửa `.env` – các biến **bắt buộc**:

| Biến | Ý nghĩa |
|---|---|
| `APP_ENV=production`, `APP_DEBUG=false`, `APP_URL=https://caycanh.vn` | Môi trường chạy thật |
| `DB_DATABASE`, `DB_USERNAME`, `DB_PASSWORD` | Database ở bước 2 |
| `CACHE_STORE=redis`, `SESSION_DRIVER=redis`, `QUEUE_CONNECTION=redis` | Redis cho cache, phiên, hàng đợi |
| `MAIL_MAILER=smtp`, `MAIL_HOST`, `MAIL_PORT`, `MAIL_USERNAME`, `MAIL_PASSWORD`, `MAIL_FROM_ADDRESS` | Gửi email thông báo |
| `ADMIN_EMAIL`, `ADMIN_PASSWORD` | Tài khoản quản trị đầu tiên (đổi mật khẩu sau khi đăng nhập) |

Biến **tuỳ chọn**:

| Biến | Ý nghĩa |
|---|---|
| `TELEGRAM_BOT_TOKEN` | Token bot lấy từ @BotFather. Thêm bot vào nhóm kinh doanh, rồi nhập chat ID của nhóm trong Admin → Cài đặt chung |
| `ZALO_OA_ACCESS_TOKEN`, `ZALO_OA_RECIPIENTS` | Chỉ khi có Zalo OA đã xác thực; danh sách user_id (cách nhau dấu phẩy) của nhân viên đã quan tâm OA |
| `BACKUP_DISKS=local,backup_remote`, `BACKUP_S3_*` | Sao lưu thêm ra kho S3-compatible (Cloudflare R2, Backblaze B2, AWS S3…) |
| `BACKUP_NOTIFY_EMAIL` | Email nhận cảnh báo khi sao lưu lỗi |
| `SETTINGS_CACHE_ENABLED=true` | Cache bảng cài đặt |

Sau đó:

```bash
php artisan migrate --force
php artisan db:seed --force            # tạo 4 vai trò + tài khoản admin từ ADMIN_EMAIL
npm ci && npm run build
php artisan storage:link
php artisan optimize && php artisan filament:optimize
sudo chown -R www-data:www-data storage bootstrap/cache
```

## 4. Nginx + SSL

```bash
sudo cp deploy/nginx.conf /etc/nginx/sites-available/caycanh.conf   # sửa tên miền trong file
sudo ln -s /etc/nginx/sites-available/caycanh.conf /etc/nginx/sites-enabled/
sudo nginx -t && sudo systemctl reload nginx
sudo certbot --nginx -d caycanh.vn -d www.caycanh.vn
```

## 5. Supervisor (queue + SSR)

```bash
sudo cp deploy/supervisor/*.conf /etc/supervisor/conf.d/
sudo supervisorctl reread && sudo supervisorctl update
sudo supervisorctl status
```

## 6. Cron

```bash
sudo crontab -u www-data -e
# thêm dòng:
* * * * * cd /var/www/caycanh/web && php artisan schedule:run >> /dev/null 2>&1
```

Lịch đã cấu hình sẵn (`php artisan schedule:list`):

| Giờ | Việc |
|---|---|
| 01:00 hằng ngày | Sinh lịch chăm sóc 60 ngày tới |
| 02:00 hằng ngày | Sao lưu database |
| 02:30 hằng ngày | Tạo sitemap.xml, robots.txt |
| 03:00 Chủ nhật | Sao lưu database + thư mục ảnh (storage/app/public) |
| 04:00 / 04:30 | Dọn bản sao lưu cũ (giữ 7 ngày, 4 tuần, 3 tháng) / kiểm tra sức khoẻ sao lưu |
| 06:30 hằng ngày | Email nhắc kỹ thuật viên lịch chăm sóc trong ngày |

Bản sao lưu nằm trong `storage/app/private/<APP_NAME>` (và kho S3 nếu bật).

## 7. Các lần cập nhật sau

```bash
cd /var/www/caycanh/web && ./deploy/deploy.sh
```

## 8. Kiểm tra sau triển khai

- Mở trang chủ, xem mã nguồn trang (Ctrl+U) phải thấy nội dung HTML đầy đủ (SSR) và thẻ `application/ld+json`.
- Gửi thử form Liên hệ → nhóm Telegram và email nhận được tin trong vài giây.
- Đặt thử một đơn hàng → kiểm tra Admin → Đơn hàng.
- `php artisan backup:run --only-db` chạy thành công.
