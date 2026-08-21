<?php
$page_title = 'Trang Chủ - Đồ Gia Dụng Thông Minh Cao Cấp';
require_once __DIR__ . '/includes/header.php';

// Fetch Flash Sale Products
$flashStmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_flash_sale = 1 LIMIT 4");
$flashProducts = $flashStmt->fetchAll();

// Fetch Best Sellers
$bestStmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.is_best_seller = 1 LIMIT 8");
$bestProducts = $bestStmt->fetchAll();

// Fetch All Categories with product count
$catCountStmt = $pdo->query("SELECT c.*, COUNT(p.id) as total_products FROM categories c LEFT JOIN products p ON c.id = p.category_id GROUP BY c.id ORDER BY c.id ASC");
$allCats = $catCountStmt->fetchAll();
?>

<main class="container my-4">

  <!-- ================= HERO BANNER ================= -->
  <section class="hero-banner">
    <div class="row align-items-center">
      <div class="col-lg-7">
        <div class="hero-tag">
          <i class="fas fa-sparkles"></i> Siêu Phẩm Gia Dụng Thông Minh 2026
        </div>
        <h1 class="hero-title">
          Nâng Tầm Không Gian Sống Cùng <span>DatCyber</span>
        </h1>
        <p class="hero-desc">
          Trải nghiệm hệ sinh thái thiết bị gia dụng cao cấp: Nồi chiên không dầu đối lưu, Robot hút bụi tự giặt sấy giẻ, Máy lọc không khí chuẩn y tế HEPA H13 và nhiều tiện ích tuyệt vời cho gia đình bạn.
        </p>
        <div class="d-flex flex-wrap gap-3">
          <a href="products.php" class="btn btn-primary-custom">
            <i class="fas fa-cart-shopping"></i> Mua Ngay Hôm Nay
          </a>
          <a href="products.php?category=thiet-bi-nha-bep" class="btn btn-outline-custom">
            <i class="fas fa-fire-burner"></i> Thiết Bị Nhà Bếp
          </a>
        </div>

        <div class="d-flex align-items-center gap-4 mt-4 pt-2">
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-award text-warning fs-4"></i>
            <div>
              <div class="fw-bold fs-6">Chính Hãng 100%</div>
              <small class="text-white-50">Bảo hành 24 tháng</small>
            </div>
          </div>
          <div class="d-flex align-items-center gap-2">
            <i class="fas fa-users text-info fs-4"></i>
            <div>
              <div class="fw-bold fs-6">50.000+ Khách Hàng</div>
              <small class="text-white-50">Hài lòng 99.8%</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-lg-5">
        <div class="hero-img-box animate-float">
          <img src="assets/images/products/air_fryer.jpg" alt="DatCyber Air Fryer" class="img-fluid">
          <div class="hero-floating-card">
            <div class="p-2 bg-warning bg-opacity-25 rounded-circle text-warning">
              <i class="fas fa-bolt fa-lg"></i>
            </div>
            <div>
              <div class="fw-bold small">Nồi Chiên DatCyber CrispyPro</div>
              <div class="text-danger fw-bold">2.490.000 ₫ <span class="badge bg-danger ms-1">-24%</span></div>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

  <!-- ================= CATEGORIES SECTION ================= -->
  <section class="my-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <span class="text-primary text-uppercase fw-bold small"><i class="fas fa-layer-group me-1"></i> Danh Mục Tuyển Chọn</span>
        <h2 class="fw-bold mt-1 m-0">Khám Phá Danh Mục Sản Phẩm</h2>
      </div>
      <a href="products.php" class="text-primary fw-bold text-decoration-none">Xem tất cả <i class="fas fa-arrow-right ms-1"></i></a>
    </div>

    <div class="category-grid">
      <?php foreach ($allCats as $cat): ?>
        <a href="products.php?category=<?php echo urlencode($cat['slug']); ?>" class="category-card text-decoration-none">
          <div class="category-icon-wrap">
            <i class="fas <?php echo htmlspecialchars($cat['icon']); ?>"></i>
          </div>
          <div class="category-name"><?php echo htmlspecialchars($cat['name']); ?></div>
          <div class="category-count"><?php echo $cat['total_products']; ?> sản phẩm có sẵn</div>
        </a>
      <?php endforeach; ?>
    </div>
  </section>

  <!-- ================= FLASH SALE SECTION ================= -->
  <section class="flash-sale-box">
    <div class="flash-header">
      <div class="flash-title">
        <i class="fas fa-bolt-lightning text-warning animate-badge-pulse"></i>
        <span>FLASH SALE GIỜ VÀNG - GIẢM ĐẾN 50%</span>
      </div>
      <div class="d-flex align-items-center gap-2">
        <span class="fw-bold text-secondary small d-none d-sm-inline">KẾT THÚC TRONG:</span>
        <div class="countdown-box">
          <span class="countdown-unit" id="countHours">11</span>
          <span class="countdown-colon">:</span>
          <span class="countdown-unit" id="countMins">45</span>
          <span class="countdown-colon">:</span>
          <span class="countdown-unit" id="countSecs">30</span>
        </div>
      </div>
    </div>

    <div class="product-grid mb-0">
      <?php foreach ($flashProducts as $item): ?>
        <div class="product-card">
          <div class="product-badge-group">
            <?php if ($item['discount_percent'] > 0): ?>
              <span class="badge-discount">-<?php echo $item['discount_percent']; ?>%</span>
            <?php endif; ?>
            <span class="badge-hot"><i class="fas fa-fire me-1"></i>Hot Sale</span>
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

  <!-- ================= PROMO VOUCHER BANNER ================= -->
  <section class="my-5 p-4 rounded-4 text-white position-relative overflow-hidden" style="background: linear-gradient(135deg, #1e3a8a 0%, #0284c7 100%); box-shadow: var(--shadow-lg);">
    <div class="row align-items-center">
      <div class="col-lg-8">
        <span class="badge bg-warning text-dark fw-bold mb-2">ƯU ĐÃI ĐẶC QUYỀN</span>
        <h3 class="fw-bold mb-2">Nhập mã <span class="text-warning text-decoration-underline">DATCYBER10</span> giảm ngay 10% cho đơn từ 1 Triệu</h3>
        <p class="text-white-50 m-0">Áp dụng kèm mã <strong class="text-white">FREESHIP</strong> miễn phí giao hàng trên toàn quốc cho tất cả sản phẩm.</p>
      </div>
      <div class="col-lg-4 text-lg-end mt-3 mt-lg-0">
        <a href="products.php" class="btn btn-warning btn-lg fw-bold rounded-pill px-4 shadow">
          Sử Dụng Mã Ngay <i class="fas fa-tag ms-1"></i>
        </a>
      </div>
    </div>
  </section>

  <!-- ================= BEST SELLERS ================= -->
  <section class="my-5">
    <div class="d-flex justify-content-between align-items-end mb-4">
      <div>
        <span class="text-primary text-uppercase fw-bold small"><i class="fas fa-crown me-1"></i> Đồ Gia Dụng Bán Chạy</span>
        <h2 class="fw-bold mt-1 m-0">Sản Phẩm Được Yêu Thích Nhất</h2>
      </div>
      <a href="products.php?sort=best_seller" class="text-primary fw-bold text-decoration-none">Xem toàn bộ <i class="fas fa-arrow-right ms-1"></i></a>
    </div>

    <div class="product-grid">
      <?php foreach ($bestProducts as $item): ?>
        <div class="product-card">
          <div class="product-badge-group">
            <?php if ($item['discount_percent'] > 0): ?>
              <span class="badge-discount">-<?php echo $item['discount_percent']; ?>%</span>
            <?php endif; ?>
            <span class="badge bg-primary text-white"><i class="fas fa-check-circle me-1"></i>Bán chạy</span>
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

  <!-- ================= CUSTOMER REVIEWS / TESTIMONIALS ================= -->
  <section class="my-5 p-4 p-lg-5 bg-white rounded-4 border shadow-sm">
    <div class="text-center max-w-600 mx-auto mb-5">
      <span class="text-primary text-uppercase fw-bold small"><i class="fas fa-heart me-1"></i> Khách Hàng Nói Về DatCyber</span>
      <h2 class="fw-bold mt-1">Đánh Giá Từ Hơn 50.000+ Gia Đình Việt</h2>
      <p class="text-secondary">Chất lượng sản phẩm đỉnh cao cùng dịch vụ bảo hành tận tình 1 đổi 1 trong 24 tháng.</p>
    </div>

    <div class="row g-4">
      <div class="col-md-4">
        <div class="p-4 bg-light rounded-4 h-100 border">
          <div class="text-warning mb-3">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
          </div>
          <p class="text-secondary fst-italic mb-3">
            "Chiếc nồi chiên không dầu DatCyber CrispyPro 6.5L thực sự làm thay đổi căn bếp nhà mình. Nấu đồ ăn nhanh, giòn rụm và không bị dầu mỡ. Màn hình cảm ứng nhìn rất xịn xò!"
          </p>
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">TQ</div>
            <div>
              <div class="fw-bold">Trần Minh Quang</div>
              <small class="text-muted">Hà Nội • Mua Nồi Chiên CrispyPro</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="p-4 bg-light rounded-4 h-100 border">
          <div class="text-warning mb-3">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
          </div>
          <p class="text-secondary fst-italic mb-3">
            "Robot hút bụi lau nhà OmniClean X9 cực kỳ đáng tiền! Tự động giặt giẻ lau bằng nước nóng và sấy khô nên nhà không bao giờ bị mùi ẩm. Đi làm về nhà lúc nào cũng sạch bóng."
          </p>
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-success text-white fw-bold d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">NM</div>
            <div>
              <div class="fw-bold">Hoàng Nhật Minh</div>
              <small class="text-muted">Đà Nẵng • Mua Robot OmniClean X9</small>
            </div>
          </div>
        </div>
      </div>

      <div class="col-md-4">
        <div class="p-4 bg-light rounded-4 h-100 border">
          <div class="text-warning mb-3">
            <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
          </div>
          <p class="text-secondary fst-italic mb-3">
            "Máy lọc không khí AirShield Ultra lọc bụi mịn PM2.5 rất tốt. Bé nhà mình bị dị ứng thời tiết nhưng từ khi đặt máy trong phòng ngủ là ngủ êm ro. Giao hàng 2 tiếng là tới!"
          </p>
          <div class="d-flex align-items-center gap-3">
            <div class="rounded-circle bg-warning text-white fw-bold d-flex align-items-center justify-content-center" style="width: 42px; height: 42px;">PL</div>
            <div>
              <div class="fw-bold">Phạm Phương Linh</div>
              <small class="text-muted">TP. Hồ Chí Minh • Mua Máy Lọc Khí</small>
            </div>
          </div>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
