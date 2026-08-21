<?php
/**
 * HieuMini Admin - Danh sách dự án
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $id = (int)($_POST['id'] ?? 0);
    $action = input('action');

    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM projects WHERE id = ?')->execute([$id]);
        flash('Đã xoá dự án khỏi hệ thống.');
    } elseif ($action === 'toggle') {
        $pdo->prepare('UPDATE projects SET status = 1 - status WHERE id = ?')->execute([$id]);
        flash('Đã đổi trạng thái hiển thị của dự án.');
    } elseif ($action === 'feature') {
        $pdo->prepare('UPDATE projects SET is_featured = 1 - is_featured WHERE id = ?')->execute([$id]);
        flash('Đã đổi trạng thái nổi bật.');
    }
    redirect('admin/projects.php');
}

$q = trim((string)($_GET['q'] ?? ''));
$sql = 'SELECT p.*, c.name AS category_name FROM projects p JOIN categories c ON c.id = p.category_id';
$params = [];
if ($q !== '') {
    $sql .= ' WHERE p.title LIKE ?';
    $params[] = '%' . $q . '%';
}
$sql .= ' ORDER BY p.id DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$projects = $stmt->fetchAll();

$adminTitle = 'Quản lý dự án';
$adminActions = '<a class="btn btn--primary" href="' . e(url('admin/project-form.php')) . '">+ Thêm dự án mới</a>';
require __DIR__ . '/includes/header.php';
?>

<form method="get" class="toolbar glass" style="padding:var(--sp-4)">
  <div style="display:flex;gap:8px;flex:1;max-width:460px">
    <label class="sr-only" for="q">Tìm theo tên dự án</label>
    <input class="input" type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="Tìm theo tên dự án…">
    <button class="btn btn--ghost btn--sm" type="submit">Tìm</button>
  </div>
  <span class="result-count"><?= count($projects) ?> dự án</span>
</form>

<div class="table-wrap" style="margin-top:var(--sp-4)">
  <table class="data">
    <caption class="sr-only">Danh sách toàn bộ dự án</caption>
    <thead>
      <tr>
        <th scope="col">#</th><th scope="col">Dự án</th><th scope="col">Danh mục</th>
        <th scope="col">Giá bán</th><th scope="col">Đã bán</th><th scope="col">Trạng thái</th><th scope="col">Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($projects as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td>
            <div style="display:flex;gap:10px;align-items:center">
              <img src="<?= e(asset($p['thumbnail'])) ?>" alt="" width="56" height="35" style="border-radius:6px;object-fit:cover" loading="lazy">
              <div>
                <strong><?= e(excerpt($p['title'], 44)) ?></strong>
                <?php if ($p['is_featured']): ?><span class="badge badge--new" style="margin-left:6px">Nổi bật</span><?php endif; ?>
                <div style="font-size:var(--fs-xs);color:var(--fg-muted)"><?= e($p['slug']) ?></div>
              </div>
            </div>
          </td>
          <td><?= e($p['category_name']) ?></td>
          <td><?= money(final_price($p)) ?></td>
          <td><?= num((int)$p['sales']) ?></td>
          <td>
            <span class="badge <?= $p['status'] ? 'badge--ok' : 'badge--off' ?>"><?= $p['status'] ? 'Đang bán' : 'Đã ẩn' ?></span>
          </td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap">
              <a class="btn btn--ghost btn--sm" href="<?= e(url('project-detail.php?slug=' . $p['slug'])) ?>" target="_blank" rel="noopener">Xem</a>
              <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/project-form.php?id=' . $p['id'])) ?>">Sửa</a>
              <form method="post" style="display:inline">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="toggle">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button class="btn btn--ghost btn--sm" type="submit"><?= $p['status'] ? 'Ẩn' : 'Hiện' ?></button>
              </form>
              <form method="post" style="display:inline" onsubmit="return confirm('Xoá vĩnh viễn dự án này? Thao tác không thể hoàn tác.')">
                <?= csrf_field() ?>
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button class="btn btn--danger btn--sm" type="submit">Xoá</button>
              </form>
            </div>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
