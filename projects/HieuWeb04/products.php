<?php
$page_title = 'Danh Sách Sản Phẩm Gia Dụng';
require_once __DIR__ . '/includes/header.php';

// Filter parameters
$catSlug = clean_input($_GET['category'] ?? '');
$search = clean_input($_GET['search'] ?? '');
$priceRange = clean_input($_GET['price'] ?? '');
$sort = clean_input($_GET['sort'] ?? 'newest');

// Build SQL Query
$sql = "SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        JOIN categories c ON p.category_id = c.id 
        WHERE 1=1";
$params = [];

if (!empty($catSlug)) {
    $sql .= " AND c.slug = ?";
    $params[] = $catSlug;
}

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.short_description LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

if (!empty($priceRange)) {
    if ($priceRange === 'under1m') {
        $sql .= " AND p.price < 1000000";
    } elseif ($priceRange === '1m-3m') {
        $sql .= " AND p.price BETWEEN 1000000 AND 3000000";
    } elseif ($priceRange === '3m-6m') {
        $sql .= " AND p.price BETWEEN 3000000 AND 6000000";
    } elseif ($priceRange === 'above6m') {
        $sql .= " AND p.price > 6000000";
    }
}

// Sorting logic
switch ($sort) {
    case 'price_asc':
        $sql .= " ORDER BY p.price ASC";
        break;
    case 'price_desc':
        $sql .= " ORDER BY p.price DESC";
        break;
    case 'rating':
        $sql .= " ORDER BY p.rating DESC";
        break;
    case 'best_seller':
        $sql .= " ORDER BY p.is_best_seller DESC, p.id DESC";
        break;
    case 'newest':
    default:
        $sql .= " ORDER BY p.id DESC";
        break;
}

$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Get current category info if filtered
$currentCategoryName = 'Tất cả sản phẩm';
if (!empty($catSlug)) {
    $cStmt = $pdo->prepare("SELECT name FROM categories WHERE slug = ?");
    $cStmt->execute([$catSlug]);
    $catRow = $cStmt->fetch();
    if ($catRow) $currentCategoryName = $catRow['name'];
}
?>

