<?php
/**
 * Helper Functions
 * DatCyber Home Appliances
 */

// Format currency to VND
function format_price($number) {
    return number_format((float)$number, 0, ',', '.') . ' ₫';
}

// Sanitize plain string input (prevent XSS)
function clean_input($data) {
    if ($data === null) return '';
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

// Sanitize rich HTML content allowing only safe tags (prevent Stored XSS)
function sanitize_html($html) {
    if (empty($html)) return '';
    $allowedTags = '<p><br><strong><em><b><i><u><ul><ol><li><h3><h4><h5><h6><span><div><table><thead><tbody><tr><th><td><img><hr><blockquote>';
    $clean = strip_tags($html, $allowedTags);
    // Remove javascript: and inline on* event attributes
    $clean = preg_replace('/javascript\s*:/i', '', $clean);
    $clean = preg_replace('/\s*on[a-zA-Z]+\s*=\s*("[^"]*"|\'[^\']*\'|[^\s>]+)/i', '', $clean);
    return $clean;
}

// Validate Vietnamese phone number format (10 digits starting with 03, 05, 07, 08, 09)
function is_valid_phone($phone) {
    $phone = preg_replace('/[\s\.\-\(\)]/', '', $phone);
    return (bool)preg_match('/^(0[3|5|7|8|9])[0-9]{8}$/', $phone);
}

// Validate email address
function is_valid_email($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}

// Check if user is logged in as admin
function is_admin_logged_in() {
    return isset($_SESSION['admin_user']) && !empty($_SESSION['admin_user']['id']);
}

// Enforce admin login check
function require_admin() {
    if (!is_admin_logged_in()) {
        header('Location: login.php');
        exit;
    }
}

// Get Cart Item Count
function get_cart_count() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $total += (int)$item['quantity'];
    }
    return $total;
}

// Get Cart Subtotal
function get_cart_subtotal() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    $subtotal = 0;
    foreach ($_SESSION['cart'] as $item) {
        $subtotal += ((float)$item['price'] * (int)$item['quantity']);
    }
    return $subtotal;
}

// Generate unique order code
function generate_order_code() {
    return 'HM-' . date('Ymd') . '-' . strtoupper(substr(md5(uniqid((string)rand(), true)), 0, 6));
}

// Render star ratings
function render_stars($rating) {
    $rating = max(0, min(5, (float)$rating));
    $full = floor($rating);
    $half = ($rating - $full) >= 0.5 ? 1 : 0;
    $empty = 5 - $full - $half;
    
    $html = '<div class="star-rating" title="Đánh giá ' . number_format($rating, 1) . '/5">';
    for ($i = 0; $i < $full; $i++) {
        $html .= '<i class="fas fa-star text-warning"></i>';
    }
    if ($half) {
        $html .= '<i class="fas fa-star-half-alt text-warning"></i>';
    }
    for ($i = 0; $i < $empty; $i++) {
        $html .= '<i class="far fa-star text-muted"></i>';
    }
    $html .= ' <span class="rating-num">(' . number_format($rating, 1) . ')</span></div>';
    return $html;
}

// Set Flash Message
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // success, error, warning, info
        'message' => $message
    ];
}

// Get and clear Flash Message
function get_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        unset($_SESSION['flash']);
        return $flash;
    }
    return null;
}
