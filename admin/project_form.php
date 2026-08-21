<?php
/**
 * HieuMini Admin — Thêm / sửa dự án
 */

declare(strict_types=1);
require __DIR__ . '/guard.php';
require __DIR__ . '/layout.php';

$id = (int) ($_GET['id'] ?? 0);
$isEdit = $id > 0;

$blank = [
    'code' => '', 'slug' => '', 'name' => '', 'tagline' => '', 'summary' => '', 'description' => '',
    'category' => '', 'tech_stack' => '', 'folder' => '', 'entry_file' => 'index.php',
    'admin_path' => 'admin/', 'db_name' => '', 'accent_from' => '#4F46E5', 'accent_to' => '#7C3AED',
    'year' => (int) date('Y'), 'table_count' => 0, 'page_count' => 0,
    'status' => 'published', 'sort_order' => 0, 'sold' => 0,
];

$data = $blank;

if ($isEdit) {
    $found = Database::one('SELECT * FROM projects WHERE id = ?', [$id]);
    if (!$found) {
        flash('error', 'Không tìm thấy dự án cần sửa.');
        redirect('admin/projects.php');
    }
    $data = array_merge($blank, array_intersect_key($found, $blank));
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($blank as $key => $default) {
        $data[$key] = is_int($default)
            ? (int) ($_POST[$key] ?? $default)
            : trim((string) ($_POST[$key] ?? ''));
    }

    if (!csrf_check()) {
        $errors['form'] = 'Phiên làm việc đã hết hạn. Vui lòng gửi lại biểu mẫu.';
    }
    if ($data['name'] === '') {
        $errors['name'] = 'Tên dự án không được để trống.';
    }
    if ($data['code'] === '') {
        $errors['code'] = 'Mã dự án không được để trống.';
    }
    if ($data['folder'] === '') {
        $errors['folder'] = 'Cần chỉ định thư mục con trong projects/ để nhúng bản chạy trực tiếp.';
    }
    if ($data['slug'] === '') {
        $data['slug'] = slugify($data['name']);
    }
    if (!in_array($data['status'], ['draft', 'published', 'archived'], true)) {
        $data['status'] = 'published';
    }

    /* Mã và slug phải là duy nhất */
    $dupe = Database::one(
        'SELECT id FROM projects WHERE (code = ? OR slug = ?) AND id <> ? LIMIT 1',
        [$data['code'], $data['slug'], $id]
    );
    if ($dupe) {
        $errors['code'] = 'Đã tồn tại dự án khác dùng chung mã hoặc slug này.';
    }

    if (!$errors) {
        /* Chỉ ghi những cột thực sự tồn tại trong bảng — an toàn nếu chưa chạy
           migration thêm cột "sold" (đã bán). */
        $tableCols = array_column(Database::all('SHOW COLUMNS FROM projects'), 'Field');
        $columns = array_values(array_intersect(array_keys($blank), $tableCols));

        if ($isEdit) {
            $set = implode(', ', array_map(static fn ($c) => "$c = ?", $columns));
            $params = array_map(static fn ($c) => $data[$c], $columns);
            $params[] = $id;
            Database::run("UPDATE projects SET $set WHERE id = ?", $params);
            flash('success', 'Đã cập nhật dự án “' . $data['name'] . '”.');
        } else {
            $place = implode(', ', array_fill(0, count($columns), '?'));
            Database::run(
                'INSERT INTO projects (' . implode(', ', $columns) . ") VALUES ($place)",
                array_map(static fn ($c) => $data[$c], $columns)
            );
            flash('success', 'Đã thêm dự án “' . $data['name'] . '” vào bộ sưu tập.');
        }
        redirect('admin/projects.php');
    }
}

admin_head($isEdit ? 'Sửa dự án' : 'Thêm dự án');

/** Sinh nhanh một trường nhập liệu. */
function field(string $name, string $label, array $data, array $errors, array $opt = []): void
{
    $type = $opt['type'] ?? 'text';
    $hint = $opt['hint'] ?? '';
    $has = isset($errors[$name]);
    $id = 'f-' . $name;

    echo '<div class="field' . ($has ? ' has-error' : '') . '">';
    echo '<label for="' . e($id) . '">' . e($label) . '</label>';

    $attrs = 'id="' . e($id) . '" name="' . e($name) . '"'
           . ($has ? ' aria-invalid="true" aria-describedby="err-' . e($name) . '"' : '');

    if ($type === 'textarea') {
        echo '<textarea ' . $attrs . ' rows="' . (int) ($opt['rows'] ?? 4) . '">' . e((string) $data[$name]) . '</textarea>';
    } elseif ($type === 'select') {
        echo '<select ' . $attrs . '>';
        foreach ($opt['options'] as $value => $text) {
            $sel = (string) $data[$name] === (string) $value ? ' selected' : '';
            echo '<option value="' . e((string) $value) . '"' . $sel . '>' . e($text) . '</option>';
        }
        echo '</select>';
    } else {
        echo '<input type="' . e($type) . '" ' . $attrs . ' value="' . e((string) $data[$name]) . '">';
    }

    if ($hint !== '') {
        echo '<p class="hint">' . e($hint) . '</p>';
    }
    if ($has) {
        echo '<span class="error" id="err-' . e($name) . '">' . e($errors[$name]) . '</span>';
    }
    echo '</div>';
}
?>

