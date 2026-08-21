# =====================================================================
#  HieuMini — Ảnh Docker triển khai lên Render
#  Một container phục vụ cả cổng trưng bày lẫn 6 dự án con,
#  nhờ vậy khung nhúng <iframe> dùng đường dẫn tương đối vẫn hoạt động.
# =====================================================================
FROM php:8.2-apache

# ---------- 1. Phần mở rộng PHP ----------
# pdo_mysql: bắt buộc để kết nối TiDB Cloud / MySQL
# gd, zip:   một số dự án con dùng để xử lý ảnh và tệp nén
RUN apt-get update && apt-get install -y --no-install-recommends \
        libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev \
        ca-certificates \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql gd zip \
    && update-ca-certificates \
    && apt-get purge -y --auto-remove libfreetype6-dev libjpeg62-turbo-dev libpng-dev libzip-dev \
    && rm -rf /var/lib/apt/lists/*

# ---------- 2. Cấu hình Apache ----------
# rewrite: cần cho .htaccess của HieuWeb05 và HieuWeb06
# headers: cho phép các dự án con điều chỉnh tiêu đề phản hồi
RUN a2enmod rewrite headers

# Cho phép tệp .htaccess trong mã nguồn có hiệu lực
RUN printf '%s\n' \
    '<Directory /var/www/html>' \
    '    Options -Indexes +FollowSymLinks' \
    '    AllowOverride All' \
    '    Require all granted' \
    '</Directory>' \
    > /etc/apache2/conf-available/hieumini.conf \
    && a2enconf hieumini

# Render cấp cổng qua biến môi trường PORT (mặc định 10000).
# Apache trong ảnh gốc nghe cổng 80, nên ta thay bằng biến của Render
# ngay lúc khởi động container.
RUN printf '%s\n' \
    '#!/bin/sh' \
    'set -e' \
    'PORT="${PORT:-80}"' \
    'sed -ri "s/^Listen .*/Listen ${PORT}/" /etc/apache2/ports.conf' \
    'sed -ri "s/:80>/:${PORT}>/" /etc/apache2/sites-available/000-default.conf' \
    'echo "ServerName localhost" > /etc/apache2/conf-available/servername.conf' \
    'a2enconf servername >/dev/null 2>&1 || true' \
    'exec apache2-foreground' \
    > /usr/local/bin/start.sh \
    && chmod +x /usr/local/bin/start.sh

# ---------- 3. Cấu hình PHP cho môi trường chạy thật ----------
RUN printf '%s\n' \
    'display_errors = Off' \
    'log_errors = On' \
    'error_log = /dev/stderr' \
    'expose_php = Off' \
    'upload_max_filesize = 8M' \
    'post_max_size = 10M' \
    'memory_limit = 256M' \
    'max_execution_time = 60' \
    'date.timezone = Asia/Ho_Chi_Minh' \
    'session.cookie_httponly = 1' \
    'session.cookie_samesite = Lax' \
    > /usr/local/etc/php/conf.d/hieumini.ini

# ---------- 4. Mã nguồn ----------
COPY . /var/www/html/

# Thư mục tải lên của HieuWeb06 cần quyền ghi
RUN mkdir -p /var/www/html/projects/HieuWeb06/uploads \
    && chown -R www-data:www-data /var/www/html \
    && find /var/www/html -type d -exec chmod 755 {} + \
    && find /var/www/html -type f -exec chmod 644 {} +

EXPOSE 10000
CMD ["/usr/local/bin/start.sh"]
