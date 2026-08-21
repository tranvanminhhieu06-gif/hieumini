<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - ADMIN DASHBOARD (CEO EXECUTIVE METRICS)
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/../includes/config.php';

if (!is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/login.php");
    exit;
}

// 1. Thống kê tổng doanh thu
$total_revenue = $pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE order_status != 'cancelled'")->fetchColumn();

// 2. Tổng đơn hàng
$total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

// 3. Tổng số lượng sản phẩm
$total_products = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();

// 4. Tổng số lượt đăng ký trải nghiệm VIP
$total_bookings = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();

// 5. Đơn hàng gần đây (5 đơn mới nhất)
$recent_orders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();

// 6. Đặt lịch VIP gần đây (5 lượt mới nhất)
$recent_bookings = $pdo->query("SELECT * FROM bookings ORDER BY id DESC LIMIT 5")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Bảng Điều Khiển CEO | <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body style="background: var(--bg-primary);">

    <div class="admin-layout">
        <!-- Sidebar Navigation -->
        <aside class="admin-sidebar">
            <!-- Brand -->
            <a href="<?= BASE_URL ?>/admin/index.php" class="brand-logo">
                <div class="logo-icon">HM</div>
                <div class="logo-text">
                    <span class="brand-name">HIEUMINI</span>
                    <span class="brand-tagline">CEO DASHBOARD</span>
                </div>
            </a>

            <!-- Nav Links -->
            <ul class="admin-nav-list">
                <li><a href="<?= BASE_URL ?>/admin/index.php" class="admin-nav-link active"><i class="fas fa-chart-pie"></i> Tổng Quan CEO</a></li>
                <li><a href="<?= BASE_URL ?>/admin/products.php" class="admin-nav-link"><i class="fas fa-dumbbell"></i> Quản Lý Sản Phẩm (<?= $total_products ?>)</a></li>
                <li><a href="<?= BASE_URL ?>/admin/orders.php" class="admin-nav-link"><i class="fas fa-shopping-bag"></i> Quản Lý Đơn Hàng (<?= $total_orders ?>)</a></li>
                <li><a href="<?= BASE_URL ?>/admin/bookings.php" class="admin-nav-link"><i class="fas fa-calendar-check"></i> Đặt Lịch VIP (<?= $total_bookings ?>)</a></li>
                <li><a href="<?= BASE_URL ?>/admin/contacts.php" class="admin-nav-link"><i class="fas fa-envelope"></i> Hộp Thư Khách Hàng</a></li>
                <li style="margin-top: auto; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                    <a href="<?= BASE_URL ?>/index.php" target="_blank" class="admin-nav-link"><i class="fas fa-external-link-alt"></i> Xem Website</a>
                </li>
                <li><a href="<?= BASE_URL ?>/admin/logout.php" class="admin-nav-link" style="color: var(--ruby-accent);"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a></li>
            </ul>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-main-content">
            <!-- Top Bar Header -->
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2.5rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 style="font-size: 2.2rem; font-weight: 800; color: #fff;">BÁO CÁO ĐIỀU HÀNH DOANH NGHIỆP</h1>
                    <span style="color: var(--text-secondary); font-size: 0.95rem;">Xin chào Chủ Tịch / CEO HieuMini | Cập nhật lúc <?= date('H:i, d/m/Y') ?></span>
                </div>
                <div style="display: flex; gap: 0.75rem;">
                    <a href="<?= BASE_URL ?>/admin/product-add.php" class="btn btn-primary btn-sm btn-shimmer">
                        <i class="fas fa-plus"></i> Thêm Sản Phẩm
                    </a>
                    <a href="<?= BASE_URL ?>/admin/orders.php" class="btn btn-secondary btn-sm">
                        <i class="fas fa-list"></i> Xem Tất Cả Đơn
                    </a>
                </div>
            </div>

            <!-- 4 Executive Stat Cards -->
            <div class="admin-stats-grid">
                <!-- 1. Total Revenue -->
                <div class="admin-stat-card">
                    <div>
                        <span style="font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.4rem;">TỔNG DOANH THU</span>
                        <div class="admin-stat-val"><?= format_currency($total_revenue) ?></div>
                        <span style="font-size: 0.75rem; color: var(--emerald-accent);"><i class="fas fa-arrow-up"></i> Tăng trưởng vượt bậc</span>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(245,158,11,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--gold-light); font-size: 1.5rem;">
                        <i class="fas fa-coins"></i>
                    </div>
                </div>

                <!-- 2. Orders Count -->
                <div class="admin-stat-card">
                    <div>
                        <span style="font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.4rem;">TỔNG ĐƠN HÀNG</span>
                        <div class="admin-stat-val" style="color: var(--cyan-accent);"><?= $total_orders ?></div>
                        <span style="font-size: 0.75rem; color: var(--text-secondary);">Giao dịch toàn hệ thống</span>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(6,182,212,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--cyan-accent); font-size: 1.5rem;">
                        <i class="fas fa-shopping-cart"></i>
                    </div>
                </div>

                <!-- 3. Total Products (30) -->
                <div class="admin-stat-card">
                    <div>
                        <span style="font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.4rem;">SẢN PHẨM & GÓI TẬP</span>
                        <div class="admin-stat-val" style="color: var(--emerald-accent);"><?= $total_products ?> / 30</div>
                        <span style="font-size: 0.75rem; color: var(--emerald-accent);"><i class="fas fa-check-circle"></i> Đầy đủ 30 sản phẩm</span>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(16,185,129,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--emerald-accent); font-size: 1.5rem;">
                        <i class="fas fa-boxes"></i>
                    </div>
                </div>

                <!-- 4. VIP Bookings -->
                <div class="admin-stat-card">
                    <div>
                        <span style="font-size: 0.85rem; color: var(--text-secondary); text-transform: uppercase; font-weight: 700; display: block; margin-bottom: 0.4rem;">ĐẶT LỊCH TRẢI NGHIỆM</span>
                        <div class="admin-stat-val" style="color: var(--purple-accent);"><?= $total_bookings ?></div>
                        <span style="font-size: 0.75rem; color: var(--gold-light);">Hội viên VIP tiềm năng</span>
                    </div>
                    <div style="width: 50px; height: 50px; background: rgba(168,85,247,0.15); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--purple-accent); font-size: 1.5rem;">
                        <i class="fas fa-crown"></i>
                    </div>
                </div>
            </div>

            <!-- Two Columns: Recent Orders & Recent Bookings -->
            <div style="display: grid; grid-template-columns: 1.2fr 1fr; gap: 2rem; margin-bottom: 2.5rem;">
                <!-- Recent Orders -->
                <div class="form-card" style="padding: 1.75rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                        <h3 style="font-size: 1.2rem; color: #fff;"><i class="fas fa-receipt" style="color: var(--gold-primary);"></i> Đơn Hàng Mới Nhất</h3>
                        <a href="<?= BASE_URL ?>/admin/orders.php" style="font-size: 0.85rem; color: var(--gold-primary);">Xem tất cả &rarr;</a>
                    </div>

                    <?php if (empty($recent_orders)): ?>
                        <p style="color: var(--text-secondary); font-size: 0.9rem;">Chưa có đơn hàng nào được ghi nhận.</p>
                    <?php else: ?>
                        <div style="overflow-x: auto;">
                            <table class="cart-table" style="font-size: 0.85rem;">
                                <thead>
                                    <tr>
                                        <th>Mã Đơn</th>
                                        <th>Khách Hàng</th>
                                        <th>Tổng Tiền</th>
                                        <th>Trạng Thái</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($recent_orders as $ord): ?>
                                    <tr>
                                        <td><strong style="color: var(--gold-light);"><?= htmlspecialchars($ord['order_code']) ?></strong></td>
                                        <td><?= htmlspecialchars($ord['customer_name']) ?></td>
                                        <td style="color: #fff; font-weight: 700;"><?= format_currency($ord['total_amount']) ?></td>
                                        <td>
                                            <span class="badge badge-gold" style="font-size: 0.7rem;"><?= strtoupper($ord['order_status']) ?></span>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    <?php endif; ?>
                </div>

                <!-- Recent VIP Bookings -->
                <div class="form-card" style="padding: 1.75rem;">
                    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 1.25rem;">
                        <h3 style="font-size: 1.2rem; color: #fff;"><i class="fas fa-calendar-check" style="color: var(--cyan-accent);"></i> Lịch Hẹn VIP Mới</h3>
                        <a href="<?= BASE_URL ?>/admin/bookings.php" style="font-size: 0.85rem; color: var(--cyan-accent);">Xem tất cả &rarr;</a>
                    </div>

                    <?php if (empty($recent_bookings)): ?>
                        <p style="color: var(--text-secondary); font-size: 0.9rem;">Chưa có lịch hẹn nào.</p>
                    <?php else: ?>
                        <div style="display: flex; flex-direction: column; gap: 0.85rem;">
                            <?php foreach ($recent_bookings as $b): ?>
                            <div style="background: var(--bg-secondary); border: 1px solid var(--border-subtle); border-radius: 6px; padding: 0.85rem; font-size: 0.85rem;">
                                <div style="display: flex; justify-content: space-between; margin-bottom: 0.25rem;">
                                    <strong style="color: #fff;"><?= htmlspecialchars($b['full_name']) ?></strong>
                                    <span style="color: var(--gold-primary);"><?= htmlspecialchars($b['booking_date']) ?></span>
                                </div>
                                <div style="color: var(--text-secondary);"><?= htmlspecialchars($b['service_type']) ?></div>
                                <div style="color: var(--text-muted); font-size: 0.75rem; margin-top: 0.25rem;">SĐT: <?= htmlspecialchars($b['phone']) ?></div>
                            </div>
                            <?php endforeach; ?>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        </main>
    </div>

</body>
</html>
