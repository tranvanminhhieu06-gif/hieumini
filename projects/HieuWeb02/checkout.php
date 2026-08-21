<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth_check.php';

if (!isset($_SESSION['cart']) || empty($_SESSION['cart'])) {
    set_flash('warning', 'Giỏ hàng của bạn đang trống! Vui lòng chọn sản phẩm trước khi thanh toán.');
    header("Location: products.php");
    exit;
}

$cart = $_SESSION['cart'];
$subtotal = get_cart_total();
$discount = isset($_SESSION['coupon']) ? $_SESSION['coupon']['discount_amount'] : 0;
$shipping_fee = $subtotal > 2000000 ? 0 : 30000;
$total_amount = max(0, $subtotal - $discount + $shipping_fee);
$user = current_user();

// Xử lý Đặt hàng (POST)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $customer_name = sanitize($_POST['customer_name'] ?? '');
    $customer_email = sanitize($_POST['customer_email'] ?? '');
    $customer_phone = sanitize($_POST['customer_phone'] ?? '');
    $customer_address = sanitize($_POST['customer_address'] ?? '');
    $shipping_city = sanitize($_POST['shipping_city'] ?? 'Hà Nội');
    $payment_method = sanitize($_POST['payment_method'] ?? 'cod');
    $note = sanitize($_POST['note'] ?? '');

    if (empty($customer_name) || empty($customer_phone) || empty($customer_address)) {
        set_flash('danger', 'Vui lòng điền đầy đủ Họ tên, Số điện thoại và Địa chỉ giao hàng!');
    } else {
        $order_code = 'HM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));
        $user_id = $user ? $user['id'] : null;
        $payment_status = ($payment_method === 'bank_transfer' || $payment_method === 'momo') ? 'paid' : 'unpaid';

        if ($pdo) {
            try {
                $pdo->beginTransaction();

                // Lưu đơn hàng
                $stmt = $pdo->prepare("INSERT INTO orders (order_code, user_id, customer_name, customer_email, customer_phone, customer_address, shipping_city, payment_method, payment_status, shipping_status, subtotal, discount, shipping_fee, total_amount, note) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $order_code, $user_id, $customer_name, $customer_email, $customer_phone,
                    $customer_address, $shipping_city, $payment_method, $payment_status,
                    $subtotal, $discount, $shipping_fee, $total_amount, $note
                ]);
                $order_id = $pdo->lastInsertId();

                // Lưu chi tiết các sản phẩm trong đơn hàng
                $stmt_item = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, price, quantity, total) VALUES (?, ?, ?, ?, ?, ?)");
                foreach ($cart as $item) {
                    $item_price = !empty($item['sale_price']) && $item['sale_price'] > 0 ? $item['sale_price'] : $item['price'];
                    $item_total = $item_price * $item['quantity'];
                    $stmt_item->execute([
                        $order_id, $item['id'], $item['name'], $item_price, $item['quantity'], $item_total
                    ]);
                }

                $pdo->commit();
            } catch (Exception $e) {
                if ($pdo->inTransaction()) $pdo->rollBack();
            }
        }

        // Lưu thông tin đơn vừa đặt vào session để hiển thị trang success
        $_SESSION['last_order'] = [
            'order_code' => $order_code,
            'customer_name' => $customer_name,
            'customer_phone' => $customer_phone,
            'customer_address' => $customer_address . ', ' . $shipping_city,
            'total_amount' => $total_amount,
            'payment_method' => $payment_method,
            'items' => $cart
        ];

        // Xóa giỏ hàng sau khi đặt thành công
        $_SESSION['cart'] = [];
        unset($_SESSION['coupon']);

        set_flash('success', 'Chúc mừng bạn đã đặt hàng thành công tại HieuMini!');
        header("Location: order_success.php?code=" . $order_code);
        exit;
    }
}

$page_title = 'Thanh Toán Đơn Hàng';
require_once __DIR__ . '/includes/header.php';
?>

