<?php
/**
 * Khung giao diện chung cho khu vực quản trị.
 * Dùng: $adminTitle = '...'; require 'layout.php';  ... ; require 'layout_end.php';
 */
if (!defined('SITE_NAME')) {
    exit('Truy cập không hợp lệ.');
}
$admin = $admin ?? current_admin();
?>
<!doctype html>
<html lang="vi" data-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($adminTitle ?? 'Quản trị') ?> — <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<style>
  .admin-shell { display: grid; grid-template-columns: 240px minmax(0, 1fr); min-height: 100vh; }
  .admin-side {
    border-right: 1px solid var(--border); padding: var(--space-6) var(--space-5);
    background: var(--bg-soft); position: sticky; top: 0; height: 100vh; overflow: auto;
  }
  .admin-side h4 {
    font-size: var(--fs-xs); letter-spacing: .18em; text-transform: uppercase;
    color: var(--fg-subtle); margin: var(--space-6) 0 var(--space-3); font-family: var(--font-display);
  }
  .admin-side a {
    display: block; padding: var(--space-2) var(--space-3); border-radius: var(--r-md);
    color: var(--fg-muted); font-size: var(--fs-sm); margin-bottom: 2px;
  }
  .admin-side a:hover { background: var(--surface); color: var(--fg); }
  .admin-side a.is-active { background: var(--accent); color: var(--on-accent); }
  .admin-main { padding: var(--space-7) var(--space-7) var(--space-9); }
  .admin-head { display: flex; align-items: center; justify-content: space-between; gap: var(--space-4); margin-bottom: var(--space-7); flex-wrap: wrap; }
  .stat-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(180px, 1fr)); gap: var(--space-4); margin-bottom: var(--space-8); }
  .stat-card { padding: var(--space-5); border: 1px solid var(--border); border-radius: var(--r-md); background: var(--surface); }
  .stat-card b { display: block; font-family: var(--font-display); font-size: var(--fs-2xl); line-height: 1; color: var(--fg); }
  .stat-card span { font-size: var(--fs-xs); letter-spacing: .14em; text-transform: uppercase; color: var(--fg-subtle); }
  .data-table { width: 100%; border-collapse: collapse; font-size: var(--fs-sm); }
  .data-table th, .data-table td { text-align: left; padding: var(--space-3) var(--space-3); border-bottom: 1px solid var(--border); vertical-align: middle; }
  .data-table th { color: var(--fg-subtle); font-weight: 400; font-size: var(--fs-xs); letter-spacing: .1em; text-transform: uppercase; }
  .data-table tbody tr:hover { background: var(--surface); }
  .data-table img { width: 34px; height: 51px; object-fit: cover; border-radius: 2px; }
  .btn-sm { min-height: 36px; padding: var(--space-2) var(--space-4); font-size: var(--fs-sm); }
  .table-wrap { overflow-x: auto; }
  @media (max-width: 860px) {
    .admin-shell { grid-template-columns: 1fr; }
    .admin-side { position: static; height: auto; border-right: 0; border-bottom: 1px solid var(--border); }
  }
</style>
</head>
<body>
<div class="admin-shell">
  <aside class="admin-side">
    <a class="brand" href="<?= e(url('index.php')) ?>" style="font-size:1.25rem">HieuMini<span>.</span></a>
    <p style="font-size:var(--fs-xs);color:var(--fg-subtle);margin:var(--space-2) 0 0">Khu vực quản trị</p>

    <h4>Nội dung</h4>
    <a href="<?= e(admin_url('index.php')) ?>"    class="<?= current_page() === 'index.php' ? 'is-active' : '' ?>">Bảng điều khiển</a>
    <a href="<?= e(admin_url('books.php')) ?>"    class="<?= in_array(current_page(), ['books.php', 'book_form.php'], true) ? 'is-active' : '' ?>">Sách</a>
    <a href="<?= e(admin_url('reviews.php')) ?>"  class="<?= current_page() === 'reviews.php' ? 'is-active' : '' ?>">Đánh giá</a>
    <a href="<?= e(admin_url('messages.php')) ?>" class="<?= current_page() === 'messages.php' ? 'is-active' : '' ?>">Tin nhắn</a>

    <h4>Xem trang</h4>
    <a href="<?= e(url('index.php')) ?>" target="_blank" rel="noopener">Mở website ↗</a>

    <h4>Tài khoản</h4>
    <p style="font-size:var(--fs-sm);color:var(--fg-muted);margin:0 0 var(--space-3)">
      <?= e($admin['full_name'] ?? $admin['username']) ?>
    </p>
    <form method="post" action="<?= e(admin_url('logout.php')) ?>">
      <?= csrf_field() ?>
      <button class="btn btn--ghost btn-sm" type="submit" style="width:100%">Đăng xuất</button>
    </form>
  </aside>

  <main class="admin-main">
    <?php foreach (flash_pull() as $f): ?>
      <div class="alert alert--<?= $f['type'] === 'error' ? 'error' : 'ok' ?>" role="status"><?= e($f['message']) ?></div>
    <?php endforeach; ?>
