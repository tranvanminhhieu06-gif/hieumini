<?php
/**
 * HieuMini Admin — Đăng xuất
 */

declare(strict_types=1);
require __DIR__ . '/guard.php';

unset($_SESSION['admin_ok'], $_SESSION['admin_seen']);
session_regenerate_id(true);

flash('success', 'Bạn đã đăng xuất khỏi khu vực quản trị.');
redirect('admin/login.php');
