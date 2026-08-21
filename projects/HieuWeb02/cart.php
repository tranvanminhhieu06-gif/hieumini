<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth_check.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = isset($_GET['action']) ? $_GET['action'] : '';

// 1. Xử lý Thêm vào giỏ (Hỗ trợ cả AJAX JSON và Form Submit)
if ($action === 'add') {
    $product_id = isset($_POST['product_id']) ? (int)$_POST['product_id'] : (isset($_GET['id']) ? (int)$_GET['id'] : 0);
    $quantity = isset($_POST['quantity']) ? (int)$_POST['quantity'] : 1;
    if ($quantity < 1) $quantity = 1;

    $product_data = null;
    if ($pdo && $product_id > 0) {
        try {
            $stmt = $pdo->prepare("SELECT id, name, slug, brand, price, sale_price, thumbnail FROM products WHERE id = ?");
            $stmt->execute([$product_id]);
            $product_data = $stmt->fetch();
        } catch (Exception $e) {}
    }

    // Fallback data nếu CSDL chưa kết nối
    if (!$product_data && $product_id > 0) {
        $mock_list = [
            1 => ['id' => 1, 'name' => 'iPhone 16 Pro Max 256GB Titan Sa Mạc', 'brand' => 'Apple', 'price' => 34990000, 'sale_price' => 31990000, 'thumbnail' => 'iphone16.png'],
            2 => ['id' => 2, 'name' => 'Samsung Galaxy S24 Ultra 5G 12GB/256GB', 'brand' => 'Samsung', 'price' => 31990000, 'sale_price' => 26990000, 'thumbnail' => 's24.png'],
            3 => ['id' => 3, 'name' => 'MacBook Pro 14 M3 Pro (18GB/512GB SSD)', 'brand' => 'Apple', 'price' => 49990000, 'sale_price' => 45490000, 'thumbnail' => 'mbp.png'],
            4 => ['id' => 4, 'name' => 'Laptop Gaming ASUS ROG Zephyrus G16 OLED', 'brand' => 'ASUS', 'price' => 54990000, 'sale_price' => 49990000, 'thumbnail' => 'rog.png'],
            7 => ['id' => 7, 'name' => 'Tai nghe Sony WH-1000XM5 Chống Ồn', 'brand' => 'Sony', 'price' => 8490000, 'sale_price' => 6990000, 'thumbnail' => 'sony.png'],
        ];
        $product_data = $mock_list[$product_id] ?? ['id' => $product_id, 'name' => 'Sản phẩm công nghệ #' . $product_id, 'brand' => 'HieuMini', 'price' => 5000000, 'sale_price' => 4500000, 'thumbnail' => 'prod.png'];
    }

    if ($product_data) {
        if (isset($_SESSION['cart'][$product_id])) {
            $_SESSION['cart'][$product_id]['quantity'] += $quantity;
        } else {
            $_SESSION['cart'][$product_id] = [
                'id' => $product_data['id'],
                'name' => $product_data['name'],
                'brand' => $product_data['brand'] ?? 'HieuMini',
                'price' => $product_data['price'],
                'sale_price' => $product_data['sale_price'],
                'quantity' => $quantity,
                'thumbnail' => $product_data['thumbnail'] ?? ''
            ];
        }

        // Nếu là yêu cầu AJAX fetch
        if (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) || (isset($_SERVER['CONTENT_TYPE']) && strpos($_SERVER['CONTENT_TYPE'], 'application/x-www-form-urlencoded') !== false && !isset($_POST['buy_now']))) {
            header('Content-Type: application/json');
            echo json_encode([
                'status' => 'success',
                'message' => 'Đã thêm ' . $product_data['name'] . ' vào giỏ hàng!',
                'cart_count' => get_cart_count()
            ]);
            exit;
        }

        // Nếu bấm "Mua Ngay" -> chuyển hướng đến trang checkout
        if (isset($_POST['buy_now'])) {
            header("Location: checkout.php");
            exit;
        }

        set_flash('success', 'Đã thêm sản phẩm vào giỏ hàng thành công!');
        header("Location: cart.php");
        exit;
    }
}

// 2. Cập nhật số lượng
if ($action === 'update' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $product_id = (int)$_POST['product_id'];
    $quantity = (int)$_POST['quantity'];
    if ($quantity <= 0) {
        unset($_SESSION['cart'][$product_id]);
    } else if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
    }
    header("Location: cart.php");
    exit;
}

// 3. Xóa sản phẩm khỏi giỏ
if ($action === 'remove') {
    $product_id = (int)$_GET['id'];
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        set_flash('info', 'Đã xóa sản phẩm khỏi giỏ hàng.');
    }
    header("Location: cart.php");
    exit;
}

