<?php
// cart.php - Shopping Cart Management Page
$custom_page_title = "Giỏ Hàng Của Bạn";
require_once __DIR__ . '/config/app.php';

// Handle direct POST add/update actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'add') {
        $product_id = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));

        $stmt = $pdo->prepare("SELECT id, name, price, sale_price, image FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $prod = $stmt->fetch();

        if ($prod) {
            $price = $prod['sale_price'] ? (float)$prod['sale_price'] : (float)$prod['price'];
            if (!isset($_SESSION['cart'])) $_SESSION['cart'] = [];

            if (isset($_SESSION['cart'][$product_id])) {
                $_SESSION['cart'][$product_id]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$product_id] = [
                    'id' => $prod['id'],
                    'name' => $prod['name'],
                    'price' => $price,
                    'image' => $prod['image'],
                    'quantity' => $quantity
                ];
            }

            if (isset($_POST['buy_now'])) {
                header('Location: checkout.php');
                exit;
            }
            set_flash('success', 'Đã thêm sản phẩm vào giỏ hàng thành công!');
        }
        header('Location: cart.php');
        exit;
    }

    if ($action === 'clear') {
        $_SESSION['cart'] = [];
        unset($_SESSION['applied_coupon']);
        set_flash('info', 'Đã làm trống giỏ hàng.');
        header('Location: cart.php');
        exit;
    }
}

