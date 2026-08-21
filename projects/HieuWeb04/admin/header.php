<?php
require_once __DIR__ . '/../includes/db.php';
require_once __DIR__ . '/../includes/functions.php';

// Enforce admin login
require_admin();

$admin_user = $_SESSION['admin_user'];
$admin_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?php echo isset($admin_title) ? htmlspecialchars($admin_title) . ' | Quản Trị DatCyber' : 'Bảng Quản Trị DatCyber'; ?></title>
  
  <!-- Google Fonts: Plus Jakarta Sans & Outfit -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&family=Outfit:wght@500;600;700;800&display=swap" rel="stylesheet">

  <!-- Bootstrap 5 CSS -->
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
  <!-- FontAwesome 6 -->
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
  <!-- Custom Admin CSS -->
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .admin-sidebar {
      background: #0f172a;
      min-height: 100vh;
      color: #94a3b8;
    }
    .admin-nav-link {
      color: #cbd5e1;
      padding: 0.75rem 1.25rem;
      border-radius: 8px;
      display: flex;
      align-items: center;
      gap: 0.75rem;
      font-weight: 500;
      transition: all 0.2s ease;
      text-decoration: none;
      margin-bottom: 0.35rem;
    }
    .admin-nav-link:hover, .admin-nav-link.active {
      background: #0284c7;
      color: #ffffff;
    }
    .admin-stat-card {
      border-radius: 16px;
      border: 1px solid #e2e8f0;
      background: #fff;
      padding: 1.5rem;
      box-shadow: 0 4px 12px rgba(15,23,42,0.05);
      transition: transform 0.2s;
    }
    .admin-stat-card:hover {
      transform: translateY(-4px);
    }
  </style>
</head>
<body class="bg-light">

<div class="container-fluid">
  <div class="row">
    
    <!-- Sidebar -->
    <div class="col-md-3 col-lg-2 admin-sidebar p-3 d-flex flex-column">
      <a href="index.php" class="navbar-brand-custom text-white mb-4 d-block px-2">
        <i class="fas fa-home-heart text-primary"></i>
        <span>Dat<strong class="text-primary">Admin</strong></span>
      </a>

      <div class="d-flex flex-column flex-grow-1">
        <a href="index.php" class="admin-nav-link <?php echo $admin_page === 'index.php' ? 'active' : ''; ?>">
          <i class="fas fa-chart-pie fa-fw"></i> Tổng quan
        </a>
        <a href="products.php" class="admin-nav-link <?php echo $admin_page === 'products.php' ? 'active' : ''; ?>">
          <i class="fas fa-boxes-stacked fa-fw"></i> Quản lý sản phẩm
        </a>
        <a href="orders.php" class="admin-nav-link <?php echo $admin_page === 'orders.php' ? 'active' : ''; ?>">
          <i class="fas fa-file-invoice-dollar fa-fw"></i> Quản lý đơn hàng
        </a>
        <a href="categories.php" class="admin-nav-link <?php echo $admin_page === 'categories.php' ? 'active' : ''; ?>">
          <i class="fas fa-tags fa-fw"></i> Danh mục
        </a>
      </div>

      <div class="pt-3 border-top border-secondary border-opacity-25 mt-auto">
        <a href="../index.php" class="btn btn-outline-info w-100 btn-sm mb-2" target="_blank">
          <i class="fas fa-arrow-up-right-from-square me-1"></i> Xem Website
        </a>
        <a href="logout.php" class="btn btn-outline-danger w-100 btn-sm">
          <i class="fas fa-right-from-bracket me-1"></i> Đăng Xuất
        </a>
      </div>
    </div>

    <!-- Main Content Area -->
    <div class="col-md-9 col-lg-10 p-4">
      <!-- Admin Top Navbar -->
      <div class="bg-white p-3 rounded-4 border shadow-sm mb-4 d-flex justify-content-between align-items-center flex-wrap gap-2">
        <h4 class="fw-bold m-0"><?php echo $admin_title ?? 'Bảng Điều Khiển'; ?></h4>
        <div class="d-flex align-items-center gap-3">
          <span class="badge bg-success bg-opacity-10 text-success p-2 d-none d-sm-inline-block">
            <i class="fas fa-circle-dot me-1"></i> Hệ thống hoạt động bình thường
          </span>
          <div class="d-flex align-items-center gap-2">
            <div class="rounded-circle bg-primary text-white fw-bold d-flex align-items-center justify-content-center" style="width: 38px; height: 38px;">
              <?php echo strtoupper(substr($admin_user['name'] ?? 'AD', 0, 2)); ?>
            </div>
            <div class="d-none d-sm-block text-start">
              <div class="fw-bold small"><?php echo htmlspecialchars($admin_user['name'] ?? 'Admin'); ?></div>
              <small class="text-muted"><?php echo htmlspecialchars($admin_user['email'] ?? 'admin@datcyber.vn'); ?></small>
            </div>
            <a href="logout.php" class="btn btn-sm btn-outline-secondary ms-2" title="Đăng xuất">
              <i class="fas fa-power-off text-danger"></i>
            </a>
          </div>
        </div>
      </div>
