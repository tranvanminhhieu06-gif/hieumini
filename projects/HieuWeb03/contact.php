<?php
// contact.php - Contact & Help Center
$custom_page_title = "Liên Hệ & Hỗ Trợ Khách Hàng";
require_once __DIR__ . '/config/app.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_contact'])) {
    $fullname = clean_input($_POST['fullname'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $subject = clean_input($_POST['subject'] ?? '');
    $message = clean_input($_POST['message'] ?? '');

    if (empty($fullname) || empty($email) || empty($message)) {
        $error_msg = 'Vui lòng điền đầy đủ Họ tên, Email và Nội dung tin nhắn.';
    } else {
        $ins = $pdo->prepare("INSERT INTO contacts (fullname, email, phone, subject, message, status) VALUES (?, ?, ?, ?, ?, 'new')");
        $ins->execute([$fullname, $email, $phone, $subject, $message]);
        $success_msg = 'Tin nhắn của bạn đã được gửi thành công! Đội ngũ HieuMini sẽ phản hồi trong vòng 24 giờ.';
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="margin: 40px auto 70px;">
  <!-- Breadcrumb -->
  <div style="padding: 10px 0 20px; font-size: 0.88rem; color: var(--muted); display: flex; align-items: center; gap: 8px;">
    <a href="index.php" style="color: var(--muted);"><i class="bi bi-house"></i> Trang chủ</a>
    <span>/</span>
    <span style="color: var(--dark); font-weight: 700;">Liên hệ</span>
  </div>

  <div class="contact-layout">
    <!-- Left: Store Contact Information -->
    <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 36px; box-shadow: var(--shadow-sm);">
      <span class="section-pill">THÔNG TIN LIÊN HỆ</span>
      <h2 style="font-size: 1.8rem; font-weight: 800; margin-bottom: 16px; color: var(--dark);">HieuMini Luôn Sẵn Sàng Lắng Nghe Bạn</h2>
      <p style="color: var(--muted); font-size: 0.95rem; line-height: 1.7; margin-bottom: 30px;">
        Bạn cần tư vấn chọn đồ dùng học tập, phản hồi dịch vụ hoặc đặt mua số lượng lớn cho trường lớp? Hãy liên hệ ngay với chúng tôi!
      </p>

      <div style="display: flex; flex-direction: column; gap: 20px;">
        <div style="display: flex; gap: 16px; align-items: flex-start;">
          <div style="width: 44px; height: 44px; border-radius: 12px; background: #e0e7ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
            <i class="bi bi-geo-alt-fill"></i>
          </div>
          <div>
            <div style="font-weight: 700; color: var(--dark); font-size: 1rem;">Địa Chỉ Cửa Hàng</div>
            <div style="font-size: 0.9rem; color: var(--muted);">Tòa nhà HieuMini Tower, 123 Đường Cầu Giấy, Hà Nội</div>
          </div>
        </div>

        <div style="display: flex; gap: 16px; align-items: flex-start;">
          <div style="width: 44px; height: 44px; border-radius: 12px; background: #fdf2f8; color: var(--secondary); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
            <i class="bi bi-telephone-fill"></i>
          </div>
          <div>
            <div style="font-weight: 700; color: var(--dark); font-size: 1rem;">Hotline Tư Vấn 24/7</div>
            <div style="font-size: 0.9rem; color: var(--muted);">090.123.4567 (Zalo / Call)</div>
          </div>
        </div>

        <div style="display: flex; gap: 16px; align-items: flex-start;">
          <div style="width: 44px; height: 44px; border-radius: 12px; background: #ecfdf5; color: var(--accent-emerald); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
            <i class="bi bi-envelope-fill"></i>
          </div>
          <div>
            <div style="font-weight: 700; color: var(--dark); font-size: 1rem;">Email Hỗ Trợ</div>
            <div style="font-size: 0.9rem; color: var(--muted);">support@hieumini.vn</div>
          </div>
        </div>

        <div style="display: flex; gap: 16px; align-items: flex-start;">
          <div style="width: 44px; height: 44px; border-radius: 12px; background: #fef3c7; color: var(--accent-amber); display: flex; align-items: center; justify-content: center; font-size: 1.2rem; flex-shrink: 0;">
            <i class="bi bi-clock-fill"></i>
          </div>
          <div>
            <div style="font-weight: 700; color: var(--dark); font-size: 1rem;">Thời Gian Hoạt Động</div>
            <div style="font-size: 0.9rem; color: var(--muted);">Thứ 2 - Chủ Nhật: 08:00 - 22:00</div>
          </div>
        </div>
      </div>
    </div>

    <!-- Right: Message Form -->
    <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 36px; box-shadow: var(--shadow-sm);">
      <h3 style="font-size: 1.3rem; font-weight: 800; margin-bottom: 20px; color: var(--dark);">Gửi Lời Nhắn Đến HieuMini</h3>

      <?php if (!empty($success_msg)): ?>
        <div style="background: #dcfce7; color: #15803d; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; font-weight: 600;">
          <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success_msg) ?>
        </div>
      <?php endif; ?>

      <?php if (!empty($error_msg)): ?>
        <div style="background: #fee2e2; color: #dc2626; padding: 14px 18px; border-radius: var(--radius-md); margin-bottom: 20px; font-weight: 600;">
          <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error_msg) ?>
        </div>
      <?php endif; ?>

      <form action="contact.php" method="POST">
        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="form-group">
            <label class="form-label">Họ và tên *</label>
            <input type="text" name="fullname" required class="form-control" placeholder="Nguyễn Văn A" value="<?= is_logged_in() ? htmlspecialchars(current_user()['fullname']) : '' ?>">
          </div>
          <div class="form-group">
            <label class="form-label">Số điện thoại</label>
            <input type="tel" name="phone" class="form-control" placeholder="0901234567" value="<?= is_logged_in() ? htmlspecialchars(current_user()['phone'] ?? '') : '' ?>">
          </div>
        </div>

        <div class="form-group">
          <label class="form-label">Địa chỉ Email *</label>
          <input type="email" name="email" required class="form-control" placeholder="email@example.com" value="<?= is_logged_in() ? htmlspecialchars(current_user()['email']) : '' ?>">
        </div>

        <div class="form-group">
          <label class="form-label">Tiêu đề</label>
          <input type="text" name="subject" class="form-control" placeholder="Tư vấn combo đồ dùng học tập...">
        </div>

        <div class="form-group">
          <label class="form-label">Nội dung tin nhắn *</label>
          <textarea name="message" required rows="4" class="form-control" placeholder="Nhập nội dung cần hỗ trợ tại đây..."></textarea>
        </div>

        <button type="submit" name="send_contact" value="1" class="btn btn-primary btn-lg" style="width: 100%; justify-content: center;">
          <i class="bi bi-send-fill"></i> Gửi Tin Nhắn
        </button>
      </form>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
