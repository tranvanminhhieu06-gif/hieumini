<?php
// products.php - Product Catalog & Filtering
$custom_page_title = "Danh Mục Đồ Dùng Học Tập";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';

// Filter parameters
$cat_id = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$q = clean_input($_GET['q'] ?? '');
$price_range = clean_input($_GET['price'] ?? '');
$sort = clean_input($_GET['sort'] ?? 'newest');
$filter = clean_input($_GET['filter'] ?? '');

// Build dynamic SQL query
$query = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($cat_id > 0) {
    $query .= " AND p.category_id = ?";
    $params[] = $cat_id;
}

if (!empty($q)) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?)";
    $searchTerm = "%{$q}%";
    $params[] = $searchTerm;
    $params[] = $searchTerm;
    $params[] = $searchTerm;
}

if ($filter === 'hot') {
    $query .= " AND p.is_hot = 1";
} elseif ($filter === 'sale') {
    $query .= " AND p.sale_price IS NOT NULL";
} elseif ($filter === 'new') {
    $query .= " AND p.is_new = 1";
}

if ($price_range === 'under50') {
    $query .= " AND COALESCE(p.sale_price, p.price) < 50000";
} elseif ($price_range === '50to100') {
    $query .= " AND COALESCE(p.sale_price, p.price) BETWEEN 50000 AND 100000";
} elseif ($price_range === '100to200') {
    $query .= " AND COALESCE(p.sale_price, p.price) BETWEEN 100000 AND 200000";
} elseif ($price_range === 'over200') {
    $query .= " AND COALESCE(p.sale_price, p.price) > 200000";
}

// Sorting
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY COALESCE(p.sale_price, p.price) ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY COALESCE(p.sale_price, p.price) DESC";
        break;
    case 'name_asc':
        $query .= " ORDER BY p.name ASC";
        break;
    case 'rating':
        $query .= " ORDER BY p.rating DESC, p.review_count DESC";
        break;
    case 'newest':
    default:
        $query .= " ORDER BY p.id DESC";
        break;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();
$total_found = count($products);
?>

