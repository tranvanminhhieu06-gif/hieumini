<?php
/** Duyệt đánh giá của bạn đọc trước khi cho hiển thị công khai. */
declare(strict_types=1);

require __DIR__ . '/guard.php';
$admin = require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();
    $id     = (int) ($_POST['id'] ?? 0);
    $action = (string) ($_POST['action'] ?? '');

    if ($action === 'approve') {
        db_query('UPDATE reviews SET is_approved = 1 WHERE id = :id', [':id' => $id]);
        flash('ok', 'Đã duyệt đánh giá.');
    } elseif ($action === 'hide') {
        db_query('UPDATE reviews SET is_approved = 0 WHERE id = :id', [':id' => $id]);
        flash('ok', 'Đã ẩn đánh giá khỏi trang công khai.');
    } elseif ($action === 'delete') {
        db_query('DELETE FROM reviews WHERE id = :id', [':id' => $id]);
        flash('ok', 'Đã xoá đánh giá.');
    }
    header('Location: ' . admin_url('reviews.php' . (($_POST['back'] ?? '') !== '' ? '?loc=' . urlencode((string) $_POST['back']) : '')));
    exit;
}

$filter = (string) ($_GET['loc'] ?? 'cho-duyet');
$where  = match ($filter) {
    'da-duyet' => ' WHERE r.is_approved = 1',
    'tat-ca'   => '',
    default    => ' WHERE r.is_approved = 0',
};

$reviews = db_all(
    'SELECT r.*, b.title, b.slug FROM reviews r JOIN books b ON b.id = r.book_id'
    . $where . ' ORDER BY r.created_at DESC'
);
$pending = (int) db_value('SELECT COUNT(*) FROM reviews WHERE is_approved = 0');

$adminTitle = 'Đánh giá';
require __DIR__ . '/layout.php';
?>

<div class="admin-head">
  <div>
    <h1 style="font-size:var(--fs-2xl);margin:0">Đánh giá</h1>
    <p style="margin:var(--space-2) 0 0"><?= $pending ?> đánh giá đang chờ duyệt.</p>
  </div>
</div>

<div class="filter-chips" style="margin-bottom:var(--space-6)">
  <a class="chip <?= $filter === 'cho-duyet' ? 'is-active' : '' ?>" href="<?= e(admin_url('reviews.php?loc=cho-duyet')) ?>">Chờ duyệt</a>
  <a class="chip <?= $filter === 'da-duyet' ? 'is-active' : '' ?>"  href="<?= e(admin_url('reviews.php?loc=da-duyet')) ?>">Đã duyệt</a>
  <a class="chip <?= $filter === 'tat-ca' ? 'is-active' : '' ?>"    href="<?= e(admin_url('reviews.php?loc=tat-ca')) ?>">Tất cả</a>
</div>

<?php if (!$reviews): ?>
  <div class="empty-state">
    <?= icon('check') ?>
    <h3>Không có đánh giá nào ở mục này</h3>
    <p>Khi bạn đọc gửi đánh giá mới, chúng sẽ xuất hiện ở tab “Chờ duyệt”.</p>
  </div>
<?php else: ?>
  <div class="table-wrap">
  <table class="data-table">
    <thead><tr><th>Sách</th><th>Người gửi</th><th>Điểm</th><th>Nội dung</th><th>Ngày</th><th>Thao tác</th></tr></thead>
    <tbody>
      <?php foreach ($reviews as $r): ?>
        <tr>
          <td><a href="<?= e(url('book.php?s=' . $r['slug'])) ?>" target="_blank" rel="noopener"><?= e($r['title']) ?></a></td>
          <td><?= e($r['reader_name']) ?></td>
          <td><?= (int) $r['rating'] ?>/5</td>
          <td style="max-width:340px;color:var(--fg-muted)"><?= e(excerpt($r['content'], 120)) ?></td>
          <td style="white-space:nowrap;color:var(--fg-subtle)"><?= e(date('d/m/Y', strtotime($r['created_at']))) ?></td>
          <td style="white-space:nowrap">
            <form method="post" style="display:inline">
              <?= csrf_field() ?>
              <input type="hidden" name="back" value="<?= e($filter) ?>">
              <input type="hidden" name="id" value="<?= (int) $r['id'] ?>">
              <?php if ((int) $r['is_approved'] === 0): ?>
                <button class="btn btn--primary btn-sm" name="action" value="approve" type="submit">Duyệt</button>
              <?php else: ?>
                <button class="btn btn--ghost btn-sm" name="action" value="hide" type="submit">Ẩn</button>
              <?php endif; ?>
              <button class="btn btn--ghost btn-sm" name="action" value="delete" type="submit"
                      style="color:var(--danger);border-color:var(--danger)"
                      onclick="return confirm('Xoá hẳn đánh giá này?')">Xoá</button>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
  </div>
<?php endif; ?>

<?php require __DIR__ . '/layout_end.php'; ?>
