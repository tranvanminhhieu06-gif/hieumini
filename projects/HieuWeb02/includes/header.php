<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth_check.php';

$cart_count = get_cart_count();
$categories = get_categories($pdo);
$user = current_user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($page_title) ? $page_title . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="HieuMini - Hệ thống siêu thị bán lẻ sản phẩm công nghệ, điện thoại iPhone, Samsung, Laptop MacBook, Gaming ROG, Tablet, Smartwatch chính hãng giá tốt nhất.">
    
    <!-- Google Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    
    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

    <!-- Top Promotion Bar -->
    <div class="top-bar">
        <div class="container">
            <div><i class="fa-solid fa-bolt text-warning"></i> <strong>FLASH SALE 2026:</strong> Nhập mã <strong>HIEUMINI2026</strong> giảm ngay 10% đơn từ 5 Triệu!</div>
            <div>
                <i class="fa-solid fa-phone-volume"></i> Hotline: <a href="tel:19008888">1900 8888</a> (Miễn phí)
            </div>
        </div>
    </div>

    <!-- Main Header -->
    <header class="site-header">
        <div class="container">
            <div class="navbar">
                <!-- Logo Brand -->
                <a href="index.php" class="brand-logo" id="logo-brand">
                    <i class="fa-solid fa-microchip"></i>
                    <span>HieuMini<span style="color: var(--accent);">.vn</span></span>
                </a>

                <!-- Search Box -->
                <div class="header-search">
                    <form action="products.php" method="GET" id="main-search-form">
                        <input type="text" name="keyword" placeholder="Bạn cần tìm iPhone 16, MacBook Pro M3, ROG..." value="<?php echo isset($_GET['keyword']) ? htmlspecialchars($_GET['keyword']) : ''; ?>" required>
                        <button type="submit" aria-label="Tìm kiếm"><i class="fa-solid fa-magnifying-glass"></i></button>
                    </form>
                </div>

                <!-- Nav Actions (Cart, User, Admin) -->
                <div class="nav-actions">
                    <?php if ($user): ?>
                        <div class="nav-user-dropdown" style="position: relative; display: inline-block;">
                            <a href="profile.php" class="nav-item-btn">
                                <i class="fa-solid fa-circle-user" style="color: var(--primary);"></i>
                                <span><?php echo htmlspecialchars($user['full_name']); ?></span>
                            </a>
                        </div>
                        <?php if ($user['role'] === 'admin'): ?>
                            <a href="admin/index.php" class="nav-item-btn" style="background: rgba(99, 102, 241, 0.2); border-color: var(--primary);">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Quản trị</span>
                            </a>
                        <?php endif; ?>
                        <a href="my_orders.php" class="nav-item-btn" title="Đơn mua của tôi">
                            <i class="fa-solid fa-receipt"></i>
                            <span>Đơn hàng</span>
                        </a>
                        <a href="logout.php" class="nav-item-btn" style="color: var(--danger);" title="Đăng xuất">
                            <i class="fa-solid fa-right-from-bracket" style="color: var(--danger);"></i>
                        </a>
                    <?php else: ?>
                        <a href="login.php" class="nav-item-btn">
                            <i class="fa-solid fa-user"></i>
                            <span>Đăng nhập</span>
                        </a>
                        <a href="register.php" class="nav-item-btn" style="background: rgba(99, 102, 241, 0.15); border-color: rgba(99,102,241,0.3);">
                            <i class="fa-solid fa-user-plus"></i>
                            <span>Đăng ký</span>
                        </a>
                    <?php endif; ?>

                    <!-- Cart Button -->
                    <a href="cart.php" class="nav-item-btn cart-btn" id="header-cart-btn">
                        <i class="fa-solid fa-cart-shopping"></i>
                        <span>Giỏ hàng</span>
                        <?php if ($cart_count > 0): ?>
                            <span class="badge-count"><?php echo $cart_count; ?></span>
                        <?php endif; ?>
                    </a>
                </div>
            </div>
        </div>

        <!-- Categories Strip Navigation -->
        <div class="category-nav">
            <div class="container">
                <ul class="category-menu">
                    <li>
                        <a href="products.php" class="<?php echo !isset($_GET['cat']) ? 'active' : ''; ?>">
                            <i class="fa-solid fa-border-all"></i> Tất cả sản phẩm
                        </a>
                    </li>
                    <?php foreach ($categories as $cat): ?>
                        <li>
                            <a href="products.php?cat=<?php echo $cat['id']; ?>" class="<?php echo (isset($_GET['cat']) && $_GET['cat'] == $cat['id']) ? 'active' : ''; ?>">
                                <i class="fa-solid <?php echo htmlspecialchars($cat['icon'] ?? 'fa-tag'); ?>"></i>
                                <?php echo htmlspecialchars($cat['name'] ?? ''); ?>
                            </a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </header>

    <!-- Global Toast & Flash Message Container -->
    <div class="container" style="margin-top: 20px;">
        <?php echo display_flash(); ?>
    </div>
