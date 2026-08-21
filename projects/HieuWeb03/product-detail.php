<?php
// product-detail.php - Product Details & Customer Reviews
require_once __DIR__ . '/config/app.php';

$product_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("
  SELECT p.*, c.name as category_name, c.id as cat_id 
  FROM products p 
  JOIN categories c ON p.category_id = c.id 
  WHERE p.id = ?
");
$stmt->execute([$product_id]);
$product = $stmt->fetch();

if (!$product) {
    set_flash('danger', 'Sản phẩm không tồn tại hoặc đã ngừng kinh doanh.');
    header('Location: products.php');
    exit;
}

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewer_name = clean_input($_POST['reviewer_name'] ?? 'Khách hàng ẩn danh');
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $comment = clean_input($_POST['comment'] ?? '');

    if (!empty($comment)) {
        $ins_review = $pdo->prepare("INSERT INTO reviews (product_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
        $ins_review->execute([$product_id, $reviewer_name, $rating, $comment]);
        
        // Update product review stats
        $pdo->prepare("UPDATE products SET review_count = review_count + 1 WHERE id = ?")->execute([$product_id]);
        
        set_flash('success', 'Cảm ơn bạn đã gửi đánh giá sản phẩm!');
        header("Location: product-detail.php?id={$product_id}");
        exit;
    }
}

// Fetch Reviews
$rev_stmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
$rev_stmt->execute([$product_id]);
$reviews = $rev_stmt->fetchAll();

// Fetch Related Products (4 items)
$rel_stmt = $pdo->prepare("
  SELECT p.*, c.name as category_name 
  FROM products p 
  JOIN categories c ON p.category_id = c.id 
  WHERE p.category_id = ? AND p.id != ? 
  LIMIT 4
");
$rel_stmt->execute([$product['category_id'], $product_id]);
$related_products = $rel_stmt->fetchAll();

$custom_page_title = $product['name'];
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container">
  <!-- Breadcrumb -->
  <div style="padding: 20px 0 10px; font-size: 0.88rem; color: var(--muted); display: flex; align-items: center; gap: 8px;">
    <a href="index.php" style="color: var(--muted);"><i class="bi bi-house"></i> Trang chủ</a>
    <span>/</span>
    <a href="products.php?category=<?= $product['cat_id'] ?>" style="color: var(--muted);"><?= htmlspecialchars($product['category_name']) ?></a>
    <span>/</span>
    <span style="color: var(--dark); font-weight: 700;"><?= htmlspecialchars($product['name']) ?></span>
  </div>

  <!-- Product Main Detail Layout -->
  <div class="product-detail-layout">
    <!-- Left: Gallery Preview -->
    <?php
      $base_img_name = pathinfo($product['image'], PATHINFO_FILENAME);
      $img_detail = file_exists(__DIR__ . "/assets/images/products/{$base_img_name}_detail.png") ? "{$base_img_name}_detail.png" : $product['image'];
      $img_box = file_exists(__DIR__ . "/assets/images/products/{$base_img_name}_box.png") ? "{$base_img_name}_box.png" : $product['image'];
    ?>
    <div class="detail-gallery">
      <img id="mainDetailImage" src="assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="<?= htmlspecialchars($product['name']) ?>" class="detail-main-img">
      <div class="detail-thumbs">
        <div class="thumb-item active" data-full-src="assets/images/products/<?= htmlspecialchars($product['image']) ?>" title="Ảnh mặt trước">
          <img src="assets/images/products/<?= htmlspecialchars($product['image']) ?>" alt="Mặt trước">
        </div>
        <div class="thumb-item" data-full-src="assets/images/products/<?= htmlspecialchars($img_detail) ?>" title="Cận cảnh chi tiết">
          <img src="assets/images/products/<?= htmlspecialchars($img_detail) ?>" alt="Chi tiết">
        </div>
        <div class="thumb-item" data-full-src="assets/images/products/<?= htmlspecialchars($img_box) ?>" title="Đóng hộp & Phụ kiện">
          <img src="assets/images/products/<?= htmlspecialchars($img_box) ?>" alt="Đóng hộp">
        </div>
      </div>
    </div>

    <!-- Right: Product Info & Actions -->
    <div class="detail-info">
      <div style="display: flex; gap: 8px; margin-bottom: 10px;">
        <span class="badge-tag badge-new"><?= htmlspecialchars($product['category_name']) ?></span>
        <?php if ($product['is_hot']): ?><span class="badge-tag badge-hot">BÁN CHẠY</span><?php endif; ?>
      </div>

      <h1 class="detail-title"><?= htmlspecialchars($product['name']) ?></h1>

      <div class="detail-meta-row">
        <span>Mã SKU: <strong><?= htmlspecialchars($product['sku']) ?></strong></span>
        <span>|</span>
        <span style="display: flex; align-items: center; gap: 4px;">
          <?= render_rating_stars($product['rating']) ?>
          <span style="font-weight: 700; color: var(--dark); font-size: 0.92rem;"><?= $product['rating'] ?></span>
          <span style="color: var(--muted);">(<?= $product['review_count'] ?> đánh giá)</span>
        </span>
        <span>|</span>
        <span style="color: var(--accent-emerald); font-weight: 700; display: flex; align-items: center; gap: 4px;">
          <i class="bi bi-check-circle-fill"></i> Còn hàng (<?= $product['stock_quantity'] ?> sản phẩm)
        </span>
      </div>

      <!-- Price Box -->
      <div class="detail-price-box">
        <?php if ($product['sale_price']): ?>
          <span class="detail-price"><?= format_price($product['sale_price']) ?></span>
          <span style="font-size: 1.2rem; color: var(--muted); text-decoration: line-through;"><?= format_price($product['price']) ?></span>
          <?php $discount_pct = round((($product['price'] - $product['sale_price']) / $product['price']) * 100); ?>
          <span style="background: #ef4444; color: #fff; font-size: 0.85rem; font-weight: 800; padding: 4px 10px; border-radius: var(--radius-full);">Tiết kiệm <?= $discount_pct ?>%</span>
        <?php else: ?>
          <span class="detail-price"><?= format_price($product['price']) ?></span>
        <?php endif; ?>
      </div>

      <!-- Short Description -->
      <p style="font-size: 1rem; color: #475569; margin-bottom: 24px; line-height: 1.7;">
        <?= nl2br(htmlspecialchars($product['description'])) ?>
      </p>

      <!-- Purchase Actions -->
      <form action="cart.php" method="POST" style="margin-bottom: 30px;">
        <input type="hidden" name="action" value="add">
        <input type="hidden" name="product_id" value="<?= $product['id'] ?>">

        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 24px;">
          <label style="font-weight: 700; font-size: 0.95rem;">Số lượng:</label>
          <div class="quantity-adjuster">
            <button type="button" class="qty-btn qty-minus">-</button>
            <input type="number" name="quantity" id="productQuantity" class="qty-input" value="1" min="1" max="<?= $product['stock_quantity'] ?>">
            <button type="button" class="qty-btn qty-plus">+</button>
          </div>
        </div>

        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
          <button type="button" class="btn btn-primary btn-lg ajax-add-to-cart" data-product-id="<?= $product['id'] ?>" style="flex: 1; min-width: 200px;">
            <i class="bi bi-cart-plus-fill"></i> Thêm Vào Giỏ Hàng
          </button>
          <button type="submit" name="buy_now" value="1" class="btn btn-secondary btn-lg" style="flex: 1; min-width: 180px;">
            <i class="bi bi-lightning-fill"></i> Mua Ngay
          </button>
        </div>
      </form>

      <!-- Guarantees box -->
      <div style="background: var(--white); border: 1px solid var(--border); border-radius: var(--radius-md); padding: 18px; display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 0.88rem;">
        <div style="display: flex; align-items: center; gap: 8px;">
          <i class="bi bi-shield-fill-check" style="color: var(--primary); font-size: 1.2rem;"></i> 100% Hàng chính hãng
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <i class="bi bi-truck" style="color: var(--primary); font-size: 1.2rem;"></i> Đóng gói chống sốc an toàn
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <i class="bi bi-arrow-counterclockwise" style="color: var(--primary); font-size: 1.2rem;"></i> Đổi trả miễn phí 7 ngày
        </div>
        <div style="display: flex; align-items: center; gap: 8px;">
          <i class="bi bi-gift" style="color: var(--primary); font-size: 1.2rem;"></i> Tặng kèm sticker xinh xắn
        </div>
      </div>
    </div>
  </div>

  <!-- Specifications & Customer Reviews Tabs -->
  <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 36px; margin-bottom: 60px; box-shadow: var(--shadow-sm);">
    <h3 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 20px; color: var(--dark); border-bottom: 2px solid var(--border); padding-bottom: 12px;">
      <i class="bi bi-card-text"></i> Thông Số Kỹ Thuật Chi Tiết
    </h3>
    
    <div style="margin-bottom: 40px;">
      <table style="width: 100%; border-collapse: collapse; font-size: 0.95rem;">
        <?php 
        $specs = explode("\n", $product['specification']);
        foreach ($specs as $spec):
          if (strpos($spec, ':') !== false) {
            list($key, $val) = explode(':', $spec, 2);
          } else {
            $key = 'Chi tiết'; $val = $spec;
          }
        ?>
        <tr style="border-bottom: 1px solid var(--border);">
          <td style="padding: 12px 16px; font-weight: 700; width: 220px; background: var(--bg-light); color: var(--dark);"><?= htmlspecialchars(trim($key)) ?></td>
          <td style="padding: 12px 16px; color: #475569;"><?= htmlspecialchars(trim($val)) ?></td>
        </tr>
        <?php endforeach; ?>
      </table>
    </div>

    <!-- Reviews Section -->
    <h3 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 20px; color: var(--dark); border-bottom: 2px solid var(--border); padding-bottom: 12px;">
      <i class="bi bi-chat-heart"></i> Đánh Giá Từ Khách Hàng (<?= count($reviews) ?>)
    </h3>

    <!-- Review Form -->
    <div style="background: var(--bg-light); border-radius: var(--radius-md); padding: 24px; margin-bottom: 30px; border: 1px solid var(--border);">
      <h4 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 14px;">Gửi Đánh Giá Của Bạn</h4>
      <form action="product-detail.php?id=<?= $product['id'] ?>" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
          <div>
            <label class="form-label">Tên của bạn:</label>
            <input type="text" name="reviewer_name" required class="form-control" placeholder="Nhập họ tên của bạn..." value="<?= is_logged_in() ? htmlspecialchars(current_user()['fullname']) : '' ?>">
          </div>
          <div>
            <label class="form-label">Đánh giá số sao:</label>
            <select name="rating" class="form-control">
              <option value="5">⭐⭐⭐⭐⭐ (5/5 sao - Tuyệt vời)</option>
              <option value="4">⭐⭐⭐⭐ (4/5 sao - Rất tốt)</option>
              <option value="3">⭐⭐⭐ (3/5 sao - Bình thường)</option>
              <option value="2">⭐⭐ (2/5 sao - Chưa hài lòng)</option>
              <option value="1">⭐ (1/5 sao - Rất tệ)</option>
            </select>
          </div>
        </div>
        <div class="form-group">
          <label class="form-label">Nội dung nhận xét:</label>
          <textarea name="comment" required rows="3" class="form-control" placeholder="Chia sẻ cảm nhận của bạn về chất lượng đồ dùng học tập này..."></textarea>
        </div>
        <button type="submit" name="submit_review" value="1" class="btn btn-primary">
          <i class="bi bi-send-fill"></i> Gửi Đánh Giá Ngay
        </button>
      </form>
    </div>

    <!-- Reviews List -->
    <div>
      <?php if (!empty($reviews)): ?>
        <?php foreach ($reviews as $rev): ?>
        <div style="padding: 16px 0; border-bottom: 1px solid var(--border);">
          <div style="display: flex; justify-content: space-between; margin-bottom: 6px;">
            <div style="font-weight: 700; color: var(--dark); display: flex; align-items: center; gap: 8px;">
              <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0e7ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 0.9rem;">
                <i class="bi bi-person"></i>
              </div>
              <?= htmlspecialchars($rev['user_name']) ?>
            </div>
            <span style="font-size: 0.82rem; color: var(--muted);"><?= date('d/m/Y', strtotime($rev['created_at'])) ?></span>
          </div>
          <div class="product-rating" style="margin-bottom: 6px;">
            <?= render_rating_stars($rev['rating']) ?>
          </div>
          <p style="color: #475569; font-size: 0.95rem; line-height: 1.6;"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
        </div>
        <?php endforeach; ?>
      <?php else: ?>
        <p style="color: var(--muted); font-style: italic;">Chưa có đánh giá nào cho sản phẩm này. Hãy là người đầu tiên nhận xét!</p>
      <?php endif; ?>
    </div>
  </div>

  <!-- Related Products Section -->
  <?php if (!empty($related_products)): ?>
  <div style="margin-bottom: 60px;">
    <div class="section-title-wrap" style="text-align: left; margin-bottom: 24px;">
      <span class="section-pill">GỢI Ý CHO BẠN</span>
      <h2 class="section-heading" style="font-size: 1.8rem;">Sản Phẩm Cùng Danh Mục</h2>
    </div>
    <div class="product-grid">
      <?php foreach ($related_products as $p): ?>
      <div class="product-card">
        <div class="product-thumb">
          <a href="product-detail.php?id=<?= $p['id'] ?>">
            <img src="assets/images/products/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          </a>
        </div>
        <div class="product-content">
          <a href="product-detail.php?id=<?= $p['id'] ?>" class="product-title"><?= htmlspecialchars($p['name']) ?></a>
          <div class="product-price-row">
            <span class="product-price"><?= format_price($p['sale_price'] ?? $p['price']) ?></span>
          </div>
          <button class="add-to-cart-btn ajax-add-to-cart" data-product-id="<?= $p['id'] ?>">
            <i class="bi bi-cart-plus"></i> Thêm Vào Giỏ
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
