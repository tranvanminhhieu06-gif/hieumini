<?php
$admin_title = 'Quản Lý Danh Mục Sản Phẩm';
require_once __DIR__ . '/header.php';

// Handle Add Category
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_category'])) {
    $name = clean_input($_POST['name'] ?? '');
    $slug = clean_input($_POST['slug'] ?? '');
    $icon = clean_input($_POST['icon'] ?? 'fa-cube');
    $description = clean_input($_POST['description'] ?? '');

    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }

    if (!empty($name)) {
        $stmt = $pdo->prepare("INSERT INTO categories (name, slug, icon, description) VALUES (?, ?, ?, ?)");
        $stmt->execute([$name, $slug, $icon, $description]);
        set_flash('success', 'Đã thêm danh mục mới thành công!');
        header('Location: categories.php');
        exit;
    }
}

// Handle Delete Category
if (isset($_GET['delete'])) {
    $catId = (int)$_GET['delete'];
    $stmt = $pdo->prepare("DELETE FROM categories WHERE id = ?");
    $stmt->execute([$catId]);
    set_flash('success', 'Đã xóa danh mục thành công!');
    header('Location: categories.php');
    exit;
}

// Fetch categories with product counts
$cats = $pdo->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.id ASC")->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold m-0"><i class="fas fa-tags text-primary me-2"></i> Danh Mục Gia Dụng (<?php echo count($cats); ?>)</h4>
</div>

<?php $flash = get_flash(); if ($flash): ?>
  <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : htmlspecialchars($flash['type']); ?> alert-dismissible fade show">
    <?php echo htmlspecialchars($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<div class="row g-4">
  
  <!-- Add Category Form -->
  <div class="col-lg-4">
    <div class="bg-white p-4 rounded-4 border shadow-sm">
      <h5 class="fw-bold mb-3">Thêm Danh Mục Mới</h5>
      <form action="categories.php" method="POST">
        <div class="mb-3">
          <label class="form-label small fw-semibold">Tên danh mục *</label>
          <input type="text" name="name" class="form-control" required placeholder="VD: Thiết Bị Nhà Bếp">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Icon FontAwesome (class)</label>
          <input type="text" name="icon" class="form-control" value="fa-cube" placeholder="fa-utensils, fa-robot...">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Slug URL (Tự sinh nếu trống)</label>
          <input type="text" name="slug" class="form-control" placeholder="thiet-bi-nha-bep">
        </div>
        <div class="mb-3">
          <label class="form-label small fw-semibold">Mô tả ngắn</label>
          <textarea name="description" class="form-control" rows="3" placeholder="Mô tả danh mục..."></textarea>
        </div>
        <button type="submit" name="add_category" class="btn btn-primary-custom w-100 justify-content-center">
          <i class="fas fa-plus me-1"></i> Thêm Danh Mục
        </button>
      </form>
    </div>
  </div>

  <!-- Category List Table -->
  <div class="col-lg-8">
    <div class="bg-white p-4 rounded-4 border shadow-sm">
      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>ID</th>
              <th>Icon</th>
              <th>Tên Danh Mục</th>
              <th>Slug</th>
              <th>Số Sản Phẩm</th>
              <th class="text-center">Xóa</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($cats as $c): ?>
              <tr>
                <td><strong>#<?php echo $c['id']; ?></strong></td>
                <td><i class="fas <?php echo htmlspecialchars($c['icon']); ?> text-primary fs-5"></i></td>
                <td>
                  <div class="fw-bold"><?php echo htmlspecialchars($c['name']); ?></div>
                  <small class="text-muted"><?php echo htmlspecialchars($c['description']); ?></small>
                </td>
                <td><code><?php echo htmlspecialchars($c['slug']); ?></code></td>
                <td><span class="badge bg-primary rounded-pill"><?php echo $c['product_count']; ?></span></td>
                <td class="text-center">
                  <a href="categories.php?delete=<?php echo $c['id']; ?>" onclick="return confirm('Xóa danh mục này sẽ xóa các sản phẩm thuộc danh mục?');" class="btn btn-sm btn-outline-danger">
                    <i class="fas fa-trash"></i>
                  </a>
                </td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
