<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - CONTACT INQUIRIES MANAGEMENT
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/../includes/config.php';

if (!is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/login.php");
    exit;
}

if (isset($_POST['update_contact_status'])) {
    $c_id = (int)$_POST['contact_id'];
    $st = sanitize($_POST['status']);
    $pdo->prepare("UPDATE contacts SET status = ? WHERE id = ?")->execute([$st, $c_id]);
    set_flash('success', 'Đã cập nhật trạng thái liên hệ #' . $c_id);
    header("Location: " . BASE_URL . "/admin/contacts.php");
    exit;
}

$contacts = $pdo->query("SELECT * FROM contacts ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Hộp Thư Khách Hàng | <?= SITE_NAME ?></title>
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
                <li><a href="<?= BASE_URL ?>/admin/bookings.php" class="admin-nav-link"><i class="fas fa-calendar-check"></i> Đặt Lịch VIP</a></li>
                <li><a href="<?= BASE_URL ?>/admin/contacts.php" class="admin-nav-link active"><i class="fas fa-envelope"></i> Hộp Thư Khách Hàng</a></li>
                <li style="margin-top: auto; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                    <a href="<?= BASE_URL ?>/index.php" target="_blank" class="admin-nav-link"><i class="fas fa-external-link-alt"></i> Xem Website</a>
                </li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main-content">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 800; color: #fff;">HỘP THƯ LIÊN HỆ & PHẢN HỒI</h1>
                    <span style="color: var(--text-secondary); font-size: 0.9rem;">Danh sách ý kiến đóng góp và yêu cầu tư vấn từ khách hàng</span>
                </div>
            </div>

            <!-- Contacts Table -->
            <div class="cart-table-card">
                <table class="cart-table" style="font-size: 0.85rem;">
                    <thead>
                        <tr>
                            <th>Họ Tên & Email</th>
                            <th>Số Điện Thoại</th>
                            <th>Tiêu Đề</th>
                            <th>Nội Dung Thư</th>
                            <th>Ngày Gửi</th>
                            <th>Trạng Thái</th>
                            <th>Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($contacts)): ?>
                        <tr><td colspan="7" style="text-align:center; padding: 2rem; color: var(--text-muted);">Hộp thư hiện đang trống.</td></tr>
                        <?php else: ?>
                            <?php foreach ($contacts as $c): ?>
                            <tr>
                                <td>
                                    <strong style="color: #fff; display: block;"><?= htmlspecialchars($c['name']) ?></strong>
                                    <span style="color: var(--text-secondary); font-size: 0.8rem;"><?= htmlspecialchars($c['email']) ?></span>
                                </td>
                                <td style="color: var(--gold-light); font-weight: 700;">
                                    <?= htmlspecialchars($c['phone'] ?? 'Chưa cung cấp') ?>
                                </td>
                                <td style="color: var(--text-primary); font-weight: 600;">
                                    <?= htmlspecialchars($c['subject']) ?>
                                </td>
                                <td style="max-width: 250px; color: #cbd5e1; font-size: 0.85rem; line-height: 1.5;">
                                    <?= nl2br(htmlspecialchars($c['message'])) ?>
                                </td>
                                <td style="color: var(--text-muted); font-size: 0.75rem;">
                                    <?= date('d/m/Y H:i', strtotime($c['created_at'])) ?>
                                </td>
                                <td>
                                    <span class="badge <?= $c['status'] == 'replied' ? 'badge-emerald' : 'badge-gold' ?>">
                                        <?= strtoupper($c['status']) ?>
                                    </span>
                                </td>
                                <td>
                                    <form action="<?= BASE_URL ?>/admin/contacts.php" method="POST" style="display: flex; gap: 0.3rem;">
                                        <input type="hidden" name="contact_id" value="<?= $c['id'] ?>">
                                        <select name="status" class="form-control" style="padding: 0.3rem; font-size: 0.75rem;">
                                            <option value="unread" <?= $c['status'] == 'unread' ? 'selected' : '' ?>>Chưa đọc</option>
                                            <option value="read" <?= $c['status'] == 'read' ? 'selected' : '' ?>>Đã đọc</option>
                                            <option value="replied" <?= $c['status'] == 'replied' ? 'selected' : '' ?>>Đã phản hồi</option>
                                        </select>
                                        <button type="submit" name="update_contact_status" class="btn btn-primary btn-sm" style="padding: 0.3rem 0.5rem; font-size: 0.7rem;">Lưu</button>
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
