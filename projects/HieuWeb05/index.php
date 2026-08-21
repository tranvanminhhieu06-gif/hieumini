<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - HOMEPAGE (CHUẨN CEO)
 */
require_once __DIR__ . '/includes/config.php';

$page_title = "HieuMini Luxury Fitness Club | Đẳng Cấp Thể Hình CEO 5 Sao";
$page_desc = "Tổ hợp thể hình thượng lưu chuẩn 5 sao dành cho lãnh đạo, doanh nhân. Hệ thống máy tập Olympic, Dinh dưỡng tinh khiết và Huấn luyện viên Master quốc tế.";

// Lấy danh mục sản phẩm
$stmt_cat = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$categories = $stmt_cat->fetchAll();

// Lấy toàn bộ 30 sản phẩm từ CSDL
$stmt_prod = $pdo->query("
    SELECT p.*, c.name AS category_name, c.slug AS category_slug 
    FROM products p 
    JOIN categories c ON p.category_id = c.id 
    ORDER BY p.id ASC
");
$all_products = $stmt_prod->fetchAll();

// Lọc các gói hội viên nổi bật cho phần Bảng Giá
$vip_memberships = array_filter($all_products, function($p) {
    return $p['category_id'] == 1;
});

require_once __DIR__ . '/includes/header.php';
?>

<!-- ==================== HERO SECTION (CEO EXECUTIVE) ==================== -->
<section class="hero-section">
    <div class="hero-bg" style="background-image: url('<?= BASE_URL ?>/assets/images/hero-gym.jpg');"></div>
    <div class="hero-overlay"></div>
    <div class="container hero-content">
        <div class="reveal">
            <div class="hero-subtitle-badge">
                <i class="fas fa-crown"></i> BIỂU TƯỢNG ĐẲNG CẤP THỂ HÌNH DOANH NHÂN
            </div>
            <h1 class="hero-title">
                HIEUMINI <span class="text-gold-gradient">LUXURY FITNESS</span><br>
                CHUẨN MỰC CEO 5 SAO
            </h1>
            <p class="hero-description">
                Không gian rèn luyện thể chất và tái tạo năng lượng đỉnh cao dành riêng cho các nhà lãnh đạo. Trang thiết bị cơ học chuẩn Olympic, phòng xông đá muối Himalaya và dịch vụ chăm sóc sức khỏe thượng lưu.
            </p>
            <div class="hero-buttons">
                <button type="button" class="btn btn-primary btn-lg btn-shimmer" data-open-modal="booking-modal">
                    <i class="fas fa-crown"></i> ĐẶT LỊCH TRẢI NGHIỆM VIP
                </button>
                <a href="<?= BASE_URL ?>/products.php" class="btn btn-secondary btn-lg">
                    <i class="fas fa-dumbbell"></i> KHÁM PHÁ CỬA HÀNG
                </a>
            </div>

            <!-- Animated Stats Counter Row -->
            <div class="hero-stats-row">
                <div class="stat-box">
                    <span class="stat-number" data-target="5000" data-suffix="+">0</span>
                    <span class="stat-label">Hội Viên Doanh Nhân</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number" data-target="99" data-suffix="%">0</span>
                    <span class="stat-label">Tỷ Lệ Hài Lòng 5★</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number" data-target="25" data-suffix="+">0</span>
                    <span class="stat-label">Master Trainer Quốc Tế</span>
                </div>
                <div class="stat-box">
                    <span class="stat-number" data-target="1500" data-suffix="m²">0</span>
                    <span class="stat-label">Không Gian VIP Chuẩn CEO</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== CATEGORIES SECTION ==================== -->
<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-tag">HỆ SINH THÁI HIEUMINI</span>
            <h2 class="section-title">DANH MỤC DỊCH VỤ & SẢN PHẨM</h2>
            <p class="section-desc">Giải pháp toàn diện từ gói tập thượng lưu, máy móc chuyên nghiệp đến nguồn dinh dưỡng thể hình tinh khiết 100%.</p>
        </div>

        <div class="categories-grid">
            <?php foreach ($categories as $cat): 
                $count_stmt = $pdo->prepare("SELECT COUNT(*) FROM products WHERE category_id = ?");
                $count_stmt->execute([$cat['id']]);
                $p_count = $count_stmt->fetchColumn();
            ?>
            <a href="<?= BASE_URL ?>/products.php?category=<?= $cat['slug'] ?>" class="category-card reveal">
                <div class="category-icon-wrap">
                    <i class="fas <?= htmlspecialchars($cat['icon']) ?>"></i>
                </div>
                <h3 class="category-name"><?= htmlspecialchars($cat['name']) ?></h3>
                <span class="category-count"><?= $p_count ?> Sản phẩm & Dịch vụ</span>
            </a>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<!-- ==================== VIP MEMBERSHIPS COMPARISON ==================== -->
<section class="section" style="background: #0d1017; border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-tag">ĐẶC QUYỀN THƯỢNG LƯU</span>
            <h2 class="section-title">GÓI HỘI VIÊN CEO LUXURY</h2>
            <p class="section-desc">Chọn gói thành viên phù hợp với phong cách lãnh đạo và thời gian biểu bận rộn của bạn.</p>
        </div>

        <div class="pricing-grid">
            <!-- 1. Executive Gold -->
            <div class="pricing-card reveal">
                <div class="pricing-header">
                    <h3 class="pricing-name">EXECUTIVE GOLD</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Dành cho nhà quản lý & chuyên gia</p>
                    <div class="pricing-price">13.900.000 ₫</div>
                    <span class="pricing-period">Thời hạn 06 Tháng</span>
                </div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check-circle"></i> Tập luyện không giới hạn khung giờ 05:30 - 22:30</li>
                    <li><i class="fas fa-check-circle"></i> Miễn phí Sauna Phần Lan & Tủ đồ Smartlock</li>
                    <li><i class="fas fa-check-circle"></i> Đo InBody 770 y khoa 2 lần/tháng</li>
                    <li><i class="fas fa-check-circle"></i> Tặng 04 buổi tập cùng Master Trainer</li>
                    <li><i class="fas fa-check-circle"></i> Miễn phí nước ion kiềm & khăn tập kháng khuẩn</li>
                </ul>
                <button type="button" class="btn btn-gold-outline btn-block" data-open-modal="booking-modal">
                    ĐĂNG KÝ GÓI GOLD
                </button>
            </div>

            <!-- 2. CEO Diamond Elite (Featured) -->
            <div class="pricing-card featured reveal delay-1">
                <div class="pricing-ribbon">GÓI CEO KHUYÊN DÙNG</div>
                <div class="pricing-header">
                    <h3 class="pricing-name" style="color: var(--gold-light);">CEO DIAMOND ELITE</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Đặc quyền tối thượng toàn hệ thống</p>
                    <div class="pricing-price" style="color: var(--gold-light);">24.500.000 ₫</div>
                    <span class="pricing-period">Thời hạn 12 Tháng (Tiết kiệm 3.5M)</span>
                </div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check-circle"></i> <strong>Toàn quyền truy cập VIP Lounge</strong> & Hồ bơi vô cực</li>
                    <li><i class="fas fa-check-circle"></i> Phục hồi bồn sục Jacuzzi & Xông hơi đá muối Himalaya</li>
                    <li><i class="fas fa-check-circle"></i> Đo InBody 770 & Tư vấn dinh dưỡng cá nhân hàng tuần</li>
                    <li><i class="fas fa-check-circle"></i> <strong>Tặng 12 buổi Master Trainer 1:1</strong> chuyên sâu</li>
                    <li><i class="fas fa-check-circle"></i> Dịch vụ giặt sấy trang phục tập riêng biệt</li>
                    <li><i class="fas fa-check-circle"></i> Được dẫn 01 khách đi kèm miễn phí mỗi tháng</li>
                </ul>
                <button type="button" class="btn btn-primary btn-block btn-shimmer" data-open-modal="booking-modal">
                    <i class="fas fa-crown"></i> ĐĂNG KÝ CEO DIAMOND
                </button>
            </div>

            <!-- 3. Platinum 90 Days -->
            <div class="pricing-card reveal delay-2">
                <div class="pricing-header">
                    <h3 class="pricing-name">PLATINUM 90 DAYS</h3>
                    <p style="color: var(--text-secondary); font-size: 0.9rem;">Bứt phá thể lực trong 3 tháng</p>
                    <div class="pricing-price">7.900.000 ₫</div>
                    <span class="pricing-period">Thời hạn 03 Tháng</span>
                </div>
                <ul class="pricing-features">
                    <li><i class="fas fa-check-circle"></i> Tập luyện toàn bộ khu máy tập cardio & tạ Olympic</li>
                    <li><i class="fas fa-check-circle"></i> Sử dụng dịch vụ Sauna & Phòng tắm cao cấp</li>
                    <li><i class="fas fa-check-circle"></i> Tham gia các lớp Yoga & Kickboxing</li>
                    <li><i class="fas fa-check-circle"></i> Hỗ trợ xây dựng lịch tập cá nhân hóa</li>
                    <li><i class="fas fa-check-circle"></i> Miễn phí đo InBody đầu vào & kết thúc</li>
                </ul>
                <button type="button" class="btn btn-gold-outline btn-block" data-open-modal="booking-modal">
                    ĐĂNG KÝ GÓI PLATINUM
                </button>
            </div>
        </div>
    </div>
</section>

<!-- ==================== 30-PRODUCT SHOWCASE WITH TABS ==================== -->
<section class="section" id="products-showcase">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-tag">BỘ SƯU TẬP TOÀN DIỆN</span>
            <h2 class="section-title">30 SẢN PHẨM & DỊCH VỤ FITNESS ĐẲNG CẤP</h2>
            <p class="section-desc">Khám phá đầy đủ trang thiết bị, dinh dưỡng và gói tập chuẩn CEO được tuyển chọn nghiêm ngặt.</p>
        </div>

        <!-- Filter Tabs Navigation -->
        <div class="product-tabs-nav reveal">
            <button type="button" class="tab-btn active" data-category="all">Tất Cả (30)</button>
            <?php foreach ($categories as $c): ?>
            <button type="button" class="tab-btn" data-category="<?= $c['id'] ?>"><?= htmlspecialchars($c['name']) ?></button>
            <?php endforeach; ?>
        </div>

        <!-- Products Grid (30 Items) -->
        <div class="products-grid">
            <?php foreach ($all_products as $p): 
                $discount_percent = get_discount_percent($p['price'], $p['original_price']);
            ?>
            <div class="product-card product-tab-item reveal" data-category="<?= $p['category_id'] ?>">
                <!-- Thumbnail -->
                <div class="product-thumb-wrap">
                    <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($p['image']) ?>" alt="<?= htmlspecialchars($p['name']) ?>" loading="lazy">
                    
                    <?php if ($p['badge']): ?>
                    <span class="badge badge-gold product-badge-tag"><?= htmlspecialchars($p['badge']) ?></span>
                    <?php endif; ?>

                    <?php if ($discount_percent > 0): ?>
                    <span class="product-discount-tag">-<?= $discount_percent ?>%</span>
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

                <!-- Card Body -->
                <div class="product-body">
                    <span class="product-category-meta"><?= htmlspecialchars($p['category_name']) ?></span>
                    <h4 class="product-title">
                        <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $p['id'] ?>"><?= htmlspecialchars($p['name']) ?></a>
                    </h4>
                    
                    <div class="product-rating">
                        <?php 
                        $rating_full = floor($p['rating']);
                        for ($i = 0; $i < 5; $i++): 
                            if ($i < $rating_full): ?>
                                <i class="fas fa-star"></i>
                            <?php else: ?>
                                <i class="far fa-star"></i>
                            <?php endif;
                        endfor; ?>
                        <span class="review-count">(<?= $p['review_count'] ?> đánh giá)</span>
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

        <div style="text-align: center; margin-top: 3.5rem;" class="reveal">
            <a href="<?= BASE_URL ?>/products.php" class="btn btn-primary btn-lg btn-shimmer">
                <i class="fas fa-th-large"></i> XEM TOÀN BỘ CỬA HÀNG VỚI BỘ LỌC CHUYÊN SÂU
            </a>
        </div>
    </div>
</section>

<!-- ==================== INTERACTIVE BMI & INBODY CALCULATOR ==================== -->
<section class="section" style="background: #090b0f; border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-tag">CÔNG NGHỆ CHẨN ĐOÁN THỂ LỰC</span>
            <h2 class="section-title">TÍNH CHỈ SỐ THỂ HÌNH & CALORIE CEO</h2>
            <p class="section-desc">Đánh giá nhanh tình trạng cơ thể và nhận lời khuyên dinh dưỡng, bài tập phù hợp ngay lập tức.</p>
        </div>

        <div class="bmi-widget-card reveal">
            <!-- Form Input -->
            <div>
                <h3 style="font-size: 1.4rem; color: var(--gold-light); margin-bottom: 1.25rem;">
                    <i class="fas fa-calculator"></i> Nhập Thông Số Cơ Thể Của Bạn
                </h3>
                <form id="bmi-calculator-form">
                    <div class="bmi-input-row">
                        <div class="bmi-form-group">
                            <label>Chiều Cao (cm)</label>
                            <input type="number" id="bmi-height" class="bmi-form-control" placeholder="Ví dụ: 175" min="100" max="230" required value="175">
                        </div>
                        <div class="bmi-form-group">
                            <label>Cân Nặng (kg)</label>
                            <input type="number" id="bmi-weight" class="bmi-form-control" placeholder="Ví dụ: 72" min="30" max="200" required value="72">
                        </div>
                    </div>
                    <div class="bmi-input-row">
                        <div class="bmi-form-group">
                            <label>Độ Tuổi</label>
                            <input type="number" id="bmi-age" class="bmi-form-control" placeholder="Ví dụ: 32" min="15" max="80" value="32">
                        </div>
                        <div class="bmi-form-group">
                            <label>Giới Tính</label>
                            <select id="bmi-gender" class="bmi-form-control">
                                <option value="male">Nam Doanh Nhân</option>
                                <option value="female">Nữ Doanh Nhân</option>
                            </select>
                        </div>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-shimmer" style="margin-top: 1rem;">
                        <i class="fas fa-chart-line"></i> PHÂN TÍCH CHỈ SỐ NGAY
                    </button>
                </form>
            </div>

            <!-- Result Panel -->
            <div class="bmi-result-panel">
                <div class="bmi-score-circle" id="bmi-score-circle">
                    <span class="bmi-number" id="bmi-score-number">23.5</span>
                    <span style="font-size: 0.75rem; color: var(--text-secondary); text-transform: uppercase;">BMI SCORE</span>
                </div>
                <h4 class="bmi-category-status" id="bmi-status-text" style="color: var(--emerald-accent);">Chuẩn Body VIP / Lý Tưởng</h4>
                <p class="bmi-recommendation" id="bmi-rec-text">
                    Thể trạng của bạn đang ở mức rất tuyệt vời! Tiếp tục duy trì phong độ rèn luyện và phục hồi với gói CEO Diamond Elite.
                </p>
                <div style="margin-top: 1.5rem;">
                    <button type="button" class="btn btn-gold-outline btn-sm" data-open-modal="booking-modal">
                        <i class="fas fa-stethoscope"></i> Đặt Lịch Đo InBody 770 Chi Tiết
                    </button>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== MASTER TRAINERS & COACHES ==================== -->
<section class="section">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-tag">ĐỘI NGŨ CHUYÊN GIA</span>
            <h2 class="section-title">MASTER TRAINERS QUỐC TẾ</h2>
            <p class="section-desc">Được dẫn dắt bởi các huấn luyện viên đạt chứng chỉ NASM, ISSA và vận động viên thể hình danh tiếng.</p>
        </div>

        <div class="trainers-grid">
            <div class="trainer-card reveal">
                <div class="trainer-img-wrap">
                    <img src="<?= BASE_URL ?>/assets/images/trainers/trainer_1.jpg" alt="Alexander Vũ" loading="lazy">
                </div>
                <div class="trainer-body">
                    <h3 class="trainer-name">ALEXANDER VŨ</h3>
                    <p class="trainer-role">HLV Trưởng • NASM Master • 12 Năm Kinh Nghiệm</p>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5;">
                        Chuyên gia tái cấu trúc hình thể và tối ưu hiệu suất thể lực dành riêng cho các nhà lãnh đạo cấp cao.
                    </p>
                </div>
            </div>

            <div class="trainer-card reveal delay-1">
                <div class="trainer-img-wrap">
                    <img src="<?= BASE_URL ?>/assets/images/trainers/trainer_2.jpg" alt="Elena Nguyễn" loading="lazy">
                </div>
                <div class="trainer-body">
                    <h3 class="trainer-name">ELENA NGUYỄN</h3>
                    <p class="trainer-role">Chuyên Gia Dinh Dưỡng & Giảm Mỡ Nữ CEO</p>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5;">
                        Thiết kế thực đơn dinh dưỡng tinh chỉnh giúp tăng năng lượng làm việc và sở hữu vóc dáng thon gọn.
                    </p>
                </div>
            </div>

            <div class="trainer-card reveal delay-2">
                <div class="trainer-img-wrap">
                    <img src="<?= BASE_URL ?>/assets/images/trainers/trainer_3.jpg" alt="Marcus Trần" loading="lazy">
                </div>
                <div class="trainer-body">
                    <h3 class="trainer-name">MARCUS TRẦN</h3>
                    <p class="trainer-role">Vận Động Viên Quốc Gia • Trị Liệu Thể Thao</p>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5;">
                        Chuyên sâu về nắn chỉnh cột sống, phục hồi đau mỏi thắt lưng và giải phóng căng thẳng cơ bắp.
                    </p>
                </div>
            </div>

            <div class="trainer-card reveal delay-3">
                <div class="trainer-img-wrap">
                    <img src="<?= BASE_URL ?>/assets/images/trainers/trainer_4.jpg" alt="Sarah Phạm" loading="lazy">
                </div>
                <div class="trainer-body">
                    <h3 class="trainer-name">SARAH PHẠM</h3>
                    <p class="trainer-role">Master Yoga & Thiền Định Doanh Nhân</p>
                    <p style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.5;">
                        Giúp tái tạo sự tập trung, điều hòa nhịp thở và giải phóng áp lực tâm trí sau những cuộc họp căng thẳng.
                    </p>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== CEO TESTIMONIALS ==================== -->
<section class="section" style="background: #0d1017; border-top: 1px solid var(--border-subtle);">
    <div class="container">
        <div class="section-header reveal">
            <span class="section-tag">ĐÁNH GIÁ TỪ HỘI VIÊN</span>
            <h2 class="section-title">CẢM NHẬN CỦA CÁC CEO VỀ HIEUMINI</h2>
            <p class="section-desc">Hơn 5.000 doanh nhân và lãnh đạo đã tin tưởng lựa chọn HieuMini Luxury Fitness Club.</p>
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(320px, 1fr)); gap: 2rem;">
            <div class="category-card reveal" style="text-align: left; align-items: flex-start; padding: 2rem;">
                <div style="color: var(--gold-light); font-size: 1.1rem; margin-bottom: 1rem;">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p style="color: #e2e8f0; font-size: 1rem; line-height: 1.7; margin-bottom: 1.5rem; font-style: italic;">
                    "Không gian tập tại HieuMini cực kỳ yên tĩnh và riêng tư. Tôi có thể vừa tập luyện vừa xông hơi tái tạo năng lượng trước khi bước vào các cuộc họp chiến lược. Gói Diamond Elite hoàn toàn xứng đáng từng đồng đầu tư."
                </p>
                <div>
                    <h4 style="font-size: 1.1rem; color: #fff;">Doanh nhân Trần Đình Tuấn</h4>
                    <span style="font-size: 0.85rem; color: var(--gold-primary);">Chủ tịch Tập đoàn Tuấn Phát</span>
                </div>
            </div>

            <div class="category-card reveal delay-1" style="text-align: left; align-items: flex-start; padding: 2rem;">
                <div style="color: var(--gold-light); font-size: 1.1rem; margin-bottom: 1rem;">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p style="color: #e2e8f0; font-size: 1rem; line-height: 1.7; margin-bottom: 1.5rem; font-style: italic;">
                    "Máy chạy bộ Commercial X9 Pro và thực phẩm bổ sung Whey Isolate của HieuMini đạt chuẩn cao cấp. Huấn luyện viên nắm rất rõ tình trạng sức khỏe của tôi và điều chỉnh lịch tập linh hoạt tuyệt đối."
                </p>
                <div>
                    <h4 style="font-size: 1.1rem; color: #fff;">Chị Lê Thị Thu Hương</h4>
                    <span style="font-size: 0.85rem; color: var(--gold-primary);">Giám đốc Điều hành FinCorp</span>
                </div>
            </div>

            <div class="category-card reveal delay-2" style="text-align: left; align-items: flex-start; padding: 2rem;">
                <div style="color: var(--gold-light); font-size: 1.1rem; margin-bottom: 1rem;">
                    <i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i><i class="fas fa-star"></i>
                </div>
                <p style="color: #e2e8f0; font-size: 1rem; line-height: 1.7; margin-bottom: 1.5rem; font-style: italic;">
                    "Sau 3 tháng theo học giáo án 1:1 cùng Master Trainer, chỉ số mỡ nội tạng của tôi giảm từ 12 xuống còn 7, sức bền tăng rõ rệt. Phong cách phục vụ tại HieuMini chuẩn 5 sao không nơi nào sánh được."
                </p>
                <div>
                    <h4 style="font-size: 1.1rem; color: #fff;">Anh Hoàng Mạnh Thắng</h4>
                    <span style="font-size: 0.85rem; color: var(--gold-primary);">Founder TechVenture</span>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- ==================== VIP CTA BANNER ==================== -->
