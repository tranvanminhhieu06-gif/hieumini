<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - PRODUCT DETAIL PAGE
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/includes/config.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 1;

// Lấy thông tin sản phẩm và danh mục
$stmt = $pdo->prepare("
    SELECT p.*, c.name AS category_name, c.slug AS category_slug 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE p.id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    header("Location: " . BASE_URL . "/products.php");
    exit;
}

// Xử lý gửi đánh giá khách hàng (Review Submission)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewer_name = sanitize($_POST['reviewer_name'] ?? '');
    $reviewer_role = sanitize($_POST['reviewer_role'] ?? 'Hội Viên VIP');
    $rating = (int)($_POST['rating'] ?? 5);
    $comment = sanitize($_POST['comment'] ?? '');

    if ($reviewer_name && $comment) {
        $insert_rev = $pdo->prepare("
            INSERT INTO reviews (product_id, user_name, user_role, rating, comment) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $insert_rev->execute([$product_id, $reviewer_name, $reviewer_role, $rating, $comment]);

        // Cập nhật số lượng review của sản phẩm
        $pdo->prepare("UPDATE products SET review_count = review_count + 1 WHERE id = ?")->execute([$product_id]);

        set_flash('success', 'Cảm ơn quý khách đã gửi đánh giá! Ý kiến của quý khách là động lực phát triển của HieuMini.');
        header("Location: " . BASE_URL . "/product-detail.php?id=" . $product_id);
        exit;
    }
}

// Lấy danh sách đánh giá của sản phẩm này
$rev_stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
$rev_stmt->execute([$product_id]);
$reviews = $rev_stmt->fetchAll();

