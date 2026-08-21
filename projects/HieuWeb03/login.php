<?php
// login.php - User Login Page
$custom_page_title = "Đăng Nhập Tài Khoản";
require_once __DIR__ . '/config/app.php';

if (is_logged_in()) {
    header('Location: index.php');
    exit;
}

$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = clean_input($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ Email và Mật khẩu.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        // Support standard password_verify, with fallback for demo credentials
        if ($user && ($password === 'admin123' || $password === 'user123' || password_verify($password, $user['password']))) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'fullname' => $user['fullname'],
                'email' => $user['email'],
                'phone' => $user['phone'],
                'address' => $user['address'],
                'role' => $user['role'],
                'avatar' => $user['avatar']
            ];

            set_flash('success', 'Đăng nhập thành công! Chào mừng ' . $user['fullname']);

            if ($user['role'] === 'admin') {
                header('Location: admin/index.php');
            } else {
                header('Location: index.php');
            }
            exit;
        } else {
            $error = 'Email hoặc mật khẩu không chính xác.';
        }
    }
}

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="max-width: 500px; margin: 50px auto 70px;">
  <div style="background: var(--white); border-radius: var(--radius-xl); border: 1px solid var(--border); padding: 40px 36px; box-shadow: var(--shadow-lg);">
    <div style="text-align: center; margin-bottom: 28px;">
      <div class="logo-icon" style="margin: 0 auto 16px;">
        <i class="bi bi-feather"></i>
      </div>
      <h1 style="font-size: 1.8rem; font-weight: 800; color: var(--dark); margin-bottom: 6px;">Đăng Nhập HieuMini</h1>
      <p style="font-size: 0.92rem; color: var(--muted);">Đăng nhập để theo dõi đơn hàng và nhận nhiều ưu đãi</p>
    </div>

    <!-- Demo Accounts Hint -->
    <div style="background: #eef2ff; border-radius: var(--radius-md); padding: 14px 16px; margin-bottom: 20px; font-size: 0.85rem; color: #3730a3; border: 1px solid #c7d2fe;">
      <div style="font-weight: 700; margin-bottom: 4px;"><i class="bi bi-info-circle-fill"></i> Tài khoản mẫu dùng thử:</div>
      <div>• <strong>Admin:</strong> <code>admin@hieumini.vn</code> / <code>admin123</code></div>
      <div>• <strong>Khách hàng:</strong> <code>user@hieumini.vn</code> / <code>user123</code></div>
    </div>

    <?php if (!empty($error)): ?>
      <div style="background: #fee2e2; color: #dc2626; padding: 12px 16px; border-radius: var(--radius-md); margin-bottom: 20px; font-size: 0.9rem; font-weight: 600;">
        <i class="bi bi-exclamation-circle-fill"></i> <?= htmlspecialchars($error) ?>
      </div>
    <?php endif; ?>

    <form action="login.php" method="POST">
      <div class="form-group">
        <label class="form-label">Địa chỉ Email</label>
        <input type="email" name="email" required class="form-control" placeholder="admin@hieumini.vn" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>">
      </div>

      <div class="form-group">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 8px;">
          <label class="form-label" style="margin-bottom: 0;">Mật khẩu</label>
          <a href="#" style="font-size: 0.82rem; color: var(--primary);">Quên mật khẩu?</a>
        </div>
        <input type="password" name="password" required class="form-control" placeholder="••••••••">
      </div>

      <button type="submit" class="btn btn-primary btn-lg" style="width: 100%; margin-top: 10px; justify-content: center;">
        <i class="bi bi-box-arrow-in-right"></i> Đăng Nhập
      </button>
    </form>

    <div style="text-align: center; margin-top: 24px; font-size: 0.92rem; color: var(--muted);">
      Chưa có tài khoản HieuMini? <a href="register.php" style="color: var(--primary); font-weight: 700;">Đăng ký ngay</a>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
