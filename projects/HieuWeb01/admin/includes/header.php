<?php
/**
 * Admin Header & Sidebar Template - UI/UX Pro Max Edition
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Kiểm tra quyền Admin
if (!is_logged_in() || !is_admin()) {
    set_flash('danger', 'Bạn cần đăng nhập bằng tài khoản Quản trị viên để truy cập trang này.');
    redirect('login.php?redirect=' . urlencode($_SERVER['REQUEST_URI']));
}

$currentUser = current_user($pdo);
$activePage = basename($_SERVER['PHP_SELF']);

// Số đơn hàng đang chờ xử lý
$pendingCount = (int)$pdo->query("SELECT COUNT(*) FROM orders WHERE order_status = 'pending'")->fetchColumn();

// Lấy 3 đơn hàng mới nhất để hiển thị trong Notification Dropdown
$recentOrdersStmt = $pdo->query("SELECT id, order_code, customer_name, total_amount, created_at FROM orders WHERE order_status = 'pending' ORDER BY id DESC LIMIT 3");
$recentPendingOrders = $recentOrdersStmt->fetchAll();

// Breadcrumb Title mapping
$pageTitles = [
    'index.php' => 'Bảng Điều Khiển (Dashboard)',
    'analytics.php' => 'Báo Cáo & Thống Kê Phân Tích',
    'products.php' => 'Quản Lý Sản Phẩm',
    'product_add.php' => 'Thêm Sản Phẩm Mới',
    'product_edit.php' => 'Chỉnh Sửa Sản Phẩm',
    'categories.php' => 'Quản Lý Danh Mục',
    'orders.php' => 'Quản Lý Đơn Hàng',
    'order_detail.php' => 'Chi Tiết Đơn Hàng',
    'coupons.php' => 'Mã Giảm Giá & Khuyến Mãi',
    'users.php' => 'Khách Hàng & Phân Quyền',
    'settings.php' => 'Cài Đặt Cửa Hàng & Hệ Thống'
];
$currentPageName = isset($pageTitles[$activePage]) ? $pageTitles[$activePage] : (isset($adminTitle) ? $adminTitle : 'Quản Trị');
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($adminTitle) ? htmlspecialchars($adminTitle) . ' - Quản Trị HieuMini' : 'Hệ Thống Quản Trị HieuMini Pro Max' ?></title>
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Pro/Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Main Style & Admin Pro Max Style -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

    <!-- Admin Mobile Backdrop Overlay -->
    <div class="admin-sidebar-backdrop" id="admin-sidebar-backdrop"></div>

    <!-- Admin Sidebar Pro Max -->
    <aside class="admin-sidebar" id="admin-sidebar">
        <!-- Sidebar Brand Header -->
        <div class="sidebar-brand">
            <a href="index.php" class="brand-link">
                <div class="brand-badge-icon">
                    <i class="fa-solid fa-cube"></i>
                </div>
                <div class="brand-text-wrap">
                    <div class="brand-title">HieuMini</div>
                    <div class="brand-tag">PRO CONTROL</div>
                </div>
            </a>
            <button type="button" class="sidebar-collapse-pin" id="sidebar-collapse-pin" title="Thu gọn Sidebar" aria-label="Thu gọn Sidebar">
                <i class="fa-solid fa-angles-left"></i>
            </button>
        </div>

        <!-- Admin Profile Quick Card -->
        <div class="sidebar-user-card">
            <div class="user-avatar-wrap">
                <span class="user-avatar-text">
                    <?= mb_strtoupper(mb_substr($currentUser['full_name'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                </span>
                <span class="online-indicator" title="Đang trực tuyến"></span>
            </div>
            <div class="user-meta-wrap">
                <div class="user-display-name"><?= htmlspecialchars($currentUser['full_name']) ?></div>
                <div class="user-role-label"><i class="fa-solid fa-shield-check"></i> Super Admin</div>
            </div>
        </div>

        <!-- Sidebar Navigation Menu with Grouped Sections -->
        <div class="sidebar-menu-scroll">
            <ul class="sidebar-menu">
                <!-- Group 1: TỔNG QUAN -->
                <li class="menu-section-header">TỔNG QUAN</li>
                <li class="sidebar-item">
                    <a href="index.php" class="sidebar-link <?= ($activePage === 'index.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-chart-pie menu-icon"></i>
                        <span class="link-label">Bảng Điều Khiển</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="analytics.php" class="sidebar-link <?= ($activePage === 'analytics.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-chart-line menu-icon"></i>
                        <span class="link-label">Báo Cáo & Phân Tích</span>
                    </a>
                </li>

                <!-- Group 2: QUẢN LÝ CỬA HÀNG -->
                <li class="menu-section-header">CỬA HÀNG & BÁN HÀNG</li>
                <li class="sidebar-item">
                    <a href="products.php" class="sidebar-link <?= in_array($activePage, ['products.php', 'product_add.php', 'product_edit.php']) ? 'active' : '' ?>">
                        <i class="fa-solid fa-shirt menu-icon"></i>
                        <span class="link-label">Quản Lý Sản Phẩm</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="categories.php" class="sidebar-link <?= ($activePage === 'categories.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-layer-group menu-icon"></i>
                        <span class="link-label">Danh Mục Hàng Hóa</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="orders.php" class="sidebar-link <?= in_array($activePage, ['orders.php', 'order_detail.php']) ? 'active' : '' ?>">
                        <i class="fa-solid fa-bag-shopping menu-icon"></i>
                        <span class="link-label">Quản Lý Đơn Hàng</span>
                        <?php if ($pendingCount > 0): ?>
                            <span class="sidebar-counter-badge pulse" title="<?= $pendingCount ?> đơn hàng chờ duyệt">
                                <?= $pendingCount ?>
                            </span>
                        <?php endif; ?>
                    </a>
                </li>

                <!-- Group 3: MARKETING & KHÁCH HÀNG -->
                <li class="menu-section-header">MARKETING & KHÁCH HÀNG</li>
                <li class="sidebar-item">
                    <a href="coupons.php" class="sidebar-link <?= ($activePage === 'coupons.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-ticket-simple menu-icon"></i>
                        <span class="link-label">Mã Giảm Giá (Coupons)</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="users.php" class="sidebar-link <?= ($activePage === 'users.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-users-gear menu-icon"></i>
                        <span class="link-label">Khách Hàng & Phân Quyền</span>
                    </a>
                </li>

                <!-- Group 4: HỆ THỐNG & ĐIỀU HƯỚNG -->
                <li class="menu-section-header">HỆ THỐNG & CÀI ĐẶT</li>
                <li class="sidebar-item">
                    <a href="settings.php" class="sidebar-link <?= ($activePage === 'settings.php') ? 'active' : '' ?>">
                        <i class="fa-solid fa-sliders menu-icon"></i>
                        <span class="link-label">Cài Đặt Hệ Thống</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../index.php" target="_blank" class="sidebar-link storefront-link">
                        <i class="fa-solid fa-arrow-up-right-from-square menu-icon"></i>
                        <span class="link-label">Xem Website Bán Hàng</span>
                        <span class="ext-badge">Live</span>
                    </a>
                </li>
                <li class="sidebar-item">
                    <a href="../logout.php" class="sidebar-link logout-link">
                        <i class="fa-solid fa-power-off menu-icon"></i>
                        <span class="link-label">Đăng Xuất Hệ Thống</span>
                    </a>
                </li>
            </ul>
        </div>

        <!-- Sidebar Footer Status -->
        <div class="sidebar-footer">
            <div class="system-status-indicator">
                <span class="status-pulse-dot"></span>
                <span class="status-text">Server: Online (v2.6 Pro)</span>
            </div>
        </div>
    </aside>

    <!-- Main Content Area -->
    <div class="admin-main">
        <!-- Admin Topbar Glassmorphism Pro Max -->
        <header class="admin-topbar">
            <!-- Left Side: Toggle & Breadcrumb -->
            <div class="topbar-left-group">
                <button type="button" class="admin-toggle-btn" id="admin-sidebar-toggle" aria-label="Đóng/Mở Sidebar">
                    <i class="fa-solid fa-bars-staggered"></i>
                </button>
                <nav class="admin-breadcrumbs" aria-label="Breadcrumb">
                    <a href="index.php" class="bc-home"><i class="fa-solid fa-house"></i> Admin</a>
                    <span class="bc-separator"><i class="fa-solid fa-chevron-right"></i></span>
                    <span class="bc-current"><?= htmlspecialchars($currentPageName) ?></span>
                </nav>
            </div>

            <!-- Right Side Actions: Live Store Link, Notifications, Profile Dropdown -->
            <div class="topbar-right-group">
                <!-- Quick Store Link Button -->
                <a href="../index.php" target="_blank" class="admin-action-pill store-pill" title="Mở trang bán hàng khách">
                    <i class="fa-solid fa-store"></i>
                    <span class="pill-label">Cửa Hàng</span>
                </a>

                <!-- Notification Bell Dropdown -->
                <div class="admin-notification-wrapper" id="admin-notification-wrapper">
                    <button type="button" class="admin-icon-btn notification-bell-btn" id="notification-toggle-btn" title="Thông báo hệ thống" aria-expanded="false">
                        <i class="fa-regular fa-bell"></i>
                        <?php if ($pendingCount > 0): ?>
                            <span class="notification-badge-dot"><?= $pendingCount ?></span>
                        <?php endif; ?>
                    </button>

                    <!-- Notifications Dropdown Card -->
                    <div class="admin-notification-dropdown" id="notification-dropdown">
                        <div class="noti-header">
                            <div class="noti-header-title">
                                <i class="fa-solid fa-bell text-warning"></i>
                                <span>Thông Báo Quản Trị</span>
                            </div>
                            <span class="noti-count-badge"><?= $pendingCount ?> mới</span>
                        </div>
                        <div class="noti-list">
                            <?php if (!empty($recentPendingOrders)): ?>
                                <?php foreach ($recentPendingOrders as $pOrder): ?>
                                    <a href="order_detail.php?id=<?= $pOrder['id'] ?>" class="noti-item unread">
                                        <div class="noti-item-icon warning">
                                            <i class="fa-solid fa-bag-shopping"></i>
                                        </div>
                                        <div class="noti-item-content">
                                            <div class="noti-item-text">
                                                Đơn hàng <strong>#<?= htmlspecialchars($pOrder['order_code']) ?></strong> đang chờ duyệt
                                            </div>
                                            <div class="noti-item-meta">
                                                <span><?= htmlspecialchars($pOrder['customer_name']) ?></span> • <strong><?= format_currency($pOrder['total_amount']) ?></strong>
                                            </div>
                                        </div>
                                    </a>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <div class="noti-empty">
                                    <i class="fa-solid fa-circle-check text-success"></i>
                                    <p>Không có đơn hàng nào cần xử lý gấp!</p>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="noti-footer">
                            <a href="orders.php">Xem tất cả đơn hàng <i class="fa-solid fa-arrow-right"></i></a>
                        </div>
                    </div>
                </div>

                <!-- Admin User Profile Dropdown -->
                <div class="admin-user-menu-wrapper" id="admin-user-menu-wrapper">
                    <button type="button" class="admin-user-pill-btn" id="admin-user-btn" aria-expanded="false">
                        <div class="admin-pill-avatar">
                            <?= mb_strtoupper(mb_substr($currentUser['full_name'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                        </div>
                        <div class="admin-pill-text">
                            <span class="admin-pill-name"><?= htmlspecialchars($currentUser['full_name']) ?></span>
                            <span class="admin-pill-role">Administrator</span>
                        </div>
                        <i class="fa-solid fa-chevron-down admin-pill-arrow"></i>
                    </button>

                    <!-- Admin Profile Dropdown Menu -->
                    <div class="admin-user-dropdown" id="admin-user-dropdown">
                        <div class="admin-dd-header">
                            <div class="dd-avatar-big">
                                <?= mb_strtoupper(mb_substr($currentUser['full_name'], 0, 1, 'UTF-8'), 'UTF-8') ?>
                            </div>
                            <div class="dd-user-details">
                                <div class="dd-user-name"><?= htmlspecialchars($currentUser['full_name']) ?></div>
                                <div class="dd-user-email"><?= htmlspecialchars($currentUser['email']) ?></div>
                            </div>
                        </div>
                        <div class="admin-dd-links">
                            <a href="../profile.php" class="admin-dd-link">
                                <i class="fa-solid fa-user-gear"></i>
                                <span>Hồ sơ cá nhân</span>
                            </a>
                            <a href="index.php" class="admin-dd-link">
                                <i class="fa-solid fa-gauge-high"></i>
                                <span>Bảng điều khiển</span>
                            </a>
                            <div class="admin-dd-divider"></div>
                            <a href="../logout.php" class="admin-dd-link logout">
                                <i class="fa-solid fa-right-from-bracket"></i>
                                <span>Đăng xuất</span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </header>

        <!-- Admin Body Content -->
        <div class="admin-content">
            <?php display_flash(); ?>
