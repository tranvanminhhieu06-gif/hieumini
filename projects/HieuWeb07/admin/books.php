<?php
/** Quản lý sách — danh sách, tìm kiếm, sửa, xoá. */
declare(strict_types=1);

require __DIR__ . '/guard.php';
$admin = require_admin();

// ---------- Xoá ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'delete') {
    require_post_csrf();
    $id = (int) ($_POST['id'] ?? 0);
    $book = db_one('SELECT title FROM books WHERE id = :id', [':id' => $id]);
    if ($book) {
        db_query('DELETE FROM books WHERE id = :id', [':id' => $id]);
        flash('ok', 'Đã xoá sách “' . $book['title'] . '” cùng các đánh giá của nó.');
    }
    header('Location: ' . admin_url('books.php'));
    exit;
}

$q = trim((string) ($_GET['q'] ?? ''));
$params = [];
$where = '';
if ($q !== '') {
    $where = ' WHERE b.title LIKE :q OR a.name LIKE :q';
    $params[':q'] = '%' . $q . '%';
}

$books = db_all(book_select_sql() . $where . ' ORDER BY b.id DESC', $params);

$adminTitle = 'Quản lý sách';
require __DIR__ . '/layout.php';
?>

<div class="admin-head">
  <div>
    <h1 style="font-size:var(--fs-2xl);margin:0">Sách</h1>
    <p style="margin:var(--space-2) 0 0"><?= count($books) ?> đầu sách<?= $q !== '' ? ' khớp từ khoá “' . e($q) . '”' : '' ?>.</p>
  </div>
  <a class="btn btn--primary btn-sm" href="<?= e(admin_url('book_form.php')) ?>">+ Thêm sách</a>
</div>

<form method="get" class="search-box" style="margin-bottom:var(--space-6);max-width:420px">
  <?= icon('search', 'search-ico') ?>
  <label for="q" class="skip-link">Tìm sách</label>
  <input type="search" id="q" name="q" value="<?= e($q) ?>" placeholder="Tìm theo tựa hoặc tác giả…">
</form>

<div class="table-wrap">
<table class="data-table">
  <thead>
    <tr><th>Bìa</th><th>Tựa sách</th><th>Tác giả</th><th>Thể loại</th><th>Năm</th><th>Xem</th><th>Thao tác</th></tr>
  </thead>
  <tbody>
    <?php if (!$books): ?>
      <tr><td colspan="7" style="padding:var(--space-7);text-align:center;color:var(--fg-subtle)">
        Không có sách nào khớp. <a href="<?= e(admin_url('books.php')) ?>">Xoá bộ lọc</a>
      </td></tr>
    <?php endif; ?>

    <?php foreach ($books as $b): ?>
      <tr>
        <td><img src="<?= e(cover_url($b['cover'])) ?>" alt=""></td>
        <td>
          <a href="<?= e(url('book.php?s=' . $b['slug'])) ?>" target="_blank" rel="noopener"><?= e($b['title']) ?></a>
          <?php if ((int) $b['is_featured']): ?><span class="badge" style="margin-left:6px">Nổi bật</span><?php endif; ?>
        </td>
        <td><?= e($b['author_name']) ?></td>
        <td style="color:var(--fg-subtle)"><?= e($b['category_name']) ?></td>
        <td><?= e(format_year((int) $b['published_year'])) ?></td>
        <td><?= format_number((int) $b['views']) ?></td>
        <td style="white-space:nowrap">
          <a class="btn btn--ghost btn-sm" href="<?= e(admin_url('book_form.php?id=' . $b['id'])) ?>">Sửa</a>
          <?php /* Xoá phải là POST kèm CSRF: nếu để dạng link, chỉ cần bot quét qua
                   là dữ liệu bay sạch. onsubmit chỉ để hỏi lại cho chắc. */ ?>
          <form method="post" style="display:inline"
                onsubmit="return confirm('Xoá “<?= e(addslashes($b['title'])) ?>”? Các đánh giá của sách này cũng bị xoá theo.')">
            <?= csrf_field() ?>
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="id" value="<?= (int) $b['id'] ?>">
            <button class="btn btn--ghost btn-sm" type="submit" style="color:var(--danger);border-color:var(--danger)">Xoá</button>
          </form>
        </td>
      </tr>
    <?php endforeach; ?>
  </tbody>
</table>
</div>

<?php require __DIR__ . '/layout_end.php'; ?>
