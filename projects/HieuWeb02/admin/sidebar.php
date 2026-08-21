<?php
$current_page = basename($_SERVER['PHP_SELF']);
$admin_user = current_user();
?>
<aside class="admin-sidebar">
    <div class="admin-brand">
        <i class="fa-solid fa-microchip"></i>
        <span>HieuMini <span style="font-size: 0.8rem; color: var(--accent); font-weight: 600;">ADMIN</span></span>
    </div>

    <div class="admin-menu">
        <div class="menu-label">Bảng Điều Khiển</div>
        <a href="index.php" class="admin-nav-item <?php echo $current_page === 'index.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-chart-pie"></i>
            <span>Tổng quan & KPI</span>
        </a>

        <div class="menu-label" style="margin-top: 14px;">Quản Lý Bán Hàng</div>
        <a href="products.php" class="admin-nav-item <?php echo in_array($current_page, ['products.php', 'product_add.php', 'product_edit.php']) ? 'active' : ''; ?>">
            <i class="fa-solid fa-boxes-stacked"></i>
            <span>Sản phẩm</span>
        </a>
        <a href="categories.php" class="admin-nav-item <?php echo $current_page === 'categories.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-layer-group"></i>
            <span>Danh mục</span>
        </a>
        <a href="orders.php" class="admin-nav-item <?php echo $current_page === 'orders.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-cart-flatbed"></i>
            <span>Đơn hàng</span>
        </a>

        <div class="menu-label" style="margin-top: 14px;">Hệ Thống</div>
        <a href="users.php" class="admin-nav-item <?php echo $current_page === 'users.php' ? 'active' : ''; ?>">
            <i class="fa-solid fa-users-gear"></i>
            <span>Người dùng</span>
        </a>
    </div>

    <div class="admin-sidebar-footer">
        <a href="../index.php" target="_blank" class="btn btn-outline btn-sm" style="width: 100%; margin-bottom: 8px;">
            <i class="fa-solid fa-arrow-up-right-from-square"></i> Xem Website
        </a>
        <a href="../logout.php" class="btn btn-danger btn-sm" style="width: 100%;">
            <i class="fa-solid fa-power-off"></i> Đăng xuất
        </a>
    </div>
</aside>
