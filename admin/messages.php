<?php
/**
 * HieuMini Admin — Hộp thư liên hệ
 */

declare(strict_types=1);
require __DIR__ . '/guard.php';
require __DIR__ . '/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = (string) ($_POST['action'] ?? '');
    $msgId  = (int) ($_POST['id'] ?? 0);

    if (!csrf_check()) {
        flash('error', 'Phiên làm việc đã hết hạn. Vui lòng thử lại.');
    } elseif ($action === 'toggle') {
        Database::run('UPDATE messages SET is_read = 1 - is_read WHERE id = ?', [$msgId]);
        flash('success', 'Đã cập nhật trạng thái tin nhắn.');
    } elseif ($action === 'delete') {
        Database::run('DELETE FROM messages WHERE id = ?', [$msgId]);
        flash('success', 'Đã xóa tin nhắn.');
    }
    redirect('admin/messages.php');
}

$filter = $_GET['filter'] ?? 'all';

$messages = match ($filter) {
    'unread' => Database::all('SELECT * FROM messages WHERE is_read = 0 ORDER BY created_at DESC'),
    'read'   => Database::all('SELECT * FROM messages WHERE is_read = 1 ORDER BY created_at DESC'),
    default  => Database::all('SELECT * FROM messages ORDER BY created_at DESC'),
};

$counts = [
    'all'    => (int) Database::scalar('SELECT COUNT(*) FROM messages'),
    'unread' => (int) Database::scalar('SELECT COUNT(*) FROM messages WHERE is_read = 0'),
    'read'   => (int) Database::scalar('SELECT COUNT(*) FROM messages WHERE is_read = 1'),
];

admin_head('Tin nhắn liên hệ');
?>

<div class="filter-bar">
  <?php
  $tabs = ['all' => 'Tất cả', 'unread' => 'Chưa đọc', 'read' => 'Đã đọc'];
  foreach ($tabs as $key => $label): ?>
    <a class="chip<?= $filter === $key ? ' is-active' : '' ?>"
       href="<?= e(url('admin/messages.php?filter=' . $key)) ?>">
      <?= e($label) ?> (<?= $counts[$key] ?>)
    </a>
  <?php endforeach; ?>
</div>

<?php if (!$messages): ?>
  <div class="empty-state">
    <?= icon('inbox', 'ico') ?>
    <h3>Hộp thư trống</h3>
    <p>Chưa có tin nhắn nào ở mục này.</p>
  </div>
<?php else: ?>
  <div class="msg-list">
    <?php foreach ($messages as $m): ?>
      <article class="panel msg<?= $m['is_read'] ? '' : ' is-unread' ?>" data-reveal>
        <div class="msg-head">
          <div>
            <b><?= e($m['name']) ?></b>
            <a href="mailto:<?= e($m['email']) ?>" style="font-size:var(--fs-sm)"><?= e($m['email']) ?></a>
          </div>
          <div class="msg-actions">
            <span class="badge <?= $m['is_read'] ? 'badge--muted' : 'badge--warn' ?>">
              <?= $m['is_read'] ? 'Đã đọc' : 'Chưa đọc' ?>
            </span>
            <span style="font-size:var(--fs-xs);color:var(--fg-subtle)"><?= e(vn_date($m['created_at'], true)) ?></span>
          </div>
        </div>

        <h3 style="margin:12px 0 8px;font-size:var(--fs-base)"><?= e($m['subject']) ?></h3>
        <p style="margin:0;white-space:pre-line"><?= e($m['content']) ?></p>

        <div class="toolbar" style="margin:16px 0 0;justify-content:flex-end">
          <form method="post" style="display:inline">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="toggle">
            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
            <button type="submit" class="btn btn--ghost btn--sm">
              <?= icon('check', 'ico ico-sm') ?> Đánh dấu <?= $m['is_read'] ? 'chưa đọc' : 'đã đọc' ?>
            </button>
          </form>
          <form method="post" style="display:inline"
                onsubmit="return confirm('Xóa vĩnh viễn tin nhắn này?')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
            <button type="submit" class="btn btn--danger btn--sm">
              <?= icon('trash', 'ico ico-sm') ?> Xóa
            </button>
          </form>
        </div>
      </article>
    <?php endforeach; ?>
  </div>
<?php endif; ?>

<?php admin_foot(); ?>
