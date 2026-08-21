<?php
/**
 * Header giao diện người dùng HieuMini - UI/UX Pro Max Edition
 */
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/functions.php';

// Lấy danh mục sản phẩm từ CSDL
$catStmt = $pdo->query("SELECT id, name, slug FROM categories WHERE status = 1 ORDER BY id ASC");
$headerCategories = $catStmt->fetchAll();

// Mapping icon cho danh mục thời trang
$categoryIcons = [
    'ao-thun-polo' => 'fa-solid fa-shirt',
    'ao-so-mi' => 'fa-solid fa-vest',
    'ao-khoac-hoodie' => 'fa-solid fa-vest-patches',
    'quan-jeans' => 'fa-solid fa-person',
    'quan-kaki' => 'fa-solid fa-person-walking',
    'vay-dam-nu' => 'fa-solid fa-person-dress'
];

$currentUser = current_user($pdo);
$cartCount = get_cart_count();
$activeCat = isset($_GET['cat']) ? trim($_GET['cat']) : '';
$currentScript = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? htmlspecialchars($pageTitle) . ' - ' . SITE_NAME : SITE_NAME . ' | Thời Trang Cao Cấp 2026' ?></title>
    <meta name="description" content="HieuMini - Thương hiệu thời trang đường phố và công sở cao cấp. Mua sắm áo thun, sơ mi, hoodie, quần jean chất lượng số 1.">
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@300;400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Main CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Top Announcement Bar Pro Max -->
    <div class="topbar" id="topbar">
        <div class="container">
            <div class="topbar-carousel">
                <div class="topbar-item active">
                    <i class="fa-solid fa-bolt topbar-icon"></i>
                    <span><strong>ƯU ĐÃI ĐẶC BIỆT:</strong> Miễn phí vận chuyển toàn quốc cho đơn hàng từ <strong>300.000đ</strong></span>
                </div>
                <div class="topbar-item">
                    <i class="fa-solid fa-rotate-left topbar-icon"></i>
                    <span><strong>CHÍNH SÁCH VÀNG:</strong> Đổi trả miễn phí trong <strong>30 ngày</strong> tận nhà</span>
                </div>
                <div class="topbar-item">
                    <i class="fa-solid fa-gift topbar-icon"></i>
                    <span><strong>MÃ GIẢM GIÁ:</strong> Nhập <code>HIEU10</code> giảm ngay 10% đơn đầu tiên</span>
                </div>
            </div>
            <div class="topbar-links">
                <a href="admin/index.php" class="topbar-link admin-topbar-link" style="color: #fbbf24; font-weight: 700; background: rgba(245, 158, 11, 0.15); padding: 3px 8px; border-radius: 4px;" title="Vào Bảng Điều Khiển Quản Trị Viên">
                    <i class="fa-solid fa-shield-halved"></i> <span>Quản Trị (Admin)</span>
                </a>
                <span class="topbar-divider"></span>
                <a href="tel:0988889999" class="topbar-link"><i class="fa-solid fa-phone"></i> <span>Hotline: 0988.889.999</span></a>
                <span class="topbar-divider"></span>
                <a href="my_orders.php" class="topbar-link"><i class="fa-solid fa-truck-fast"></i> <span>Tra cứu đơn hàng</span></a>
                <span class="topbar-divider"></span>
                <button type="button" class="topbar-link" data-open-modal="size-modal"><i class="fa-solid fa-ruler"></i> <span>Bảng Size</span></button>
            </div>
        </div>
    </div>

    <!-- Main Header & Navigation -->
    <header class="site-header" id="site-header">
        <div class="container">
            <div class="navbar">
                <!-- Mobile Menu Hamburger Button -->
                <button type="button" class="navbar-toggle-btn" id="mobile-menu-toggle" aria-label="Mở menu di động" aria-expanded="false">
                    <span class="bar bar-1"></span>
                    <span class="bar bar-2"></span>
                    <span class="bar bar-3"></span>
                </button>

                <!-- Brand Logo -->
                <a href="index.php" class="brand-logo" title="HieuMini Fashion">
                    <img src="assets/images/logo.png" alt="HieuMini Fashion Studio">
                </a>

                <!-- Desktop Navigation Menu -->
                <nav class="nav-menu" id="desktop-nav">
                    <a href="index.php" class="nav-link <?= ($currentScript === 'index.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-house-chimney nav-icon-prefix"></i>
                        <span>Trang Chủ</span>
                    </a>

                    <!-- Category Mega Dropdown -->
                    <div class="nav-dropdown-wrapper">
                        <a href="products.php" class="nav-link nav-link-dropdown <?= ($currentScript === 'products.php' && !empty($activeCat)) ? 'active' : '' ?>">
                            <i class="fa-solid fa-layer-group nav-icon-prefix"></i>
                            <span>Danh Mục</span>
                            <i class="fa-solid fa-chevron-down dropdown-arrow"></i>
                        </a>
                        <div class="nav-dropdown-menu">
                            <div class="nav-dropdown-header">
                                <span>Bộ Sưu Tập Thời Trang 2026</span>
                                <a href="products.php" class="view-all-link">Xem tất cả <i class="fa-solid fa-arrow-right"></i></a>
                            </div>
                            <div class="nav-dropdown-grid">
                                <?php foreach ($headerCategories as $cat): 
                                    $iconClass = isset($categoryIcons[$cat['slug']]) ? $categoryIcons[$cat['slug']] : 'fa-solid fa-tag';
                                    $isCurrentCat = ($activeCat === $cat['slug']);
                                ?>
                                    <a href="products.php?cat=<?= urlencode($cat['slug']) ?>" class="dropdown-category-card <?= $isCurrentCat ? 'active' : '' ?>">
                                        <div class="category-icon-box">
                                            <i class="<?= $iconClass ?>"></i>
                                        </div>
                                        <div class="category-info">
                                            <span class="category-title"><?= htmlspecialchars($cat['name']) ?></span>
                                            <span class="category-sub">Khám phá mẫu mới</span>
                                        </div>
                                        <?php if ($cat['slug'] === 'ao-thun-polo'): ?>
                                            <span class="nav-badge hot">HOT</span>
                                        <?php elseif ($cat['slug'] === 'ao-khoac-hoodie'): ?>
                                            <span class="nav-badge new">NEW</span>
                                        <?php endif; ?>
                                    </a>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>

                    <a href="products.php?cat=ao-thun-polo" class="nav-link <?= ($activeCat === 'ao-thun-polo') ? 'active' : '' ?>">
                        <span>Áo Thun & Polo</span>
                        <span class="nav-badge hot">HOT</span>
                    </a>

                    <a href="products.php?cat=ao-so-mi" class="nav-link <?= ($activeCat === 'ao-so-mi') ? 'active' : '' ?>">
                        <span>Sơ Mi</span>
                    </a>

                    <a href="products.php?cat=ao-khoac-hoodie" class="nav-link <?= ($activeCat === 'ao-khoac-hoodie') ? 'active' : '' ?>">
                        <span>Áo Khoác</span>
                    </a>

                    <a href="products.php?cat=quan-jeans" class="nav-link <?= ($activeCat === 'quan-jeans') ? 'active' : '' ?>">
                        <span>Quần Jeans</span>
                    </a>

                    <a href="products.php?sort=discount" class="nav-link nav-sale-link">
                        <i class="fa-solid fa-fire text-danger"></i>
                        <span>Khuyến Mãi</span>
                    </a>
                </nav>

                <!-- Desktop Search Bar -->
                <form action="products.php" method="GET" class="header-search" id="header-search-form">
                    <i class="fa-solid fa-magnifying-glass search-icon"></i>
                    <input type="text" name="keyword" id="header-search-input" class="search-input" placeholder="Tìm kiếm áo thun, hoodie, quần jean..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>" autocomplete="off" spellcheck="false">
                    <button type="button" class="search-clear-btn" id="search-clear-btn" aria-label="Xóa từ khóa tìm kiếm" style="display: none;">
                        <i class="fa-solid fa-xmark"></i>
                    </button>
                    <div class="search-shortcut-hint">
                        <kbd>/</kbd>
                    </div>

                    <!-- Search Quick Suggestions & Live Results Dropdown -->
                    <div class="search-suggestions-dropdown" id="search-suggestions">
                        <!-- 1. Default Quick Trends (shown when keyword is empty) -->
                        <div class="suggestions-default-box" id="suggestions-default-box">
                            <div class="suggestions-header">
                                <span><i class="fa-solid fa-arrow-trend-up text-accent"></i> Xu Hướng Tìm Kiếm Phổ Biến</span>
                            </div>
                            <div class="suggestions-tags">
                                <a href="products.php?keyword=Áo+polo" class="suggestion-tag"><i class="fa-solid fa-magnifying-glass mr-1"></i> Áo polo nam</a>
                                <a href="products.php?keyword=Hoodie" class="suggestion-tag"><i class="fa-solid fa-magnifying-glass mr-1"></i> Hoodie nỉ bông</a>
                                <a href="products.php?keyword=Sơ+mi" class="suggestion-tag"><i class="fa-solid fa-magnifying-glass mr-1"></i> Sơ mi trắng Oxford</a>
                                <a href="products.php?keyword=Quần+jean" class="suggestion-tag"><i class="fa-solid fa-magnifying-glass mr-1"></i> Quần jean ống suông</a>
                                <a href="products.php?keyword=Oversize" class="suggestion-tag"><i class="fa-solid fa-magnifying-glass mr-1"></i> Áo oversize</a>
                            </div>
                        </div>

                        <!-- 2. Dynamic Live Search Results (populated via AJAX) -->
                        <div class="search-live-results" id="search-live-results" style="display: none;"></div>

                        <!-- 3. Footer Action -->
                        <div class="suggestions-footer" id="suggestions-footer" style="display: none;">
                            <a href="#" class="view-all-search-btn" id="view-all-search-btn">
                                <span>Xem tất cả kết quả cho "<strong id="search-keyword-display"></strong>"</span>
                                <i class="fa-solid fa-arrow-right"></i>
                            </a>
                        </div>
                    </div>
                </form>

                <!-- Actions: Mobile Search, User Menu, Cart -->
                <div class="header-actions">
                    <!-- Mobile Search Trigger Button -->
                    <button type="button" class="action-btn mobile-search-trigger" id="mobile-search-btn" title="Tìm kiếm" aria-label="Tìm kiếm">
                        <i class="fa-solid fa-magnifying-glass"></i>
                    </button>

                    <!-- User Menu Dropdown -->
                    <?php if ($currentUser): ?>
                        <div class="user-menu-wrapper" id="user-menu-wrapper">
                            <button type="button" class="action-btn user-avatar-btn" id="user-dropdown-btn" title="Tài khoản: <?= htmlspecialchars($currentUser['full_name']) ?>" aria-expanded="false">
                                <span class="user-avatar-initials">
                                    <?= mb_strtoupper(mb_substr($currentUser['full_name'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                                </span>
                                <span class="user-status-dot"></span>
                            </button>

                            <div class="user-dropdown-card" id="user-dropdown-card">
                                <div class="dropdown-user-header">
                                    <div class="user-avatar-large">
                                        <?= mb_strtoupper(mb_substr($currentUser['full_name'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                                    </div>
                                    <div class="user-info-text">
                                        <div class="user-name"><?= htmlspecialchars($currentUser['full_name']) ?></div>
                                        <div class="user-email"><?= htmlspecialchars($currentUser['email']) ?></div>
                                        <?php if ($currentUser['role'] === 'admin'): ?>
                                            <span class="user-role-badge admin"><i class="fa-solid fa-shield-halved"></i> Quản Trị Viên</span>
                                        <?php else: ?>
                                            <span class="user-role-badge customer"><i class="fa-solid fa-circle-check"></i> Khách Hàng VIP</span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <div class="dropdown-menu-list">
                                    <?php if ($currentUser['role'] === 'admin'): ?>
                                        <a href="admin/index.php" class="dropdown-item-link admin-highlight">
                                            <div class="dropdown-item-icon admin-icon">
                                                <i class="fa-solid fa-gauge-high"></i>
                                            </div>
                                            <div class="dropdown-item-content">
                                                <span class="item-title">Trang Quản Trị (Admin)</span>
                                                <span class="item-sub">Quản lý kho & đơn hàng</span>
                                            </div>
                                        </a>
                                        <div class="dropdown-separator"></div>
                                    <?php endif; ?>

                                    <a href="profile.php" class="dropdown-item-link">
                                        <div class="dropdown-item-icon">
                                            <i class="fa-solid fa-id-card"></i>
                                        </div>
                                        <div class="dropdown-item-content">
                                            <span class="item-title">Hồ sơ cá nhân</span>
                                            <span class="item-sub">Thông tin & địa chỉ nhận hàng</span>
                                        </div>
                                    </a>

                                    <a href="my_orders.php" class="dropdown-item-link">
                                        <div class="dropdown-item-icon">
                                            <i class="fa-solid fa-bag-shopping"></i>
                                        </div>
                                        <div class="dropdown-item-content">
                                            <span class="item-title">Đơn hàng của tôi</span>
                                            <span class="item-sub">Kiểm tra lịch sử mua sắm</span>
                                        </div>
                                    </a>

                                    <div class="dropdown-separator"></div>

                                    <a href="logout.php" class="dropdown-item-link logout-link">
                                        <div class="dropdown-item-icon danger">
                                            <i class="fa-solid fa-right-from-bracket"></i>
                                        </div>
                                        <div class="dropdown-item-content">
                                            <span class="item-title">Đăng xuất</span>
                                            <span class="item-sub">Rời phiên làm việc</span>
                                        </div>
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php else: ?>
                        <a href="login.php" class="action-btn auth-btn" title="Đăng nhập / Đăng ký" aria-label="Đăng nhập">
                            <i class="fa-regular fa-user"></i>
                        </a>
                    <?php endif; ?>

                    <!-- Cart Trigger Button -->
                    <a href="cart.php" class="action-btn cart-btn" id="header-cart-btn" title="Giỏ hàng" aria-label="Giỏ hàng">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span class="cart-badge <?= ($cartCount > 0) ? 'has-items' : '' ?>" id="cart-counter"><?= $cartCount ?></span>
                    </a>
                </div>
            </div>
        </div>
    </header>

    <!-- Mobile Fullscreen Search Overlay -->
    <div class="mobile-search-overlay" id="mobile-search-overlay">
        <div class="mobile-search-dialog">
            <div class="mobile-search-top">
                <form action="products.php" method="GET" class="mobile-search-form">
                    <i class="fa-solid fa-magnifying-glass mobile-search-icon"></i>
                    <input type="text" name="keyword" id="mobile-search-input" class="mobile-search-input" placeholder="Tìm kiếm trang phục yêu thích..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                    <button type="submit" class="mobile-search-submit">Tìm</button>
                </form>
                <button type="button" class="mobile-search-close" id="mobile-search-close" aria-label="Đóng">
                    <i class="fa-solid fa-xmark"></i>
                </button>
            </div>
            <div class="mobile-search-suggestions">
                <div class="suggestions-label"><i class="fa-solid fa-fire text-danger"></i> Xu Hướng Tìm Kiếm:</div>
                <div class="mobile-tag-cloud">
                    <a href="products.php?keyword=Áo+polo">Áo Polo</a>
                    <a href="products.php?keyword=Hoodie">Áo Hoodie</a>
                    <a href="products.php?keyword=Quần+jean">Quần Jean</a>
                    <a href="products.php?keyword=Sơ+mi">Sơ Mi Nam</a>
                    <a href="products.php?keyword=Váy+nữ">Váy Nữ</a>
                </div>
            </div>
        </div>
    </div>

    <!-- Mobile Offcanvas Navigation Drawer -->
    <div class="mobile-drawer-backdrop" id="mobile-drawer-backdrop"></div>
    <aside class="mobile-drawer" id="mobile-drawer" aria-label="Menu di động">
        <!-- Drawer Header -->
        <div class="drawer-header">
            <a href="index.php" class="drawer-logo">
                <img src="assets/images/logo.png" alt="HieuMini Logo">
            </a>
            <button type="button" class="drawer-close-btn" id="drawer-close-btn" aria-label="Đóng menu">
                <i class="fa-solid fa-xmark"></i>
            </button>
        </div>

        <!-- Drawer User Card -->
        <div class="drawer-user-section">
            <?php if ($currentUser): ?>
                <div class="drawer-user-card">
                    <div class="drawer-avatar">
                        <?= mb_strtoupper(mb_substr($currentUser['full_name'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                    </div>
                    <div class="drawer-user-meta">
                        <div class="drawer-name"><?= htmlspecialchars($currentUser['full_name']) ?></div>
                        <div class="drawer-email"><?= htmlspecialchars($currentUser['email']) ?></div>
                    </div>
                </div>
                <?php if ($currentUser['role'] === 'admin'): ?>
                    <a href="admin/index.php" class="drawer-admin-btn">
                        <i class="fa-solid fa-gauge-high"></i> Vào Trang Quản Trị Admin
                    </a>
                <?php endif; ?>
            <?php else: ?>
                <div class="drawer-guest-card">
                    <p class="drawer-guest-text">Đăng nhập để nhận ngay voucher <strong>HIEU10</strong> và theo dõi đơn hàng dễ dàng.</p>
                    <div class="drawer-guest-actions">
                        <a href="login.php" class="drawer-btn primary"><i class="fa-solid fa-arrow-right-to-bracket"></i> Đăng Nhập</a>
                        <a href="register.php" class="drawer-btn secondary"><i class="fa-solid fa-user-plus"></i> Đăng Ký</a>
                    </div>
                    <a href="admin/login.php" class="drawer-admin-btn" style="margin-top: 10px; background: rgba(245, 158, 11, 0.15); color: #fbbf24; border: 1px solid rgba(245, 158, 11, 0.3); text-align: center; justify-content: center; display: flex; align-items: center; gap: 6px; padding: 8px 12px; border-radius: 8px; font-weight: 700; text-decoration: none; font-size: 0.85rem;">
                        <i class="fa-solid fa-shield-halved"></i> Cổng Quản Trị Super Admin
                    </a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Drawer Search -->
        <div class="drawer-search-box">
            <form action="products.php" method="GET" class="drawer-search-form">
                <input type="text" name="keyword" class="drawer-search-input" placeholder="Tìm sản phẩm..." value="<?= isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : '' ?>">
                <button type="submit" class="drawer-search-submit"><i class="fa-solid fa-magnifying-glass"></i></button>
            </form>
        </div>

        <!-- Drawer Menu Navigation -->
        <div class="drawer-menu-wrapper">
            <div class="drawer-section-title">DANH MỤC SẢN PHẨM</div>
            <ul class="drawer-menu-list">
                <li class="drawer-menu-item">
                    <a href="index.php" class="drawer-link <?= ($currentScript === 'index.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-house-chimney"></i>
                        <span>Trang Chủ</span>
                    </a>
                </li>
                <li class="drawer-menu-item">
                    <a href="products.php" class="drawer-link <?= ($currentScript === 'products.php' && empty($activeCat)) ? 'active' : '' ?>">
                        <i class="fa-solid fa-layer-group"></i>
                        <span>Tất Cả Sản Phẩm</span>
                    </a>
                </li>
                <?php foreach ($headerCategories as $cat): 
                    $iconClass = isset($categoryIcons[$cat['slug']]) ? $categoryIcons[$cat['slug']] : 'fa-solid fa-tag';
                    $isCurrentCat = ($activeCat === $cat['slug']);
                ?>
                    <li class="drawer-menu-item">
                        <a href="products.php?cat=<?= urlencode($cat['slug']) ?>" class="drawer-link <?= $isCurrentCat ? 'active' : '' ?>">
                            <i class="<?= $iconClass ?>"></i>
                            <span><?= htmlspecialchars($cat['name']) ?></span>
                            <?php if ($cat['slug'] === 'ao-thun-polo'): ?>
                                <span class="drawer-badge hot">HOT</span>
                            <?php elseif ($cat['slug'] === 'ao-khoac-hoodie'): ?>
                                <span class="drawer-badge new">NEW</span>
                            <?php endif; ?>
                        </a>
                    </li>
                <?php endforeach; ?>
            </ul>

            <div class="drawer-section-title">TIỆN ÍCH & TÀI KHOẢN</div>
            <ul class="drawer-menu-list">
                <?php if ($currentUser): ?>
                    <li class="drawer-menu-item">
                        <a href="profile.php" class="drawer-link <?= ($currentScript === 'profile.php') ? 'active' : '' ?>">
                            <i class="fa-solid fa-id-card"></i>
                            <span>Hồ Sơ Cá Nhân</span>
                        </a>
                    </li>
                    <li class="drawer-menu-item">
                        <a href="my_orders.php" class="drawer-link <?= ($currentScript === 'my_orders.php') ? 'active' : '' ?>">
                            <i class="fa-solid fa-bag-shopping"></i>
                            <span>Đơn Hàng Của Tôi</span>
                        </a>
                    </li>
                <?php endif; ?>
                <li class="drawer-menu-item">
                    <a href="cart.php" class="drawer-link <?= ($currentScript === 'cart.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Giỏ Hàng</span>
                        <span class="drawer-badge primary"><?= $cartCount ?> sản phẩm</span>
                    </a>
                </li>
                <li class="drawer-menu-item">
                    <a href="#" data-open-modal="size-modal" class="drawer-link">
                        <i class="fa-solid fa-ruler-combined"></i>
                        <span>Bảng Quy Đổi Size</span>
                    </a>
                </li>
                <?php if ($currentUser): ?>
                    <li class="drawer-menu-item">
                        <a href="logout.php" class="drawer-link text-danger">
                            <i class="fa-solid fa-right-from-bracket"></i>
                            <span>Đăng Xuất</span>
                        </a>
                    </li>
                <?php endif; ?>
            </ul>
        </div>

        <!-- Drawer Footer -->
        <div class="drawer-footer">
            <div class="drawer-hotline">
                <i class="fa-solid fa-headset"></i>
                <div>
                    <div class="hotline-label">Hỗ trợ khách hàng 24/7</div>
                    <a href="tel:0988889999" class="hotline-phone">0988.889.999</a>
                </div>
            </div>
        </div>
    </aside>

    <!-- Global Toast Container -->
    <div id="toast-container"></div>

    <main class="main-content">
        <div class="container" style="padding-top: 15px;">
            <?php display_flash(); ?>
        </div>
