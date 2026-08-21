<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - ORDER CONFIRMATION & RECEIPT
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/includes/config.php';

$order_code = sanitize($_GET['code'] ?? '');

if (empty($order_code)) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

// Lấy thông tin đơn hàng
$order_stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = ?");
$order_stmt->execute([$order_code]);
$order = $order_stmt->fetch();

if (!$order) {
    header("Location: " . BASE_URL . "/index.php");
    exit;
}

// Lấy danh sách sản phẩm trong đơn hàng
$items_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items_stmt->execute([$order['id']]);
$order_items = $items_stmt->fetchAll();

$page_title = "Xác Nhận Đơn Hàng " . htmlspecialchars($order['order_code']) . " | " . SITE_NAME;
$page_desc = "Đơn hàng của quý khách đã được ghi nhận thành công tại HieuMini Luxury Fitness Club.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <div style="max-width: 800px; margin: 0 auto;">
        <!-- Success Header Card -->
        <div class="category-card reveal" style="text-align: center; padding: 3rem 2rem; margin-bottom: 2.5rem; border-color: var(--border-gold);">
            <div style="width: 85px; height: 85px; background: rgba(16, 185, 129, 0.15); border: 2px solid var(--emerald-accent); border-radius: 50%; display: flex; align-items: center; justify-content: center; margin: 0 auto 1.5rem; color: var(--emerald-accent); font-size: 2.5rem;">
                <i class="fas fa-check"></i>
            </div>
            <span class="badge badge-emerald" style="margin-bottom: 0.75rem;">ĐẶT HÀNG THÀNH CÔNG</span>
            <h1 style="font-size: 2.2rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem;">
                CẢM ƠN QUÝ KHÁCH ĐÃ TIN CHỌN HIEUMINI!
            </h1>
            <p style="color: var(--text-secondary); font-size: 1.05rem; line-height: 1.6;">
                Mã đơn hàng của quý khách là <strong style="color: var(--gold-light); font-size: 1.2rem;"><?= htmlspecialchars($order['order_code']) ?></strong>.
                Chuyên viên tư vấn VIP sẽ liên hệ trong vòng 15 phút để xác nhận và sắp xếp giao hàng.
            </p>
        </div>

        <!-- Receipt Details Card -->
        <div class="form-card reveal delay-1" id="printable-receipt" style="margin-bottom: 2.5rem;">
            <div style="display: flex; align-items: center; justify-content: space-between; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1.25rem; margin-bottom: 1.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h3 style="font-size: 1.3rem; color: var(--gold-light);"><i class="fas fa-receipt"></i> HÓA ĐƠN ĐIỆN TỬ VIP</h3>
                    <span style="font-size: 0.85rem; color: var(--text-muted);">Ngày đặt: <?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?></span>
                </div>
                <div>
                    <span class="badge badge-gold">TRẠNG THÁI: <?= strtoupper($order['order_status']) ?></span>
                </div>
            </div>

            <!-- Customer Details Grid -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1.5rem; margin-bottom: 2rem; font-size: 0.95rem;">
                <div>
                    <strong style="color: var(--gold-primary); display: block; margin-bottom: 0.35rem;">THÔNG TIN NGƯỜI NHẬN:</strong>
                    <div style="color: #fff; font-weight: 600;"><?= htmlspecialchars($order['customer_name']) ?></div>
                    <div style="color: var(--text-secondary);">SĐT: <?= htmlspecialchars($order['customer_phone']) ?></div>
                    <?php if ($order['customer_email']): ?>
                        <div style="color: var(--text-secondary);">Email: <?= htmlspecialchars($order['customer_email']) ?></div>
                    <?php endif; ?>
                </div>
                <div>
                    <strong style="color: var(--gold-primary); display: block; margin-bottom: 0.35rem;">ĐỊA CHỈ GIAO HÀNG:</strong>
                    <div style="color: #fff;"><?= htmlspecialchars($order['customer_address']) ?></div>
                    <div style="color: var(--text-muted); font-size: 0.85rem; margin-top: 0.3rem;">Phương thức: <?= strtoupper($order['payment_method']) ?></div>
                </div>
            </div>

            <!-- Item Table -->
            <table class="cart-table" style="margin-bottom: 1.5rem;">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th style="text-align: center;">SL</th>
                        <th style="text-align: right;">Đơn giá</th>
                        <th style="text-align: right;">Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($order_items as $oi): ?>
                    <tr>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($oi['product_image']) ?>" style="width: 42px; height: 42px; border-radius: 4px; object-fit: cover;">
                                <span style="color: #fff; font-weight: 600;"><?= htmlspecialchars($oi['product_name']) ?></span>
                            </div>
                        </td>
                        <td style="text-align: center; color: var(--text-primary); font-weight: 700;">x<?= $oi['quantity'] ?></td>
                        <td style="text-align: right; color: var(--text-secondary);"><?= format_currency($oi['price']) ?></td>
                        <td style="text-align: right; color: var(--gold-light); font-weight: 700;"><?= format_currency($oi['subtotal']) ?></td>
                    </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Totals -->
            <div style="margin-left: auto; max-width: 320px; display: flex; flex-direction: column; gap: 0.6rem; font-size: 0.95rem;">
                <div style="display: flex; justify-content: space-between; color: var(--text-secondary);">
                    <span>Tạm tính:</span>
                    <strong style="color: #fff;"><?= format_currency($order['subtotal']) ?></strong>
                </div>
                <?php if ($order['discount_amount'] > 0): ?>
                <div style="display: flex; justify-content: space-between; color: var(--emerald-accent);">
                    <span>Ưu đãi Voucher:</span>
                    <strong>-<?= format_currency($order['discount_amount']) ?></strong>
                </div>
                <?php endif; ?>
                <div style="display: flex; justify-content: space-between; color: var(--text-secondary);">
                    <span>Vận chuyển VIP:</span>
                    <strong style="color: var(--emerald-accent);">MIỄN PHÍ</strong>
                </div>
                <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; border-top: 1px solid var(--border-subtle); padding-top: 0.75rem; color: #fff;">
                    <span>Tổng Thanh Toán:</span>
                    <span style="color: var(--gold-light); font-family: 'Outfit', sans-serif;"><?= format_currency($order['total_amount']) ?></span>
                </div>
            </div>

            <!-- If Bank Transfer: Display VietQR payment block -->
            <?php if ($order['payment_method'] === 'bank_transfer'): ?>
            <div style="margin-top: 2.5rem; padding: 1.75rem; background: rgba(6, 182, 212, 0.08); border: 1.5px dashed var(--cyan-accent); border-radius: var(--radius-sm); text-align: center;">
                <h4 style="color: var(--cyan-accent); font-size: 1.15rem; margin-bottom: 0.75rem;">
                    <i class="fas fa-qrcode"></i> QUÉT MÃ VIETQR ĐỂ THANH TOÁN TỰ ĐỘNG
                </h4>
                <p style="color: #cbd5e1; font-size: 0.9rem; margin-bottom: 1.25rem;">
                    Mở ứng dụng ngân hàng hoặc ví điện tử bất kỳ để quét mã chuyển khoản nhanh 24/7.
                </p>
                <div style="display: inline-block; background: #fff; padding: 12px; border-radius: 8px; box-shadow: 0 4px 15px rgba(0,0,0,0.5); margin-bottom: 1rem;">
                    <img src="https://api.vietqr.io/image/970422-888899998888-qr_only.jpg?amount=<?= (int)$order['total_amount'] ?>&addInfo=<?= urlencode($order['order_code']) ?>" alt="VietQR" style="width: 200px; height: 200px; object-fit: contain;">
                </div>
                <p style="font-size: 0.85rem; color: var(--text-muted);">
                    Nội dung chuyển khoản: <strong style="color: var(--gold-light);"><?= htmlspecialchars($order['order_code']) ?></strong> | Số tiền: <strong style="color: var(--gold-light);"><?= format_currency($order['total_amount']) ?></strong>
                </p>
            </div>
            <?php endif; ?>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;" class="reveal delay-2">
            <button type="button" class="btn btn-secondary btn-lg" onclick="window.print()">
                <i class="fas fa-print"></i> IN HÓA ĐƠN
            </button>
            <a href="<?= BASE_URL ?>/products.php" class="btn btn-primary btn-lg btn-shimmer">
                <i class="fas fa-shopping-bag"></i> TIẾP TỤC MUA SẮM
            </a>
            <a href="<?= BASE_URL ?>/index.php" class="btn btn-gold-outline btn-lg">
                <i class="fas fa-home"></i> VỀ TRANG CHỦ
            </a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
