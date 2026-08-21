<?php
// register.php - User Registration
$custom_page_title = "Đăng Ký Tài Khoản";
require_once __DIR__ . '/config/app.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = clean_input($_POST['fullname'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $address = clean_input($_POST['address'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($fullname) || empty($email) || empty($password)) {
        $error = 'Vui lòng điền các trường thông tin bắt buộc (*).';
    } elseif ($password !== $confirm_password) {
        $error = 'Mật khẩu xác nhận không khớp.';
    } elseif (strlen($password) < 6) {
        $error = 'Mật khẩu phải có độ dài tối thiểu 6 ký tự.';
    } else {
        // Check existing email
        $chk = $pdo->prepare("SELECT id FROM users WHERE email = ?");
        $chk->execute([$email]);
        if ($chk->fetch()) {
            $error = 'Email này đã được đăng ký tài khoản khác.';
        } else {
            $hashed = password_hash($password, PASSWORD_DEFAULT);
            $ins = $pdo->prepare("INSERT INTO users (fullname, email, password, phone, address, role) VALUES (?, ?, ?, ?, ?, 'customer')");
            $ins->execute([$fullname, $email, $hashed, $phone, $address]);
            $new_user_id = $pdo->lastInsertId();

            $_SESSION['user'] = [
                'id' => $new_user_id,
                'fullname' => $fullname,
                'email' => $email,
                'phone' => $phone,
                'address' => $address,
                'role' => 'customer',
                'avatar' => 'default_avatar.png'
            ];

            set_flash('success', 'Đăng ký tài khoản thành công! Chào mừng bạn đến với HieuMini.');
            header('Location: index.php');
            exit;
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="max-width: 550px; margin: 40px auto 70px;">
  <div style="background: var(--white); border-radius: var(--radius-xl); border: 1px solid var(--border); padding: 40px 36px; box-shadow: var(--shadow-lg);">
    <div style="text-align: center; margin-bottom: 28px;">
      <div class="logo-icon" style="margin: 0 auto 16px;">
        <i class="bi bi-feather"></i>
      </div>
      <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark); margin-bottom: 6px;">Tạo Tài Khoản Mới</h1>
      <p style="font-size: 0.92rem; color: var(--muted);">Gia nhập cộng đồng người yêu thích đồ dùng học tập sáng tạo</p>
    </div>

    <?php if (!empty($error)): ?>
      <div style="background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 0.9rem; font-weight: 600;">
        <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="register.php" method="POST">
      <div class="form-group">
        <label class="form-label">Họ và tên *</label>
        <input type="text" name="fullname" required class="form-control" placeholder="Nguyễn Văn A" value="<?= htmlspecialchars($_POST['fullname'] ?? '') ?>">
      </div>

      <div class="form-group">
        <label class="form-label">Địa chỉ Email *</label>
        <input type="email" name="email" required class="form-control" placeholder="example@domain.com" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="form-row-2col">
        <div class="form-group">
          <label class="form-label">Số điện thoại</label>
          <input type="tel" name="phone" class="form-control" placeholder="0901234567" value="<?= htmlspecialchars($_POST['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label">Địa chỉ giao hàng</label>
          <input type="text" name="address" class="form-control" placeholder="Hà Nội" value="<?= htmlspecialchars($_POST['address'] ?? '') ?>">
        </div>
      </div>

      <div class="form-row-2col">
        <div class="form-group">
          <label class="form-label">Mật khẩu *</label>
          <input type="password" name="password" required class="form-control" placeholder="Tối thiểu 6 ký tự">
        </div>
        <div class="form-group">
          <label class="form-label">Xác nhận mật khẩu *</label>
          <input type="password" name="confirm_password" required class="form-control" placeholder="Nhập lại mật khẩu">
        </div>
      </div>

      <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 10px; justify-content: center;">
        <i class="bi bi-person-plus-fill"></i> Đăng Ký Tài Khoản
      </button>
    </form>

    <div style="text-align: center; margin-top: 24px; font-size: 0.92rem; color: var(--muted);">
      Đã có tài khoản? <a href="login.php" style="color: var(--primary); font-weight: 700;">Đăng nhập ngay</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
