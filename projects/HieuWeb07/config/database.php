<?php
/**
 * HieuMini Books — Kết nối cơ sở dữ liệu
 *
 * Dùng PDO thay cho mysqli vì PDO cho phép viết câu lệnh có tham số
 * (prepared statement) gọn hơn và đổi hệ quản trị sau này dễ hơn.
 */

declare(strict_types=1);

/**
 * Trả về kết nối PDO dùng chung cho cả phiên chạy.
 * Mở một lần rồi tái sử dụng — mỗi lần mở kết nối mới đều tốn thời gian.
 */
function db(): PDO
{
    static $pdo = null;
    if ($pdo instanceof PDO) {
        return $pdo;
    }

    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=utf8mb4', DB_HOST, DB_NAME);

    try {
        $pdo = new PDO($dsn, DB_USER, DB_PASS, [
            // Lỗi ném ra dưới dạng ngoại lệ, không im lặng trả về false
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            // Mặc định lấy mảng kết hợp, khỏi phải chỉ định ở từng lệnh fetch
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            // Dùng prepared statement thật của MySQL thay vì bản giả lập của PDO,
            // nhờ vậy tham số được máy chủ tách hẳn khỏi câu lệnh -> chặn SQL Injection
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    } catch (PDOException $e) {
        http_response_code(503);
        if (IS_DEV) {
            exit('<h1>Không kết nối được cơ sở dữ liệu</h1><pre>'
                . htmlspecialchars($e->getMessage(), ENT_QUOTES, 'UTF-8')
                . '</pre><p>Kiểm tra lại MySQL đã bật chưa và thông tin trong <code>config/config.php</code>.</p>');
        }
        error_log('DB connect failed: ' . $e->getMessage());
        exit('Hệ thống đang bảo trì, vui lòng quay lại sau.');
    }

    return $pdo;
}

/**
 * Chạy một câu truy vấn có tham số và trả về đối tượng kết quả.
 * Mọi giá trị do người dùng nhập đều phải đi qua $params, tuyệt đối
 * không nối thẳng vào chuỗi SQL.
 */
function db_query(string $sql, array $params = []): PDOStatement
{
    $stmt = db()->prepare($sql);
    $stmt->execute($params);
    return $stmt;
}

/** Lấy nhiều dòng. */
function db_all(string $sql, array $params = []): array
{
    return db_query($sql, $params)->fetchAll();
}

/** Lấy một dòng, không có thì trả về null. */
function db_one(string $sql, array $params = []): ?array
{
    $row = db_query($sql, $params)->fetch();
    return $row === false ? null : $row;
}

/** Lấy một giá trị đơn (COUNT, SUM...). */
function db_value(string $sql, array $params = [])
{
    return db_query($sql, $params)->fetchColumn();
}
