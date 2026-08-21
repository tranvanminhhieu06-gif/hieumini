<?php
/**
 * HieuMini Admin - Quản lý bài viết
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $id = (int)($_POST['id'] ?? 0);
    $action = input('action');

    if ($action === 'delete') {
        $pdo->prepare('DELETE FROM posts WHERE id = ?')->execute([$id]);
        flash('Đã xoá bài viết.');
    } elseif ($action === 'toggle') {
        $pdo->prepare('UPDATE posts SET status = 1 - status WHERE id = ?')->execute([$id]);
        flash('Đã đổi trạng thái xuất bản.');
    }
    redirect('admin/posts.php');
}

$posts = $pdo->query('SELECT * FROM posts ORDER BY published_at DESC')->fetchAll();

$adminTitle = 'Quản lý bài viết';
$adminActions = '<a class="btn btn--primary" href="' . e(url('admin/post-form.php')) . '">+ Viết bài mới</a>';
require __DIR__ . '/includes/header.php';
?>

<div class="table-wrap">
  <table class="data">
    <caption class="sr-only">Danh sách bài viết blog</caption>
    <thead>
      <tr><th scope="col">#</th><th scope="col">Tiêu đề</th><th scope="col">Thẻ</th><th scope="col">Lượt đọc</th><th scope="col">Xuất bản</th><th scope="col">Trạng thái</th><th scope="col">Thao tác</th></tr>
    </thead>
    <tbody>
      <?php foreach ($posts as $p): ?>
        <tr>
          <td><?= (int)$p['id'] ?></td>
          <td>
            <strong><?= e(excerpt($p['title'], 56)) ?></strong>
            <div style="font-size:var(--fs-xs);color:var(--fg-muted)"><?= e($p['slug']) ?></div>
          </td>
          <td style="font-size:var(--fs-xs)"><?= e((string)$p['tags']) ?></td>
          <td><?= num((int)$p['views']) ?></td>
          <td><?= e(vn_date($p['published_at'])) ?></td>
          <td><span class="badge <?= $p['status'] ? 'badge--ok' : 'badge--wait' ?>"><?= $p['status'] ? 'Đã đăng' : 'Bản nháp' ?></span></td>
          <td>
            <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap">
              <a class="btn btn--ghost btn--sm" href="<?= e(url('blog-detail.php?slug=' . $p['slug'])) ?>" target="_blank" rel="noopener">Xem</a>
              <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/post-form.php?id=' . $p['id'])) ?>">Sửa</a>
              <form method="post"><?= csrf_field() ?>
                <input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
                <button class="btn btn--ghost btn--sm" type="submit"><?= $p['status'] ? 'Ẩn' : 'Đăng' ?></button>
              </form>
              <form method="post" onsubmit="return confirm('Xoá bài viết này?')"><?= csrf_field() ?>
                <input type="hidden" name="action" value="delete"><input type="hidden" name="id" value="<?= (int)$p['id'] ?>">
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
