<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - PRODUCT & SERVICE CATALOG
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/includes/config.php';

// Xử lý các tham số tìm kiếm, lọc và phân trang
$search = isset($_GET['search']) ? trim($_GET['search']) : '';
$category_slug = isset($_GET['category']) ? trim($_GET['category']) : '';
$min_price = isset($_GET['min_price']) && is_numeric($_GET['min_price']) ? (float)$_GET['min_price'] : 0;
$max_price = isset($_GET['max_price']) && is_numeric($_GET['max_price']) ? (float)$_GET['max_price'] : 0;
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'default';
$page = isset($_GET['page']) && is_numeric($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Lấy danh mục để hiển thị sidebar
$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();

// Xây dựng câu truy vấn SQL động
$where_clauses = ["1=1"];
$params = [];

if ($search !== '') {
    $where_clauses[] = "(p.name LIKE ? OR p.sku LIKE ? OR p.short_description LIKE ?)";
    $search_param = "%{$search}%";
    $params[] = $search_param;
    $params[] = $search_param;
    $params[] = $search_param;
}

if ($category_slug !== '') {
    $where_clauses[] = "c.slug = ?";
    $params[] = $category_slug;
}

if ($min_price > 0) {
    $where_clauses[] = "p.price >= ?";
    $params[] = $min_price;
}

if ($max_price > 0) {
    $where_clauses[] = "p.price <= ?";
    $params[] = $max_price;
}

// Sắp xếp
$order_by = "p.id ASC";
if ($sort === 'price_asc') {
    $order_by = "p.price ASC";
} elseif ($sort === 'price_desc') {
    $order_by = "p.price DESC";
} elseif ($sort === 'rating_desc') {
    $order_by = "p.rating DESC, p.review_count DESC";
} elseif ($sort === 'newest') {
    $order_by = "p.id DESC";
}

$where_sql = implode(" AND ", $where_clauses);

// Đếm tổng số sản phẩm thỏa mãn điều kiện
$count_stmt = $pdo->prepare("
    SELECT COUNT(*) 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE {$where_sql}
");
$count_stmt->execute($params);
$total_products = $count_stmt->fetchColumn();
$total_pages = ceil($total_products / $limit);

// Lấy danh sách sản phẩm theo trang
$sql = "
    SELECT p.*, c.name AS category_name, c.slug AS category_slug 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    WHERE {$where_sql} 
    ORDER BY {$order_by} 
    LIMIT {$limit} OFFSET {$offset}
";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$page_title = "Cửa Hàng Thể Hình & Gói Tập VIP | " . SITE_NAME;
$page_desc = "Khám phá 30 sản phẩm và dịch vụ thể hình cao cấp chuẩn CEO: Gói hội viên VIP, máy tập thương mại, whey isolate, phụ kiện thể hình chính hãng.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <!-- Breadcrumb & Header Title -->
    <div style="margin-bottom: 2.5rem;" class="reveal">
        <div style="font-size: 0.85rem; color: var(--gold-primary); margin-bottom: 0.5rem;">
            <a href="<?= BASE_URL ?>/index.php">Trang Chủ</a> / <span>Cửa Hàng Thể Hình</span>
            <?php if ($category_slug): ?>
                / <strong style="color: #fff;"><?= htmlspecialchars($category_slug) ?></strong>
            <?php endif; ?>
        </div>
        <h1 style="font-size: 2.6rem; font-weight: 800;">CỬA HÀNG THỂ HÌNH & GÓI TẬP CEO</h1>
        <p style="color: var(--text-secondary); font-size: 1.05rem;">
            Tuyển tập 30 sản phẩm & giải pháp rèn luyện thể chất chuẩn 5 sao được bảo hành và kiểm định chất lượng nghiêm ngặt.
        </p>
    </div>

    <div class="catalog-layout">
        <!-- Filter Sidebar -->
        <aside class="filter-sidebar reveal">
            <!-- Search Widget -->
            <div class="filter-widget">
                <h3 class="filter-widget-title"><span><i class="fas fa-search"></i> Tìm Kiếm</span></h3>
                <form action="<?= BASE_URL ?>/products.php" method="GET">
                    <input type="text" name="search" class="form-control" placeholder="Từ khóa..." value="<?= htmlspecialchars($search) ?>" style="padding: 0.6rem 0.85rem; font-size: 0.9rem;">
                    <?php if ($category_slug): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category_slug) ?>"><?php endif; ?>
                    <button type="submit" class="btn btn-primary btn-sm btn-block" style="margin-top: 0.6rem;">Tìm</button>
                </form>
            </div>

            <!-- Categories Widget -->
            <div class="filter-widget">
                <h3 class="filter-widget-title"><span><i class="fas fa-list"></i> Danh Mục</span></h3>
                <ul class="filter-category-list">
                    <li class="filter-category-item <?= empty($category_slug) ? 'active' : '' ?>">
                        <a href="<?= BASE_URL ?>/products.php<?= !empty($search) ? '?search='.urlencode($search) : '' ?>">
                            <span>Tất Cả Danh Mục</span>
                            <span>(30)</span>
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): 
                        $c_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                        $c_stmt->execute([$cat['id']]);
                        $cnt = $c_stmt->fetchColumn();
                        $is_active = ($category_slug === $cat['slug']);
                    ?>
                    <li class="filter-category-item <?= $is_active ? 'active' : '' ?>">
                        <a href="<?= BASE_URL ?>/products.php?category=<?= $cat['slug'] ?><?= !empty($search) ? '&search='.urlencode($search) : '' ?>">
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                            <span>(<?= $cnt ?>)</span>
                        </a>
                    </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Price Filter Widget -->
            <div class="filter-widget">
                <h3 class="filter-widget-title"><span><i class="fas fa-filter"></i> Lọc Theo Giá</span></h3>
                <ul class="filter-category-list">
                    <li class="filter-category-item">
                        <a href="<?= BASE_URL ?>/products.php?max_price=1000000<?= !empty($category_slug) ? '&category='.$category_slug : '' ?>">Dưới 1.000.000 ₫</a>
                    </li>
                    <li class="filter-category-item">
                        <a href="<?= BASE_URL ?>/products.php?min_price=1000000&max_price=5000000<?= !empty($category_slug) ? '&category='.$category_slug : '' ?>">1.000.000 ₫ - 5.000.000 ₫</a>
                    </li>
                    <li class="filter-category-item">
                        <a href="<?= BASE_URL ?>/products.php?min_price=5000000&max_price=20000000<?= !empty($category_slug) ? '&category='.$category_slug : '' ?>">5.000.000 ₫ - 20.000.000 ₫</a>
                    </li>
                    <li class="filter-category-item">
                        <a href="<?= BASE_URL ?>/products.php?min_price=20000000<?= !empty($category_slug) ? '&category='.$category_slug : '' ?>">Trên 20.000.000 ₫</a>
                    </li>
                </ul>
            </div>

            <!-- Clear Filter Button -->
            <div style="margin-top: 1.5rem;">
                <a href="<?= BASE_URL ?>/products.php" class="btn btn-secondary btn-sm btn-block">
                    <i class="fas fa-undo"></i> Xóa Bộ Lọc
                </a>
            </div>
        </aside>

        <!-- Product Main Grid -->
        <main>
            <!-- Top Controls Bar -->
            <div class="catalog-header-bar reveal">
                <div class="catalog-results-count">
                    Hiển thị <strong><?= count($products) ?></strong> / <strong><?= $total_products ?></strong> sản phẩm
                    <?php if ($search): ?> cho từ khóa "<em><?= htmlspecialchars($search) ?></em>"<?php endif; ?>
                </div>

                <!-- Sort Control -->
                <form action="<?= BASE_URL ?>/products.php" method="GET" style="display: flex; align-items: center; gap: 0.5rem;">
                    <?php if ($search): ?><input type="hidden" name="search" value="<?= htmlspecialchars($search) ?>"><?php endif; ?>
                    <?php if ($category_slug): ?><input type="hidden" name="category" value="<?= htmlspecialchars($category_slug) ?>"><?php endif; ?>
                    <?php if ($min_price > 0): ?><input type="hidden" name="min_price" value="<?= $min_price ?>"><?php endif; ?>
                    <?php if ($max_price > 0): ?><input type="hidden" name="max_price" value="<?= $max_price ?>"><?php endif; ?>
                    
                    <label style="font-size: 0.85rem; color: var(--text-secondary);">Sắp xếp:</label>
                    <select name="sort" class="catalog-sort-select" onchange="this.form.submit()">
                        <option value="default" <?= $sort == 'default' ? 'selected' : '' ?>>Mặc định</option>
                        <option value="price_asc" <?= $sort == 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= $sort == 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                        <option value="rating_desc" <?= $sort == 'rating_desc' ? 'selected' : '' ?>>Đánh giá cao nhất</option>
                        <option value="newest" <?= $sort == 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                    </select>
                </form>
            </div>

            <!-- Products List -->
            <?php if (empty($products)): ?>
                <div class="category-card" style="padding: 4rem 2rem; text-align: center;">
                    <i class="fas fa-box-open" style="font-size: 3.5rem; color: var(--text-muted); margin-bottom: 1rem;"></i>
                    <h3 style="color: #fff; font-size: 1.4rem; margin-bottom: 0.5rem;">Không tìm thấy sản phẩm phù hợp</h3>
                    <p style="color: var(--text-secondary); margin-bottom: 1.5rem;">Vui lòng thử tìm kiếm bằng từ khóa khác hoặc điều chỉnh lại bộ lọc giá.</p>
                    <a href="<?= BASE_URL ?>/products.php" class="btn btn-primary btn-sm">Xem Toàn Bộ 30 Sản Phẩm</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $p): 
                        $discount = get_discount_percent($p['price'], $p['original_price']);
                    ?>
                    <div class="product-card reveal">
                        <div class="product-thumb-wrap">
                            <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                            <?php if ($p['badge']): ?>
                            <span class="badge badge-gold product-badge-tag"><?= htmlspecialchars($p['badge']) ?></span>
                            <?php endif; ?>

                            <?php if ($discount > 0): ?>
                            <span class="product-discount-tag">-<?= $discount ?>%</span>
                            <?php endif; ?>

                            <div class="product-quick-actions">
                                <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $p['id'] ?>" class="action-btn-circle" title="Xem chi tiết">
                                    <i class="fas fa-eye"></i>
                                </a>
                                <button type="button" class="action-btn-circle add-cart-btn-direct" data-id="<?= $p['id'] ?>" title="Thêm vào giỏ">
                                    <i class="fas fa-shopping-cart"></i>
                                </button>
                            </div>
                        </div>

                        <div class="product-body">
                            <span class="product-category-meta"><?= htmlspecialchars($p['category_name']) ?></span>
                            <h4 class="product-title">
                                <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a>
                            </h4>
                            
                            <div class="product-rating">
                                <?php 
                                $r = floor($p['rating']);
                                for ($i = 0; $i < 5; $i++): 
                                    if ($i < $r) echo '<i class="fas fa-star"></i>';
                                    else echo '<i class="far fa-star"></i>';
                                endfor; ?>
                                <span class="review-count">(<?= $p['review_count'] ?>)</span>
                            </div>

                            <div class="product-price-row">
                                <div class="product-price-wrap">
                                    <span class="product-price-current"><?= format_currency($p['price']) ?></span>
                                    <?php if ($p['original_price'] && $p['original_price'] > $p['price']): ?>
                                    <span class="product-price-original"><?= format_currency($p['original_price']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <button type="button" class="add-cart-btn-direct" data-id="<?= $p['id'] ?>">
                                    <i class="fas fa-cart-plus"></i> Chọn
                                </button>
                            </div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>

                <!-- Pagination -->
                <?php if ($total_pages > 1): ?>
                <div class="pagination reveal">
                    <?php for ($i = 1; $i <= $total_pages; $i++): 
                        $query_params = $_GET;
                        $query_params['page'] = $i;
                        $page_url = '?' . http_build_query($query_params);
                    ?>
                    <a href="<?= $page_url ?>" class="page-link <?= $page == $i ? 'active' : '' ?>"><?= $i ?></a>
                    <?php endfor; ?>
                </div>
                <?php endif; ?>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
