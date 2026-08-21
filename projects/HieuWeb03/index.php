<?php
// index.php - Home Page of HieuMini Stationery
$custom_page_title = "Trang Chủ - Thế Giới Đồ Dùng Học Tập & Sáng Tạo";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Fetch Hot & Featured Products (12 items)
$featured_stmt = $pdo->query("
  SELECT p.*, c.name as category_name 
  FROM products p 
  JOIN categories c ON p.category_id = c.id 
  WHERE p.is_featured = 1 OR p.is_hot = 1 
  ORDER BY p.is_hot DESC, p.rating DESC 
  LIMIT 8
");
$featured_products = $featured_stmt->fetchAll();

// Fetch New Arrivals (8 items)
$new_stmt = $pdo->query("
  SELECT p.*, c.name as category_name 
  FROM products p 
  JOIN categories c ON p.category_id = c.id 
  ORDER BY p.id DESC 
  LIMIT 8
");
$new_products = $new_stmt->fetchAll();
?>

<!-- Hero Section -->
<section class="hero-section">
  <div class="container">
    <div class="hero-grid">
      <!-- Left Hero Text -->
      <div>
        <div class="hero-badge">
          <i class="bi bi-stars" style="color: var(--secondary);"></i>
          <span>BỘ SƯU TẬP TỰU TRƯỜNG 2026</span>
        </div>
        <h1 class="hero-title">
          Thiên Đường <span class="highlight">Đồ Dùng Học Tập</span> & Sáng Tạo
        </h1>
        <p class="hero-desc">
          Khám phá hơn 500+ mẫu bút pastel, sổ còng binder, dụng cụ vẽ mỹ thuật chuyên nghiệp và phụ kiện học đường cực xinh với ưu đãi lên tới 50%.
        </p>
        <div style="display: flex; gap: 16px; flex-wrap: wrap;">
          <a href="products.php" class="btn btn-primary btn-lg">
            <i class="bi bi-bag-check-fill"></i> Mua Sắm Ngay
          </a>
          <a href="products.php?filter=hot" class="btn btn-outline btn-lg">
            <i class="bi bi-fire" style="color: #ef4444;"></i> Sản Phẩm Hot
          </a>
        </div>
        <div class="hero-stats">
          <div class="stat-item">
            <h3>500+</h3>
            <p>Sản phẩm chính hãng</p>
          </div>
          <div class="stat-item">
            <h3>10k+</h3>
            <p>Học sinh tin dùng</p>
          </div>
          <div class="stat-item">
            <h3>4.9★</h3>
            <p>Đánh giá xuất sắc</p>
          </div>
        </div>
      </div>

      <!-- Right Hero Graphic with Floating Badges -->
      <div class="hero-image-wrapper">
        <img src="assets/images/banners/hero-banner.png" alt="HieuMini Stationery" class="hero-main-banner">
        
        <!-- Floating Badge 1 -->
        <div class="floating-badge badge-top-left">
          <div style="width: 44px; height: 44px; background: #e0e7ff; color: var(--primary); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
            <i class="bi bi-lightning-charge-fill"></i>
          </div>
          <div>
            <div style="font-weight: 800; font-size: 0.95rem; color: var(--dark);">Giao Hỏa Tốc 2H</div>
            <div style="font-size: 0.78rem; color: var(--muted);">Nội thành Hà Nội & TP.HCM</div>
          </div>
        </div>

        <!-- Floating Badge 2 -->
        <div class="floating-badge badge-bottom-right">
          <div style="width: 44px; height: 44px; background: #fdf2f8; color: var(--secondary); border-radius: var(--radius-full); display: flex; align-items: center; justify-content: center; font-size: 1.3rem;">
            <i class="bi bi-gift-fill"></i>
          </div>
          <div>
            <div style="font-weight: 800; font-size: 0.95rem; color: var(--dark);">Quà Tặng Tựu Trường</div>
            <div style="font-size: 0.78rem; color: var(--muted);">Tặng sticker cho mọi đơn</div>
          </div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Brand Benefits Bar -->
<section class="benefits-section">
  <div class="container">
    <div class="benefits-grid">
      <div class="benefit-item">
        <div class="benefit-icon"><i class="bi bi-truck"></i></div>
        <div class="benefit-info">
          <h4>Freeship Toàn Quốc</h4>
          <p>Đơn hàng từ 250.000 đ</p>
        </div>
      </div>
      <div class="benefit-item">
        <div class="benefit-icon"><i class="bi bi-patch-check"></i></div>
        <div class="benefit-info">
          <h4>100% Chính Hãng</h4>
          <p>Cam kết chất lượng cao cấp</p>
        </div>
      </div>
      <div class="benefit-item">
        <div class="benefit-icon"><i class="bi bi-arrow-repeat"></i></div>
        <div class="benefit-info">
          <h4>Đổi Trả 7 Ngày</h4>
          <p>Thủ tục đơn giản & nhanh chóng</p>
        </div>
      </div>
      <div class="benefit-item">
        <div class="benefit-icon"><i class="bi bi-headset"></i></div>
        <div class="benefit-info">
          <h4>Hỗ Trợ 24/7</h4>
          <p>Tư vấn tận tâm, chu đáo</p>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- Categories Section -->
<section style="padding: 30px 0 50px;">
  <div class="container">
    <div class="section-title-wrap">
      <span class="section-pill">DANH MỤC NỔI BẬT</span>
      <h2 class="section-heading">Khám Phá Theo Nhu Cầu Học Tập</h2>
      <p class="section-subtitle">Tất cả dụng cụ học tập bạn cần cho một năm học rực rỡ và thành công</p>
    </div>

    <div class="category-grid">
      <?php foreach ($all_categories as $cat): ?>
      <a href="products.php?category=<?= $cat['id'] ?>" class="category-card">
        <div class="cat-icon-circle">
          <i class="bi <?= htmlspecialchars($cat['icon']) ?>"></i>
        </div>
        <div class="cat-name"><?= htmlspecialchars($cat['name']) ?></div>
        <span class="cat-badge"><?= htmlspecialchars($cat['badge']) ?></span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Flash Sales Countdown Section -->
<section style="padding: 10px 0;">
  <div class="container">
    <div class="flash-sale-card">
      <div>
        <span style="background: #ef4444; color: #fff; font-size: 0.78rem; font-weight: 800; padding: 4px 12px; border-radius: var(--radius-full); text-transform: uppercase;">
          <i class="bi bi-fire"></i> FLASH SALE GIỜ VÀNG
        </span>
        <h2 style="font-size: 2.2rem; font-weight: 800; margin: 12px 0 6px;">Giảm Sốc Đến 50% Văn Phòng Phẩm</h2>
        <p style="color: #cbd5e1; font-size: 1rem;">Nhanh tay sở hữu các combo bút pastel, sổ tay và máy tính với giá siêu hời!</p>
        <div class="countdown-box">
          <div class="time-unit">
            <span class="time-val" id="saleHours">14</span>
            <span class="time-label">Giờ</span>
          </div>
          <div class="time-unit">
            <span class="time-val" id="saleMins">45</span>
            <span class="time-label">Phút</span>
          </div>
          <div class="time-unit">
            <span class="time-val" id="saleSecs">30</span>
            <span class="time-label">Giây</span>
          </div>
        </div>
      </div>
      <div>
        <a href="products.php?filter=sale" class="btn btn-secondary btn-lg" style="white-space: nowrap;">
          <i class="bi bi-lightning-charge-fill"></i> Xem Tất Cả Ưu Đãi
        </a>
      </div>
    </div>
  </div>
</section>

<!-- Featured & Best Seller Products -->
<section style="padding: 30px 0 60px;">
  <div class="container">
    <div class="section-title-wrap" style="display: flex; justify-content: space-between; align-items: flex-end; text-align: left;">
      <div>
        <span class="section-pill">BÁN CHẠY NHẤT</span>
        <h2 class="section-heading">Sản Phẩm Được Học Sinh Yêu Thích</h2>
      </div>
      <a href="products.php?filter=hot" style="color: var(--primary); font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
        Xem toàn bộ <i class="bi bi-arrow-right"></i>
      </a>
    </div>

    <div class="product-grid">
      <?php foreach ($featured_products as $p): ?>
      <div class="product-card">
        <div class="product-thumb">
          <div class="product-badges">
            <?php if ($p['is_hot']): ?><span class="badge-tag badge-hot">HOT</span><?php endif; ?>
            <?php if ($p['is_new']): ?><span class="badge-tag badge-new">MỚI</span><?php endif; ?>
          </div>
          <?php if ($p['sale_price']): 
            $discount_pct = round((($p['price'] - $p['sale_price']) / $p['price']) * 100);
          ?>
            <span class="product-discount-pill">-<?= $discount_pct ?>%</span>
          <?php endif; ?>
          <a href="product-detail.php?id=<?= $p['id'] ?>">
            <img src="assets/images/products/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          </a>
          <div class="product-quick-actions">
            <a href="product-detail.php?id=<?= $p['id'] ?>" class="quick-btn" title="Xem chi tiết">
              <i class="bi bi-eye"></i>
            </a>
            <button class="quick-btn ajax-add-to-cart" data-product-id="<?= $p['id'] ?>" title="Thêm vào giỏ">
              <i class="bi bi-bag-plus"></i>
            </button>
          </div>
        </div>
        <div class="product-content">
          <div class="product-cat-name"><?= htmlspecialchars($p['category_name']) ?></div>
          <a href="product-detail.php?id=<?= $p['id'] ?>" class="product-title" title="<?= htmlspecialchars($p['name']) ?>">
            <?= htmlspecialchars($p['name']) ?>
          </a>
          <div class="product-rating">
            <?= render_rating_stars($p['rating']) ?>
            <span class="rating-count">(<?= $p['review_count'] ?>)</span>
          </div>
          <div class="product-price-row">
            <?php if ($p['sale_price']): ?>
              <span class="product-price"><?= format_price($p['sale_price']) ?></span>
              <span class="product-old-price"><?= format_price($p['price']) ?></span>
            <?php else: ?>
              <span class="product-price"><?= format_price($p['price']) ?></span>
            <?php endif; ?>
          </div>
          <button class="add-to-cart-btn ajax-add-to-cart" data-product-id="<?= $p['id'] ?>">
            <i class="bi bi-cart-plus"></i> Thêm Vào Giỏ
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- 3 Promotional Banners Grid -->
<section style="padding: 10px 0 50px;">
  <div class="container">
    <div class="promo-grid">
      <div class="promo-card">
        <a href="products.php?category=1">
          <img src="assets/images/banners/promo-1.png" alt="Bộ Sưu Tập Bút Pastel">
        </a>
      </div>
      <div class="promo-card">
        <a href="products.php?category=2">
          <img src="assets/images/banners/promo-2.png" alt="Sổ Bullet Journal & Planner">
        </a>
      </div>
      <div class="promo-card">
        <a href="products.php?category=3">
          <img src="assets/images/banners/promo-3.png" alt="Dụng Cụ Mỹ Thuật Chuyên Nghiệp">
        </a>
      </div>
    </div>
  </div>
</section>

<!-- New Arrivals Products -->
<section style="padding: 20px 0 60px;">
  <div class="container">
    <div class="section-title-wrap" style="display: flex; justify-content: space-between; align-items: flex-end; text-align: left;">
      <div>
        <span class="section-pill">HÀNG MỚI VỀ</span>
        <h2 class="section-heading">Bộ Sưu Tập Vừa Cập Bến</h2>
      </div>
      <a href="products.php?sort=newest" style="color: var(--primary); font-weight: 700; font-size: 0.95rem; display: flex; align-items: center; gap: 6px;">
        Xem thêm <i class="bi bi-arrow-right"></i>
      </a>
    </div>

    <div class="product-grid">
      <?php foreach ($new_products as $p): ?>
      <div class="product-card">
        <div class="product-thumb">
          <div class="product-badges">
            <span class="badge-tag badge-new">NEW</span>
          </div>
          <?php if ($p['sale_price']): 
            $discount_pct = round((($p['price'] - $p['sale_price']) / $p['price']) * 100);
          ?>
            <span class="product-discount-pill">-<?= $discount_pct ?>%</span>
          <?php endif; ?>
          <a href="product-detail.php?id=<?= $p['id'] ?>">
            <img src="assets/images/products/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
          </a>
          <div class="product-quick-actions">
            <a href="product-detail.php?id=<?= $p['id'] ?>" class="quick-btn" title="Xem chi tiết">
              <i class="bi bi-eye"></i>
            </a>
            <button class="quick-btn ajax-add-to-cart" data-product-id="<?= $p['id'] ?>" title="Thêm vào giỏ">
              <i class="bi bi-bag-plus"></i>
            </button>
          </div>
        </div>
        <div class="product-content">
          <div class="product-cat-name"><?= htmlspecialchars($p['category_name']) ?></div>
          <a href="product-detail.php?id=<?= $p['id'] ?>" class="product-title" title="<?= htmlspecialchars($p['name']) ?>">
            <?= htmlspecialchars($p['name']) ?>
          </a>
          <div class="product-rating">
            <?= render_rating_stars($p['rating']) ?>
            <span class="rating-count">(<?= $p['review_count'] ?>)</span>
          </div>
          <div class="product-price-row">
            <?php if ($p['sale_price']): ?>
              <span class="product-price"><?= format_price($p['sale_price']) ?></span>
              <span class="product-old-price"><?= format_price($p['price']) ?></span>
            <?php else: ?>
              <span class="product-price"><?= format_price($p['price']) ?></span>
            <?php endif; ?>
          </div>
          <button class="add-to-cart-btn ajax-add-to-cart" data-product-id="<?= $p['id'] ?>">
            <i class="bi bi-cart-plus"></i> Thêm Vào Giỏ
          </button>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
