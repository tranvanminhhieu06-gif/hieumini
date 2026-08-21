<?php
/**
 * Quản Lý Đơn Hàng - Admin HieuMini
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Cập nhật nhanh trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'quick_update_status') {
    $orderId = (int)$_POST['order_id'];
    $newStatus = $_POST['order_status'];
    $pdo->prepare("UPDATE orders SET order_status = ? WHERE id = ?")->execute([$newStatus, $orderId]);
    set_flash('success', 'Đã cập nhật trạng thái đơn hàng thành công!');
    redirect('orders.php');
}

$statusFilter = $_GET['status'] ?? '';
$keyword = trim($_GET['keyword'] ?? '');

$query = "SELECT * FROM orders WHERE 1=1";
$params = [];

if ($statusFilter) {
    $query .= " AND order_status = ?";
    $params[] = $statusFilter;
}

if ($keyword) {
    $query .= " AND (order_code LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

$query .= " ORDER BY id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$adminTitle = "Quản Lý Đơn Đặt Hàng";
require_once __DIR__ . '/includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-size: 1.4rem; color: var(--primary); margin-bottom: 4px;">Danh Sách Đơn Hàng (<?= count($orders) ?>)</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Theo dõi, xử lý và cập nhật tiến độ giao hàng cho khách</p>
    </div>
</div>

<!-- Toolbar Bộ Lọc Đơn Hàng -->
<div class="admin-card" style="margin-bottom: 20px;">
    <div class="admin-card-body" style="padding: 16px 20px;">
        <form method="GET" style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm theo Mã đơn, Tên KH, SĐT..." value="<?= htmlspecialchars($keyword) ?>">
            </div>

            <div style="width: 200px;">
                <select name="status" class="form-control">
                    <option value="">-- Tất cả trạng thái --</option>
                    <option value="pending" <?= $statusFilter === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                    <option value="processing" <?= $statusFilter === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                    <option value="shipping" <?= $statusFilter === 'shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
                    <option value="completed" <?= $statusFilter === 'completed' ? 'selected' : '' ?>>Đã hoàn thành</option>
                    <option value="cancelled" <?= $statusFilter === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Lọc Đơn Hàng
            </button>

            <?php if ($keyword || $statusFilter): ?>
                <a href="orders.php" class="btn btn-outline btn-sm">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Bảng Đơn Hàng -->
<div class="admin-card">
    <div class="admin-card-body" style="padding: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th>Mã Đơn Hàng</th>
                    <th>Ngày Đặt</th>
                    <th>Khách Hàng</th>
                    <th>Địa Chỉ Giao Hàng</th>
                    <th>Tổng Tiền</th>
                    <th>Thanh Toán</th>
                    <th>Trạng Thái</th>
                    <th style="text-align: center;">Thao Tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($orders)): ?>
                    <tr><td colspan="8" style="text-align: center; padding: 30px;">Không có đơn hàng nào.</td></tr>
                <?php else: ?>
                    <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--accent);"><?= htmlspecialchars($ord['order_code']) ?></strong>
                            </td>
                            <td><?= format_datetime($ord['created_at']) ?></td>
                            <td>
                                <div style="font-weight: 700; color: var(--primary);"><?= htmlspecialchars($ord['customer_name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                            </td>
                            <td>
                                <div style="font-size: 0.8rem; max-width: 220px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;" title="<?= htmlspecialchars($ord['shipping_address']) ?>">
                                    <?= htmlspecialchars($ord['shipping_address']) ?>
                                </div>
                            </td>
                            <td>
                                <strong style="color: #ef4444;"><?= format_price($ord['total_amount']) ?></strong>
                            </td>
                            <td>
                                <span class="badge <?= $ord['payment_status'] === 'paid' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= $ord['payment_status'] === 'paid' ? 'Đã TT' : 'Chưa TT' ?>
                                </span>
                            </td>
                            <td>
                                <form action="orders.php" method="POST" style="margin: 0;">
                                    <input type="hidden" name="action" value="quick_update_status">
                                    <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                                    <select name="order_status" onchange="this.form.submit()" style="font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; border: 1px solid var(--border); background: #fff;">
                                        <option value="pending" <?= $ord['order_status'] === 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                        <option value="processing" <?= $ord['order_status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                                        <option value="shipping" <?= $ord['order_status'] === 'shipping' ? 'selected' : '' ?>>Đang giao</option>
                                        <option value="completed" <?= $ord['order_status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                                        <option value="cancelled" <?= $ord['order_status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                    </select>
                                </form>
                            </td>
                            <td style="text-align: center;">
                                <a href="order_detail.php?id=<?= $ord['id'] ?>" class="btn btn-outline btn-sm" title="Xem chi tiết & In đơn">
                                    <i class="fa-solid fa-eye"></i> Chi tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
