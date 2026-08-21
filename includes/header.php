<?php
/**
 * HieuMini — Đầu trang dùng chung
 * Biến tùy chọn khai báo trước khi require: $pageTitle, $pageDesc, $bodyClass
 */
if (!defined('SITE_NAME')) {
    exit('Truy cập không hợp lệ.');
}

$pageTitle = $pageTitle ?? SITE_NAME . ' — ' . SITE_TAGLINE;
$pageDesc  = $pageDesc  ?? 'Bộ sưu tập 6 website thương mại điện tử xây dựng bằng PHP và MySQL, xem trực tiếp ngay trên trình duyệt.';
?>
<!doctype html>
<html lang="vi" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<meta name="author" content="<?= e(SITE_AUTHOR) ?>">
<meta name="theme-color" content="#4F46E5">
<meta property="og:type" content="website">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($pageDesc) ?>">
<meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='9' fill='%234F46E5'/><text x='16' y='22' font-family='sans-serif' font-size='16' font-weight='700' fill='white' text-anchor='middle'>H</text></svg>">
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<script>
/* Áp dụng chế độ sáng/tối ngay trước khi vẽ trang để tránh nhấp nháy nền. */
(function () {
  try {
    var t = localStorage.getItem('hieumini-theme');
    if (!t) t = window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light';
    document.documentElement.setAttribute('data-theme', t);
  } catch (e) {}
})();
</script>
</head>
<body class="<?= e($bodyClass ?? '') ?>">

<div class="scroll-progress" aria-hidden="true"></div>
<a class="skip-link" href="#main">Bỏ qua điều hướng, tới nội dung chính</a>

<header class="site-header">
  <div class="header-inner">
    <a class="brand" href="<?= e(url('index.php')) ?>">
      <span class="brand-mark" aria-hidden="true">H</span>
      <span><?= e(SITE_NAME) ?></span>
    </a>

    <nav class="nav" id="primaryNav" aria-label="Điều hướng chính">
      <a href="<?= e(url('index.php')) ?>" class="<?= trim(nav_active('index.php')) ?>">Trang chủ</a>
      <a href="<?= e(url('index.php#projects')) ?>">Dự án</a>
      <a href="<?= e(url('about.php')) ?>" class="<?= trim(nav_active('about.php')) ?>">Giới thiệu</a>
      <a href="<?= e(url('contact.php')) ?>" class="<?= trim(nav_active('contact.php')) ?>">Liên hệ</a>
    </nav>

    <div class="header-actions">
      <button type="button" class="icon-btn" id="themeToggle" aria-label="Chuyển đổi giao diện sáng tối">
        <?= icon('moon') ?><?= icon('sun') ?>
      </button>
      <button type="button" class="icon-btn nav-toggle" aria-controls="primaryNav" aria-expanded="false" aria-label="Mở menu điều hướng">
        <?= icon('menu') ?>
      </button>
    </div>
  </div>
</header>

<main id="main">
<?php
$flashes = flash_pull();
if ($flashes):
?>
<div class="toast-stack" role="status" aria-live="polite">
  <?php foreach ($flashes as $f): ?>
    <div class="toast"><?= icon($f['type'] === 'error' ? 'close' : 'check', 'ico ico-sm') ?><span><?= e($f['message']) ?></span></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>
