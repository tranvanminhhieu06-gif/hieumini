<?php
/**
 * HieuMini Admin - Duyệt đánh giá
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $id = (int)($_POST['id'] ?? 0);
    $action = input('action');

    $get = $pdo->prepare('SELECT project_id FROM reviews WHERE id = ?');
    $get->execute([$id]);
    $projectId = (int)$get->fetchColumn();

    if ($action === 'toggle') {
        $pdo->prepare('UPDATE reviews SET status = 1 - status WHERE id = ?')->execute([$id]);
        flash('Đã đổi trạng thái hiển thị của đánh giá.');
    } elseif ($action === 'delete') {
        $pdo->prepare('DELETE FROM reviews WHERE id = ?')->execute([$id]);
        flash('Đã xoá đánh giá.');
    }
    if ($projectId) {
        refresh_rating($pdo, $projectId);
    }
    redirect('admin/reviews.php');
}

$reviews = $pdo->query('SELECT r.*, u.full_name, u.email, p.title AS project_title, p.slug
                        FROM reviews r
                        JOIN users u ON u.id = r.user_id
                        JOIN projects p ON p.id = r.project_id
                        ORDER BY r.created_at DESC')->fetchAll();

$adminTitle = 'Quản lý đánh giá';
require __DIR__ . '/includes/header.php';
?>

<div class="table-wrap">
  <table class="data">
    <caption class="sr-only">Danh sách đánh giá của khách hàng</caption>
    <thead>
      <tr><th scope="col">#</th><th scope="col">Dự án</th><th scope="col">Người đánh giá</th><th scope="col">Nội dung</th><th scope="col">Sao</th><th scope="col">Trạng thái</th><th scope="col">Thao tác</th></tr>
    </thead>
    <tbody>
      <?php foreach ($reviews as $r): ?>
        <tr>
          <td><?= (int)$r['id'] ?></td>
          <td><a href="<?= e(url('project-detail.php?slug=' . $r['slug'])) ?>" target="_blank" rel="noopener"><?= e(excerpt($r['project_title'], 34)) ?></a></td>
          <td><?= e($r['full_name']) ?><div style="font-size:var(--fs-xs);color:var(--fg-muted)"><?= e(vn_date($r['created_at'])) ?></div></td>
          <td style="max-width:340px"><?= e(excerpt($r['content'], 130)) ?></td>
          <td><?= stars((float)$r['rating']) ?></td>
          <td><span class="badge <?= $r['status'] ? 'badge--ok' : 'badge--wait' ?>"><?= $r['status'] ? 'Hiển thị' : 'Đang ẩn' ?></span></td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end">
              <form method="post"><?= csrf_field() ?>
                <input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn--ghost btn--sm" type="submit"><?= $r['status'] ? 'Ẩn' : 'Duyệt' ?></button>
              </form>
              <form method="post" onsubmit="return confirm('Xoá đánh giá này?')"><?= csrf_field() ?>
                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$r['id'] ?>">
                <button class="btn btn--danger btn--sm" type="submit">Xoá</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$reviews): ?>
        <tr><td colspan="7" style="text-align:center;color:var(--fg-muted)">Chưa có đánh giá nào.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
