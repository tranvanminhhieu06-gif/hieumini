<?php
// includes/navbar.php
$categories_stmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
$all_categories = $categories_stmt->fetchAll();
?>
<!-- Top Announcement Bar -->
<div class="topbar">
  <div class="container">
    <div>
      <span class="topbar-badge">HOT DEAL</span>
      <span>Miễn phí vận chuyển toàn quốc cho đơn hàng từ <strong>250.000 đ</strong></span>
    </div>
    <div style="display: flex; gap: 20px; font-size: 0.82rem;">
      <span><i class="bi bi-telephone-fill"></i> Hotline: 090.123.4567</span>
      <span><i class="bi bi-geo-alt-fill"></i> Hà Nội, Việt Nam</span>
    </div>
  </div>
</div>

<!-- Main Sticky Navbar -->
<header class="navbar-sticky">
  <div class="container">
    <div class="navbar-inner">
      <!-- Left: Mobile Menu Toggle & Brand Logo -->
      <div style="display: flex; align-items: center; gap: 12px;">
        <button type="button" class="mobile-menu-toggle" id="mobileMenuOpenBtn" aria-label="Mở menu điều hướng">
          <i class="bi bi-list"></i>
        </button>

        <!-- Logo -->
        <a href="index.php" class="brand-logo">
          <div class="logo-icon">
            <i class="bi bi-feather"></i>
          </div>
          <div class="brand-name">
            Hieu<span>Mini</span>
          </div>
        </a>
      </div>

      <!-- Search Bar -->
      <div class="search-container">
        <form action="products.php" method="GET" class="search-form">
          <input type="text" name="q" id="globalSearchInput" class="search-input" placeholder="Tìm kiếm bút pastel, sổ còng, màu vẽ, máy tính..." autocomplete="off" value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
          <button type="submit" class="search-btn" aria-label="Tìm kiếm">
            <i class="bi bi-search"></i>
          </button>
        </form>
        <!-- Dynamic search suggestions container -->
        <div id="searchSuggestions" class="search-suggestions"></div>
      </div>

      <!-- Desktop Navigation Links -->
      <nav class="nav-links">
        <a href="index.php" class="nav-link <?= $current_page === 'index.php' ? 'active' : '' ?>">
          <i class="bi bi-house-door"></i> <span>Trang Chủ</span>
        </a>
        <a href="products.php" class="nav-link <?= $current_page === 'products.php' ? 'active' : '' ?>">
          <i class="bi bi-grid"></i> <span>Sản Phẩm</span>
        </a>
        <a href="about.php" class="nav-link <?= $current_page === 'about.php' ? 'active' : '' ?>">
          <i class="bi bi-info-circle"></i> <span>Giới Thiệu</span>
        </a>
        <a href="contact.php" class="nav-link <?= $current_page === 'contact.php' ? 'active' : '' ?>">
          <i class="bi bi-envelope"></i> <span>Liên Hệ</span>
        </a>
      </nav>

      <!-- Action Buttons -->
      <div class="nav-actions">
        <!-- Shopping Cart Icon -->
        <a href="cart.php" class="action-btn cart-action-btn" title="Giỏ hàng" aria-label="Giỏ hàng">
          <i class="bi bi-bag"></i>
          <span class="cart-badge"><?= $cart_count ?></span>
        </a>

        <!-- User Account Menu -->
        <?php if (is_logged_in()): $u = current_user(); ?>
        <div class="user-dropdown">
          <button class="action-btn user-btn" aria-label="Tài khoản người dùng">
            <i class="bi bi-person-circle"></i>
            <span class="user-name-text"><?= htmlspecialchars($u['fullname']) ?></span>
            <i class="bi bi-chevron-down chevron-icon"></i>
          </button>
          <div class="dropdown-menu">
            <div class="dropdown-header">
              Xin chào, <strong><?= htmlspecialchars($u['fullname']) ?></strong>
              <div class="dropdown-role"><?= $u['role'] === 'admin' ? 'Quản trị viên' : 'Thành viên' ?></div>
            </div>
            <?php if (is_admin()): ?>
            <a href="admin/index.php" class="dropdown-item dropdown-admin">
              <i class="bi bi-speedometer2"></i> Trang Quản Trị Admin
            </a>
            <div class="dropdown-divider"></div>
            <?php endif; ?>
            <a href="profile.php" class="dropdown-item">
              <i class="bi bi-person-gear"></i> Tài khoản & Đơn hàng
            </a>
            <div class="dropdown-divider"></div>
            <a href="logout.php" class="dropdown-item dropdown-logout">
              <i class="bi bi-box-arrow-right"></i> Đăng xuất
            </a>
          </div>
        </div>
        <?php else: ?>
        <a href="login.php" class="btn btn-primary btn-sm login-nav-btn">
          <i class="bi bi-person-fill"></i> <span>Đăng Nhập</span>
        </a>
        <?php endif; ?>
      </div>
    </div>
  </div>
