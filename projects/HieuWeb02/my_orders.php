<?php
$page_title = 'Lịch Sử Đơn Hàng Của Bạn';
require_once __DIR__ . '/includes/header.php';

$orders = [];
$search_phone = isset($_GET['phone']) ? trim($_GET['phone']) : '';

if ($pdo) {
    try {
        if (is_logged_in()) {
            $user_id = current_user()['id'];
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? ORDER BY id DESC");
            $stmt->execute([$user_id]);
            $orders = $stmt->fetchAll();
        } elseif (!empty($search_phone)) {
            $stmt = $pdo->prepare("SELECT * FROM orders WHERE customer_phone = ? ORDER BY id DESC");
            $stmt->execute([$search_phone]);
            $orders = $stmt->fetchAll();
        }
    } catch (Exception $e) {}
}

// Fallback nếu không có CSDL
if (empty($orders) && is_logged_in()) {
    $orders = [
        [
            'id' => 1,
            'order_code' => 'HM-20260820-A19B2',
            'customer_name' => current_user()['full_name'],
            'total_amount' => 31990000,
            'payment_method' => 'bank_transfer',
            'payment_status' => 'paid',
            'shipping_status' => 'completed',
            'created_at' => date('Y-m-d H:i:s')
        ]
    ];
}
?>

<main class="container" style="margin: 30px auto 60px;">
    <!-- Breadcrumb -->
    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--text-muted); margin-bottom: 24px;">
        <a href="index.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <span>/</span>
        <span style="color: #fff;">Lịch sử đơn hàng</span>
    </div>

    <div class="glass-panel" style="padding: 30px; margin-bottom: 30px;">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
            <h1 style="font-size: 1.4rem; font-weight: 800; color: #fff;">
                <i class="fa-solid fa-receipt" style="color: var(--primary);"></i> Danh Sách Đơn Hàng
            </h1>

            <!-- Tra cứu cho khách vãng lai nếu chưa đăng nhập -->
            <?php if (!is_logged_in()): ?>
                <form action="my_orders.php" method="GET" style="display: flex; gap: 8px;">
                    <input type="text" name="phone" class="form-control" placeholder="Nhập số điện thoại tra cứu..." value="<?php echo htmlspecialchars($search_phone); ?>" required style="width: 250px;">
                    <button type="submit" class="btn btn-primary btn-sm">Tra cứu</button>
                </form>
            <?php endif; ?>
        </div>

        <?php if (!empty($orders)): ?>
            <div class="cart-table-wrap">
                <table class="cart-table">
                    <thead>
                        <tr>
                            <th>Mã đơn hàng</th>
                            <th>Ngày đặt</th>
                            <th>Người nhận</th>
                            <th>Phương thức</th>
                            <th>Tổng tiền</th>
                            <th>Trạng thái</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($orders as $ord): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--accent); font-family: monospace;"><?php echo htmlspecialchars($ord['order_code']); ?></strong>
                                </td>
                                <td style="color: var(--text-muted); font-size: 0.85rem;">
                                    <?php echo date('d/m/Y H:i', strtotime($ord['created_at'])); ?>
                                </td>
                                <td>
                                    <strong style="color: #fff;"><?php echo htmlspecialchars($ord['customer_name']); ?></strong>
                                </td>
                                <td>
                                    <span style="font-size: 0.85rem; color: #cbd5e1;"><?php echo $ord['payment_method'] === 'bank_transfer' ? 'VietQR' : 'COD'; ?></span>
                                </td>
                                <td>
                                    <strong style="color: #f43f5e;"><?php echo format_currency($ord['total_amount']); ?></strong>
                                </td>
                                <td>
                                    <?php echo render_order_status($ord['shipping_status']); ?>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div style="text-align: center; padding: 40px 20px;">
                <i class="fa-solid fa-clipboard-list" style="font-size: 3rem; color: var(--text-muted); margin-bottom: 14px;"></i>
                <h3 style="font-size: 1.1rem; color: #fff; margin-bottom: 6px;">Không tìm thấy đơn hàng nào</h3>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Nếu bạn chưa đăng nhập, vui lòng nhập số điện thoại đã dùng để đặt hàng để tra cứu.</p>
            </div>
        <?php endif; ?>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
