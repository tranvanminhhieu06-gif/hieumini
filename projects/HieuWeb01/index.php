<?php
/**
 * Trang Chủ HieuMini Fashion Studio
 */
$pageTitle = "Trang Chủ - Thời Trang Cao Cấp";
require_once __DIR__ . '/includes/header.php';

// Lấy danh mục nổi bật
$categories = $pdo->query("SELECT * FROM categories WHERE status = 1 ORDER BY id ASC")->fetchAll();

// Lấy sản phẩm Flash Sale (Có giảm giá)
$flashSaleStmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 1 AND p.discount_price IS NOT NULL AND p.discount_price < p.price ORDER BY p.view_count DESC LIMIT 4");
$flashSales = $flashSaleStmt->fetchAll();

// Lấy sản phẩm nổi bật
$featuredStmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 1 AND p.featured = 1 ORDER BY p.id DESC LIMIT 8");
$featuredProds = $featuredStmt->fetchAll();

// Lấy sản phẩm mới nhất
$newestStmt = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.status = 1 ORDER BY p.id DESC LIMIT 8");
$newestProds = $newestStmt->fetchAll();
?>

<!-- 1. Hero Slider -->
<section class="hero-slider-section">
    <div class="hero-slider">
        <!-- Slide 1 -->
        <div class="hero-slide active" style="background-image: url('assets/images/banners/hero_banner_1.jpg');">
            <div class="container">
                <div class="hero-content">
                    <span class="hero-badge">NEW ARRIVALS 2026</span>
                    <h2 class="hero-title">Bộ Sưu Tập Hè Phóng Khoáng</h2>
                    <p class="hero-desc">Khám phá phong cách Streetwear đương đại với chất liệu 100% Organic Cotton thoáng mát vượt trội.</p>
                    <div class="hero-btns">
                        <a href="products.php" class="btn btn-accent btn-lg">Khám Phá Ngay <i class="fa-solid fa-arrow-right"></i></a>
                        <a href="products.php?cat=ao-thun-polo" class="btn btn-outline btn-lg" style="color: #fff; border-color: rgba(255,255,255,0.4);">Xem Áo Thun</a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 2 -->
        <div class="hero-slide" style="background-image: url('assets/images/banners/hero_banner_2.jpg');">
            <div class="container">
                <div class="hero-content">
                    <span class="hero-badge" style="background: #f59e0b;">FLASH SALE 50%</span>
                    <h2 class="hero-title">Đại Tiệc Sale Giữa Mùa</h2>
                    <p class="hero-desc">Hàng ngàn mẫu áo sơ mi, áo khoác bomber, quần jeans giảm giá cực sốc. Freeship đơn từ 300K.</p>
                    <div class="hero-btns">
                        <a href="products.php" class="btn btn-accent btn-lg">Săn Deal Ngay <i class="fa-solid fa-bolt"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Slide 3 -->
        <div class="hero-slide" style="background-image: url('assets/images/banners/hero_banner_3.jpg');">
            <div class="container">
                <div class="hero-content">
                    <span class="hero-badge" style="background: #10b981;">EXCLUSIVE FIT</span>
                    <h2 class="hero-title">Phong Cách Smart Casual</h2>
                    <p class="hero-desc">Định hình vẻ lịch lãm, tự tin cùng các thiết kế sơ mi Oxford và quần Kaki may đo chuẩn mực.</p>
                    <div class="hero-btns">
                        <a href="products.php?cat=ao-so-mi" class="btn btn-accent btn-lg">Xem Sơ Mi <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>
        </div>

        <!-- Nav Arrows -->
        <button class="slider-btn prev"><i class="fa-solid fa-chevron-left"></i></button>
        <button class="slider-btn next"><i class="fa-solid fa-chevron-right"></i></button>
    </div>
</section>