<section class="section" style="background: linear-gradient(135deg, #181d26 0%, #0d1017 100%); border-top: 1px solid var(--border-gold);">
    <div class="container" style="text-align: center;">
        <div class="reveal" style="max-width: 800px; margin: 0 auto;">
            <span class="badge badge-gold" style="margin-bottom: 1rem;">ĐẶC QUYỀN HỘI VIÊN MỚI</span>
            <h2 style="font-size: 2.8rem; font-weight: 900; margin-bottom: 1.25rem;">
                SẴN SÀNG CHINH PHỤC <span class="text-gold-gradient">ĐỈNH CAO THỂ LỰC?</span>
            </h2>
            <p style="color: var(--text-secondary); font-size: 1.15rem; line-height: 1.7; margin-bottom: 2.5rem;">
                Đăng ký ngay hôm nay để nhận 01 buổi trải nghiệm dịch vụ 5 sao miễn phí, đo chỉ số InBody 770 và voucher ưu đãi 20% khi mua sắm dinh dưỡng/thiết bị gym với mã <strong style="color: var(--gold-light);">CEOFIT20</strong>.
            </p>
            <button type="button" class="btn btn-primary btn-lg btn-shimmer" data-open-modal="booking-modal">
                <i class="fas fa-crown"></i> ĐĂNG KÝ TRẢI NGHIỆM MIỄN PHÍ NGAY
            </button>
        </div>
    </div>
</section>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
