<?php
/**
 * Trang Chi Tiết Sản Phẩm Thời Trang HieuMini
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if (!$id) {
    redirect('products.php');
}

// Cập nhật lượt xem
$pdo->prepare("UPDATE products SET view_count = view_count + 1 WHERE id = ?")->execute([$id]);

// Lấy thông tin sản phẩm
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ? AND p.status = 1");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    redirect('products.php');
}

$pageTitle = $product['name'];

// Xử lý gửi đánh giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'submit_review') {
    if (!is_logged_in()) {
        set_flash('danger', 'Bạn cần đăng nhập để gửi đánh giá sản phẩm!');
        redirect("login.php?redirect=" . urlencode("product_detail.php?id=$id"));
    }
    
    $rating = (int)($_POST['rating'] ?? 5);
    $comment = trim($_POST['comment'] ?? '');
    $user = current_user($pdo);
    
    if (!empty($comment)) {
        $insRev = $pdo->prepare("INSERT INTO reviews (product_id, user_id, user_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
        $insRev->execute([$id, $user['id'], $user['full_name'], $rating, $comment]);
        set_flash('success', 'Cảm ơn bạn đã gửi đánh giá sản phẩm!');
        redirect("product_detail.php?id=$id");
    } else {
        set_flash('danger', 'Vui lòng nhập nội dung nhận xét.');
    }
}

// Lấy danh sách đánh giá
$revStmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
$revStmt->execute([$id]);
$reviews = $revStmt->fetchAll();

// Lấy sản phẩm liên quan cùng danh mục
$relStmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? AND p.status = 1 LIMIT 4");
$relStmt->execute([$product['category_id'], $id]);
$relatedProds = $relStmt->fetchAll();

$sizes = explode(',', $product['sizes']);
$colors = explode(',', $product['colors']);
$discountPct = $product['discount_price'] ? round((($product['price'] - $product['discount_price']) / $product['price']) * 100) : 0;

require_once __DIR__ . '/includes/header.php';
?>

<!-- Breadcrumbs -->
<div class="container" style="padding-top: 20px;">
    <div class="breadcrumbs" style="margin-bottom: 20px;">
        <a href="index.php">Trang Chủ</a> / 
        <a href="products.php?cat=<?= htmlspecialchars($product['category_slug']) ?>"><?= htmlspecialchars($product['category_name']) ?></a> / 
        <span><?= htmlspecialchars($product['name']) ?></span>
    </div>
</div>

<div class="container">
    <!-- Product Detail Layout -->
    <div class="product-detail-layout">
        <!-- Gallery Image -->
        <div class="detail-gallery">
            <div class="main-img-box">
                <img src="assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" id="main-product-img">
            </div>
            <div style="display: flex; gap: 10px; justify-content: center;">
                <div style="width: 70px; height: 70px; border: 2px solid var(--accent); border-radius: var(--radius-md); overflow: hidden; cursor: pointer;">
                    <img src="assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="Thumb" style="width: 100%; height: 100%; object-fit: cover;">
                </div>
            </div>
        </div>

        <!-- Product Info & Purchase Form -->
        <div class="detail-info">
            <div style="font-size: 0.85rem; text-transform: uppercase; color: var(--accent); font-weight: 700; margin-bottom: 6px;">
                <?= htmlspecialchars($product['category_name']) ?>
            </div>
            <h1><?= htmlspecialchars($product['name']) ?></h1>
            
            <div class="detail-meta-row">
                <div>Mã SP: <strong><?= htmlspecialchars($product['sku']) ?></strong></div>
                <div>•</div>
                <div class="prod-rating">
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <i class="fa-solid fa-star"></i>
                    <span>(<?= count($reviews) ?> đánh giá)</span>
                </div>
                <div>•</div>
                <div style="color: var(--success); font-weight: 600;"><i class="fa-solid fa-circle-check"></i> Còn hàng (<?= $product['stock'] ?>)</div>
            </div>

            <!-- Price Box -->
            <div class="detail-price-box">
                <span class="current-price"><?= format_price($product['discount_price'] ?? $product['price']) ?></span>
                <?php if ($product['discount_price']): ?>
                    <span class="old-price"><?= format_price($product['price']) ?></span>
                    <span class="badge-discount" style="position: static;">-<?= $discountPct ?>%</span>
                <?php endif; ?>
            </div>

            <!-- Description summary -->
            <p style="color: var(--secondary); font-size: 0.95rem; line-height: 1.7; margin-bottom: 24px;">
                <?= htmlspecialchars($product['description']) ?>
            </p>

            <!-- Add to cart form -->
            <form action="cart.php" method="POST">
                <input type="hidden" name="action" value="add">
                <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

                <!-- Select Size -->
                <div class="form-group">
                    <div class="selector-label">
                        <span>Chọn Kích Cỡ:</span>
                        <span class="size-guide-btn" data-open-modal="size-modal"><i class="fa-solid fa-ruler"></i> Hướng dẫn chọn size</span>
                    </div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <?php foreach ($sizes as $idx => $s): ?>
                            <?php $s = trim($s); ?>
                            <label style="cursor: pointer;">
                                <input type="radio" name="size" value="<?= $s ?>" <?= $idx === 0 ? 'checked' : '' ?> style="display: none;" onchange="this.parentElement.parentElement.querySelectorAll('span').forEach(el => el.classList.remove('active')); this.nextElementSibling.classList.add('active');">
                                <span class="size-pill <?= $idx === 0 ? 'active' : '' ?>" style="min-width: 44px; text-align: center; display: inline-block;"><?= $s ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Select Color -->
                <div class="form-group" style="margin-top: 18px;">
                    <div class="selector-label"><span>Chọn Màu Sắc:</span></div>
                    <div style="display: flex; gap: 10px; flex-wrap: wrap;">
                        <?php foreach ($colors as $idx => $c): ?>
                            <?php $c = trim($c); ?>
                            <label style="cursor: pointer;">
                                <input type="radio" name="color" value="<?= $c ?>" <?= $idx === 0 ? 'checked' : '' ?> style="display: none;" onchange="this.parentElement.parentElement.querySelectorAll('span').forEach(el => el.classList.remove('active')); this.nextElementSibling.classList.add('active');">
                                <span class="size-pill <?= $idx === 0 ? 'active' : '' ?>"><?= $c ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Quantity -->
                <div class="form-group" style="margin-top: 20px;">
                    <div class="selector-label"><span>Số Lượng:</span></div>
                    <div class="quantity-control">
                        <button type="button" class="qty-btn minus"><i class="fa-solid fa-minus"></i></button>
                        <input type="number" name="quantity" class="qty-input" value="1" min="1" max="<?= $product['stock'] ?>">
                        <button type="button" class="qty-btn plus"><i class="fa-solid fa-plus"></i></button>
                    </div>
                </div>

                <!-- Buttons -->
                <div class="detail-actions">
                    <button type="submit" name="submit_action" value="add_to_cart" class="btn btn-outline btn-lg" style="flex: 1;">
                        <i class="fa-solid fa-cart-plus"></i> Thêm Vào Giỏ
                    </button>
                    <button type="submit" name="submit_action" value="buy_now" class="btn btn-accent btn-lg" style="flex: 1;">
                        <i class="fa-solid fa-bolt"></i> Mua Ngay
                    </button>
                </div>
            </form>

            <!-- Store Perks -->
            <div class="perks-list">
                <div class="perk-item"><i class="fa-solid fa-truck text-accent"></i> Giao nhanh toàn quốc 2-4 ngày</div>
                <div class="perk-item"><i class="fa-solid fa-box-open text-accent"></i> Được kiểm tra hàng trước khi nhận</div>
                <div class="perk-item"><i class="fa-solid fa-rotate-left text-accent"></i> Đổi trả miễn phí trong 30 ngày</div>
                <div class="perk-item"><i class="fa-solid fa-certificate text-accent"></i> Cam kết 100% đúng mô tả</div>
            </div>
        </div>
    </div>

    <!-- Product Tabs (Mô tả, Hướng dẫn bảo quản, Đánh giá) -->
    <div style="margin-bottom: 60px;">
        <div class="tabs-header">
            <button class="tab-btn active" data-tab="tab-desc">Chi Tiết Sản Phẩm</button>
            <button class="tab-btn" data-tab="tab-care">Hướng Dẫn Bảo Quản</button>
            <button class="tab-btn" data-tab="tab-reviews">Đánh Giá & Nhận Xét (<?= count($reviews) ?>)</button>
        </div>

        <div id="tab-desc" class="tab-pane active">
            <div style="font-size: 1rem; line-height: 1.8; color: var(--secondary);">
                <?= $product['content'] ?? '<p>Chất liệu cao cấp, đường may tỉ mỉ, form dáng chuẩn thời trang hiện đại.</p>' ?>
            </div>
        </div>

        <div id="tab-care" class="tab-pane">
            <ul style="line-height: 2; color: var(--secondary); font-size: 0.95rem;">
                <li><i class="fa-solid fa-check text-success"></i> Nên giặt bằng nước lạnh hoặc nước ấm dưới 30°C để giữ màu vải tốt nhất.</li>
                <li><i class="fa-solid fa-check text-success"></i> Giặt mặt trái sản phẩm và không sử dụng thuốc tẩy có nồng độ Clo cao.</li>
                <li><i class="fa-solid fa-check text-success"></i> Phơi ở nơi thoáng mát, tránh ánh nắng gắt trực tiếp làm khô cứng sợi vải.</li>
                <li><i class="fa-solid fa-check text-success"></i> Ủi (là) ở nhiệt độ trung bình (khoảng 110°C - 150°C), tốt nhất nên ủi mặt trong.</li>
            </ul>
        </div>

        <div id="tab-reviews" class="tab-pane">
            <!-- Review Form -->
            <div style="background: #f8fafc; padding: 24px; border-radius: var(--radius-md); margin-bottom: 30px; border: 1px solid var(--border);">
                <h4 style="margin-bottom: 12px;"><i class="fa-solid fa-pen-to-square text-accent"></i> Viết Đánh Giá Của Bạn</h4>
                <?php if (is_logged_in()): ?>
                    <form action="product_detail.php?id=<?= $product['id'] ?>" method="POST">
                        <input type="hidden" name="action" value="submit_review">
                        <div class="form-group">
                            <label class="form-label">Chọn số sao đánh giá:</label>
                            <select name="rating" class="form-control" style="max-width: 200px;">
                                <option value="5">⭐⭐⭐⭐⭐ 5 Sao (Tuyệt vời)</option>
                                <option value="4">⭐⭐⭐⭐ 4 Sao (Hài lòng)</option>
                                <option value="3">⭐⭐⭐ 3 Sao (Bình thường)</option>
                                <option value="2">⭐⭐ 2 Sao (Tạm được)</option>
                                <option value="1">⭐ 1 Sao (Chưa hài lòng)</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Cảm nhận và nhận xét của bạn:</label>
                            <textarea name="comment" rows="3" class="form-control" placeholder="Chia sẻ cảm nhận về chất vải, form áo, độ hoàn thiện..." required></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-sm">Gửi Nhận Xét</button>
                    </form>
                <?php else: ?>
                    <p style="color: var(--text-muted);">
                        Vui lòng <a href="login.php?redirect=<?= urlencode("product_detail.php?id=$id") ?>" style="color: var(--accent); font-weight: 700; text-decoration: underline;">Đăng Nhập</a> để viết nhận xét cho sản phẩm này.
                    </p>
                <?php endif; ?>
            </div>

            <!-- Review list -->
            <div class="review-list">
                <?php if (empty($reviews)): ?>
                    <p style="color: var(--text-muted); text-align: center; padding: 20px;">Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên đánh giá!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                        <div style="border-bottom: 1px solid var(--border); padding-bottom: 16px; margin-bottom: 16px;">
                            <div style="display: flex; justify-content: space-between; margin-bottom: 4px;">
                                <strong style="color: var(--primary);"><?= htmlspecialchars($rev['user_name']) ?></strong>
                                <span style="font-size: 0.8rem; color: var(--text-light);"><?= format_datetime($rev['created_at']) ?></span>
                            </div>
                            <div style="color: #f59e0b; font-size: 0.85rem; margin-bottom: 8px;">
                                <?php for ($i = 0; $i < $rev['rating']; $i++): ?><i class="fa-solid fa-star"></i><?php endfor; ?>
                            </div>
                            <p style="color: var(--secondary); font-size: 0.9rem;"><?= htmlspecialchars($rev['comment']) ?></p>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($relatedProds)): ?>
        <section style="margin-bottom: 60px;">
            <div class="section-header">
                <div class="section-subtitle">GỢI Ý DÀNH CHO BẠN</div>
                <h2 class="section-title">Sản Phẩm Tương Tự</h2>
            </div>
            <div class="products-grid">
                <?php foreach ($relatedProds as $p): ?>
                    <div class="product-card">
                        <a href="product_detail.php?id=<?= $p['id'] ?>" class="prod-thumb">
                            <img src="assets/images/products/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                        </a>
                        <div class="prod-body">
                            <span class="prod-category"><?= htmlspecialchars($p['category_name']) ?></span>
                            <a href="product_detail.php?id=<?= $p['id'] ?>" class="prod-name"><?= htmlspecialchars($p['name']) ?></a>
                            <div class="prod-price-row">
                                <span class="current-price"><?= format_price($p['discount_price'] ?? $p['price']) ?></span>
                            </div>
                            <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm btn-block">Xem Chi Tiết</a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </section>
    <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
