<?php
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);

// Fetch Product details
$stmt = $pdo->prepare("SELECT p.*, c.name as category_name, c.slug as category_slug 
                       FROM products p 
                       JOIN categories c ON p.category_id = c.id 
                       WHERE p.id = ?");
$stmt->execute([$id]);
$product = $stmt->fetch();

if (!$product) {
    header('Location: products.php');
    exit;
}

$page_title = $product['name'];
require_once __DIR__ . '/includes/header.php';

// Handle Review Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit_review'])) {
    $reviewerName = clean_input($_POST['reviewer_name'] ?? '');
    $rating = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $comment = clean_input($_POST['comment'] ?? '');

    if (!empty($reviewerName) && !empty($comment) && mb_strlen($comment) >= 5) {
        $insStmt = $pdo->prepare("INSERT INTO reviews (product_id, user_name, rating, comment) VALUES (?, ?, ?, ?)");
        $insStmt->execute([$id, $reviewerName, $rating, $comment]);

        // Update product rating and review count
        $avgStmt = $pdo->prepare("SELECT AVG(rating) as avg_rating, COUNT(id) as total_rev FROM reviews WHERE product_id = ?");
        $avgStmt->execute([$id]);
        $stats = $avgStmt->fetch();

        $upStmt = $pdo->prepare("UPDATE products SET rating = ?, review_count = ? WHERE id = ?");
        $upStmt->execute([round((float)$stats['avg_rating'], 1), (int)$stats['total_rev'], $id]);

        set_flash('success', 'Cảm ơn bạn đã gửi đánh giá cho sản phẩm!');
        header("Location: product-detail.php?id=$id#reviews");
        exit;
    } else {
        set_flash('error', 'Vui lòng nhập tên và nội dung đánh giá tối thiểu 5 ký tự!');
        header("Location: product-detail.php?id=$id#reviews");
        exit;
    }
}

// Fetch Reviews for this product
$revStmt = $pdo->prepare("SELECT * FROM reviews WHERE product_id = ? ORDER BY id DESC");
$revStmt->execute([$id]);
$reviews = $revStmt->fetchAll();

// Fetch Related Products
$relStmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.category_id = ? AND p.id != ? LIMIT 4");
$relStmt->execute([$product['category_id'], $id]);
$relatedProducts = $relStmt->fetchAll();

// Parse Specifications
$specsList = [];
if (!empty($product['specs'])) {
    $lines = explode("\n", $product['specs']);
    foreach ($lines as $line) {
        if (strpos($line, ':') !== false) {
            list($key, $val) = explode(':', $line, 2);
            $specsList[trim($key)] = trim($val);
        }
    }
}
?>

