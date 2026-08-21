<?php
/**
 * HieuMini Admin - Cấu hình chung & SEO
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

$fields = [
    'general' => [
        'site_name'     => ['Tên website', 'text'],
        'site_tagline'  => ['Khẩu hiệu', 'text'],
        'site_url'      => ['Địa chỉ website', 'text'],
    ],
    'seo' => [
        'site_description' => ['Mô tả mặc định (meta description)', 'textarea'],
        'site_keywords'    => ['Từ khoá mặc định', 'text'],
        'og_image'         => ['Ảnh chia sẻ mạng xã hội', 'text'],
        'ga_id'            => ['Mã Google Analytics 4 (G-XXXXXXX)', 'text'],
    ],
    'contact' => [
        'contact_email'   => ['Email liên hệ', 'text'],
        'contact_phone'   => ['Số điện thoại', 'text'],
        'contact_address' => ['Địa chỉ', 'text'],
        'bank_info'       => ['Thông tin chuyển khoản', 'text'],
    ],
    'social' => [
        'facebook' => ['Facebook', 'text'],
        'youtube'  => ['YouTube', 'text'],
        'github'   => ['GitHub', 'text'],
    ],
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $stmt = $pdo->prepare('INSERT INTO settings (setting_key, setting_value, group_name) VALUES (?,?,?)
                           ON DUPLICATE KEY UPDATE setting_value = VALUES(setting_value)');
    foreach ($fields as $group => $items) {
        foreach ($items as $key => $meta) {
            $stmt->execute([$key, trim((string)($_POST[$key] ?? '')), $group]);
        }
    }
    flash('Đã lưu cấu hình hệ thống.');
    redirect('admin/settings.php');
}

$SETTINGS = load_settings($pdo);

$groupTitle = [
    'general' => 'Thông tin chung',
    'seo'     => 'Tối ưu công cụ tìm kiếm',
    'contact' => 'Thông tin liên hệ',
    'social'  => 'Mạng xã hội',
];

$adminTitle = 'Cấu hình hệ thống';
require __DIR__ . '/includes/header.php';
?>

<form method="post">
  <?= csrf_field() ?>
  <div class="grid grid--2" style="align-items:start">
    <?php foreach ($fields as $group => $items): ?>
      <section class="glass" style="padding:var(--sp-5)">
        <h2 style="font-size:var(--fs-lg)"><?= e($groupTitle[$group]) ?></h2>
        <div class="form-grid">
          <?php foreach ($items as $key => $meta): ?>
            <div class="field">
              <label for="<?= e($key) ?>"><?= e($meta[0]) ?></label>
              <?php if ($meta[1] === 'textarea'): ?>
                <textarea id="<?= e($key) ?>" name="<?= e($key) ?>" style="min-height:110px"><?= e(setting($key)) ?></textarea>
              <?php else: ?>
                <input type="text" id="<?= e($key) ?>" name="<?= e($key) ?>" value="<?= e(setting($key)) ?>">
              <?php endif; ?>
              <?php if ($key === 'site_description'): ?>
                <span class="hint">Nên dài 150-160 ký tự. Hiện tại: <?= mb_strlen(setting($key), 'UTF-8') ?> ký tự.</span>
              <?php elseif ($key === 'ga_id'): ?>
                <span class="hint">Để trống nếu chưa dùng Google Analytics.</span>
              <?php endif; ?>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
    <?php endforeach; ?>
  </div>

  <div style="display:flex;gap:10px;margin-top:var(--sp-5)">
    <button class="btn btn--primary btn--lg" type="submit">Lưu toàn bộ cấu hình</button>
    <a class="btn btn--ghost btn--lg" href="<?= e(url('sitemap.php')) ?>" target="_blank" rel="noopener">Xem sitemap.xml</a>
    <a class="btn btn--ghost btn--lg" href="<?= e(url('robots.txt')) ?>" target="_blank" rel="noopener">Xem robots.txt</a>
  </div>
</form>

<?php require __DIR__ . '/includes/footer.php'; ?>