// 4. Xóa toàn bộ giỏ
if ($action === 'clear') {
    $_SESSION['cart'] = [];
    unset($_SESSION['coupon']);
    set_flash('info', 'Đã làm trống giỏ hàng.');
    header("Location: cart.php");
    exit;
}

// 5. Áp dụng mã giảm giá Coupon
if ($action === 'apply_coupon' && $_SERVER['REQUEST_METHOD'] === 'POST') {
    $coupon_code = strtoupper(trim($_POST['coupon_code']));
    $cart_subtotal = get_cart_total();

    if ($coupon_code === 'HIEUMINI2026') {
        if ($cart_subtotal >= 5000000) {
            $_SESSION['coupon'] = [
                'code' => 'HIEUMINI2026',
                'discount_percent' => 10,
                'discount_amount' => $cart_subtotal * 0.1
            ];
            set_flash('success', 'Áp dụng mã giảm giá HIEUMINI2026 thành công! Bạn được giảm 10%.');
        } else {
            set_flash('danger', 'Mã HIEUMINI2026 chỉ áp dụng cho đơn hàng từ 5.000.000₫ trở lên.');
        }
    } elseif ($coupon_code === 'TECHNEW') {
        $_SESSION['coupon'] = [
            'code' => 'TECHNEW',
            'discount_percent' => 5,
            'discount_amount' => $cart_subtotal * 0.05
        ];
        set_flash('success', 'Áp dụng mã giảm giá TECHNEW thành công! Giảm 5%.');
    } else {
        set_flash('danger', 'Mã giảm giá không hợp lệ hoặc đã hết hạn!');
    }
    header("Location: cart.php");
    exit;
}

$page_title = 'Giỏ Hàng Của Bạn';
require_once __DIR__ . '/includes/header.php';

$cart = $_SESSION['cart'];
$subtotal = get_cart_total();
$discount = 0;
if (isset($_SESSION['coupon'])) {
    $discount = $_SESSION['coupon']['discount_amount'];
}
$shipping_fee = $subtotal > 2000000 ? 0 : ($subtotal > 0 ? 30000 : 0);
$total = max(0, $subtotal - $discount + $shipping_fee);
?>