<main class="container my-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-3 border shadow-sm">
      <li class="breadcrumb-item"><a href="index.php" class="text-primary text-decoration-none"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
      <li class="breadcrumb-item"><a href="products.php" class="text-secondary text-decoration-none">Sản phẩm</a></li>
      <li class="breadcrumb-item"><a href="products.php?category=<?php echo urlencode($product['category_slug']); ?>" class="text-secondary text-decoration-none"><?php echo htmlspecialchars($product['category_name']); ?></a></li>
      <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($product['name']); ?></li>
    </ol>
  </nav>

  <!-- Product Main Detail Section -->
  <section class="bg-white p-4 p-lg-5 rounded-4 border shadow-sm mb-5">
    <div class="row g-5">
      
      <!-- Left: Image Gallery -->
      <div class="col-lg-5">
        <div class="detail-gallery-main position-relative">
          <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" id="mainProductImg">
          <div class="position-absolute top-0 start-0 m-3">
            <?php if ($product['discount_percent'] > 0): ?>
              <span class="badge-discount fs-6">-<?php echo $product['discount_percent']; ?>%</span>
            <?php endif; ?>
          </div>
        </div>

        <div class="d-flex gap-2 justify-content-center mt-3">
          <div class="p-1 rounded border border-primary" style="width: 70px; height: 70px; cursor: pointer;">
            <img src="assets/images/products/<?php echo htmlspecialchars($product['image']); ?>" class="w-100 h-100 object-fit-cover rounded">
          </div>
        </div>

        <div class="row text-center mt-4 g-2">
          <div class="col-4">
            <div class="p-2 bg-light rounded-3 border">
              <i class="fas fa-shield-halved text-primary fs-5 mb-1"></i>
              <div class="small fw-bold">Bảo hành 24T</div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2 bg-light rounded-3 border">
              <i class="fas fa-rotate-left text-success fs-5 mb-1"></i>
              <div class="small fw-bold">1 Đổi 1 (30 ngày)</div>
            </div>
          </div>
          <div class="col-4">
            <div class="p-2 bg-light rounded-3 border">
              <i class="fas fa-truck text-warning fs-5 mb-1"></i>
              <div class="small fw-bold">Free Vận chuyển</div>
            </div>
          </div>
        </div>
      </div>

      <!-- Right: Product Info & Actions -->
      <div class="col-lg-7">
        <span class="text-primary text-uppercase fw-bold small"><i class="fas fa-tag me-1"></i> <?php echo htmlspecialchars($product['category_name']); ?></span>
        <h1 class="fw-bold fs-2 mt-1 mb-2"><?php echo htmlspecialchars($product['name']); ?></h1>

        <!-- Rating & Stock -->
        <div class="d-flex align-items-center gap-3 mb-3">
          <?php echo render_stars($product['rating']); ?>
          <span class="text-muted">|</span>
          <span class="text-secondary small"><?php echo $product['review_count']; ?> đánh giá</span>
          <span class="text-muted">|</span>
          <span class="badge bg-success bg-opacity-10 text-success"><i class="fas fa-check me-1"></i>Còn hàng (<?php echo $product['stock']; ?> chiếc)</span>
        </div>

        <!-- Price Box -->
        <div class="detail-price-box">
          <div class="detail-price-current"><?php echo format_price($product['price']); ?></div>
          <?php if ($product['old_price'] > $product['price']): ?>
            <div class="detail-price-old"><?php echo format_price($product['old_price']); ?></div>
            <span class="badge bg-danger rounded-pill">Tiết kiệm <?php echo format_price($product['old_price'] - $product['price']); ?></span>
          <?php endif; ?>
        </div>

        <!-- Short Description -->
        <p class="text-secondary mb-4" style="font-size: 1.05rem;">
          <?php echo htmlspecialchars($product['short_description']); ?>
        </p>

        <!-- Quantity and Action Buttons -->
        <div class="d-flex flex-wrap align-items-center gap-3 mb-4">
          <div class="d-flex align-items-center gap-2">
            <span class="fw-semibold">Số lượng:</span>
            <div class="qty-control">
              <button type="button" class="qty-btn" onclick="let inp=document.getElementById('detailQty'); if(parseInt(inp.value)>1) inp.value=parseInt(inp.value)-1;">-</button>
              <input type="text" id="detailQty" class="qty-input" value="1" readonly>
              <button type="button" class="qty-btn" onclick="let inp=document.getElementById('detailQty'); if(parseInt(inp.value) < <?php echo (int)$product['stock']; ?>) inp.value=parseInt(inp.value)+1;">+</button>
            </div>
          </div>

          <button type="button" class="btn btn-primary-custom flex-grow-1" onclick="addToCart(<?php echo $product['id']; ?>, parseInt(document.getElementById('detailQty').value));">
            <i class="fas fa-cart-plus me-1"></i> Thêm Vào Giỏ Hàng
          </button>

          <button type="button" class="btn btn-warning btn-lg fw-bold rounded-pill px-4 text-dark shadow-sm" onclick="addToCart(<?php echo $product['id']; ?>, parseInt(document.getElementById('detailQty').value), false, 'checkout.php');">
            Mua Ngay
          </button>
        </div>

        <!-- Special Benefits Box -->
        <div class="p-3 bg-light rounded-3 border">
          <h6 class="fw-bold text-dark mb-2"><i class="fas fa-gift text-danger me-2"></i>Ưu Đãi Đặc Biệt Khi Đặt Hàng Hôm Nay:</h6>
          <ul class="list-unstyled text-secondary small m-0 d-flex flex-column gap-1">
            <li><i class="fas fa-check-circle text-success me-2"></i>Tặng kèm sách công thức nấu ăn & cẩm nang gia dụng 2026</li>
            <li><i class="fas fa-check-circle text-success me-2"></i>Giảm 10% khi mua kèm phụ kiện thay thế chính hãng</li>
            <li><i class="fas fa-check-circle text-success me-2"></i>Miễn phí kiểm tra và lắp đặt tận nhà tại Hà Nội & TP.HCM</li>
          </ul>
        </div>

      </div>

    </div>
  </section>

  <!-- Product Specifications & Description Tabs -->
  <section class="bg-white p-4 p-lg-5 rounded-4 border shadow-sm mb-5">
    <ul class="nav nav-pills mb-4 border-bottom pb-3 gap-2" id="prodTabs" role="tablist">
      <li class="nav-item">
        <button class="nav-link active rounded-pill fw-bold" id="desc-tab" data-bs-toggle="pill" data-bs-target="#desc-pane">
          <i class="fas fa-file-lines me-1"></i> Mô Tả Chi Tiết
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link rounded-pill fw-bold" id="specs-tab" data-bs-toggle="pill" data-bs-target="#specs-pane">
          <i class="fas fa-sliders me-1"></i> Thông Số Kỹ Thuật
        </button>
      </li>
      <li class="nav-item">
        <button class="nav-link rounded-pill fw-bold" id="reviews-tab" data-bs-toggle="pill" data-bs-target="#reviews-pane">
          <i class="fas fa-star me-1"></i> Đánh Giá (<?php echo count($reviews); ?>)
        </button>
      </li>
    </ul>

    <div class="tab-content" id="prodTabsContent">
      
      <!-- Tab 1: Detailed Description -->
      <div class="tab-pane fade show active" id="desc-pane">
        <div class="product-description-content" style="line-height: 1.8; color: #334155;">
          <?php echo sanitize_html($product['description']); ?>
        </div>
      </div>

      <!-- Tab 2: Specs -->
      <div class="tab-pane fade" id="specs-pane">
        <?php if (!empty($specsList)): ?>
          <table class="spec-table">
            <tbody>
              <?php foreach ($specsList as $key => $val): ?>
                <tr>
                  <td><?php echo htmlspecialchars($key); ?></td>
                  <td><strong><?php echo htmlspecialchars($val); ?></strong></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        <?php else: ?>
          <p class="text-muted">Đang cập nhật thông số kỹ thuật...</p>
        <?php endif; ?>
      </div>

      <!-- Tab 3: Reviews -->
      <div class="tab-pane fade" id="reviews-pane">
        <div class="row g-4" id="reviews">
          
          <!-- Left: Review Summary & Form -->
          <div class="col-lg-5">
            <div class="p-4 bg-light rounded-4 border mb-4">
              <h5 class="fw-bold mb-3">Viết Đánh Giá Của Bạn</h5>
              <form action="product-detail.php?id=<?php echo $id; ?>" method="POST">
                <div class="mb-3">
                  <label class="form-label small fw-semibold">Họ và tên của bạn:</label>
                  <input type="text" name="reviewer_name" class="form-control" required placeholder="VD: Nguyễn Văn A">
                </div>
                <div class="mb-3">
                  <label class="form-label small fw-semibold">Chọn điểm đánh giá:</label>
                  <select name="rating" class="form-select">
                    <option value="5">⭐⭐⭐⭐⭐ (5 sao - Rất tuyệt vời)</option>
                    <option value="4">⭐⭐⭐⭐ (4 sao - Tốt)</option>
                    <option value="3">⭐⭐⭐ (3 sao - Bình thường)</option>
                    <option value="2">⭐⭐ (2 sao - Tạm được)</option>
                    <option value="1">⭐ (1 sao - Kém)</option>
                  </select>
                </div>
                <div class="mb-3">
                  <label class="form-label small fw-semibold">Nhận xét chi tiết về sản phẩm:</label>
                  <textarea name="comment" rows="3" class="form-control" required placeholder="Chia sẻ cảm nhận về chất lượng, đóng gói, trải nghiệm sử dụng..."></textarea>
                </div>
                <button type="submit" name="submit_review" class="btn btn-primary-custom w-100 justify-content-center">
                  <i class="fas fa-paper-plane me-1"></i> Gửi Đánh Giá Ngay
                </button>
              </form>
            </div>
          </div>

          <!-- Right: Review List -->
          <div class="col-lg-7">
            <h5 class="fw-bold mb-3">Đánh Giá Từ Người Dùng (<?php echo count($reviews); ?>)</h5>
            <?php if (empty($reviews)): ?>
              <div class="p-4 bg-light rounded-3 text-center text-muted">
                <i class="far fa-comment-dots fa-2x mb-2"></i>
                <p class="m-0">Chưa có đánh giá nào. Hãy là người đầu tiên trải nghiệm và chia sẻ!</p>
              </div>
            <?php else: ?>
              <div class="d-flex flex-column gap-3">
                <?php foreach ($reviews as $rev): ?>
                  <div class="p-3 bg-light rounded-3 border">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                      <div class="fw-bold"><i class="fas fa-user-circle text-primary me-2"></i><?php echo htmlspecialchars($rev['user_name']); ?></div>
                      <small class="text-muted"><?php echo date('d/m/Y H:i', strtotime($rev['created_at'])); ?></small>
                    </div>
                    <div class="text-warning small mb-2">
                      <?php for ($s=0; $s<$rev['rating']; $s++): ?><i class="fas fa-star"></i><?php endfor; ?>
                    </div>
                    <p class="text-secondary small m-0"><?php echo htmlspecialchars($rev['comment']); ?></p>
                  </div>
                <?php endforeach; ?>
              </div>
            <?php endif; ?>
          </div>

        </div>
      </div>

    </div>
  </section>

  <!-- Related Products Section -->
  <?php if (!empty($relatedProducts)): ?>
    <section class="my-5">
      <div class="d-flex justify-content-between align-items-end mb-4">
        <div>
          <span class="text-primary text-uppercase fw-bold small"><i class="fas fa-tags me-1"></i> Gợi Ý Thêm</span>
          <h3 class="fw-bold mt-1 m-0">Sản Phẩm Cùng Danh Mục</h3>
        </div>
      </div>

      <div class="product-grid">
        <?php foreach ($relatedProducts as $item): ?>
          <div class="product-card">
            <div class="product-badge-group">
              <?php if ($item['discount_percent'] > 0): ?>
                <span class="badge-discount">-<?php echo $item['discount_percent']; ?>%</span>
              <?php endif; ?>
            </div>

            <div class="product-img-wrap">
              <img src="assets/images/products/<?php echo htmlspecialchars($item['image']); ?>" alt="<?php echo htmlspecialchars($item['name']); ?>">
              <div class="product-quick-actions">
                <button type="button" class="btn-action-icon" onclick="openQuickView(<?php echo $item['id']; ?>)" title="Xem nhanh">
                  <i class="fas fa-eye"></i>
                </button>
                <button type="button" class="btn-action-icon" onclick="addToCart(<?php echo $item['id']; ?>)" title="Thêm vào giỏ">
                  <i class="fas fa-cart-plus"></i>
                </button>
              </div>
            </div>

            <div class="product-category-tag"><?php echo htmlspecialchars($item['category_name']); ?></div>
            <a href="product-detail.php?id=<?php echo $item['id']; ?>" class="product-title" title="<?php echo htmlspecialchars($item['name']); ?>">
              <?php echo htmlspecialchars($item['name']); ?>
            </a>

            <div class="product-rating">
              <?php echo render_stars($item['rating']); ?>
            </div>

            <div class="product-price-row">
              <div>
                <div class="price-current"><?php echo format_price($item['price']); ?></div>
                <?php if ($item['old_price'] > $item['price']): ?>
                  <div class="price-old"><?php echo format_price($item['old_price']); ?></div>
                <?php endif; ?>
              </div>
              <button type="button" class="btn-add-cart-mini" onclick="addToCart(<?php echo $item['id']; ?>)">
                <i class="fas fa-plus"></i> Thêm
              </button>
            </div>
          </div>
        <?php endforeach; ?>
      </div>
    </section>
  <?php endif; ?>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
