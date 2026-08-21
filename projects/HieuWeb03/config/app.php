<?php
// config/app.php - Application Configuration & Global Helpers

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Base URL definition
if (!defined('SITE_NAME')) define('SITE_NAME', 'HieuMini');
if (!defined('SITE_TAGLINE')) define('SITE_TAGLINE', 'Thiên Đường Đồ Dùng Học Tập & Sáng Tạo');
if (!defined('SITE_URL')) define('SITE_URL', 'http://localhost:8000');
if (!defined('CURRENCY_SYMBOL')) define('CURRENCY_SYMBOL', 'đ');

// Include Database connection
require_once __DIR__ . '/db.php';

// Helper: Format Currency (VND)
function format_price($amount) {
    return number_format($amount, 0, ',', '.') . ' ' . CURRENCY_SYMBOL;
}

// Helper: Sanitize Input String
function clean_input($data) {
    return htmlspecialchars(trim($data), ENT_QUOTES, 'UTF-8');
}

// Helper: Create URL Slug
function create_slug($string) {
    $search = ['á','à','ả','ã','ạ','ă','ắ','ằ','ẳ','ẵ','ặ','â','ấ','ầ','ẩ','ẫ','ậ',
               'é','è','ẻ','ẽ','ẹ','ê','ế','ề','ể','ễ','ệ',
               'í','ì','ỉ','ĩ','ị',
               'ó','ò','ỏ','õ','ọ','ô','ố','ồ','ổ','ỗ','ộ','ơ','ớ','ờ','ở','ỡ','ợ',
               'ú','ù','ủ','ũ','ụ','ư','ứ','ừ','ử','ữ','ự',
               'ý','ỳ','ỷ','ỹ','ỵ','đ',
               'Á','À','Ả','Ã','Ạ','Ă','Ắ','Ằ','Ẳ','Ẵ','Ặ','Â','Ấ','Ầ','Ẩ','Ẫ','Ậ',
               'É','È','Ẻ','Ẽ','Ẹ','Ê','Ế','Ề','Ể','Ễ','Ệ',
               'Í','Ì','Ỉ','Ĩ','Ị',
               'Ó','Ò','Ỏ','Õ','Ọ','Ô','Ố','Ồ','Ổ','Ỗ','Ộ','Ơ','Ớ','Ờ','Ở','Ỡ','Ợ',
               'Ú','Ù','Ủ','Ũ','Ụ','Ư','Ứ','Ừ','Ử','Ữ','Ự',
               'Ý','Ỳ','Ỷ','Ỹ','Ỵ','Đ'];
    $replace = ['a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
                'e','e','e','e','e','e','e','e','e','e','e',
                'i','i','i','i','i',
                'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
                'u','u','u','u','u','u','u','u','u','u','u',
                'y','y','y','y','y','d',
                'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
                'e','e','e','e','e','e','e','e','e','e','e',
                'i','i','i','i','i',
                'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
                'u','u','u','u','u','u','u','u','u','u','u',
                'y','y','y','y','y','d'];
    $string = str_replace($search, $replace, $string);
    $string = strtolower(preg_replace('/[^a-zA-Z0-9\s-]/', '', $string));
    return preg_replace('/[\s-]+/', '-', $string);
}

// Flash Notification Helpers
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}

// Auth Helpers
function is_logged_in() {
    return isset($_SESSION['user']) && !empty($_SESSION['user']['id']);
}

function is_admin() {
    return is_logged_in() && isset($_SESSION['user']['role']) && $_SESSION['user']['role'] === 'admin';
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function require_login() {
    if (!is_logged_in()) {
        set_flash('warning', 'Vui lòng đăng nhập để tiếp tục.');
        header('Location: login.php');
        exit;
    }
}

function require_admin() {
    if (!is_admin()) {
        set_flash('danger', 'Bạn không có quyền truy cập trang quản trị.');
        header('Location: ../login.php');
        exit;
    }
}

// Shopping Cart Helpers
function get_cart_count() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    $count = 0;
    foreach ($_SESSION['cart'] as $item) {
        $count += (int)$item['quantity'];
    }
    return $count;
}

function get_cart_subtotal() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += (float)$item['price'] * (int)$item['quantity'];
    }
    return $total;
}

// Helper: Render Dynamic Rating Stars
function render_rating_stars($rating) {
    $rating = max(0, min(5, (float)$rating));
    $full = floor($rating);
    $half = ($rating - $full) >= 0.3 ? 1 : 0;
    $empty = 5 - $full - $half;
    
    $html = '';
    for ($i = 0; $i < $full; $i++) {
        $html .= '<i class="bi bi-star-fill star-icon"></i>';
    }
    if ($half) {
        $html .= '<i class="bi bi-star-half star-icon"></i>';
    }
    for ($i = 0; $i < $empty; $i++) {
        $html .= '<i class="bi bi-star star-icon star-empty"></i>';
    }
    return $html;
}

