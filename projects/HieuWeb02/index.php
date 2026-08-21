<?php
$page_title = 'Trang Chủ - Siêu Thị Đồ Công Nghệ Cao Cấp';
require_once __DIR__ . '/includes/header.php';

// Lấy danh sách sản phẩm Flash Sale & Sản phẩm nổi bật
$flash_sale_products = [];
$featured_products = [];

if ($pdo) {
    try {
        $stmt_flash = $pdo->query("SELECT * FROM products WHERE is_flash_sale = 1 AND stock_quantity > 0 ORDER BY id DESC LIMIT 4");
        $flash_sale_products = $stmt_flash->fetchAll();

        $stmt_feat = $pdo->query("SELECT * FROM products WHERE is_featured = 1 ORDER BY views DESC LIMIT 8");
        $featured_products = $stmt_feat->fetchAll();
    } catch (Exception $e) {}
}

// Fallback dữ liệu mẫu nếu CSDL chưa kết nối
if (empty($flash_sale_products)) {
    $flash_sale_products = [
        [
            'id' => 1, 'name' => 'iPhone 16 Pro Max 256GB Titan Sa Mạc', 'slug' => 'iphone-16-pro-max-256gb',
            'brand' => 'Apple', 'price' => 34990000, 'sale_price' => 31990000, 'rating' => 5.0, 'is_flash_sale' => 1, 'icon' => 'fa-mobile-screen'
        ],
        [
            'id' => 2, 'name' => 'Samsung Galaxy S24 Ultra 5G 12GB/256GB', 'slug' => 'samsung-galaxy-s24-ultra',
            'brand' => 'Samsung', 'price' => 31990000, 'sale_price' => 26990000, 'rating' => 4.9, 'is_flash_sale' => 1, 'icon' => 'fa-mobile'
        ],
        [
            'id' => 4, 'name' => 'Laptop Gaming ASUS ROG Zephyrus G16 OLED', 'slug' => 'asus-rog-zephyrus-g16-oled',
            'brand' => 'ASUS', 'price' => 54990000, 'sale_price' => 49990000, 'rating' => 4.8, 'is_flash_sale' => 1, 'icon' => 'fa-laptop'
        ],
        [
            'id' => 7, 'name' => 'Tai nghe Sony WH-1000XM5 Chống Ồn Cao Cấp', 'slug' => 'tai-nghe-sony-wh-1000xm5',
            'brand' => 'Sony', 'price' => 8490000, 'sale_price' => 6990000, 'rating' => 4.9, 'is_flash_sale' => 1, 'icon' => 'fa-headphones'
        ]
    ];
}

if (empty($featured_products)) {
    $featured_products = array_merge($flash_sale_products, [
        [
            'id' => 3, 'name' => 'MacBook Pro 14 M3 Pro (18GB/512GB SSD Space Black)', 'slug' => 'macbook-pro-14-m3-pro',
            'brand' => 'Apple', 'price' => 49990000, 'sale_price' => 45490000, 'rating' => 5.0, 'icon' => 'fa-laptop-code'
        ],
        [
            'id' => 5, 'name' => 'iPad Pro 11 inch M4 Wi-Fi 256GB Ultra Thin 5.3mm', 'slug' => 'ipad-pro-11-m4-256gb',
            'brand' => 'Apple', 'price' => 28990000, 'sale_price' => 26990000, 'rating' => 4.9, 'icon' => 'fa-tablet-screen-button'
        ],
        [
            'id' => 6, 'name' => 'Apple Watch Ultra 2 GPS + Cellular 49mm Titanium', 'slug' => 'apple-watch-ultra-2-49mm',
            'brand' => 'Apple', 'price' => 21990000, 'sale_price' => 19490000, 'rating' => 5.0, 'icon' => 'fa-clock'
        ],
        [
            'id' => 8, 'name' => 'Loa Bluetooth Marshall Stanmore III Chính Hãng', 'slug' => 'loa-marshall-stanmore-iii',
            'brand' => 'Marshall', 'price' => 10490000, 'sale_price' => 8990000, 'rating' => 4.7, 'icon' => 'fa-volume-high'
        ]
    ]);
}
?>

