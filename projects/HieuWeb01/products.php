<?php
/**
 * Trang Danh Sách Sản Phẩm & Bộ Lọc Thời Trang HieuMini
 */
$pageTitle = "Danh Sách Sản Phẩm";
require_once __DIR__ . '/includes/header.php';

// Xử lý bộ lọc
$catSlug = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';
$priceRange = isset($_GET['price']) ? trim($_GET['price']) : '';
$selectedSize = isset($_GET['size']) ? trim($_GET['size']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';

// Lấy danh sách tất cả danh mục kèm số lượng sản phẩm
$cats = $pdo->query("SELECT c.*, COUNT(p.id) as product_count FROM categories c LEFT JOIN products p ON c.id = p.category_id AND p.status = 1 WHERE c.status = 1 GROUP BY c.id ORDER BY c.id ASC")->fetchAll();

// Xây dựng câu query lọc
$query = "SELECT p.*, c.name as category_name, c.slug as category_slug FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 1";
$params = [];

if ($catSlug) {
    $query .= " AND c.slug = ?";
    $params[] = $catSlug;
}

if ($keyword) {
    $query .= " AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ?)";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
    $params[] = "%$keyword%";
}

if ($selectedSize) {
    $query .= " AND p.sizes LIKE ?";
    $params[] = "%$selectedSize%";
}

if ($priceRange) {
    switch ($priceRange) {
        case 'under_200':
            $query .= " AND COALESCE(p.discount_price, p.price) < 200000";
            break;
        case '200_400':
            $query .= " AND COALESCE(p.discount_price, p.price) BETWEEN 200000 AND 400000";
            break;
        case '400_600':
            $query .= " AND COALESCE(p.discount_price, p.price) BETWEEN 400000 AND 600000";
            break;
        case 'above_600':
            $query .= " AND COALESCE(p.discount_price, p.price) > 600000";
            break;
    }
}

// Sắp xếp
switch ($sort) {
    case 'price_asc':
        $query .= " ORDER BY COALESCE(p.discount_price, p.price) ASC";
        break;
    case 'price_desc':
        $query .= " ORDER BY COALESCE(p.discount_price, p.price) DESC";
        break;
    case 'popular':
        $query .= " ORDER BY p.view_count DESC";
        break;
    case 'newest':
    default:
        $query .= " ORDER BY p.id DESC";
        break;
}

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$products = $stmt->fetchAll();

// Tên danh mục hiện tại
$currentCatName = "Tất Cả Sản Phẩm Thời Trang";
if ($catSlug) {
    foreach ($cats as $c) {
        if ($c['slug'] === $catSlug) {
            $currentCatName = $c['name'];
            break;
        }
    }
}
?>

<!-- Header Banner -->
<div class="page-header-banner">
    <div class="container">
        <h1><?= htmlspecialchars($currentCatName) ?></h1>
        <div class="breadcrumbs">
            <a href="index.php">Trang Chủ</a> / <span><?= htmlspecialchars($currentCatName) ?></span>
            <?php if ($keyword): ?>
                / <span>Tìm kiếm: "<?= htmlspecialchars($keyword) ?>"</span>
            <?php endif; ?>
        </div>
    </div>
</div>

<div class="container">
    <div class="shop-layout">
        <!-- Sidebar Bộ Lọc -->
        <aside class="shop-sidebar">
            <!-- Filter Category -->
            <div class="filter-group">
                <h4 class="filter-title"><i class="fa-solid fa-layer-group"></i> Danh Mục</h4>
                <ul class="filter-list">
                    <li>
                        <a href="products.php" class="<?= empty($catSlug) ? 'active' : '' ?>">
                            <span>Tất cả sản phẩm</span>
                            <span>(<?= array_sum(array_column($cats, 'product_count')) ?>)</span>
                        </a>
                    </li>
                    <?php foreach ($cats as $c): ?>
                        <li>
                            <a href="products.php?cat=<?= $c['slug'] ?>" class="<?= $catSlug === $c['slug'] ? 'active' : '' ?>">
                                <span><?= htmlspecialchars($c['name']) ?></span>
                                <span>(<?= $c['product_count'] ?>)</span>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>

            <!-- Filter Price -->
            <div class="filter-group">
                <h4 class="filter-title"><i class="fa-solid fa-tag"></i> Mức Giá</h4>
                <ul class="filter-list">
                    <li><a href="products.php?<?= http_build_query(array_merge($_GET, ['price' => 'under_200'])) ?>" class="<?= $priceRange === 'under_200' ? 'active' : '' ?>">Dưới 200.000₫</a></li>
                    <li><a href="products.php?<?= http_build_query(array_merge($_GET, ['price' => '200_400'])) ?>" class="<?= $priceRange === '200_400' ? 'active' : '' ?>">200.000₫ - 400.000₫</a></li>
                    <li><a href="products.php?<?= http_build_query(array_merge($_GET, ['price' => '400_600'])) ?>" class="<?= $priceRange === '400_600' ? 'active' : '' ?>">400.000₫ - 600.000₫</a></li>
                    <li><a href="products.php?<?= http_build_query(array_merge($_GET, ['price' => 'above_600'])) ?>" class="<?= $priceRange === 'above_600' ? 'active' : '' ?>">Trên 600.000₫</a></li>
                </ul>
            </div>

            <!-- Filter Size -->
            <div class="filter-group">
                <h4 class="filter-title"><i class="fa-solid fa-shirt"></i> Kích Cỡ (Size)</h4>
                <div class="size-pill-group">
                    <?php $sizesList = ['S', 'M', 'L', 'XL', 'XXL', '29', '30', '31', '32']; ?>
                    <?php foreach ($sizesList as $sz): ?>
                        <a href="products.php?<?= http_build_query(array_merge($_GET, ['size' => ($selectedSize === $sz ? '' : $sz)])) ?>" class="size-pill <?= $selectedSize === $sz ? 'active' : '' ?>">
                            <?= $sz ?>
                        </a>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Clear Filter Button -->
            <?php if ($catSlug || $keyword || $priceRange || $selectedSize): ?>
                <div style="margin-top: 20px;">
                    <a href="products.php" class="btn btn-outline btn-sm btn-block">
                        <i class="fa-solid fa-xmark"></i> Xóa Bộ Lọc
                    </a>
                </div>
            <?php endif; ?>
        </aside>

        <!-- Main Product Grid & Toolbar -->
        <main>
            <!-- Top Toolbar -->
            <div class="shop-top-toolbar">
                <div style="font-size: 0.9rem; color: var(--text-muted);">
                    Hiển thị <strong><?= count($products) ?></strong> sản phẩm
                </div>

                <form method="GET" style="display: flex; align-items: center; gap: 8px;">
                    <?php if ($catSlug): ?><input type="hidden" name="cat" value="<?= htmlspecialchars($catSlug) ?>"><?php endif; ?>
                    <?php if ($keyword): ?><input type="hidden" name="keyword" value="<?= htmlspecialchars($keyword) ?>"><?php endif; ?>
                    <?php if ($priceRange): ?><input type="hidden" name="price" value="<?= htmlspecialchars($priceRange) ?>"><?php endif; ?>
                    <?php if ($selectedSize): ?><input type="hidden" name="size" value="<?= htmlspecialchars($selectedSize) ?>"><?php endif; ?>
                    
                    <label style="font-size: 0.85rem; color: var(--secondary); font-weight: 600;">Sắp xếp:</label>
                    <select name="sort" class="sort-select" onchange="this.form.submit()">
                        <option value="newest" <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
                        <option value="popular" <?= $sort === 'popular' ? 'selected' : '' ?>>Xem nhiều nhất</option>
                        <option value="price_asc" <?= $sort === 'price_asc' ? 'selected' : '' ?>>Giá tăng dần</option>
                        <option value="price_desc" <?= $sort === 'price_desc' ? 'selected' : '' ?>>Giá giảm dần</option>
                    </select>
                </form>
            </div>

            <!-- Products Grid -->
            <?php if (empty($products)): ?>
                <div style="background: #fff; border-radius: var(--radius-lg); padding: 60px; text-align: center; border: 1px solid var(--border);">
                    <i class="fa-solid fa-box-open" style="font-size: 3.5rem; color: #cbd5e1; margin-bottom: 16px;"></i>
                    <h3 style="margin-bottom: 8px;">Không tìm thấy sản phẩm phù hợp!</h3>
                    <p style="color: var(--text-muted); margin-bottom: 20px;">Vui lòng thử lại với các tiêu chí lọc hoặc từ khóa tìm kiếm khác.</p>
                    <a href="products.php" class="btn btn-primary">Xem Tất Cả Sản Phẩm</a>
                </div>
            <?php else: ?>
                <div class="products-grid">
                    <?php foreach ($products as $p): ?>
                        <?php 
                            $discountPct = $p['discount_price'] ? round((($p['price'] - $p['discount_price']) / $p['price']) * 100) : 0;
                        ?>
                        <div class="product-card">
                            <?php if ($discountPct > 0): ?>
                                <span class="badge-discount">-<?= $discountPct ?>%</span>
                            <?php endif; ?>
                            <?php if ($p['featured']): ?>
                                <span class="badge-featured">Hot</span>
                            <?php endif; ?>

                            <a href="product_detail.php?id=<?= $p['id'] ?>" class="prod-thumb">
                                <img src="assets/images/products/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>">
                            </a>
                            <div class="prod-body">
                                <span class="prod-category"><?= htmlspecialchars($p['category_name']) ?></span>
                                <a href="product_detail.php?id=<?= $p['id'] ?>" class="prod-name" title="<?= htmlspecialchars($p['name']) ?>">
                                    <?= htmlspecialchars($p['name']) ?>
                                </a>
                                <div class="prod-rating">
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <i class="fa-solid fa-star"></i>
                                    <span>(<?= $p['view_count'] ?> lượt xem)</span>
                                </div>
                                <div class="prod-price-row">
                                    <span class="current-price"><?= format_price($p['discount_price'] ?? $p['price']) ?></span>
                                    <?php if ($p['discount_price']): ?>
                                        <span class="old-price"><?= format_price($p['price']) ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="prod-actions">
                                    <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm btn-block">
                                        <i class="fa-solid fa-eye"></i> Chi Tiết
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