<div class="container">
    <!-- 2. Benefits Row -->
    <section class="benefits-grid">
        <div class="benefit-card">
            <div class="benefit-icon"><i class="fa-solid fa-truck-fast"></i></div>
            <div class="benefit-info">
                <h4>Giao Hàng Miễn Phí</h4>
                <p>Đơn hàng từ 300.000đ</p>
            </div>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon"><i class="fa-solid fa-arrow-rotate-left"></i></div>
            <div class="benefit-info">
                <h4>Đổi Trả 30 Ngày</h4>
                <p>Thủ tục dễ dàng, nhanh chóng</p>
            </div>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon"><i class="fa-solid fa-shield-check"></i></div>
            <div class="benefit-info">
                <h4>Chính Hãng 100%</h4>
                <p>Cam kết chất lượng vải cao cấp</p>
            </div>
        </div>
        <div class="benefit-card">
            <div class="benefit-icon"><i class="fa-solid fa-headset"></i></div>
            <div class="benefit-info">
                <h4>Hỗ Trợ 24/7</h4>
                <p>Hotline tư vấn chọn size tận tâm</p>
            </div>
        </div>
    </section>

    <!-- 3. Featured Categories -->
    <section style="margin-bottom: 50px;">
        <div class="section-header">
            <div class="section-subtitle">DANH MỤC THỜI TRANG</div>
            <h2 class="section-title">Khám Phá Phong Cách Của Bạn</h2>
        </div>
        <div class="categories-grid">
            <?php foreach ($categories as $c): ?>
                <a href="products.php?cat=<?= $c['slug'] ?>" class="category-card">
                    <div class="cat-img-wrapper">
                        <img src="assets/images/categories/<?= htmlspecialchars($c['image']) ?>" alt="<?= htmlspecialchars($c['name']) ?>">
                    </div>
                    <div class="cat-content">
                        <h4 class="cat-title"><?= htmlspecialchars($c['name']) ?></h4>
                        <span class="cat-count">Xem bộ sưu tập →</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 4. Flash Sale Section with Countdown -->
    <section class="flash-sale-box">
        <div class="flash-sale-title">
            <div style="font-size: 0.85rem; text-transform: uppercase; letter-spacing: 1.5px; color: var(--accent); font-weight: 700;">
                <i class="fa-solid fa-fire"></i> GIỜ VÀNG GIÁ SỐC
            </div>
            <h3>Flash Sale Hôm Nay</h3>
            <p style="color: #cbd5e1; font-size: 0.9rem;">Cơ hội săn ngay các item bán chạy nhất với mức giá cực kỳ ưu đãi!</p>
        </div>

        <div class="countdown-wrap" id="flash-countdown">
            <div class="countdown-item">
                <div class="countdown-num" id="cd-hours">18</div>
                <div class="countdown-lbl">Giờ</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-num" id="cd-mins">45</div>
                <div class="countdown-lbl">Phút</div>
            </div>
            <div class="countdown-item">
                <div class="countdown-num" id="cd-secs">30</div>
                <div class="countdown-lbl">Giây</div>
            </div>
        </div>
    </section>

    <!-- Flash Sale Products Grid -->
    <section style="margin-bottom: 60px;">
        <div class="products-grid">
            <?php foreach ($flashSales as $p): ?>
                <?php 
                    $discountPct = round((($p['price'] - $p['discount_price']) / $p['price']) * 100);
                ?>
                <div class="product-card">
                    <?php if ($discountPct > 0): ?>
                        <span class="badge-discount">-<?= $discountPct ?>%</span>
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
                            <span>(5.0)</span>
                        </div>
                        <div class="prod-price-row">
                            <span class="current-price"><?= format_price($p['discount_price'] ?? $p['price']) ?></span>
                            <?php if ($p['discount_price']): ?>
                                <span class="old-price"><?= format_price($p['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="prod-actions">
                            <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm btn-block">
                                <i class="fa-solid fa-cart-plus"></i> Chọn Mua
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

    <!-- 5. Featured Products (Sản phẩm nổi bật) -->
    <section style="margin-bottom: 60px;">
        <div class="section-header">
            <div class="section-subtitle">TRENDING NOW</div>
            <h2 class="section-title">Sản Phẩm Nổi Bật Bán Chạy</h2>
        </div>

        <div class="products-grid">
            <?php foreach ($featuredProds as $p): ?>
                <?php 
                    $discountPct = $p['discount_price'] ? round((($p['price'] - $p['discount_price']) / $p['price']) * 100) : 0;
                ?>
                <div class="product-card">
                    <?php if ($discountPct > 0): ?>
                        <span class="badge-discount">-<?= $discountPct ?>%</span>
                    <?php endif; ?>
                    <span class="badge-featured"><i class="fa-solid fa-crown"></i> Nổi Bật</span>
                    
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
                            <i class="fa-solid fa-star-half-stroke"></i>
                            <span>(4.9)</span>
                        </div>
                        <div class="prod-price-row">
                            <span class="current-price"><?= format_price($p['discount_price'] ?? $p['price']) ?></span>
                            <?php if ($p['discount_price']): ?>
                                <span class="old-price"><?= format_price($p['price']) ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="prod-actions">
                            <a href="product_detail.php?id=<?= $p['id'] ?>" class="btn btn-primary btn-sm btn-block">
                                <i class="fa-solid fa-cart-plus"></i> Xem Chi Tiết
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <div style="text-align: center; margin-top: 20px;">
            <a href="products.php" class="btn btn-outline btn-lg">
                Xem Thêm Tất Cả Sản Phẩm <i class="fa-solid fa-arrow-right"></i>
            </a>
        </div>
    </section>

    <!-- 6. Brand Story & Lookbook Banner -->
    <section style="background: linear-gradient(135deg, #0f172a 0%, #1e293b 100%); border-radius: var(--radius-lg); padding: 48px; color: white; margin-bottom: 60px; display: grid; grid-template-columns: 1.2fr 1fr; gap: 40px; align-items: center;">
        <div>
            <span style="color: var(--accent); font-weight: 700; text-transform: uppercase; letter-spacing: 1px; font-size: 0.85rem;">HIEUMINI PHILOSOPHY</span>
            <h2 style="color: white; font-size: 2.2rem; margin: 12px 0 18px; line-height: 1.3;">Thời Trang Độc Bản - Khẳng Định Cá Tính Riêng</h2>
            <p style="color: #cbd5e1; font-size: 1rem; line-height: 1.8; margin-bottom: 24px;">
                Tại HieuMini, chúng tôi tin rằng mỗi bộ trang phục không chỉ đơn thuần là vải vóc mà là câu chuyện thể hiện cái tôi, sự tự tin và phong cách sống hiện đại. Từng đường may, sợi chỉ đều được kiểm duyệt khắt khe theo tiêu chuẩn quốc tế.
            </p>
            <div style="display: flex; gap: 24px;">
                <div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--accent);">50.000+</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Khách Hàng Tin Dùng</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--accent);">99.8%</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Đánh Giá 5 Sao</div>
                </div>
                <div>
                    <div style="font-size: 2rem; font-weight: 800; color: var(--accent);">100%</div>
                    <div style="font-size: 0.8rem; color: #94a3b8;">Cotton Tự Nhiên</div>
                </div>
            </div>
        </div>
        <div style="text-align: center;">
            <img src="assets/images/banners/hero_banner_1.jpg" alt="Lookbook" style="border-radius: var(--radius-lg); border: 2px solid rgba(255,255,255,0.15); box-shadow: var(--shadow-xl);">
        </div>
    </section>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
