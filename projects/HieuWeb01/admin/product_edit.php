<?php
/**
 * Chỉnh Sửa Sản Phẩm - Admin HieuMini
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    redirect('products.php');
}

$stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    redirect('products.php');
}

$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY id ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $sku = strtoupper(trim($_POST['sku'] ?? ''));
    $price = (float)($_POST['price'] ?? 0);
    $discountPrice = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $stock = (int)($_POST['stock'] ?? 0);
    $sizes = trim($_POST['sizes'] ?? '');
    $colors = trim($_POST['colors'] ?? '');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;

    $errors = [];
    if (empty($name)) $errors[] = "Tên sản phẩm không được để trống.";
    if (empty($sku)) $errors[] = "Mã SKU không được để trống.";
    if ($price <= 0) $errors[] = "Giá sản phẩm phải lớn hơn 0.";

    $imageName = $product['image'];
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            $imageName = 'prod_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $uploadPath = __DIR__ . '/../../assets/images/products/' . $imageName;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
        }
    }

    if (empty($errors)) {
        $slug = create_slug($name) . '-' . $id;
        $upd = $pdo->prepare("UPDATE products SET category_id = ?, name = ?, slug = ?, sku = ?, price = ?, discount_price = ?, stock = ?, sizes = ?, colors = ?, description = ?, content = ?, image = ?, featured = ? WHERE id = ?");
        $upd->execute([
            $categoryId,
            $name,
            $slug,
            $sku,
            $price,
            $discountPrice,
            $stock,
            $sizes,
            $colors,
            $description,
            $content,
            $imageName,
            $featured,
            $id
        ]);

        set_flash('success', 'Cập nhật sản phẩm thành công!');
        redirect('products.php');
    } else {
        set_flash('danger', implode('<br>', $errors));
    }
}

$adminTitle = "Chỉnh Sửa Sản Phẩm: " . $product['name'];
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 24px;">
    <a href="products.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><i class="fa-solid fa-pen-to-square text-accent"></i> Cập Nhật Thông Tin: <?= htmlspecialchars($product['name']) ?></h3>
    </div>
    <div class="admin-card-body">
        <form action="product_edit.php?id=<?= $id ?>" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                <!-- Cột trái -->
                <div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Tên sản phẩm thời trang <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" value="<?= htmlspecialchars($product['name']) ?>" required>
                    </div>

                    <div class="form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Mã SKU <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control" value="<?= htmlspecialchars($product['sku']) ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Danh mục thời trang <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-control" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>" <?= $product['category_id'] == $cat['id'] ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($cat['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Giá gốc niêm yết (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" value="<?= $product['price'] ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Giá khuyến mãi (Nếu có)</label>
                            <input type="number" name="discount_price" class="form-control" value="<?= $product['discount_price'] ?? '' ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Số lượng tồn kho</label>
                            <input type="number" name="stock" class="form-control" value="<?= $product['stock'] ?>" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Các kích cỡ (Sizes)</label>
                            <input type="text" name="sizes" class="form-control" value="<?= htmlspecialchars($product['sizes']) ?>">
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Các màu sắc</label>
                        <input type="text" name="colors" class="form-control" value="<?= htmlspecialchars($product['colors']) ?>">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Mô tả ngắn gọn</label>
                        <textarea name="description" rows="3" class="form-control"><?= htmlspecialchars($product['description']) ?></textarea>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Nội dung chi tiết (HTML)</label>
                        <textarea name="content" rows="6" class="form-control"><?= htmlspecialchars($product['content']) ?></textarea>
                    </div>
                </div>

                <!-- Cột phải -->
                <div>
                    <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 20px;">
                        <h4 style="font-size: 1rem; margin-bottom: 12px;"><i class="fa-solid fa-image text-accent"></i> Ảnh Sản Phẩm Hiện Tại</h4>
                        
                        <div style="text-align: center; margin-bottom: 16px;">
                            <img src="../assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="Current" class="image-preview-target" style="width: 150px; height: 150px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); margin: 0 auto;">
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">Thay đổi ảnh mới:</label>
                            <input type="file" name="image" class="form-control image-upload-input" accept="image/*">
                        </div>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 24px;">
                        <h4 style="font-size: 1rem; margin-bottom: 12px;"><i class="fa-solid fa-sliders text-accent"></i> Tùy Chọn Nổi Bật</h4>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem;">
                            <input type="checkbox" name="featured" value="1" <?= $product['featured'] ? 'checked' : '' ?> style="width: 18px; height: 18px; accent-color: var(--accent);">
                            <span>Đánh dấu là <strong>Sản phẩm Nổi Bật (Hot)</strong></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-accent btn-lg btn-block">
                        <i class="fa-solid fa-floppy-disk"></i> Cập Nhật Thay Đổi
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
