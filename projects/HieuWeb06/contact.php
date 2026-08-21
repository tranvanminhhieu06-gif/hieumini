<?php
/**
 * HieuMini - Liên hệ & yêu cầu báo giá
 */
require_once __DIR__ . '/includes/config.php';

$errors = [];
$form = [
    'name'    => is_logged_in() ? current_user_name() : '',
    'email'   => (string)($_SESSION['user_email'] ?? ''),
    'phone'   => '',
    'subject' => (string)($_GET['subject'] ?? ''),
    'message' => '',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();

    foreach (['name', 'email', 'phone', 'subject', 'message'] as $k) {
        $form[$k] = input($k);
    }

    if (mb_strlen($form['name'], 'UTF-8') < 2) {
        $errors['name'] = 'Vui lòng nhập họ tên.';
    }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Địa chỉ email chưa đúng định dạng.';
    }
    if ($form['phone'] !== '' && !preg_match('/^0\d{9}$/', preg_replace('/\s+/', '', $form['phone']) ?? '')) {
        $errors['phone'] = 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.';
    }
    if (mb_strlen($form['message'], 'UTF-8') < 10) {
        $errors['message'] = 'Nội dung cần ít nhất 10 ký tự để HieuMini hiểu rõ yêu cầu.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO contacts (name, email, phone, subject, message) VALUES (?,?,?,?,?)');
        $stmt->execute([
            $form['name'], $form['email'], $form['phone'] ?: null,
            $form['subject'] ?: 'Liên hệ từ website', $form['message'],
        ]);
        flash('Đã gửi liên hệ. HieuMini sẽ phản hồi trong vòng 24 giờ làm việc.');
        redirect('contact.php');
    }
}

seo([
    'title'       => 'Liên hệ & yêu cầu báo giá | ' . SITE_NAME,
    'description' => 'Liên hệ HieuMini để được tư vấn chọn mã nguồn phù hợp, yêu cầu tuỳ chỉnh tính năng hoặc nhận báo giá website riêng trong 24 giờ.',
    'keywords'    => 'liên hệ hieumini, báo giá website, tư vấn mã nguồn',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container page-head">
  <?= breadcrumb(['Trang chủ' => 'index.php', 'Liên hệ' => null]) ?>
  <h1 class="reveal" style="margin-top:var(--sp-4)">Liên hệ &amp; báo giá</h1>
  <p class="reveal" style="max-width:70ch">Mô tả càng chi tiết, báo giá càng chính xác. HieuMini phản hồi mọi yêu cầu trong vòng 24 giờ làm việc.</p>
</div>

<div class="container section--tight">
  <div class="detail-grid">

    <form class="glass reveal" method="post" style="padding:var(--sp-6)" data-validate novalidate>
      <?= csrf_field() ?>
      <h2 style="font-size:var(--fs-xl)">Gửi yêu cầu</h2>

      <div class="form-grid">
        <div class="grid grid--2">
          <div class="field">
            <label for="name">Họ và tên <span aria-hidden="true">*</span></label>
            <input type="text" id="name" name="name" required autocomplete="name" value="<?= e($form['name']) ?>"
                   aria-invalid="<?= isset($errors['name']) ? 'true' : 'false' ?>">
            <?php if (isset($errors['name'])): ?><span class="error"><?= e($errors['name']) ?></span><?php endif; ?>
          </div>
          <div class="field">
            <label for="phone">Số điện thoại</label>
            <input type="tel" id="phone" name="phone" autocomplete="tel" inputmode="numeric" value="<?= e($form['phone']) ?>"
                   aria-invalid="<?= isset($errors['phone']) ? 'true' : 'false' ?>">
            <?php if (isset($errors['phone'])): ?><span class="error"><?= e($errors['phone']) ?></span><?php endif; ?>
          </div>
        </div>

        <div class="field">
          <label for="email">Email <span aria-hidden="true">*</span></label>
          <input type="email" id="email" name="email" required autocomplete="email" value="<?= e($form['email']) ?>"
                 aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>">
          <?php if (isset($errors['email'])): ?><span class="error"><?= e($errors['email']) ?></span><?php endif; ?>
        </div>

        <div class="field">
          <label for="subject">Chủ đề</label>
          <input type="text" id="subject" name="subject" value="<?= e($form['subject']) ?>"
                 placeholder="VD: Báo giá website bán mỹ phẩm">
        </div>

        <div class="field">
          <label for="message">Nội dung chi tiết <span aria-hidden="true">*</span></label>
          <textarea id="message" name="message" required minlength="10"
                    placeholder="Bạn cần loại website nào? Có tính năng đặc biệt nào cần thêm? Thời gian mong muốn bàn giao?"
                    aria-invalid="<?= isset($errors['message']) ? 'true' : 'false' ?>"><?= e($form['message']) ?></textarea>
          <?php if (isset($errors['message'])): ?><span class="error"><?= e($errors['message']) ?></span>
          <?php else: ?><span class="hint">Càng nhiều thông tin, báo giá càng sát thực tế.</span><?php endif; ?>
        </div>

        <button class="btn btn--primary btn--lg" type="submit">Gửi yêu cầu</button>
      </div>
    </form>

    <aside>
      <div class="glass reveal reveal--right" style="padding:var(--sp-5)">
        <h2 style="font-size:var(--fs-xl)">Thông tin liên hệ</h2>
        <ul class="contact-list">
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7-5.6-7-10a7 7 0 1 1 14 0c0 4.4-7 10-7 10z"/><circle cx="12" cy="11" r="2.5"/></svg><span><?= e(setting('contact_address')) ?></span></li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a1 1 0 0 1-1 1A16 16 0 0 1 4 5a1 1 0 0 1 1-1z"/></svg><a href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a></li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M4 7l8 5 8-5"/></svg><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></li>
        </ul>

        <hr>
        <h3 style="font-size:var(--fs-base)">Giờ làm việc</h3>
        <p style="font-size:var(--fs-sm);margin:0">Thứ Hai - Thứ Bảy: 8h00 - 21h00<br>Chủ Nhật: hỗ trợ khẩn cấp qua Zalo</p>

        <hr>
        <h3 style="font-size:var(--fs-base)">Cam kết phản hồi</h3>
        <ul class="check-list">
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Tư vấn chọn dự án: trong 4 giờ</li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Báo giá tuỳ chỉnh: trong 24 giờ</li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Xử lý lỗi kỹ thuật: trong 48 giờ</li>
        </ul>
      </div>
    </aside>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
