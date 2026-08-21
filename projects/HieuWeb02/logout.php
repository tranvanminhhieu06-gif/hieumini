<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

unset($_SESSION['user']);
set_flash('info', 'Bạn đã đăng xuất khỏi hệ thống.');
header("Location: index.php");
exit;
