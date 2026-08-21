<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - ADMIN LOGOUT
 */
require_once __DIR__ . '/../includes/config.php';

unset($_SESSION['admin_user']);
set_flash('info', 'Quý khách đã đăng xuất khỏi phiên làm việc an toàn.');
header("Location: " . BASE_URL . "/admin/login.php");
exit;
