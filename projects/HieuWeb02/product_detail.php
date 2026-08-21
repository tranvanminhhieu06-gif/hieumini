<?php
$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;
require_once __DIR__ . '/includes/header.php';

$product = null;
$reviews = [];
$related_products = [];

// Xử lý gửi đánh giá mới
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewer_name = sanitize($_POST['reviewer_name'] ?? 'Khách hàng');
    $rating_val = (int)($_POST['rating'] ?? 5);
    $comment_val = sanitize($_POST['comment'] ?? '');

    if (!empty($comment_val) && $pdo) {
        try {
            $user_id = is_logged_in() ? current_user()['id'] : null;
            $stmt_rev = $pdo->prepare("INSERT INTO reviews (product_id, user_id, user_name, rating, comment) VALUES (?, ?, ?, ?, ?)");
            $stmt_rev->execute([$product_id, $user_id, $reviewer_name, $rating_val, $comment_val]);
            set_flash('success', 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm!');
            header("Location: product_detail.php?id=" . $product_id);
            exit;
        } catch (Exception $e) {}
    }
}

if ($pdo) {
    try {
        // Tăng lượt xem
        $pdo->prepare("UPDATE products SET views = views + 1 WHERE id = ?")->execute([$product_id]);

        // Lấy chi tiết sản phẩm
        $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
        $stmt->execute([$product_id]);
        $product = $stmt->fetch();

        if ($product) {
            // Lấy đánh giá
            $stmt_r = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
            $stmt_r->execute([$product_id]);
            $reviews = $stmt_r->fetchAll();

            // Lấy sản phẩm liên quan
            $stmt_rel = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? LIMIT 4");
            $stmt_rel->execute([$product['category_id'], $product_id]);
            $related_products = $stmt_rel->fetchAll();
        }
    } catch (Exception $e) {}
}

// Fallback nếu không có CSDL
if (!$product) {
    $product = [
        'id' => $product_id,
        'category_id' => 1,
        'category_name' => 'Điện thoại Smartphone',
        'name' => 'iPhone 16 Pro Max 256GB Titan Sa Mạc',
        'slug' => 'iphone-16-pro-max-256gb',
        'brand' => 'Apple',
        'price' => 34990000,
        'sale_price' => 31990000,
        'stock_quantity' => 25,
        'rating' => 5.0,
        'short_desc' => 'Chip A18 Pro 3nm cực mạnh, Camera nút điều khiển Camera Control mới, viền titan siêu mỏng nhẹ.',
        'description' => 'iPhone 16 Pro Max mang đến bước đột phá công nghệ với chip A18 Pro tiến trình 3nm thế hệ thứ 2 cho hiệu năng vượt trội và tiết kiệm điện năng tối đa. Màn hình Super Retina XDR 6.9 inch cùng hệ thống camera Pro 48MP zoom quang học 5x sắc nét.',
        'specifications' => '{"Màn hình": "6.9 inch Super Retina XDR OLED 120Hz ProMotion", "Chip CPU": "Apple A18 Pro 6 nhân", "RAM": "8 GB", "Bộ nhớ trong": "256 GB", "Camera sau": "Chính 48MP + Góc siêu rộng 48MP + Tele 12MP 5x", "Pin & Sạc": "4.685 mAh, Sạc nhanh 30W, MagSafe 25W"}',
        'icon' => 'fa-mobile-screen'
    ];
}

$specs_array = [];
if (!empty($product['specifications'])) {
    $specs_array = json_decode($product['specifications'], true);
}
$discount = calculate_discount($product['price'], $product['sale_price']);
?>

