<?php
/**
 * HieuMini Admin - Khung giao diện (phần đầu)
 * Mọi trang quản trị đều require tệp này sau khi gọi require_admin().
 */
if (!defined('BASE_URL')) {
    require_once dirname(__DIR__, 2) . '/includes/config.php';
}
$adminTitle = $adminTitle ?? 'Bảng điều khiển';
$currentAdmin = basename($_SERVER['PHP_SELF'] ?? '');
$flashes = take_flash();

$menu = [
    'index.php'      => ['Tổng quan',     'M4 13h6V4H4zM14 21h6V10h-6zM4 21h6v-5H4zM14 7h6V4h-6z'],
    'projects.php'   => ['Dự án',         'M3 7l9-4 9 4-9 4zM3 12l9 4 9-4M3 17l9 4 9-4'],
    'categories.php' => ['Danh mục',      'M4 6h16M4 12h16M4 18h10'],
    'orders.php'     => ['Đơn hàng',      'M6 2h12l2 6-8 14L4 8z'],
    'users.php'      => ['Người dùng',    'M16 20v-2a4 4 0 0 0-8 0v2M12 11a4 4 0 1 0 0-8 4 4 0 0 0 0 8z'],
    'reviews.php'    => ['Đánh giá',      'M12 3l2.9 6.1 6.6.9-4.8 4.6 1.2 6.6L12 18l-5.9 3.2 1.2-6.6-4.8-4.6 6.6-.9z'],
    'posts.php'      => ['Bài viết',      'M5 3h11l4 4v14H5zM16 3v5h5'],
    'contacts.php'   => ['Liên hệ',       'M3 5h18v14H3zM4 7l8 5 8-5'],
    'settings.php'   => ['Cấu hình SEO',  'M12 15a3 3 0 1 0 0-6 3 3 0 0 0 0 6zM19 12a7 7 0 0 0-.2-1.6l2-1.5-2-3.4-2.3 1a7 7 0 0 0-2.8-1.6L13.4 2h-2.8l-.3 2.9a7 7 0 0 0-2.8 1.6l-2.3-1-2 3.4 2 1.5A7 7 0 0 0 5 12c0 .5.1 1 .2 1.6l-2 1.5 2 3.4 2.3-1a7 7 0 0 0 2.8 1.6l.3 2.9h2.8l.3-2.9a7 7 0 0 0 2.8-1.6l2.3 1 2-3.4-2-1.5c.1-.5.2-1 .2-1.6z'],
];
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($adminTitle) ?> · Quản trị <?= e(SITE_NAME) ?></title>
<link rel="icon" type="image/svg+xml" href="<?= e(asset('assets/images/favicon.svg')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
<script>
(function () {
  document.documentElement.classList.add('js');
  try {
    var saved = localStorage.getItem('hm-theme');
    var theme = saved || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
    if (theme === 'light') document.documentElement.setAttribute('data-theme', 'light');
  } catch (e) {}
})();
</script>
</head>
<body>
<a class="skip-link" href="#adminMain">Tới nội dung chính</a>
<div class="aurora" aria-hidden="true"><span class="aurora__blob aurora__blob--1"></span><span class="aurora__blob aurora__blob--2"></span></div>

<div class="admin-body">
  <aside class="admin-side">
    <a class="brand" href="<?= e(url('index.php')) ?>">
      <svg class="brand__mark" viewBox="0 0 40 40" aria-hidden="true">
        <defs><linearGradient id="ag" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#7C3AED"/><stop offset="1" stop-color="#22D3EE"/></linearGradient></defs>
        <rect x="1.5" y="1.5" width="37" height="37" rx="11" fill="url(#ag)" opacity=".18"/>
        <rect x="1.5" y="1.5" width="37" height="37" rx="11" fill="none" stroke="url(#ag)" stroke-width="2"/>
        <path d="M12 28V12h3.6v6.2h8.8V12H28v16h-3.6v-6.4h-8.8V28z" fill="url(#ag)"/>
      </svg>
      <span class="brand__text"><span class="brand__hieu">Hieu</span><span class="brand__mini">Mini</span></span>
    </a>

    <nav class="admin-nav" aria-label="Điều hướng quản trị">
      <?php foreach ($menu as $file => $item): ?>
        <a class="<?= $currentAdmin === $file ? 'is-active' : '' ?>" href="<?= e(url('admin/' . $file)) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= $item[1] ?>"/></svg>
          <?= e($item[0]) ?>
        </a>
      <?php endforeach; ?>
      <hr>
      <a href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M18 13v6a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V8a2 2 0 0 1 2-2h6M15 3h6v6M10 14L21 3"/></svg>
        Xem website
      </a>
      <a href="<?= e(url('logout.php')) ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M9 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h4M16 17l5-5-5-5M21 12H9"/></svg>
        Đăng xuất
      </a>
    </nav>
  </aside>

  <main class="admin-main" id="adminMain">
    <div class="admin-top">
      <div>
        <h1 style="font-size:var(--fs-2xl);margin:0"><?= e($adminTitle) ?></h1>
        <p style="margin:0;font-size:var(--fs-sm);color:var(--fg-muted)">
          Xin chào <?= e(current_user_name()) ?> · <?= e(date('H:i, d/m/Y')) ?>
        </p>
      </div>
      <div style="display:flex;align-items:center;gap:10px;flex-wrap:wrap">
        <button type="button" class="icon-btn theme-toggle" id="themeToggle"
                aria-label="Chuyển sang giao diện sáng" aria-pressed="false" title="Chuyển giao diện sáng / tối">
          <svg class="icon-moon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 13A9 9 0 1 1 11 3a7 7 0 0 0 10 10z"/></svg>
          <svg class="icon-sun" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
        </button>
        <?= $adminActions ?? '' ?>
      </div>
    </div>

    <?php foreach ($flashes as $f): ?>
      <div class="flash flash--<?= e($f['type']) ?>" style="position:static;margin-bottom:var(--sp-4)"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
