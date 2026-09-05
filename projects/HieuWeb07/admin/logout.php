<?php
/** Đăng xuất — chỉ chấp nhận POST kèm CSRF để không ai đăng xuất người khác bằng một đường link. */
declare(strict_types=1);
require __DIR__ . '/guard.php';
require_post_csrf();

$_SESSION = [];
session_destroy();
session_start();
flash('ok', 'Bạn đã đăng xuất.');
header('Location: ' . admin_url('login.php'));
