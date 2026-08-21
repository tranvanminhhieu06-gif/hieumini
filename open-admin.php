<?php
/**
 * Tệp này đã ngừng sử dụng.
 * Chức năng tự động đăng nhập đã được gỡ bỏ; nay mỗi dự án con dùng chung một
 * tài khoản quản trị cố định (hiển thị trên trang chi tiết). Giữ tệp lại dưới
 * dạng chuyển hướng để mọi liên kết cũ không bị lỗi 404. Có thể xóa tệp này.
 */
declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';

$slug = trim((string) ($_GET['slug'] ?? ''));
redirect($slug !== '' ? 'project.php?slug=' . rawurlencode($slug) : 'index.php');
