<?php
$page_title = 'Thanh Toán Đơn Hàng';
require_once __DIR__ . '/includes/header.php';

$cart = $_SESSION['cart'] ?? [];
if (empty($cart)) {
    header('Location: cart.php');
    exit;
}

$subtotal = get_cart_subtotal();
$discount = 0;
$couponInfo = $_SESSION['coupon'] ?? null;

// Recalculate and validate discount from database if coupon is present
if ($couponInfo) {
    $cpStmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (expiry_date IS NULL OR expiry_date >= CURDATE())");
    $cpStmt->execute([$couponInfo['code']]);
    $validCoupon = $cpStmt->fetch();
    if ($validCoupon && $subtotal >= (float)$validCoupon['min_order']) {
        if ($validCoupon['discount_type'] === 'percent') {
            $discount = ($subtotal * (float)$validCoupon['discount_value']) / 100;
        } else {
            $discount = (float)$validCoupon['discount_value'];
        }
    } else {
        unset($_SESSION['coupon']);
        $couponInfo = null;
        $discount = 0;
    }
}

$shippingFee = ($subtotal >= 500000) ? 0 : 30000;
$finalTotal = max(0, $subtotal - $discount + $shippingFee);

// Handle Order Placement
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['place_order'])) {
    $name = clean_input($_POST['customer_name'] ?? '');
    $phone = clean_input($_POST['customer_phone'] ?? '');
    $email = clean_input($_POST['customer_email'] ?? '');
    $address = clean_input($_POST['customer_address'] ?? '');
    $note = clean_input($_POST['customer_note'] ?? '');
    $paymentMethod = clean_input($_POST['payment_method'] ?? 'cod');

    if (empty($name) || empty($phone) || empty($address)) {
        $error = 'Vui lòng điền đầy đủ Họ tên, Số điện thoại và Địa chỉ nhận hàng!';
    } elseif (!is_valid_phone($phone)) {
        $error = 'Số điện thoại không đúng định dạng Việt Nam (10 số, bắt đầu bằng 03, 05, 07, 08, 09)!';
    } elseif (!empty($email) && !is_valid_email($email)) {
        $error = 'Địa chỉ Email không đúng định dạng!';
    } else {
        try {
            $pdo->beginTransaction();

            // Verify stock for all items before committing
            foreach ($cart as $item) {
                $stStmt = $pdo->prepare("SELECT stock, name FROM products WHERE id = ? FOR UPDATE");
                $stStmt->execute([$item['id']]);
                $prodRow = $stStmt->fetch();
                if (!$prodRow || (int)$prodRow['stock'] < (int)$item['quantity']) {
                    throw new Exception("Sản phẩm '" . ($prodRow['name'] ?? 'Không rõ') . "' không đủ số lượng trong kho!");
                }
            }

            $orderCode = generate_order_code();
            $userId = $_SESSION['user_id'] ?? null;

            $stmt = $pdo->prepare("INSERT INTO orders (order_code, user_id, customer_name, customer_email, customer_phone, customer_address, customer_note, payment_method, total_amount, discount_amount, shipping_fee, final_amount, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([
                $orderCode, $userId, $name, $email, $phone, $address, $note, $paymentMethod,
                $subtotal, $discount, $shippingFee, $finalTotal
            ]);
            $orderId = $pdo->lastInsertId();

            // Insert items & reduce stock
            $itemStmt = $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, price, quantity, subtotal) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $upStock = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");

            foreach ($cart as $item) {
                $itemSubtotal = (float)$item['price'] * (int)$item['quantity'];
                $itemStmt->execute([
                    $orderId, $item['id'], $item['name'], $item['image'], $item['price'], $item['quantity'], $itemSubtotal
                ]);
                $upStock->execute([(int)$item['quantity'], $item['id']]);
            }

            $pdo->commit();

            // Clear session cart and coupon
            unset($_SESSION['cart']);
            unset($_SESSION['coupon']);

            header("Location: order-success.php?code=$orderCode");
            exit;

        } catch (Exception $e) {
            $pdo->rollBack();
            $error = 'Đã có lỗi xảy ra khi tạo đơn hàng: ' . $e->getMessage();
        }
    }
}
?>

