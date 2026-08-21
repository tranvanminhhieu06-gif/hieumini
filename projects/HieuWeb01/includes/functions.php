<?php
/**
 * Các hàm tiện ích dùng chung cho hệ thống HieuMini
 */
require_once __DIR__ . '/../config/db.php';

// Format tiền tệ Việt Nam (VNĐ)
function format_price($price) {
    return number_format($price, 0, ',', '.') . ' ' . CURRENCY_SYMBOL;
}

// Kiểm tra người dùng đã đăng nhập chưa
function is_logged_in() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

// Kiểm tra quyền Admin
function is_admin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

// Lấy thông tin user hiện tại
function current_user($pdo) {
    if (!is_logged_in()) return null;
    $stmt = $pdo->prepare("SELECT id, full_name, email, phone, address, role, avatar FROM users WHERE id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    return $stmt->fetch();
}

// Chuyển hướng trang
function redirect($url) {
    header("Location: " . $url);
    exit;
}

// Thông báo Flash Message
function set_flash($type, $message) {
    $_SESSION['flash'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'message' => $message
    ];
}

function display_flash() {
    if (isset($_SESSION['flash'])) {
        $flash = $_SESSION['flash'];
        $type = $flash['type'];
        $msg = htmlspecialchars($flash['message']);
        echo "<div class='alert alert-{$type} alert-dismissible fade show shadow-sm' role='alert'>
                <span>{$msg}</span>
                <button type='button' class='btn-close' onclick='this.parentElement.remove()'></button>
              </div>";
        unset($_SESSION['flash']);
    }
}

// Xử lý giỏ hàng
function get_cart_count() {
    $count = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $count += (int)$item['quantity'];
        }
    }
    return $count;
}

function get_cart_subtotal() {
    $total = 0;
    if (isset($_SESSION['cart']) && is_array($_SESSION['cart'])) {
        foreach ($_SESSION['cart'] as $item) {
            $price = (float)$item['price'];
            $qty = (int)$item['quantity'];
            $total += $price * $qty;
        }
    }
    return $total;
}

// Tạo slug tiếng Việt
function create_slug($string) {
    $search = array(
        '#(à|á|ạ|ả|ã|â|ầ|ấ|ậ|ẩ|ẫ|ă|ằ|ắ|ặ|ẳ|ẵ)#iu',
        '#(è|é|ẹ|ẻ|ẽ|ê|ề|ế|ệ|ể|ễ)#iu',
        '#(ì|í|ị|ỉ|ĩ)#iu',
        '#(ò|ó|ọ|ỏ|õ|ô|ồ|ố|ộ|ổ|ỗ|ơ|ờ|ớ|ợ|ở|ỡ)#iu',
        '#(ù|ú|ụ|ủ|ũ|ư|ừ|ứ|ự|ử|ữ)#iu',
        '#(ỳ|ý|ỵ|ỷ|ỹ)#iu',
        '#(đ)#iu',
        '#[^a-zA-Z0-9\s_]#',
        '#[\s_]+#'
    );
    $replace = array('a', 'e', 'i', 'o', 'u', 'y', 'd', '', '-');
    $string = preg_replace($search, $replace, $string);
    return strtolower(trim($string, '-'));
}

// Format ngày giờ Việt Nam
function format_datetime($dt) {
    if (!$dt) return '';
    return date('d/m/Y H:i', strtotime($dt));
}

// Format trạng thái đơn hàng badge
function get_order_status_badge($status) {
    switch ($status) {
        case 'pending':
            return '<span class="badge badge-warning">Chờ xử lý</span>';
        case 'processing':
            return '<span class="badge badge-info">Đang xử lý</span>';
        case 'shipping':
            return '<span class="badge badge-primary">Đang giao hàng</span>';
        case 'completed':
            return '<span class="badge badge-success">Đã hoàn thành</span>';
        case 'cancelled':
            return '<span class="badge badge-danger">Đã hủy</span>';
        default:
            return '<span class="badge badge-secondary">' . htmlspecialchars($status) . '</span>';
    }
}
