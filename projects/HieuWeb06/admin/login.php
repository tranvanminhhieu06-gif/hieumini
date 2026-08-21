<?php
/**
 * HieuMini Admin - Đăng nhập quản trị
 */
require_once dirname(__DIR__) . '/includes/config.php';

if (is_logged_in() && is_admin()) {
    redirect('admin/index.php');
}

$error = '';
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $email    = mb_strtolower(input('email'), 'UTF-8');
    $password = (string)($_POST['password'] ?? '');

    $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND role = "admin" AND status = 1 LIMIT 1');
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if ($user && password_verify($password, $user['password'])) {
        session_regenerate_id(true);
        $_SESSION['user_id']    = (int)$user['id'];
        $_SESSION['user_name']  = $user['full_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role']  = 'admin';
        $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);
        flash('Đăng nhập quản trị thành công.');
        redirect('admin/index.php');
    }
    $error = 'Thông tin đăng nhập không đúng hoặc tài khoản không có quyền quản trị.';
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Đăng nhập quản trị · <?= e(SITE_NAME) ?></title>
<link rel="icon" type="image/svg+xml" href="<?= e(asset('assets/images/favicon.svg')) ?>">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:wght@300;400;500;600;700;800&family=Space+Grotesk:wght@500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="<?= e(asset('assets/css/style.css')) ?>">
<script>
(function () {
  document.documentElement.classList.add('js');
  try {
    var saved = localStorage.getItem('hm-theme');
    var theme = saved || (window.matchMedia('(prefers-color-scheme: light)').matches ? 'light' : 'dark');
    if (theme === 'light') document.documentElement.setAttribute('data-theme', 'light');
  } catch (e) {}
})();
</script>
</head>
<body>
<div class="aurora" aria-hidden="true"><span class="aurora__blob aurora__blob--1"></span><span class="aurora__blob aurora__blob--2"></span></div>

<div class="container auth-wrap" style="min-height:100vh">
  <div class="glass auth-card">
    <a class="brand" href="<?= e(url('index.php')) ?>" style="margin-bottom:var(--sp-4)">
      <svg class="brand__mark" width="40" height="40" style="width:40px;height:40px;flex-shrink:0" viewBox="0 0 40 40" aria-hidden="true">
        <defs><linearGradient id="lg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#7C3AED"/><stop offset="1" stop-color="#22D3EE"/></linearGradient></defs>
        <rect x="1.5" y="1.5" width="37" height="37" rx="11" fill="url(#lg)" opacity=".18"/>
        <rect x="1.5" y="1.5" width="37" height="37" rx="11" fill="none" stroke="url(#lg)" stroke-width="2"/>
        <path d="M12 28V12h3.6v6.2h8.8V12H28v16h-3.6v-6.4h-8.8V28z" fill="url(#lg)"/>
      </svg>
      <span class="brand__text"><span class="brand__hieu">Hieu</span><span class="brand__mini">Mini</span></span>
    </a>

    <h1 style="font-size:var(--fs-2xl)">Khu vực quản trị</h1>
    <p>Chỉ tài khoản có quyền quản trị mới truy cập được khu vực này.</p>

    <?php if ($error): ?>
      <div class="flash flash--error" style="position:static;margin-bottom:var(--sp-4)"><?= e($error) ?></div>
    <?php endif; ?>

    <form method="post" class="form-grid" data-validate novalidate>
      <?= csrf_field() ?>
      <div class="field">
        <label for="email">Email quản trị</label>
        <input type="email" id="email" name="email" required autocomplete="username" value="<?= e($email) ?>">
      </div>
      <div class="field">
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>
      <button class="btn btn--primary btn--block btn--lg" type="submit">Đăng nhập</button>
    </form>

    <div class="divider">Tài khoản dùng thử</div>
    <div class="demo-hint">Email: <strong>admin@hieumini.vn</strong> · Mật khẩu: <strong>admin123</strong></div>
    <p style="margin-top:var(--sp-4);text-align:center"><a href="<?= e(url('index.php')) ?>">← Về trang chủ</a></p>
  </div>
</div>
<script src="<?= e(asset('assets/js/main.js')) ?>" defer></script>
</body>
</html>
