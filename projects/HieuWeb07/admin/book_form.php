<?php
/** Thêm mới hoặc sửa một cuốn sách. */
declare(strict_types=1);

require __DIR__ . '/guard.php';
$admin = require_admin();

$id     = (int) ($_GET['id'] ?? 0);
$isEdit = $id > 0;
$book   = $isEdit ? db_one('SELECT * FROM books WHERE id = :id', [':id' => $id]) : null;

if ($isEdit && !$book) {
    flash('error', 'Không tìm thấy cuốn sách cần sửa.');
    header('Location: ' . admin_url('books.php'));
    exit;
}

$cats    = all_categories();
$authors = db_all('SELECT id, name FROM authors ORDER BY name');
$errors  = [];

// Giá trị hiển thị trên biểu mẫu: ưu tiên dữ liệu vừa gửi lên (để người dùng
// không phải gõ lại khi có lỗi), sau đó mới tới dữ liệu trong CSDL.
$val = static function (string $key, $default = '') use ($book) {
    return $_POST[$key] ?? $book[$key] ?? $default;
};

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    require_post_csrf();

    $data = [
        'title'          => trim((string) ($_POST['title'] ?? '')),
        'author_id'      => (int) ($_POST['author_id'] ?? 0),
        'category_id'    => (int) ($_POST['category_id'] ?? 0),
        'publisher'      => trim((string) ($_POST['publisher'] ?? '')),
        'published_year' => (int) ($_POST['published_year'] ?? 0),
        'pages'          => (int) ($_POST['pages'] ?? 0),
        'language'       => trim((string) ($_POST['language'] ?? 'Tiếng Việt')),
        'isbn'           => trim((string) ($_POST['isbn'] ?? '')),
        'cover'          => trim((string) ($_POST['cover'] ?? '')),
        'summary'        => trim((string) ($_POST['summary'] ?? '')),
        'is_featured'    => isset($_POST['is_featured']) ? 1 : 0,
    ];

    if (mb_strlen($data['title']) < 2)   $errors['title']       = 'Tựa sách cần ít nhất 2 ký tự.';
    if (!$data['author_id'])             $errors['author_id']   = 'Chọn tác giả.';
    if (!$data['category_id'])           $errors['category_id'] = 'Chọn thể loại.';
    if ($data['pages'] < 1)              $errors['pages']       = 'Số trang phải lớn hơn 0.';
    if (mb_strlen($data['summary']) < 20) $errors['summary']    = 'Tóm tắt cần ít nhất 20 ký tự.';

    $slug = slugify($data['title']);
    $clash = db_one('SELECT id FROM books WHERE slug = :s AND id <> :id', [':s' => $slug, ':id' => $id]);
    if ($clash) {
        $errors['title'] = 'Đã có cuốn khác dùng đường dẫn này. Đổi tựa sách một chút.';
    }

    if (!$errors) {
        if ($isEdit) {
            db_query(
                'UPDATE books SET slug=:slug, title=:title, author_id=:author_id, category_id=:category_id,
                    publisher=:publisher, published_year=:published_year, pages=:pages, language=:language,
                    isbn=:isbn, cover=:cover, summary=:summary, is_featured=:is_featured
                 WHERE id=:id',
                array_merge($data, [':slug' => $slug, ':id' => $id])
            );
            flash('ok', 'Đã cập nhật “' . $data['title'] . '”.');
        } else {
            db_query(
                'INSERT INTO books (slug, title, author_id, category_id, publisher, published_year,
                    pages, language, isbn, cover, summary, is_featured)
                 VALUES (:slug, :title, :author_id, :category_id, :publisher, :published_year,
                    :pages, :language, :isbn, :cover, :summary, :is_featured)',
                array_merge($data, [':slug' => $slug])
            );
            flash('ok', 'Đã thêm “' . $data['title'] . '” vào kho.');
        }
        header('Location: ' . admin_url('books.php'));
        exit;
    }
}

$adminTitle = $isEdit ? 'Sửa sách' : 'Thêm sách';
require __DIR__ . '/layout.php';
?>

<div class="admin-head">
  <div>
    <h1 style="font-size:var(--fs-2xl);margin:0"><?= $isEdit ? 'Sửa sách' : 'Thêm sách mới' ?></h1>
    <?php if ($isEdit): ?>
      <p style="margin:var(--space-2) 0 0"><?= e($book['title']) ?></p>
    <?php endif; ?>
  </div>
  <a class="btn btn--ghost btn-sm" href="<?= e(admin_url('books.php')) ?>">← Về danh sách</a>
</div>

