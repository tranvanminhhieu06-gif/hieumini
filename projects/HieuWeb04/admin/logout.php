<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

unset($_SESSION['admin_user']);
set_flash('info', 'Bạn đã đăng xuất khỏi bảng quản trị thành công!');
header('Location: login.php');
exit;
