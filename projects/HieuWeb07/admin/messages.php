<?php
/** Hộp thư liên hệ. */
declare(strict_types=1);

require __DIR__ . '/guard.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $id     = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'read') {
        db_query('UPDATE messages SET is_read = 1 WHERE id = :id', [':id' => $id]);
    } elseif ($action === 'unread') {
        db_query('UPDATE messages SET is_read = 0 WHERE id = :id', [':id' => $id]);
    } elseif ($action === 'delete') {
        db_query('DELETE FROM messages WHERE id = :id', [':id' => $id]);
        flash('ok', 'Đã xoá tin nhắn.');
    }
    header('Location: ' . admin_url('messages.php'));
    exit;
}

$messages = db_all('SELECT * FROM messages ORDER BY is_read ASC, created_at DESC');
$unread   = (int) db_value('SELECT COUNT(*) FROM messages WHERE is_read = 0');

$adminTitle = 'Tin nhắn';
require __DIR__ . '/layout.php';
?>

<div class="admin-head">
  <div>
    <h1 style="font-size:var(--fs-2xl);margin:0">Tin nhắn liên hệ</h1>
    <p style="margin:var(--space-2) 0 0"><?= $unread ?> tin chưa đọc trên tổng số <?= count($messages) ?>.</p>
  </div>
</div>

<?php if (!$messages): ?>
  <div class="empty-state">
    <?= icon('mail') ?>
    <h3>Chưa có tin nhắn nào</h3>
    <p>Tin nhắn gửi từ trang Liên hệ sẽ hiện ở đây.</p>
  </div>
<?php else: ?>
  <?php foreach ($messages as $m): ?>
    <article style="padding:var(--space-5);border:1px solid <?= (int) $m['is_read'] ? 'var(--border)' : 'var(--accent)' ?>;
                    border-radius:var(--r-md);margin-bottom:var(--space-4);background:var(--surface)">
      <div style="display:flex;align-items:center;gap:var(--space-3);flex-wrap:wrap;margin-bottom:var(--space-3)">
        <strong><?= e($m['name']) ?></strong>
        <a href="mailto:<?= e($m['email']) ?>" style="font-size:var(--fs-sm)"><?= e($m['email']) ?></a>
        <?php if (!(int) $m['is_read']): ?><span class="badge">Mới</span><?php endif; ?>
        <time style="margin-left:auto;color:var(--fg-subtle);font-size:var(--fs-xs)"
              datetime="<?= e($m['created_at']) ?>"><?= e(date('d/m/Y H:i', strtotime($m['created_at']))) ?></time>
      </div>

      <?php if ($m['subject']): ?>
        <p style="margin:0 0 var(--space-2);color:var(--fg)"><strong><?= e($m['subject']) ?></strong></p>
      <?php endif; ?>
      <p style="margin:0 0 var(--space-4);white-space:pre-line"><?= e($m['content']) ?></p>

      <form method="post" style="display:flex;gap:var(--space-2)">
        <?= csrf_field() ?>
        <input type="hidden" name="id" value="<?= (int) $m['id'] ?>">
        <?php if ((int) $m['is_read']): ?>
          <button class="btn btn--ghost btn-sm" name="action" value="unread" type="submit">Đánh dấu chưa đọc</button>
        <?php else: ?>
          <button class="btn btn--primary btn-sm" name="action" value="read" type="submit">Đánh dấu đã đọc</button>
        <?php endif; ?>
        <button class="btn btn--ghost btn-sm" name="action" value="delete" type="submit"
                style="color:var(--danger);border-color:var(--danger)"
                onclick="return confirm('Xoá tin nhắn này?')">Xoá</button>
      </form>
    </article>
  <?php endforeach; ?>
<?php endif; ?>

<?php require __DIR__ . '/layout_end.php'; ?>
