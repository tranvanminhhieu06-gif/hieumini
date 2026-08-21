<?php
/**
 * ==========================================================
 * HIEUMINI TECH STORE - CẤU HÌNH KẾT NỐI CƠ SỞ DỮ LIỆU (PDO)
 * ==========================================================
 */

if (!defined('DB_HOST')) define('DB_HOST', getenv('DB_HOST') ?: '127.0.0.1');
if (!defined('DB_PORT')) define('DB_PORT', getenv('DB_PORT') ?: '3306');
if (!defined('DB_NAME')) define('DB_NAME', getenv('DB_NAME_WEB02') ?: 'hieumini_bookstore_db');
if (!defined('DB_USER')) define('DB_USER', getenv('DB_USER') ?: 'root');
if (!defined('DB_PASS')) define('DB_PASS', getenv('DB_PASS') !== false ? getenv('DB_PASS') : '');
if (!defined('DB_CHARSET')) define('DB_CHARSET', 'utf8mb4');

// BASE_URL: root-relative (tương thích localhost & Render HTTPS)
$__docRoot = str_replace('\\', '/', rtrim((string)($_SERVER['DOCUMENT_ROOT'] ?? ''), '/'));
$__projectDir = str_replace('\\', '/', dirname(__DIR__)); // config/ -> bước lên -> project root
$__basePath = ($__docRoot !== '' && str_starts_with($__projectDir, $__docRoot))
    ? substr($__projectDir, strlen($__docRoot))
    : '';
if (!defined('BASE_URL')) define('BASE_URL', rtrim($__basePath, '/'));
unset($__docRoot, $__projectDir, $__basePath);
if (!defined('SITE_NAME')) define('SITE_NAME', 'HieuMini - Siêu Thị Công Nghệ Đỉnh Cao');

class Database {
    private static $instance = null;
    private $conn;

    private function __construct() {
        $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES " . DB_CHARSET
        ];

        // Auto-enable SSL for Cloud MySQL
        if (getenv('DB_SSL') === 'true' || getenv('DB_SSL') === '1' || str_contains(DB_HOST, 'tidbcloud.com') || str_contains(DB_HOST, 'aivencloud.com') || DB_PORT === '4000' || DB_PORT === 4000) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            $caFile = file_exists('/etc/ssl/certs/ca-certificates.crt') ? '/etc/ssl/certs/ca-certificates.crt' : (file_exists('C:/xampp/apache/bin/curl-ca-bundle.crt') ? 'C:/xampp/apache/bin/curl-ca-bundle.crt' : null);
            if ($caFile) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $caFile;
            }
        }

        try {
            $this->conn = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            $this->conn = null;
            $this->error = $e->getMessage();
        }
    }

    public static function getInstance() {
        if (self::$instance == null) {
            self::$instance = new Database();
        }
        return self::$instance;
    }

    public function getConnection() {
        return $this->conn;
    }

    public function getError() {
        return isset($this->error) ? $this->error : null;
    }
}

// Khởi tạo biến $pdo toàn cục
$db = Database::getInstance();
$pdo = $db->getConnection();
