<?php
/**
 * Liên hệ — biểu mẫu gửi tin nhắn, lưu vào bảng messages cho quản trị đọc.
 */
declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? null)) {
        $errors['form'] = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và gửi lại.';
    } else {
        $name    = trim((string) ($_POST['name'] ?? ''));
        $email   = trim((string) ($_POST['email'] ?? ''));
        $subject = trim((string) ($_POST['subject'] ?? ''));
        $content = trim((string) ($_POST['content'] ?? ''));

        if (mb_strlen($name) < 2)                              $errors['name']    = 'Vui lòng nhập tên từ 2 ký tự trở lên.';
        if (!filter_var($email, FILTER_VALIDATE_EMAIL))        $errors['email']   = 'Địa chỉ email chưa đúng định dạng.';
        if (mb_strlen($content) < 10)                          $errors['content'] = 'Nội dung cần ít nhất 10 ký tự.';
        if (mb_strlen($content) > 2000)                        $errors['content'] = 'Nội dung tối đa 2000 ký tự.';

        if (!$errors) {
            db_query(
                'INSERT INTO messages (name, email, subject, content) VALUES (:n, :e, :s, :c)',
                [':n' => $name, ':e' => $email, ':s' => $subject !== '' ? $subject : null, ':c' => $content]
            );
            flash('ok', 'Đã nhận được tin nhắn của bạn. Cảm ơn bạn đã liên hệ!');
            header('Location: ' . url('contact.php'));
            exit;
        }
    }
}

$pageTitle = 'Liên hệ — ' . SITE_NAME;
$pageDesc  = 'Gửi câu hỏi, góp ý hoặc đề xuất sách mới cho thư viện HieuMini Books.';

require __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div style="display:grid;grid-template-columns:minmax(0,1fr) minmax(0,1.1fr);gap:var(--space-9);align-items:start"
         class="contact-layout">
      <div>
        <span class="eyebrow">Liên hệ</span>
        <h1 style="font-size:var(--fs-3xl)" data-split>Có gì muốn nhắn, cứ gửi</h1>
        <p class="lead">
          Góp ý về giao diện, đề xuất thêm sách vào kho, hay báo một lỗi bạn gặp phải —
          tất cả đều được đọc.
        </p>

        <hr class="rule">

        <p style="display:flex;align-items:center;gap:var(--space-3)">
          <?= icon('mail') ?>
          <a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a>
        </p>
        <p style="display:flex;align-items:center;gap:var(--space-3)">
          <?= icon('user') ?>
          <span><?= e(SITE_AUTHOR) ?></span>
        </p>
      </div>

      <form method="post" style="background:var(--surface);border:1px solid var(--border);
                                 border-radius:var(--r-md);padding:var(--space-7)">
        <?php if (!empty($errors['form'])): ?>
          <div class="alert alert--error" role="alert"><?= e($errors['form']) ?></div>
        <?php endif; ?>

        <?= csrf_field() ?>

        <div class="field">
          <label for="name">Tên của bạn</label>
          <input type="text" id="name" name="name" required maxlength="120"
                 value="<?= e($_POST['name'] ?? '') ?>"
                 <?= isset($errors['name']) ? 'aria-invalid="true" aria-describedby="err-name"' : '' ?>>
          <?php if (isset($errors['name'])): ?><span class="error" id="err-name"><?= e($errors['name']) ?></span><?php endif; ?>
        </div>

        <div class="field">
          <label for="email">Email</label>
          <input type="email" id="email" name="email" required maxlength="160"
                 value="<?= e($_POST['email'] ?? '') ?>"
                 <?= isset($errors['email']) ? 'aria-invalid="true" aria-describedby="err-email"' : '' ?>>
          <span class="hint">Chỉ dùng để trả lời bạn, không chia sẻ cho bên thứ ba.</span>
          <?php if (isset($errors['email'])): ?><span class="error" id="err-email"><?= e($errors['email']) ?></span><?php endif; ?>
        </div>

        <div class="field">
          <label for="subject">Tiêu đề <span style="color:var(--fg-subtle)">(không bắt buộc)</span></label>
          <input type="text" id="subject" name="subject" maxlength="200" value="<?= e($_POST['subject'] ?? '') ?>">
        </div>

        <div class="field">
          <label for="content">Nội dung</label>
          <textarea id="content" name="content" required minlength="10" maxlength="2000"
                    <?= isset($errors['content']) ? 'aria-invalid="true" aria-describedby="err-content"' : '' ?>
          ><?= e($_POST['content'] ?? '') ?></textarea>
          <?php if (isset($errors['content'])): ?><span class="error" id="err-content"><?= e($errors['content']) ?></span><?php endif; ?>
        </div>

        <button class="btn btn--primary" type="submit" style="width:100%"><?= icon('mail') ?> Gửi tin nhắn</button>
      </form>
    </div>
  </div>
</section>

<style>
  @media (max-width: 900px) { .contact-layout { grid-template-columns: 1fr !important; gap: var(--space-7) !important; } }
</style>

<?php require __DIR__ . '/includes/footer.php'; ?>