<?php if (isset($errors['form'])): ?>
  <div class="alert alert--error" role="alert"><?= icon('close', 'ico ico-sm') ?> <?= e($errors['form']) ?></div>
<?php endif; ?>

<form method="post" novalidate>
  <?= csrf_field() ?>

  <div class="admin-grid">
    <section class="panel">
      <h3><?= icon('list', 'ico ico-sm') ?> Thông tin cơ bản</h3>
      <?php
      field('name',     'Tên dự án *', $data, $errors);
      field('code',     'Mã dự án *',  $data, $errors, ['hint' => 'Ví dụ: HieuWeb07']);
      field('slug',     'Slug',        $data, $errors, ['hint' => 'Bỏ trống để hệ thống tự sinh từ tên dự án.']);
      field('tagline',  'Mô tả ngắn',  $data, $errors, ['hint' => 'Một câu tóm tắt hiển thị trên thẻ dự án.']);
      field('category', 'Lĩnh vực',    $data, $errors);
      field('tech_stack', 'Công nghệ', $data, $errors, ['hint' => 'Phân tách bằng dấu phẩy: PHP 8, MySQL, AJAX']);
      ?>
    </section>

    <section class="panel">
      <h3><?= icon('code', 'ico ico-sm') ?> Nhúng bản chạy trực tiếp</h3>
      <?php
      field('folder',     'Thư mục trong projects/ *', $data, $errors, ['hint' => 'Ví dụ: HieuWeb07']);
      field('entry_file', 'Tệp khởi đầu',              $data, $errors, ['hint' => 'Mặc định index.php']);
      field('admin_path', 'Đường dẫn trang quản trị',  $data, $errors, ['hint' => 'Ví dụ: admin/ hoặc admin/login.php']);
      field('db_name',    'Tên cơ sở dữ liệu',         $data, $errors);
      field('status',     'Trạng thái',                $data, $errors, [
          'type' => 'select',
          'options' => ['published' => 'Hiển thị công khai', 'draft' => 'Nháp', 'archived' => 'Lưu trữ'],
      ]);
      ?>
      <div class="grid-2">
        <?php
        field('accent_from', 'Màu gradient đầu', $data, $errors, ['type' => 'color']);
        field('accent_to',   'Màu gradient cuối', $data, $errors, ['type' => 'color']);
        ?>
      </div>
      <div class="grid-3">
        <?php
        field('year',        'Năm',        $data, $errors, ['type' => 'number']);
        field('table_count', 'Số bảng',    $data, $errors, ['type' => 'number']);
        field('page_count',  'Số trang',   $data, $errors, ['type' => 'number']);
        ?>
      </div>
      <div class="grid-2">
        <?php
        field('sort_order', 'Thứ tự hiển thị', $data, $errors, ['type' => 'number', 'hint' => 'Số nhỏ hiển thị trước.']);
        field('sold', 'Đã bán', $data, $errors, ['type' => 'number', 'hint' => 'Số lượt bán/đăng ký hiển thị trên thẻ dự án.']);
        ?>
      </div>
    </section>
  </div>

  <section class="panel">
    <h3><?= icon('edit', 'ico ico-sm') ?> Nội dung chi tiết</h3>
    <?php
    field('summary',     'Tóm tắt',   $data, $errors, ['type' => 'textarea', 'rows' => 3]);
    field('description', 'Mô tả đầy đủ', $data, $errors, [
        'type' => 'textarea', 'rows' => 10,
        'hint' => 'Cách nhau một dòng trống để tách đoạn văn.',
    ]);
    ?>
  </section>

  <div class="toolbar" style="justify-content:flex-end">
    <a class="btn btn--ghost" href="<?= e(url('admin/projects.php')) ?>">Hủy bỏ</a>
    <button type="submit" class="btn btn--primary">
      <?= icon('check', 'ico ico-sm') ?> <?= $isEdit ? 'Lưu thay đổi' : 'Thêm dự án' ?>
    </button>
  </div>
</form>

<?php admin_foot(); ?>
