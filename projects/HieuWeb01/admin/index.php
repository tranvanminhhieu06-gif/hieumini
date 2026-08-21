<?php
/**
 * Admin Dashboard - HieuMini
 */
$adminTitle = "Bảng Điều Khiển Tổng Quan";
require_once __DIR__ . '/includes/header.php';

// 1. Thống kê tổng số
$totalRevenue = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE order_status != 'cancelled'")->fetchColumn() ?: 0;
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 0;
$totalUsers = $pdo->query("SELECT COUNT(*) FROM users WHERE role = 'customer'")->fetchColumn() ?: 0;
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products WHERE status = 1")->fetchColumn() ?: 0;

// 2. Đơn hàng gần đây
$recentOrdersStmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5");
$recentOrders = $recentOrdersStmt->fetchAll();

// 3. Sản phẩm bán chạy / xem nhiều
$topProdsStmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.view_count DESC LIMIT 5");
$topProducts = $topProdsStmt->fetchAll();

// 4. Cảnh báo sắp hết hàng
$lowStockStmt = $pdo->query("SELECT * FROM products WHERE stock <= 50 AND status = 1 ORDER BY stock ASC LIMIT 4");
$lowStocks = $lowStockStmt->fetchAll();
?>

<!-- 4 Thẻ chỉ số thống kê (Stat Cards) -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-info">
            <h5>Tổng Doanh Thu</h5>
            <div class="stat-value" style="color: #10b981;"><?= format_price($totalRevenue) ?></div>
        </div>
        <div class="stat-icon green">
            <i class="fa-solid fa-sack-dollar"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h5>Tổng Đơn Hàng</h5>
            <div class="stat-value"><?= $totalOrders ?></div>
        </div>
        <div class="stat-icon blue">
            <i class="fa-solid fa-cart-shopping"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h5>Khách Hàng Đăng Ký</h5>
            <div class="stat-value"><?= $totalUsers ?></div>
        </div>
        <div class="stat-icon purple">
            <i class="fa-solid fa-users"></i>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-info">
            <h5>Sản Phẩm Đang Bán</h5>
            <div class="stat-value"><?= $totalProducts ?></div>
        </div>
        <div class="stat-icon amber">
            <i class="fa-solid fa-shirt"></i>
        </div>
    </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
    <!-- Bảng Đơn Hàng Gần Đây -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-solid fa-clock-rotate-left text-accent"></i> Đơn Hàng Mới Nhất</h3>
            <a href="orders.php" class="btn btn-outline btn-sm">Xem tất cả</a>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã Đơn</th>
                        <th>Khách Hàng</th>
                        <th>Tổng Tiền</th>
                        <th>Trạng Thái</th>
                        <th>Hành Động</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($recentOrders)): ?>
                        <tr><td colspan="5" style="text-align: center; padding: 20px;">Chưa có đơn hàng nào.</td></tr>
                    <?php else: ?>
                        <?php foreach ($recentOrders as $ord): ?>
                            <tr>
                                <td><strong style="color: var(--accent);"><?= htmlspecialchars($ord['order_code']) ?></strong></td>
                                <td>
                                    <div><strong><?= htmlspecialchars($ord['customer_name']) ?></strong></div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted);"><?= htmlspecialchars($ord['customer_phone']) ?></div>
                                </td>
                                <td><strong style="color: #ef4444;"><?= format_price($ord['total_amount']) ?></strong></td>
                                <td><?= get_order_status_badge($ord['order_status']) ?></td>
                                <td>
                                    <a href="order_detail.php?id=<?= $ord['id'] ?>" class="btn btn-outline btn-sm">
                                        <i class="fa-solid fa-eye"></i> Xem
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

    <!-- Danh sách sản phẩm Hot / Quan tâm nhiều -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-solid fa-fire text-danger"></i> Sản Phẩm Xem Nhiều</h3>
        </div>
        <div class="admin-card-body" style="padding: 16px;">
            <?php foreach ($topProducts as $tp): ?>
                <div style="display: flex; gap: 12px; align-items: center; margin-bottom: 14px; padding-bottom: 14px; border-bottom: 1px solid var(--border);">
                    <img src="../assets/images/products/<?= htmlspecialchars($tp['image']) ?>" alt="Thumb" style="width: 44px; height: 44px; border-radius: 6px; object-fit: cover;">
                    <div style="flex: 1; min-width: 0;">
                        <div style="font-size: 0.85rem; font-weight: 700; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                            <?= htmlspecialchars($tp['name']) ?>
                        </div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">
                            <?= format_price($tp['discount_price'] ?? $tp['price']) ?> • <span style="color: var(--accent);"><?= $tp['view_count'] ?> views</span>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
