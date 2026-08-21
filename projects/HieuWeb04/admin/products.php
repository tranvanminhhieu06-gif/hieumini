<?php
$admin_title = 'Quản Lý Sản Phẩm Gia Dụng';
require_once __DIR__ . '/header.php';

// Handle Delete Product
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $delStmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $delStmt->execute([$delId]);
    set_flash('success', 'Đã xóa sản phẩm thành công!');
    header('Location: products.php');
    exit;
}

// Handle Add / Edit Product
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['save_product'])) {
    $editId = (int)($_POST['product_id'] ?? 0);
    $categoryId = (int)$_POST['category_id'];
    $name = clean_input($_POST['name'] ?? '');
    $slug = clean_input($_POST['slug'] ?? '');
    if (empty($slug)) {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $name)));
    }
    $price = max(0, (float)$_POST['price']);
    $oldPrice = (!empty($_POST['old_price']) && (float)$_POST['old_price'] > 0) ? (float)$_POST['old_price'] : null;
    $image = clean_input($_POST['image'] ?? 'air_fryer.jpg');
    $stock = max(0, (int)$_POST['stock']);
    $shortDesc = clean_input($_POST['short_description'] ?? '');
    $description = sanitize_html($_POST['description'] ?? '');
    $specs = clean_input($_POST['specs'] ?? '');
    $isFeatured = isset($_POST['is_featured']) ? 1 : 0;
    $isBestSeller = isset($_POST['is_best_seller']) ? 1 : 0;
    $isFlashSale = isset($_POST['is_flash_sale']) ? 1 : 0;
    $discountPercent = ($oldPrice && $oldPrice > $price) ? round((($oldPrice - $price) / $oldPrice) * 100) : 0;

    if (empty($name) || $price <= 0 || $categoryId <= 0) {
        set_flash('error', 'Vui lòng nhập tên sản phẩm, danh mục và giá bán hợp lệ!');
    } else {
        if ($editId > 0) {
            $uStmt = $pdo->prepare("UPDATE products SET category_id=?, name=?, slug=?, price=?, old_price=?, image=?, short_description=?, description=?, specs=?, stock=?, is_featured=?, is_best_seller=?, is_flash_sale=?, discount_percent=? WHERE id=?");
            $uStmt->execute([$categoryId, $name, $slug, $price, $oldPrice, $image, $shortDesc, $description, $specs, $stock, $isFeatured, $isBestSeller, $isFlashSale, $discountPercent, $editId]);
            set_flash('success', 'Đã cập nhật thông tin sản phẩm thành công!');
        } else {
            $iStmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, price, old_price, image, short_description, description, specs, stock, is_featured, is_best_seller, is_flash_sale, discount_percent) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $iStmt->execute([$categoryId, $name, $slug, $price, $oldPrice, $image, $shortDesc, $description, $specs, $stock, $isFeatured, $isBestSeller, $isFlashSale, $discountPercent]);
            set_flash('success', 'Đã thêm sản phẩm mới thành công!');
        }
    }
    header('Location: products.php');
    exit;
}

// Fetch all categories for selector
$cats = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

// Fetch products list
$prods = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.id DESC")->fetchAll();

// Check if editing
$editItem = null;
if (isset($_GET['edit'])) {
    $eId = (int)$_GET['edit'];
    $eStmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $eStmt->execute([$eId]);
    $editItem = $eStmt->fetch();
}
?>

<div class="d-flex justify-content-between align-items-center mb-4">
  <h4 class="fw-bold m-0"><i class="fas fa-boxes-stacked text-primary me-2"></i> Danh Sách Sản Phẩm (<?php echo count($prods); ?>)</h4>
  <button class="btn btn-primary-custom" data-bs-toggle="collapse" data-bs-target="#addProductForm">
    <i class="fas fa-plus me-1"></i> <?php echo $editItem ? 'Sửa Sản Phẩm' : 'Thêm Sản Phẩm Mới'; ?>
  </button>
</div>

