<?php
/**
 * HieuMini Admin — Danh sách và xóa dự án
 */

declare(strict_types=1);
require __DIR__ . '/guard.php';
require __DIR__ . '/layout.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    if (!csrf_check()) {
        flash('error', 'Phiên làm việc đã hết hạn. Vui lòng thử lại.');
    } else {
        Database::run('DELETE FROM projects WHERE id = ?', [(int) ($_POST['id'] ?? 0)]);
        flash('success', 'Đã xóa dự án khỏi bộ sưu tập.');
    }
    redirect('admin/projects.php');
}

$keyword = trim((string) ($_GET['q'] ?? ''));

if ($keyword !== '') {
    $projects = Database::all(
        'SELECT * FROM projects WHERE name LIKE ? OR code LIKE ? OR category LIKE ?
          ORDER BY sort_order ASC, id ASC',
        ["%$keyword%", "%$keyword%", "%$keyword%"]
    );
} else {
    $projects = Database::all('SELECT * FROM projects ORDER BY sort_order ASC, id ASC');
}

admin_head('Quản lý dự án');
?>

<div class="toolbar">
  <form method="get" class="toolbar-search" role="search">
    <label class="sr-only" for="q">Tìm dự án theo tên, mã hoặc lĩnh vực</label>
    <input type="search" id="q" name="q" value="<?= e($keyword) ?>"
           placeholder="Tìm theo tên, mã hoặc lĩnh vực…">
    <button type="submit" class="btn btn--ghost btn--sm"><?= icon('search', 'ico ico-sm') ?> Tìm</button>
  </form>
  <a class="btn btn--primary btn--sm" href="<?= e(url('admin/project_form.php')) ?>">
    <?= icon('plus', 'ico ico-sm') ?> Thêm dự án
  </a>
</div>

<section class="panel" style="padding:0;overflow:hidden">
  <?php if (!$projects): ?>
    <div class="empty-state" style="border:0">
      <?= icon('inbox', 'ico') ?>
      <h3>Không có dự án nào khớp</h3>
      <p>Thử từ khóa khác hoặc thêm dự án mới.</p>
    </div>
  <?php else: ?>
  <div style="overflow-x:auto">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Mã</th><th>Tên dự án</th><th>Lĩnh vực</th><th>Thư mục</th>
          <th style="text-align:right">Lượt xem</th><th>Trạng thái</th><th style="text-align:right">Thao tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($projects as $p): ?>
        <tr>
          <td><span class="project-code" style="color:<?= e($p['accent_from']) ?>"><?= e($p['code']) ?></span></td>
          <td>
            <b><?= e($p['name']) ?></b>
            <div style="font-size:var(--fs-xs);color:var(--fg-subtle)"><?= e(excerpt($p['tagline'], 70)) ?></div>
          </td>
          <td><?= e($p['category']) ?></td>
          <td>
            <code><?= e($p['folder']) ?></code>
            <?php if (!project_exists($p)): ?>
              <span class="badge badge--danger">Thiếu</span>
            <?php endif; ?>
          </td>
          <td style="text-align:right;font-weight:600"><?= num($p['views']) ?></td>
          <td>
            <span class="badge <?= $p['status'] === 'published' ? 'badge--ok' : 'badge--muted' ?>">
              <?= $p['status'] === 'published' ? 'Hiển thị' : ($p['status'] === 'draft' ? 'Nháp' : 'Lưu trữ') ?>
            </span>
          </td>
          <td style="text-align:right;white-space:nowrap">
            <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/project_form.php?id=' . (int) $p['id'])) ?>">
              <?= icon('edit', 'ico ico-sm') ?> Sửa
            </a>
            <form method="post" style="display:inline"
                  onsubmit="return confirm('Xóa dự án <?= e(addslashes($p['name'])) ?>? Thao tác này không thể hoàn tác.')">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="delete">
              <input type="hidden" name="id" value="<?= (int) $p['id'] ?>">
              <button type="submit" class="btn btn--danger btn--sm">
                <?= icon('trash', 'ico ico-sm') ?> Xóa
              </button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
  <?php endif; ?>
</section>

<?php admin_foot(); ?>
