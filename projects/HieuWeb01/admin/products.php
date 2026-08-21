<?php
/**
 * Quản Lý Sản Phẩm - Admin HieuMini
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Xử lý xóa sản phẩm
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM products WHERE id = ?")->execute([$delId]);
    set_flash('success', 'Đã xóa sản phẩm thành công!');
    redirect('products.php');
}

$catFilter = isset($_GET['category_id']) ? (int)$_GET['category_id'] : 0;
$keyword = trim($_GET['keyword'] ?? '');

$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($catFilter > 0) {
    $query .= " AND p.category_id = ?";
    $params[] = $catFilter;
}

if ($keyword) {
    $query .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

$query .= " ORDER BY p.id DESC";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

$adminTitle = "Quản Lý Sản Phẩm Thời Trang";
require_once __DIR__ . '/includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
    <div>
        <h2 style="font-size: 1.4rem; color: var(--primary); margin-bottom: 4px;">Danh Sách Sản Phẩm (<?= count($products) ?>)</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Quản lý, thêm mới, chỉnh sửa thông tin giá và tồn kho sản phẩm</p>
    </div>
    <a href="product_add.php" class="btn btn-accent">
        <i class="fa-solid fa-plus"></i> Thêm Sản Phẩm Mới
    </a>
</div>

<!-- Toolbar Bộ Lọc & Tìm Kiếm -->
<div class="admin-card" style="margin-bottom: 20px;">
    <div class="admin-card-body" style="padding: 16px 20px;">
        <form method="GET" style="display: flex; gap: 14px; align-items: center; flex-wrap: wrap;">
            <div style="flex: 1; min-width: 200px;">
                <input type="text" name="keyword" class="form-control" placeholder="Tìm theo tên sản phẩm, mã SKU..." value="<?= htmlspecialchars($keyword) ?>">
            </div>

            <div style="width: 220px;">
                <select name="category_id" class="form-control">
                    <option value="0">-- Tất cả danh mục --</option>
                    <?php foreach ($categories as $cat): ?>
                        <option value="<?= $cat['id'] ?>" <?= $catFilter === $cat['id'] ? 'selected' : '' ?>>
                            <?= htmlspecialchars($cat['name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <button type="submit" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-filter"></i> Lọc Dữ Liệu
            </button>

            <?php if ($keyword || $catFilter): ?>
                <a href="products.php" class="btn btn-outline btn-sm">Xóa lọc</a>
            <?php endif; ?>
        </form>
    </div>
</div>

<!-- Bảng Sản Phẩm -->
<div class="admin-card">
    <div class="admin-card-body" style="padding: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Hình ảnh</th>
                    <th>Tên sản phẩm / SKU</th>
                    <th>Danh mục</th>
                    <th>Giá bán</th>
                    <th>Tồn kho</th>
                    <th>Kích cỡ (Sizes)</th>
                    <th>Nổi bật</th>
                    <th style="width: 140px; text-align: center;">Thao tác</th>
                </tr>
            </thead>
            <tbody>
                <?php if (empty($products)): ?>
                    <tr><td colspan="8" style="text-align: center; padding: 30px;">Không có sản phẩm nào.</td></tr>
                <?php else: ?>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td>
                                <img src="../assets/images/products/<?= htmlspecialchars($p['image']) ?>" alt="Thumb" style="width: 50px; height: 50px; object-fit: cover; border-radius: 6px; border: 1px solid var(--border);">
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--primary);"><?= htmlspecialchars($p['name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">SKU: <code><?= htmlspecialchars($p['sku']) ?></code></div>
                            </td>
                            <td>
                                <span class="badge badge-secondary"><?= htmlspecialchars($p['category_name']) ?></span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: #ef4444;"><?= format_price($p['discount_price'] ?? $p['price']) ?></div>
                                <?php if ($p['discount_price']): ?>
                                    <div style="font-size: 0.75rem; text-decoration: line-through; color: var(--text-light);"><?= format_price($p['price']) ?></div>
                                <?php endif; ?>
                            </td>
                            <td>
                                <?php if ($p['stock'] <= 20): ?>
                                    <span class="badge badge-danger"><?= $p['stock'] ?> (Sắp hết)</span>
                                <?php else: ?>
                                    <span class="badge badge-success"><?= $p['stock'] ?></span>
                                <?php endif; ?>
                            </td>
                            <td>
                                <span style="font-size: 0.8rem; color: var(--secondary);"><?= htmlspecialchars($p['sizes']) ?></span>
                            </td>
                            <td>
                                <?= $p['featured'] ? '<span class="badge badge-warning">Hot</span>' : '<span style="color: #94a3b8;">-</span>' ?>
                            </td>
                            <td style="text-align: center;">
                                <a href="product_edit.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm" title="Chỉnh sửa">
                                    <i class="fa-solid fa-pen-to-square"></i>
                                </a>
                                <a href="products.php?delete=<?= $p['id'] ?>" class="btn btn-outline btn-sm btn-delete-confirm" style="color: #ef4444;" title="Xóa">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
