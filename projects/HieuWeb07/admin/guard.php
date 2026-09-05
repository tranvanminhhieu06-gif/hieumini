<?php
/**
 * Chốt chặn cho toàn bộ khu vực quản trị.
 * Mọi trang trong thư mục admin/ đều require tệp này ở dòng đầu tiên.
 */
declare(strict_types=1);

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

function admin_url(string $path = ''): string
{
    return url('admin/' . ltrim($path, '/'));
}

function current_admin(): ?array
{
    if (empty($_SESSION['admin_id'])) {
        return null;
    }
    static $a = null;
    return $a ??= db_one('SELECT id, username, full_name FROM admins WHERE id = :id',
                         [':id' => $_SESSION['admin_id']]);
}

/**
 * Bắt buộc đăng nhập. Ghi lại trang định vào để sau khi đăng nhập
 * quay đúng chỗ đó, thay vì luôn đổ về bảng điều khiển.
 */
function require_admin(): array
{
    $admin = current_admin();
    if (!$admin) {
        $_SESSION['after_login'] = $_SERVER['REQUEST_URI'] ?? admin_url('index.php');
        header('Location: ' . admin_url('login.php'));
        exit;
    }
    return $admin;
}

/** Mọi thao tác ghi trong admin đều phải qua kiểm tra này. */
function require_post_csrf(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST' || !csrf_check($_POST['csrf'] ?? null)) {
        http_response_code(400);
        exit('Yêu cầu không hợp lệ.');
    }
}
