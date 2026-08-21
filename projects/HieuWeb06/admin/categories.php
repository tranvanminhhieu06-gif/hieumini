<?php
/**
 * HieuMini Admin - Quản lý danh mục
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $action = input('action');
    $id     = (int)($_POST['id'] ?? 0);

    if ($action === 'save') {
        $name = input('name');
        $slug = input('slug') !== '' ? slugify(input('slug')) : slugify($name);
        $desc = input('description');
        $sort = (int)($_POST['sort_order'] ?? 0);

        if (mb_strlen($name, 'UTF-8') < 2) {
            flash('Tên danh mục quá ngắn.', 'error');
        } else {
            if ($id > 0) {
                $pdo->prepare('UPDATE categories SET name=?, slug=?, description=?, sort_order=? WHERE id=?')
                    ->execute([$name, $slug, $desc, $sort, $id]);
                flash('Đã cập nhật danh mục.');
            } else {
                $pdo->prepare('INSERT INTO categories (name, slug, description, sort_order) VALUES (?,?,?,?)')
                    ->execute([$name, $slug, $desc, $sort]);
                flash('Đã thêm danh mục mới.');
            }
        }
    } elseif ($action === 'delete') {
        $count = $pdo->prepare('SELECT COUNT(*) FROM projects WHERE category_id = ?');
        $count->execute([$id]);
        if ((int)$count->fetchColumn() > 0) {
            flash('Không thể xoá: danh mục vẫn còn dự án bên trong.', 'error');
        } else {
            $pdo->prepare('DELETE FROM categories WHERE id = ?')->execute([$id]);
            flash('Đã xoá danh mục.');
        }
    }
    redirect('admin/categories.php');
}

$categories = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM projects p WHERE p.category_id = c.id) AS total
                           FROM categories c ORDER BY c.sort_order, c.id')->fetchAll();

$editId = (int)($_GET['edit'] ?? 0);
$editing = null;
foreach ($categories as $c) {
    if ((int)$c['id'] === $editId) {
        $editing = $c;
    }
}

$adminTitle = 'Quản lý danh mục';
require __DIR__ . '/includes/header.php';
?>

<div class="grid grid--2" style="align-items:start">
  <div class="table-wrap">
    <table class="data">
      <caption class="sr-only">Danh sách danh mục dự án</caption>
      <thead><tr><th scope="col">#</th><th scope="col">Tên danh mục</th><th scope="col">Đường dẫn</th><th scope="col">Số dự án</th><th scope="col">Thao tác</th></tr></thead>
      <tbody>
        <?php foreach ($categories as $c): ?>
          <tr>
            <td><?= (int)$c['id'] ?></td>
            <td><strong><?= e($c['name']) ?></strong><div style="font-size:var(--fs-xs);color:var(--fg-muted)"><?= e(excerpt($c['description'], 60)) ?></div></td>
            <td><code><?= e($c['slug']) ?></code></td>
            <td><?= (int)$c['total'] ?></td>
            <td>
              <div style="display:flex;gap:6px;justify-content:flex-end">
                <a class="btn btn--ghost btn--sm" href="?edit=<?= (int)$c['id'] ?>">Sửa</a>
                <form method="post" onsubmit="return confirm('Xoá danh mục này?')">
                  <?= csrf_field() ?>
                  <input type="hidden" name="action" value="delete">
                  <input type="hidden" name="id" value="<?= (int)$c['id'] ?>">
                  <button class="btn btn--danger btn--sm" type="submit">Xoá</button>
                </form>
              </div>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>

  <form method="post" class="glass" style="padding:var(--sp-5)" data-validate novalidate>
    <?= csrf_field() ?>
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="id" value="<?= (int)($editing['id'] ?? 0) ?>">
    <h2 style="font-size:var(--fs-lg)"><?= $editing ? 'Sửa danh mục' : 'Thêm danh mục mới' ?></h2>
    <div class="form-grid">
      <div class="field">
        <label for="name">Tên danh mục <span aria-hidden="true">*</span></label>
        <input type="text" id="name" name="name" required value="<?= e((string)($editing['name'] ?? '')) ?>">
      </div>
      <div class="field">
        <label for="slug">Đường dẫn</label>
        <input type="text" id="slug" name="slug" value="<?= e((string)($editing['slug'] ?? '')) ?>">
        <span class="hint">Để trống sẽ tự sinh từ tên danh mục.</span>
      </div>
      <div class="field">
        <label for="description">Mô tả ngắn</label>
        <textarea id="description" name="description" maxlength="255" style="min-height:90px"><?= e((string)($editing['description'] ?? '')) ?></textarea>
      </div>
      <div class="field">
        <label for="sort_order">Thứ tự hiển thị</label>
        <input type="number" id="sort_order" name="sort_order" value="<?= (int)($editing['sort_order'] ?? 0) ?>">
      </div>
      <div style="display:flex;gap:10px">
        <button class="btn btn--primary" type="submit"><?= $editing ? 'Lưu' : 'Thêm mới' ?></button>
        <?php if ($editing): ?><a class="btn btn--ghost" href="<?= e(url('admin/categories.php')) ?>">Huỷ</a><?php endif; ?>
      </div>
    </div>
  </form>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
