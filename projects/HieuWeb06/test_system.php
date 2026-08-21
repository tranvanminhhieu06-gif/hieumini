<?php
/**
 * HieuMini - Trang tự kiểm tra hệ thống
 * Mở http://localhost/DoAnWebsite/projects/HieuMini/test_system.php sau khi cài đặt.
 * XOÁ tệp này trước khi đưa website lên máy chủ thật.
 */
require_once __DIR__ . '/includes/config.php';

$checks = [];

/** Ghi nhận một mục kiểm tra. */
function check(string $name, bool $ok, string $detail = ''): array
{
    return ['name' => $name, 'ok' => $ok, 'detail' => $detail];
}

// 1. Môi trường
$checks[] = check('Phiên bản PHP ≥ 8.0', PHP_VERSION_ID >= 80000, 'Hiện tại: ' . PHP_VERSION);
$checks[] = check('Phần mở rộng PDO MySQL', extension_loaded('pdo_mysql'));
$checks[] = check('Phần mở rộng mbstring (tiếng Việt)', extension_loaded('mbstring'));
$checks[] = check('Phần mở rộng json', extension_loaded('json'));
$checks[] = check('Hàm password_hash khả dụng', function_exists('password_hash'));

// 2. Cơ sở dữ liệu
try {
    $pdo->query('SELECT 1');
    $checks[] = check('Kết nối cơ sở dữ liệu', true, DB_NAME . '@' . DB_HOST);
} catch (Throwable $e) {
    $checks[] = check('Kết nối cơ sở dữ liệu', false, $e->getMessage());
}

$tables = ['users', 'categories', 'projects', 'project_images', 'coupons', 'orders',
           'order_items', 'reviews', 'wishlists', 'posts', 'contacts', 'settings'];
foreach ($tables as $t) {
    try {
        $n = (int)$pdo->query("SELECT COUNT(*) FROM `$t`")->fetchColumn();
        $checks[] = check("Bảng `$t`", true, $n . ' bản ghi');
    } catch (Throwable $e) {
        $checks[] = check("Bảng `$t`", false, 'Không tồn tại - hãy nhập lại database.sql');
    }
}

// 3. Tệp và thư mục
$paths = [
    'includes/config.php', 'includes/functions.php', 'includes/header.php', 'includes/footer.php',
    'assets/css/style.css', 'assets/js/main.js', 'assets/images/logo.svg', 'assets/images/favicon.svg',
    'index.php', 'projects.php', 'project-detail.php', 'cart.php', 'checkout.php',
    'admin/index.php', 'admin/login.php', 'sitemap.php', 'robots.txt',
];
foreach ($paths as $path) {
    $checks[] = check("Tệp $path", is_file(ROOT_DIR . '/' . $path));
}

// 4. Ảnh đại diện dự án
try {
    $missing = [];
    foreach ($pdo->query('SELECT title, thumbnail FROM projects')->fetchAll() as $row) {
        if ($row['thumbnail'] && !is_file(ROOT_DIR . '/' . $row['thumbnail'])) {
            $missing[] = $row['title'];
        }
    }
    $checks[] = check('Ảnh đại diện của mọi dự án tồn tại', $missing === [],
        $missing ? 'Thiếu: ' . implode(', ', array_slice($missing, 0, 3)) : 'Đầy đủ');
} catch (Throwable) {
    $checks[] = check('Ảnh đại diện của mọi dự án tồn tại', false);
}

// 5. Bảo mật cơ bản
$checks[] = check('Token CSRF hoạt động', csrf_verify(csrf_token()));
$checks[] = check('Mật khẩu quản trị đã băm BCRYPT', (function (PDO $pdo): bool {
    try {
        $h = (string)$pdo->query('SELECT password FROM users WHERE role = "admin" LIMIT 1')->fetchColumn();
        return str_starts_with($h, '$2y$');
    } catch (Throwable) {
        return false;
    }
})($pdo));
$checks[] = check('Chế độ DEBUG đang bật (nên tắt khi chạy thật)', DEBUG_MODE, DEBUG_MODE ? 'DEBUG_MODE = true' : 'DEBUG_MODE = false');

$passed = count(array_filter($checks, static fn($c) => $c['ok']));
$total  = count($checks);
$rate   = (int)round($passed / max(1, $total) * 100);

seo(['title' => 'Kiểm tra hệ thống | ' . SITE_NAME, 'robots' => 'noindex, nofollow']);
require __DIR__ . '/includes/header.php';
?>

<div class="container section">
  <div class="glass" style="padding:var(--sp-6);max-width:900px;margin-inline:auto">
    <h1>Kiểm tra hệ thống HieuMini</h1>
    <p style="font-size:var(--fs-lg)">
      Đạt <strong class="text-grad"><?= $passed ?>/<?= $total ?></strong> mục kiểm tra (<?= $rate ?>%).
      <?= $rate === 100 ? 'Hệ thống sẵn sàng hoạt động.' : 'Hãy xử lý các mục còn thiếu bên dưới.' ?>
    </p>

    <div class="table-wrap">
      <table class="data">
        <caption class="sr-only">Kết quả kiểm tra hệ thống</caption>
        <thead><tr><th scope="col">Mục kiểm tra</th><th scope="col">Chi tiết</th><th scope="col">Kết quả</th></tr></thead>
        <tbody>
          <?php foreach ($checks as $c): ?>
            <tr>
              <td><?= e($c['name']) ?></td>
              <td style="color:var(--fg-muted);font-size:var(--fs-xs)"><?= e($c['detail']) ?></td>
              <td><span class="badge <?= $c['ok'] ? 'badge--ok' : 'badge--off' ?>"><?= $c['ok'] ? 'Đạt' : 'Chưa đạt' ?></span></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>

    <div class="demo-hint" style="margin-top:var(--sp-5)">
      Sau khi kiểm tra xong, hãy <strong>xoá tệp test_system.php</strong> và đặt
      <code>DEBUG_MODE = false</code> trong <code>includes/config.php</code> trước khi đưa website lên máy chủ thật.
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
