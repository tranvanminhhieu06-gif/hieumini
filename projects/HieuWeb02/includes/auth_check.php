<?php
/**
 * ==========================================================
 * HIEUMINI TECH STORE - XÁC THỰC VÀ PHÂN QUYỀN (AUTH CHECK)
 * ==========================================================
 */

if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Kiểm tra xem người dùng đã đăng nhập chưa
 */
function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

/**
 * Kiểm tra xem người dùng hiện tại có phải là Admin hay không
 */
function is_admin() {
    return is_logged_in() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

/**
 * Lấy thông tin user hiện tại
 */
function current_user() {
    return is_logged_in() ? $_SESSION['user'] : null;
}

/**
 * Yêu cầu đăng nhập trước khi truy cập trang
 */
function require_login($redirect_url = 'login.php') {
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
        set_flash('warning', 'Vui lòng đăng nhập để tiếp tục thao tác!');
        header("Location: " . BASE_URL . "/" . $redirect_url);
        exit;
    }
}

/**
 * Yêu cầu quyền quản trị viên Admin
 */
function require_admin($redirect_url = 'admin/login.php') {
    if (!is_admin()) {
        set_flash('danger', 'Khu vực hạn chế! Bạn cần quyền Quản trị viên để truy cập.');
        header("Location: " . BASE_URL . "/" . $redirect_url);
        exit;
    }
}
