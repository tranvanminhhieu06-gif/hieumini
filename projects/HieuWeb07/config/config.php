<?php
/**
 * HieuMini Books — Cấu hình chung
 *
 * Nạp đầu tiên ở mọi trang. Định nghĩa hằng số toàn cục, mở phiên làm việc
 * và bật/tắt hiển thị lỗi tuỳ theo môi trường.
 */

declare(strict_types=1);

// ---------- Thông tin website ----------
const SITE_NAME    = 'HieuMini Books';
const SITE_TAGLINE = 'Thư viện sách trực tuyến';
const SITE_DESC    = 'Thư viện sách trực tuyến HieuMini — tra cứu 30 đầu sách văn học, khoa học, lịch sử và thiếu nhi theo thể loại, tác giả, kèm đánh giá của bạn đọc.';
const SITE_AUTHOR  = 'Trần Văn Minh Hiếu';
const SITE_EMAIL   = 'tranvanminhhieu06@gmail.com';

// ---------- Kết nối cơ sở dữ liệu ----------
// Sửa 4 dòng dưới cho khớp máy chủ của bạn. Mặc định là thiết lập XAMPP.
const DB_HOST = '127.0.0.1';
const DB_NAME = 'hieumini_books_db';
const DB_USER = 'root';
const DB_PASS = '';

// ---------- Môi trường ----------
// Đặt false khi đưa lên máy chủ thật: khi đó lỗi được ghi log thay vì in ra
// màn hình, tránh lộ đường dẫn và thông tin kết nối cho người ngoài.
const IS_DEV = true;

// ---------- Tham số hiển thị ----------
const BOOKS_PER_PAGE = 12;

// ---------- Đường dẫn ----------
define('ROOT_PATH', dirname(__DIR__));

/**
 * Tự dò thư mục gốc của website để mã nguồn chạy được cả ở
 * http://localhost/HieuWebsite/projects/HieuWeb07/ lẫn ở tên miền riêng.
 */
function base_url(): string
{
    static $base = null;
    if ($base !== null) {
        return $base;
    }
    $dir = str_replace('\\', '/', dirname($_SERVER['SCRIPT_NAME'] ?? '/'));
    // Trang trong thư mục admin/ hoặc api/ vẫn phải trỏ về gốc dự án
    $dir = preg_replace('#/(admin|api)$#', '', $dir);
    $base = rtrim($dir, '/');
    return $base;
}

// ---------- Xử lý lỗi ----------
if (IS_DEV) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL);
    ini_set('display_errors', '0');
    ini_set('log_errors', '1');
}

// ---------- Phiên làm việc ----------
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,                                    // JS không đọc được cookie phiên
        'samesite' => 'Lax',                                   // chặn gửi cookie khi bị nhúng từ site khác
        'secure'   => !empty($_SERVER['HTTPS']),
    ]);
    session_start();
}

date_default_timezone_set('Asia/Ho_Chi_Minh');
mb_internal_encoding('UTF-8');