<main class="container my-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-3 border shadow-sm">
      <li class="breadcrumb-item"><a href="index.php" class="text-primary text-decoration-none"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
      <li class="breadcrumb-item"><a href="cart.php" class="text-secondary text-decoration-none">Giỏ hàng</a></li>
      <li class="breadcrumb-item active" aria-current="page">Thanh toán</li>
    </ol>
  </nav>

  <h2 class="fw-bold mb-4"><i class="fas fa-credit-card text-primary me-2"></i> Thông Tin Thanh Toán & Đặt Hàng</h2>

  <?php if (!empty($error)): ?>
    <div class="alert alert-danger shadow-sm"><?php echo htmlspecialchars($error); ?></div>
  <?php endif; ?>

  <form action="checkout.php" method="POST">
    <div class="row g-4">
      
      <!-- Customer Information Form -->
      <div class="col-lg-7">
        <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
          <h5 class="fw-bold mb-3"><i class="fas fa-user-tag text-primary me-2"></i> 1. Thông Tin Người Nhận</h5>
          
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Họ và tên <span class="text-danger">*</span></label>
              <input type="text" name="customer_name" class="form-control" required placeholder="Nguyễn Văn A" value="<?php echo htmlspecialchars($_POST['customer_name'] ?? ''); ?>">
            </div>

            <div class="col-md-6">
              <label class="form-label small fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
              <input type="tel" name="customer_phone" class="form-control" required placeholder="0912 345 678" value="<?php echo htmlspecialchars($_POST['customer_phone'] ?? ''); ?>">
            </div>

            <div class="col-12">
              <label class="form-label small fw-semibold">Địa chỉ Email</label>
              <input type="email" name="customer_email" class="form-control" placeholder="nguyenvana@gmail.com" value="<?php echo htmlspecialchars($_POST['customer_email'] ?? ''); ?>">
            </div>

            <div class="col-12">
              <label class="form-label small fw-semibold">Địa chỉ giao hàng chi tiết <span class="text-danger">*</span></label>
              <input type="text" name="customer_address" class="form-control" required placeholder="Số nhà, tên đường, phường/xã, quận/huyện, tỉnh/thành phố" value="<?php echo htmlspecialchars($_POST['customer_address'] ?? ''); ?>">
            </div>

            <div class="col-12">
              <label class="form-label small fw-semibold">Ghi chú giao hàng (Tùy chọn)</label>
              <textarea name="customer_note" rows="2" class="form-control" placeholder="Ví dụ: Giao giờ hành chính, gọi trước khi đến..."><?php echo htmlspecialchars($_POST['customer_note'] ?? ''); ?></textarea>
            </div>
          </div>
        </div>

        <!-- Payment Method Selection -->
        <div class="bg-white p-4 rounded-4 border shadow-sm">
          <h5 class="fw-bold mb-3"><i class="fas fa-wallet text-primary me-2"></i> 2. Phương Thức Thanh Toán</h5>

          <div class="d-flex flex-column gap-3">
            <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer bg-light">
              <input type="radio" name="payment_method" value="cod" checked class="form-check-input mt-0">
              <i class="fas fa-hand-holding-dollar text-success fs-4"></i>
              <div>
                <div class="fw-bold">Thanh toán khi nhận hàng (COD)</div>
                <small class="text-muted">Kiểm tra sản phẩm và thanh toán tiền mặt trực tiếp cho shipper</small>
              </div>
            </label>

            <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer">
              <input type="radio" name="payment_method" value="banking" class="form-check-input mt-0">
              <i class="fas fa-qrcode text-primary fs-4"></i>
              <div>
                <div class="fw-bold">Chuyển khoản Ngân hàng / Quét mã VietQR</div>
                <small class="text-muted">Chuyển khoản tự động qua hệ thống mã QR Napas 24/7</small>
              </div>
            </label>

            <label class="p-3 border rounded-3 d-flex align-items-center gap-3 cursor-pointer">
              <input type="radio" name="payment_method" value="momo" class="form-check-input mt-0">
              <i class="fas fa-mobile-screen-button text-danger fs-4"></i>
              <div>
                <div class="fw-bold">Ví MoMo / VNPay SmartPay</div>
                <small class="text-muted">Thanh toán nhanh chóng và bảo mật qua cổng ví điện tử</small>
              </div>
            </label>
          </div>
        </div>

      </div>

      <!-- Order Review Sidebar -->
      <div class="col-lg-5">
        <div class="bg-white p-4 rounded-4 border shadow-sm sticky-top" style="top: 90px;">
          <h5 class="fw-bold mb-3 border-bottom pb-2">Đơn Hàng Của Bạn (<?php echo get_cart_count(); ?> món)</h5>

          <div class="d-flex flex-column gap-2 mb-3" style="max-height: 250px; overflow-y: auto;">
            <?php foreach ($cart as $item): ?>
              <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div class="d-flex align-items-center gap-2">
                  <img src="assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" style="width: 45px; height: 45px; object-fit: cover;" class="rounded border">
                  <div>
                    <div class="small fw-bold text-truncate" style="max-width: 170px;"><?php echo htmlspecialchars($item['name']); ?></div>
                    <small class="text-muted">SL: <?php echo $item['quantity']; ?></small>
                  </div>
                </div>
                <span class="small fw-bold text-danger"><?php echo format_price($item['price'] * $item['quantity']); ?></span>
              </div>
            <?php endforeach; ?>
          </div>

          <div class="d-flex justify-content-between mb-2">
            <span class="text-secondary small">Tạm tính:</span>
            <span class="fw-bold small"><?php echo format_price($subtotal); ?></span>
          </div>

          <?php if ($discount > 0): ?>
            <div class="d-flex justify-content-between mb-2 text-success small">
              <span>Giảm giá (<?php echo htmlspecialchars($couponInfo['code']); ?>):</span>
              <span class="fw-bold">-<?php echo format_price($discount); ?></span>
            </div>
          <?php endif; ?>

          <div class="d-flex justify-content-between mb-2 small">
            <span class="text-secondary">Phí giao hàng:</span>
            <span class="fw-bold <?php echo $shippingFee == 0 ? 'text-success' : ''; ?>">
              <?php echo $shippingFee == 0 ? 'Miễn phí' : format_price($shippingFee); ?>
            </span>
          </div>

          <div class="border-top pt-3 mt-3 d-flex justify-content-between align-items-baseline mb-4">
            <span class="fw-bold">Tổng thanh toán:</span>
            <span class="fs-4 fw-bold text-danger"><?php echo format_price($finalTotal); ?></span>
          </div>

          <button type="submit" name="place_order" class="btn btn-primary-custom w-100 justify-content-center py-3 fs-6">
            <i class="fas fa-lock me-2"></i> Xác Nhận Đặt Hàng
          </button>
        </div>
      </div>

    </div>
  </form>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
