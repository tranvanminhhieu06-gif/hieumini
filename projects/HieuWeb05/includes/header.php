<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - HEADER TEMPLATE
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/config.php';

// Xác định trang hiện tại để active navigation
$current_page = basename($_SERVER['PHP_SELF']);
$cart_count = get_cart_count();

// Tiêu đề & Mô tả mặc định nếu chưa đặt
if (!isset($page_title)) {
    $page_title = SITE_NAME . ' | ' . SITE_TAGLINE;
}
if (!isset($page_desc)) {
    $page_desc = 'Hệ thống phòng tập thể hình thương gia & thương mại điện tử thiết bị dinh dưỡng thể hình cao cấp chuẩn CEO hàng đầu Việt Nam.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($page_title) ?></title>
    <meta name="description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta name="keywords" content="HieuMini Gym, gym CEO, phòng tập VIP, whey protein isolate, thiết bị gym cao cấp, master trainer, đai nâng tạ, InBody 770">
    <meta name="author" content="HieuMini Luxury Fitness">

    <!-- Open Graph SEO -->
    <meta property="og:title" content="<?= htmlspecialchars($page_title) ?>">
    <meta property="og:description" content="<?= htmlspecialchars($page_desc) ?>">
    <meta property="og:image" content="<?= BASE_URL ?>/assets/images/hero-gym.jpg">
    <meta property="og:url" content="<?= BASE_URL ?>">
    <meta property="og:type" content="website">

    <!-- Favicon -->
    <link rel="icon" type="image/png" href="<?= BASE_URL ?>/assets/images/logo.png">

    <!-- Google Fonts (Plus Jakarta Sans, Outfit, Montserrat with full Vietnamese subsets) & Font Awesome 6.5 -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

    <!-- Custom CEO Design System Stylesheet -->
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body>

    <!-- Header Navigation -->
    <header class="site-header">
        <div class="container header-inner">
            <!-- Brand Logo -->
            <a href="<?= BASE_URL ?>/index.php" class="brand-logo">
                <div class="logo-icon">HM</div>
                <div class="logo-text">
                    <span class="brand-name">HIEUMINI</span>
                    <span class="brand-tagline">LUXURY FITNESS</span>
                </div>
            </a>

            <!-- Navigation Links -->
            <nav>
                <ul class="nav-menu">
                    <li><a href="<?= BASE_URL ?>/index.php" class="nav-link <?= $current_page == 'index.php' ? 'active' : '' ?>">Trang Chủ</a></li>
                    <li><a href="<?= BASE_URL ?>/products.php" class="nav-link <?= $current_page == 'products.php' || $current_page == 'product-detail.php' ? 'active' : '' ?>">Sản Phẩm & Dịch Vụ</a></li>
                    <li><a href="<?= BASE_URL ?>/services.php" class="nav-link <?= $current_page == 'services.php' ? 'active' : '' ?>">Gói Hội Viên VIP</a></li>
                    <li><a href="<?= BASE_URL ?>/bmi-calculator.php" class="nav-link <?= $current_page == 'bmi-calculator.php' ? 'active' : '' ?>">Đo Chỉ Số BMI</a></li>
                    <li><a href="<?= BASE_URL ?>/about.php" class="nav-link <?= $current_page == 'about.php' ? 'active' : '' ?>">Về HieuMini</a></li>
                    <li><a href="<?= BASE_URL ?>/contact.php" class="nav-link <?= $current_page == 'contact.php' ? 'active' : '' ?>">Liên Hệ</a></li>
                </ul>
            </nav>

            <!-- Header Actions -->
            <div class="header-actions">
                <!-- Search Button -->
                <button type="button" class="search-toggle-btn" title="Tìm kiếm">
                    <i class="fas fa-search"></i>
                </button>

                <!-- Cart Button -->
                <a href="<?= BASE_URL ?>/cart.php" class="cart-btn" title="Giỏ hàng">
                    <i class="fas fa-shopping-bag"></i>
                    <span class="cart-count"><?= $cart_count ?></span>
                </a>

                <!-- VIP Booking CTA Button -->
                <button type="button" class="btn btn-primary btn-sm btn-shimmer header-cta-btn" data-open-modal="booking-modal">
                    <i class="fas fa-crown"></i> Đặt Lịch VIP
                </button>

                <!-- Mobile Menu Button -->
                <button type="button" class="mobile-toggle-btn" title="Menu">
                    <i class="fas fa-bars"></i>
                </button>
            </div>
        </div>

        <!-- Search Bar Dropdown Overlay -->
        <div class="search-dropdown-bar">
            <div class="container">
                <form action="<?= BASE_URL ?>/products.php" method="GET" class="search-input-group">
                    <input type="text" name="search" placeholder="Tìm kiếm gói hội viên, máy chạy bộ, whey isolate, thực phẩm bổ sung..." autocomplete="off">
                    <button type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </header>
