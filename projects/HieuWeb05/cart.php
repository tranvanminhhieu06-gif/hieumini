<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - SHOPPING CART
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/includes/config.php';

// Xử lý các hành động POST từ giỏ hàng nếu không dùng AJAX
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['remove_item'])) {
        $remove_id = (int)$_POST['product_id'];
        unset($_SESSION['cart'][$remove_id]);
        set_flash('success', 'Đã xóa sản phẩm khỏi giỏ hàng.');
        header("Location: " . BASE_URL . "/cart.php");
        exit;
    }
    if (isset($_POST['clear_cart'])) {
        $_SESSION['cart'] = [];
        unset($_SESSION['applied_coupon']);
        set_flash('info', 'Đã làm trống giỏ hàng.');
        header("Location: " . BASE_URL . "/cart.php");
        exit;
    }
}

$cart_items = $_SESSION['cart'] ?? [];
$subtotal = get_cart_subtotal();
$discount = get_cart_discount();
$total = get_cart_total();

$page_title = "Giỏ Hàng Của Bạn | " . SITE_NAME;
$page_desc = "Xem và thanh toán các sản phẩm dinh dưỡng, thiết bị gym và gói hội viên đã chọn.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <!-- Breadcrumb & Title -->
    <div style="margin-bottom: 2.5rem;" class="reveal">
        <div style="font-size: 0.85rem; color: var(--gold-primary); margin-bottom: 0.5rem;">
            <a href="<?= BASE_URL ?>/index.php">Trang Chủ</a> / <span>Giỏ Hàng</span>
        </div>
        <h1 style="font-size: 2.6rem; font-weight: 800;">GIỎ HÀNG THỂ HÌNH VIP</h1>
    </div>

    <?php if (empty($cart_items)): ?>
        <!-- Empty Cart State -->
        <div class="category-card reveal" style="padding: 5rem 2rem; text-align: center; max-width: 650px; margin: 0 auto;">
            <div style="width: 90px; height: 90px; background: rgba(245,158,11,0.1); border: 1px solid var(--border-gold); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--gold-light); font-size: 2.5rem;">
                <i class="fas fa-shopping-bag"></i>
            </div>
            <h2 style="font-size: 1.6rem; color: #fff; margin-bottom: 0.75rem;">Giỏ hàng của bạn đang trống</h2>
            <p style="color: var(--text-secondary); margin-bottom: 2rem; line-height: 1.6;">
                Khám phá ngay 30 sản phẩm dinh dưỡng thể hình, máy tập Olympic và gói thẻ hội viên chuẩn CEO của HieuMini.
            </p>
            <a href="<?= BASE_URL ?>/products.php" class="btn btn-primary btn-lg btn-shimmer">
                <i class="fas fa-dumbbell"></i> KHÁM PHÁ CỬA HÀNG NGAY
            </a>
        </div>
    <?php else: ?>
        <!-- Cart Layout -->
        <div class="cart-layout">
            <!-- Left: Table of items -->
            <div class="reveal">
                <div class="cart-table-card">
                    <table class="cart-table">
                        <thead>
                            <tr>
                                <th>Sản Phẩm & Gói Tập</th>
                                <th>Đơn Giá</th>
                                <th style="text-align: center;">Số Lượng</th>
                                <th>Thành Tiền</th>
                                <th style="text-align: center;">Xóa</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($cart_items as $item): 
                                $item_subtotal = (float)$item['price'] * (int)$item['quantity'];
                            ?>
                            <tr>
                                <td>
                                    <div class="cart-product-cell">
                                        <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($item['image']) ?>" alt="<?= htmlspecialchars($item['name']) ?>" class="cart-product-img">
                                        <div>
                                            <h4 style="font-size: 1.05rem; font-weight: 700; color: #fff; margin-bottom: 0.3rem;">
                                                <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $item['id'] ?>"><?= htmlspecialchars($item['name']) ?></a>
                                            </h4>
                                            <span style="font-size: 0.8rem; color: var(--gold-primary); font-weight: 600;">SKU: <?= htmlspecialchars($item['sku'] ?? 'HM-PROD') ?></span>
                                        </div>
                                    </div>
                                </td>
                                <td style="font-weight: 700; color: var(--text-primary);">
                                    <?= format_currency($item['price']) ?>
                                </td>
                                <td style="text-align: center;">
                                    <div class="qty-stepper" style="display: inline-flex;">
                                        <button type="button" class="qty-btn qty-minus">-</button>
                                        <input type="number" class="qty-input cart-qty-input" data-id="<?= $item['id'] ?>" value="<?= $item['quantity'] ?>" min="1" max="99">
                                        <button type="button" class="qty-btn qty-plus">+</button>
                                    </div>
                                </td>
                                <td style="font-weight: 800; color: var(--gold-light); font-family: 'Outfit', sans-serif; font-size: 1.15rem;">
                                    <?= format_currency($item_subtotal) ?>
                                </td>
                                <td style="text-align: center;">
                                    <form action="<?= BASE_URL ?>/cart.php" method="POST" onsubmit="return confirm('Bạn có chắc muốn xóa sản phẩm này?');">
                                        <input type="hidden" name="product_id" value="<?= $item['id'] ?>">
                                        <button type="submit" name="remove_item" class="action-btn-circle" style="width: 34px; height: 34px; margin: 0 auto; color: var(--ruby-accent);" title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>

                <!-- Bottom Actions -->
                <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                    <a href="<?= BASE_URL ?>/products.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-arrow-left"></i> Tiếp Tục Mua Sắm
                    </a>
                    <form action="<?= BASE_URL ?>/cart.php" method="POST" onsubmit="return confirm('Bạn muốn xóa tất cả sản phẩm trong giỏ hàng?');">
                        <button type="submit" name="clear_cart" class="btn btn-secondary btn-sm" style="color: var(--ruby-accent);">
                            <i class="fas fa-trash"></i> Làm Trống Giỏ Hàng
                        </button>
                    </form>
                </div>
            </div>

            <!-- Right: Order Summary Card -->
            <div class="reveal delay-1">
                <div class="cart-summary-card">
                    <h3 style="font-size: 1.35rem; font-weight: 800; color: #fff; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
                        TỔNG QUAN ĐƠN HÀNG
                    </h3>

                    <div class="summary-row">
                        <span>Tạm tính (<?= get_cart_count() ?> sản phẩm)</span>
                        <strong style="color: #fff;"><?= format_currency($subtotal) ?></strong>
                    </div>

                    <?php if ($discount > 0): ?>
                    <div class="summary-row" style="color: var(--emerald-accent);">
                        <span>Ưu đãi Voucher (<?= htmlspecialchars($_SESSION['applied_coupon']) ?>)</span>
                        <strong>-<?= format_currency($discount) ?></strong>
                    </div>
                    <?php endif; ?>

                    <div class="summary-row">
                        <span>Phí vận chuyển VIP</span>
                        <strong style="color: var(--emerald-accent);">MIỄN PHÍ TOÀN QUỐC</strong>
                    </div>

                    <!-- Coupon Form -->
                    <form id="coupon-form" class="coupon-input-group">
                        <input type="text" name="coupon_code" placeholder="MÃ GIẢM GIÁ (VD: CEOFIT20)" value="<?= htmlspecialchars($_SESSION['applied_coupon'] ?? '') ?>">
                        <button type="submit" class="btn btn-primary btn-sm">ÁP DỤNG</button>
                    </form>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                        Gợi ý: Dùng mã <strong style="color: var(--gold-light);">CEOFIT20</strong> (Giảm 20%) hoặc <strong style="color: var(--gold-light);">HIEUMINI10</strong> (Giảm 10%).
                    </p>

                    <div class="summary-row total">
                        <span>Tổng Thanh Toán</span>
                        <span class="total-amount"><?= format_currency($total) ?></span>
                    </div>

                    <div style="margin-top: 2rem;">
                        <a href="<?= BASE_URL ?>/checkout.php" class="btn btn-primary btn-block btn-lg btn-shimmer">
                            <i class="fas fa-lock"></i> TIẾN HÀNH THANH TOÁN
                        </a>
                    </div>

                    <div style="margin-top: 1.5rem; text-align: center; font-size: 0.8rem; color: var(--text-muted); display: flex; align-items: center; justify-content: center; gap: 0.5rem;">
                        <i class="fas fa-shield-alt" style="color: var(--emerald-accent);"></i>
                        Bảo mật thanh toán SSL 256-bit chuẩn quốc tế
                    </div>
                </div>
            </div>
        </div>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
