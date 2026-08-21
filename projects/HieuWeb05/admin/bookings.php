<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - VIP BOOKINGS MANAGEMENT
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/../includes/config.php';

if (!is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/login.php");
    exit;
}

// Xử lý cập nhật trạng thái lịch hẹn
if (isset($_POST['update_booking_status'])) {
    $b_id = (int)$_POST['booking_id'];
    $st = sanitize($_POST['status']);
    $pdo->prepare("UPDATE bookings SET status = ? WHERE id = ?")->execute([$st, $b_id]);
    set_flash('success', 'Đã cập nhật trạng thái lịch hẹn #' . $b_id);
    header("Location: " . BASE_URL . "/admin/bookings.php");
    exit;
}

$bookings = $pdo->query("SELECT * FROM bookings ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Đặt Lịch VIP | <?= SITE_NAME ?></title>
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
                <li><a href="<?= BASE_URL ?>/admin/orders.php" class="admin-nav-link"><i class="fas fa-shopping-bag"></i> Quản Lý Đơn Hàng</a></li>
                <li><a href="<?= BASE_URL ?>/admin/bookings.php" class="admin-nav-link active"><i class="fas fa-calendar-check"></i> Đặt Lịch VIP</a></li>
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
                    <h1 style="font-size: 2rem; font-weight: 800; color: #fff;">DANH SÁCH ĐẶT LỊCH TRẢI NGHIỆM VIP</h1>
                    <span style="color: var(--text-secondary); font-size: 0.9rem;">Danh sách khách hàng đăng ký tập thử và tư vấn Master Trainer</span>
                </div>
            </div>

            <!-- Bookings Table -->
            <div class="cart-table-card">
                <table class="cart-table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Họ Tên Khách Hàng</th>
                            <th>Liên Hệ</th>
                            <th>Dịch Vụ Quan Tâm</th>
                            <th>Chi Nhánh & Thời Gian</th>
                            <th>Ghi Chú Mục Tiêu</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($bookings)): ?>
                        <tr><td colspan="7" style="text-align:center; padding: 2rem; color: var(--text-muted);">Chưa có lịch hẹn nào.</td></tr>
                        <?php else: ?>
                            <?php foreach ($bookings as $b): ?>
                            <tr>
                                <td><strong style="color: #fff;"><?= htmlspecialchars($b['full_name']) ?></strong></td>
                                <td>
                                    <span style="color: var(--gold-light); display: block; font-weight: 700;"><?= htmlspecialchars($b['phone']) ?></span>
                                    <span style="color: var(--text-muted); font-size: 0.75rem;"><?= htmlspecialchars($b['email'] ?? '') ?></span>
                                </td>
                                <td style="color: var(--text-primary); font-weight: 600;"><?= htmlspecialchars($b['service_type']) ?></td>
                                <td>
                                    <span style="color: #fff; display: block;"><?= htmlspecialchars($b['branch']) ?></span>
                                    <span style="color: var(--cyan-accent); font-size: 0.8rem;">Ngày: <?= htmlspecialchars($b['booking_date']) ?> (<?= htmlspecialchars($b['booking_time']) ?>)</span>
                                </td>
                                <td style="max-width: 200px; color: var(--text-secondary); font-size: 0.8rem;">
                                    <?= htmlspecialchars($b['notes'] ?? 'Không có') ?>
                                </td>
                                <td>
                                    <span class="badge <?= $b['status'] == 'confirmed' ? 'badge-emerald' : 'badge-gold' ?>">
                                        <?= strtoupper($b['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="<?= BASE_URL ?>/admin/bookings.php" method="POST" style="display: flex; gap: 0.3rem;">
                                        <input type="hidden" name="booking_id" value="<?= $b['id'] ?>">
                                        <select name="status" class="form-control" style="padding: 0.3rem; font-size: 0.75rem;">
                                            <option value="pending" <?= $b['status'] == 'pending' ? 'selected' : '' ?>>Chờ gọi</option>
                                            <option value="confirmed" <?= $b['status'] == 'confirmed' ? 'selected' : '' ?>>Đã chốt</option>
                                            <option value="completed" <?= $b['status'] == 'completed' ? 'selected' : '' ?>>Đã đến</option>
                                            <option value="cancelled" <?= $b['status'] == 'cancelled' ? 'selected' : '' ?>>Hủy</option>
                                        </select>
                                        <button type="submit" name="update_booking_status" class="btn btn-primary btn-sm" style="padding: 0.3rem 0.5rem; font-size: 0.7rem;">Lưu</button>
                                    </form>
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
