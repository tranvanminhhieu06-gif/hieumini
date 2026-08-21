<?php
/**
 * Thêm Mới Sản Phẩm - Admin HieuMini
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY id ASC")->fetchAll();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $categoryId = (int)($_POST['category_id'] ?? 0);
    $name = trim($_POST['name'] ?? '');
    $sku = strtoupper(trim($_POST['sku'] ?? ''));
    $price = (float)($_POST['price'] ?? 0);
    $discountPrice = !empty($_POST['discount_price']) ? (float)$_POST['discount_price'] : null;
    $stock = (int)($_POST['stock'] ?? 50);
    $sizes = trim($_POST['sizes'] ?? 'S,M,L,XL');
    $colors = trim($_POST['colors'] ?? 'Đen,Trắng,Xám');
    $description = trim($_POST['description'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $featured = isset($_POST['featured']) ? 1 : 0;

    $errors = [];
    if (empty($name)) $errors[] = "Tên sản phẩm không được để trống.";
    if (empty($sku)) $errors[] = "Mã SKU không được để trống.";
    if ($price <= 0) $errors[] = "Giá sản phẩm phải lớn hơn 0.";

    // Xử lý upload ảnh hoặc chọn ảnh mặc định
    $imageName = 'ao_thun_streetwear.jpg'; // Default fallback
    if (isset($_FILES['image']) && $_FILES['image']['error'] === UPLOAD_ERR_OK) {
        $ext = strtolower(pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION));
        $allowed = ['jpg', 'jpeg', 'png', 'webp'];
        if (in_array($ext, $allowed)) {
            $imageName = 'prod_' . time() . '_' . rand(100, 999) . '.' . $ext;
            $uploadPath = __DIR__ . '/../../assets/images/products/' . $imageName;
            move_uploaded_file($_FILES['image']['tmp_name'], $uploadPath);
        } else {
            $errors[] = "File ảnh không đúng định dạng (cho phép jpg, png, webp).";
        }
    } else if (!empty($_POST['default_image_select'])) {
        $imageName = $_POST['default_image_select'];
    }

    if (empty($errors)) {
        $slug = create_slug($name) . '-' . rand(10, 99);
        $stmt = $pdo->prepare("INSERT INTO products (category_id, name, slug, sku, price, discount_price, stock, sizes, colors, description, content, image, featured, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)");
        $stmt->execute([
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
            $featured
        ]);

        set_flash('success', 'Thêm mới sản phẩm thời trang thành công!');
        redirect('products.php');
    } else {
        set_flash('danger', implode('<br>', $errors));
    }
}

$adminTitle = "Thêm Mới Sản Phẩm";
require_once __DIR__ . '/includes/header.php';
?>

<div style="margin-bottom: 24px;">
    <a href="products.php" class="btn btn-outline btn-sm"><i class="fa-solid fa-arrow-left"></i> Quay lại danh sách</a>
</div>

<div class="admin-card">
    <div class="admin-card-header">
        <h3 class="admin-card-title"><i class="fa-solid fa-plus-circle text-accent"></i> Nhập Thông Tin Sản Phẩm Mới</h3>
    </div>
    <div class="admin-card-body">
        <form action="product_add.php" method="POST" enctype="multipart/form-data">
            <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 30px;">
                <!-- Cột trái: Thông tin chính -->
                <div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Tên sản phẩm thời trang <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Áo Thun Streetwear Limited 2026" required>
                    </div>

                    <div class="form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Mã SKU sản phẩm <span class="text-danger">*</span></label>
                            <input type="text" name="sku" class="form-control" placeholder="VD: HM-TS99" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Danh mục thời trang <span class="text-danger">*</span></label>
                            <select name="category_id" class="form-control" required>
                                <?php foreach ($categories as $cat): ?>
                                    <option value="<?= $cat['id'] ?>"><?= htmlspecialchars($cat['name']) ?></option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Giá gốc niêm yết (VNĐ) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" placeholder="350000" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Giá khuyến mãi (Nếu có)</label>
                            <input type="number" name="discount_price" class="form-control" placeholder="289000">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="admin-form-group">
                            <label class="admin-form-label">Số lượng tồn kho</label>
                            <input type="number" name="stock" class="form-control" value="50" required>
                        </div>
                        <div class="admin-form-group">
                            <label class="admin-form-label">Các kích cỡ (Cách nhau bởi dấu phẩy)</label>
                            <input type="text" name="sizes" class="form-control" value="S,M,L,XL,XXL">
                        </div>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Các màu sắc (Cách nhau bởi dấu phẩy)</label>
                        <input type="text" name="colors" class="form-control" value="Đen,Trắng,Xám,Xanh Rêu">
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Mô tả ngắn gọn</label>
                        <textarea name="description" rows="3" class="form-control" placeholder="Mô tả tóm tắt chất liệu, form dáng..."></textarea>
                    </div>

                    <div class="admin-form-group">
                        <label class="admin-form-label">Nội dung chi tiết sản phẩm (HTML)</label>
                        <textarea name="content" rows="6" class="form-control" placeholder="<p>Chi tiết sản phẩm, xuất xứ, tiêu chuẩn may mặc...</p>"></textarea>
                    </div>
                </div>

                <!-- Cột phải: Upload ảnh & Cài đặt bổ sung -->
                <div>
                    <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 20px;">
                        <h4 style="font-size: 1rem; margin-bottom: 12px;"><i class="fa-solid fa-image text-accent"></i> Hình Ảnh Sản Phẩm</h4>
                        
                        <div class="admin-form-group">
                            <label class="admin-form-label">Tải lên ảnh mới:</label>
                            <input type="file" name="image" class="form-control image-upload-input" accept="image/*">
                        </div>

                        <div class="admin-form-group">
                            <label class="admin-form-label">Hoặc chọn ảnh mẫu có sẵn:</label>
                            <select name="default_image_select" class="form-control">
                                <option value="ao_thun_streetwear.jpg">Áo Thun Streetwear</option>
                                <option value="ao_polo_dệt_bo.jpg">Áo Polo Nam Bo Cổ</option>
                                <option value="ao_so_mi_oxford.jpg">Áo Sơ Mi Oxford</option>
                                <option value="ao_khoac_bomber.jpg">Áo Khoác Bomber</option>
                                <option value="ao_hoodie_ni_bong.jpg">Áo Hoodie Nỉ Bông</option>
                                <option value="quan_jean_slimfit.jpg">Quần Jean Slimfit</option>
                                <option value="quan_kaki_chino.jpg">Quần Kaki Chino</option>
                                <option value="dam_hoa_nhi.jpg">Đầm Hoa Nhí Vintage</option>
                            </select>
                        </div>

                        <div style="text-align: center; margin-top: 14px;">
                            <img src="../assets/images/products/ao_thun_streetwear.jpg" alt="Preview" class="image-preview-target" style="width: 140px; height: 140px; object-fit: cover; border-radius: 8px; border: 1px solid var(--border); margin: 0 auto;">
                        </div>
                    </div>

                    <div style="background: #f8fafc; border: 1px solid var(--border); border-radius: var(--radius-md); padding: 20px; margin-bottom: 24px;">
                        <h4 style="font-size: 1rem; margin-bottom: 12px;"><i class="fa-solid fa-sliders text-accent"></i> Tùy Chọn Nổi Bật</h4>
                        <label style="display: flex; align-items: center; gap: 8px; cursor: pointer; font-size: 0.9rem;">
                            <input type="checkbox" name="featured" value="1" style="width: 18px; height: 18px; accent-color: var(--accent);">
                            <span>Đánh dấu là <strong>Sản phẩm Nổi Bật (Hot)</strong></span>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-accent btn-lg btn-block">
                        <i class="fa-solid fa-floppy-disk"></i> Lưu Sản Phẩm
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
