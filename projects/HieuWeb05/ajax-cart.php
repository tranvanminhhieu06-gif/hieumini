<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - AJAX BACKEND API ENDPOINTS
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

$action = $_POST['action'] ?? $_GET['action'] ?? '';

switch ($action) {
    case 'add':
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($product_id <= 0 || $quantity <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Thông tin sản phẩm không hợp lệ.']);
            exit;
        }

        // Lấy thông tin sản phẩm từ CSDL
        $stmt = $pdo->prepare("SELECT id, name, price, original_price, image, sku, stock FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if (!$product) {
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại.']);
            exit;
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product['id'],
                'name' => $product['name'],
                'price' => (float)$product['price'],
                'image' => $product['image'],
                'sku' => $product['sku'],
                'quantity' => $quantity
            ];
        }

        echo json_encode([
            'status' => 'success',
            'message' => 'Đã thêm "' . htmlspecialchars($product['name']) . '" vào giỏ hàng!',
            'cart_count' => get_cart_count(),
            'cart_subtotal' => get_cart_subtotal(),
            'cart_total' => get_cart_total()
        ]);
        exit;

    case 'update':
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = (int)($_POST['quantity'] ?? 1);

        if ($product_id <= 0 || $quantity <= 0) {
            echo json_encode(['status' => 'error', 'message' => 'Số lượng không hợp lệ.']);
            exit;
        }

        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] = $quantity;
            echo json_encode([
                'status' => 'success',
                'message' => 'Đã cập nhật số lượng thành công.',
                'cart_count' => get_cart_count(),
                'cart_subtotal' => get_cart_subtotal(),
                'cart_total' => get_cart_total()
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không có trong giỏ hàng.']);
        }
        exit;

    case 'remove':
        $product_id = (int)($_POST['product_id'] ?? 0);
        if (isset($_SESSION['cart'][$product_id])) {
            unset($_SESSION['cart'][$product_id]);
            echo json_encode([
                'status' => 'success',
                'message' => 'Đã xóa sản phẩm khỏi giỏ hàng.',
                'cart_count' => get_cart_count(),
                'cart_subtotal' => get_cart_subtotal(),
                'cart_total' => get_cart_total()
            ]);
        } else {
            echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy sản phẩm.']);
        }
        exit;

    case 'apply_coupon':
        $code = strtoupper(trim($_POST['coupon_code'] ?? ''));
        $valid_coupons = [
            'CEOFIT20' => 'Áp dụng mã CEOFIT20 thành công! Giảm ngay 20% tổng đơn hàng.',
            'HIEUMINI10' => 'Áp dụng mã HIEUMINI10 thành công! Giảm ngay 10% tổng đơn hàng.',
            'VIPMEMBER' => 'Áp dụng mã VIPMEMBER thành công! Giảm ngay 15% tổng đơn hàng.'
        ];

        if (array_key_exists($code, $valid_coupons)) {
            $_SESSION['applied_coupon'] = $code;
            echo json_encode([
                'status' => 'success',
                'message' => $valid_coupons[$code],
                'coupon' => $code,
                'discount' => get_cart_discount(),
                'total' => get_cart_total()
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Mã ưu đãi không hợp lệ hoặc đã hết hạn.'
            ]);
        }
        exit;

    case 'book_trial':
        $name = sanitize($_POST['full_name'] ?? '');
        $phone = sanitize($_POST['phone'] ?? '');
        $email = sanitize($_POST['email'] ?? '');
        $service = sanitize($_POST['service_type'] ?? 'Gói Hội Viên CEO Diamond Elite');
        $branch = sanitize($_POST['branch'] ?? 'HieuMini Luxury Diamond - Quận 1, TP.HCM');
        $date = sanitize($_POST['booking_date'] ?? date('Y-m-d'));
        $time = sanitize($_POST['booking_time'] ?? '09:00 - 11:30');
        $notes = sanitize($_POST['notes'] ?? '');

        if ($name && $phone) {
            $stmt = $pdo->prepare("
                INSERT INTO bookings (full_name, phone, email, service_type, branch, booking_date, booking_time, notes, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')
            ");
            $stmt->execute([$name, $phone, $email, $service, $branch, $date, $time, $notes]);

            echo json_encode([
                'status' => 'success',
                'message' => 'Chúc mừng quý khách ' . htmlspecialchars($name) . '! Đã đặt lịch trải nghiệm VIP thành công. HieuMini sẽ gọi điện xác nhận trong ít phút.'
            ]);
        } else {
            echo json_encode([
                'status' => 'error',
                'message' => 'Vui lòng cung cấp đầy đủ Họ tên và Số điện thoại liên hệ.'
            ]);
        }
        exit;

    default:
        echo json_encode(['status' => 'error', 'message' => 'Yêu cầu không hợp lệ.']);
        exit;
}
