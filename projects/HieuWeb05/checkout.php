<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - CHECKOUT & ORDER PLACEMENT
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/includes/config.php';

$cart_items = $_SESSION['cart'] ?? [];
if (empty($cart_items)) {
    header("Location: " . BASE_URL . "/cart.php");
    exit;
}

$subtotal = get_cart_subtotal();
$discount = get_cart_discount();
$total = get_cart_total();
$coupon = $_SESSION['applied_coupon'] ?? null;

$errors = [];

// Xử lý đặt hàng khi submit
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name = sanitize($_POST['customer_name'] ?? '');
    $phone = sanitize($_POST['customer_phone'] ?? '');
    $email = sanitize($_POST['customer_email'] ?? '');
    $address = sanitize($_POST['customer_address'] ?? '');
    $payment_method = sanitize($_POST['payment_method'] ?? 'cod');
    $notes = sanitize($_POST['notes'] ?? '');

    // Kiểm tra tính hợp lệ
    if (empty($name)) $errors[] = "Vui lòng nhập họ và tên quý khách.";
    if (empty($phone)) $errors[] = "Vui lòng nhập số điện thoại liên hệ.";
    if (empty($address)) $errors[] = "Vui lòng nhập địa chỉ nhận hàng chi tiết.";

    if (empty($errors)) {
        try {
            $pdo->beginTransaction();

            // Sinh mã đơn hàng độc quyền CEO: HM-YYYYMMDD-XXXX
            $order_code = 'HM-' . date('ymd') . '-' . strtoupper(substr(uniqid(), -4));

            // Xác định trạng thái thanh toán
            $payment_status = ($payment_method === 'bank_transfer') ? 'pending' : 'pending';

            $order_stmt = $pdo->prepare("
                INSERT INTO orders (
                    order_code, user_id, customer_name, customer_email, 
                    customer_phone, customer_address, payment_method, payment_status, 
                    order_status, subtotal, discount_amount, shipping_fee, 
                    total_amount, coupon_code, notes
                ) VALUES (
                    ?, ?, ?, ?, 
                    ?, ?, ?, ?, 
                    'pending', ?, ?, 0, 
                    ?, ?, ?
                )
            ");

            $user_id = isset($_SESSION['user']['id']) ? $_SESSION['user']['id'] : null;

            $order_stmt->execute([
                $order_code, $user_id, $name, $email,
                $phone, $address, $payment_method, $payment_status,
                $subtotal, $discount,
                $total, $coupon, $notes
            ]);

            $order_id = $pdo->lastInsertId();

            // Lưu chi tiết các sản phẩm trong đơn hàng
            $item_stmt = $pdo->prepare("
                INSERT INTO order_items (
                    order_id, product_id, product_name, product_image, price, quantity, subtotal
                ) VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($cart_items as $item) {
                $item_sub = (float)$item['price'] * (int)$item['quantity'];
                $item_stmt->execute([
                    $order_id,
                    $item['id'],
                    $item['name'],
                    $item['image'],
                    $item['price'],
                    $item['quantity'],
                    $item_sub
                ]);

                // Giảm tồn kho sản phẩm
                $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?")
                    ->execute([$item['quantity'], $item['id']]);
            }

            $pdo->commit();

            // Xóa giỏ hàng sau khi đặt thành công
            $_SESSION['cart'] = [];
            unset($_SESSION['applied_coupon']);

            header("Location: " . BASE_URL . "/order-success.php?code=" . $order_code);
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $errors[] = "Lỗi xử lý đơn hàng: " . $e->getMessage();
        }
    }
}

$page_title = "Thanh Toán Đơn Hàng VIP | " . SITE_NAME;
$page_desc = "Điền thông tin và lựa chọn phương thức thanh toán an toàn cho đơn hàng của bạn.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <!-- Breadcrumb & Header -->
    <div style="margin-bottom: 2.5rem;" class="reveal">
        <div style="font-size: 0.85rem; color: var(--gold-primary); margin-bottom: 0.5rem;">
            <a href="<?= BASE_URL ?>/index.php">Trang Chủ</a> / <a href="<?= BASE_URL ?>/cart.php">Giỏ Hàng</a> / <span>Thanh Toán</span>
        </div>
        <h1 style="font-size: 2.6rem; font-weight: 800;">THANH TOÁN ĐƠN HÀNG CEO</h1>
    </div>

    <?php if (!empty($errors)): ?>
        <div style="background: rgba(239, 68, 68, 0.15); border: 1px solid var(--ruby-accent); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 2rem; color: #fca5a5;">
            <h4 style="font-weight: 700; margin-bottom: 0.5rem;"><i class="fas fa-exclamation-triangle"></i> Vui lòng kiểm tra lại:</h4>
            <ul style="margin-left: 1.5rem;">
                <?php foreach ($errors as $err): ?>
                    <li><?= htmlspecialchars($err) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form action="<?= BASE_URL ?>/checkout.php" method="POST">
        <div class="checkout-layout">
            <!-- Left: Billing Details & Payment Methods -->
            <div class="reveal">
                <!-- Customer Details -->
                <div class="form-card" style="margin-bottom: 2rem;">
                    <h3 class="form-title"><i class="fas fa-user-check" style="color: var(--gold-primary);"></i> THÔNG TIN KHÁCH HÀNG & GIAO HÀNG</h3>
                    
                    <div class="form-group">
                        <label>Họ và Tên Doanh Nhân / Người Nhận (*)</label>
                        <input type="text" name="customer_name" class="form-control" required placeholder="Ví dụ: Trần Đình Tuấn" value="<?= htmlspecialchars($_POST['customer_name'] ?? '') ?>">
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Số Điện Thoại Liên Hệ (*)</label>
                            <input type="tel" name="customer_phone" class="form-control" required placeholder="0988 888 xxx" value="<?= htmlspecialchars($_POST['customer_phone'] ?? '') ?>">
                        </div>
                        <div class="form-group">
                            <label>Email Xác Nhận Hóa Đơn</label>
                            <input type="email" name="customer_email" class="form-control" placeholder="ceo@company.com" value="<?= htmlspecialchars($_POST['customer_email'] ?? '') ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Địa Chỉ Nhận Hàng Chi Tiết (*)</label>
                        <input type="text" name="customer_address" class="form-control" required placeholder="Số nhà, Tòa nhà/Penthouse, Tên đường, Phường/Xã, Quận/Huyện..." value="<?= htmlspecialchars($_POST['customer_address'] ?? '') ?>">
                    </div>

                    <div class="form-group">
                        <label>Ghi Chú Đơn Hàng / Yêu Cầu Giao Hàng Riêng</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Ví dụ: Giao giờ hành chính, gọi trước 15 phút, xuất hóa đơn VAT công ty..."><?= htmlspecialchars($_POST['notes'] ?? '') ?></textarea>
                    </div>
                </div>

                <!-- Payment Methods Selector -->
                <div class="form-card">
                    <h3 class="form-title"><i class="fas fa-credit-card" style="color: var(--gold-primary);"></i> PHƯƠNG THỨC THANH TOÁN</h3>

                    <label class="payment-method-card active" onclick="selectPayment(this)">
                        <input type="radio" name="payment_method" value="cod" checked style="accent-color: var(--gold-primary);">
                        <i class="fas fa-hand-holding-usd" style="font-size: 1.5rem; color: var(--gold-light);"></i>
                        <div>
                            <strong style="color: #fff; display: block;">Thanh toán khi nhận hàng (COD VIP)</strong>
                            <span style="font-size: 0.85rem; color: var(--text-secondary);">Kiểm tra hàng trước khi thanh toán tiền mặt cho nhân viên giao hàng.</span>
                        </div>
                    </label>

                    <label class="payment-method-card" onclick="selectPayment(this)">
                        <input type="radio" name="payment_method" value="bank_transfer" style="accent-color: var(--gold-primary);">
                        <i class="fas fa-qrcode" style="font-size: 1.5rem; color: var(--cyan-accent);"></i>
                        <div>
                            <strong style="color: #fff; display: block;">Chuyển khoản Ngân hàng tự động (VietQR 24/7)</strong>
                            <span style="font-size: 0.85rem; color: var(--text-secondary);">Quét mã QR qua ứng dụng ngân hàng, kích hoạt dịch vụ tức thì.</span>
                        </div>
                    </label>

                    <label class="payment-method-card" onclick="selectPayment(this)">
                        <input type="radio" name="payment_method" value="credit_card" style="accent-color: var(--gold-primary);">
                        <i class="fas fa-credit-card" style="font-size: 1.5rem; color: var(--emerald-accent);"></i>
                        <div>
                            <strong style="color: #fff; display: block;">Thẻ Tín Dụng Quốc Tế (Visa / Master / JCB)</strong>
                            <span style="font-size: 0.85rem; color: var(--text-secondary);">Cổng thanh toán bảo mật 3D-Secure tiêu chuẩn bảo mật quốc tế.</span>
                        </div>
                    </label>

                    <!-- Dynamic Bank Info Preview -->
                    <div id="bank-info-box" style="display: none; background: rgba(6, 182, 212, 0.08); border: 1px solid var(--cyan-accent); border-radius: var(--radius-sm); padding: 1.25rem; margin-top: 1rem;">
                        <h5 style="color: var(--cyan-accent); font-weight: 700; margin-bottom: 0.5rem;"><i class="fas fa-info-circle"></i> THÔNG TIN TÀI KHOẢN HIEUMINI FITNESS:</h5>
                        <ul style="font-size: 0.9rem; color: #e2e8f0; list-style: none; display: flex; flex-direction: column; gap: 0.35rem;">
                            <li>• Ngân hàng: <strong>MBBank (Ngân Hàng Quân Đội)</strong></li>
                            <li>• Số tài khoản CEO VIP: <strong>8888 9999 8888</strong></li>
                            <li>• Chủ tài khoản: <strong>CONG TY CO PHAN THE HINH HIEUMINI</strong></li>
                            <li>• Nội dung chuyển khoản: <strong style="color: var(--gold-light);">[Họ tên] - [SĐT]</strong></li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Right: Order Items Summary -->
            <div class="reveal delay-1">
                <div class="cart-summary-card">
                    <h3 style="font-size: 1.35rem; font-weight: 800; color: #fff; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
                        ĐƠN HÀNG CỦA BẠN
                    </h3>

                    <!-- Mini item list -->
                    <div style="display: flex; flex-direction: column; gap: 1rem; margin-bottom: 1.5rem; max-height: 300px; overflow-y: auto; padding-right: 0.5rem;">
                        <?php foreach ($cart_items as $item): ?>
                        <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.75rem; font-size: 0.9rem;">
                            <div style="display: flex; align-items: center; gap: 0.6rem;">
                                <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="" style="width: 44px; height: 44px; border-radius: 4px; object-fit: cover;">
                                <div>
                                    <span style="color: #fff; font-weight: 600; display: block; max-width: 180px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;"><?= htmlspecialchars($item['name']) ?></span>
                                    <span style="color: var(--text-muted); font-size: 0.8rem;">SL: x<?= $item['quantity'] ?></span>
                                </div>
                            </div>
                            <span style="color: var(--gold-light); font-weight: 700;">
                                <?= format_currency($item['price'] * $item['quantity']) ?>
                            </span>
                        </div>
                        <?php endforeach; ?>
                    </div>

                    <div class="summary-row">
                        <span>Tạm tính</span>
                        <strong style="color: #fff;"><?= format_currency($subtotal) ?></strong>
                    </div>

                    <?php if ($discount > 0): ?>
                    <div class="summary-row" style="color: var(--emerald-accent);">
                        <span>Ưu đãi (<?= htmlspecialchars($coupon) ?>)</span>
                        <strong>-<?= format_currency($discount) ?></strong>
                    </div>
                    <?php endif; ?>

                    <div class="summary-row">
                        <span>Giao hàng VIP</span>
                        <strong style="color: var(--emerald-accent);">MIỄN PHÍ</strong>
                    </div>

                    <div class="summary-row total">
                        <span>Tổng Cộng</span>
                        <span class="total-amount"><?= format_currency($total) ?></span>
                    </div>

                    <div style="margin-top: 2rem;">
                        <button type="submit" name="place_order" class="btn btn-primary btn-block btn-lg btn-shimmer">
                            <i class="fas fa-check-double"></i> ĐẶT HÀNG & XÁC NHẬN VIP
                        </button>
                    </div>

                    <p style="margin-top: 1rem; font-size: 0.75rem; color: var(--text-muted); text-align: center;">
                        Bằng việc nhấn Đặt Hàng, bạn đồng ý với Điều Khoản Dịch Vụ và Cam Kết Bảo Mật Thông Tin của HieuMini.
                    </p>
                </div>
            </div>
        </div>
    </form>
</div>

<script>
function selectPayment(card) {
    document.querySelectorAll('.payment-method-card').forEach(c => c.classList.remove('active'));
    card.classList.add('active');
    const radio = card.querySelector('input[type="radio"]');
    if (radio) radio.checked = true;

    const bankBox = document.getElementById('bank-info-box');
    if (radio && radio.value === 'bank_transfer') {
        bankBox.style.display = 'block';
    } else {
        bankBox.style.display = 'none';
    }
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