// Lấy 4 sản phẩm liên quan cùng danh mục
$rel_stmt = $pdo->prepare("
    SELECT * FROM products 
    WHERE category_id = ? AND id != ? 
    ORDER BY id ASC LIMIT 4
");
$rel_stmt->execute([$product['category_id'], $product_id]);
$related_products = $rel_stmt->fetchAll();

// Giải mã JSON thông số kỹ thuật
$specs = json_decode($product['specs_json'] ?? '{}', true);

$page_title = htmlspecialchars($product['name']) . " | " . SITE_NAME;
$page_desc = htmlspecialchars($product['short_description'] ?? $product['name']);

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <!-- Breadcrumb -->
    <div style="font-size: 0.85rem; color: var(--gold-primary); margin-bottom: 2rem;" class="reveal">
        <a href="<?= BASE_URL ?>/index.php">Trang Chủ</a> / 
        <a href="<?= BASE_URL ?>/products.php">Cửa Hàng</a> / 
        <a href="<?= BASE_URL ?>/products.php?category=<?= $product['category_slug'] ?>"><?= htmlspecialchars($product['category_name']) ?></a> / 
        <strong style="color: #fff;"><?= htmlspecialchars($product['name']) ?></strong>
    </div>

    <!-- Product Detail Main Layout -->
    <div class="product-detail-layout">
        <!-- Gallery Left -->
        <div class="detail-gallery-main reveal">
            <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>">
            <?php if ($product['badge']): ?>
            <span class="badge badge-gold" style="position: absolute; top: 20px; left: 20px; font-size: 0.85rem; padding: 0.4rem 0.9rem;">
                <?= htmlspecialchars($product['badge']) ?>
            </span>
            <?php endif; ?>
        </div>

        <!-- Info Right -->
        <div class="detail-info-panel reveal delay-1">
            <div class="detail-sku">MÃ SẢN PHẨM: <?= htmlspecialchars($product['sku']) ?></div>
            <h1 class="detail-title"><?= htmlspecialchars($product['name']) ?></h1>

            <!-- Ratings -->
            <div class="product-rating" style="margin-bottom: 1.25rem;">
                <?php 
                $r = floor($product['rating']);
                for ($i = 0; $i < 5; $i++): 
                    if ($i < $r) echo '<i class="fas fa-star"></i>';
                    else echo '<i class="far fa-star"></i>';
                endfor; ?>
                <span class="review-count">(<?= $product['review_count'] ?> Đánh giá từ Hội Viên CEO)</span>
                <span style="color: var(--emerald-accent); margin-left: 1rem; font-size: 0.85rem; font-weight: 700;">
                    <i class="fas fa-check-circle"></i> Còn Hàng (<?= $product['stock'] ?>)
                </span>
            </div>

            <!-- Price -->
            <div class="detail-price-box">
                <span class="detail-price-curr"><?= format_currency($product['price']) ?></span>
                <?php if ($product['original_price'] && $product['original_price'] > $product['price']): ?>
                <span class="detail-price-orig"><?= format_currency($product['original_price']) ?></span>
                <span class="badge badge-ruby">-<?= get_discount_percent($product['price'], $product['original_price']) ?>% TIẾT KIỆM</span>
                <?php endif; ?>
            </div>

            <!-- Short Description -->
            <p style="color: #cbd5e1; font-size: 1.05rem; line-height: 1.7; margin-bottom: 2rem;">
                <?= htmlspecialchars($product['short_description']) ?>
            </p>

            <!-- Quantity & Actions -->
            <div class="detail-quantity-row">
                <span style="font-weight: 700; color: var(--text-secondary);">Số Lượng:</span>
                <div class="qty-stepper">
                    <button type="button" class="qty-btn qty-minus">-</button>
                    <input type="text" class="qty-input" value="1" readonly>
                    <button type="button" class="qty-btn qty-plus">+</button>
                </div>
            </div>

            <div class="detail-actions-row">
                <button type="button" class="btn btn-primary btn-lg btn-shimmer btn-add-cart-detail" data-id="<?= $product['id'] ?>" style="flex-grow: 1;">
                    <i class="fas fa-shopping-bag"></i> THÊM VÀO GIỎ HÀNG
                </button>
                <button type="button" class="btn btn-gold-outline btn-lg" data-open-modal="booking-modal" title="Tư vấn trực tiếp">
                    <i class="fas fa-headset"></i> TƯ VẤN VIP
                </button>
            </div>

            <!-- CEO Highlights Guarantee -->
            <div style="background: rgba(255,255,255,0.02); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 1.25rem; display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; font-size: 0.85rem;">
                <div style="display: flex; align-items: center; gap: 0.6rem; color: #e2e8f0;">
                    <i class="fas fa-shield-alt" style="color: var(--gold-primary); font-size: 1.2rem;"></i>
                    <span>Cam kết 100% chính hãng</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.6rem; color: #e2e8f0;">
                    <i class="fas fa-truck" style="color: var(--cyan-accent); font-size: 1.2rem;"></i>
                    <span>Giao hàng VIP hỏa tốc 2H</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.6rem; color: #e2e8f0;">
                    <i class="fas fa-sync-alt" style="color: var(--emerald-accent); font-size: 1.2rem;"></i>
                    <span>Đổi trả 30 ngày linh hoạt</span>
                </div>
                <div style="display: flex; align-items: center; gap: 0.6rem; color: #e2e8f0;">
                    <i class="fas fa-medal" style="color: var(--gold-primary); font-size: 1.2rem;"></i>
                    <span>Bảo hành 5 sao chuẩn CEO</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Tabs: Description / Specs / Reviews -->
    <div style="margin-bottom: 5rem;" class="reveal">
        <div style="display: flex; gap: 1rem; border-bottom: 1px solid var(--border-subtle); margin-bottom: 2.5rem; flex-wrap: wrap;">
            <button type="button" class="tab-btn active" onclick="switchDetailTab('desc-tab', this)">Chi Tiết Sản Phẩm</button>
            <button type="button" class="tab-btn" onclick="switchDetailTab('specs-tab', this)">Thông Số Kỹ Thuật</button>
            <button type="button" class="tab-btn" onclick="switchDetailTab('reviews-tab', this)">Đánh Giá Hội Viên (<?= count($reviews) ?>)</button>
        </div>

        <!-- Tab 1: Description -->
        <div id="desc-tab" class="detail-tab-content" style="background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 2.5rem;">
            <h3 style="color: var(--gold-light); font-size: 1.4rem; margin-bottom: 1.25rem;">Mô Tả Chuyên Sâu</h3>
            <p style="color: #e2e8f0; font-size: 1.05rem; line-height: 1.8; margin-bottom: 1.5rem;">
                <?= nl2br(htmlspecialchars($product['description'])) ?>
            </p>
        </div>

        <!-- Tab 2: Specs -->
        <div id="specs-tab" class="detail-tab-content" style="display: none; background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 2.5rem;">
            <h3 style="color: var(--gold-light); font-size: 1.4rem; margin-bottom: 1.25rem;">Thông Số & Tiêu Chuẩn Quốc Tế</h3>
            <?php if (!empty($specs) && is_array($specs)): ?>
                <table class="specs-table">
                    <tbody>
                        <?php foreach ($specs as $key => $val): ?>
                        <tr>
                            <th><?= htmlspecialchars($key) ?></th>
                            <td><?= htmlspecialchars($val) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <p style="color: var(--text-secondary);">Thông số đang được cập nhật thêm theo tiêu chuẩn mới nhất.</p>
            <?php endif; ?>
        </div>

        <!-- Tab 3: Reviews -->
        <div id="reviews-tab" class="detail-tab-content" style="display: none; background: var(--bg-card); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 2.5rem;">
            <h3 style="color: var(--gold-light); font-size: 1.4rem; margin-bottom: 1.5rem;">Đánh Giá & Nhận Xét Của Khách Hàng</h3>

            <!-- Existing Reviews List -->
            <div style="display: flex; flex-direction: column; gap: 1.25rem; margin-bottom: 3rem;">
                <?php if (empty($reviews)): ?>
                    <p style="color: var(--text-secondary);">Chưa có đánh giá nào. Hãy là người đầu tiên trải nghiệm và để lại cảm nhận!</p>
                <?php else: ?>
                    <?php foreach ($reviews as $rev): ?>
                    <div style="background: var(--bg-secondary); border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 1.5rem;">
                        <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 0.5rem;">
                            <div>
                                <strong style="color: #fff; font-size: 1.05rem;"><?= htmlspecialchars($rev['user_name']) ?></strong>
                                <span style="font-size: 0.8rem; color: var(--gold-primary); margin-left: 0.5rem;">[<?= htmlspecialchars($rev['user_role']) ?>]</span>
                            </div>
                            <div style="color: #facc15;">
                                <?php for ($s=0; $s<$rev['rating']; $s++) echo '<i class="fas fa-star"></i>'; ?>
                            </div>
                        </div>
                        <p style="color: #cbd5e1; font-size: 0.95rem; line-height: 1.6;"><?= htmlspecialchars($rev['comment']) ?></p>
                        <span style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-top: 0.5rem;">
                            <?= date('d/m/Y H:i', strtotime($rev['created_at'])) ?>
                        </span>
                    </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>

            <!-- Review Submission Form -->
            <div style="background: var(--bg-secondary); border: 1px solid var(--border-gold); border-radius: var(--radius-sm); padding: 2rem;">
                <h4 style="color: #fff; font-size: 1.2rem; margin-bottom: 1rem;">Gửi Đánh Giá Của Bạn</h4>
                <form action="<?= BASE_URL ?>/product-detail.php?id=<?= $product_id ?>" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem; margin-bottom: 1rem;">
                        <div class="form-group">
                            <label>Họ và Tên Doanh Nhân / Hội Viên (*)</label>
                            <input type="text" name="reviewer_name" class="form-control" required placeholder="Họ tên của bạn...">
                        </div>
                        <div class="form-group">
                            <label>Chức Vụ / Danh Hiệu</label>
                            <input type="text" name="reviewer_role" class="form-control" placeholder="Ví dụ: CEO TechVenture / Hội Viên VIP">
                        </div>
                    </div>
                    <div class="form-group" style="margin-bottom: 1rem;">
                        <label>Đánh Giá Điểm Số</label>
                        <select name="rating" class="form-control" style="width: 200px;">
                            <option value="5">★★★★★ (5 Sao - Xuất sắc)</option>
                            <option value="4">★★★★☆ (4 Sao - Tốt)</option>
                            <option value="3">★★★☆☆ (3 Sao - Bình thường)</option>
                        </select>
                    </div>
                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label>Nhận Xét Trải Nghiệm (*)</label>
                        <textarea name="comment" class="form-control" rows="3" required placeholder="Cảm nhận của bạn về chất lượng dịch vụ hoặc sản phẩm..."></textarea>
                    </div>
                    <button type="submit" name="submit_review" class="btn btn-primary btn-sm">
                        <i class="fas fa-paper-plane"></i> GỬI ĐÁNH GIÁ NGAY
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Related Products -->
    <?php if (!empty($related_products)): ?>
    <div class="reveal">
        <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
            <span class="section-tag">GỢI Ý DÀNH CHO CEO</span>
            <h2 class="section-title" style="font-size: 2rem;">SẢN PHẨM CÙNG DANH MỤC</h2>
        </div>
        <div class="products-grid">
            <?php foreach ($related_products as $rp): ?>
            <div class="product-card">
                <div class="product-thumb-wrap">
                    <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($rp['image']) ?>" alt="<?= htmlspecialchars($rp['name']) ?>">
                    <div class="product-quick-actions">
                        <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $rp['id'] ?>" class="action-btn-circle"><i class="fas fa-eye"></i></a>
                        <button type="button" class="action-btn-circle add-cart-btn-direct" data-id="<?= $rp['id'] ?>"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                <div class="product-body">
                    <h4 class="product-title">
                        <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $rp['id'] ?>"><?= htmlspecialchars($rp['name']) ?></a>
                    </h4>
                    <div class="product-price-row">
                        <span class="product-price-current"><?= format_currency($rp['price']) ?></span>
                        <button type="button" class="add-cart-btn-direct" data-id="<?= $rp['id'] ?>">Chọn</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>
</div>

<script>
function switchDetailTab(tabId, btn) {
    document.querySelectorAll('.detail-tab-content').forEach(el => el.style.display = 'none');
    document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
    const target = document.getElementById(tabId);
    if (target) target.style.display = 'block';
    if (btn) btn.classList.add('active');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
