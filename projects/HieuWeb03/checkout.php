<?php
// checkout.php - Order Checkout & Payment
$custom_page_title = "Thanh Toán Đơn Hàng";
require_once __DIR__ . '/config/app.php';

$cart_items = $_SESSION['cart'] ?? [];
if (empty($cart_items)) {
    set_flash('warning', 'Giỏ hàng của bạn đang trống.');
    header('Location: cart.php');
    exit;
}

$subtotal = get_cart_subtotal();
$discount = 0;
$coupon_code = '';
if (isset($_SESSION['applied_coupon'])) {
    $coupon_code = $_SESSION['applied_coupon']['code'];
    $discount = $_SESSION['applied_coupon']['discount'];
}

$shipping_fee = ($subtotal >= 250000 || $coupon_code === 'FREESHIP') ? 0 : 30000;
$total_amount = max(0, $subtotal - $discount + $shipping_fee);

// Pre-fill user data if logged in
$user = current_user();
$name = $user['fullname'] ?? '';
$email = $user['email'] ?? '';
$phone = $user['phone'] ?? '';
$address = $user['address'] ?? '';

// Handle Order Placement
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $customer_name = clean_input($_POST['customer_name'] ?? '');
    $customer_phone = clean_input($_POST['customer_phone'] ?? '');
    $customer_email = clean_input($_POST['customer_email'] ?? '');
    $shipping_address = clean_input($_POST['shipping_address'] ?? '');
    $order_notes = clean_input($_POST['order_notes'] ?? '');
    $payment_method = clean_input($_POST['payment_method'] ?? 'cod');

    if (empty($customer_name) || empty($customer_phone) || empty($shipping_address)) {
        $error = "Vui lòng điền đầy đủ Họ tên, Số điện thoại và Địa chỉ nhận hàng.";
    } else {
        try {
            $pdo->beginTransaction();

            $order_code = 'HM-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -4));
            $user_id = is_logged_in() ? current_user()['id'] : null;

            $stmt = $pdo->prepare("
                INSERT INTO orders (order_code, user_id, customer_name, customer_email, customer_phone, shipping_address, order_notes, subtotal, discount_amount, shipping_fee, total_amount, payment_method, payment_status, status)
                VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'unpaid', 'pending')
            ");
            $stmt->execute([
                $order_code, $user_id, $customer_name, $customer_email, $customer_phone, $shipping_address, $order_notes,
                $subtotal, $discount, $shipping_fee, $total_amount, $payment_method
            ]);
            $order_id = $pdo->lastInsertId();

            // Insert Order Items
            $item_stmt = $pdo->prepare("
                INSERT INTO order_items (order_id, product_id, product_name, product_image, price, quantity, total_price)
                VALUES (?, ?, ?, ?, ?, ?, ?)
            ");

            foreach ($cart_items as $item) {
                $item_total = $item['price'] * $item['quantity'];
                $item_stmt->execute([
                    $order_id, $item['id'], $item['name'], $item['image'], $item['price'], $item['quantity'], $item_total
                ]);

                // Reduce stock quantity
                $pdo->prepare("UPDATE products SET stock_quantity = GREATEST(0, stock_quantity - ?) WHERE id = ?")
                    ->execute([$item['quantity'], $item['id']]);
            }

            $pdo->commit();

            // Clear session cart
            $_SESSION['cart'] = [];
            unset($_SESSION['applied_coupon']);

            // Redirect to success page
            header("Location: order-success.php?code={$order_code}");
            exit;
        } catch (Exception $e) {
            $pdo->rollBack();
            $error = "Lỗi khi xử lý đơn hàng: " . $e->getMessage();
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container">
  <!-- Breadcrumb -->
  <div style="padding: 20px 0 10px; font-size: 0.88rem; color: var(--muted); display: flex; align-items: center; gap: 8px;">
    <a href="index.php" style="color: var(--muted);"><i class="bi bi-house"></i> Trang chủ</a>
    <span>/</span>
    <a href="cart.php" style="color: var(--muted);">Giỏ hàng</a>
    <span>/</span>
    <span style="color: var(--dark); font-weight: 700;">Thanh toán</span>
  </div>

  <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 24px; color: var(--dark);">
    <i class="bi bi-credit-card" style="color: var(--primary);"></i> Thông Tin Thanh Toán
  </h1>

  <?php if (!empty($error)): ?>
    <div style="background: #fee2e2; color: #dc2626; padding: 14px 20px; border-radius: var(--radius-md); margin-bottom: 24px; font-weight: 600;">
      <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form action="checkout.php" method="POST">
    <div class="checkout-layout">
      <!-- Left: Customer info & payment options -->
      <div class="checkout-form-card">
        <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 20px; color: var(--dark);">
          1. Thông Tin Người Nhận
        </h3>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="form-group">
            <label class="form-label">Họ và tên *</label>
            <input type="text" name="customer_name" required class="form-control" placeholder="Nguyễn Văn A" value="<?= htmlspecialchars($name) ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Số điện thoại nhận hàng *</label>
            <input type="tel" name="customer_phone" required class="form-control" placeholder="0901234567" value="<?= htmlspecialchars($phone) ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Email (Nhận thông báo đơn hàng)</label>
          <input type="email" name="customer_email" class="form-control" placeholder="email@example.com" value="<?= htmlspecialchars($email) ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Địa chỉ giao hàng chi tiết *</label>
          <textarea name="shipping_address" required rows="3" class="form-control" placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố..."><?= htmlspecialchars($address) ?></textarea>
        </div>

        <div class="form-group">
          <label class="form-label">Ghi chú đơn hàng (Tùy chọn)</label>
          <textarea name="order_notes" rows="2" class="form-control" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi giao..."></textarea>
        </div>

        <h3 style="font-size: 1.25rem; font-weight: 800; margin: 30px 0 20px; color: var(--dark);">
          2. Phương Thức Thanh Toán
        </h3>

        <!-- Option 1: COD -->
        <label class="payment-method-card active">
          <input type="radio" name="payment_method" value="cod" checked style="accent-color: var(--primary); width: 18px; height: 18px;">
          <div>
            <div style="font-weight: 700; color: var(--dark);">Thanh toán khi nhận hàng (COD)</div>
            <div style="font-size: 0.85rem; color: var(--muted);">Thanh toán tiền mặt cho nhân viên giao hàng khi nhận được bưu phẩm.</div>
          </div>
        </label>

        <!-- Option 2: Bank Transfer (VietQR) -->
        <label class="payment-method-card">
          <input type="radio" name="payment_method" value="bank_transfer" style="accent-color: var(--primary); width: 18px; height: 18px;">
          <div>
            <div style="font-weight: 700; color: var(--dark);">Chuyển khoản Ngân Hàng (Quét mã VietQR)</div>
            <div style="font-size: 0.85rem; color: var(--muted);">Chuyển khoản nhanh 24/7 qua mã QR tự động xác nhận.</div>
          </div>
        </label>

        <!-- Option 3: MoMo -->
        <label class="payment-method-card">
          <input type="radio" name="payment_method" value="momo" style="accent-color: var(--primary); width: 18px; height: 18px;">
          <div>
            <div style="font-weight: 700; color: var(--dark);">Ví điện tử MoMo / VNPay</div>
            <div style="font-size: 0.85rem; color: var(--muted);">Thanh toán an toàn, bảo mật qua cổng ví điện tử.</div>
          </div>
        </label>
      </div>

      <!-- Right: Order Items & Total Summary -->
      <div>
        <div class="cart-summary-card">
          <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 20px; color: var(--dark);">
            Đơn Hàng Của Bạn (<?= get_cart_count() ?>)
          </h3>

          <div style="max-height: 280px; overflow-y: auto; margin-bottom: 20px; padding-right: 6px;">
            <?php foreach ($cart_items as $item): ?>
            <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
              <div style="display: flex; align-items: center; gap: 10px;">
                <img src="assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="" style="width: 44px; height: 44px; border-radius: 6px; object-fit: contain; background: #f8fafc;">
                <div>
                  <div style="font-size: 0.88rem; font-weight: 700; color: var(--dark);"><?= htmlspecialchars($item['name']) ?></div>
                  <div style="font-size: 0.8rem; color: var(--muted);">SL: x<?= $item['quantity'] ?></div>
                </div>
              </div>
              <div style="font-size: 0.9rem; font-weight: 700; color: var(--dark); white-space: nowrap;">
                <?= format_price($item['price'] * $item['quantity']) ?>
              </div>
            </div>
            <?php endforeach; ?>
          </div>

          <div class="summary-row">
            <span style="color: var(--muted);">Tạm tính:</span>
            <span style="font-weight: 700;"><?= format_price($subtotal) ?></span>
          </div>

          <?php if ($discount > 0): ?>
          <div class="summary-row" style="color: var(--accent-emerald);">
            <span>Giảm giá:</span>
            <span style="font-weight: 700;">-<?= format_price($discount) ?></span>
          </div>
          <?php endif; ?>

          <div class="summary-row">
            <span style="color: var(--muted);">Phí vận chuyển:</span>
            <span style="font-weight: 700;">
              <?= $shipping_fee === 0 ? '<span style="color: var(--accent-emerald);">Miễn phí</span>' : format_price($shipping_fee) ?>
            </span>
          </div>

          <div class="summary-row summary-total">
            <span>Tổng thanh toán:</span>
            <span><?= format_price($total_amount) ?></span>
          </div>

          <button type="submit" name="place_order" value="1" class="btn btn-secondary btn-lg" style="width: 100%; margin-top: 24px; justify-content: center;">
            <i class="bi bi-shield-lock-fill"></i> Đặt Hàng Ngay
          </button>
        </div>
      </div>
    </div>
  </form>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const paymentCards = document.querySelectorAll('.payment-method-card');
  paymentCards.forEach(card => {
    card.addEventListener('click', () => {
      paymentCards.forEach(c => c.classList.remove('active'));
      card.classList.add('active');
      const radio = card.querySelector('input[type="radio"]');
      if (radio) radio.checked = true;
    });
  });
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>

