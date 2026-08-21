<?php
/**
 * Đăng xuất tài khoản
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

session_unset();
session_destroy();

session_start();
set_flash('info', 'Bạn đã đăng xuất khỏi hệ thống.');
redirect('index.php');
