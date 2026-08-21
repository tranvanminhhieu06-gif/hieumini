<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_admin();

// Xử lý Xóa sản phẩm
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'Đã xóa sản phẩm thành công!');
        } catch (Exception $e) {
            set_flash('danger', 'Lỗi khi xóa sản phẩm: ' . $e->getMessage());
        }
    }
    header("Location: products.php");
    exit;
}

$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$products = [];

if ($pdo) {
    try {
        if (!empty($search)) {
            $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.name LIKE ? OR p.brand LIKE ? ORDER BY p.id DESC");
            $stmt->execute(["%$search%", "%$search%"]);
            $products = $stmt->fetchAll();
        } else {
            $stmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC");
            $products = $stmt->fetchAll();
        }
    } catch (Exception $e) {}
}

if (empty($products)) {
    $products = [
        ['id' => 1, 'category_name' => 'Điện thoại', 'name' => 'iPhone 16 Pro Max 256GB Titan Sa Mạc', 'brand' => 'Apple', 'price' => 34990000, 'sale_price' => 31990000, 'stock_quantity' => 25, 'is_flash_sale' => 1, 'is_featured' => 1],
        ['id' => 2, 'category_name' => 'Điện thoại', 'name' => 'Samsung Galaxy S24 Ultra 5G 12GB/256GB', 'brand' => 'Samsung', 'price' => 31990000, 'sale_price' => 26990000, 'stock_quantity' => 18, 'is_flash_sale' => 1, 'is_featured' => 1],
        ['id' => 3, 'category_name' => 'Laptop & Macbook', 'name' => 'MacBook Pro 14 M3 Pro (18GB/512GB SSD)', 'brand' => 'Apple', 'price' => 49990000, 'sale_price' => 45490000, 'stock_quantity' => 12, 'is_flash_sale' => 0, 'is_featured' => 1],
        ['id' => 4, 'category_name' => 'Laptop & Macbook', 'name' => 'Laptop Gaming ASUS ROG Zephyrus G16 OLED', 'brand' => 'ASUS', 'price' => 54990000, 'sale_price' => 49990000, 'stock_quantity' => 8, 'is_flash_sale' => 1, 'is_featured' => 1],
        ['id' => 5, 'category_name' => 'Máy tính bảng', 'name' => 'iPad Pro 11 inch M4 Wi-Fi 256GB Ultra Thin', 'brand' => 'Apple', 'price' => 28990000, 'sale_price' => 26990000, 'stock_quantity' => 15, 'is_flash_sale' => 0, 'is_featured' => 1],
        ['id' => 7, 'category_name' => 'Tai nghe & Âm thanh', 'name' => 'Tai nghe Sony WH-1000XM5 Chống Ồn Cao Cấp', 'brand' => 'Sony', 'price' => 8490000, 'sale_price' => 6990000, 'stock_quantity' => 30, 'is_flash_sale' => 1, 'is_featured' => 1]
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Sản Phẩm - HieuMini Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <div class="admin-main">
        <header class="admin-header">
            <h2 style="font-size: 1.3rem; font-weight: 800; color: #fff;">Quản Lý Danh Sách Sản Phẩm</h2>
            <a href="product_add.php" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-plus"></i> Thêm Sản Phẩm Mới
            </a>
        </header>

        <main class="admin-content">
            <?php echo display_flash(); ?>

            <div class="admin-card">
                <div class="admin-card-header" style="flex-wrap: wrap; gap: 14px;">
                    <div class="admin-card-title">
                        <i class="fa-solid fa-boxes-stacked" style="color: var(--primary);"></i> Danh Sách Thiết Bị (<?php echo count($products); ?>)
                    </div>

                    <form action="products.php" method="GET" style="display: flex; gap: 8px;">
                        <input type="text" name="search" class="form-control" placeholder="Tìm tên, thương hiệu..." value="<?php echo htmlspecialchars($search); ?>" style="width: 240px; padding: 6px 12px; font-size: 0.85rem;">
                        <button type="submit" class="btn btn-outline btn-sm">Tìm kiếm</button>
                    </form>
                </div>

                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th style="width: 50px;">ID</th>
                                <th>Sản phẩm</th>
                                <th>Danh mục</th>
                                <th>Hãng</th>
                                <th>Giá bán / Sale</th>
                                <th>Tồn kho</th>
                                <th>Nhãn</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($products as $p): 
                                $adm_thumb = !empty($p['thumbnail']) && file_exists(__DIR__ . '/../assets/images/' . $p['thumbnail']) ? '../assets/images/' . $p['thumbnail'] : '../assets/images/default_prod.png';
                            ?>
                                <tr>
                                    <td><strong style="color: var(--text-muted); font-family: monospace;">#<?php echo $p['id']; ?></strong></td>
                                    <td>
                                        <div style="display: flex; align-items: center; gap: 12px;">
                                            <img src="<?php echo $adm_thumb; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" style="width: 40px; height: 40px; border-radius: var(--radius-sm); object-fit: cover; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1);">
                                            <strong style="color: #fff; font-size: 0.92rem;"><?php echo htmlspecialchars($p['name']); ?></strong>
                                        </div>
                                    </td>
                                    <td><span class="badge badge-info"><?php echo htmlspecialchars($p['category_name'] ?? 'Công nghệ'); ?></span></td>
                                    <td><strong><?php echo htmlspecialchars($p['brand']); ?></strong></td>
                                    <td>
                                        <div style="font-weight: 700; color: #f43f5e;"><?php echo format_currency(!empty($p['sale_price']) ? $p['sale_price'] : $p['price']); ?></div>
                                        <?php if (!empty($p['sale_price'])): ?>
                                            <div style="font-size: 0.75rem; color: var(--text-muted); text-decoration: line-through;"><?php echo format_currency($p['price']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span style="font-weight: 700; color: <?php echo $p['stock_quantity'] > 5 ? 'var(--success)' : 'var(--danger)'; ?>;">
                                            <?php echo $p['stock_quantity']; ?> chiếc
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($p['is_flash_sale'])): ?>
                                            <span class="badge badge-warning" style="font-size: 0.7rem;">Flash Sale</span>
                                        <?php endif; ?>
                                        <?php if (!empty($p['is_featured'])): ?>
                                            <span class="badge badge-primary" style="font-size: 0.7rem;">Nổi bật</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <a href="product_edit.php?id=<?php echo $p['id']; ?>" class="btn-icon edit" title="Chỉnh sửa">
                                                <i class="fa-solid fa-pen-to-square"></i>
                                            </a>
                                            <a href="products.php?delete=<?php echo $p['id']; ?>" class="btn-icon delete btn-delete-confirm" title="Xóa">
                                                <i class="fa-solid fa-trash-can"></i>
                                            </a>
                                        </div>
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
