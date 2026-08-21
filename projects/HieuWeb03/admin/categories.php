<?php
// admin/categories.php - Manage Stationery Categories
$page_title = "Quản Lý Danh Mục Đồ Dùng";
require_once __DIR__ . '/includes/header.php';

// Add new category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = clean_input($_POST['name'] ?? '');
    $icon = clean_input($_POST['icon'] ?? 'bi-pencil-square');
    $badge = clean_input($_POST['badge'] ?? 'Phổ biến');
    $desc = clean_input($_POST['description'] ?? '');

    if (!empty($name)) {
        $slug = create_slug($name);
        $ins = $pdo->prepare("INSERT INTO categories (name, slug, description, icon, badge) VALUES (?, ?, ?, ?, ?)");
        $ins->execute([$name, $slug, $desc, $icon, $badge]);
        set_flash('success', "Đã thêm danh mục '{$name}' thành công!");
        header('Location: categories.php');
        exit;
    }
}

// Delete category
if (isset($_GET['del'])) {
    $del_id = (int)$_GET['del'];
    $pdo->prepare("DELETE FROM categories WHERE id = ?")->execute([$del_id]);
    set_flash('success', "Đã xóa danh mục thành công.");
    header('Location: categories.php');
    exit;
}

$categories = $pdo->query("
    SELECT c.*, count(p.id) as product_count
    FROM categories c
    LEFT JOIN products p ON c.id = p.category_id
    GROUP BY c.id
    ORDER BY c.id ASC
")->fetchAll();
?>

<div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 28px;">
  <!-- Add Category Form -->
  <div class="data-table-card" style="height: fit-content;">
    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 20px; color: var(--dark);">Thêm Danh Mục Mới</h3>
    <form action="categories.php" method="POST">
      <div class="form-group">
        <label class="form-label">Tên danh mục *</label>
        <input type="text" name="name" required class="form-control" placeholder="Ví dụ: Bút Vẽ Kỹ Thuật">
      </div>
      <div class="form-group">
        <label class="form-label">Icon Bootstrap (vd: bi-pen, bi-palette, bi-book)</label>
        <input type="text" name="icon" class="form-control" value="bi-pencil-square">
      </div>
      <div class="form-group">
        <label class="form-label">Huy hiệu tag</label>
        <input type="text" name="badge" class="form-control" value="Mới về">
      </div>
      <div class="form-group">
        <label class="form-label">Mô tả ngắn</label>
        <textarea name="description" rows="2" class="form-control" placeholder="Mô tả tóm tắt..."></textarea>
      </div>
      <button type="submit" name="add_category" value="1" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">
        <i class="bi bi-plus-lg"></i> Thêm Danh Mục
      </button>
    </form>
  </div>

  <!-- Categories Table -->
  <div class="data-table-card">
    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 20px; color: var(--dark);">Danh Sách Danh Mục Hiện Có</h3>
    <div style="overflow-x: auto;">
      <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Icon</th>
          <th>Tên Danh Mục</th>
          <th>Slug</th>
          <th>Số Sản Phẩm</th>
          <th style="text-align: right;">Thao Tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($categories as $c): ?>
        <tr>
          <td style="font-weight: 700; color: var(--muted);">#<?= $c['id'] ?></td>
          <td>
            <div style="width: 36px; height: 36px; border-radius: 8px; background: #e0e7ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.1rem;">
              <i class="bi <?= htmlspecialchars($c['icon']) ?>"></i>
            </div>
          </td>
          <td>
            <div style="font-weight: 700; color: var(--dark);"><?= htmlspecialchars($c['name']) ?></div>
            <span class="badge-tag badge-new" style="font-size: 0.7rem;"><?= htmlspecialchars($c['badge']) ?></span>
          </td>
          <td style="color: var(--muted); font-size: 0.85rem;"><code><?= htmlspecialchars($c['slug']) ?></code></td>
          <td>
            <span style="font-weight: 700; color: var(--primary); background: #eef2ff; padding: 4px 10px; border-radius: var(--radius-full);">
              <?= $c['product_count'] ?> món
            </span>
          </td>
          <td style="text-align: right;">
            <a href="categories.php?del=<?= $c['id'] ?>" class="btn btn-sm" style="background: #fee2e2; color: #dc2626;" onclick="return confirm('Bạn có chắc chắn muốn xóa danh mục này?');">
              <i class="bi bi-trash"></i>
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
