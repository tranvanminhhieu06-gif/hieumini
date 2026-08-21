<?php
// admin/product-edit.php - Edit Product Form with Image Upload & Preset Select
$page_title = "Chỉnh Sửa Sản Phẩm";
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('danger', 'Sản phẩm không tồn tại.');
    header('Location: products.php');
    exit;
}

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $category_id = (int)$_POST['category_id'];
    $name = clean_input($_POST['name'] ?? '');
    $sku = clean_input($_POST['sku'] ?? '');
    $price = (float)($_POST['price'] ?? 0);
    $sale_price = !empty($_POST['sale_price']) ? (float)$_POST['sale_price'] : null;
    $stock_quantity = (int)($_POST['stock_quantity'] ?? 100);
    $description = clean_input($_POST['description'] ?? '');
    $specification = clean_input($_POST['specification'] ?? '');
    $image_select = clean_input($_POST['image_select'] ?? $product['image']);
    $is_featured = isset($_POST['is_featured']) ? 1 : 0;
    $is_hot = isset($_POST['is_hot']) ? 1 : 0;
    $is_new = isset($_POST['is_new']) ? 1 : 0;

    // Check if user uploaded a new image file
    if (isset($_FILES['image_upload']) && $_FILES['image_upload']['error'] === UPLOAD_ERR_OK) {
        $file_tmp = $_FILES['image_upload']['tmp_name'];
        $file_name = $_FILES['image_upload']['name'];
        $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

        if (in_array($file_ext, $allowed)) {
            $new_img_name = 'prod_' . time() . '_' . rand(100, 999) . '.' . $file_ext;
            $upload_dest = __DIR__ . '/../assets/images/products/' . $new_img_name;
            if (move_uploaded_file($file_tmp, $upload_dest)) {
                $image_select = $new_img_name;
            }
        }
    }

    if (empty($name) || empty($sku) || $price <= 0 || $category_id <= 0) {
        $error = 'Vui lòng điền đầy đủ các thông tin bắt buộc (*).';
    } else {
        $upd = $pdo->prepare("
            UPDATE products 
            SET category_id = ?, name = ?, sku = ?, price = ?, sale_price = ?, image = ?, description = ?, specification = ?, stock_quantity = ?, is_featured = ?, is_hot = ?, is_new = ?
            WHERE id = ?
        ");
        $upd->execute([
            $category_id, $name, $sku, $price, $sale_price, $image_select, $description, $specification, $stock_quantity, $is_featured, $is_hot, $is_new, $id
        ]);

        set_flash('success', "Đã cập nhật sản phẩm '{$name}' thành công!");
        header('Location: products.php');
        exit;
    }
}
?>

<div class="data-table-card" style="max-width: 900px; margin: 0 auto;">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; border-bottom: 1px solid var(--border); padding-bottom: 16px;">
    <h3 style="font-size: 1.3rem; font-weight: 800; color: var(--dark);">Chỉnh Sửa Sản Phẩm #<?= $product['id'] ?></h3>
    <a href="products.php" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
  </div>

  <?php if (!empty($error)): ?>
    <div style="background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: var(--radius-md); margin-bottom: 20px; font-weight: 600;">
      <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
    </div>
  <?php endif; ?>

  <form action="product-edit.php?id=<?= $product['id'] ?>" method="POST" enctype="multipart/form-data">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 20px;">
      <div class="form-group">
        <label class="form-label">Tên sản phẩm *</label>
        <input type="text" name="name" required class="form-control" value="<?= htmlspecialchars($product['name']) ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Mã SKU *</label>
        <input type="text" name="sku" required class="form-control" value="<?= htmlspecialchars($product['sku']) ?>">
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 20px;">
      <div class="form-group">
        <label class="form-label">Danh mục *</label>
        <select name="category_id" required class="form-control">
          <?php foreach ($categories as $c): ?>
            <option value="<?= $c['id'] ?>" <?= $product['category_id'] == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="form-group">
        <label class="form-label">Giá gốc (VNĐ) *</label>
        <input type="number" name="price" required class="form-control" value="<?= (int)$product['price'] ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Giá khuyến mãi (VNĐ)</label>
        <input type="number" name="sale_price" class="form-control" value="<?= $product['sale_price'] ? (int)$product['sale_price'] : '' ?>">
      </div>
    </div>

    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
      <div class="form-group">
        <label class="form-label">Số lượng trong kho</label>
        <input type="number" name="stock_quantity" class="form-control" value="<?= $product['stock_quantity'] ?>">
      </div>
      <div class="form-group">
        <label class="form-label">Tải lên ảnh mới (Tùy chọn)</label>
        <input type="file" name="image_upload" class="form-control" accept="image/*">
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Hình ảnh hiện tại & mẫu có sẵn</label>
      <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 10px;">
        <img src="../assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="Ảnh hiện tại" style="width: 60px; height: 60px; border-radius: 8px; object-fit: contain; background: #f8fafc; border: 1px solid var(--border);">
        <select name="image_select" class="form-control" style="flex: 1;">
          <option value="<?= htmlspecialchars($product['image']) ?>">Giữ ảnh hiện tại (<?= htmlspecialchars($product['image']) ?>)</option>
          <?php for($i=1; $i<=30; $i++): ?>
            <option value="p<?= $i ?>.png">Ảnh mẫu p<?= $i ?>.png</option>
          <?php endfor; ?>
        </select>
      </div>
    </div>

    <div class="form-group">
      <label class="form-label">Mô tả sản phẩm</label>
      <textarea name="description" rows="4" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
    </div>

    <div class="form-group">
      <label class="form-label">Thông số kỹ thuật</label>
      <textarea name="specification" rows="3" class="form-control"><?= htmlspecialchars($product['specification']) ?></textarea>
    </div>

    <div style="display: flex; gap: 24px; margin-bottom: 24px; padding: 16px; background: var(--bg-light); border-radius: var(--radius-md);">
      <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
        <input type="checkbox" name="is_hot" value="1" <?= $product['is_hot'] ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--primary);">
        <i class="bi bi-fire" style="color: #ef4444;"></i> Sản phẩm Hot
      </label>
      <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
        <input type="checkbox" name="is_featured" value="1" <?= $product['is_featured'] ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--primary);">
        <i class="bi bi-star-fill" style="color: var(--accent-amber);"></i> Nổi bật trang chủ
      </label>
      <label style="display: flex; align-items: center; gap: 8px; font-weight: 600; cursor: pointer;">
        <input type="checkbox" name="is_new" value="1" <?= $product['is_new'] ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--primary);">
        <i class="bi bi-stars" style="color: var(--accent-emerald);"></i> Hàng mới về
      </label>
    </div>

    <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">
      <i class="bi bi-check2-circle"></i> Cập Nhật Sản Phẩm
    </button>
  </form>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
