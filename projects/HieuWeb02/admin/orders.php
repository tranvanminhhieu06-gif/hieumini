<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_admin();

// Cập nhật trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = sanitize($_POST['shipping_status']);

    if ($pdo) {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET shipping_status = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            set_flash('success', 'Đã cập nhật trạng thái đơn hàng #' . $order_id . ' thành công!');
        } catch (Exception $e) {
            set_flash('danger', 'Lỗi: ' . $e->getMessage());
        }
    }
    header("Location: orders.php");
    exit;
}

$orders = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC");
        $orders = $stmt->fetchAll();
    } catch (Exception $e) {}
}

if (empty($orders)) {
    $orders = [
        ['id' => 1, 'order_code' => 'ORD-2026-001', 'customer_name' => 'Nguyễn Hoàng Nam', 'customer_phone' => '0912345678', 'customer_address' => '120 Phố Huế, Hà Nội', 'total_amount' => 31990000, 'payment_method' => 'bank_transfer', 'payment_status' => 'paid', 'shipping_status' => 'completed', 'created_at' => date('Y-m-d H:i')],
        ['id' => 2, 'order_code' => 'ORD-2026-002', 'customer_name' => 'Lê Thị Thu Thảo', 'customer_phone' => '0934567890', 'customer_address' => '45 Lê Duẩn, Q1, HCM', 'total_amount' => 6670500, 'payment_method' => 'cod', 'payment_status' => 'unpaid', 'shipping_status' => 'shipping', 'created_at' => date('Y-m-d H:i')]
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng - HieuMini Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <div class="admin-main">
        <header class="admin-header">
            <h2 style="font-size: 1.3rem; font-weight: 800; color: #fff;">Quản Lý Đơn Hàng & Vận Chuyển</h2>
        </header>

        <main class="admin-content">
            <?php echo display_flash(); ?>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <i class="fa-solid fa-cart-flatbed" style="color: var(--primary);"></i> Toàn Bộ Đơn Hàng (<?php echo count($orders); ?>)
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Mã đơn</th>
                                <th>Khách hàng</th>
                                <th>Địa chỉ giao</th>
                                <th>Tổng tiền</th>
                                <th>Phương thức</th>
                                <th>Trạng thái hiện tại</th>
                                <th>Cập nhật trạng thái</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($orders as $ord): ?>
                                <tr>
                                    <td><strong style="color: var(--accent); font-family: monospace;"><?php echo htmlspecialchars($ord['order_code']); ?></strong></td>
                                    <td>
                                        <strong style="color: #fff;"><?php echo htmlspecialchars($ord['customer_name']); ?></strong><br>
                                        <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo htmlspecialchars($ord['customer_phone']); ?></span>
                                    </td>
                                    <td style="max-width: 200px; font-size: 0.85rem; color: #cbd5e1;"><?php echo htmlspecialchars($ord['customer_address']); ?></td>
                                    <td><strong style="color: #f43f5e;"><?php echo format_currency($ord['total_amount']); ?></strong></td>
                                    <td>
                                        <span style="font-size: 0.82rem; color: #cbd5e1;"><?php echo $ord['payment_method'] === 'bank_transfer' ? 'VietQR' : 'COD'; ?></span>
                                    </td>
                                    <td><?php echo render_order_status($ord['shipping_status']); ?></td>
                                    <td>
                                        <form action="orders.php" method="POST" style="display: flex; gap: 6px;">
                                            <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                                            <select name="shipping_status" class="form-control" style="padding: 4px 8px; font-size: 0.8rem; width: auto;">
                                                <option value="pending" <?php echo $ord['shipping_status'] === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                                                <option value="processing" <?php echo $ord['shipping_status'] === 'processing' ? 'selected' : ''; ?>>Đang xử lý</option>
                                                <option value="shipping" <?php echo $ord['shipping_status'] === 'shipping' ? 'selected' : ''; ?>>Đang giao</option>
                                                <option value="completed" <?php echo $ord['shipping_status'] === 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                                                <option value="cancelled" <?php echo $ord['shipping_status'] === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
                                            </select>
                                            <button type="submit" name="update_status" class="btn btn-primary btn-sm" style="padding: 4px 8px;" title="Cập nhật">
                                                <i class="fa-solid fa-check"></i>
                                            </button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
