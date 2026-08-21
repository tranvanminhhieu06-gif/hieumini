<?php
/**
 * Chi Tiết Đơn Hàng & In Hóa Đơn - Admin HieuMini
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    redirect('orders.php');
}

// Xử lý cập nhật trạng thái
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_order') {
    $orderStatus = $_POST['order_status'];
    $paymentStatus = $_POST['payment_status'];
    
    $upd = $pdo->prepare("UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?");
    $upd->execute([$orderStatus, $paymentStatus, $id]);
    set_flash('success', 'Đã cập nhật trạng thái đơn hàng thành công!');
    redirect("order_detail.php?id=$id");
}

$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    redirect('orders.php');
}

$itemStmt = $pdo->prepare("SELECT oi.*, p.image FROM order_items oi LEFT JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
$itemStmt->execute([$id]);
$orderItems = $itemStmt->fetchAll();

$adminTitle = "Chi Tiết Đơn Hàng: " . $order['order_code'];
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 24px; display: flex; justify-content: space-between; align-items: center;">
    <a href="orders.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
    <button onclick="window.print()" class="btn btn-primary btn-sm"><i class="fa-solid fa-print"></i> In Hóa Đơn</button>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
    <!-- Thông tin đơn hàng & Danh sách món -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title">
                <i class="fa-solid fa-file-invoice text-accent"></i> Hóa Đơn #<?= htmlspecialchars($order['order_code']) ?>
            </h3>
            <span style="font-size: 0.85rem; color: var(--text-muted);"><?= format_datetime($order['created_at']) ?></span>
        </div>
        <div class="admin-card-body">
            <!-- Thông tin khách hàng -->
            <div style="background: #f8fafc; border-radius: var(--radius-md); padding: 18px; margin-bottom: 24px; border: 1px solid var(--border);">
                <h4 style="font-size: 0.95rem; margin-bottom: 8px; color: var(--primary);"><i class="fa-solid fa-user text-accent"></i> Khách Hàng Giao Nhận:</h4>
                <div style="font-size: 0.875rem; line-height: 1.8; color: var(--secondary);">
                    <div>Họ và tên: <strong><?= htmlspecialchars($order['customer_name']) ?></strong></div>
                    <div>Số điện thoại: <strong><?= htmlspecialchars($order['customer_phone']) ?></strong></div>
                    <div>Email: <?= htmlspecialchars($order['customer_email'] ?: 'Chưa cập nhật') ?></div>
                    <div>Địa chỉ nhận: <strong><?= htmlspecialchars($order['shipping_address']) ?></strong></div>
                    <?php if (!empty($order['notes'])): ?>
                        <div>Ghi chú: <em><?= htmlspecialchars($order['notes']) ?></em></div>
                    <?php endif; ?>
                </div>
            </div>

            <!-- Bảng sản phẩm -->
            <h4 style="font-size: 1rem; margin-bottom: 12px; color: var(--primary);"><i class="fa-solid fa-bag-shopping text-accent"></i> Sản Phẩm Đã Mua</h4>
            <table class="admin-table" style="margin-bottom: 20px;">
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
                            <td><strong style="color: var(--primary);"><?= format_price($it['subtotal']) ?></strong></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>

            <!-- Tổng kết tiền -->
            <div style="background: #f8fafc; border-radius: var(--radius-md); padding: 16px 20px;">
                <div class="summary-row" style="display: flex; justify-content: space-between; margin-bottom: 8px;">
                    <span>Phí vận chuyển:</span>
                    <span><?= $order['shipping_fee'] == 0 ? 'Miễn phí' : format_price($order['shipping_fee']) ?></span>
                </div>
                <?php if ($order['discount_amount'] > 0): ?>
                    <div class="summary-row" style="display: flex; justify-content: space-between; color: var(--success); margin-bottom: 8px;">
                        <span>Giảm giá (<?= htmlspecialchars($order['coupon_code'] ?? '') ?>):</span>
                        <span>-<?= format_price($order['discount_amount']) ?></span>
                    </div>
                <?php endif; ?>
                <div class="summary-row total" style="display: flex; justify-content: space-between; border-top: 1px solid var(--border); padding-top: 10px; font-weight: 800; font-size: 1.15rem;">
                    <span>Tổng hóa đơn:</span>
                    <span style="color: #ef4444;"><?= format_price($order['total_amount']) ?></span>
                </div>
            </div>
        </div>
    </div>

    <!-- Cột phải: Cập nhật trạng thái -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-solid fa-pen-to-square text-accent"></i> Cập Nhật Tiến Độ</h3>
        </div>
        <div class="admin-card-body">
            <form action="order_detail.php?id=<?= $id ?>" method="POST">
                <input type="hidden" name="action" value="update_order">

                <div class="admin-form-group">
                    <label class="admin-form-label">Trạng thái đơn hàng:</label>
                    <select name="order_status" class="form-control">
                        <option value="pending" <?= $order['order_status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                        <option value="processing" <?= $order['order_status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý (Đóng gói)</option>
                        <option value="shipping" <?= $order['order_status'] === 'shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
                        <option value="completed" <?= $order['order_status'] === 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                        <option value="cancelled" <?= $order['order_status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy đơn</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">Tình trạng thanh toán:</label>
                    <select name="payment_status" class="form-control">
                        <option value="unpaid" <?= $order['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Chưa thanh toán</option>
                        <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                    </select>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">Phương thức thanh toán:</label>
                    <input type="text" class="form-control" value="<?= strtoupper($order['payment_method']) ?>" readonly style="background: #f1f5f9;">
                </div>

                <button type="submit" class="btn btn-accent btn-block" style="margin-top: 20px;">
                    <i class="fa-solid fa-check"></i> Lưu Cập Nhật
                </button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
