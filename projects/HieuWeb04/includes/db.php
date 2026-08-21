<?php
/**
 * Database Connection using PDO
 * DatCyber Smart Home Appliances
 */

$host = getenv('DB_HOST') ?: '127.0.0.1';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME_WEB04') ?: 'datcyber_appliances_db';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASS') !== false ? getenv('DB_PASS') : '';
$charset = 'utf8mb4';

$dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=$charset";
$options = [
    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_EMULATE_PREPARES   => false,
    PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
];

// Auto-enable SSL for Cloud MySQL
if (getenv('DB_SSL') === 'true' || getenv('DB_SSL') === '1' || str_contains($host, 'tidbcloud.com') || str_contains($host, 'aivencloud.com') || $port === '4000' || $port === 4000) {
    $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
    $caFile = file_exists('/etc/ssl/certs/ca-certificates.crt') ? '/etc/ssl/certs/ca-certificates.crt' : (file_exists('C:/xampp/apache/bin/curl-ca-bundle.crt') ? 'C:/xampp/apache/bin/curl-ca-bundle.crt' : null);
    if ($caFile) {
        $options[PDO::MYSQL_ATTR_SSL_CA] = $caFile;
    }
}

try {
    $pdo = new PDO($dsn, $username, $password, $options);
} catch (PDOException $e) {
    $db_error = $e->getMessage();
}

// Start session if not started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
