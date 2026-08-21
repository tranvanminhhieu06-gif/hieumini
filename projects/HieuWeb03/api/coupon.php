<?php
// api/coupon.php - Coupon Voucher Verification
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/app.php';

$code = clean_input($_POST['code'] ?? '');
$subtotal = get_cart_subtotal();

if (empty($code)) {
    echo json_encode(['status' => 'error', 'message' => 'Vui lòng nhập mã giảm giá']);
    exit;
}

try {
    $stmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND expiry_date >= CURDATE()");
    $stmt->execute([$code]);
    $coupon = $stmt->fetch();

    if (!$coupon) {
        echo json_encode(['status' => 'error', 'message' => 'Mã giảm giá không hợp lệ hoặc đã hết hạn']);
        exit;
    }

    if ($subtotal < (float)$coupon['min_order_value']) {
        echo json_encode([
            'status' => 'error',
            'message' => 'Đơn hàng tối thiểu ' . format_price($coupon['min_order_value']) . ' để áp dụng mã này'
        ]);
        exit;
    }

    $discount = 0;
    if ($coupon['discount_type'] === 'percentage') {
        $discount = ($subtotal * (float)$coupon['discount_value']) / 100;
        if ($coupon['max_discount'] && $discount > (float)$coupon['max_discount']) {
            $discount = (float)$coupon['max_discount'];
        }
    } else {
        $discount = (float)$coupon['discount_value'];
    }

    $_SESSION['applied_coupon'] = [
        'code' => $coupon['code'],
        'discount' => $discount
    ];

    echo json_encode([
        'status' => 'success',
        'message' => 'Áp dụng mã giảm giá thành công!',
        'discount' => $discount,
        'formatted_discount' => format_price($discount),
        'total' => max(0, $subtotal - $discount),
        'formatted_total' => format_price(max(0, $subtotal - $discount))
    ]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
