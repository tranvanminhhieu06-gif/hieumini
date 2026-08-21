<?php
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/functions.php';

// Fetch all categories for navigation
try {
    $catStmt = $pdo->query("SELECT * FROM categories ORDER BY id ASC");
    $categories = $catStmt->fetchAll();
} catch (Exception $e) {
    $categories = [];
}

$cartCount = get_cart_count();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($page_title) ? htmlspecialchars($page_title) . ' | DatCyber' : 'DatCyber - Đồ Gia Dụng Thông Minh & Tiện Ích Gia Đình Cao Cấp'; ?></title>
  <meta name="description" content="DatCyber chuyên phân phối đồ gia dụng thông minh chính hãng: Nồi chiên không dầu, robot hút bụi, máy ép chậm, máy lọc không khí cao cấp, bảo hành 24 tháng.">
  
  <!-- Favicon -->
  <link rel="icon" href="data:image/svg+xml,<svg xmlns=%22http://www.w3.org/2000/svg%22 viewBox=%220 0 100 100%22><text y=%22.9em%22 font-size=%2290%22>🏠</text></svg>">
  
  <!-- Google Fonts: Plus Jakarta Sans & Outfit (Vietnamese Supported) -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  
  <!-- FontAwesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  
  <!-- Custom CSS -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

  <!-- Top Bar -->
  <div class="top-bar">
    <div class="container d-flex justify-content-between align-items-center">
      <div class="d-flex align-items-center gap-3">
        <span><i class="fas fa-truck-fast text-info me-1"></i> Miễn phí vận chuyển toàn quốc đơn từ 500k</span>
        <span class="d-none d-md-inline">|</span>
        <span class="d-none d-md-inline"><i class="fas fa-shield-halved text-success me-1"></i> Bảo hành chính hãng 24 tháng 1 đổi 1</span>
      </div>
      <div class="d-flex align-items-center gap-3">
        <a href="tel:19006868"><i class="fas fa-headset me-1"></i> Hotline: <strong>1900 6868</strong></a>
        <span>|</span>
        <?php if (is_admin_logged_in()): ?>
          <a href="admin/index.php" class="text-warning"><i class="fas fa-user-gear me-1"></i> Trang Admin (<?php echo htmlspecialchars($_SESSION['admin_user']['name']); ?>)</a>
        <?php else: ?>
          <a href="admin/login.php" class="text-warning"><i class="fas fa-user-shield me-1"></i> Quản trị Admin</a>
        <?php endif; ?>
      </div>
    </div>
  </div>

  <!-- Main Navbar -->
  <header class="navbar-main">
    <div class="container py-2 d-flex align-items-center justify-content-between gap-3">
      
      <!-- Brand Logo -->
      <a href="index.php" class="navbar-brand-custom">
        <i class="fas fa-home-heart text-primary"></i>
        <span>Dat<strong class="text-primary">Cyber</strong></span>
        <span class="brand-badge">2026</span>
      </a>

      <!-- Search Bar (Desktop) -->
      <div class="search-container d-none d-lg-block">
        <form action="products.php" method="GET">
          <i class="fas fa-search search-icon"></i>
          <input type="text" name="search" class="search-input" placeholder="Tìm kiếm robot hút bụi, nồi chiên, máy lọc khí..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        </form>
      </div>

      <!-- Actions -->
      <div class="d-flex align-items-center gap-2">
        <a href="contact.php" class="nav-action-btn d-none d-sm-flex">
          <i class="fas fa-location-dot text-primary"></i>
          <span>Showroom</span>
        </a>

        <!-- Cart Drawer Trigger -->
        <a href="#" class="nav-action-btn trigger-cart-drawer">
          <div class="cart-icon-wrapper">
            <i class="fas fa-bag-shopping text-primary"></i>
            <span class="cart-badge-count"><?php echo $cartCount; ?></span>
          </div>
          <span class="d-none d-md-inline">Giỏ hàng</span>
        </a>
      </div>
    </div>

    <!-- Mobile Search Bar -->
    <div class="container d-block d-lg-none pb-2 pt-1">
      <form action="products.php" method="GET" class="position-relative">
        <input type="text" name="search" class="form-control rounded-pill ps-4 py-2 border-primary border-opacity-50" style="font-size:0.9rem;" placeholder="Tìm thiết bị gia dụng thông minh..." value="<?php echo isset($_GET['search']) ? htmlspecialchars($_GET['search']) : ''; ?>">
        <button type="submit" class="btn btn-primary-custom position-absolute top-50 end-0 translate-middle-y rounded-pill me-1 py-1 px-3 btn-sm">
          <i class="fas fa-search"></i>
        </button>
      </form>
    </div>

    <!-- Category Nav Bar -->
    <div class="category-nav-bar">
      <div class="container d-flex align-items-center justify-content-between overflow-x-auto">
        <div class="d-flex align-items-center gap-1">
          <a href="index.php" class="cat-nav-link <?php echo $current_page == 'index.php' ? 'active' : ''; ?>">
            <i class="fas fa-house"></i> Trang chủ
          </a>
          <a href="products.php" class="cat-nav-link <?php echo $current_page == 'products.php' && !isset($_GET['category']) ? 'active' : ''; ?>">
            <i class="fas fa-boxes-stacked"></i> Tất cả sản phẩm
          </a>
          <?php foreach ($categories as $cat): ?>
            <a href="products.php?category=<?php echo urlencode($cat['slug'] ?? ''); ?>" class="cat-nav-link <?php echo (isset($_GET['category']) && $_GET['category'] == ($cat['slug'] ?? '')) ? 'active' : ''; ?>">
              <i class="fas <?php echo htmlspecialchars($cat['icon'] ?? 'fa-cube'); ?>"></i> <?php echo htmlspecialchars($cat['name'] ?? ''); ?>
            </a>
          <?php endforeach; ?>
        </div>
        <div class="d-none d-lg-flex align-items-center gap-2">
          <a href="about.php" class="cat-nav-link <?php echo $current_page == 'about.php' ? 'active' : ''; ?>">Về DatCyber</a>
          <a href="contact.php" class="cat-nav-link <?php echo $current_page == 'contact.php' ? 'active' : ''; ?>">Liên hệ</a>
        </div>
      </div>
    </div>
  </header>

  <!-- Flash Message Display -->
  <?php $flash = get_flash(); if ($flash): ?>
    <div class="container mt-3">
      <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : htmlspecialchars($flash['type']); ?> alert-dismissible fade show shadow-sm" role="alert">
        <?php echo htmlspecialchars($flash['message']); ?>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
      </div>
    </div>
  <?php endif; ?>

  <!-- Slide-out Cart Drawer -->
  <div class="cart-drawer-overlay" id="cartDrawerOverlay">
    <div class="cart-drawer">
      <div class="cart-drawer-header">
        <h5 class="m-0 fw-bold"><i class="fas fa-bag-shopping text-primary me-2"></i> Giỏ Hàng Của Bạn</h5>
        <button class="btn-close" id="closeCartDrawer"></button>
      </div>
      <div class="cart-drawer-body" id="cartDrawerBody">
        <!-- Loaded dynamically via AJAX -->
      </div>
      <div class="cart-drawer-footer" id="cartDrawerFooter">
        <!-- Loaded dynamically via AJAX -->
      </div>
    </div>
  </div>

  <!-- Quick View Modal -->
  <div class="modal fade" id="quickViewModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
      <div class="modal-content border-0 shadow-lg rounded-4 overflow-hidden">
        <div class="modal-header border-bottom-0 pb-0">
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body p-4" id="quickViewModalContent">
          <!-- Loaded dynamically -->
        </div>
      </div>
    </div>
  </div>
