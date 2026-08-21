<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_admin();

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$product = null;

if ($pdo && $product_id > 0) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();
    } catch (Exception $e) {}
}

if (!$product) {
    $product = [
        'id' => $product_id,
        'category_id' => 1,
        'name' => 'iPhone 16 Pro Max 256GB Titan Sa Mạc',
        'brand' => 'Apple',
        'price' => 34990000,
        'sale_price' => 31990000,
        'stock_quantity' => 25,
        'short_desc' => 'Chip A18 Pro 3nm cực mạnh, Camera nút điều khiển mới...',
        'description' => 'Mô tả chi tiết sản phẩm...',
        'specifications' => '{"Màn hình": "6.9 inch OLED", "Chip": "A18 Pro"}',
        'is_featured' => 1,
        'is_flash_sale' => 1
    ];
}

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

    if ($pdo) {
        try {
            $stmt_up = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, brand = ?, price = ?, sale_price = ?, stock_quantity = ?, short_desc = ?, description = ?, specifications = ?, is_featured = ?, is_flash_sale = ? WHERE id = ?");
            $stmt_up->execute([
                $category_id, $name, $brand, $price, $sale_price,
                $stock_quantity, $short_desc, $description, $specifications,
                $is_featured, $is_flash_sale, $product_id
            ]);
            set_flash('success', 'Cập nhật thông tin sản phẩm thành công!');
            header("Location: products.php");
            exit;
        } catch (Exception $e) {
            set_flash('danger', 'Lỗi cập nhật: ' . $e->getMessage());
        }
    } else {
        set_flash('success', 'Cập nhật sản phẩm thành công!');
        header("Location: products.php");
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chỉnh Sửa Sản Phẩm #<?php echo $product['id']; ?> - HieuMini Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <div class="admin-main">
        <header class="admin-header">
            <h2 style="font-size: 1.3rem; font-weight: 800; color: #fff;">Chỉnh Sửa Sản Phẩm #<?php echo $product['id']; ?></h2>
            <a href="products.php" class="btn btn-outline btn-sm">
                <i class="fa-solid fa-arrow-left"></i> Quay lại danh sách
            </a>
        </header>

        <main class="admin-content">
            <?php echo display_flash(); ?>

            <div class="glass-panel" style="padding: 30px; max-width: 900px;">
                <form action="product_edit.php?id=<?php echo $product['id']; ?>" method="POST">
                    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Tên sản phẩm <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="name" class="form-control" value="<?php echo htmlspecialchars($product['name']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Danh mục <span style="color: var(--danger);">*</span></label>
                            <select name="category_id" class="form-control">
                                <?php foreach ($categories as $c): ?>
                                    <option value="<?php echo $c['id']; ?>" <?php echo $product['category_id'] == $c['id'] ? 'selected' : ''; ?>><?php echo htmlspecialchars($c['name']); ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
                        <div class="form-group">
                            <label>Thương hiệu:</label>
                            <input type="text" name="brand" class="form-control" value="<?php echo htmlspecialchars($product['brand']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Giá niêm yết (VNĐ) <span style="color: var(--danger);">*</span></label>
                            <input type="number" name="price" class="form-control" value="<?php echo (int)$product['price']; ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Giá khuyến mãi (VNĐ):</label>
                            <input type="number" name="sale_price" class="form-control" value="<?php echo !empty($product['sale_price']) ? (int)$product['sale_price'] : ''; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Số lượng trong kho:</label>
                        <input type="number" name="stock_quantity" class="form-control" value="<?php echo $product['stock_quantity']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Mô tả ngắn:</label>
                        <input type="text" name="short_desc" class="form-control" value="<?php echo htmlspecialchars($product['short_desc'] ?? ''); ?>">
                    </div>

                    <div class="form-group">
                        <label>Mô tả chi tiết:</label>
                        <textarea name="description" class="form-control" rows="5"><?php echo htmlspecialchars($product['description'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label>Thông số kỹ thuật (JSON):</label>
                        <textarea name="specifications" class="form-control" rows="4"><?php echo htmlspecialchars($product['specifications'] ?? ''); ?></textarea>
                    </div>

                    <div style="display: flex; gap: 24px; margin-bottom: 24px;">
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff;">
                            <input type="checkbox" name="is_featured" value="1" <?php echo !empty($product['is_featured']) ? 'checked' : ''; ?>>
                            <span>Sản phẩm nổi bật</span>
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; color: #fff;">
                            <input type="checkbox" name="is_flash_sale" value="1" <?php echo !empty($product['is_flash_sale']) ? 'checked' : ''; ?>>
                            <span style="color: var(--accent-pink); font-weight: 700;">Gắn nhãn Flash Sale</span>
                        </label>
                    </div>

                    <div style="display: flex; gap: 14px;">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                        </button>
                        <a href="products.php" class="btn btn-outline">Hủy bỏ</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</body>
</html>
