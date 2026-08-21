<?php
/**
 * HieuMini - Đăng nhập thành viên
 */
require_once __DIR__ . '/includes/config.php';

if (is_logged_in()) {
    redirect('account.php');
}

$errors = [];
$email = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();

    // Chặn dò mật khẩu: tối đa 5 lần sai trong 10 phút
    $now = time();
    $tries = $_SESSION['login_tries'] ?? ['count' => 0, 'first' => $now];
    if ($now - $tries['first'] > 600) {
        $tries = ['count' => 0, 'first' => $now];
    }

    $email    = mb_strtolower(input('email'), 'UTF-8');
    $password = (string)($_POST['password'] ?? '');

    if ($tries['count'] >= 5) {
        $errors['general'] = 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau 10 phút.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
        $errors['general'] = 'Vui lòng nhập email và mật khẩu hợp lệ.';
    } else {
        $stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND status = 1 LIMIT 1');
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            session_regenerate_id(true);          // chống cố định phiên
            $_SESSION['user_id']    = (int)$user['id'];
            $_SESSION['user_name']  = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role']  = $user['role'];
            unset($_SESSION['login_tries']);

            $pdo->prepare('UPDATE users SET last_login = NOW() WHERE id = ?')->execute([$user['id']]);
            flash('Chào mừng ' . $user['full_name'] . ' quay lại!');

            $back = $_SESSION['redirect_after_login'] ?? null;
            unset($_SESSION['redirect_after_login']);
            redirect($back ?: ($user['role'] === 'admin' ? 'admin/index.php' : 'account.php'));
        }

        $tries['count']++;
        $_SESSION['login_tries'] = $tries;
        $errors['general'] = 'Email hoặc mật khẩu không đúng. Còn ' . max(0, 5 - $tries['count']) . ' lần thử.';
    }
}

seo([
    'title'       => 'Đăng nhập tài khoản | ' . SITE_NAME,
    'description' => 'Đăng nhập HieuMini để theo dõi đơn hàng, tải lại mã nguồn đã mua và quản lý danh sách yêu thích.',
    'robots'      => 'noindex, follow',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container auth-wrap">
  <div class="glass auth-card reveal reveal--zoom">
    <h1 style="font-size:var(--fs-2xl)">Đăng nhập</h1>
    <p>Chưa có tài khoản? <a href="<?= e(url('register.php')) ?>">Đăng ký miễn phí</a>.</p>

    <?php if (!empty($errors['general'])): ?>
      <div class="flash flash--error" style="position:static;margin-bottom:var(--sp-4)"><?= e($errors['general']) ?></div>
    <?php endif; ?>

    <form method="post" class="form-grid" data-validate novalidate>
      <?= csrf_field() ?>
      <div class="field">
        <label for="email">Email</label>
        <input type="email" id="email" name="email" required autocomplete="email" value="<?= e($email) ?>">
      </div>
      <div class="field">
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" required autocomplete="current-password" minlength="6">
      </div>
      <button class="btn btn--primary btn--block btn--lg" type="submit">Đăng nhập</button>
    </form>

    <div class="divider">Tài khoản dùng thử</div>
    <div class="demo-hint">
      Thành viên: <strong>user@hieumini.vn</strong> / <strong>user123</strong><br>
      Quản trị: <strong>admin@hieumini.vn</strong> / <strong>admin123</strong>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
