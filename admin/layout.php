<?php
/**
 * HieuMini Admin — Khung giao diện dùng chung
 * Gọi admin_head($title) ở đầu trang và admin_foot() ở cuối trang.
 */

declare(strict_types=1);

/* Tệp này chỉ chứa hàm dựng giao diện, không phải một trang.
   Nếu bị gọi trực tiếp từ trình duyệt thì từ chối như một đường dẫn không tồn tại. */
if (!defined('ADMIN_ENABLED')) {
    http_response_code(404);
    exit;
}

function admin_nav_active(string $file): string
{
    return basename($_SERVER['SCRIPT_NAME'] ?? '') === $file ? ' is-active' : '';
}

function admin_head(string $title): void
{
    ?>
<!doctype html>
<html lang="vi" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($title) ?> — Quản trị <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
<script>
(function () {
  try {
    var t = localStorage.getItem('hieumini-theme');
    if (t) document.documentElement.setAttribute('data-theme', t);
  } catch (e) {}
})();
</script>
</head>
<body class="admin">
<a class="skip-link" href="#adminMain">Bỏ qua điều hướng</a>

<div class="admin-shell">
  <aside class="admin-side">
    <a class="brand" href="<?= e(url('admin/index.php')) ?>">
      <span class="brand-mark" aria-hidden="true">H</span>
      <span>Quản trị</span>
    </a>

    <nav class="admin-nav" aria-label="Điều hướng quản trị">
      <a href="<?= e(url('admin/index.php')) ?>" class="<?= trim(admin_nav_active('index.php')) ?>">
        <?= icon('chart', 'ico ico-sm') ?> Bảng điều khiển
      </a>
      <a href="<?= e(url('admin/projects.php')) ?>" class="<?= trim(admin_nav_active('projects.php') . admin_nav_active('project_form.php')) ?>">
        <?= icon('grid', 'ico ico-sm') ?> Quản lý dự án
      </a>
      <a href="<?= e(url('admin/messages.php')) ?>" class="<?= trim(admin_nav_active('messages.php')) ?>">
        <?= icon('inbox', 'ico ico-sm') ?> Tin nhắn
      </a>
      <a href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">
        <?= icon('external', 'ico ico-sm') ?> Xem website
      </a>
    </nav>

    <div class="admin-side-foot">
      <button type="button" class="icon-btn" id="themeToggle" aria-label="Chuyển đổi giao diện sáng tối">
        <?= icon('moon') ?><?= icon('sun') ?>
      </button>
      <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/logout.php')) ?>">
        <?= icon('logout', 'ico ico-sm') ?> Đăng xuất
      </a>
    </div>
  </aside>

  <main class="admin-main" id="adminMain">
    <header class="admin-topbar">
      <h1><?= e($title) ?></h1>
      <span class="badge badge--live"><i></i> Phiên quản trị đang mở</span>
    </header>

    <div class="admin-content">
      <?php foreach (flash_pull() as $f): ?>
        <div class="alert alert--<?= $f['type'] === 'error' ? 'error' : 'success' ?>" role="status">
          <?= icon($f['type'] === 'error' ? 'close' : 'check', 'ico ico-sm') ?> <?= e($f['message']) ?>
        </div>
      <?php endforeach; ?>
    <?php
}

function admin_foot(): void
{
    ?>
    </div>
  </main>
</div>
<script src="<?= e(asset('js/main.js')) ?>" defer></script>
</body>
</html>
    <?php
}
