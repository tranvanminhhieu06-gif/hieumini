<?php
/**
 * HieuMini - Phần đầu trang dùng chung (SEO + Điều hướng)
 * Mỗi trang gọi seo([...]) TRƯỚC khi require tệp này.
 */
if (!defined('BASE_URL')) {
    require_once __DIR__ . '/config.php';
}

$pageTitle = seo_get('title', SITE_NAME . ' - ' . SITE_SLOGAN);
$pageDesc  = seo_get('description', setting('site_description', SITE_SLOGAN));
$pageKeys  = seo_get('keywords', setting('site_keywords'));
$pageImage = seo_get('image', asset(setting('og_image', 'assets/images/og-cover.svg')));
$canonical = seo_get('canonical', (isset($_SERVER['REQUEST_URI']) ? BASE_URL . strtok($_SERVER['REQUEST_URI'], '?') : BASE_URL));
$pageType  = seo_get('og_type', 'website');
$robots    = seo_get('robots', 'index, follow, max-image-preview:large');
$bodyClass = seo_get('body_class', '');
$flashes   = take_flash();
$cartQty   = cart_count();
$currentFile = basename($_SERVER['PHP_SELF'] ?? '');
?>
<!DOCTYPE html>
<html lang="vi" prefix="og: https://ogp.me/ns#">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1, viewport-fit=cover">
<script>
/* Đặt giao diện TRƯỚC khi trang vẽ để không bị nháy sai màu. */
(function () {
  document.documentElement.classList.add('js');
  try {
    var saved = localStorage.getItem('hm-theme');
    var prefersLight = window.matchMedia('(prefers-color-scheme: light)').matches;
    var theme = saved || (prefersLight ? 'light' : 'dark');
    if (theme === 'light') document.documentElement.setAttribute('data-theme', 'light');
  } catch (e) { /* trình duyệt chặn localStorage - giữ giao diện tối mặc định */ }
})();
</script>

<!-- ===== SEO cơ bản ===== -->
<title><?= e($pageTitle) ?></title>
<meta name="description" content="<?= e($pageDesc) ?>">
<?php if ($pageKeys): ?><meta name="keywords" content="<?= e($pageKeys) ?>"><?php endif; ?>
<meta name="robots" content="<?= e($robots) ?>">
<meta name="author" content="Trần Văn Minh Hiếu">
<meta name="language" content="Vietnamese">
<link rel="canonical" href="<?= e($canonical) ?>">

<!-- ===== Open Graph (Facebook, Zalo) ===== -->
<meta property="og:site_name" content="<?= e(SITE_NAME) ?>">
<meta property="og:locale" content="vi_VN">
<meta property="og:type" content="<?= e($pageType) ?>">
<meta property="og:title" content="<?= e($pageTitle) ?>">
<meta property="og:description" content="<?= e($pageDesc) ?>">
<meta property="og:url" content="<?= e($canonical) ?>">
<meta property="og:image" content="<?= e($pageImage) ?>">
<meta property="og:image:width" content="1200">
<meta property="og:image:height" content="630">

<!-- ===== Twitter Card ===== -->
<meta name="twitter:card" content="summary_large_image">
<meta name="twitter:title" content="<?= e($pageTitle) ?>">
<meta name="twitter:description" content="<?= e($pageDesc) ?>">
<meta name="twitter:image" content="<?= e($pageImage) ?>">

<!-- ===== Giao diện & Biểu tượng ===== -->
<meta name="theme-color" content="#0B0B1A">
<meta name="color-scheme" content="dark light">
<link rel="icon" type="image/svg+xml" href="<?= e(asset('assets/images/favicon.svg')) ?>">
<link rel="apple-touch-icon" href="<?= e(asset('assets/images/favicon.svg')) ?>">

<!-- ===== Phông chữ ===== -->
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">

<link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
<link rel="alternate" type="application/rss+xml" title="<?= e(SITE_NAME) ?> Blog" href="<?= e(url('rss.php')) ?>">
<link rel="sitemap" type="application/xml" href="<?= e(url('sitemap.php')) ?>">

