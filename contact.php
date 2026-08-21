<?php
/**
 * HieuMini — Liên hệ
 * Biểu mẫu lưu tin nhắn vào bảng messages, có kiểm tra dữ liệu và token CSRF.
 */

declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';

$errors = [];
$old = ['name' => '', 'email' => '', 'subject' => '', 'content' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    foreach ($old as $key => $_) {
        $old[$key] = trim((string) ($_POST[$key] ?? ''));
    }

    if (!csrf_check()) {
        $errors['form'] = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và gửi lại.';
    }
    if (mb_strlen($old['name']) < 2) {
        $errors['name'] = 'Vui lòng nhập họ tên từ 2 ký tự trở lên.';
    }
    if (!filter_var($old['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Địa chỉ email chưa đúng định dạng, ví dụ: ten@example.com';
    }
    if (mb_strlen($old['content']) < 10) {
        $errors['content'] = 'Nội dung cần ít nhất 10 ký tự để chúng tôi hiểu yêu cầu của bạn.';
    }

    if (!$errors) {
        Database::run(
            'INSERT INTO messages (name, email, subject, content, ip) VALUES (?, ?, ?, ?, ?)',
            [
                mb_substr($old['name'], 0, 120),
                mb_substr($old['email'], 0, 180),
                mb_substr($old['subject'] !== '' ? $old['subject'] : 'Liên hệ từ website', 0, 200),
                $old['content'],
                mb_substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            ]
        );
        flash('success', 'Đã gửi tin nhắn thành công. Cảm ơn bạn đã liên hệ!');
        redirect('contact.php');
    }
}

log_visit();

$pageTitle = 'Liên hệ — ' . SITE_NAME;
$pageDesc  = 'Gửi câu hỏi hoặc đề nghị hợp tác tới tác giả bộ sưu tập website HieuMini.';
require __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="detail-layout">
      <div>
        <div class="section-head" data-reveal>
          <span class="eyebrow"><?= icon('mail', 'ico ico-sm') ?> Liên hệ</span>
          <h1 style="font-size:var(--fs-3xl)">Bạn cần trao đổi về dự án nào?</h1>
          <p>Điền biểu mẫu bên dưới, tin nhắn sẽ được lưu trực tiếp vào cơ sở dữ liệu của hệ thống.</p>
        </div>

        <?php if (isset($errors['form'])): ?>
          <div class="alert alert--error" role="alert">
            <?= icon('close', 'ico ico-sm') ?> <?= e($errors['form']) ?>
          </div>
        <?php endif; ?>

        <?php if ($errors && !isset($errors['form'])): ?>
          <div class="alert alert--error" role="alert">
            <?= icon('close', 'ico ico-sm') ?>
            <div>
              <strong>Vui lòng kiểm tra lại <?= count($errors) ?> trường:</strong>
              <ul style="margin:6px 0 0;padding-left:18px">
                <?php foreach ($errors as $field => $msg): ?>
                  <li><a href="#f-<?= e($field) ?>" style="color:inherit"><?= e($msg) ?></a></li>
                <?php endforeach; ?>
              </ul>
            </div>
          </div>
        <?php endif; ?>

        <form method="post" novalidate class="panel" data-reveal>
          <?= csrf_field() ?>

          <div class="field<?= isset($errors['name']) ? ' has-error' : '' ?>">
            <label for="f-name">Họ và tên <span aria-hidden="true" style="color:var(--danger)">*</span></label>
            <input type="text" id="f-name" name="name" required maxlength="120"
                   autocomplete="name" value="<?= e($old['name']) ?>"
                   <?= isset($errors['name']) ? 'aria-describedby="err-name" aria-invalid="true"' : '' ?>>
            <?php if (isset($errors['name'])): ?>
              <span class="error" id="err-name"><?= icon('close', 'ico ico-sm') ?><?= e($errors['name']) ?></span>
            <?php endif; ?>
          </div>

          <div class="field<?= isset($errors['email']) ? ' has-error' : '' ?>">
            <label for="f-email">Email <span aria-hidden="true" style="color:var(--danger)">*</span></label>
            <input type="email" id="f-email" name="email" required maxlength="180"
                   autocomplete="email" value="<?= e($old['email']) ?>"
                   <?= isset($errors['email']) ? 'aria-describedby="err-email" aria-invalid="true"' : '' ?>>
            <p class="hint">Chúng tôi chỉ dùng email này để phản hồi yêu cầu của bạn.</p>
            <?php if (isset($errors['email'])): ?>
              <span class="error" id="err-email"><?= icon('close', 'ico ico-sm') ?><?= e($errors['email']) ?></span>
            <?php endif; ?>
          </div>

          <div class="field">
            <label for="f-subject">Tiêu đề</label>
            <input type="text" id="f-subject" name="subject" maxlength="200"
                   placeholder="Ví dụ: Hỏi về dự án HieuWeb05" value="<?= e($old['subject']) ?>">
          </div>

          <div class="field<?= isset($errors['content']) ? ' has-error' : '' ?>">
            <label for="f-content">Nội dung <span aria-hidden="true" style="color:var(--danger)">*</span></label>
            <textarea id="f-content" name="content" required
                      <?= isset($errors['content']) ? 'aria-describedby="err-content" aria-invalid="true"' : '' ?>><?= e($old['content']) ?></textarea>
            <?php if (isset($errors['content'])): ?>
              <span class="error" id="err-content"><?= icon('close', 'ico ico-sm') ?><?= e($errors['content']) ?></span>
            <?php endif; ?>
          </div>

          <button type="submit" class="btn btn--primary btn--block">
            <?= icon('mail', 'ico ico-sm') ?> Gửi tin nhắn
          </button>
        </form>
      </div>

      <aside>
        <div class="panel" data-reveal>
          <h3><?= icon('mail', 'ico ico-sm') ?> Thông tin trực tiếp</h3>
          <ul class="spec-list">
            <li><span class="k">Tác giả</span><span class="v"><?= e(SITE_AUTHOR) ?></span></li>
            <li><span class="k">Email</span><span class="v"><a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></span></li>
            <li><span class="k">Máy chủ</span><span class="v">XAMPP / Apache</span></li>
          </ul>
        </div>

        <div class="panel" data-reveal>
          <h3><?= icon('shield', 'ico ico-sm') ?> Dữ liệu của bạn</h3>
          <p style="font-size:var(--fs-sm);margin:0">
            Tin nhắn được lưu vào bảng <code>messages</code> trên máy chủ nội bộ và chỉ hiển thị
            trong phân hệ quản trị. Hệ thống không gửi dữ liệu ra bên ngoài.
          </p>
        </div>
      </aside>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
