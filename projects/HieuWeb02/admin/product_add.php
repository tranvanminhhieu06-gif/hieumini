<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_admin();

$categories = get_categories($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = sanitize($_POST['name'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 1);
    $brand = sanitize($_POST['brand'] ?? 'HieuMini');
    $price = (float)($_POST['price'] ?? 0);
    $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 10);
    $short_desc = sanitize($_POST['short_desc'] ?? '');
    $description = sanitize($_POST['description'] ?? '');
    $specifications = $_POST['specifications'] ?? '';
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_flash_sale = isset($_POST['is_flash_sale']) ? 1 : 0;
    $slug = slugify($name) . '-' . time();

    if (empty($name) || $price <= 0) {
        set_flash('danger', 'Vui lòng nhập tên sản phẩm và mức giá hợp lệ!');
    } else {
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, brand, price, sale_price, stock_quantity, thumbnail, short_desc, description, specifications, is_featured, is_flash_sale) VALUES (?, ?, ?, ?, ?, ?, ?, 'default_prod.png', ?, ?, ?, ?, ?)");
                $stmt->execute([
                    $category_id, $name, $slug, $brand, $price, $sale_price,
                    $stock_quantity, $short_desc, $description, $specifications,
                    $is_featured, $is_flash_sale
                ]);
                set_flash('success', 'Thêm sản phẩm mới "' . $name . '" thành công!');
                header("Location: products.php");
                exit;
            } catch (Exception $e) {
                set_flash('danger', 'Lỗi khi lưu sản phẩm: ' . $e->getMessage());
            }
        } else {
            set_flash('success', 'Thêm sản phẩm thành công!');
            header("Location: products.php");
            exit;
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Thêm Sản Phẩm Mới - HieuMini Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <div class="admin-main">
        <header class="admin-header">
            <h2 style="font-size: 1.3rem; font-weight: 800; color: #fff;">Thêm Sản Phẩm Công Nghệ Mới</h2>
            <a href="products.php" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
            </a>
        </header>

        <main class="admin-content">
            <?php echo display_flash(); ?>

            <div class="glass-panel" style="padding: 30px; max-width: 900px;">
                <form action="product_add.php" method="POST">
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Tên sản phẩm <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="VD: iPhone 16 Pro Max 256GB" required>
                        </div>
                        <div class="form-group">
                            <label>Danh mục <span style="color: var(--danger);">*</span></label>
                            <select name="category_id" class="form-control">
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Thương hiệu:</label>
                            <input type="text" name="brand" class="form-control" placeholder="VD: Apple, ASUS..." required>
                        </div>
                        <div class="form-group">
                            <label>Giá niêm yết (VNĐ) <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="price" class="form-control" placeholder="VD: 34990000" required>
                        </div>
                        <div class="form-group">
                            <label>Giá khuyến mãi (VNĐ):</label>
                            <input type="number" name="sale_price" class="form-control" placeholder="VD: 31990000">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Số lượng trong kho:</label>
                        <input type="number" name="stock_quantity" class="form-control" value="20" required>
                    </div>

                    <div class="form-group">
                        <label>Mô tả ngắn (Hiển thị ở trang danh sách):</label>
                        <input type="text" name="short_desc" class="form-control" placeholder="VD: Chip A18 Pro 3nm, Camera nút điều khiển mới...">
                    </div>

                    <div class="form-group">
                        <label>Mô tả chi tiết:</label>
                        <textarea name="description" class="form-control" rows="5" placeholder="Chi tiết về thiết kế, tính năng, điểm nổi bật..."></textarea>
                    </div>

                    <div class="form-group">
                        <label>Thông số kỹ thuật (Định dạng JSON):</label>
                        <textarea name="specifications" class="form-control" rows="4">{"Màn hình": "6.9 inch OLED 120Hz", "Chip CPU": "Apple A18 Pro", "RAM": "8 GB", "Bộ nhớ": "256 GB", "Pin": "4685 mAh"}</textarea>
                    </div>

                    <div style="display: flex; gap: 24px; margin-bottom: 24px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff;">
                            <input type="checkbox" name="is_featured" value="1" checked>
                            <span>Sản phẩm nổi bật</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff;">
                            <input type="checkbox" name="is_flash_sale" value="1">
                            <span style="color: var(--accent-pink); font-weight: 700;">Gắn nhãn Flash Sale</span>
                        </label>
                    </div>

                    <div style="display: flex; gap: 14px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-cloud-arrow-up"></i> Lưu sản phẩm
                        </button>
                        <a href="products.php" class="btn btn-outline">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