<!-- ===== Dữ liệu có cấu trúc: Tổ chức + Website ===== -->
<script type="application/ld+json">
<?= json_encode([
    '@context' => 'https://schema.org',
    '@graph'   => [
        [
            '@type'       => 'Organization',
            '@id'         => BASE_URL . '/#organization',
            'name'        => SITE_NAME,
            'url'         => BASE_URL,
            'logo'        => asset('assets/images/logo.svg'),
            'description' => setting('site_description'),
            'email'       => setting('contact_email'),
            'telephone'   => setting('contact_phone'),
            'address'     => ['@type' => 'PostalAddress', 'streetAddress' => setting('contact_address'), 'addressCountry' => 'VN'],
            'sameAs'      => array_values(array_filter([setting('facebook'), setting('youtube'), setting('github')])),
        ],
        [
            '@type'           => 'WebSite',
            '@id'             => BASE_URL . '/#website',
            'url'             => BASE_URL,
            'name'            => SITE_NAME,
            'inLanguage'      => 'vi-VN',
            'publisher'       => ['@id' => BASE_URL . '/#organization'],
            'potentialAction' => [
                '@type'       => 'SearchAction',
                'target'      => ['@type' => 'EntryPoint', 'urlTemplate' => BASE_URL . '/projects.php?q={search_term_string}'],
                'query-input' => 'required name=search_term_string',
            ],
        ],
    ],
], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES | JSON_PRETTY_PRINT) ?>
</script>
<?= seo_get('schema') ?>
<?php if (setting('ga_id')): ?>
<script async src="https://www.googletagmanager.com/gtag/js?id=<?= e(setting('ga_id')) ?>"></script>
<script>window.dataLayer=window.dataLayer||[];function gtag(){dataLayer.push(arguments);}gtag('js',new Date());gtag('config','<?= e(setting('ga_id')) ?>');</script>
<?php endif; ?>
</head>
<body class="<?= e($bodyClass) ?>">
<a class="skip-link" href="#main">Bỏ qua điều hướng, tới nội dung chính</a>

<div class="aurora" aria-hidden="true">
  <span class="aurora__blob aurora__blob--1"></span>
  <span class="aurora__blob aurora__blob--2"></span>
  <span class="aurora__blob aurora__blob--3"></span>
</div>
<div class="scroll-progress" aria-hidden="true"><span id="scrollBar"></span></div>

