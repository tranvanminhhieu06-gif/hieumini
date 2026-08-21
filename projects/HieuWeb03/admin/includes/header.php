<?php
// admin/includes/header.php
require_once __DIR__ . '/../../config/app.php';
require_admin();

$admin_title = isset($page_title) ? $page_title . ' - Quản Trị HieuMini' : 'Hệ Thống Quản Trị - HieuMini';
$current_admin_page = basename($_SERVER['PHP_SELF']);
$current_admin = current_user();
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($admin_title) ?></title>
  <!-- Preconnect & Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
  <link rel="stylesheet" href="../assets/css/style.css">
  <style>
    .admin-layout {
      display: grid;
      grid-template-columns: 260px 1fr;
      min-height: 100vh;
      background: #f1f5f9;
    }
    .admin-sidebar {
      background: #0f172a;
      color: #94a3b8;
      padding: 24px 16px;
      display: flex;
      flex-direction: column;
      position: sticky;
      top: 0;
      height: 100vh;
      box-shadow: 4px 0 15px rgba(0,0,0,0.05);
    }
    .admin-sidebar a {
      color: #94a3b8;
      display: flex;
      align-items: center;
      gap: 12px;
      padding: 12px 16px;
      border-radius: var(--radius-md);
      font-weight: 600;
      font-size: 0.95rem;
      margin-bottom: 4px;
      transition: var(--transition-fast);
    }
    .admin-sidebar a:hover, .admin-sidebar a.active {
      color: #fff;
      background: rgba(79, 70, 229, 0.25);
    }
    .admin-sidebar a.active {
      background: var(--primary);
      color: #fff;
      box-shadow: 0 4px 12px var(--primary-glow);
    }
    .admin-content {
      padding: 30px 40px;
      overflow-y: auto;
    }
    .kpi-grid {
      display: grid;
      grid-template-columns: repeat(4, 1fr);
      gap: 24px;
      margin-bottom: 30px;
    }
    .kpi-card {
      background: #fff;
      border-radius: var(--radius-lg);
      padding: 24px;
      border: 1px solid var(--border);
      display: flex;
      align-items: center;
      gap: 20px;
      box-shadow: var(--shadow-sm);
      transition: var(--transition-base);
    }
    .kpi-card:hover {
      transform: translateY(-4px);
      box-shadow: var(--shadow-md);
    }
    .kpi-icon {
      width: 56px;
      height: 56px;
      border-radius: var(--radius-md);
      display: flex;
      align-items: center;
      justify-content: center;
      font-size: 1.8rem;
    }
    .data-table-card {
      background: #fff;
      border-radius: var(--radius-lg);
      border: 1px solid var(--border);
      padding: 28px;
      box-shadow: var(--shadow-sm);
    }
    .admin-table {
      width: 100%;
      border-collapse: collapse;
      font-size: 0.92rem;
    }
    .admin-table th {
      text-align: left;
      padding: 12px 14px;
      color: var(--muted);
      border-bottom: 2px solid var(--border);
      font-weight: 700;
      text-transform: uppercase;
      font-size: 0.8rem;
      letter-spacing: 0.5px;
    }
    .admin-table td {
      padding: 14px;
      border-bottom: 1px solid var(--border);
      vertical-align: middle;
    }
    .admin-table tr:hover td {
      background: #f8fafc;
    }
    @media (max-width: 1024px) {
      .admin-layout { grid-template-columns: 1fr; }
      .admin-sidebar { 
        height: auto; 
        position: fixed; 
        left: -280px; 
        top: 0; 
        bottom: 0; 
        width: 280px; 
        z-index: 1000;
        transition: transform 0.3s cubic-bezier(0.16, 1, 0.3, 1);
        overflow-y: auto;
      }
      .admin-sidebar.open {
        transform: translateX(280px);
      }
      .admin-sidebar-backdrop {
        display: none;
        position: fixed;
        inset: 0;
        background: rgba(15, 23, 42, 0.6);
        backdrop-filter: blur(4px);
        z-index: 999;
      }
      .admin-sidebar-backdrop.active {
        display: block;
      }
      .admin-mobile-bar {
        display: flex !important;
        justify-content: space-between;
        align-items: center;
        background: #0f172a;
        color: #fff;
        padding: 12px 20px;
        position: sticky;
        top: 0;
        z-index: 900;
      }
      .kpi-grid { grid-template-columns: repeat(2, 1fr); }
    }
    @media (max-width: 600px) {
      .kpi-grid { grid-template-columns: 1fr; }
      .admin-content { padding: 16px 12px; }
      .admin-table th, .admin-table td { padding: 10px 8px; font-size: 0.82rem; }
    }
  </style>
</head>
<body>

<div class="admin-sidebar-backdrop" id="adminSidebarBackdrop" onclick="toggleAdminSidebar()"></div>