<main class="container" style="margin: 30px auto 60px;">
    <!-- Breadcrumb -->
    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--text-muted); margin-bottom: 24px;">
        <a href="index.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <span>/</span>
        <span style="color: #fff;">Giỏ hàng (<?php echo count($cart); ?> loại sản phẩm)</span>
    </div>

    <?php if (!empty($cart)): ?>
        <div class="cart-layout">
            <!-- Bảng giỏ hàng -->
            <div>
                <div class="cart-table-wrap">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Sản phẩm</th>
                                <th>Đơn giá</th>
                                <th style="text-align: center;">Số lượng</th>
                                <th>Thành tiền</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart as $item): 
                                $price = !empty($item['sale_price']) && $item['sale_price'] > 0 ? $item['sale_price'] : $item['price'];
                                $item_total = $price * $item['quantity'];
                            ?>
                                <tr>
                                    <td>
                                        <div class="cart-item-info">
                                            <?php
                                            $c_thumb = !empty($item['thumbnail']) && file_exists(__DIR__ . '/assets/images/' . $item['thumbnail']) ? 'assets/images/' . $item['thumbnail'] : 'assets/images/default_prod.png';
                                            ?>
                                            <div class="cart-thumb">
                                                <img src="<?php echo $c_thumb; ?>" alt="<?php echo htmlspecialchars($item['name']); ?>" style="width: 100%; height: 100%; object-fit: cover; border-radius: var(--radius-sm);">
                                            </div>
                                            <div>
                                                <span style="font-size: 0.75rem; color: var(--accent); font-weight: 700;"><?php echo htmlspecialchars($item['brand']); ?></span>
                                                <h4 style="font-size: 0.95rem; font-weight: 600; color: #fff;">
                                                    <a href="product_detail.php?id=<?php echo $item['id']; ?>"><?php echo htmlspecialchars($item['name']); ?></a>
                                                </h4>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; color: #fff;"><?php echo format_currency($price); ?></span>
                                    </td>
                                    <td style="text-align: center;">
                                        <form action="cart.php?action=update" method="POST" style="display: inline-block;">
                                            <input type="hidden" name="product_id" value="<?php echo $item['id']; ?>">
                                            <div class="quantity-box">
                                                <button type="button" class="qty-btn qty-adjust-btn minus">-</button>
                                                <input type="number" name="quantity" class="qty-input" value="<?php echo $item['quantity']; ?>" min="1" max="99">
                                                <button type="button" class="qty-btn qty-adjust-btn plus">+</button>
                                            </div>
                                        </form>
                                    </td>
                                    <td>
                                        <span style="font-weight: 800; color: #f43f5e;"><?php echo format_currency($item_total); ?></span>
                                    </td>
                                    <td style="text-align: right;">
                                        <a href="cart.php?action=remove&id=<?php echo $item['id']; ?>" style="color: var(--danger); font-size: 1.1rem; padding: 6px;" title="Xóa">
                                            <i class="fa-solid fa-trash-can"></i>
                                        </a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 20px;">
                    <a href="products.php" class="btn btn-outline">
                        <i class="fa-solid fa-arrow-left"></i> Tiếp tục mua sắm
                    </a>
                    <a href="cart.php?action=clear" class="btn btn-outline" style="color: var(--danger); border-color: rgba(239, 68, 68, 0.4);" onclick="return confirm('Bạn có muốn xóa toàn bộ giỏ hàng?');">
                        <i class="fa-solid fa-trash"></i> Xóa toàn bộ giỏ hàng
                    </a>
                </div>
            </div>

            <!-- Cột Tóm tắt Đơn Hàng -->
            <div>
                <div class="order-summary-card">
                    <h3 style="font-size: 1.2rem; font-weight: 700; margin-bottom: 20px; border-bottom: var(--border-glass); padding-bottom: 12px;">
                        <i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Tóm Tắt Đơn Hàng
                    </h3>

                    <div class="summary-row">
                        <span>Tạm tính giỏ hàng:</span>
                        <strong style="color: #fff;"><?php echo format_currency($subtotal); ?></strong>
                    </div>

                    <?php if ($discount > 0): ?>
                        <div class="summary-row" style="color: var(--success);">
                            <span>Giảm giá (Mã <?php echo $_SESSION['coupon']['code']; ?>):</span>
                            <strong>-<?php echo format_currency($discount); ?></strong>
                        </div>
                    <?php endif; ?>

                    <div class="summary-row">
                        <span>Phí vận chuyển:</span>
                        <strong style="color: <?php echo $shipping_fee === 0 ? 'var(--success)' : '#fff'; ?>;">
                            <?php echo $shipping_fee === 0 ? 'MIỄN PHÍ' : format_currency($shipping_fee); ?>
                        </strong>
                    </div>

                    <?php if ($subtotal <= 2000000 && $subtotal > 0): ?>
                        <p style="font-size: 0.8rem; color: var(--accent); margin-bottom: 12px;">
                            <i class="fa-solid fa-circle-info"></i> Mua thêm <strong><?php echo format_currency(2000000 - $subtotal); ?></strong> để được Miễn phí giao hàng!
                        </p>
                    <?php endif; ?>

                    <div class="summary-row total">
                        <span>Tổng thanh toán:</span>
                        <span class="price-highlight"><?php echo format_currency($total); ?></span>
                    </div>

                    <!-- Mã giảm giá -->
                    <form action="cart.php?action=apply_coupon" method="POST" style="margin: 20px 0;">
                        <div style="display: flex; gap: 8px;">
                            <input type="text" name="coupon_code" class="form-control" placeholder="Nhập mã: HIEUMINI2026" required value="<?php echo $_SESSION['coupon']['code'] ?? ''; ?>" style="text-transform: uppercase;">
                            <button type="submit" class="btn btn-outline" style="white-space: nowrap;">Áp dụng</button>
                        </div>
                    </form>

                    <a href="checkout.php" class="btn btn-primary" style="width: 100%; padding: 14px; font-size: 1rem;">
                        <i class="fa-solid fa-credit-card"></i> TIẾN HÀNH THANH TOÁN
                    </a>
                </div>
            </div>
        </div>
    <?php else: ?>
        <div class="glass-panel" style="text-align: center; padding: 70px 20px;">
            <div style="width: 90px; height: 90px; border-radius: 50%; background: rgba(99, 102, 241, 0.15); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
                <i class="fa-solid fa-bag-shopping" style="font-size: 3rem; color: var(--primary);"></i>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 800; margin-bottom: 8px;">Giỏ hàng của bạn đang trống!</h2>
            <p style="color: var(--text-muted); margin-bottom: 24px;">Hãy khám phá hàng ngàn siêu phẩm công nghệ giảm giá cực sâu tại HieuMini.</p>
            <a href="products.php" class="btn btn-primary">
                <i class="fa-solid fa-cart-arrow-down"></i> Mua sắm ngay hôm nay
            </a>
        </div>
    <?php endif; ?>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