</header>

<!-- Mobile Navigation Drawer & Backdrop -->
<div class="mobile-drawer-backdrop" id="mobileDrawerBackdrop"></div>

<div class="mobile-nav-drawer" id="mobileNavDrawer">
  <div class="mobile-drawer-header">
    <a href="index.php" class="brand-logo">
      <div class="logo-icon">
        <i class="bi bi-feather"></i>
      </div>
      <div class="brand-name">
        Hieu<span>Mini</span>
      </div>
    </a>
    <button type="button" class="mobile-drawer-close" id="mobileMenuCloseBtn" aria-label="Đóng menu">
      <i class="bi bi-x-lg"></i>
    </button>
  </div>

  <!-- Mobile Search -->
  <div class="mobile-drawer-search">
    <form action="products.php" method="GET" class="search-form">
      <input type="text" name="q" class="search-input" placeholder="Tìm bút, sổ, màu vẽ..." value="<?= htmlspecialchars($_GET['q'] ?? '') ?>">
      <button type="submit" class="search-btn" aria-label="Tìm kiếm"><i class="bi bi-search"></i></button>
    </form>
  </div>

  <div class="mobile-drawer-body">
    <div class="mobile-drawer-section-title">ĐIỀU HƯỚNG TRANG</div>
    <nav class="mobile-nav-list">
      <a href="index.php" class="mobile-nav-item <?= $current_page === 'index.php' ? 'active' : '' ?>">
        <i class="bi bi-house-door"></i> Trang Chủ
      </a>
      <a href="products.php" class="mobile-nav-item <?= $current_page === 'products.php' ? 'active' : '' ?>">
        <i class="bi bi-grid"></i> Tất Cả Sản Phẩm
      </a>
      <a href="about.php" class="mobile-nav-item <?= $current_page === 'about.php' ? 'active' : '' ?>">
        <i class="bi bi-info-circle"></i> Giới Thiệu
      </a>
      <a href="contact.php" class="mobile-nav-item <?= $current_page === 'contact.php' ? 'active' : '' ?>">
        <i class="bi bi-envelope"></i> Liên Hệ Hỗ Trợ
      </a>
    </nav>

    <div class="mobile-drawer-section-title">DANH MỤC ĐỒ DÙNG HỌC TẬP</div>
    <nav class="mobile-nav-list">
      <?php foreach ($all_categories as $cat): ?>
      <a href="products.php?category=<?= $cat['id'] ?>" class="mobile-nav-item">
        <i class="bi <?= htmlspecialchars($cat['icon']) ?>"></i> <?= htmlspecialchars($cat['name']) ?>
      </a>
      <?php endforeach; ?>
    </nav>
  </div>

  <div class="mobile-drawer-footer">
    <?php if (is_logged_in()): ?>
      <a href="profile.php" class="btn btn-outline btn-sm" style="width: 100%; justify-content: center;">
        <i class="bi bi-person-gear"></i> Tài khoản của tôi
      </a>
      <?php if (is_admin()): ?>
      <a href="admin/index.php" class="btn btn-secondary btn-sm" style="width: 100%; justify-content: center; margin-top: 8px;">
        <i class="bi bi-speedometer2"></i> Trang Admin
      </a>
      <?php endif; ?>
      <a href="logout.php" class="btn btn-outline btn-sm" style="width: 100%; justify-content: center; margin-top: 8px; color: #ef4444; border-color: #fee2e2;">
        <i class="bi bi-box-arrow-right"></i> Đăng xuất
      </a>
    <?php else: ?>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px;">
        <a href="login.php" class="btn btn-primary btn-sm" style="justify-content: center;">
          <i class="bi bi-box-arrow-in-right"></i> Đăng Nhập
        </a>
        <a href="register.php" class="btn btn-outline btn-sm" style="justify-content: center;">
          <i class="bi bi-person-plus"></i> Đăng Ký
        </a>
      </div>
    <?php endif; ?>
    <div style="margin-top: 14px; text-align: center; font-size: 0.8rem; color: var(--muted);">
      <i class="bi bi-telephone-fill"></i> Hotline: <strong>090.123.4567</strong>
    </div>
  </div>
</div>