<main class="container" style="margin: 30px auto 60px;">
    <!-- Breadcrumb -->
    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--text-muted); margin-bottom: 24px;">
        <a href="index.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <span>/</span>
        <a href="products.php?cat=<?php echo $product['category_id']; ?>"><?php echo htmlspecialchars($product['category_name'] ?? 'Danh mục'); ?></a>
        <span>/</span>
        <span style="color: #fff;"><?php echo htmlspecialchars($product['name']); ?></span>
    </div>

    <!-- Chi tiết sản phẩm Grid -->
    <div style="display: grid; grid-template-columns: 1fr 1.2fr; gap: 40px; margin-bottom: 50px;">
        
        <!-- Cột trái: Hình ảnh -->
        <div class="glass-panel" style="padding: 30px; display: flex; flex-direction: column; align-items: center; justify-content: center; min-height: 420px; position: relative;">
            <?php if ($discount > 0): ?>
                <span class="card-badge-discount" style="font-size: 0.9rem; padding: 6px 12px;">GIẢM <?php echo $discount; ?>%</span>
            <?php endif; ?>

            <?php
            $main_thumb = !empty($product['thumbnail']) && file_exists(__DIR__ . '/assets/images/' . $product['thumbnail']) ? 'assets/images/' . $product['thumbnail'] : 'assets/images/default_prod.png';
            ?>
            <div style="width: 100%; max-width: 380px; height: 320px; border-radius: var(--radius-lg); overflow: hidden; display: flex; align-items: center; justify-content: center; margin-bottom: 20px; background: rgba(255,255,255,0.02); border: var(--border-glass);">
                <img src="<?php echo $main_thumb; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 90%; max-height: 90%; object-fit: contain; filter: drop-shadow(0 10px 20px rgba(0,0,0,0.5)); border-radius: var(--radius-md);">
            </div>

            <div style="display: flex; gap: 12px; flex-wrap: wrap; justify-content: center;">
                <span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-check text-success"></i> Hàng mới 100% nguyên seal</span>
                <span style="font-size: 0.8rem; color: var(--text-muted);"><i class="fa-solid fa-check text-success"></i> Đầy đủ phụ kiện chính hãng</span>
            </div>
        </div>

        <!-- Cột phải: Thông tin & Đặt hàng -->
        <div>
            <span class="badge badge-primary" style="margin-bottom: 8px;"><?php echo htmlspecialchars($product['brand']); ?></span>
            <h1 style="font-size: 1.8rem; font-weight: 800; line-height: 1.3; margin-bottom: 12px;"><?php echo htmlspecialchars($product['name']); ?></h1>

            <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                <?php echo render_rating_stars($product['rating']); ?>
                <span style="color: var(--text-muted); font-size: 0.85rem;">| Tình trạng: <strong style="color: var(--success);"><?php echo ($product['stock_quantity'] ?? 10) > 0 ? 'Còn hàng (' . ($product['stock_quantity'] ?? 10) . ')' : 'Hết hàng'; ?></strong></span>
            </div>

            <!-- Giá bán -->
            <div class="glass-panel" style="padding: 16px 20px; margin-bottom: 24px; background: rgba(255, 255, 255, 0.03);">
                <div style="display: flex; align-items: baseline; gap: 14px;">
                    <span style="font-size: 2rem; font-weight: 800; color: #f43f5e;"><?php echo format_currency(!empty($product['sale_price']) ? $product['sale_price'] : $product['price']); ?></span>
                    <?php if (!empty($product['sale_price'])): ?>
                        <span style="font-size: 1.1rem; color: var(--text-muted); text-decoration: line-through;"><?php echo format_currency($product['price']); ?></span>
                    <?php endif; ?>
                </div>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 6px;"><i class="fa-solid fa-gift text-warning"></i> Tặng gói bảo hành VIP HieuMini Care 24 Tháng trị giá 2.000.000₫</p>
            </div>

            <!-- Mô tả ngắn -->
            <p style="color: #cbd5e1; font-size: 0.95rem; margin-bottom: 24px; line-height: 1.6;">
                <?php echo htmlspecialchars($product['short_desc'] ?? ''); ?>
            </p>

            <!-- Form Chọn số lượng & Đặt mua -->
            <form action="cart.php?action=add" method="POST" style="margin-bottom: 30px;">
                <input type="hidden" name="product_id" value="<?php echo $product['id']; ?>">
                
                <div style="display: flex; align-items: center; gap: 16px; margin-bottom: 20px;">
                    <label style="font-size: 0.9rem; font-weight: 600; color: var(--text-muted);">Số lượng:</label>
                    <div class="quantity-box">
                        <button type="button" class="qty-btn qty-adjust-btn minus">-</button>
                        <input type="number" name="quantity" id="product-qty" class="qty-input" value="1" min="1" max="<?php echo $product['stock_quantity'] ?? 50; ?>">
                        <button type="button" class="qty-btn qty-adjust-btn plus">+</button>
                    </div>
                </div>

                <div style="display: flex; gap: 14px;">
                    <button type="submit" name="buy_now" value="1" class="btn btn-primary" style="flex: 1; padding: 14px;">
                        <i class="fa-solid fa-bolt"></i> MUA NGAY (GIAO TẬN NƠI)
                    </button>
                    <button type="button" class="btn btn-outline ajax-add-cart" data-id="<?php echo $product['id']; ?>" style="padding: 14px 20px;">
                        <i class="fa-solid fa-cart-plus"></i> Thêm vào giỏ
                    </button>
                </div>
            </form>

            <!-- Cam kết mua hàng -->
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 0.85rem; color: var(--text-muted); border-top: var(--border-glass); padding-top: 16px;">
                <div><i class="fa-solid fa-truck-fast text-primary"></i> Giao hàng miễn phí toàn quốc</div>
                <div><i class="fa-solid fa-shield-halved text-success"></i> Bảo hành chính hãng 2 năm</div>
                <div><i class="fa-solid fa-credit-card text-warning"></i> Trả góp 0% qua thẻ tín dụng</div>
                <div><i class="fa-solid fa-arrows-rotate text-accent"></i> 1 đổi 1 trong 30 ngày đầu</div>
            </div>
        </div>
    </div>

    <!-- Thông số kỹ thuật & Mô tả chi tiết -->
    <div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 30px; margin-bottom: 50px;">
        
        <!-- Mô tả chi tiết -->
        <div class="glass-panel" style="padding: 30px;">
            <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 16px; color: #fff; border-bottom: var(--border-glass); padding-bottom: 10px;">
                <i class="fa-solid fa-file-lines" style="color: var(--primary);"></i> Đặc Điểm Nổi Bật
            </h3>
            <div style="color: #cbd5e1; line-height: 1.8; font-size: 0.95rem;">
                <p style="margin-bottom: 16px;"><?php echo nl2br(htmlspecialchars($product['description'] ?? '')); ?></p>
                
                <div style="background: rgba(255,255,255,0.03); padding: 18px; border-radius: var(--radius-md); border-left: 4px solid var(--primary); margin: 20px 0;">
                    <h4 style="font-size: 1rem; color: #fff; margin-bottom: 6px;">Ưu đãi độc quyền tại HieuMini:</h4>
                    <ul style="list-style: disc; padding-left: 20px; font-size: 0.9rem;">
                        <li>Tặng phiếu mua hàng phụ kiện trị giá 500.000₫.</li>
                        <li>Thu cũ đổi mới trợ giá đến 3.000.000₫.</li>
                        <li>Tặng dán màn hình cường lực cao cấp & ốp lưng chống sốc.</li>
                    </ul>
                </div>
            </div>
        </div>

        <!-- Thông số cấu hình -->
        <div class="glass-panel" style="padding: 30px;">
            <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 16px; color: #fff; border-bottom: var(--border-glass); padding-bottom: 10px;">
                <i class="fa-solid fa-sliders" style="color: var(--accent);"></i> Thông Số Kỹ Thuật
            </h3>
            
            <?php if (!empty($specs_array)): ?>
                <table style="width: 100%; border-collapse: collapse; font-size: 0.9rem;">
                    <?php foreach ($specs_array as $key => $val): ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: 10px 0; color: var(--text-muted); width: 35%;"><?php echo htmlspecialchars($key); ?></td>
                            <td style="padding: 10px 0; color: #fff; font-weight: 600;"><?php echo htmlspecialchars($val); ?></td>
                        </tr>
                    <?php endforeach; ?>
                </table>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 0.9rem;">Thông số đang được cập nhật từ nhà sản xuất.</p>
            <?php endif; ?>
        </div>
    </div>

    <!-- Đánh giá khách hàng -->
    <div class="glass-panel" style="padding: 30px; margin-bottom: 50px;">
        <h3 style="font-size: 1.3rem; font-weight: 700; margin-bottom: 24px; color: #fff;">
            <i class="fa-solid fa-comments" style="color: #fbbf24;"></i> Đánh Giá & Nhận Xét Của Khách Hàng
        </h3>

        <!-- Form gửi đánh giá -->
        <form action="product_detail.php?id=<?php echo $product['id']; ?>" method="POST" style="background: rgba(255,255,255,0.03); padding: 20px; border-radius: var(--radius-md); margin-bottom: 30px;">
            <h4 style="font-size: 1rem; margin-bottom: 14px; color: #cbd5e1;">Để lại đánh giá của bạn:</h4>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 14px;">
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Họ và tên của bạn:</label>
                    <input type="text" name="reviewer_name" class="form-control" placeholder="Nhập tên..." required value="<?php echo is_logged_in() ? htmlspecialchars(current_user()['full_name']) : ''; ?>">
                </div>
                <div class="form-group" style="margin-bottom: 0;">
                    <label>Điểm đánh giá:</label>
                    <select name="rating" class="form-control">
                        <option value="5">⭐⭐⭐⭐⭐ (5 sao - Xuất sắc)</option>
                        <option value="4">⭐⭐⭐⭐ (4 sao - Rất tốt)</option>
                        <option value="3">⭐⭐⭐ (3 sao - Tạm được)</option>
                        <option value="2">⭐⭐ (2 sao - Kém)</option>
                        <option value="1">⭐ (1 sao - Rất tệ)</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <label>Nội dung nhận xét:</label>
                <textarea name="comment" class="form-control" rows="3" placeholder="Chia sẻ cảm nhận về hiệu năng, thiết kế, thời lượng pin..." required></textarea>
            </div>
            <button type="submit" name="submit_review" class="btn btn-primary btn-sm">
                <i class="fa-solid fa-paper-plane"></i> Gửi nhận xét
            </button>
        </form>

        <!-- Danh sách nhận xét -->
        <div>
            <?php if (!empty($reviews)): ?>
                <?php foreach ($reviews as $rev): ?>
                    <div style="border-bottom: 1px solid rgba(255,255,255,0.06); padding: 16px 0;">
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                            <strong style="color: #fff;"><?php echo htmlspecialchars($rev['user_name']); ?></strong>
                            <span style="font-size: 0.8rem; color: var(--text-muted);"><?php echo date('d/m/Y H:i', strtotime($rev['created_at'])); ?></span>
                        </div>
                        <div style="margin-bottom: 6px;">
                            <?php echo render_rating_stars($rev['rating']); ?>
                        </div>
                        <p style="color: #cbd5e1; font-size: 0.9rem;"><?php echo htmlspecialchars($rev['comment']); ?></p>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <p style="color: var(--text-muted); font-size: 0.9rem; font-style: italic;">Chưa có nhận xét nào. Hãy là người đầu tiên trải nghiệm và đánh giá sản phẩm này!</p>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
