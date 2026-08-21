<?php
/**
 * HieuMini Admin - Thêm / Sửa bài viết
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$errors = [];

$post = [
    'title' => '', 'slug' => '', 'excerpt' => '', 'content' => '',
    'thumbnail' => 'assets/images/blog/post-1.svg', 'author' => 'HieuMini Team',
    'tags' => '', 'status' => 1, 'meta_title' => '', 'meta_description' => '',
    'published_at' => date('Y-m-d\TH:i'),
];

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        flash('Không tìm thấy bài viết.', 'error');
        redirect('admin/posts.php');
    }
    $post = array_merge($post, $found);
    $post['published_at'] = date('Y-m-d\TH:i', strtotime($found['published_at']));
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();

    foreach (array_keys($post) as $k) {
        if (isset($_POST[$k])) {
            $post[$k] = is_string($_POST[$k]) ? trim($_POST[$k]) : $_POST[$k];
        }
    }
    $post['status'] = isset($_POST['status']) ? 1 : 0;
    $post['slug']   = $post['slug'] !== '' ? slugify((string)$post['slug']) : slugify((string)$post['title']);

    if (mb_strlen((string)$post['title'], 'UTF-8') < 8) {
        $errors['title'] = 'Tiêu đề phải có ít nhất 8 ký tự.';
    }
    $chk = $pdo->prepare('SELECT id FROM posts WHERE slug = ? AND id <> ?');
    $chk->execute([$post['slug'], $id]);
    if ($chk->fetchColumn()) {
        $errors['slug'] = 'Đường dẫn này đã được dùng cho bài viết khác.';
    }

    if (!$errors) {
        $published = date('Y-m-d H:i:s', strtotime((string)$post['published_at']) ?: time());
        if ($isEdit) {
            $pdo->prepare('UPDATE posts SET title=?, slug=?, excerpt=?, content=?, thumbnail=?, author=?, tags=?, status=?, meta_title=?, meta_description=?, published_at=? WHERE id=?')
                ->execute([$post['title'], $post['slug'], $post['excerpt'], $post['content'], $post['thumbnail'],
                           $post['author'], $post['tags'], $post['status'], $post['meta_title'], $post['meta_description'], $published, $id]);
            flash('Đã cập nhật bài viết.');
        } else {
            $pdo->prepare('INSERT INTO posts (title, slug, excerpt, content, thumbnail, author, tags, status, meta_title, meta_description, published_at) VALUES (?,?,?,?,?,?,?,?,?,?,?)')
                ->execute([$post['title'], $post['slug'], $post['excerpt'], $post['content'], $post['thumbnail'],
                           $post['author'], $post['tags'], $post['status'], $post['meta_title'], $post['meta_description'], $published]);
            flash('Đã đăng bài viết mới.');
        }
        redirect('admin/posts.php');
    }
}

$adminTitle = $isEdit ? 'Sửa bài viết #' . $id : 'Viết bài mới';
$adminActions = '<a class="btn btn--ghost" href="' . e(url('admin/posts.php')) . '">← Về danh sách</a>';
require __DIR__ . '/includes/header.php';
?>

<form method="post" class="glass" style="padding:var(--sp-6)" data-validate novalidate>
  <?= csrf_field() ?>

  <div class="grid grid--2">
    <div class="form-grid">
      <div class="field">
        <label for="title">Tiêu đề <span aria-hidden="true">*</span></label>
        <input type="text" id="title" name="title" required value="<?= e((string)$post['title']) ?>"
               aria-invalid="<?= isset($errors['title']) ? 'true' : 'false' ?>">
        <?php if (isset($errors['title'])): ?><span class="error"><?= e($errors['title']) ?></span><?php endif; ?>
      </div>

      <div class="field">
        <label for="slug">Đường dẫn</label>
        <input type="text" id="slug" name="slug" value="<?= e((string)$post['slug']) ?>"
               aria-invalid="<?= isset($errors['slug']) ? 'true' : 'false' ?>">
        <?php if (isset($errors['slug'])): ?><span class="error"><?= e($errors['slug']) ?></span><?php endif; ?>
      </div>

      <div class="field">
        <label for="excerpt">Đoạn mở đầu</label>
        <textarea id="excerpt" name="excerpt" maxlength="400" style="min-height:90px"><?= e((string)$post['excerpt']) ?></textarea>
      </div>

      <div class="field">
        <label for="content">Nội dung bài viết</label>
        <textarea id="content" name="content" style="min-height:340px"><?= e((string)$post['content']) ?></textarea>
        <span class="hint">Mỗi đoạn văn viết trên một dòng riêng.</span>
      </div>
    </div>

    <div class="form-grid">
      <div class="field">
        <label for="thumbnail">Ảnh đại diện</label>
        <input type="text" id="thumbnail" name="thumbnail" value="<?= e((string)$post['thumbnail']) ?>">
      </div>
      <div class="field">
        <label for="author">Tác giả</label>
        <input type="text" id="author" name="author" value="<?= e((string)$post['author']) ?>">
      </div>
      <div class="field">
        <label for="tags">Thẻ</label>
        <input type="text" id="tags" name="tags" value="<?= e((string)$post['tags']) ?>" placeholder="seo, php, hướng dẫn">
      </div>
      <div class="field">
        <label for="published_at">Thời điểm xuất bản</label>
        <input type="datetime-local" id="published_at" name="published_at" value="<?= e((string)$post['published_at']) ?>">
      </div>
      <label class="checkbox"><input type="checkbox" name="status" value="1" <?= $post['status'] ? 'checked' : '' ?>><span>Xuất bản công khai</span></label>

      <hr>
      <div class="field">
        <label for="meta_title">Thẻ tiêu đề SEO</label>
        <input type="text" id="meta_title" name="meta_title" maxlength="180" value="<?= e((string)$post['meta_title']) ?>">
      </div>
      <div class="field">
        <label for="meta_description">Thẻ mô tả SEO</label>
        <textarea id="meta_description" name="meta_description" maxlength="300" style="min-height:90px"><?= e((string)$post['meta_description']) ?></textarea>
      </div>
    </div>
  </div>

  <div style="display:flex;gap:10px;margin-top:var(--sp-6)">
    <button class="btn btn--primary btn--lg" type="submit"><?= $isEdit ? 'Lưu thay đổi' : 'Đăng bài' ?></button>
    <a class="btn btn--ghost btn--lg" href="<?= e(url('admin/posts.php')) ?>">Huỷ</a>
  </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
