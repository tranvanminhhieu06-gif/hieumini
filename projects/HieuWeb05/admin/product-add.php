<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - ADD NEW PRODUCT
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/../includes/config.php';

if (!is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/login.php");
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $sku = sanitize($_POST['sku'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 1);
    $price = (float)($_POST['price'] ?? 0);
    $original_price = !empty($_POST['original_price']) ? (float)$_POST['original_price'] : null;
    $stock = (int)($_POST['stock'] ?? 100);
    $badge = sanitize($_POST['badge'] ?? '');
    $image = sanitize($_POST['image'] ?? '01_membership_diamond.jpg');
    $short_desc = sanitize($_POST['short_description'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $specs_json = $_POST['specs_json'] ?? '{}';

    if ($name && $sku && $price > 0) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name), '-'));
        
        try {
            $stmt = $pdo->prepare("
                INSERT INTO products (
                    category_id, sku, name, slug, price, original_price, stock, badge, image, 
                    short_description, description, specs_json
                ) VALUES (
                    ?, ?, ?, ?, ?, ?, ?, ?, ?, 
                    ?, ?, ?
                )
            ");
            $stmt->execute([
                $category_id, $sku, $name, $slug, $price, $original_price, $stock, $badge, $image,
                $short_desc, $description, $specs_json
            ]);

            set_flash('success', 'Đã thêm sản phẩm "' . htmlspecialchars($name) . '" thành công!');
            header("Location: " . BASE_URL . "/admin/products.php");
            exit;
        } catch (PDOException $e) {
            $error = 'Lỗi thêm sản phẩm (Có thể SKU hoặc Slug đã tồn tại): ' . $e->getMessage();
        }
    } else {
        $error = 'Vui lòng điền đầy đủ Tên, SKU và Giá bán hợp lệ.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới | <?= SITE_NAME ?></title>
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
                <li><a href="<?= BASE_URL ?>/admin/products.php" class="admin-nav-link active"><i class="fas fa-dumbbell"></i> Quản Lý Sản Phẩm</a></li>
                <li><a href="<?= BASE_URL ?>/admin/orders.php" class="admin-nav-link"><i class="fas fa-shopping-bag"></i> Quản Lý Đơn Hàng</a></li>
                <li><a href="<?= BASE_URL ?>/admin/bookings.php" class="admin-nav-link"><i class="fas fa-calendar-check"></i> Đặt Lịch VIP</a></li>
                <li><a href="<?= BASE_URL ?>/admin/contacts.php" class="admin-nav-link"><i class="fas fa-envelope"></i> Hộp Thư Khách Hàng</a></li>
                <li style="margin-top: auto; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                    <a href="<?= BASE_URL ?>/index.php" target="_blank" class="admin-nav-link"><i class="fas fa-external-link-alt"></i> Xem Website</a>
                </li>
            </ul>
        </aside>

        <!-- Content -->
        <main class="admin-main-content">
            <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 2rem;">
                <h1 style="font-size: 2rem; font-weight: 800; color: #fff;">THÊM SẢN PHẨM / DỊCH VỤ MỚI</h1>
                <a href="<?= BASE_URL ?>/admin/products.php" class="btn btn-secondary btn-sm"><i class="fas fa-arrow-left"></i> Quay Lại</a>
            </div>

            <?php if ($error): ?>
                <div style="background: rgba(239,68,68,0.15); border: 1px solid var(--ruby-accent); border-radius: 4px; padding: 1rem; margin-bottom: 1.5rem; color: #fca5a5;">
                    <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <div class="form-card" style="max-width: 900px;">
                <form action="<?= BASE_URL ?>/admin/product-add.php" method="POST">
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 1.25rem;">
                        <div class="form-group">
                            <label>Tên Sản Phẩm / Gói Tập (*)</label>
                            <input type="text" name="name" class="form-control" required placeholder="Ví dụ: Máy Chạy Bộ Commercial X9 Pro">
                        </div>
                        <div class="form-group">
                            <label>Mã SKU (*)</label>
                            <input type="text" name="sku" class="form-control" required placeholder="Ví dụ: EQP-09">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">
                        <div class="form-group">
                            <label>Danh Mục (*)</label>
                            <select name="category_id" class="form-control">
                                <?php foreach ($categories as $c): ?>
                                <option value="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Giá Bán (VNĐ) (*)</label>
                            <input type="number" name="price" class="form-control" required placeholder="59000000">
                        </div>
                        <div class="form-group">
                            <label>Giá Niêm Yết Gốc (VNĐ)</label>
                            <input type="number" name="original_price" class="form-control" placeholder="68000000">
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 1.25rem;">
                        <div class="form-group">
                            <label>Số Lượng Tồn Kho</label>
                            <input type="number" name="stock" class="form-control" value="50">
                        </div>
                        <div class="form-group">
                            <label>Huy Hiệu (Badge)</label>
                            <input type="text" name="badge" class="form-control" placeholder="Ví dụ: BEST SELLER, CEO VIP">
                        </div>
                        <div class="form-group">
                            <label>Tên File Ảnh Sản Phẩm</label>
                            <input type="text" name="image" class="form-control" value="06_commercial_treadmill.jpg">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Mô Tả Ngắn (Hiển thị xem nhanh)</label>
                        <input type="text" name="short_description" class="form-control" placeholder="Tóm tắt tính năng nổi bật...">
                    </div>

                    <div class="form-group">
                        <label>Mô Tả Chi Tiết Chuyên Sâu</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Chi tiết về sản phẩm, công nghệ, nguồn gốc, công dụng..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Thông Số Kỹ Thuật (Định dạng JSON)</label>
                        <textarea name="specs_json" class="form-control" rows="3">{"Xuất xứ": "USA / Germany", "Bảo hành": "5 Năm VIP", "Tiêu chuẩn": "Olympic IWF"}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary btn-lg btn-shimmer" style="margin-top: 1rem;">
                        <i class="fas fa-save"></i> LƯU SẢN PHẨM MỚI
                    </button>
                </form>
            </div>
        </main>
    </div>

</body>
</html>
