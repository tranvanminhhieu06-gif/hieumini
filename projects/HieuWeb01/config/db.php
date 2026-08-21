<?php
/**
 * Cấu hình kết nối CSDL và hằng số hệ thống HieuMini
 */
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME_WEB01') ?: 'hieumini_db');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

if (!defined('SITE_NAME')) define('SITE_NAME', 'HieuMini - Fashion Studio');
if (!defined('SITE_URL')) define('SITE_URL', 'http://localhost/HieuWeb01');
if (!defined('CURRENCY_SYMBOL')) define('CURRENCY_SYMBOL', '₫');

try {
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
        PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
    ];

    // Auto-enable SSL for Cloud MySQL providers (TiDB, Aiven, etc.)
    if (getenv('DB_SSL') === 'true' || getenv('DB_SSL') === '1' || str_contains(DB_HOST, 'tidbcloud.com') || str_contains(DB_HOST, 'aivencloud.com') || DB_PORT === '4000' || DB_PORT === 4000) {
        $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        $caFile = file_exists('/etc/ssl/certs/ca-certificates.crt') ? '/etc/ssl/certs/ca-certificates.crt' : (file_exists('C:/xampp/apache/bin/curl-ca-bundle.crt') ? 'C:/xampp/apache/bin/curl-ca-bundle.crt' : null);
        if ($caFile) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $caFile;
        }
    }

    $pdo = new PDO("mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET, DB_USER, DB_PASS, $options);
} catch (PDOException $e) {
    // Fallback PDO placeholder if DB is temporarily not ready
    $db_error = $e->getMessage();
}

// Khởi tạo giỏ hàng nếu chưa có
if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}
