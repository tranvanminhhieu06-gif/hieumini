<?php
// logout.php
require_once __DIR__ . '/config/app.php';
unset($_SESSION['user']);
set_flash('info', 'Bạn đã đăng xuất tài khoản an toàn.');
header('Location: index.php');
exit;
