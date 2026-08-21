<?php
/**
 * Trang Xác Nhận Đặt Hàng Thành Công HieuMini
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$orderCode = $_GET['code'] ?? '';
if (!$orderCode) {
    redirect('index.php');
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = ?");
$stmt->execute([$orderCode]);
$order = $stmt->fetch();

if (!$order) {
    redirect('index.php');
}

$itemStmt = $pdo->prepare("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$itemStmt->execute([$order['id']]);
$orderItems = $itemStmt->fetchAll();

$pageTitle = "Đặt Hàng Thành Công";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 40px 20px 80px;">
    <div style="max-width: 750px; margin: 0 auto; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 40px; box-shadow: var(--shadow-md);">
        <!-- Icon Success Animation -->
        <div style="text-align: center; margin-bottom: 24px;">
            <div style="width: 80px; height: 80px; background: #dcfce7; color: var(--success); border-radius: 999px; display: inline-flex; align-items: center; justify-content: center; font-size: 2.5rem; margin-bottom: 16px;">
                <i class="fa-solid fa-check"></i>
            </div>
            <h2 style="font-size: 1.8rem; color: var(--primary);">Đặt Hàng Thành Công!</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 6px;">
                Cảm ơn bạn đã tin tưởng mua sắm tại <strong>HieuMini Fashion Studio</strong>. Chúng tôi đã nhận được thông tin đơn hàng và sẽ liên hệ giao hàng sớm nhất.
            </p>
        </div>

        <!-- Order Metadata Pill -->
        <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 18px; margin-bottom: 24px; display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 0.875rem;">
            <div>
                <span style="color: var(--text-muted);">Mã đơn hàng:</span>
                <strong style="color: var(--accent); font-size: 1rem; display: block;"><?= htmlspecialchars($order['order_code']) ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted);">Ngày đặt:</span>
                <strong style="display: block;"><?= format_datetime($order['created_at']) ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted);">Phương thức thanh toán:</span>
                <strong style="display: block; text-transform: uppercase;"><?= $order['payment_method'] === 'cod' ? 'Thanh toán khi nhận hàng (COD)' : 'Chuyển khoản Ngân hàng' ?></strong>
            </div>
            <div>
                <span style="color: var(--text-muted);">Trạng thái đơn:</span>
                <div style="margin-top: 2px;"><?= get_order_status_badge($order['order_status']) ?></div>
            </div>
        </div>

        <!-- Thông tin giao hàng -->
        <div style="margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
            <h4 style="font-size: 1rem; margin-bottom: 10px; color: var(--primary);"><i class="fa-solid fa-location-dot text-accent"></i> Địa Chỉ Nhận Hàng</h4>
            <div style="font-size: 0.9rem; line-height: 1.8; color: var(--secondary);">
                <div>Người nhận: <strong><?= htmlspecialchars($order['customer_name']) ?></strong> - <strong><?= htmlspecialchars($order['customer_phone']) ?></strong></div>
                <div>Địa chỉ: <?= htmlspecialchars($order['shipping_address']) ?></div>
                <?php if (!empty($order['notes'])): ?>
                    <div>Ghi chú: <em><?= htmlspecialchars($order['notes']) ?></em></div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Danh sách sản phẩm -->
        <div style="margin-bottom: 24px;">
            <h4 style="font-size: 1rem; margin-bottom: 12px; color: var(--primary);"><i class="fa-solid fa-bag-shopping text-accent"></i> Chi Tiết Sản Phẩm</h4>
            <table class="cart-table" style="font-size: 0.875rem;">
                <thead>
                    <tr>
                        <th>Sản phẩm</th>
                        <th>Đơn giá</th>
                        <th>SL</th>
                        <th>Thành tiền</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orderItems as $it): ?>
                        <tr>
                            <td>
                                <strong><?= htmlspecialchars($it['product_name']) ?></strong>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">Size: <?= htmlspecialchars($it['size']) ?> | Màu: <?= htmlspecialchars($it['color']) ?></div>
                            </td>
                            <td><?= format_price($it['price']) ?></td>
                            <td>x<?= $it['quantity'] ?></td>
                            <td><strong><?= format_price($it['subtotal']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>

        <!-- Tổng tiền -->
        <div style="background: #f8fafc; border-radius: var(--radius-md); padding: 16px 20px; margin-bottom: 30px;">
            <div class="summary-row">
                <span>Phí vận chuyển:</span>
                <span><?= $order['shipping_fee'] == 0 ? '<strong style="color: var(--success);">Miễn Phí</strong>' : format_price($order['shipping_fee']) ?></span>
            </div>
            <?php if ($order['discount_amount'] > 0): ?>
                <div class="summary-row" style="color: var(--success);">
                    <span>Giảm giá (<?= htmlspecialchars($order['coupon_code'] ?? '') ?>):</span>
                    <span>-<?= format_price($order['discount_amount']) ?></span>
                </div>
            <?php endif; ?>
            <div class="summary-row total" style="border-top: 1px solid var(--border); margin-top: 10px; padding-top: 10px;">
                <span>Tổng giá trị đơn hàng:</span>
                <span style="color: #ef4444; font-size: 1.3rem;"><?= format_price($order['total_amount']) ?></span>
            </div>
        </div>

        <!-- Nút thao tác -->
        <div style="display: flex; gap: 14px; justify-content: center; flex-wrap: wrap;">
            <a href="index.php" class="btn btn-outline">
                <i class="fa-solid fa-house"></i> Về Trang Chủ
            </a>
            <a href="my_orders.php" class="btn btn-primary">
                <i class="fa-solid fa-clock-rotate-left"></i> Theo Dõi Đơn Hàng
            </a>
            <button onclick="window.print()" class="btn btn-accent">
                <i class="fa-solid fa-print"></i> In Hóa Đơn
            </button>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