<!-- Admin Mobile Topbar -->
<div class="admin-mobile-bar" style="display: none;">
  <div style="display: flex; align-items: center; gap: 10px;">
    <button onclick="toggleAdminSidebar()" style="background: none; border: none; color: #fff; font-size: 1.5rem; cursor: pointer; display: flex; align-items: center; padding: 4px;">
      <i class="bi bi-list"></i>
    </button>
    <div style="font-weight: 800; font-size: 1.1rem; color: #fff;">Hieu<span style="color: #818cf8;">Admin</span></div>
  </div>
  <a href="../index.php" target="_blank" style="color: #38bdf8; font-size: 0.85rem; font-weight: 600;">
    <i class="bi bi-box-arrow-up-right"></i> Xem web
  </a>
</div>

<div class="admin-layout">
  <!-- Sidebar -->
  <aside class="admin-sidebar" id="adminSidebar">
    <div style="display: flex; align-items: center; justify-content: space-between; margin-bottom: 30px; padding: 0 10px;">
      <div style="display: flex; align-items: center; gap: 12px;">
        <div class="logo-icon" style="width: 38px; height: 38px; font-size: 1.1rem;">
          <i class="bi bi-feather"></i>
        </div>
        <div>
          <div style="font-weight: 800; font-size: 1.2rem; color: #fff;">HieuMini</div>
          <div style="font-size: 0.72rem; color: #818cf8; letter-spacing: 1px; font-weight: 700;">ADMIN CONTROL</div>
        </div>
      </div>
      <button onclick="toggleAdminSidebar()" class="admin-close-btn" style="background: none; border: none; color: #94a3b8; font-size: 1.3rem; cursor: pointer; display: none;">
        <i class="bi bi-x-lg"></i>
      </button>
    </div>

    <nav style="flex: 1;">
      <a href="index.php" class="<?= $current_admin_page === 'index.php' ? 'active' : '' ?>">
        <i class="bi bi-speedometer2"></i> Tổng Quan Dashboard
      </a>
      <a href="products.php" class="<?= in_array($current_admin_page, ['products.php', 'product-add.php', 'product-edit.php']) ? 'active' : '' ?>">
        <i class="bi bi-box-seam"></i> Quản Lý Sản Phẩm
      </a>
      <a href="categories.php" class="<?= $current_admin_page === 'categories.php' ? 'active' : '' ?>">
        <i class="bi bi-tags"></i> Danh Mục Đồ Dùng
      </a>
      <a href="orders.php" class="<?= in_array($current_admin_page, ['orders.php', 'order-detail.php']) ? 'active' : '' ?>">
        <i class="bi bi-receipt"></i> Đơn Hàng
      </a>
      <a href="users.php" class="<?= $current_admin_page === 'users.php' ? 'active' : '' ?>">
        <i class="bi bi-people"></i> Khách Hàng
      </a>
      <a href="contacts.php" class="<?= $current_admin_page === 'contacts.php' ? 'active' : '' ?>">
        <i class="bi bi-envelope-paper"></i> Tin Nhắn Liên Hệ
      </a>
    </nav>

    <div style="border-top: 1px solid #1e293b; padding-top: 16px;">
      <a href="../index.php" target="_blank" style="color: #38bdf8;">
        <i class="bi bi-box-arrow-up-right"></i> Xem Trang Web
      </a>
      <a href="../logout.php" style="color: #f87171;">
        <i class="bi bi-box-arrow-right"></i> Đăng Xuất
      </a>
    </div>
  </aside>

  <script>
    function toggleAdminSidebar() {
      const sidebar = document.getElementById('adminSidebar');
      const backdrop = document.getElementById('adminSidebarBackdrop');
      if (sidebar) sidebar.classList.toggle('open');
      if (backdrop) backdrop.classList.toggle('active');
    }
  </script>

  <!-- Main Content wrapper -->
  <div class="admin-content">
    <!-- Top info bar -->
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 28px; background: #fff; padding: 14px 24px; border-radius: var(--radius-md); border: 1px solid var(--border); flex-wrap: wrap; gap: 12px;">
      <h2 style="font-size: 1.3rem; font-weight: 800; color: var(--dark); margin: 0;">
        <?= htmlspecialchars($page_title ?? 'Tổng Quan Hệ Thống') ?>
      </h2>
      <div style="display: flex; align-items: center; gap: 12px;">
        <div style="text-align: right;">
          <div style="font-weight: 700; font-size: 0.92rem; color: var(--dark);"><?= htmlspecialchars($current_admin['fullname']) ?></div>
          <div style="font-size: 0.78rem; color: var(--primary); font-weight: 600;">Quản trị viên cấp cao</div>
        </div>
        <div style="width: 40px; height: 40px; border-radius: 50%; background: #e0e7ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-weight: 800;">
          A
        </div>
      </div>
    </div>