<main>
    <!-- Hero Banner Section -->
    <section class="hero-section">
        <div class="container">
            <div class="hero-grid">
                <!-- Banner Chính -->
                <div class="hero-banner-main">
                    <span class="hero-tag"><i class="fa-solid fa-sparkles"></i> SIÊU PHẨM CÔNG NGHỆ 2026</span>
                    <h1 class="hero-title">iPhone 16 Pro Max<br><span style="color: var(--accent);">Titan Sa Mạc</span> Đẳng Cấp</h1>
                    <p class="hero-desc">Trang bị chip Apple A18 Pro 3nm đỉnh cao, Camera nút bấm cảm ứng mới cùng thời lượng pin dẫn đầu phân khúc.</p>
                    <div style="display: flex; gap: 14px; flex-wrap: wrap;">
                        <a href="product_detail.php?id=1" class="btn btn-primary">
                            <i class="fa-solid fa-cart-shopping"></i> Mua ngay giá ưu đãi
                        </a>
                        <a href="products.php" class="btn btn-outline">
                            <i class="fa-solid fa-compass"></i> Khám phá tất cả
                        </a>
                    </div>
                </div>

                <!-- Side Banners -->
                <div class="hero-side-banners">
                    <div class="side-banner-item" style="background: linear-gradient(135deg, rgba(30, 41, 69, 0.8), rgba(15, 23, 42, 0.9));">
                        <span class="badge badge-warning" style="width: fit-content; margin-bottom: 8px;">MacBook M3 Pro</span>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 6px;">Cỗ Máy Đồ Họa Đỉnh Cao</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">Giảm trực tiếp 4.500.000₫ cho HSSV</p>
                        <a href="products.php?cat=2" class="view-all-link" style="font-size: 0.85rem;">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></a>
                    </div>

                    <div class="side-banner-item" style="background: linear-gradient(135deg, rgba(16, 185, 129, 0.15), rgba(15, 23, 42, 0.9)); border-color: rgba(16, 185, 129, 0.3);">
                        <span class="badge badge-success" style="width: fit-content; margin-bottom: 8px;">Galaxy AI 2026</span>
                        <h3 style="font-size: 1.25rem; font-weight: 700; margin-bottom: 6px;">Galaxy S24 Ultra 5G</h3>
                        <p style="font-size: 0.85rem; color: var(--text-muted); margin-bottom: 12px;">Tặng kèm củ sạc 45W & Bảo hành 2 năm</p>
                        <a href="products.php?cat=1" class="view-all-link" style="color: var(--success); font-size: 0.85rem;">Xem chi tiết <i class="fa-solid fa-arrow-right"></i></a>
                    </div>
                </div>
            </div>

            <!-- 4 Feature Badges -->
            <div class="features-bar">
                <div class="feature-box">
                    <div class="feature-icon"><i class="fa-solid fa-shield-check"></i></div>
                    <div class="feature-info">
                        <h4>100% Chính Hãng</h4>
                        <p>Bảo hành chính hãng 24 tháng</p>
                    </div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon" style="color: var(--accent); background: rgba(6, 182, 212, 0.15);"><i class="fa-solid fa-truck-bolt"></i></div>
                    <div class="feature-info">
                        <h4>Giao Siêu Tốc 2H</h4>
                        <p>Miễn phí giao nội thành toàn quốc</p>
                    </div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon" style="color: var(--warning); background: rgba(245, 158, 11, 0.15);"><i class="fa-solid fa-arrow-rotate-left"></i></div>
                    <div class="feature-info">
                        <h4>Đổi Trả 30 Ngày</h4>
                        <p>1 đổi 1 nếu có lỗi kỹ thuật</p>
                    </div>
                </div>
                <div class="feature-box">
                    <div class="feature-icon" style="color: var(--accent-pink); background: rgba(236, 72, 153, 0.15);"><i class="fa-solid fa-credit-card"></i></div>
                    <div class="feature-info">
                        <h4>Trả Góp 0% Lãi Suất</h4>
                        <p>Xét duyệt online chỉ trong 5 phút</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Flash Sale Section -->
    <section class="section">
        <div class="container">
            <div class="flash-sale-box">
                <div class="flash-sale-header">
                    <div class="flash-title">
                        <i class="fa-solid fa-bolt-lightning"></i>
                        <span>GIỜ VÀNG GIÁ SỐC (FLASH SALE)</span>
                    </div>
                    <div class="countdown-timer">
                        <span style="color: var(--text-muted); font-size: 0.9rem; font-weight: 600;">Kết thúc sau:</span>
                        <div class="timer-block" id="cd-hours">12</div> :
                        <div class="timer-block" id="cd-mins">45</div> :
                        <div class="timer-block" id="cd-secs">30</div>
                    </div>
                </div>

                <div class="product-grid">
                    <?php foreach ($flash_sale_products as $p): 
                        $discount = calculate_discount($p['price'], $p['sale_price']);
                        $thumb = !empty($p['thumbnail']) && file_exists(__DIR__ . '/assets/images/' . $p['thumbnail']) ? 'assets/images/' . $p['thumbnail'] : 'assets/images/default_prod.png';
                    ?>
                        <div class="product-card">
                            <?php if ($discount > 0): ?>
                                <span class="card-badge-discount">-<?php echo $discount; ?>%</span>
                            <?php endif; ?>
                            <span class="card-badge-tag"><i class="fa-solid fa-fire text-danger"></i> HOT</span>

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
            </div>
        </div>
    </section>

    <!-- Featured Products Section -->
    <section class="section">
        <div class="container">
            <div class="section-header">
                <div class="section-title-wrap">
                    <h2><i class="fa-solid fa-crown" style="color: #fbbf24;"></i> Sản Phẩm Công Nghệ Bán Chạy</h2>
                    <p>Những thiết bị dẫn đầu công nghệ được đông đảo khách hàng tin dùng nhất</p>
                </div>
                <a href="products.php" class="view-all-link">Xem tất cả sản phẩm <i class="fa-solid fa-arrow-right"></i></a>
            </div>

            <div class="product-grid">
                <?php foreach ($featured_products as $p): 
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
        </div>
    </section>

    <!-- Why Choose HieuMini Section -->
    <section class="section" style="background: rgba(255, 255, 255, 0.02); border-top: var(--border-glass); border-bottom: var(--border-glass);">
        <div class="container">
            <div style="text-align: center; max-width: 700px; margin: 0 auto 40px;">
                <span class="hero-tag"><i class="fa-solid fa-award"></i> VÌ SAO CHỌN CHÚNG TÔI</span>
                <h2 style="font-size: 2rem; font-weight: 800; margin-bottom: 12px;">Trải Nghiệm Mua Sắm Đỉnh Cao</h2>
                <p style="color: var(--text-muted);">HieuMini cam kết mang tới cho tín đồ công nghệ dịch vụ tiêu chuẩn 5 sao với sản phẩm chính hãng và giá trị vượt trội.</p>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 24px;">
                <div class="glass-panel" style="padding: 28px; text-align: center;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(99, 102, 241, 0.2); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 16px;">
                        <i class="fa-solid fa-microchip"></i>
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Cập Nhật Công Nghệ Mới Nhất</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">Sản phẩm được nhập khẩu chính hãng trực tiếp từ Apple, Samsung, ASUS, Sony ngay trong ngày mở bán toàn cầu.</p>
                </div>

                <div class="glass-panel" style="padding: 28px; text-align: center;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(6, 182, 212, 0.2); color: var(--accent); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 16px;">
                        <i class="fa-solid fa-qrcode"></i>
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Thanh Toán QR & COD Linh Hoạt</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">Hỗ trợ quét mã VietQR tự động xác nhận trong 3 giây hoặc nhận hàng thanh toán tiền mặt (COD) tận nhà an tâm.</p>
                </div>

                <div class="glass-panel" style="padding: 28px; text-align: center;">
                    <div style="width: 60px; height: 60px; border-radius: 50%; background: rgba(16, 185, 129, 0.2); color: var(--success); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 16px;">
                        <i class="fa-solid fa-headset"></i>
                    </div>
                    <h3 style="font-size: 1.15rem; font-weight: 700; margin-bottom: 8px;">Hỗ Trợ Kỹ Thuật Trọn Đời</h3>
                    <p style="font-size: 0.9rem; color: var(--text-muted);">Đội ngũ kỹ thuật viên lành nghề luôn sẵn sàng tư vấn, cài đặt phần mềm và hỗ trợ chuyển đổi dữ liệu miễn phí.</p>
                </div>
            </div>
        </div>
    </section>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
