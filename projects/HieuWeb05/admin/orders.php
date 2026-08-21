<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - ADMIN ORDER MANAGEMENT
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/../includes/config.php';

if (!is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/login.php");
    exit;
}

// Xử lý cập nhật trạng thái đơn hàng
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = sanitize($_POST['order_status']);
    $new_payment_status = sanitize($_POST['payment_status']);

    $upd = $pdo->prepare("UPDATE orders SET order_status = ?, payment_status = ? WHERE id = ?");
    $upd->execute([$new_status, $new_payment_status, $order_id]);

    set_flash('success', 'Đã cập nhật trạng thái đơn hàng #' . $order_id . ' thành công!');
    header("Location: " . BASE_URL . "/admin/orders.php");
    exit;
}

// Lấy danh sách đơn hàng
$orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đơn Hàng VIP | <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body style="background: var(--bg-primary);">

    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <a href="<?= BASE_URL ?>/admin/index.php" class="brand-logo">
                <div class="logo-icon">HM</div>
                <div class="logo-text"><span class="brand-name">HIEUMINI</span><span class="brand-tagline">CEO DASHBOARD</span></div>
            </a>
            <ul class="admin-nav-list">
                <li><a href="<?= BASE_URL ?>/admin/index.php" class="admin-nav-link"><i class="fas fa-chart-pie"></i> Tổng Quan CEO</a></li>
                <li><a href="<?= BASE_URL ?>/admin/products.php" class="admin-nav-link"><i class="fas fa-dumbbell"></i> Quản Lý Sản Phẩm</a></li>
                <li><a href="<?= BASE_URL ?>/admin/orders.php" class="admin-nav-link active"><i class="fas fa-shopping-bag"></i> Quản Lý Đơn Hàng</a></li>
                <li><a href="<?= BASE_URL ?>/admin/bookings.php" class="admin-nav-link"><i class="fas fa-calendar-check"></i> Đặt Lịch VIP</a></li>
                <li><a href="<?= BASE_URL ?>/admin/contacts.php" class="admin-nav-link"><i class="fas fa-envelope"></i> Hộp Thư Khách Hàng</a></li>
                <li style="margin-top: auto; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                    <a href="<?= BASE_URL ?>/index.php" target="_blank" class="admin-nav-link"><i class="fas fa-external-link-alt"></i> Xem Website</a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main-content">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 800; color: #fff;">DANH SÁCH ĐƠN HÀNG HỆ THỐNG</h1>
                    <span style="color: var(--text-secondary); font-size: 0.9rem;">Tổng số <?= count($orders) ?> giao dịch mua hàng</span>
                </div>
            </div>

            <!-- Orders Table -->
            <div class="cart-table-card">
                <table class="cart-table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Mã Đơn Hàng</th>
                            <th>Khách Hàng & Liên Hệ</th>
                            <th>Địa Chỉ Nhận</th>
                            <th>Tổng Tiền</th>
                            <th>Phương Thức</th>
                            <th>Trạng Thái</th>
                            <th style="text-align: center;">Cập Nhật</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                        <tr><td colspan="7" style="text-align:center; padding: 2rem; color: var(--text-muted);">Chưa có đơn hàng nào.</td></tr>
                        <?php else: ?>
                            <?php foreach ($orders as $o): ?>
                            <tr>
                                <td>
                                    <strong style="color: var(--gold-light); font-size: 0.95rem;"><?= htmlspecialchars($o['order_code']) ?></strong>
                                    <span style="display: block; font-size: 0.75rem; color: var(--text-muted);"><?= date('d/m/Y H:i', strtotime($o['created_at'])) ?></span>
                                </td>
                                <td>
                                    <strong style="color: #fff; display: block;"><?= htmlspecialchars($o['customer_name']) ?></strong>
                                    <span style="color: var(--text-secondary);"><?= htmlspecialchars($o['customer_phone']) ?></span>
                                </td>
                                <td style="max-width: 200px; color: var(--text-secondary); font-size: 0.8rem;">
                                    <?= htmlspecialchars($o['customer_address']) ?>
                                </td>
                                <td style="color: var(--gold-light); font-weight: 800; font-size: 1rem; font-family: 'Outfit', sans-serif;">
                                    <?= format_currency($o['total_amount']) ?>
                                </td>
                                <td>
                                    <span class="badge badge-gold" style="font-size: 0.7rem;"><?= strtoupper($o['payment_method']) ?></span>
                                    <span style="display: block; font-size: 0.7rem; color: <?= $o['payment_status'] == 'paid' ? 'var(--emerald-accent)' : 'var(--text-muted)' ?>;">
                                        (<?= strtoupper($o['payment_status']) ?>)
                                    </span>
                                </td>
                                <td>
                                    <form action="<?= BASE_URL ?>/admin/orders.php" method="POST" style="display: flex; flex-direction: column; gap: 0.3rem;">
                                        <input type="hidden" name="order_id" value="<?= $o['id'] ?>">
                                        <select name="order_status" class="form-control" style="padding: 0.3rem 0.5rem; font-size: 0.75rem;">
                                            <option value="pending" <?= $o['order_status'] == 'pending' ? 'selected' : '' ?>>Chờ xử lý</option>
                                            <option value="processing" <?= $o['order_status'] == 'processing' ? 'selected' : '' ?>>Đang chuẩn bị</option>
                                            <option value="shipping" <?= $o['order_status'] == 'shipping' ? 'selected' : '' ?>>Đang giao hàng</option>
                                            <option value="completed" <?= $o['order_status'] == 'completed' ? 'selected' : '' ?>>Hoàn tất</option>
                                            <option value="cancelled" <?= $o['order_status'] == 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
                                        </select>
                                        <select name="payment_status" class="form-control" style="padding: 0.3rem 0.5rem; font-size: 0.75rem;">
                                            <option value="pending" <?= $o['payment_status'] == 'pending' ? 'selected' : '' ?>>Chưa thanh toán</option>
                                            <option value="paid" <?= $o['payment_status'] == 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
                                            <option value="failed" <?= $o['payment_status'] == 'failed' ? 'selected' : '' ?>>Thất bại</option>
                                        </select>
                                        <button type="submit" name="update_order_status" class="btn btn-primary btn-sm" style="padding: 0.3rem 0.5rem; font-size: 0.7rem;">
                                            Lưu
                                        </button>
                                    </form>
                                </td>
                                <td style="text-align: center;">
                                    <a href="<?= BASE_URL ?>/order-success.php?code=<?= $o['order_code'] ?>" target="_blank" class="action-btn-circle" style="width: 32px; height: 32px; font-size: 0.8rem; margin: 0 auto;" title="Xem hóa đơn">
                                        <i class="fas fa-print"></i>
                                    </a>
                                </td>
                            </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </main>
    </div>

</body>
</html>
