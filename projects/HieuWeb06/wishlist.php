<?php
/**
 * HieuMini - Danh sách dự án yêu thích
 */
require_once __DIR__ . '/includes/config.php';
require_login();

$stmt = $pdo->prepare('SELECT p.*, c.name AS category_name FROM wishlists w
                       JOIN projects p ON p.id = w.project_id
                       JOIN categories c ON c.id = p.category_id
                       WHERE w.user_id = ? AND p.status = 1
                       ORDER BY w.created_at DESC');
$stmt->execute([current_user_id()]);
$items = $stmt->fetchAll();

seo([
    'title'  => 'Dự án yêu thích | ' . SITE_NAME,
    'robots' => 'noindex, nofollow',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container page-head">
  <?= breadcrumb(['Trang chủ' => 'index.php', 'Yêu thích' => null]) ?>
  <h1 style="margin-top:var(--sp-4)">Dự án bạn đã lưu</h1>
  <p><?= count($items) ?> dự án trong danh sách yêu thích.</p>
</div>

<div class="container section--tight">
  <?php if ($items): ?>
    <div class="grid grid--3 stagger">
      <?php foreach ($items as $p) { include __DIR__ . '/includes/project-card.php'; } ?>
    </div>
  <?php else: ?>
    <div class="glass empty-state reveal">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20s-7-4.4-7-9.2A4 4 0 0 1 12 8a4 4 0 0 1 7 2.8C19 15.6 12 20 12 20z"/></svg>
      <h2>Chưa có dự án yêu thích</h2>
      <p>Bấm biểu tượng trái tim ở góc mỗi thẻ dự án để lưu lại và so sánh sau.</p>
      <a class="btn btn--primary btn--lg" href="<?= e(url('projects.php')) ?>">Khám phá kho dự án</a>
    </div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