<header class="site-header" id="siteHeader">
  <div class="container site-header__inner">
    <a class="brand" href="<?= e(url('index.php')) ?>" aria-label="<?= e(SITE_NAME) ?> - Trang chủ">
      <svg class="brand__mark" viewBox="0 0 40 40" aria-hidden="true" focusable="false">
        <defs><linearGradient id="bg1" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0" stop-color="#7C3AED"/><stop offset="1" stop-color="#22D3EE"/>
        </linearGradient></defs>
        <rect x="1.5" y="1.5" width="37" height="37" rx="11" fill="url(#bg1)" opacity=".18"/>
        <rect x="1.5" y="1.5" width="37" height="37" rx="11" fill="none" stroke="url(#bg1)" stroke-width="2"/>
        <path d="M12 28V12h3.6v6.2h8.8V12H28v16h-3.6v-6.4h-8.8V28z" fill="url(#bg1)"/>
      </svg>
      <span class="brand__text"><span class="brand__hieu">Hieu</span><span class="brand__mini">Mini</span></span>
    </a>

    <button class="nav-toggle" id="navToggle" aria-expanded="false" aria-controls="primaryNav" aria-label="Mở menu điều hướng">
      <span></span><span></span><span></span>
    </button>

    <nav class="primary-nav" id="primaryNav" aria-label="Điều hướng chính">
      <ul>
        <li><a href="<?= e(url('index.php')) ?>" <?= $currentFile === 'index.php' ? 'aria-current="page"' : '' ?>>Trang chủ</a></li>
        <li class="has-drop">
          <a href="<?= e(url('projects.php')) ?>" <?= $currentFile === 'projects.php' ? 'aria-current="page"' : '' ?>>Kho dự án</a>
          <div class="drop">
            <?php foreach (get_categories($pdo) as $cat): ?>
              <a href="<?= e(url('projects.php?cat=' . $cat['slug'])) ?>">
                <span><?= e($cat['name']) ?></span>
                <small><?= (int)$cat['total'] ?> dự án</small>
              </a>
            <?php endforeach; ?>
          </div>
        </li>
        <li><a href="<?= e(url('blog.php')) ?>" <?= in_array($currentFile, ['blog.php', 'blog-detail.php'], true) ? 'aria-current="page"' : '' ?>>Blog</a></li>
        <li><a href="<?= e(url('about.php')) ?>" <?= $currentFile === 'about.php' ? 'aria-current="page"' : '' ?>>Giới thiệu</a></li>
        <li><a href="<?= e(url('contact.php')) ?>" <?= $currentFile === 'contact.php' ? 'aria-current="page"' : '' ?>>Liên hệ</a></li>
      </ul>
    </nav>

    <div class="header-actions">
      <form class="header-search" role="search" action="<?= e(url('projects.php')) ?>" method="get">
        <label class="sr-only" for="headerSearch">Tìm kiếm dự án</label>
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.2-3.2"/></svg>
        <input type="search" id="headerSearch" name="q" placeholder="Tìm dự án, công nghệ…" value="<?= e($_GET['q'] ?? '') ?>">
      </form>

      <button type="button" class="icon-btn theme-toggle" id="themeToggle"
              aria-label="Chuyển sang giao diện sáng" aria-pressed="false" title="Chuyển giao diện sáng / tối">
        <svg class="icon-moon" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M21 13A9 9 0 1 1 11 3a7 7 0 0 0 10 10z"/></svg>
        <svg class="icon-sun" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M2 12h2M20 12h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/></svg>
      </button>

      <a class="icon-btn icon-btn--wish" href="<?= e(url('wishlist.php')) ?>" aria-label="Danh sách yêu thích">
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 20s-7-4.4-7-9.2A4 4 0 0 1 12 8a4 4 0 0 1 7 2.8C19 15.6 12 20 12 20z"/></svg>
      </a>

      <a class="icon-btn cart-btn" href="<?= e(url('cart.php')) ?>" aria-label="Giỏ hàng, <?= $cartQty ?> dự án">
        <svg viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M4 5h2l2.2 9.4a2 2 0 0 0 2 1.6h6.2a2 2 0 0 0 2-1.5L20 8H7"/><circle cx="10" cy="19" r="1.4"/><circle cx="17" cy="19" r="1.4"/></svg>
        <span class="cart-badge" id="cartBadge" <?= $cartQty ? '' : 'hidden' ?>><?= $cartQty ?></span>
      </a>

      <?php if (is_logged_in()): ?>
        <div class="has-drop user-menu">
          <button class="btn btn--ghost btn--sm" aria-haspopup="true" aria-expanded="false">
            <?= e(mb_strimwidth(current_user_name(), 0, 14, '…', 'UTF-8')) ?>
          </button>
          <div class="drop drop--right">
            <a href="<?= e(url('account.php')) ?>">Tài khoản của tôi</a>
            <a href="<?= e(url('account.php?tab=orders')) ?>">Đơn hàng &amp; tải về</a>
            <a href="<?= e(url('wishlist.php')) ?>">Dự án yêu thích</a>
            <?php if (is_admin()): ?><a href="<?= e(url('admin/index.php')) ?>">Trang quản trị</a><?php endif; ?>
            <a href="<?= e(url('logout.php')) ?>">Đăng xuất</a>
          </div>
        </div>
      <?php else: ?>
        <a class="btn btn--ghost btn--sm" href="<?= e(url('login.php')) ?>">Đăng nhập</a>
        <a class="btn btn--primary btn--sm hide-sm" href="<?= e(url('register.php')) ?>">Đăng ký</a>
      <?php endif; ?>
    </div>
  </div>
</header>

<?php if ($flashes): ?>
<div class="flash-stack" role="status" aria-live="polite">
  <?php foreach ($flashes as $f): ?>
    <div class="flash flash--<?= e($f['type']) ?>"><?= e($f['message']) ?></div>
  <?php endforeach; ?>
</div>
<?php endif; ?>

<main id="main">
