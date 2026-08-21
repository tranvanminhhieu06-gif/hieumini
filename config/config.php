<?php
/**
 * HieuMini — Cấu hình chung của ứng dụng
 * ---------------------------------------------------------------
 * Tệp này được nạp đầu tiên trong mọi trang. Nó chịu trách nhiệm:
 *   1) Thiết lập múi giờ, mã hóa và chế độ báo lỗi
 *   2) Khai báo các hằng số dùng chung (tên site, đường dẫn gốc…)
 *   3) Đọc biến môi trường ADMIN_PASSWORD để quyết định
 *      có kích hoạt phân hệ quản trị hay không
 */

declare(strict_types=1);

date_default_timezone_set('Asia/Ho_Chi_Minh');
mb_internal_encoding('UTF-8');

/* -----------------------------------------------------------------
 | Chế độ gỡ lỗi
 | Đặt APP_DEBUG=1 trong biến môi trường khi phát triển để xem lỗi.
 * ----------------------------------------------------------------- */
define('APP_DEBUG', env_flag('APP_DEBUG'));

if (APP_DEBUG) {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}

/* -----------------------------------------------------------------
 | Thông tin thương hiệu
 * ----------------------------------------------------------------- */
define('SITE_NAME',    'HieuMini');
define('SITE_TAGLINE', 'Bộ sưu tập website thương mại điện tử PHP & MySQL');
define('SITE_AUTHOR',  'Trần Văn Minh Hiếu');
define('SITE_EMAIL',   'tranvanminhhieu06@gmail.com');

/* -----------------------------------------------------------------
 | Tài khoản quản trị demo dùng chung cho cả 6 dự án con
 | Được hiển thị trên trang chi tiết để người xem tự đăng nhập.
 | Kịch bản database/tidb_all.sql đã đặt email và mật khẩu này cho
 | tài khoản quản trị của cả sáu dự án.
 * ----------------------------------------------------------------- */
define('DEMO_ADMIN_USER', env_value('DEMO_ADMIN_USER', 'admin@hieumini.vn'));
define('DEMO_ADMIN_PASS', env_value('DEMO_ADMIN_PASS', 'demo123'));

/* -----------------------------------------------------------------
 | Kết nối cơ sở dữ liệu
 | Có thể ghi đè bằng biến môi trường khi triển khai máy chủ thật.
 * ----------------------------------------------------------------- */
define('DB_HOST', env_value('DB_HOST', 'localhost'));
define('DB_PORT', env_value('DB_PORT', '3306'));
define('DB_USER', env_value('DB_USER', 'root'));
define('DB_PASS', env_value('DB_PASS', ''));
define('DB_CHARSET', 'utf8mb4');

/* Tên CSDL riêng của cổng trưng bày.
   Dùng DB_NAME_PORTAL để đồng bộ quy ước đặt tên với sáu dự án con
   (DB_NAME_WEB01 … DB_NAME_WEB06), nhờ đó cả bảy chỉ dùng chung
   một bộ DB_HOST / DB_USER / DB_PASS khi triển khai lên đám mây. */
define('DB_NAME', env_value('DB_NAME_PORTAL', env_value('DB_NAME', 'hieumini_portfolio')));

/* -----------------------------------------------------------------
 | Đường dẫn gốc của website (tự dò, không cần cấu hình tay)
 | Ví dụ khi đặt tại C:\xampp\htdocs\HieuWebsite  →  /HieuWebsite
 * ----------------------------------------------------------------- */
define('BASE_URL', detect_base_url());
define('ROOT_PATH', dirname(__DIR__));
define('PROJECTS_DIR', ROOT_PATH . DIRECTORY_SEPARATOR . 'projects');

/* -----------------------------------------------------------------
 | CỔNG BẢO VỆ TRANG QUẢN TRỊ
 | Trang admin MẶC ĐỊNH TẮT HOÀN TOÀN.
 | Chỉ khi máy chủ được khởi chạy kèm biến môi trường ADMIN_PASSWORD
 | (không rỗng) thì phân hệ quản trị mới tồn tại.
 * ----------------------------------------------------------------- */
define('ADMIN_PASSWORD', env_value('ADMIN_PASSWORD', ''));
define('ADMIN_ENABLED',  ADMIN_PASSWORD !== '');

/* -----------------------------------------------------------------
 | Phiên làm việc
 * ----------------------------------------------------------------- */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}

/* =================================================================
 | Các hàm hỗ trợ cấu hình (khai báo trước khi dùng nhờ hoisting)
 * ================================================================= */

/**
 * Đọc một biến môi trường từ mọi nguồn Apache / CLI / php-fpm có thể cung cấp.
 */
function env_value(string $key, string $default = ''): string
{
    $sources = [
        getenv($key),
        $_ENV[$key]    ?? false,
        $_SERVER[$key] ?? false,
    ];

    foreach ($sources as $value) {
        if (is_string($value) && trim($value) !== '') {
            return trim($value);
        }
    }

    return $default;
}

/**
 * Đọc biến môi trường dạng bật/tắt.
 */
function env_flag(string $key): bool
{
    $value = strtolower(env_value($key, '0'));
    return in_array($value, ['1', 'true', 'on', 'yes'], true);
}

/**
 * Dò đường dẫn gốc của website từ vị trí tệp hiện tại.
 */
function detect_base_url(): string
{
    $docRoot = str_replace('\\', '/', realpath($_SERVER['DOCUMENT_ROOT'] ?? '') ?: '');
    $appRoot = str_replace('\\', '/', dirname(__DIR__));

    if ($docRoot !== '' && str_starts_with($appRoot, $docRoot)) {
        $base = substr($appRoot, strlen($docRoot));
        return rtrim('/' . ltrim($base, '/'), '/');
    }

    return '';
}
