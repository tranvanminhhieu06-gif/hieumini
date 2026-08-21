<?php
/**
 * HieuMini — Lớp kết nối cơ sở dữ liệu
 * ---------------------------------------------------------------
 * Áp dụng mẫu thiết kế Singleton: toàn ứng dụng chỉ mở đúng
 * một kết nối PDO tới MySQL, tránh lãng phí tài nguyên máy chủ.
 *
 * Mọi truy vấn trong dự án đều đi qua prepared statement nên
 * dữ liệu người dùng không bao giờ được nối chuỗi trực tiếp
 * vào câu lệnh SQL (chống SQL Injection).
 */

declare(strict_types=1);

final class Database
{
    private static ?PDO $connection = null;

    /** Ngăn khởi tạo đối tượng từ bên ngoài. */
    private function __construct() {}

    public static function connection(): PDO
    {
        if (self::$connection instanceof PDO) {
            return self::$connection;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=%s',
            DB_HOST,
            DB_PORT,
            DB_NAME,
            DB_CHARSET
        );

        $options = [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
            PDO::ATTR_STRINGIFY_FETCHES  => false,
        ];

        /* -------------------------------------------------------------
         | Tự bật TLS khi kết nối tới MySQL đám mây
         | TiDB Cloud, Aiven, PlanetScale… đều bắt buộc kết nối mã hóa.
         | Hệ thống tự nhận diện qua tên máy chủ hoặc cổng 4000, hoặc khi
         | biến môi trường DB_SSL được đặt.
         * ------------------------------------------------------------- */
        if (self::needsTls()) {
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
            $ca = self::findCaBundle();
            if ($ca !== null) {
                $options[PDO::MYSQL_ATTR_SSL_CA] = $ca;
            }
        }

        try {
            self::$connection = new PDO($dsn, DB_USER, DB_PASS, $options);
        } catch (PDOException $e) {
            self::fail($e);
        }

        return self::$connection;
    }

    /** Có cần kết nối mã hóa TLS hay không. */
    private static function needsTls(): bool
    {
        $flag = strtolower(env_value('DB_SSL', ''));
        if (in_array($flag, ['1', 'true', 'on', 'yes'], true)) {
            return true;
        }
        if (in_array($flag, ['0', 'false', 'off', 'no'], true)) {
            return false;
        }
        return str_contains(DB_HOST, 'tidbcloud.com')
            || str_contains(DB_HOST, 'aivencloud.com')
            || str_contains(DB_HOST, 'planetscale')
            || (string) DB_PORT === '4000';
    }

    /** Tìm kho chứng chỉ gốc của hệ thống, hỗ trợ cả Linux lẫn XAMPP trên Windows. */
    private static function findCaBundle(): ?string
    {
        $custom = env_value('DB_SSL_CA', '');
        $candidates = array_filter([
            $custom !== '' ? $custom : null,
            '/etc/ssl/certs/ca-certificates.crt',   // Debian, Ubuntu (ảnh Docker của Render)
            '/etc/pki/tls/certs/ca-bundle.crt',     // RHEL, CentOS, Fedora
            '/etc/ssl/cert.pem',                    // Alpine, macOS
            'C:/xampp/apache/bin/curl-ca-bundle.crt',
        ]);

        foreach ($candidates as $path) {
            if (is_file($path)) {
                return $path;
            }
        }
        return null;
    }

    /**
     * Truy vấn có tham số, trả về đối tượng PDOStatement.
     */
    public static function run(string $sql, array $params = []): PDOStatement
    {
        $statement = self::connection()->prepare($sql);
        $statement->execute($params);
        return $statement;
    }

    /** Lấy nhiều dòng. */
    public static function all(string $sql, array $params = []): array
    {
        return self::run($sql, $params)->fetchAll();
    }

    /** Lấy đúng một dòng, trả về null nếu không có. */
    public static function one(string $sql, array $params = []): ?array
    {
        $row = self::run($sql, $params)->fetch();
        return $row === false ? null : $row;
    }

    /** Lấy giá trị của cột đầu tiên ở dòng đầu tiên. */
    public static function scalar(string $sql, array $params = [])
    {
        return self::run($sql, $params)->fetchColumn();
    }

    /**
     * Hiển thị trang hướng dẫn khắc phục khi không kết nối được CSDL,
     * thay vì để PHP văng ra thông báo lỗi kỹ thuật khó hiểu.
     */
    private static function fail(PDOException $e): void
    {
        http_response_code(503);
        $detail = APP_DEBUG ? htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8') : '';

        echo '<!doctype html><html lang="vi"><head><meta charset="utf-8">'
           . '<meta name="viewport" content="width=device-width,initial-scale=1">'
           . '<title>Chưa kết nối được cơ sở dữ liệu — HieuMini</title>'
           . '<style>body{font-family:system-ui,Segoe UI,sans-serif;background:#F7F8FC;color:#0B1020;'
           . 'display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0;padding:24px}'
           . '.box{max-width:640px;background:#fff;border:1px solid #E6E9F2;border-radius:20px;padding:40px;'
           . 'box-shadow:0 24px 60px -30px rgba(11,16,32,.35)}h1{font-size:22px;margin:0 0 12px}'
           . 'ol{line-height:1.9;color:#55607A}code{background:#F1F3F9;padding:2px 7px;border-radius:6px;'
           . 'font-size:13px}.err{margin-top:18px;padding:12px 14px;background:#FEF2F2;color:#B91C1C;'
           . 'border-radius:10px;font-size:13px;word-break:break-word}</style></head><body><div class="box">'
           . '<h1>Chưa kết nối được cơ sở dữ liệu</h1>'
           . '<ol><li>Mở <b>XAMPP Control Panel</b> và bật hai dịch vụ <b>Apache</b> và <b>MySQL</b>.</li>'
           . '<li>Truy cập <code>http://localhost/phpmyadmin</code>, vào thẻ <b>Import</b> '
           . 'và nạp tệp <code>database/hieumini_portfolio.sql</code>.</li>'
           . '<li>Kiểm tra lại thông tin đăng nhập MySQL trong <code>config/config.php</code> '
           . '(mặc định XAMPP: user <code>root</code>, mật khẩu để trống).</li>'
           . '<li>Tải lại trang này.</li></ol>'
           . ($detail !== '' ? '<div class="err">' . $detail . '</div>' : '')
           . '</div></body></html>';
        exit;
    }
}
