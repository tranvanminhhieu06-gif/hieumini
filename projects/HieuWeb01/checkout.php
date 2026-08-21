<?php
/**
 * Trang Đặt Hàng & Thanh Toán HieuMini
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$cartItems = $_SESSION['cart'] ?? [];
if (empty($cartItems)) {
    set_flash('warning', 'Giỏ hàng của bạn đang trống, vui lòng chọn sản phẩm trước khi thanh toán!');
    redirect('products.php');
}

$currentUser = current_user($pdo);
$subtotal = get_cart_subtotal();

// Tính giảm giá
$discount = 0;
$couponCode = null;
if (isset($_SESSION['coupon'])) {
    $cp = $_SESSION['coupon'];
    $couponCode = $cp['code'];
    if ($cp['type'] === 'percentage') {
        $discount = ($subtotal * $cp['value']) / 100;
    } else {
        $discount = $cp['value'];
    }
}

$shippingFee = ($subtotal >= 300000) ? 0 : 30000;
$grandTotal = max(0, $subtotal - $discount + $shippingFee);

// Xử lý submit đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'place_order') {
    $fullname = trim($_POST['fullname'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $address = trim($_POST['address'] ?? '');
    $city = trim($_POST['city'] ?? '');
    $notes = trim($_POST['notes'] ?? '');
    $paymentMethod = $_POST['payment_method'] ?? 'cod';

    $errors = [];
    if (empty($fullname)) $errors[] = "Vui lòng nhập họ và tên người nhận.";
    if (empty($phone)) $errors[] = "Vui lòng nhập số điện thoại liên hệ.";
    if (empty($address)) $errors[] = "Vui lòng nhập địa chỉ giao hàng chi tiết.";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            $orderCode = 'HM-ORD-' . date('ymd') . '-' . rand(1000, 9999);
            $fullAddress = $address . ', ' . $city;
            $userId = $currentUser ? $currentUser['id'] : null;
            $paymentStatus = ($paymentMethod === 'banking') ? 'unpaid' : 'unpaid';

            $ordStmt = $pdo->prepare("INSERT INTO orders (user_id, order_code, customer_name, customer_phone, customer_email, shipping_address, payment_method, payment_status, order_status, total_amount, discount_amount, shipping_fee, coupon_code, notes) VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?)");
            
            $ordStmt->execute([
                $userId,
                $orderCode,
                $fullname,
                $phone,
                $email,
                $fullAddress,
                $paymentMethod,
                $paymentStatus,
                $grandTotal,
                $discount,
                $shippingFee,
                $couponCode,
                $notes
            ]);

            $orderId = $pdo->lastInsertId();

            // Thêm các mục sản phẩm
            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, size, color, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $updateStock = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");

            foreach ($cartItems as $item) {
                $itemSubtotal = $item['price'] * $item['quantity'];
                $itemStmt->execute([
                    $orderId,
                    $item['product_id'],
                    $item['name'],
                    $item['price'],
                    $item['quantity'],
                    $item['size'],
                    $item['color'],
                    $itemSubtotal
                ]);
                $updateStock->execute([$item['quantity'], $item['product_id']]);
            }

            // Cập nhật số lần dùng mã giảm giá
            if ($couponCode) {
                $pdo->prepare("UPDATE coupons SET used_count = used_count + 1 WHERE code = ?")->execute([$couponCode]);
            }

            $pdo->commit();

            // Xóa giỏ hàng và coupon sau khi đặt hàng thành công
            unset($_SESSION['cart']);
            unset($_SESSION['coupon']);

            // Lưu order_code vào session để hiển thị ở trang thành công
            $_SESSION['last_order_code'] = $orderCode;
            redirect('order_success.php?code=' . urlencode($orderCode));

        } catch (Exception $e) {
            $pdo->rollBack();
            set_flash('danger', 'Có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage());
        }
    } else {
        set_flash('danger', implode('<br>', $errors));
    }
}

$pageTitle = "Thanh Toán Đặt Hàng";
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header-banner">
    <div class="container">
        <h1>Thanh Toán Đơn Hàng</h1>
        <div class="breadcrumbs">
            <a href="index.php">Trang Chủ</a> / <a href="cart.php">Giỏ Hàng</a> / <span>Thanh Toán</span>
        </div>
    </div>
</div>

<div class="container">
    <form action="checkout.php" method="POST">
        <input type="hidden" name="action" value="place_order">

        <div class="checkout-layout">
            <!-- Form thông tin giao hàng -->
            <div class="checkout-form-card">
                <h3 style="font-size: 1.25rem; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                    <i class="fa-solid fa-truck-ramp-box text-accent"></i> Thông Tin Giao Hàng
                </h3>

                <div class="form-group">
                    <label class="form-label">Họ và tên người nhận <span class="text-danger">*</span></label>
                    <input type="text" name="fullname" class="form-control" placeholder="Nguyễn Văn A" value="<?= htmlspecialchars($currentUser['full_name'] ?? '') ?>" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Số điện thoại liên hệ <span class="text-danger">*</span></label>
                        <input type="tel" name="phone" class="form-control" placeholder="0988xxxxxx" value="<?= htmlspecialchars($currentUser['phone'] ?? '') ?>" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Địa chỉ Email</label>
                        <input type="email" name="email" class="form-control" placeholder="email@gmail.com" value="<?= htmlspecialchars($currentUser['email'] ?? '') ?>">
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label class="form-label">Tỉnh / Thành phố <span class="text-danger">*</span></label>
                        <select name="city" class="form-control" required>
                            <option value="Hà Nội" selected>Hà Nội</option>
                            <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh</option>
                            <option value="Đà Nẵng">Đà Nẵng</option>
                            <option value="Hải Phòng">Hải Phòng</option>
                            <option value="Cần Thơ">Cần Thơ</option>
                            <option value="Tỉnh thành khác">Tỉnh thành khác</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label class="form-label">Địa chỉ chi tiết (Số nhà, ngõ, đường) <span class="text-danger">*</span></label>
                        <input type="text" name="address" class="form-control" placeholder="Số 18 Duy Tân, Cầu Giấy" value="<?= htmlspecialchars($currentUser['address'] ?? '') ?>" required>
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Ghi chú giao hàng (Tùy chọn)</label>
                    <textarea name="notes" rows="2" class="form-control" placeholder="Ghi chú thêm về thời gian giao hoặc chỉ dẫn địa chỉ..."></textarea>
                </div>

                <h3 style="font-size: 1.25rem; margin: 30px 0 16px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                    <i class="fa-solid fa-credit-card text-accent"></i> Phương Thức Thanh Toán
                </h3>

                <label class="payment-method-card active" onclick="this.parentElement.querySelectorAll('.payment-method-card').forEach(el => el.classList.remove('active')); this.classList.add('active'); document.getElementById('qr-bank-info').style.display = 'none';">
                    <input type="radio" name="payment_method" value="cod" checked style="accent-color: var(--accent);">
                    <div>
                        <strong style="color: var(--primary); display: block;">Thanh toán khi nhận hàng (COD)</strong>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">Thanh toán tiền mặt cho nhân viên giao hàng sau khi nhận và kiểm tra hàng</span>
                    </div>
                </label>

                <label class="payment-method-card" onclick="this.parentElement.querySelectorAll('.payment-method-card').forEach(el => el.classList.remove('active')); this.classList.add('active'); document.getElementById('qr-bank-info').style.display = 'block';">
                    <input type="radio" name="payment_method" value="banking" style="accent-color: var(--accent);">
                    <div>
                        <strong style="color: var(--primary); display: block;">Chuyển khoản Ngân hàng (VietQR / Internet Banking)</strong>
                        <span style="font-size: 0.8rem; color: var(--text-muted);">Quét mã QR qua app ngân hàng - Xử lý xác nhận nhanh</span>
                    </div>
                </label>

                <!-- Box hướng dẫn chuyển khoản -->
                <div id="qr-bank-info" style="display: none; background: #f8fafc; border: 1px dashed var(--accent); padding: 18px; border-radius: var(--radius-md); margin-top: 12px;">
                    <div style="font-size: 0.85rem; font-weight: 700; color: var(--primary); margin-bottom: 6px;">
                        Thông tin tài khoản nhận thanh toán HieuMini:
                    </div>
                    <ul style="font-size: 0.85rem; color: var(--secondary); line-height: 1.8;">
                        <li>Ngân hàng: <strong>MBBank (Ngân hàng Quân Đội)</strong></li>
                        <li>Số tài khoản: <strong>0988889999</strong></li>
                        <li>Chủ tài khoản: <strong>HIEUMINI FASHION STUDIO</strong></li>
                        <li>Nội dung CK: <strong>HM [Họ tên] [SĐT]</strong></li>
                    </ul>
                </div>
            </div>

            <!-- Tóm tắt đơn hàng thanh toán -->
            <div class="cart-summary-box">
                <h3 class="summary-title">Chi Tiết Đơn Hàng (<?= count($cartItems) ?> món)</h3>

                <div style="max-height: 300px; overflow-y: auto; margin-bottom: 16px; padding-right: 6px;">
                    <?php foreach ($cartItems as $item): ?>
                        <div style="display: flex; gap: 12px; margin-bottom: 14px; padding-bottom: 14px; border-bottom: 1px solid var(--border);">
                            <img src="assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="Thumb" style="width: 50px; height: 50px; object-fit: cover; border-radius: var(--radius-sm);">
                            <div style="flex: 1;">
                                <div style="font-size: 0.85rem; font-weight: 700; color: var(--primary);"><?= htmlspecialchars($item['name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">
                                    Size: <?= htmlspecialchars($item['size']) ?> | <?= htmlspecialchars($item['color']) ?> | SL: x<?= $item['quantity'] ?>
                                </div>
                            </div>
                            <div style="font-weight: 700; font-size: 0.85rem; color: var(--primary);">
                                <?= format_price($item['price'] * $item['quantity']) ?>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>

                <div class="summary-row">
                    <span>Tạm tính:</span>
                    <span><?= format_price($subtotal) ?></span>
                </div>

                <?php if ($discount > 0): ?>
                    <div class="summary-row" style="color: var(--success);">
                        <span>Giảm giá (<?= htmlspecialchars($couponCode) ?>):</span>
                        <span>-<?= format_price($discount) ?></span>
                    </div>
                <?php endif; ?>

                <div class="summary-row">
                    <span>Phí vận chuyển:</span>
                    <span><?= $shippingFee == 0 ? '<strong style="color: var(--success);">Miễn Phí</strong>' : format_price($shippingFee) ?></span>
                </div>

                <div class="summary-row total">
                    <span>Tổng tiền:</span>
                    <span style="color: #ef4444;"><?= format_price($grandTotal) ?></span>
                </div>

                <button type="submit" class="btn btn-accent btn-lg btn-block" style="margin-top: 20px;">
                    <i class="fa-solid fa-lock"></i> Đặt Hàng Ngay (<?= format_price($grandTotal) ?>)
                </button>

                <p style="font-size: 0.75rem; color: var(--text-light); text-align: center; margin-top: 14px;">
                    <i class="fa-solid fa-shield-halved"></i> Thông tin đặt hàng của bạn được bảo mật tuyệt đối.
                </p>
            </div>
        </div>
    </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