<main class="container" style="margin: 30px auto 60px;">
    <!-- Breadcrumb -->
    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--text-muted); margin-bottom: 24px;">
        <a href="index.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <span>/</span>
        <a href="cart.php">Giỏ hàng</a>
        <span>/</span>
        <span style="color: #fff;">Thanh toán đơn hàng</span>
    </div>

    <form action="checkout.php" method="POST">
        <div class="checkout-layout">
            
            <!-- Cột trái: Thông tin nhận hàng & Phương thức thanh toán -->
            <div>
                <!-- 1. Thông tin giao hàng -->
                <div class="glass-panel" style="padding: 28px; margin-bottom: 30px;">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; color: #fff; border-bottom: var(--border-glass); padding-bottom: 12px;">
                        <i class="fa-solid fa-truck-ramp-box" style="color: var(--primary);"></i> 1. Thông Tin Người Nhận
                    </h3>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label>Họ và tên người nhận <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="customer_name" class="form-control" placeholder="VD: Trần Văn Minh Hiếu" required value="<?php echo $user['full_name'] ?? ''; ?>">
                        </div>
                        <div class="form-group">
                            <label>Số điện thoại liên hệ <span style="color: var(--danger);">*</span></label>
                            <input type="tel" name="customer_phone" class="form-control" placeholder="VD: 0988 888 999" required value="<?php echo $user['phone'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Địa chỉ Email nhận thông báo:</label>
                        <input type="email" name="customer_email" class="form-control" placeholder="VD: hieu@gmail.com" value="<?php echo $user['email'] ?? ''; ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label>Tỉnh / Thành phố <span style="color: var(--danger);">*</span></label>
                            <select name="shipping_city" class="form-control">
                                <option value="Hà Nội">Hà Nội (Giao 2H)</option>
                                <option value="TP. Hồ Chí Minh">TP. Hồ Chí Minh (Giao 2H)</option>
                                <option value="Đà Nẵng">Đà Nẵng</option>
                                <option value="Hải Phòng">Hải Phòng</option>
                                <option value="Cần Thơ">Cần Thơ</option>
                                <option value="Khác">Tỉnh thành khác...</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Địa chỉ nhận hàng chi tiết <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="customer_address" class="form-control" placeholder="Số nhà, tên đường, phường/xã..." required value="<?php echo $user['address'] ?? ''; ?>">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 0;">
                        <label>Ghi chú đơn hàng (Tùy chọn):</label>
                        <textarea name="note" class="form-control" rows="2" placeholder="Ví dụ: Giao sau 17h, gọi trước khi giao..."></textarea>
                    </div>
                </div>

                <!-- 2. Phương thức thanh toán -->
                <div class="glass-panel" style="padding: 28px;">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; color: #fff; border-bottom: var(--border-glass); padding-bottom: 12px;">
                        <i class="fa-solid fa-credit-card" style="color: var(--accent);"></i> 2. Phương Thức Thanh Toán
                    </h3>

                    <div style="display: flex; flex-direction: column; gap: 14px;">
                        <!-- COD -->
                        <label style="display: flex; align-items: center; gap: 14px; padding: 16px; border: var(--border-glass); border-radius: var(--radius-md); background: rgba(255,255,255,0.02); cursor: pointer;">
                            <input type="radio" name="payment_method" value="cod" checked>
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <i class="fa-solid fa-money-bill-wave" style="font-size: 1.4rem; color: var(--success);"></i>
                                <div>
                                    <div style="font-weight: 700; color: #fff;">Thanh toán khi nhận hàng (COD)</div>
                                    <div style="font-size: 0.82rem; color: var(--text-muted);">Kiểm tra hàng trước khi thanh toán tiền mặt cho shipper</div>
                                </div>
                            </div>
                        </label>

                        <!-- VietQR -->
                        <label style="display: flex; align-items: center; gap: 14px; padding: 16px; border: var(--border-glass); border-radius: var(--radius-md); background: rgba(255,255,255,0.02); cursor: pointer;">
                            <input type="radio" name="payment_method" value="bank_transfer">
                            <div style="display: flex; align-items: center; gap: 12px; flex: 1;">
                                <i class="fa-solid fa-qrcode" style="font-size: 1.4rem; color: var(--accent);"></i>
                                <div>
                                    <div style="font-weight: 700; color: #fff;">Chuyển khoản trực tuyến qua VietQR (Khuyên dùng)</div>
                                    <div style="font-size: 0.82rem; color: var(--text-muted);">Quét mã QR tự động xác nhận qua ngân hàng hoặc MoMo</div>
                                </div>
                            </div>
                        </label>
                    </div>

                    <!-- VietQR Preview Box -->
                    <div id="bank-qr-details" style="display: none; margin-top: 20px; background: rgba(6, 182, 212, 0.08); border: 1px solid rgba(6, 182, 212, 0.3); border-radius: var(--radius-md); padding: 20px; text-align: center;">
                        <h4 style="color: var(--accent); margin-bottom: 12px;"><i class="fa-solid fa-qrcode"></i> Quét Mã VietQR Thanh Toán</h4>
                        <div style="width: 140px; height: 140px; background: #fff; border-radius: var(--radius-md); margin: 0 auto 14px; display: flex; align-items: center; justify-content: center; padding: 10px;">
                            <i class="fa-solid fa-qrcode" style="font-size: 6rem; color: #0f172a;"></i>
                        </div>
                        <p style="font-size: 0.9rem; color: #cbd5e1; margin-bottom: 4px;">Ngân hàng: <strong>MBBank (Quân Đội)</strong></p>
                        <p style="font-size: 0.9rem; color: #cbd5e1; margin-bottom: 4px;">Số tài khoản: <strong style="color: var(--accent);">888899999999</strong></p>
                        <p style="font-size: 0.9rem; color: #cbd5e1;">Chủ tài khoản: <strong>CONG TY CÔNG NGHE HIEUMINI</strong></p>
                    </div>
                </div>
            </div>

            <!-- Cột phải: Đơn hàng của bạn -->
            <div>
                <div class="order-summary-card" style="position: sticky; top: 120px;">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; border-bottom: var(--border-glass); padding-bottom: 12px;">
                        <i class="fa-solid fa-bag-shopping" style="color: var(--primary);"></i> Đơn Hàng Của Bạn (<?php echo count($cart); ?>)
                    </h3>

                    <!-- Danh sách sản phẩm rút gọn -->
                    <div style="max-height: 240px; overflow-y: auto; margin-bottom: 20px; padding-right: 6px;">
                        <?php foreach ($cart as $item): 
                            $price = !empty($item['sale_price']) && $item['sale_price'] > 0 ? $item['sale_price'] : $item['price'];
                        ?>
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px; font-size: 0.9rem;">
                                <div style="flex: 1; padding-right: 10px;">
                                    <div style="color: #fff; font-weight: 600;"><?php echo htmlspecialchars($item['name']); ?></div>
                                    <div style="font-size: 0.8rem; color: var(--text-muted);">SL: x<?php echo $item['quantity']; ?></div>
                                </div>
                                <div style="font-weight: 700; color: #f43f5e;">
                                    <?php echo format_currency($price * $item['quantity']); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-row">
                        <span>Tạm tính:</span>
                        <strong style="color: #fff;"><?php echo format_currency($subtotal); ?></strong>
                    </div>

                    <?php if ($discount > 0): ?>
                        <div class="summary-row" style="color: var(--success);">
                            <span>Giảm giá khuyến mãi:</span>
                            <strong>-<?php echo format_currency($discount); ?></strong>
                        </div>
                    <?php endif; ?>

                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <strong style="color: <?php echo $shipping_fee === 0 ? 'var(--success)' : '#fff'; ?>;">
                            <?php echo $shipping_fee === 0 ? 'MIỄN PHÍ' : format_currency($shipping_fee); ?>
                        </strong>
                    </div>

                    <div class="summary-row total">
                        <span>Tổng số tiền:</span>
                        <span class="price-highlight"><?php echo format_currency($total_amount); ?></span>
                    </div>

                    <button type="submit" name="place_order" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1rem; margin-top: 20px;">
                        <i class="fa-solid fa-lock"></i> XÁC NHẬN ĐẶT HÀNG NGAY
                    </button>

                    <p style="font-size: 0.78rem; color: var(--text-muted); text-align: center; margin-top: 14px;">
                        Bằng việc nhấn Đặt hàng, bạn đồng ý với Điều khoản và Chính sách của HieuMini.
                    </p>
                </div>
            </div>
        </div>
    </form>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
