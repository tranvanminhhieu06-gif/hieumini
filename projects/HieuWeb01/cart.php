<?php
/**
 * Trang Giỏ Hàng & Quản Lý Mua Sắm HieuMini
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

// Xử lý các hành động trong giỏ hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';
    
    // 1. Thêm vào giỏ hàng
    if ($action === 'add') {
        $productId = (int)($_POST['product_id'] ?? 0);
        $quantity = max(1, (int)($_POST['quantity'] ?? 1));
        $size = trim($_POST['size'] ?? 'M');
        $color = trim($_POST['color'] ?? 'Đen');
        $submitAction = $_POST['submit_action'] ?? 'add_to_cart';

        $stmt = $pdo->prepare("SELECT id, name, price, discount_price, image, stock FROM products WHERE id = ? AND status = 1");
        $stmt->execute([$productId]);
        $product = $stmt->fetch();

        if ($product) {
            $effectivePrice = $product['discount_price'] ?? $product['price'];
            $cartKey = $productId . '_' . $size . '_' . $color;

            if (isset($_SESSION['cart'][$cartKey])) {
                $_SESSION['cart'][$cartKey]['quantity'] += $quantity;
            } else {
                $_SESSION['cart'][$cartKey] = [
                    'product_id' => $productId,
                    'name' => $product['name'],
                    'price' => $effectivePrice,
                    'image' => $product['image'],
                    'size' => $size,
                    'color' => $color,
                    'quantity' => $quantity
                ];
            }

            set_flash('success', 'Đã thêm "' . $product['name'] . '" vào giỏ hàng thành công!');

            if ($submitAction === 'buy_now') {
                redirect('checkout.php');
            } else {
                redirect('cart.php');
            }
        }
    }

    // 2. Cập nhật số lượng
    if ($action === 'update') {
        if (isset($_POST['quantities']) && is_array($_POST['quantities'])) {
            foreach ($_POST['quantities'] as $key => $qty) {
                $qty = (int)$qty;
                if ($qty <= 0) {
                    unset($_SESSION['cart'][$key]);
                } else if (isset($_SESSION['cart'][$key])) {
                    $_SESSION['cart'][$key]['quantity'] = $qty;
                }
            }
            set_flash('success', 'Đã cập nhật số lượng giỏ hàng!');
        }
        redirect('cart.php');
    }

    // 3. Áp dụng mã giảm giá
    if ($action === 'apply_coupon') {
        $couponCode = strtoupper(trim($_POST['coupon_code'] ?? ''));
        $subtotal = get_cart_subtotal();

        $cStmt = $pdo->prepare("SELECT * FROM coupons WHERE code = ? AND status = 1");
        $cStmt->execute([$couponCode]);
        $coupon = $cStmt->fetch();

        if ($coupon) {
            if ($coupon['expiry_date'] && strtotime($coupon['expiry_date']) < time()) {
                set_flash('danger', 'Mã giảm giá này đã hết hạn sử dụng.');
            } else if ($subtotal < $coupon['min_order_amount']) {
                set_flash('danger', 'Đơn hàng tối thiểu ' . format_price($coupon['min_order_amount']) . ' để sử dụng mã này.');
            } else {
                $_SESSION['coupon'] = [
                    'code' => $coupon['code'],
                    'type' => $coupon['discount_type'],
                    'value' => $coupon['discount_value']
                ];
                set_flash('success', 'Áp dụng mã giảm giá "' . $coupon['code'] . '" thành công!');
            }
        } else {
            set_flash('danger', 'Mã giảm giá không hợp lệ hoặc đã bị vô hiệu hóa.');
        }
        redirect('cart.php');
    }

    // 4. Xóa mã giảm giá
    if ($action === 'remove_coupon') {
        unset($_SESSION['coupon']);
        set_flash('info', 'Đã hủy mã giảm giá.');
        redirect('cart.php');
    }
}

// Xóa 1 sản phẩm khỏi giỏ qua GET
if (isset($_GET['remove'])) {
    $removeKey = $_GET['remove'];
    if (isset($_SESSION['cart'][$removeKey])) {
        unset($_SESSION['cart'][$removeKey]);
        set_flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
    redirect('cart.php');
}

$pageTitle = "Giỏ Hàng Của Bạn";
require_once __DIR__ . '/includes/header.php';

$cartItems = $_SESSION['cart'] ?? [];
$subtotal = get_cart_subtotal();

// Tính toán giảm giá và phí ship
$discount = 0;
if (isset($_SESSION['coupon'])) {
    $cp = $_SESSION['coupon'];
    if ($cp['type'] === 'percentage') {
        $discount = ($subtotal * $cp['value']) / 100;
    } else {
        $discount = $cp['value'];
    }
}

$shippingFee = ($subtotal >= 300000 || empty($cartItems)) ? 0 : 30000;
$grandTotal = max(0, $subtotal - $discount + $shippingFee);
?>

<div class="page-header-banner">
    <div class="container">
        <h1>Giỏ Hàng Mua Sắm</h1>
        <div class="breadcrumbs">
            <a href="index.php">Trang Chủ</a> / <span>Giỏ Hàng</span>
        </div>
    </div>
</div>

<div class="container">
    <?php if (empty($cartItems)): ?>
        <div style="background: #fff; border-radius: var(--radius-lg); padding: 60px 20px; text-align: center; border: 1px solid var(--border); margin-bottom: 60px;">
            <i class="fa-solid fa-cart-arrow-down" style="font-size: 4rem; color: #cbd5e1; margin-bottom: 20px;"></i>
            <h2>Giỏ hàng của bạn đang trống!</h2>
            <p style="color: var(--text-muted); margin: 10px 0 24px;">Hãy khám phá hàng ngàn mẫu quần áo thời trang cao cấp tại HieuMini ngay hôm nay.</p>
            <a href="products.php" class="btn btn-accent btn-lg"><i class="fa-solid fa-bag-shopping"></i> Khám Phá Sản Phẩm Ngay</a>
        </div>
    <?php else: ?>
        <form action="cart.php" method="POST">
            <input type="hidden" name="action" value="update">
            
            <div class="cart-layout">
                <!-- Danh sách sản phẩm trong giỏ -->
                <div class="cart-table-wrapper">
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
                            <?php foreach ($cartItems as $key => $item): ?>
                                <?php $itemSubtotal = $item['price'] * $item['quantity']; ?>
                                <tr>
                                    <td>
                                        <div class="cart-prod-cell">
                                            <img src="assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-prod-img">
                                            <div>
                                                <a href="product_detail.php?id=<?= $item['product_id'] ?>" class="cart-prod-title">
                                                    <?= htmlspecialchars($item['name']) ?>
                                                </a>
                                                <div class="cart-prod-spec">
                                                    <span>Size: <strong><?= htmlspecialchars($item['size']) ?></strong></span> | 
                                                    <span>Màu: <strong><?= htmlspecialchars($item['color']) ?></strong></span>
                                                </div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <strong><?= format_price($item['price']) ?></strong>
                                    </td>
                                    <td>
                                        <div class="quantity-control" style="transform: scale(0.9); transform-origin: left center;">
                                            <button type="button" class="qty-btn minus"><i class="fa-solid fa-minus"></i></button>
                                            <input type="number" name="quantities[<?= htmlspecialchars($key) ?>]" class="qty-input" value="<?= $item['quantity'] ?>" min="1">
                                            <button type="button" class="qty-btn plus"><i class="fa-solid fa-plus"></i></button>
                                        </div>
                                    </td>
                                    <td>
                                        <strong style="color: var(--accent);"><?= format_price($itemSubtotal) ?></strong>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="cart.php?remove=<?= urlencode($key) ?>" class="action-btn" title="Xóa món này" style="color: #ef4444;" onclick="return confirm('Bạn có chắc muốn xóa món này khỏi giỏ?');">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <div style="padding: 16px 20px; background: #f8fafc; border-top: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
                        <a href="products.php" class="btn btn-outline btn-sm">
                            <i class="fa-solid fa-arrow-left"></i> Tiếp Tục Mua Sắm
                        </a>
                        <button type="submit" class="btn btn-primary btn-sm">
                            <i class="fa-solid fa-arrows-rotate"></i> Cập Nhật Giỏ Hàng
                        </button>
                    </div>
                </div>

                <!-- Tóm tắt đơn hàng & Mã giảm giá -->
                <div>
                    <!-- Mã giảm giá Form -->
                    <div style="background: #fff; padding: 20px; border-radius: var(--radius-lg); border: 1px solid var(--border); margin-bottom: 20px;">
                        <h4 style="font-size: 1rem; margin-bottom: 12px;"><i class="fa-solid fa-ticket text-accent"></i> Mã Giảm Giá / Ưu Đãi</h4>
                        
                        <?php if (isset($_SESSION['coupon'])): ?>
                            <div style="background: var(--accent-light); border: 1px dashed var(--accent); padding: 12px 14px; border-radius: var(--radius-md); display: flex; justify-content: space-between; align-items: center;">
                                <div>
                                    <strong style="color: var(--accent);"><?= htmlspecialchars($_SESSION['coupon']['code']) ?></strong>
                                    <div style="font-size: 0.75rem; color: #92400e;">Đã áp dụng mã ưu đãi thành công</div>
                                </div>
                                <button type="submit" formaction="cart.php" name="action" value="remove_coupon" class="btn btn-outline btn-sm" style="padding: 4px 8px; font-size: 0.75rem; color: #ef4444; border-color: #fca5a5;">
                                    Gỡ bỏ
                                </button>
                            </div>
                        <?php else: ?>
                            <div class="coupon-form" style="margin-bottom: 0;">
                                <input type="text" name="coupon_code" class="coupon-input" placeholder="Nhập mã (VD: HIEUMINI10, FREESHIP)">
                                <button type="submit" formaction="cart.php" name="action" value="apply_coupon" class="btn btn-primary btn-sm">
                                    Áp Dụng
                                </button>
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-light); margin-top: 6px;">
                                Gợi ý mã: <strong>HIEUMINI10</strong> (Giảm 10%), <strong>FREESHIP</strong>
                            </div>
                        <?php endif; ?>
                    </div>

                    <!-- Bảng tóm tắt thanh toán -->
                    <div class="cart-summary-box">
                        <h3 class="summary-title">Tóm Tắt Đơn Hàng</h3>
                        
                        <div class="summary-row">
                            <span>Tạm tính hàng:</span>
                            <strong><?= format_price($subtotal) ?></strong>
                        </div>

                        <?php if ($discount > 0): ?>
                            <div class="summary-row" style="color: var(--success);">
                                <span>Mã giảm giá (<?= htmlspecialchars($_SESSION['coupon']['code']) ?>):</span>
                                <strong>-<?= format_price($discount) ?></strong>
                            </div>
                        <?php endif; ?>

                        <div class="summary-row">
                            <span>Phí vận chuyển:</span>
                            <span>
                                <?php if ($shippingFee == 0): ?>
                                    <strong style="color: var(--success);">Miễn Phí</strong>
                                <?php else: ?>
                                    <strong><?= format_price($shippingFee) ?></strong>
                                <?php endif; ?>
                            </span>
                        </div>

                        <?php if ($subtotal < 300000): ?>
                            <div style="font-size: 0.75rem; color: var(--accent); background: var(--accent-light); padding: 8px 10px; border-radius: var(--radius-sm); margin-bottom: 12px;">
                                <i class="fa-solid fa-circle-info"></i> Mua thêm <strong><?= format_price(300000 - $subtotal) ?></strong> để được <strong>FREESHIP</strong> toàn quốc!
                            </div>
                        <?php endif; ?>

                        <div class="summary-row total">
                            <span>Tổng thanh toán:</span>
                            <span style="color: #ef4444;"><?= format_price($grandTotal) ?></span>
                        </div>

                        <a href="checkout.php" class="btn btn-accent btn-lg btn-block" style="margin-top: 20px;">
                            Tiến Hành Đặt Hàng <i class="fa-solid fa-arrow-right"></i>
                        </a>
                    </div>
                </div>
            </div>
        </form>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
