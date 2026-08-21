<?php
/**
 * Quản Lý Danh Mục Thời Trang - Admin HieuMini
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Thêm danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_category') {
    $name = trim($_POST['name'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $image = trim($_POST['image'] ?? 'cat_ao_thun.jpg');

    if (!empty($name)) {
        $slug = create_slug($name);
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, description, image, status) VALUES (?, ?, ?, ?, 1)");
        $stmt->execute([$name, $slug, $description, $image]);
        set_flash('success', 'Thêm danh mục mới thành công!');
        redirect('categories.php');
    } else {
        set_flash('danger', 'Tên danh mục không được để trống.');
    }
}

// Xóa danh mục
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$delId]);
    set_flash('success', 'Đã xóa danh mục!');
    redirect('categories.php');
}

$categories = $pdo->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.id ASC")->fetchAll();

$adminTitle = "Quản Lý Danh Mục Thời Trang";
require_once __DIR__ . '/includes/header.php';
?>

<div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 30px;">
    <!-- Form Thêm Danh Mục -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-solid fa-plus text-accent"></i> Thêm Danh Mục Mới</h3>
        </div>
        <div class="admin-card-body">
            <form action="categories.php" method="POST">
                <input type="hidden" name="action" value="add_category">

                <div class="admin-form-group">
                    <label class="admin-form-label">Tên danh mục <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" placeholder="VD: Áo Khoác Gió Nam" required>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">Mô tả ngắn</label>
                    <textarea name="description" rows="3" class="form-control" placeholder="Mô tả tóm tắt danh mục..."></textarea>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">Ảnh danh mục mẫu</label>
                    <select name="image" class="form-control">
                        <option value="cat_ao_thun.jpg">cat_ao_thun.jpg</option>
                        <option value="cat_ao_somi.jpg">cat_ao_somi.jpg</option>
                        <option value="cat_ao_khoac.jpg">cat_ao_khoac.jpg</option>
                        <option value="cat_quan_jeans.jpg">cat_quan_jeans.jpg</option>
                        <option value="cat_quan_kaki.jpg">cat_quan_kaki.jpg</option>
                        <option value="cat_vay_dam.jpg">cat_vay_dam.jpg</option>
                        <option value="cat_phu_kien.jpg">cat_phu_kien.jpg</option>
                    </select>
                </div>

                <button type="submit" class="btn btn-accent btn-block">
                    <i class="fa-solid fa-plus"></i> Thêm Danh Mục
                </button>
            </form>
        </div>
    </div>

    <!-- Bảng Danh Sách Danh Mục -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-solid fa-layer-group text-accent"></i> Danh Sách Danh Mục (<?= count($categories) ?>)</h3>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th style="width: 60px;">Ảnh</th>
                        <th>Tên Danh Mục / Slug</th>
                        <th>Số Sản Phẩm</th>
                        <th>Trạng Thái</th>
                        <th style="width: 80px; text-align: center;">Thao Tác</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($categories as $cat): ?>
                        <tr>
                            <td>
                                <img src="../assets/images/categories/<?= htmlspecialchars($cat['image'] ?? 'cat_ao_thun.jpg') ?>" alt="Thumb" style="width: 44px; height: 32px; object-fit: cover; border-radius: 4px;">
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--primary);"><?= htmlspecialchars($cat['name']) ?></div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);"><code><?= htmlspecialchars($cat['slug']) ?></code></div>
                            </td>
                            <td>
                                <span class="badge badge-primary"><?= $cat['product_count'] ?> sản phẩm</span>
                            </td>
                            <td>
                                <span class="badge badge-success">Hiển thị</span>
                            </td>
                            <td style="text-align: center;">
                                <a href="categories.php?delete=<?= $cat['id'] ?>" class="btn btn-outline btn-sm btn-delete-confirm" style="color: #ef4444;" title="Xóa">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
