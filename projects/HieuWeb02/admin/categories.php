<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_admin();

// Thêm danh mục
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $cat_name = sanitize($_POST['name'] ?? '');
    $cat_icon = sanitize($_POST['icon'] ?? 'fa-laptop');
    $cat_desc = sanitize($_POST['description'] ?? '');
    $cat_slug = slugify($cat_name);

    if (!empty($cat_name) && $pdo) {
        try {
            $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, description) VALUES (?, ?, ?, ?)");
            $stmt->execute([$cat_name, $cat_slug, $cat_icon, $cat_desc]);
            set_flash('success', 'Thêm danh mục mới thành công!');
        } catch (Exception $e) {
            set_flash('danger', 'Lỗi: ' . $e->getMessage());
        }
    }
    header("Location: categories.php");
    exit;
}

// Xóa danh mục
if (isset($_GET['delete'])) {
    $del_id = (int)$_GET['delete'];
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
            $stmt->execute([$del_id]);
            set_flash('success', 'Đã xóa danh mục thành công!');
        } catch (Exception $e) {
            set_flash('danger', 'Lỗi: ' . $e->getMessage());
        }
    }
    header("Location: categories.php");
    exit;
}

$categories = get_categories($pdo);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Danh Mục - HieuMini Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <div class="admin-main">
        <header class="admin-header">
            <h2 style="font-size: 1.3rem; font-weight: 800; color: #fff;">Quản Lý Danh Mục Sản Phẩm</h2>
        </header>

        <main class="admin-content">
            <?php echo display_flash(); ?>

            <div style="display: grid; grid-template-columns: 1fr 1.5fr; gap: 30px; align-items: start;">
                <!-- Form Thêm Danh Mục -->
                <div class="glass-panel" style="padding: 24px;">
                    <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 20px; color: #fff;">
                        <i class="fa-solid fa-plus-circle" style="color: var(--primary);"></i> Thêm Danh Mục Mới
                    </h3>

                    <form action="categories.php" method="POST">
                        <div class="form-group">
                            <label>Tên danh mục <span style="color: var(--danger);">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="VD: Phụ kiện Gaming" required>
                        </div>

                        <div class="form-group">
                            <label>FontAwesome Icon Class:</label>
                            <input type="text" name="icon" class="form-control" value="fa-gamepad" placeholder="fa-laptop, fa-mobile...">
                        </div>

                        <div class="form-group">
                            <label>Mô tả ngắn:</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Mô tả nhóm sản phẩm..."></textarea>
                        </div>

                        <button type="submit" name="add_category" class="btn btn-primary" style="width: 100%;">
                            <i class="fa-solid fa-folder-plus"></i> Lưu danh mục
                        </button>
                    </form>
                </div>

                <!-- Bảng Danh Mục -->
                <div class="admin-card">
                    <div class="admin-card-header">
                        <div class="admin-card-title">
                            <i class="fa-solid fa-layer-group" style="color: var(--accent);"></i> Danh Sách Danh Mục
                        </div>
                    </div>
                    <div class="table-responsive">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Icon</th>
                                    <th>Tên danh mục</th>
                                    <th>Slug</th>
                                    <th>Thao tác</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($categories as $cat): ?>
                                    <tr>
                                        <td><strong>#<?php echo $cat['id']; ?></strong></td>
                                        <td>
                                            <div style="width: 32px; height: 32px; border-radius: var(--radius-sm); background: rgba(6, 182, 212, 0.15); display: flex; align-items: center; justify-content: center; color: var(--accent);">
                                                <i class="fa-solid <?php echo htmlspecialchars($cat['icon']); ?>"></i>
                                            </div>
                                        </td>
                                        <td><strong style="color: #fff;"><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                                        <td><code style="color: var(--text-muted); font-size: 0.8rem;"><?php echo htmlspecialchars($cat['slug']); ?></code></td>
                                        <td>
                                            <a href="categories.php?delete=<?php echo $cat['id']; ?>" class="btn-icon delete btn-delete-confirm" title="Xóa">
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
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