<main class="container my-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-3 border shadow-sm">
      <li class="breadcrumb-item"><a href="index.php" class="text-primary text-decoration-none"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
      <li class="breadcrumb-item"><a href="products.php" class="text-secondary text-decoration-none">Sản phẩm</a></li>
      <?php if (!empty($catSlug)): ?>
        <li class="breadcrumb-item active" aria-current="page"><?php echo htmlspecialchars($currentCategoryName); ?></li>
      <?php endif; ?>
    </ol>
  </nav>

  <div class="row g-4">
    
    <!-- Sidebar Filters -->
    <div class="col-lg-3">
      <div class="bg-white p-4 rounded-4 border shadow-sm sticky-top" style="top: 90px; z-index: 10;">
        <div class="d-flex justify-content-between align-items-center mb-3">
          <h5 class="fw-bold m-0"><i class="fas fa-filter text-primary me-2"></i> Bộ Lọc</h5>
          <a href="products.php" class="btn btn-sm btn-outline-secondary">Xóa bộ lọc</a>
        </div>

        <!-- Filter Form -->
        <form action="products.php" method="GET">
          <?php if (!empty($search)): ?>
            <input type="hidden" name="search" value="<?php echo htmlspecialchars($search); ?>">
          <?php endif; ?>

          <!-- Categories Filter -->
          <div class="mb-4">
            <h6 class="fw-bold text-secondary mb-2">Danh Mục</h6>
            <div class="d-flex flex-column gap-2">
              <?php
                $allCatQuery = http_build_query(array_filter([
                  'search' => $search,
                  'price' => $priceRange,
                  'sort' => $sort !== 'newest' ? $sort : ''
                ]));
                $allCatUrl = 'products.php' . ($allCatQuery ? '?' . $allCatQuery : '');
              ?>
              <a href="<?php echo $allCatUrl; ?>" class="text-decoration-none p-2 rounded <?php echo empty($catSlug) ? 'bg-primary-light text-primary fw-bold' : 'text-dark'; ?>">
                <i class="fas fa-layer-group me-2"></i> Tất cả danh mục
              </a>
              <?php foreach ($categories as $cat): 
                $catQuery = http_build_query(array_filter([
                  'category' => $cat['slug'],
                  'search' => $search,
                  'price' => $priceRange,
                  'sort' => $sort !== 'newest' ? $sort : ''
                ]));
              ?>
                <a href="products.php?<?php echo $catQuery; ?>" class="text-decoration-none p-2 rounded <?php echo ($catSlug === $cat['slug']) ? 'bg-primary-light text-primary fw-bold' : 'text-dark'; ?>">
                  <i class="fas <?php echo htmlspecialchars($cat['icon']); ?> me-2"></i> <?php echo htmlspecialchars($cat['name']); ?>
                </a>
              <?php endforeach; ?>
            </div>
          </div>

          <!-- Price Range Filter -->
          <div class="mb-4">
            <h6 class="fw-bold text-secondary mb-2">Khoảng Giá</h6>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="price" id="p_all" value="" <?php echo empty($priceRange) ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label class="form-check-label" for="p_all">Tất cả mức giá</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="price" id="p1" value="under1m" <?php echo ($priceRange === 'under1m') ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label class="form-check-label" for="p1">Dưới 1.000.000 ₫</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="price" id="p2" value="1m-3m" <?php echo ($priceRange === '1m-3m') ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label class="form-check-label" for="p2">1.000.000 ₫ - 3.000.000 ₫</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="price" id="p3" value="3m-6m" <?php echo ($priceRange === '3m-6m') ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label class="form-check-label" for="p3">3.000.000 ₫ - 6.000.000 ₫</label>
            </div>
            <div class="form-check mb-2">
              <input class="form-check-input" type="radio" name="price" id="p4" value="above6m" <?php echo ($priceRange === 'above6m') ? 'checked' : ''; ?> onchange="this.form.submit()">
              <label class="form-check-label" for="p4">Trên 6.000.000 ₫</label>
            </div>
          </div>

          <?php if (!empty($catSlug)): ?>
            <input type="hidden" name="category" value="<?php echo htmlspecialchars($catSlug); ?>">
          <?php endif; ?>
          <?php if (!empty($sort) && $sort !== 'newest'): ?>
            <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
          <?php endif; ?>
        </form>

        <!-- Guarantee Badge -->
        <div class="p-3 bg-light rounded-3 text-center border mt-4">
          <i class="fas fa-shield-check text-success fa-2x mb-2"></i>
          <div class="fw-bold small">Cam Kết DatCyber</div>
          <p class="text-muted small m-0">100% sản phẩm có tem chống hàng giả & bảo hành 2 năm.</p>
        </div>

      </div>
    </div>

    <!-- Product Grid & Top Bar -->
    <div class="col-lg-9">
      
      <!-- Top Control Bar -->
      <div class="bg-white p-3 rounded-4 border shadow-sm mb-4 d-flex flex-wrap justify-content-between align-items-center gap-3">
        <div>
          <h4 class="fw-bold m-0"><?php echo htmlspecialchars($currentCategoryName); ?></h4>
          <span class="text-muted small">Tìm thấy <strong><?php echo count($products); ?></strong> sản phẩm</span>
          <?php if (!empty($search)): ?>
            <span class="badge bg-light text-dark border ms-2">Từ khóa: "<?php echo htmlspecialchars($search); ?>"</span>
          <?php endif; ?>
        </div>

        <div class="d-flex align-items-center gap-2">
          <label for="sortSelect" class="text-secondary small fw-semibold text-nowrap">Sắp xếp theo:</label>
          <select id="sortSelect" class="form-select form-select-sm rounded-pill" style="width: 190px;" onchange="location = this.value;">
            <?php
              $baseUrl = 'products.php?';
              if (!empty($catSlug)) $baseUrl .= 'category=' . urlencode($catSlug) . '&';
              if (!empty($search)) $baseUrl .= 'search=' . urlencode($search) . '&';
              if (!empty($priceRange)) $baseUrl .= 'price=' . urlencode($priceRange) . '&';
            ?>
            <option value="<?php echo $baseUrl . 'sort=newest'; ?>" <?php echo ($sort === 'newest') ? 'selected' : ''; ?>>Mới nhất</option>
            <option value="<?php echo $baseUrl . 'sort=best_seller'; ?>" <?php echo ($sort === 'best_seller') ? 'selected' : ''; ?>>Bán chạy nhất</option>
            <option value="<?php echo $baseUrl . 'sort=price_asc'; ?>" <?php echo ($sort === 'price_asc') ? 'selected' : ''; ?>>Giá tăng dần</option>
            <option value="<?php echo $baseUrl . 'sort=price_desc'; ?>" <?php echo ($sort === 'price_desc') ? 'selected' : ''; ?>>Giá giảm dần</option>
            <option value="<?php echo $baseUrl . 'sort=rating'; ?>" <?php echo ($sort === 'rating') ? 'selected' : ''; ?>>Đánh giá cao nhất</option>
          </select>
        </div>
      </div>

      <!-- Products Listing -->
      <?php if (empty($products)): ?>
        <div class="bg-white p-5 rounded-4 border text-center shadow-sm">
          <i class="fas fa-box-open fa-4x text-muted mb-3"></i>
          <h5 class="fw-bold">Không tìm thấy sản phẩm phù hợp</h5>
          <p class="text-secondary">Vui lòng thử tìm kiếm với từ khóa khác hoặc điều chỉnh lại bộ lọc giá.</p>
          <a href="products.php" class="btn btn-primary-custom">Xem toàn bộ sản phẩm</a>
        </div>
      <?php else: ?>
        <div class="product-grid">
          <?php foreach ($products as $item): ?>
            <div class="product-card">
              <div class="product-badge-group">
                <?php if ($item['discount_percent'] > 0): ?>
                  <span class="badge-discount">-<?php echo $item['discount_percent']; ?>%</span>
                <?php endif; ?>
                <?php if ($item['is_best_seller']): ?>
                  <span class="badge-hot">Bán chạy</span>
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
      <?php endif; ?>

    </div>

  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
