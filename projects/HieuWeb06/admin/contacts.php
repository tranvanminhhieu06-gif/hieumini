<?php
/**
 * HieuMini Admin - Hộp thư liên hệ
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $id = (int)($_POST['id'] ?? 0);
    $action = input('action');

    if ($action === 'status') {
        $status = $_POST['status'] ?? 'new';
        if (in_array($status, ['new', 'processing', 'done'], true)) {
            $pdo->prepare('UPDATE contacts SET status = ? WHERE id = ?')->execute([$status, $id]);
            flash('Đã cập nhật trạng thái xử lý.');
        }
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM contacts WHERE id = ?')->execute([$id]);
        flash('Đã xoá liên hệ.');
    }
    redirect('admin/contacts.php');
}

$contacts = $pdo->query('SELECT * FROM contacts ORDER BY
                         FIELD(status, "new", "processing", "done"), created_at DESC')->fetchAll();

$statusLabel = ['new' => 'Mới', 'processing' => 'Đang xử lý', 'done' => 'Đã xong'];
$statusClass = ['new' => 'badge--hot', 'processing' => 'badge--wait', 'done' => 'badge--ok'];

$adminTitle = 'Hộp thư liên hệ';
require __DIR__ . '/includes/header.php';
?>

<div class="stack-gap">
  <?php foreach ($contacts as $c): ?>
    <article class="glass" style="padding:var(--sp-5)">
      <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between;margin-bottom:var(--sp-3)">
        <div>
          <strong style="font-size:var(--fs-lg)"><?= e($c['subject'] ?: 'Liên hệ từ website') ?></strong>
          <div style="font-size:var(--fs-sm);color:var(--fg-muted)">
            <?= e($c['name']) ?> ·
            <a href="mailto:<?= e($c['email']) ?>"><?= e($c['email']) ?></a>
            <?php if ($c['phone']): ?> · <a href="tel:<?= e($c['phone']) ?>"><?= e($c['phone']) ?></a><?php endif; ?>
            · <?= e(vn_date($c['created_at'], true)) ?>
          </div>
        </div>
        <span class="badge <?= $statusClass[$c['status']] ?>"><?= e($statusLabel[$c['status']]) ?></span>
      </div>

      <p style="white-space:pre-line;margin:0 0 var(--sp-4)"><?= e($c['message']) ?></p>

      <div style="display:flex;gap:8px;flex-wrap:wrap">
        <form method="post" style="display:flex;gap:8px;align-items:center">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="status">
          <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <label class="sr-only" for="ct<?= (int)$c['id'] ?>">Trạng thái xử lý</label>
          <select class="input" id="ct<?= (int)$c['id'] ?>" name="status" onchange="this.form.submit()" style="min-width:160px">
            <?php foreach ($statusLabel as $key => $label): ?>
              <option value="<?= e($key) ?>" <?= $c['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
          <noscript><button class="btn btn--ghost btn--sm" type="submit">Lưu</button></noscript>
        </form>
        <a class="btn btn--ghost btn--sm" href="mailto:<?= e($c['email']) ?>?subject=<?= e(rawurlencode('Phản hồi: ' . ($c['subject'] ?: 'Liên hệ từ website'))) ?>">Trả lời email</a>
        <form method="post" onsubmit="return confirm('Xoá liên hệ này?')">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="delete">
          <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
          <button class="btn btn--danger btn--sm" type="submit">Xoá</button>
        </form>
      </div>
    </article>
  <?php endforeach; ?>

  <?php if (!$contacts): ?>
    <div class="glass empty-state"><h2>Hộp thư trống</h2><p>Chưa có liên hệ nào từ khách hàng.</p></div>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
