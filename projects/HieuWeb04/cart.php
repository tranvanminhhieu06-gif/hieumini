<?php
$page_title = 'Giỏ Hàng Của Bạn';
require_once __DIR__ . '/includes/header.php';

// Handle Coupon Applied
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['apply_coupon'])) {
    $couponCode = clean_input($_POST['coupon_code'] ?? '');
    $subtotal = get_cart_subtotal();

    $cpStmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND is_active = 1 AND (expiry_date IS NULL OR expiry_date >= CURDATE())");
    $cpStmt->execute([$couponCode]);
    $coupon = $cpStmt->fetch();

    if ($coupon) {
        if ($subtotal >= $coupon['min_order']) {
            $_SESSION['coupon'] = $coupon;
            set_flash('success', "Áp dụng thành công mã giảm giá $couponCode!");
        } else {
            set_flash('error', "Mã $couponCode chỉ áp dụng cho đơn hàng từ " . format_price($coupon['min_order']));
        }
    } else {
        set_flash('error', 'Mã giảm giá không hợp lệ hoặc đã hết hạn!');
    }
    header('Location: cart.php');
    exit;
}

// Handle Remove Coupon
if (isset($_GET['remove_coupon'])) {
    unset($_SESSION['coupon']);
    set_flash('info', 'Đã hủy mã giảm giá');
    header('Location: cart.php');
    exit;
}

$cart = $_SESSION['cart'] ?? [];
$subtotal = get_cart_subtotal();
$discount = 0;
$couponInfo = $_SESSION['coupon'] ?? null;

if ($couponInfo && $subtotal >= $couponInfo['min_order']) {
    if ($couponInfo['discount_type'] === 'percent') {
        $discount = ($subtotal * $couponInfo['discount_value']) / 100;
    } else {
        $discount = (float)$couponInfo['discount_value'];
    }
} else {
    unset($_SESSION['coupon']);
}

$shippingFee = ($subtotal >= 500000 || empty($cart)) ? 0 : 30000;
$finalTotal = max(0, $subtotal - $discount + $shippingFee);
?>

