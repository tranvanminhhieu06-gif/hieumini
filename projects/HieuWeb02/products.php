<?php
$page_title = 'Danh Sách Sản Phẩm Công Nghệ';
require_once __DIR__ . '/includes/header.php';

// Nhận các tham số lọc từ GET
$category_id = isset($_GET['cat']) ? (int)$_GET['cat'] : 0;
$brand = isset($_GET['brand']) ? trim($_GET['brand']) : '';
$price_range = isset($_GET['price_range']) ? trim($_GET['price_range']) : '';
$sort = isset($_GET['sort']) ? trim($_GET['sort']) : 'newest';
$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

$products = [];
$total_products = 0;

if ($pdo) {
    try {
        $sql = "SELECT p.*, c.name as category_name FROM products p LEFT JOIN categories c ON p.category_id = c.id WHERE 1=1";
        $params = [];

        if ($category_id > 0) {
            $sql .= " AND p.category_id = ?";
            $params[] = $category_id;
        }

        if (!empty($brand)) {
            $sql .= " AND p.brand = ?";
            $params[] = $brand;
        }

        if (!empty($keyword)) {
            $sql .= " AND (p.name LIKE ? OR p.brand LIKE ? OR p.short_desc LIKE ?)";
            $searchTerm = "%$keyword%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }

        if ($price_range === 'under_5m') {
            $sql .= " AND COALESCE(p.sale_price, p.price) < 5000000";
        } elseif ($price_range === '5m_15m') {
            $sql .= " AND COALESCE(p.sale_price, p.price) BETWEEN 5000000 AND 15000000";
        } elseif ($price_range === '15m_30m') {
            $sql .= " AND COALESCE(p.sale_price, p.price) BETWEEN 15000000 AND 30000000";
        } elseif ($price_range === 'above_30m') {
            $sql .= " AND COALESCE(p.sale_price, p.price) > 30000000";
        }

        // Sắp xếp
        if ($sort === 'price_asc') {
            $sql .= " ORDER BY COALESCE(p.sale_price, p.price) ASC";
        } elseif ($sort === 'price_desc') {
            $sql .= " ORDER BY COALESCE(p.sale_price, p.price) DESC";
        } elseif ($sort === 'rating') {
            $sql .= " ORDER BY p.rating DESC";
        } else {
            $sql .= " ORDER BY p.id DESC";
        }

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $products = $stmt->fetchAll();
        $total_products = count($products);
    } catch (Exception $e) {}
}

// Fallback nếu không có CSDL
if (empty($products) && empty($keyword) && $category_id == 0) {
    $products = [
        ['id' => 1, 'category_id' => 1, 'name' => 'iPhone 16 Pro Max 256GB Titan Sa Mạc', 'slug' => 'iphone-16-pro-max-256gb', 'brand' => 'Apple', 'price' => 34990000, 'sale_price' => 31990000, 'rating' => 5.0, 'icon' => 'fa-mobile-screen'],
        ['id' => 2, 'category_id' => 1, 'name' => 'Samsung Galaxy S24 Ultra 5G 12GB/256GB', 'slug' => 'samsung-galaxy-s24-ultra', 'brand' => 'Samsung', 'price' => 31990000, 'sale_price' => 26990000, 'rating' => 4.9, 'icon' => 'fa-mobile'],
        ['id' => 3, 'category_id' => 2, 'name' => 'MacBook Pro 14 M3 Pro (18GB/512GB SSD)', 'slug' => 'macbook-pro-14-m3-pro', 'brand' => 'Apple', 'price' => 49990000, 'sale_price' => 45490000, 'rating' => 5.0, 'icon' => 'fa-laptop-code'],
        ['id' => 4, 'category_id' => 2, 'name' => 'Laptop Gaming ASUS ROG Zephyrus G16 OLED', 'slug' => 'asus-rog-zephyrus-g16-oled', 'brand' => 'ASUS', 'price' => 54990000, 'sale_price' => 49990000, 'rating' => 4.8, 'icon' => 'fa-laptop'],
        ['id' => 5, 'category_id' => 3, 'name' => 'iPad Pro 11 inch M4 Wi-Fi 256GB Ultra Thin', 'slug' => 'ipad-pro-11-m4-256gb', 'brand' => 'Apple', 'price' => 28990000, 'sale_price' => 26990000, 'rating' => 4.9, 'icon' => 'fa-tablet-screen-button'],
        ['id' => 6, 'category_id' => 4, 'name' => 'Apple Watch Ultra 2 GPS + Cellular 49mm Titanium', 'slug' => 'apple-watch-ultra-2-49mm', 'brand' => 'Apple', 'price' => 21990000, 'sale_price' => 19490000, 'rating' => 5.0, 'icon' => 'fa-clock'],
        ['id' => 7, 'category_id' => 5, 'name' => 'Tai nghe Sony WH-1000XM5 Chống Ồn Cao Cấp', 'slug' => 'tai-nghe-sony-wh-1000xm5', 'brand' => 'Sony', 'price' => 8490000, 'sale_price' => 6990000, 'rating' => 4.9, 'icon' => 'fa-headphones'],
        ['id' => 8, 'category_id' => 5, 'name' => 'Loa Bluetooth Marshall Stanmore III Chính Hãng', 'slug' => 'loa-marshall-stanmore-iii', 'brand' => 'Marshall', 'price' => 10490000, 'sale_price' => 8990000, 'rating' => 4.7, 'icon' => 'fa-volume-high'],
        ['id' => 9, 'category_id' => 6, 'name' => 'Bàn phím cơ NuPhy Air75 V2 Low-Profile Wireless', 'slug' => 'ban-phim-co-nuphy-air75-v2', 'brand' => 'NuPhy', 'price' => 3200000, 'sale_price' => 2750000, 'rating' => 4.8, 'icon' => 'fa-keyboard'],
        ['id' => 10, 'category_id' => 6, 'name' => 'Củ sạc nhanh GaN Anker Prime 100W 3 cổng', 'slug' => 'cu-sac-anker-prime-100w', 'brand' => 'Anker', 'price' => 1900000, 'sale_price' => 1490000, 'rating' => 4.9, 'icon' => 'fa-plug']
    ];
    $total_products = count($products);
}

$brands_list = ['Apple', 'Samsung', 'ASUS', 'Sony', 'Marshall', 'NuPhy', 'Anker'];
?>

<main class="container" style="margin: 30px auto 60px;">
    <!-- Breadcrumb -->
    <div style="display: flex; align-items: center; gap: 8px; font-size: 0.9rem; color: var(--text-muted); margin-bottom: 24px;">
        <a href="index.php"><i class="fa-solid fa-house"></i> Trang chủ</a>
        <span>/</span>
        <span style="color: #fff;">Sản phẩm</span>
        <?php if (!empty($keyword)): ?>
            <span>/</span>
            <span style="color: var(--accent);">Tìm kiếm: "<?php echo htmlspecialchars($keyword); ?>"</span>
        <?php endif; ?>
    </div>

    <div style="display: grid; grid-template-columns: 280px 1fr; gap: 30px; align-items: start;">
        
        <!-- Sidebar Bộ Lọc -->
        <aside class="glass-panel" style="padding: 24px; position: sticky; top: 120px;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px; border-bottom: var(--border-glass); padding-bottom: 12px;">
                <h3 style="font-size: 1.1rem; font-weight: 700;"><i class="fa-solid fa-filter" style="color: var(--primary);"></i> Bộ Lọc</h3>
                <a href="products.php" style="font-size: 0.8rem; color: var(--danger);">Xóa lọc</a>
            </div>

            <form action="products.php" method="GET" id="filter-form">
                <?php if (!empty($keyword)): ?>
                    <input type="hidden" name="keyword" value="<?php echo htmlspecialchars($keyword); ?>">
                <?php endif; ?>

                <!-- Danh mục -->
                <div style="margin-bottom: 24px;">
                    <h4 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 12px; color: #cbd5e1;">DANH MỤC</h4>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: var(--text-muted);">
                            <input type="radio" name="cat" value="0" <?php echo $category_id == 0 ? 'checked' : ''; ?> onchange="this.form.submit()">
                            Tất cả danh mục
                        </label>
                        <?php foreach ($categories as $c): ?>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: <?php echo $category_id == $c['id'] ? '#fff; font-weight: 700;' : 'var(--text-muted);'; ?>">
                                <input type="radio" name="cat" value="<?php echo $c['id']; ?>" <?php echo $category_id == $c['id'] ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <?php echo htmlspecialchars($c['name']); ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Thương hiệu -->
                <div style="margin-bottom: 24px;">
                    <h4 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 12px; color: #cbd5e1;">THƯƠNG HIỆU</h4>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: var(--text-muted);">
                            <input type="radio" name="brand" value="" <?php echo empty($brand) ? 'checked' : ''; ?> onchange="this.form.submit()">
                            Tất cả thương hiệu
                        </label>
                        <?php foreach ($brands_list as $b): ?>
                            <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: <?php echo $brand === $b ? '#fff; font-weight: 700;' : 'var(--text-muted);'; ?>">
                                <input type="radio" name="brand" value="<?php echo $b; ?>" <?php echo $brand === $b ? 'checked' : ''; ?> onchange="this.form.submit()">
                                <?php echo $b; ?>
                            </label>
                        <?php endforeach; ?>
                    </div>
                </div>

                <!-- Khoảng giá -->
                <div style="margin-bottom: 24px;">
                    <h4 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 12px; color: #cbd5e1;">MỨC GIÁ</h4>
                    <div style="display: flex; flex-direction: column; gap: 8px;">
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: var(--text-muted);">
                            <input type="radio" name="price_range" value="" <?php echo empty($price_range) ? 'checked' : ''; ?> onchange="this.form.submit()">
                            Tất cả mức giá
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: var(--text-muted);">
                            <input type="radio" name="price_range" value="under_5m" <?php echo $price_range === 'under_5m' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            Dưới 5 Triệu
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: var(--text-muted);">
                            <input type="radio" name="price_range" value="5m_15m" <?php echo $price_range === '5m_15m' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            Từ 5 - 15 Triệu
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: var(--text-muted);">
                            <input type="radio" name="price_range" value="15m_30m" <?php echo $price_range === '15m_30m' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            Từ 15 - 30 Triệu
                        </label>
                        <label style="display: flex; align-items: center; gap: 8px; font-size: 0.88rem; cursor: pointer; color: var(--text-muted);">
                            <input type="radio" name="price_range" value="above_30m" <?php echo $price_range === 'above_30m' ? 'checked' : ''; ?> onchange="this.form.submit()">
                            Trên 30 Triệu
                        </label>
                    </div>
                </div>

                <input type="hidden" name="sort" value="<?php echo htmlspecialchars($sort); ?>">
            </form>
        </aside>

        <!-- Main Product Results -->
        <div>
            <!-- Header bar kết quả & sắp xếp -->
            <div class="glass-panel" style="padding: 16px 20px; display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 14px;">
                <div>
                    <span style="color: var(--text-muted); font-size: 0.9rem;">Tìm thấy: <strong style="color: #fff;"><?php echo $total_products; ?></strong> sản phẩm</span>
                </div>

                <div style="display: flex; align-items: center; gap: 10px;">
                    <label for="sort-select" style="font-size: 0.88rem; color: var(--text-muted);">Sắp xếp:</label>
                    <select id="sort-select" class="form-control" style="width: auto; padding: 6px 12px; font-size: 0.88rem;" onchange="
                        const form = document.getElementById('filter-form');
                        form.elements['sort'].value = this.value;
                        form.submit();
                    ">
                        <option value="newest" <?php echo $sort === 'newest' ? 'selected' : ''; ?>>Mới nhất</option>
                        <option value="price_asc" <?php echo $sort === 'price_asc' ? 'selected' : ''; ?>>Giá tăng dần</option>
                        <option value="price_desc" <?php echo $sort === 'price_desc' ? 'selected' : ''; ?>>Giá giảm dần</option>
                        <option value="rating" <?php echo $sort === 'rating' ? 'selected' : ''; ?>>Đánh giá cao</option>
                    </select>
                </div>
            </div>

            <!-- Grid Sản Phẩm -->
            <?php if (!empty($products)): ?>
                <div class="product-grid">
                    <?php foreach ($products as $p): 
                        $discount = calculate_discount($p['price'], $p['sale_price']);
                        $thumb = !empty($p['thumbnail']) && file_exists(__DIR__ . '/assets/images/' . $p['thumbnail']) ? 'assets/images/' . $p['thumbnail'] : 'assets/images/default_prod.png';
                    ?>
                        <div class="product-card">
                            <?php if ($discount > 0): ?>
                                <span class="card-badge-discount">-<?php echo $discount; ?>%</span>
                            <?php endif; ?>

                            <div class="product-img-wrap">
                                <a href="product_detail.php?id=<?php echo $p['id']; ?>" style="display: flex; align-items: center; justify-content: center; width: 100%; height: 100%;">
                                    <img src="<?php echo $thumb; ?>" alt="<?php echo htmlspecialchars($p['name']); ?>" class="product-thumb-img">
                                </a>
                            </div>

                            <span class="product-brand"><?php echo htmlspecialchars($p['brand']); ?></span>
                            <h3 class="product-name">
                                <a href="product_detail.php?id=<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name']); ?></a>
                            </h3>

                            <?php echo render_rating_stars($p['rating']); ?>

                            <div class="product-price-wrap">
                                <div>
                                    <span class="price-current"><?php echo format_currency(!empty($p['sale_price']) ? $p['sale_price'] : $p['price']); ?></span>
                                    <?php if (!empty($p['sale_price'])): ?>
                                        <span class="price-old"><?php echo format_currency($p['price']); ?></span>
                                    <?php endif; ?>
                                </div>
                                <div class="card-actions">
                                    <a href="product_detail.php?id=<?php echo $p['id']; ?>" class="btn btn-outline btn-sm" style="flex: 1;">Chi tiết</a>
                                    <button type="button" class="btn-add-cart ajax-add-cart" data-id="<?php echo $p['id']; ?>" title="Thêm vào giỏ">
                                        <i class="fa-solid fa-cart-plus"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="glass-panel" style="text-align: center; padding: 60px 20px;">
                    <i class="fa-solid fa-box-open" style="font-size: 3.5rem; color: var(--text-muted); margin-bottom: 16px;"></i>
                    <h3 style="font-size: 1.3rem; margin-bottom: 8px;">Không tìm thấy sản phẩm phù hợp</h3>
                    <p style="color: var(--text-muted); margin-bottom: 24px;">Hãy thử điều chỉnh bộ lọc hoặc tìm kiếm với từ khóa khác.</p>
                    <a href="products.php" class="btn btn-primary">Xem tất cả sản phẩm</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
