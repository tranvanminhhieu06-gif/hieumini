<?php
// config/db.php - Database connection with PDO and auto-setup fallback
$db_host = getenv('DB_HOST') ?: '127.0.0.1';
$db_port = getenv('DB_PORT') ?: '3306';
$db_user = getenv('DB_USER') ?: 'root';
$db_pass = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$db_name = getenv('DB_NAME_WEB03') ?: 'hieumini_furniture_db';

$options = [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

// Auto-enable SSL for Cloud MySQL
if (getenv('DB_SSL') === 'true' || getenv('DB_SSL') === '1' || str_contains($db_host, 'tidbcloud.com') || str_contains($db_host, 'aivencloud.com') || $db_port === '4000' || $db_port === 4000) {
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    $caFile = file_exists('/etc/ssl/certs/ca-certificates.crt') ? '/etc/ssl/certs/ca-certificates.crt' : (file_exists('C:/xampp/apache/bin/curl-ca-bundle.crt') ? 'C:/xampp/apache/bin/curl-ca-bundle.crt' : null);
    if ($caFile) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = $caFile;
    }
}

try {
    // Connect to database with UTF-8mb4 charset and init command
    $pdo = new PDO("mysql:host={$db_host};port={$db_port};dbname={$db_name};charset=utf8mb4", $db_user, $db_pass, $options);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}