// Handle GET remove
if (isset($_GET['remove'])) {
    $remove_id = (int)$_GET['remove'];
    if (isset($_SESSION['cart'][$remove_id])) {
        unset($_SESSION['cart'][$remove_id]);
        set_flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
    header('Location: cart.php');
    exit;
}

$cart_items = $_SESSION['cart'] ?? [];
$subtotal = get_cart_subtotal();

// Calculate coupon discount
$discount = 0;
$coupon_code = '';
if (isset($_SESSION['applied_coupon'])) {
    $coupon_code = $_SESSION['applied_coupon']['code'];
    $discount = $_SESSION['applied_coupon']['discount'];
}

// Free shipping threshold (250,000 VND)
$freeship_threshold = 250000;
$shipping_fee = ($subtotal >= $freeship_threshold || $subtotal == 0) ? 0 : 30000;
if ($coupon_code === 'FREESHIP') {
    $shipping_fee = 0;
}
$final_total = max(0, $subtotal - $discount + $shipping_fee);

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container">
  <!-- Breadcrumb -->
  <div style="padding: 20px 0 10px; font-size: 0.88rem; color: var(--muted); display: flex; align-items: center; gap: 8px;">
    <a href="index.php" style="color: var(--muted);"><i class="bi bi-house"></i> Trang chủ</a>
    <span>/</span>
    <span style="color: var(--dark); font-weight: 700;">Giỏ hàng</span>
  </div>

  <h1 style="font-size: 2rem; font-weight: 800; margin-bottom: 24px; color: var(--dark);">
    <i class="bi bi-bag-check" style="color: var(--primary);"></i> Giỏ Hàng Của Bạn (<?= get_cart_count() ?> sản phẩm)
  </h1>

  <?php if (!empty($cart_items)): ?>
    <!-- Freeship Progress Bar Banner -->
    <div style="background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 18px 24px; margin-bottom: 28px; box-shadow: var(--shadow-sm);">
      <?php if ($subtotal >= $freeship_threshold): ?>
        <div style="color: var(--accent-emerald); font-weight: 700; display: flex; align-items: center; gap: 8px;">
          <i class="bi bi-check-circle-fill" style="font-size: 1.2rem;"></i> Chúc mừng! Bạn đã đủ điều kiện nhận <strong>FREESHIP toàn quốc</strong>!
        </div>
      <?php else: 
        $needed = $freeship_threshold - $subtotal;
        $progress = min(100, round(($subtotal / $freeship_threshold) * 100));
      ?>
        <div style="font-size: 0.95rem; font-weight: 600; color: var(--dark); margin-bottom: 8px;">
          Mua thêm <strong><?= format_price($needed) ?></strong> để được <strong>Miễn phí vận chuyển</strong>!
        </div>
        <div style="background: var(--bg-light); height: 8px; border-radius: 4px; overflow: hidden;">
          <div style="background: linear-gradient(90deg, var(--primary), var(--secondary)); width: <?= $progress ?>%; height: 100%; transition: width 0.4s ease;"></div>
        </div>
      <?php endif; ?>
    </div>

    <div class="cart-layout">
      <!-- Left: Cart Items Table -->
      <div class="cart-table-card">
        <div class="table-responsive">
          <table class="cart-table">
            <thead>
              <tr>
                <th>Sản phẩm</th>
                <th>Đơn giá</th>
                <th>Số lượng</th>
                <th>Thành tiền</th>
                <th></th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($cart_items as $item): 
                $item_total = $item['price'] * $item['quantity'];
              ?>
              <tr data-product-id="<?= $item['id'] ?>">
                <td>
                  <div class="cart-item-info">
                    <img src="assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-item-img">
                    <div>
                      <a href="product-detail.php?id=<?= $item['id'] ?>" style="font-weight: 700; color: var(--dark); font-size: 0.95rem;">
                        <?= htmlspecialchars($item['name']) ?>
                      </a>
                    </div>
                  </div>
                </td>
                <td style="font-weight: 700; color: var(--primary);">
                  <?= format_price($item['price']) ?>
                </td>
                <td>
                  <div class="quantity-adjuster" style="transform: scale(0.9);">
                    <button type="button" class="qty-btn qty-minus" data-cart-update="true" aria-label="Giảm số lượng">-</button>
                    <input type="number" class="qty-input" value="<?= $item['quantity'] ?>" min="1" aria-label="Số lượng">
                    <button type="button" class="qty-btn qty-plus" data-cart-update="true" aria-label="Tăng số lượng">+</button>
                  </div>
                </td>
                <td style="font-weight: 800; color: var(--dark);">
                  <?= format_price($item_total) ?>
                </td>
                <td style="text-align: right;">
                  <a href="cart.php?remove=<?= $item['id'] ?>" style="color: #ef4444; font-size: 1.2rem; padding: 6px 10px; display: inline-flex; align-items: center;" title="Xóa" aria-label="Xóa sản phẩm">
                    <i class="bi bi-trash3"></i>
                  </a>
                </td>
              </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 24px; padding-top: 18px; border-top: 1px solid var(--border);">
          <a href="products.php" class="btn btn-outline btn-sm">
            <i class="bi bi-arrow-left"></i> Tiếp Tục Mua Sắm
          </a>
          <form action="cart.php" method="POST">
            <input type="hidden" name="action" value="clear">
            <button type="submit" class="btn btn-sm" style="background: #fee2e2; color: #dc2626;" onclick="return confirm('Bạn có chắc chắn muốn xóa toàn bộ giỏ hàng?');">
              <i class="bi bi-trash"></i> Xóa Tất Cả
            </button>
          </form>
        </div>
      </div>

      <!-- Right: Summary Card & Coupons -->
      <div>
        <div class="cart-summary-card">
          <h3 style="font-size: 1.25rem; font-weight: 800; margin-bottom: 20px; color: var(--dark);">Tóm Tắt Đơn Hàng</h3>

          <!-- Coupon input -->
          <div style="margin-bottom: 20px;">
            <label class="form-label" style="font-size: 0.85rem;">Mã giảm giá (Thử: <code>HIEUMINI10</code>, <code>FREESHIP</code>):</label>
            <div style="display: flex; gap: 8px;">
              <input type="text" id="couponCodeInput" class="form-control" placeholder="Nhập mã voucher..." value="<?= htmlspecialchars($coupon_code) ?>" style="text-transform: uppercase;">
              <button type="button" id="applyCouponBtn" class="btn btn-primary btn-sm" style="white-space: nowrap;">Áp Dụng</button>
            </div>
            <div id="couponStatusMessage" style="font-size: 0.82rem; margin-top: 6px;"></div>
          </div>

          <!-- Price rows -->
          <div class="summary-row">
            <span style="color: var(--muted);">Tạm tính:</span>
            <span style="font-weight: 700;"><?= format_price($subtotal) ?></span>
          </div>

          <?php if ($discount > 0): ?>
          <div class="summary-row" style="color: var(--accent-emerald);">
            <span>Giảm giá (<?= htmlspecialchars($coupon_code) ?>):</span>
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
            <span>Tổng cộng:</span>
            <span><?= format_price($final_total) ?></span>
          </div>

          <a href="checkout.php" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 20px; justify-content: center;">
            <i class="bi bi-credit-card"></i> Tiến Hành Thanh Toán
          </a>
        </div>
      </div>
    </div>
  <?php else: ?>
    <!-- Empty Cart State -->
    <div style="background: var(--white); border-radius: var(--radius-xl); padding: 80px 20px; text-align: center; border: 1px solid var(--border); margin: 30px 0 60px;">
      <div style="width: 100px; height: 100px; background: #e0e7ff; color: var(--primary); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 3rem; margin: 0 auto 20px;">
        <i class="bi bi-bag-x"></i>
      </div>
      <h2 style="font-size: 1.6rem; font-weight: 800; margin-bottom: 10px;">Giỏ hàng của bạn đang trống!</h2>
      <p style="color: var(--muted); margin-bottom: 28px; max-width: 450px; margin-left: auto; margin-right: auto;">
        Hãy dạo quanh cửa hàng và chọn cho mình những món đồ dùng học tập xinh xắn nhé!
      </p>
      <a href="products.php" class="btn btn-primary btn-lg">
        <i class="bi bi-cart-plus"></i> Khám Phá Sản Phẩm Ngay
      </a>
    </div>
  <?php endif; ?>
</div>

<script>
document.addEventListener('DOMContentLoaded', () => {
  const couponBtn = document.getElementById('applyCouponBtn');
  const couponInput = document.getElementById('couponCodeInput');
  const statusMsg = document.getElementById('couponStatusMessage');

  if (couponBtn && couponInput) {
    couponBtn.addEventListener('click', () => {
      const code = couponInput.value.trim();
      if (!code) return;

      const formData = new FormData();
      formData.append('code', code);

      fetch('api/coupon.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        if (data.status === 'success') {
          statusMsg.style.color = '#10b981';
          statusMsg.textContent = data.message;
          showToast(data.message, 'success');
          setTimeout(() => window.location.reload(), 800);
        } else {
          statusMsg.style.color = '#ef4444';
          statusMsg.textContent = data.message;
          showToast(data.message, 'danger');
        }
      })
      .catch(err => {
        showToast('Lỗi áp dụng mã', 'danger');
      });
    });
  }
});
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
