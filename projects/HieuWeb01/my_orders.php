<?php
/**
 * Trang Lịch Sử Đơn Hàng Của Khách Hàng HieuMini
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$currentUser = current_user($pdo);
$searchPhone = trim($_GET['search_phone'] ?? '');

$orders = [];
if ($currentUser) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
    $stmt->execute([$currentUser['id']]);
    $orders = $stmt->fetchAll();
} else if ($searchPhone) {
    $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_phone = ? OR order_code = ? ORDER BY id DESC");
    $stmt->execute([$searchPhone, $searchPhone]);
    $orders = $stmt->fetchAll();
}

$pageTitle = "Đơn Hàng Của Tôi";
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header-banner">
    <div class="container">
        <h1>Lịch Sử & Tra Cứu Đơn Hàng</h1>
        <div class="breadcrumbs">
            <a href="index.php">Trang Chủ</a> / <span>Đơn Hàng Của Tôi</span>
        </div>
    </div>
</div>

<div class="container" style="margin-bottom: 60px;">
    <!-- Box Tra cứu nhanh dành cho khách vãng lai -->
    <?php if (!$currentUser): ?>
        <div style="background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 24px; margin-bottom: 30px; box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 1.1rem; margin-bottom: 12px;"><i class="fa-solid fa-magnifying-glass text-accent"></i> Tra Cứu Đơn Hàng Nhanh</h3>
            <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 16px;">Nhập số điện thoại hoặc mã đơn hàng của bạn để kiểm tra tình trạng xử lý:</p>
            <form action="my_orders.php" method="GET" style="display: flex; gap: 12px; max-width: 500px;">
                <input type="text" name="search_phone" class="form-control" placeholder="Nhập SĐT hoặc Mã đơn (VD: HM-ORD-...)" value="<?= htmlspecialchars($searchPhone) ?>" required>
                <button type="submit" class="btn btn-primary" style="white-space: nowrap;">Tra Cứu</button>
            </form>
        </div>
    <?php endif; ?>

    <!-- Bảng danh sách đơn hàng -->
    <div style="background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border); overflow: hidden; box-shadow: var(--shadow-sm);">
        <div style="padding: 20px 24px; border-bottom: 1px solid var(--border); display: flex; justify-content: space-between; align-items: center;">
            <h3 style="font-size: 1.15rem; margin: 0;"><i class="fa-solid fa-clock-rotate-left text-accent"></i> Danh Sách Đơn Hàng (<?= count($orders) ?>)</h3>
        </div>

        <?php if (empty($orders)): ?>
            <div style="padding: 60px 20px; text-align: center;">
                <i class="fa-solid fa-clipboard-list" style="font-size: 3.5rem; color: #cbd5e1; margin-bottom: 14px;"></i>
                <h4>Chưa tìm thấy đơn hàng nào!</h4>
                <p style="color: var(--text-muted); margin-bottom: 20px;">Bạn chưa thực hiện đơn đặt hàng nào hoặc số điện thoại tra cứu không đúng.</p>
                <a href="products.php" class="btn btn-accent">Mua Sắm Ngay</a>
            </div>
        <?php else: ?>
            <table class="cart-table" style="font-size: 0.9rem;">
                <thead>
                    <tr>
                        <th>Mã đơn hàng</th>
                        <th>Ngày đặt</th>
                        <th>Người nhận</th>
                        <th>Tổng thanh toán</th>
                        <th>Thanh toán</th>
                        <th>Trạng thái</th>
                        <th>Thao tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($orders as $ord): ?>
                        <tr>
                            <td>
                                <strong style="color: var(--accent);"><?= htmlspecialchars($ord['order_code']) ?></strong>
                            </td>
                            <td><?= format_datetime($ord['created_at']) ?></td>
                            <td>
                                <div><strong><?= htmlspecialchars($ord['customer_name']) ?></strong></div>
                                <div style="font-size: 0.8rem; color: var(--text-muted);"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                            </td>
                            <td>
                                <strong style="color: #ef4444;"><?= format_price($ord['total_amount']) ?></strong>
                            </td>
                            <td>
                                <span class="badge <?= $ord['payment_status'] === 'paid' ? 'badge-success' : 'badge-warning' ?>">
                                    <?= $ord['payment_status'] === 'paid' ? 'Đã thanh toán' : 'Chưa thanh toán' ?>
                                </span>
                            </td>
                            <td>
                                <?= get_order_status_badge($ord['order_status']) ?>
                            </td>
                            <td>
                                <a href="order_success.php?code=<?= urlencode($ord['order_code']) ?>" class="btn btn-outline btn-sm">
                                    <i class="fa-solid fa-eye"></i> Chi Tiết
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        <?php endif; ?>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