<!-- Flash Message -->
<?php $flash = get_flash(); if ($flash): ?>
  <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : htmlspecialchars($flash['type']); ?> alert-dismissible fade show">
    <?php echo htmlspecialchars($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<!-- Add / Edit Product Collapse Form -->
<div class="collapse <?php echo $editItem ? 'show' : ''; ?> mb-4" id="addProductForm">
  <div class="bg-white p-4 rounded-4 border shadow-sm">
    <h5 class="fw-bold mb-3"><?php echo $editItem ? 'Chỉnh Sửa Sản Phẩm #' . $editItem['id'] : 'Thêm Sản Phẩm Gia Dụng Mới'; ?></h5>
    
    <form action="products.php" method="POST">
      <input type="hidden" name="product_id" value="<?php echo $editItem ? $editItem['id'] : 0; ?>">
      
      <div class="row g-3">
        <div class="col-md-6">
          <label class="form-label small fw-semibold">Tên sản phẩm *</label>
          <input type="text" name="name" class="form-control" required value="<?php echo htmlspecialchars($editItem['name'] ?? ''); ?>" placeholder="VD: Nồi Chiên Không Dầu DatCyber">
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-semibold">Danh mục *</label>
          <select name="category_id" class="form-select" required>
            <?php foreach ($cats as $c): ?>
              <option value="<?php echo $c['id']; ?>" <?php echo ($editItem && $editItem['category_id'] == $c['id']) ? 'selected' : ''; ?>>
                <?php echo htmlspecialchars($c['name']); ?>
              </option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-semibold">Tên file ảnh trong `products/`</label>
          <input type="text" name="image" class="form-control" value="<?php echo htmlspecialchars($editItem['image'] ?? 'air_fryer.jpg'); ?>" required>
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-semibold">Giá bán (VNĐ) *</label>
          <input type="number" name="price" class="form-control" required value="<?php echo $editItem['price'] ?? ''; ?>" placeholder="2490000">
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-semibold">Giá gốc / Cũ (VNĐ)</label>
          <input type="number" name="old_price" class="form-control" value="<?php echo $editItem['old_price'] ?? ''; ?>" placeholder="3290000">
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-semibold">Số lượng tồn kho</label>
          <input type="number" name="stock" class="form-control" value="<?php echo $editItem['stock'] ?? 50; ?>" required>
        </div>

        <div class="col-md-3">
          <label class="form-label small fw-semibold">Slug URL (Tự động nếu để trống)</label>
          <input type="text" name="slug" class="form-control" value="<?php echo htmlspecialchars($editItem['slug'] ?? ''); ?>">
        </div>

        <div class="col-12">
          <label class="form-label small fw-semibold">Mô tả ngắn</label>
          <textarea name="short_description" class="form-control" rows="2"><?php echo htmlspecialchars($editItem['short_description'] ?? ''); ?></textarea>
        </div>

        <div class="col-12">
          <label class="form-label small fw-semibold">Thông số kỹ thuật (Mỗi dòng 1 thông số dạng: Tên: Giá trị)</label>
          <textarea name="specs" class="form-control" rows="3"><?php echo htmlspecialchars($editItem['specs'] ?? ''); ?></textarea>
        </div>

        <div class="col-12">
          <label class="form-label small fw-semibold">Mô tả chi tiết HTML</label>
          <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($editItem['description'] ?? ''); ?></textarea>
        </div>

        <div class="col-12 d-flex gap-4">
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_featured" id="featCheck" <?php echo ($editItem && $editItem['is_featured']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="featCheck">Sản phẩm nổi bật</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_best_seller" id="bestCheck" <?php echo ($editItem && $editItem['is_best_seller']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="bestCheck">Bán chạy nhất</label>
          </div>
          <div class="form-check">
            <input class="form-check-input" type="checkbox" name="is_flash_sale" id="flashCheck" <?php echo ($editItem && $editItem['is_flash_sale']) ? 'checked' : ''; ?>>
            <label class="form-check-label" for="flashCheck">Flash Sale</label>
          </div>
        </div>

        <div class="col-12 d-flex gap-2">
          <button type="submit" name="save_product" class="btn btn-primary-custom">
            <i class="fas fa-save me-1"></i> Lưu Sản Phẩm
          </button>
          <?php if ($editItem): ?>
            <a href="products.php" class="btn btn-secondary">Hủy bỏ</a>
          <?php endif; ?>
        </div>
      </div>
    </form>
  </div>
</div>

<!-- Products Table -->
<div class="bg-white p-4 rounded-4 border shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>ID</th>
          <th>Ảnh</th>
          <th>Tên Sản Phẩm</th>
          <th>Danh Mục</th>
          <th>Giá Bán</th>
          <th>Tồn Kho</th>
          <th>Đánh Giá</th>
          <th class="text-center">Thao Tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($prods as $p): ?>
          <tr>
            <td><strong>#<?php echo $p['id']; ?></strong></td>
            <td>
              <img src="../assets/images/products/<?php echo htmlspecialchars($p['image']); ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border">
            </td>
            <td>
              <div class="fw-bold" style="font-size: 0.95rem;"><?php echo htmlspecialchars($p['name']); ?></div>
              <?php if ($p['is_flash_sale']): ?><span class="badge bg-danger me-1">Flash Sale</span><?php endif; ?>
              <?php if ($p['is_best_seller']): ?><span class="badge bg-primary">Best Seller</span><?php endif; ?>
            </td>
            <td><span class="badge bg-light text-dark border"><?php echo htmlspecialchars($p['category_name']); ?></span></td>
            <td>
              <div class="text-danger fw-bold"><?php echo format_price($p['price']); ?></div>
              <?php if ($p['old_price']): ?>
                <small class="text-muted text-decoration-line-through"><?php echo format_price($p['old_price']); ?></small>
              <?php endif; ?>
            </td>
            <td><span class="badge bg-info text-dark"><?php echo $p['stock']; ?></span></td>
            <td>⭐ <?php echo $p['rating']; ?> (<?php echo $p['review_count']; ?>)</td>
            <td class="text-center">
              <a href="products.php?edit=<?php echo $p['id']; ?>" class="btn btn-sm btn-outline-primary me-1" title="Sửa"><i class="fas fa-edit"></i></a>
              <a href="products.php?delete=<?php echo $p['id']; ?>" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" class="btn btn-sm btn-outline-danger" title="Xóa"><i class="fas fa-trash"></i></a>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>
