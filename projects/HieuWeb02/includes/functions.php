<?php
/**
 * ==========================================================
 * HIEUMINI TECH STORE - CÁC HÀM TIỆN ÍCH DỰ ÁN (HELPER FUNCTIONS)
 * ==========================================================
 */

// Bắt đầu Session nếu chưa có
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}

/**
 * Định dạng tiền tệ Việt Nam Đồng (VND)
 */
function format_currency($amount) {
    return number_format((float)$amount, 0, ',', '.') . ' ₫';
}

/**
 * Tính phần trăm giảm giá giữa giá gốc và giá khuyến mãi
 */
function calculate_discount($price, $sale_price) {
    if (!$sale_price || $sale_price >= $price || $price <= 0) return 0;
    return round((($price - $sale_price) / $price) * 100);
}

/**
 * Làm sạch dữ liệu đầu vào chống XSS và Injection
 */
function sanitize($data) {
    if (is_array($data)) {
        return array_map('sanitize', $data);
    }
    return htmlspecialchars(trim((string)$data), ENT_QUOTES, 'UTF-8');
}

/**
 * Tạo URL thân thiện (Slug)
 */
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return empty($text) ? 'n-a' : $text;
}

/**
 * Thiết lập thông báo tạm thời (Flash message)
 */
function set_flash($type, $message) {
    $_SESSION['flash_message'] = [
        'type' => $type, // 'success', 'danger', 'warning', 'info'
        'text' => $message
    ];
}

/**
 * Lấy và hiển thị thông báo flash message
 */
function display_flash() {
    if (isset($_SESSION['flash_message'])) {
        $flash = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        $icon = 'fa-circle-info';
        if ($flash['type'] === 'success') $icon = 'fa-circle-check';
        if ($flash['type'] === 'danger') $icon = 'fa-triangle-exclamation';
        if ($flash['type'] === 'warning') $icon = 'fa-circle-exclamation';

        return '
        <div class="custom-alert alert-' . $flash['type'] . ' animate-fade-in">
            <i class="fa-solid ' . $icon . '"></i>
            <span>' . $flash['text'] . '</span>
            <button type="button" class="alert-close-btn" onclick="this.parentElement.remove()">&times;</button>
        </div>';
    }
    return '';
}

/**
 * Đếm tổng số lượng sản phẩm trong giỏ hàng
 */
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

/**
 * Tính tổng số tiền trong giỏ hàng
 */
function get_cart_total() {
    if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
        return 0;
    }
    $total = 0;
    foreach ($_SESSION['cart'] as $item) {
        $price = !empty($item['sale_price']) && $item['sale_price'] > 0 ? $item['sale_price'] : $item['price'];
        $total += (float)$price * (int)$item['quantity'];
    }
    return $total;
}

/**
 * Hiển thị huy hiệu trạng thái đơn hàng (Badge)
 */
function render_order_status($status) {
    switch ($status) {
        case 'pending':
            return '<span class="badge badge-warning"><i class="fa-regular fa-clock"></i> Chờ xử lý</span>';
        case 'processing':
            return '<span class="badge badge-info"><i class="fa-solid fa-gears"></i> Đang xử lý</span>';
        case 'shipping':
            return '<span class="badge badge-primary"><i class="fa-solid fa-truck-fast"></i> Đang giao</span>';
        case 'completed':
            return '<span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Hoàn thành</span>';
        case 'cancelled':
            return '<span class="badge badge-danger"><i class="fa-solid fa-ban"></i> Đã hủy</span>';
        default:
            return '<span class="badge badge-secondary">' . ucfirst($status) . '</span>';
    }
}

/**
 * Hiển thị sao đánh giá
 */
function render_rating_stars($rating) {
    $rating = (float)$rating;
    $output = '<div class="star-rating">';
    for ($i = 1; $i <= 5; $i++) {
        if ($i <= $rating) {
            $output .= '<i class="fa-solid fa-star text-warning"></i>';
        } elseif ($i - 0.5 <= $rating) {
            $output .= '<i class="fa-solid fa-star-half-stroke text-warning"></i>';
        } else {
            $output .= '<i class="fa-regular fa-star text-muted"></i>';
        }
    }
    $output .= ' <span class="rating-num">(' . number_format($rating, 1) . ')</span></div>';
    return $output;
}

/**
 * Lấy danh mục sản phẩm từ CSDL (kèm fallback dự phòng)
 */
function get_categories($pdo) {
    if ($pdo) {
        try {
            $stmt = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY id ASC");
            return $stmt->fetchAll();
        } catch (Exception $e) {}
    }
    // Dữ liệu fallback
    return [
        ['id' => 1, 'name' => 'Điện thoại Smartphone', 'slug' => 'dien-thoai', 'icon' => 'fa-mobile-screen-button'],
        ['id' => 2, 'name' => 'Laptop & Macbook', 'slug' => 'laptop-macbook', 'icon' => 'fa-laptop'],
        ['id' => 3, 'name' => 'Máy tính bảng (Tablet)', 'slug' => 'tablet', 'icon' => 'fa-tablet-screen-button'],
        ['id' => 4, 'name' => 'Đồng hồ thông minh', 'slug' => 'smartwatch', 'icon' => 'fa-clock'],
        ['id' => 5, 'name' => 'Tai nghe & Âm thanh', 'slug' => 'tai-nghe-am-thanh', 'icon' => 'fa-headphones'],
        ['id' => 6, 'name' => 'Phụ kiện công nghệ', 'slug' => 'phu-kien', 'icon' => 'fa-keyboard']
    ];
}
