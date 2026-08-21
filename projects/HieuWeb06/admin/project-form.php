<?php
/**
 * HieuMini Admin - Thêm / Sửa dự án
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$isEdit = $id > 0;
$categories = get_categories($pdo);
$errors = [];

$p = [
    'category_id' => $categories[0]['id'] ?? 1,
    'title' => '', 'slug' => '', 'short_desc' => '', 'description' => '', 'features' => '',
    'tech_stack' => 'PHP 8.2, MySQL 8, JavaScript ES6', 'price' => '', 'sale_price' => '',
    'thumbnail' => 'assets/images/og-cover.svg', 'demo_url' => '', 'badge' => '',
    'is_featured' => 0, 'status' => 1,
    'meta_title' => '', 'meta_description' => '', 'meta_keywords' => '',
];

if ($isEdit) {
    $stmt = $pdo->prepare('SELECT * FROM projects WHERE id = ?');
    $stmt->execute([$id]);
    $found = $stmt->fetch();
    if (!$found) {
        flash('Không tìm thấy dự án.', 'error');
        redirect('admin/projects.php');
    }
    $p = array_merge($p, $found);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();

    foreach (array_keys($p) as $k) {
        if (isset($_POST[$k])) {
            $p[$k] = is_string($_POST[$k]) ? trim($_POST[$k]) : $_POST[$k];
        }
    }
    $p['is_featured'] = isset($_POST['is_featured']) ? 1 : 0;
    $p['status']      = isset($_POST['status']) ? 1 : 0;
    $p['slug']        = $p['slug'] !== '' ? slugify((string)$p['slug']) : slugify((string)$p['title']);
    $p['sale_price']  = $p['sale_price'] === '' ? null : (int)$p['sale_price'];

    if (mb_strlen((string)$p['title'], 'UTF-8') < 5) {
        $errors['title'] = 'Tiêu đề phải có ít nhất 5 ký tự.';
    }
    if ((float)$p['price'] <= 0) {
        $errors['price'] = 'Giá bán phải lớn hơn 0.';
    }
    $chk = $pdo->prepare('SELECT id FROM projects WHERE slug = ? AND id <> ?');
    $chk->execute([$p['slug'], $id]);
    if ($chk->fetchColumn()) {
        $errors['slug'] = 'Đường dẫn này đã được dùng cho dự án khác.';
    }

    if (!$errors) {
        $fields = ['category_id','title','slug','short_desc','description','features','tech_stack',
                   'price','sale_price','thumbnail','demo_url','badge','is_featured','status',
                   'meta_title','meta_description','meta_keywords'];
        $values = array_map(static fn($f) => $p[$f], $fields);

        if ($isEdit) {
            $set = implode(', ', array_map(static fn($f) => "`$f` = ?", $fields));
            $values[] = $id;
            $pdo->prepare("UPDATE projects SET $set WHERE id = ?")->execute($values);
            flash('Đã cập nhật dự án "' . $p['title'] . '".');
        } else {
            $cols = implode(', ', array_map(static fn($f) => "`$f`", $fields));
            $ph   = implode(', ', array_fill(0, count($fields), '?'));
            $pdo->prepare("INSERT INTO projects ($cols) VALUES ($ph)")->execute($values);
            flash('Đã thêm dự án mới "' . $p['title'] . '".');
        }
        redirect('admin/projects.php');
    }
}

$adminTitle = $isEdit ? 'Sửa dự án #' . $id : 'Thêm dự án mới';
$adminActions = '<a class="btn btn--ghost" href="' . e(url('admin/projects.php')) . '">← Về danh sách</a>';
require __DIR__ . '/includes/header.php';
?>

<form method="post" class="glass" style="padding:var(--sp-6)" data-validate novalidate>
  <?= csrf_field() ?>

  <div class="grid grid--2">
    <div>
      <h2 style="font-size:var(--fs-lg)">Thông tin cơ bản</h2>
      <div class="form-grid">
        <div class="field">
          <label for="title">Tiêu đề dự án <span aria-hidden="true">*</span></label>
          <input type="text" id="title" name="title" required value="<?= e((string)$p['title']) ?>"
                 aria-invalid="<?= isset($errors['title']) ? 'true' : 'false' ?>">
          <?php if (isset($errors['title'])): ?><span class="error"><?= e($errors['title']) ?></span><?php endif; ?>
        </div>

        <div class="field">
          <label for="slug">Đường dẫn thân thiện (slug)</label>
          <input type="text" id="slug" name="slug" value="<?= e((string)$p['slug']) ?>"
                 aria-invalid="<?= isset($errors['slug']) ? 'true' : 'false' ?>">
          <?php if (isset($errors['slug'])): ?><span class="error"><?= e($errors['slug']) ?></span>
          <?php else: ?><span class="hint">Để trống sẽ tự sinh từ tiêu đề, ví dụ: hieushop-pro-website-ban-hang.</span><?php endif; ?>
        </div>

        <div class="field">
          <label for="category_id">Danh mục</label>
          <select id="category_id" name="category_id">
            <?php foreach ($categories as $c): ?>
              <option value="<?= (int)$c['id'] ?>" <?= (int)$p['category_id'] === (int)$c['id'] ? 'selected' : '' ?>><?= e($c['name']) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="field">
          <label for="short_desc">Mô tả ngắn</label>
          <textarea id="short_desc" name="short_desc" maxlength="300" style="min-height:80px"><?= e((string)$p['short_desc']) ?></textarea>
          <span class="hint">Hiển thị trên thẻ dự án và dùng làm mô tả dự phòng cho SEO. Tối đa 300 ký tự.</span>
        </div>

        <div class="field">
          <label for="description">Mô tả chi tiết</label>
          <textarea id="description" name="description" style="min-height:200px"><?= e((string)$p['description']) ?></textarea>
          <span class="hint">Mỗi đoạn văn viết trên một dòng riêng.</span>
        </div>

        <div class="field">
          <label for="features">Danh sách tính năng</label>
          <textarea id="features" name="features" style="min-height:150px"><?= e((string)$p['features']) ?></textarea>
          <span class="hint">Mỗi tính năng một dòng.</span>
        </div>
      </div>
    </div>

    <div>
      <h2 style="font-size:var(--fs-lg)">Giá &amp; hiển thị</h2>
      <div class="form-grid">
        <div class="grid grid--2">
          <div class="field">
            <label for="price">Giá gốc (VNĐ) <span aria-hidden="true">*</span></label>
            <input type="number" id="price" name="price" required min="0" step="1000" value="<?= e((string)$p['price']) ?>"
                   aria-invalid="<?= isset($errors['price']) ? 'true' : 'false' ?>">
            <?php if (isset($errors['price'])): ?><span class="error"><?= e($errors['price']) ?></span><?php endif; ?>
          </div>
          <div class="field">
            <label for="sale_price">Giá khuyến mãi</label>
            <input type="number" id="sale_price" name="sale_price" min="0" step="1000" value="<?= e((string)$p['sale_price']) ?>">
            <span class="hint">Để trống nếu không giảm giá.</span>
          </div>
        </div>

        <div class="field">
          <label for="tech_stack">Công nghệ sử dụng</label>
          <input type="text" id="tech_stack" name="tech_stack" value="<?= e((string)$p['tech_stack']) ?>">
          <span class="hint">Ngăn cách bằng dấu phẩy.</span>
        </div>

        <div class="field">
          <label for="thumbnail">Đường dẫn ảnh đại diện</label>
          <input type="text" id="thumbnail" name="thumbnail" value="<?= e((string)$p['thumbnail']) ?>">
          <span class="hint">Ví dụ: assets/images/projects/ten-du-an.svg</span>
        </div>

        <div class="field">
          <label for="demo_url">Liên kết demo</label>
          <input type="text" id="demo_url" name="demo_url" value="<?= e((string)$p['demo_url']) ?>" placeholder="https://…">
        </div>

        <div class="field">
          <label for="badge">Nhãn nổi bật</label>
          <select id="badge" name="badge">
            <?php foreach (['' => 'Không có', 'HOT' => 'HOT', 'NEW' => 'NEW', 'BEST SELLER' => 'BEST SELLER'] as $val => $label): ?>
              <option value="<?= e($val) ?>" <?= (string)$p['badge'] === $val ? 'selected' : '' ?>><?= e($label) ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <label class="checkbox"><input type="checkbox" name="is_featured" value="1" <?= $p['is_featured'] ? 'checked' : '' ?>><span>Hiển thị ở khu vực nổi bật trang chủ</span></label>
        <label class="checkbox"><input type="checkbox" name="status" value="1" <?= $p['status'] ? 'checked' : '' ?>><span>Đang mở bán (bỏ chọn để ẩn khỏi website)</span></label>

        <hr>
        <h2 style="font-size:var(--fs-lg)">Tối ưu công cụ tìm kiếm</h2>
        <div class="field">
          <label for="meta_title">Thẻ tiêu đề (title)</label>
          <input type="text" id="meta_title" name="meta_title" maxlength="180" value="<?= e((string)$p['meta_title']) ?>">
          <span class="hint">Nên dài 50-60 ký tự, chứa từ khoá chính ở đầu.</span>
        </div>
        <div class="field">
          <label for="meta_description">Thẻ mô tả (description)</label>
          <textarea id="meta_description" name="meta_description" maxlength="300" style="min-height:90px"><?= e((string)$p['meta_description']) ?></textarea>
          <span class="hint">Nên dài 150-160 ký tự, viết như một lời mời nhấp chuột.</span>
        </div>
        <div class="field">
          <label for="meta_keywords">Từ khoá</label>
          <input type="text" id="meta_keywords" name="meta_keywords" value="<?= e((string)$p['meta_keywords']) ?>">
        </div>
      </div>
    </div>
  </div>

  <div style="display:flex;gap:10px;margin-top:var(--sp-6);flex-wrap:wrap">
    <button class="btn btn--primary btn--lg" type="submit"><?= $isEdit ? 'Lưu thay đổi' : 'Thêm dự án' ?></button>
    <a class="btn btn--ghost btn--lg" href="<?= e(url('admin/projects.php')) ?>">Huỷ</a>
  </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
