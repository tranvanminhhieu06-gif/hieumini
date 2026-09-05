<?php
/**
 * Đầu trang dùng chung.
 *
 * Trang gọi có thể khai báo trước khi require:
 *   $pageTitle, $pageDesc, $pageImage, $canonical, $jsonLd (mảng), $bodyClass
 */
if (!defined('SITE_NAME')) {
    exit('Truy cập không hợp lệ.');
}

$pageTitle = $pageTitle ?? (SITE_NAME . ' — ' . SITE_TAGLINE);
$pageDesc  = $pageDesc  ?? SITE_DESC;
$pageImage = $pageImage ?? abs_url('assets/img/covers/og-default.webp');
$canonical = $canonical ?? abs_url(ltrim($_SERVER['REQUEST_URI'] ?? '', '/'));
?>
<!doctype html>
<html lang="vi" data-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">

<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<meta name="author" content="<?= e(SITE_AUTHOR) ?>">
<link rel="canonical" href="<?= e($canonical) ?>">

<?php /* Open Graph + Twitter: quyết định thẻ xem trước khi chia sẻ lên mạng xã hội */ ?>
<meta property="og:type" content="website">
<meta property="og:locale" content="vi_VN">
<meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($pageDesc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($pageImage) ?>">
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($pageTitle) ?>">
<meta name="twitter:description" content="<?= e($pageDesc) ?>">
<meta name="twitter:image" content="<?= e($pageImage) ?>">

<meta name="theme-color" content="#0E0D0C" media="(prefers-color-scheme: dark)">
<meta name="theme-color" content="#FBF9F4" media="(prefers-color-scheme: light)">

<link rel="icon" href="data:image/svg+xml,<svg xmlns='http://www.w3.org/2000/svg' viewBox='0 0 32 32'><rect width='32' height='32' rx='4' fill='%230E0D0C'/><text x='16' y='23' font-family='Georgia,serif' font-size='19' fill='%23D8A94A' text-anchor='middle'>H</text></svg>">

<?php /* Nạp trước phông chữ tiêu đề: chữ lớn nhất trang, tải chậm là thấy ngay */ ?>
<link rel="preload" href="<?= e(url('assets/fonts/cormorant-garamond-latin-600-normal.woff2')) ?>" as="font" type="font/woff2" crossorigin>
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">

<?php if (!empty($jsonLd)): ?>
<script type="application/ld+json"><?= json_encode($jsonLd, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) ?></script>
<?php endif; ?>

<script>
/* Chạy trước khi trình duyệt vẽ trang, vì hai việc:
   1. Gắn lớp .js — CSS chỉ ẩn nội dung để làm hiệu ứng KHI có JavaScript,
      nhờ vậy người tắt JS và bot tìm kiếm vẫn đọc được đầy đủ nội dung.
   2. Áp giao diện sáng/tối đã lưu, tránh chớp nền trắng rồi mới đổi sang tối. */
(function () {
  var r = document.documentElement;
  r.classList.add('js');
  try {
    /* Tối là giao diện chủ đạo của thiết kế này, nên đó là mặc định — chỉ đổi
       khi chính người đọc bấm nút chuyển và lựa chọn đó được lưu lại. Nếu chạy
       theo cài đặt hệ điều hành thì phần lớn máy để chế độ sáng, và hướng thiết
       kế tối kiểu tạp chí gần như không ai nhìn thấy. */
    var t = localStorage.getItem('hieumini-books-theme');
    r.setAttribute('data-theme', t === 'light' ? 'light' : 'dark');
  } catch (e) {}
})();
</script>
</head>
<body class="<?= e($bodyClass ?? '') ?>">

<div class="scroll-progress" aria-hidden="true"></div>
<a class="skip-link" href="#main">Bỏ qua điều hướng, tới nội dung chính</a>

<header class="site-header">
  <div class="header-inner">
    <a class="brand" href="<?= e(url('index.php')) ?>">HieuMini<span>.</span></a>

    <nav class="nav" id="primaryNav" aria-label="Điều hướng chính">
      <a href="<?= e(url('index.php')) ?>" class="<?= nav_active('index.php') ?>">Trang chủ</a>
      <a href="<?= e(url('books.php')) ?>" class="<?= nav_active('books.php') ?>">Kho sách</a>
      <a href="<?= e(url('authors.php')) ?>" class="<?= nav_active('authors.php') ?>">Tác giả</a>
      <a href="<?= e(url('about.php')) ?>" class="<?= nav_active('about.php') ?>">Giới thiệu</a>
      <a href="<?= e(url('contact.php')) ?>" class="<?= nav_active('contact.php') ?>">Liên hệ</a>
    </nav>

    <div class="header-actions">
      <button type="button" class="icon-btn" id="themeToggle" aria-label="Chuyển đổi giao diện sáng tối">
        <?= icon('moon') ?><?= icon('sun') ?>
      </button>
      <button type="button" class="icon-btn nav-toggle" aria-controls="primaryNav"
              aria-expanded="false" aria-label="Mở menu điều hướng">
        <?= icon('menu') ?>
      </button>
    </div>
  </div>
</header>

<main id="main">
<?php foreach (flash_pull() as $f): ?>
  <div class="container"><div class="alert alert--<?= $f['type'] === 'error' ? 'error' : 'ok' ?>" role="status"><?= e($f['message']) ?></div></div>
<?php endforeach; ?>
