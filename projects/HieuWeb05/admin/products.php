<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - ADMIN PRODUCT MANAGEMENT (30 PRODUCTS)
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/../includes/config.php';

if (!is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/login.php");
    exit;
}

// Xử lý Xóa sản phẩm
if (isset($_GET['delete_id'])) {
    $del_id = (int)$_GET['delete_id'];
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$del_id]);
    set_flash('success', 'Đã xóa sản phẩm ID ' . $del_id . ' thành công.');
    header("Location: " . BASE_URL . "/admin/products.php");
    exit;
}

// Lấy danh sách toàn bộ sản phẩm
$search = trim($_GET['search'] ?? '');
$category_id = (int)($_GET['category_id'] ?? 0);

$where = ["1=1"];
$params = [];

if ($search !== '') {
    $where[] = "(p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}
if ($category_id > 0) {
    $where[] = "p.category_id = ?";
    $params[] = $category_id;
}

$where_sql = implode(" AND ", $where);
$sql = "
    SELECT p.*, c.name AS category_name 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE {$where_sql} 
    ORDER BY p.id ASC
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
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
            <a href="<?= BASE_URL ?>/admin/index.php" class="brand-logo">
                <div class="logo-icon">HM</div>
                <div class="logo-text">
                    <span class="brand-name">HIEUMINI</span>
                    <span class="brand-tagline">CEO DASHBOARD</span>
                </div>
            </a>
            <ul class="admin-nav-list">
                <li><a href="<?= BASE_URL ?>/admin/index.php" class="admin-nav-link"><i class="fas fa-chart-pie"></i> Tổng Quan CEO</a></li>
                <li><a href="<?= BASE_URL ?>/admin/products.php" class="admin-nav-link active"><i class="fas fa-dumbbell"></i> Quản Lý Sản Phẩm</a></li>
                <li><a href="<?= BASE_URL ?>/admin/orders.php" class="admin-nav-link"><i class="fas fa-shopping-bag"></i> Quản Lý Đơn Hàng</a></li>
                <li><a href="<?= BASE_URL ?>/admin/bookings.php" class="admin-nav-link"><i class="fas fa-calendar-check"></i> Đặt Lịch VIP</a></li>
                <li><a href="<?= BASE_URL ?>/admin/contacts.php" class="admin-nav-link"><i class="fas fa-envelope"></i> Hộp Thư Khách Hàng</a></li>
                <li style="margin-top: auto; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                    <a href="<?= BASE_URL ?>/index.php" target="_blank" class="admin-nav-link"><i class="fas fa-external-link-alt"></i> Xem Website</a>
                </li>
                <li><a href="<?= BASE_URL ?>/admin/logout.php" class="admin-nav-link" style="color: var(--ruby-accent);"><i class="fas fa-sign-out-alt"></i> Đăng Xuất</a></li>
            </ul>
        </aside>

        <!-- Main Content -->
        <main class="admin-main-content">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem; flex-wrap: wrap; gap: 1rem;">
                <div>
                    <h1 style="font-size: 2rem; font-weight: 800; color: #fff;">QUẢN LÝ 30 SẢN PHẨM & DỊCH VỤ</h1>
                    <span style="color: var(--text-secondary); font-size: 0.9rem;">Hệ thống danh mục thiết bị, dinh dưỡng và gói tập chuẩn CEO</span>
                </div>
                <a href="<?= BASE_URL ?>/admin/product-add.php" class="btn btn-primary btn-sm btn-shimmer">
                    <i class="fas fa-plus"></i> Thêm Mới Sản Phẩm
                </a>
            </div>

            <!-- Filter & Search Bar -->
            <div class="form-card" style="padding: 1.25rem; margin-bottom: 2rem;">
                <form action="<?= BASE_URL ?>/admin/products.php" method="GET" style="display: flex; gap: 1rem; flex-wrap: wrap; align-items: center;">
                    <div style="flex-grow: 1; min-width: 250px;">
                        <input type="text" name="search" class="form-control" placeholder="Tìm theo tên sản phẩm hoặc mã SKU..." value="<?= htmlspecialchars($search) ?>">
                    </div>
                    <div>
                        <select name="category_id" class="form-control">
                            <option value="0">-- Tất Cả Danh Mục --</option>
                            <?php foreach ($categories as $cat): ?>
                            <option value="<?= $cat['id'] ?>" <?= $category_id == $cat['id'] ? 'selected' : '' ?>><?= htmlspecialchars($cat['name']) ?></option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-search"></i> Lọc</button>
                    <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-secondary btn-sm"><i class="fas fa-undo"></i> Đặt lại</a>
                </form>
            </div>

            <!-- Products Data Table -->
            <div class="cart-table-card">
                <table class="cart-table" style="font-size: 0.9rem;">
                    <thead>
                        <tr>
                            <th style="width: 60px;">ID</th>
                            <th style="width: 80px;">Hình Ảnh</th>
                            <th>Tên Sản Phẩm / Gói Tập</th>
                            <th>Danh Mục</th>
                            <th>Giá Bán</th>
                            <th style="text-align: center;">Tồn Kho</th>
                            <th style="text-align: center;">Huy Hiệu</th>
                            <th style="text-align: center;">Thao Tác</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($products)): ?>
                        <tr>
                            <td colspan="8" style="text-align: center; padding: 2.5rem; color: var(--text-secondary);">
                                Không có sản phẩm nào phù hợp với bộ lọc tìm kiếm.
                            </td>
                        </tr>
                        <?php else: ?>
                            <?php foreach ($products as $p): ?>
                            <tr>
                                <td style="color: var(--text-muted); font-weight: 700;">#<?= $p['id'] ?></td>
                                <td>
                                    <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($p['image']) ?>" alt="" style="width: 50px; height: 50px; border-radius: 6px; object-fit: cover; border: 1px solid var(--border-subtle);">
                                </td>
                                <td>
                                    <strong style="color: #fff; display: block; font-size: 0.95rem;"><?= htmlspecialchars($p['name']) ?></strong>
                                    <span style="font-size: 0.75rem; color: var(--gold-primary);">SKU: <?= htmlspecialchars($p['sku']) ?></span>
                                </td>
                                <td style="color: var(--text-secondary);"><?= htmlspecialchars($p['category_name']) ?></td>
                                <td style="color: var(--gold-light); font-weight: 700;"><?= format_currency($p['price']) ?></td>
                                <td style="text-align: center;">
                                    <?php if ($p['stock'] > 10): ?>
                                        <span class="badge badge-emerald"><?= $p['stock'] ?></span>
                                    <?php else: ?>
                                        <span class="badge badge-ruby">Sắp hết (<?= $p['stock'] ?>)</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <?php if ($p['badge']): ?>
                                        <span class="badge badge-gold" style="font-size: 0.7rem;"><?= htmlspecialchars($p['badge']) ?></span>
                                    <?php else: ?>
                                        <span style="color: var(--text-muted);">-</span>
                                    <?php endif; ?>
                                </td>
                                <td style="text-align: center;">
                                    <div style="display: flex; justify-content: center; gap: 0.5rem;">
                                        <a href="<?= BASE_URL ?>/admin/product-edit.php?id=<?= $p['id'] ?>" class="action-btn-circle" style="width: 32px; height: 32px; font-size: 0.8rem;" title="Chỉnh sửa">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <a href="<?= BASE_URL ?>/admin/products.php?delete_id=<?= $p['id'] ?>" class="action-btn-circle" style="width: 32px; height: 32px; font-size: 0.8rem; color: var(--ruby-accent);" onclick="return confirm('Bạn có chắc muốn xóa sản phẩm này?');" title="Xóa">
                                            <i class="fas fa-trash-alt"></i>
                                        </a>
                                    </div>
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