<?php if ($errors): ?>
  <?php /* Tóm tắt lỗi ở đầu biểu mẫu, kèm liên kết nhảy tới đúng ô sai —
           người dùng bàn phím và trình đọc màn hình cần điều này. */ ?>
  <div class="alert alert--error" role="alert">
    <strong>Còn <?= count($errors) ?> chỗ cần sửa:</strong>
    <ul style="margin:var(--space-2) 0 0;padding-left:var(--space-5)">
      <?php foreach ($errors as $field => $msg): ?>
        <li><a href="#f-<?= e($field) ?>"><?= e($msg) ?></a></li>
      <?php endforeach; ?>
    </ul>
  </div>
<?php endif; ?>

<form method="post" style="max-width:760px">
  <?= csrf_field() ?>

  <div class="field">
    <label for="f-title">Tựa sách</label>
    <input type="text" id="f-title" name="title" required maxlength="220" value="<?= e((string) $val('title')) ?>"
           <?= isset($errors['title']) ? 'aria-invalid="true"' : '' ?>>
    <?php if (isset($errors['title'])): ?><span class="error"><?= e($errors['title']) ?></span><?php endif; ?>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4)">
    <div class="field">
      <label for="f-author_id">Tác giả</label>
      <select id="f-author_id" name="author_id" required>
        <option value="">— Chọn tác giả —</option>
        <?php foreach ($authors as $a): ?>
          <option value="<?= (int) $a['id'] ?>" <?= (int) $val('author_id') === (int) $a['id'] ? 'selected' : '' ?>>
            <?= e($a['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['author_id'])): ?><span class="error"><?= e($errors['author_id']) ?></span><?php endif; ?>
    </div>

    <div class="field">
      <label for="f-category_id">Thể loại</label>
      <select id="f-category_id" name="category_id" required>
        <option value="">— Chọn thể loại —</option>
        <?php foreach ($cats as $c): ?>
          <option value="<?= (int) $c['id'] ?>" <?= (int) $val('category_id') === (int) $c['id'] ? 'selected' : '' ?>>
            <?= e($c['name']) ?>
          </option>
        <?php endforeach; ?>
      </select>
      <?php if (isset($errors['category_id'])): ?><span class="error"><?= e($errors['category_id']) ?></span><?php endif; ?>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:2fr 1fr 1fr;gap:var(--space-4)">
    <div class="field">
      <label for="f-publisher">Nhà xuất bản</label>
      <input type="text" id="f-publisher" name="publisher" maxlength="160" value="<?= e((string) $val('publisher')) ?>">
    </div>
    <div class="field">
      <label for="f-published_year">Năm xuất bản</label>
      <input type="number" id="f-published_year" name="published_year" min="-3000" max="<?= (int) date('Y') + 1 ?>"
             value="<?= e((string) $val('published_year')) ?>">
      <span class="hint">Số âm cho trước Công nguyên.</span>
    </div>
    <div class="field">
      <label for="f-pages">Số trang</label>
      <input type="number" id="f-pages" name="pages" min="1" max="20000" required value="<?= e((string) $val('pages')) ?>"
             <?= isset($errors['pages']) ? 'aria-invalid="true"' : '' ?>>
      <?php if (isset($errors['pages'])): ?><span class="error"><?= e($errors['pages']) ?></span><?php endif; ?>
    </div>
  </div>

  <div style="display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4)">
    <div class="field">
      <label for="f-language">Ngôn ngữ</label>
      <input type="text" id="f-language" name="language" maxlength="40" value="<?= e((string) $val('language', 'Tiếng Việt')) ?>">
    </div>
    <div class="field">
      <label for="f-isbn">ISBN</label>
      <input type="text" id="f-isbn" name="isbn" maxlength="24" value="<?= e((string) $val('isbn')) ?>">
    </div>
    <div class="field">
      <label for="f-cover">Tệp bìa</label>
      <input type="text" id="f-cover" name="cover" maxlength="180" value="<?= e((string) $val('cover')) ?>"
             placeholder="ten-sach.webp">
      <span class="hint">Tên tệp trong <code>assets/img/covers/</code>.</span>
    </div>
  </div>

  <div class="field">
    <label for="f-summary">Tóm tắt nội dung</label>
    <textarea id="f-summary" name="summary" required minlength="20" maxlength="2000"
              <?= isset($errors['summary']) ? 'aria-invalid="true"' : '' ?>><?= e((string) $val('summary')) ?></textarea>
    <?php if (isset($errors['summary'])): ?><span class="error"><?= e($errors['summary']) ?></span><?php endif; ?>
  </div>

  <div class="field">
    <label style="display:flex;align-items:center;gap:var(--space-3);cursor:pointer">
      <input type="checkbox" name="is_featured" value="1" style="width:auto;min-height:auto"
             <?= (int) $val('is_featured') ? 'checked' : '' ?>>
      Đánh dấu nổi bật (hiện ở trang chủ)
    </label>
  </div>

  <button class="btn btn--primary" type="submit"><?= $isEdit ? 'Lưu thay đổi' : 'Thêm vào kho' ?></button>
</form>

<?php require __DIR__ . '/layout_end.php'; ?>