<div class="container">
  <!-- Breadcrumb -->
  <div style="padding: 20px 0 10px; font-size: 0.88rem; color: var(--muted); display: flex; align-items: center; gap: 8px;">
    <a href="index.php" style="color: var(--muted);"><i class="bi bi-house"></i> Trang chủ</a>
    <span>/</span>
    <span style="color: var(--primary); font-weight: 600;">Sản phẩm</span>
    <?php if ($cat_id > 0): 
      $current_cat_name = "";
      foreach($all_categories as $c) { if($c['id'] == $cat_id) $current_cat_name = $c['name']; }
    ?>
      <span>/</span>
      <span style="color: var(--dark); font-weight: 700;"><?= htmlspecialchars($current_cat_name) ?></span>
    <?php endif; ?>
  </div>

  <div class="catalog-layout">
    <!-- Left Filters Sidebar -->
    <aside class="filter-sidebar">
      <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 20px; color: var(--dark);">
        <i class="bi bi-funnel"></i> Bộ Lọc Tìm Kiếm
      </h3>

      <!-- Categories Filter -->
      <div class="filter-group">
        <div class="filter-title">
          <span>Danh Mục</span>
        </div>
        <ul class="filter-list">
          <li>
            <a href="products.php<?= !empty($sort) ? '?sort='.$sort : '' ?>" style="display: flex; justify-content: space-between; font-size: 0.92rem; padding: 6px 0; font-weight: <?= $cat_id === 0 ? '700; color: var(--primary);' : '500;' ?>">
              <span>Tất cả danh mục</span>
              <span style="color: var(--muted);">(30)</span>
            </a>
          </li>
          <?php foreach ($all_categories as $c): ?>
          <li>
            <a href="products.php?category=<?= $c['id'] ?><?= !empty($sort) ? '&sort='.$sort : '' ?>" style="display: flex; justify-content: space-between; font-size: 0.92rem; padding: 6px 0; font-weight: <?= $cat_id == $c['id'] ? '700; color: var(--primary);' : '500;' ?>">
              <span><?= htmlspecialchars($c['name']) ?></span>
            </a>
          </li>
          <?php endforeach; ?>
        </ul>
      </div>

      <!-- Price Range Filter -->
      <div class="filter-group">
        <div class="filter-title">
          <span>Mức Giá</span>
        </div>
        <ul class="filter-list">
          <li>
            <a href="products.php?<?= http_build_query(array_merge($_GET, ['price' => ''])) ?>" style="font-size: 0.9rem; font-weight: <?= empty($price_range) ? '700; color: var(--primary);' : '500;' ?>">Tất cả mức giá</a>
          </li>
          <li>
            <a href="products.php?<?= http_build_query(array_merge($_GET, ['price' => 'under50'])) ?>" style="font-size: 0.9rem; font-weight: <?= $price_range === 'under50' ? '700; color: var(--primary);' : '500;' ?>">Dưới 50.000 đ</a>
          </li>
          <li>
            <a href="products.php?<?= http_build_query(array_merge($_GET, ['price' => '50to100'])) ?>" style="font-size: 0.9rem; font-weight: <?= $price_range === '50to100' ? '700; color: var(--primary);' : '500;' ?>">50.000 đ - 100.000 đ</a>
          </li>
          <li>
            <a href="products.php?<?= http_build_query(array_merge($_GET, ['price' => '100to200'])) ?>" style="font-size: 0.9rem; font-weight: <?= $price_range === '100to200' ? '700; color: var(--primary);' : '500;' ?>">100.000 đ - 200.000 đ</a>
          </li>
          <li>
            <a href="products.php?<?= http_build_query(array_merge($_GET, ['price' => 'over200'])) ?>" style="font-size: 0.9rem; font-weight: <?= $price_range === 'over200' ? '700; color: var(--primary);' : '500;' ?>">Trên 200.000 đ</a>
          </li>
        </ul>
      </div>

      <!-- Status Filter -->
      <div class="filter-group">
        <div class="filter-title">
          <span>Ưu Đãi & Độc Quyền</span>
        </div>
        <ul class="filter-list">
          <li>
            <a href="products.php?<?= http_build_query(array_merge($_GET, ['filter' => 'sale'])) ?>" style="font-size: 0.9rem; display: flex; align-items: center; gap: 6px; <?= $filter === 'sale' ? 'color: var(--primary); font-weight: 700;' : '' ?>">
              <i class="bi bi-tag-fill" style="color: var(--secondary);"></i> Đang giảm giá
            </a>
          </li>
          <li>
            <a href="products.php?<?= http_build_query(array_merge($_GET, ['filter' => 'hot'])) ?>" style="font-size: 0.9rem; display: flex; align-items: center; gap: 6px; <?= $filter === 'hot' ? 'color: var(--primary); font-weight: 700;' : '' ?>">
              <i class="bi bi-fire" style="color: #ef4444;"></i> Bán chạy nhất
            </a>
          </li>
          <li>
            <a href="products.php?<?= http_build_query(array_merge($_GET, ['filter' => 'new'])) ?>" style="font-size: 0.9rem; display: flex; align-items: center; gap: 6px; <?= $filter === 'new' ? 'color: var(--primary); font-weight: 700;' : '' ?>">
              <i class="bi bi-stars" style="color: var(--accent-emerald);"></i> Hàng mới về
            </a>
          </li>
        </ul>
      </div>

      <!-- Reset Filter Button -->
      <?php if (!empty($q) || $cat_id > 0 || !empty($price_range) || !empty($filter)): ?>
      <a href="products.php" class="btn btn-outline btn-sm" style="width: 100%; justify-content: center; margin-top: 10px;">
        <i class="bi bi-arrow-counterclockwise"></i> Xóa Tất Cả Bộ Lọc
      </a>
      <?php endif; ?>
    </aside>

    <!-- Mobile Filter Toggle Bar -->
    <div class="mobile-filter-bar">
      <button type="button" class="mobile-filter-toggle-btn" id="mobileFilterToggleBtn">
        <i class="bi bi-funnel-fill" style="color: var(--primary);"></i> Bộ Lọc & Tìm Kiếm
      </button>
    </div>

    <!-- Right Products Grid Container -->
    <main>
      <!-- Header bar with sorting -->
      <div class="catalog-header">
        <div>
          <span style="font-size: 1.1rem; font-weight: 800; color: var(--dark);">
            <?php if (!empty($q)): ?>
              Kết quả tìm kiếm cho "<em><?= htmlspecialchars($q) ?></em>"
            <?php else: ?>
              Tất Cả Sản Phẩm
            <?php endif; ?>
          </span>
          <span style="font-size: 0.88rem; color: var(--muted); margin-left: 8px;">(<?= $total_found ?> sản phẩm)</span>
        </div>

        <!-- Sort dropdown -->
        <div style="display: flex; align-items: center; gap: 10px; flex-wrap: wrap;">
          <label style="font-size: 0.9rem; color: var(--muted); font-weight: 600;">Sắp xếp theo:</label>
          <select onchange="location = this.value;" class="form-control" style="padding: 8px 12px; width: auto; font-size: 0.9rem;">
            <option value="products.php?<?= http_build_query(array_merge($_GET, ['sort' => 'newest'])) ?>" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
            <option value="products.php?<?= http_build_query(array_merge($_GET, ['sort' => 'price_asc'])) ?>" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
            <option value="products.php?<?= http_build_query(array_merge($_GET, ['sort' => 'price_desc'])) ?>" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
            <option value="products.php?<?= http_build_query(array_merge($_GET, ['sort' => 'rating'])) ?>" <?= $sort === 'rating' ? 'selected' : '' ?>>Đánh giá cao nhất</option>
            <option value="products.php?<?= http_build_query(array_merge($_GET, ['sort' => 'name_asc'])) ?>" <?= $sort === 'name_asc' ? 'selected' : '' ?>>Tên A-Z</option>
          </select>
        </div>
      </div>

      <!-- Products Grid -->
      <?php if ($total_found > 0): ?>
      <div class="product-grid">
        <?php foreach ($products as $p): ?>
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
              <a href="product-detail.php?id=<?= $p['id'] ?>" class="quick-btn" title="Xem chi tiết" aria-label="Xem chi tiết">
                <i class="bi bi-eye"></i>
              </a>
              <button class="quick-btn ajax-add-to-cart" data-product-id="<?= $p['id'] ?>" title="Thêm vào giỏ" aria-label="Thêm vào giỏ">
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
      <?php else: ?>
      <div style="background: var(--white); border-radius: var(--radius-lg); padding: 60px 30px; text-align: center; border: 1px solid var(--border); box-shadow: var(--shadow-sm);">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: #fee2e2; color: #ef4444; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; margin: 0 auto 20px;">
          <i class="bi bi-search"></i>
        </div>
        <h3 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 8px; color: var(--dark);">Không tìm thấy sản phẩm nào!</h3>
        <p style="color: var(--muted); margin-bottom: 24px; max-width: 480px; margin-left: auto; margin-right: auto;">Hãy thử tìm kiếm với từ khóa khác hoặc xóa bớt các bộ lọc danh mục/mức giá đang chọn.</p>
        <a href="products.php" class="btn btn-primary">
          <i class="bi bi-arrow-counterclockwise"></i> Xóa Bộ Lọc & Xem Tất Cả
        </a>
      </div>
      <?php endif; ?>
    </main>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