<main class="container my-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-3 border shadow-sm">
      <li class="breadcrumb-item"><a href="index.php" class="text-primary text-decoration-none"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
      <li class="breadcrumb-item active" aria-current="page">Giỏ hàng</li>
    </ol>
  </nav>

  <h2 class="fw-bold mb-4"><i class="fas fa-bag-shopping text-primary me-2"></i> Giỏ Hàng (<?php echo get_cart_count(); ?> sản phẩm)</h2>

  <?php if (empty($cart)): ?>
    <div class="bg-white p-5 rounded-4 border text-center shadow-sm">
      <i class="fas fa-cart-arrow-down fa-4x text-muted mb-3"></i>
      <h4 class="fw-bold">Giỏ hàng của bạn đang trống</h4>
      <p class="text-secondary">Chưa có sản phẩm nào trong giỏ hàng. Hãy khám phá và mua sắm những sản phẩm gia dụng tốt nhất nhé!</p>
      <a href="products.php" class="btn btn-primary-custom px-4 py-2 mt-2">
        <i class="fas fa-bag-shopping me-1"></i> Mua sắm ngay
      </a>
    </div>
  <?php else: ?>
    <div class="row g-4">
      
      <!-- Cart Items Table -->
      <div class="col-lg-8">
        <div class="bg-white p-4 rounded-4 border shadow-sm">
          <div class="table-responsive">
            <table class="table align-middle">
              <thead class="table-light">
                <tr>
                  <th scope="col" style="min-width: 250px;">Sản phẩm</th>
                  <th scope="col" class="text-center">Đơn giá</th>
                  <th scope="col" class="text-center">Số lượng</th>
                  <th scope="col" class="text-end">Thành tiền</th>
                  <th scope="col" class="text-center">Xóa</th>
                </tr>
              </thead>
              <tbody>
                <?php foreach ($cart as $item): ?>
                  <tr>
                    <td>
                      <div class="d-flex align-items-center gap-3">
                        <img src="assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 65px; height: 65px; object-fit: cover;" class="rounded border">
                        <div>
                          <a href="product-detail.php?id=<?php echo $item['id']; ?>" class="fw-bold text-dark text-decoration-none d-block mb-1" style="font-size: 0.95rem;">
                            <?php echo htmlspecialchars($item['name']); ?>
                          </a>
                          <span class="badge bg-light text-secondary border">Chính hãng</span>
                        </div>
                      </div>
                    </td>
                    <td class="text-center fw-semibold text-danger">
                      <?php echo format_price($item['price']); ?>
                    </td>
                    <td class="text-center">
                      <div class="qty-control mx-auto">
                        <button type="button" class="qty-btn" onclick="updateCartItemQty(<?php echo $item['id']; ?>, <?php echo $item['quantity'] - 1; ?>)">-</button>
                        <input type="text" class="qty-input" value="<?php echo $item['quantity']; ?>" readonly>
                        <button type="button" class="qty-btn" onclick="updateCartItemQty(<?php echo $item['id']; ?>, <?php echo $item['quantity'] + 1; ?>)">+</button>
                      </div>
                    </td>
                    <td class="text-end fw-bold text-danger">
                      <?php echo format_price($item['price'] * $item['quantity']); ?>
                    </td>
                    <td class="text-center">
                      <button type="button" class="btn btn-link text-danger p-0" onclick="removeCartItem(<?php echo $item['id']; ?>)" title="Xóa">
                        <i class="fas fa-trash-can"></i>
                      </button>
                    </td>
                  </tr>
                <?php endforeach; ?>
              </tbody>
            </table>
          </div>

          <div class="d-flex justify-content-between align-items-center mt-3 pt-3 border-top">
            <a href="products.php" class="btn btn-outline-secondary">
              <i class="fas fa-arrow-left me-1"></i> Tiếp tục mua sắm
            </a>
          </div>
        </div>
      </div>

      <!-- Order Summary Card -->
      <div class="col-lg-4">
        
        <!-- Voucher Card -->
        <div class="bg-white p-4 rounded-4 border shadow-sm mb-4">
          <h6 class="fw-bold mb-3"><i class="fas fa-ticket text-primary me-2"></i> Mã Giảm Giá / Voucher</h6>
          <form action="cart.php" method="POST" class="d-flex gap-2 mb-2">
            <input type="text" name="coupon_code" class="form-control" placeholder="Nhập mã voucher (VD: DATCYBER10)" value="<?php echo $couponInfo ? htmlspecialchars($couponInfo['code']) : ''; ?>" required>
            <button type="submit" name="apply_coupon" class="btn btn-primary-custom text-nowrap">Áp dụng</button>
          </form>

          <?php if ($couponInfo): ?>
            <div class="alert alert-success d-flex justify-content-between align-items-center p-2 mt-2 mb-0">
              <small>Đang dùng mã: <strong><?php echo htmlspecialchars($couponInfo['code']); ?></strong></small>
              <a href="cart.php?remove_coupon=1" class="text-danger small text-decoration-none"><i class="fas fa-times me-1"></i>Gỡ</a>
            </div>
          <?php endif; ?>

          <div class="mt-3 pt-2 border-top">
            <small class="text-muted d-block">Gợi ý mã khuyến mãi:</small>
            <span class="badge bg-light text-primary border me-1">DATCYBER10</span>
            <span class="badge bg-light text-primary border">FREESHIP</span>
          </div>
        </div>

        <!-- Summary Calculation Card -->
        <div class="bg-white p-4 rounded-4 border shadow-sm">
          <h5 class="fw-bold mb-3 border-bottom pb-2">Tóm Tắt Đơn Hàng</h5>
          
          <div class="d-flex justify-content-between mb-2">
            <span class="text-secondary">Tạm tính:</span>
            <span class="fw-bold"><?php echo format_price($subtotal); ?></span>
          </div>

          <?php if ($discount > 0): ?>
            <div class="d-flex justify-content-between mb-2 text-success">
              <span>Giảm giá (<?php echo htmlspecialchars($couponInfo['code']); ?>):</span>
              <span class="fw-bold">-<?php echo format_price($discount); ?></span>
            </div>
          <?php endif; ?>

          <div class="d-flex justify-content-between mb-2">
            <span class="text-secondary">Phí vận chuyển:</span>
            <span class="fw-bold <?php echo $shippingFee == 0 ? 'text-success' : ''; ?>">
              <?php echo $shippingFee == 0 ? 'Miễn phí' : format_price($shippingFee); ?>
            </span>
          </div>

          <div class="border-top pt-3 mt-3 d-flex justify-content-between align-items-baseline mb-4">
            <span class="fs-6 fw-bold">Tổng thanh toán:</span>
            <span class="fs-4 fw-bold text-danger"><?php echo format_price($finalTotal); ?></span>
          </div>

          <a href="checkout.php" class="btn btn-primary-custom w-100 justify-content-center py-3 fs-6">
            Tiến Hành Đặt Hàng <i class="fas fa-arrow-right ms-2"></i>
          </a>
        </div>

      </div>

    </div>
  <?php endif; ?>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
