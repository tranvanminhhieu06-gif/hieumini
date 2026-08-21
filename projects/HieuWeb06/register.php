<?php
/**
 * HieuMini - Đăng ký tài khoản
 */
require_once __DIR__ . '/includes/config.php';

if (is_logged_in()) {
    redirect('account.php');
}

$errors = [];
$form = ['full_name' => '', 'email' => '', 'phone' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();

    $form['full_name'] = input('full_name');
    $form['email']     = mb_strtolower(input('email'), 'UTF-8');
    $form['phone']     = preg_replace('/\s+/', '', input('phone')) ?? '';
    $password  = (string)($_POST['password'] ?? '');
    $password2 = (string)($_POST['password2'] ?? '');

    if (mb_strlen($form['full_name'], 'UTF-8') < 2) {
        $errors['full_name'] = 'Họ tên phải có ít nhất 2 ký tự.';
    }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Địa chỉ email chưa đúng định dạng.';
    } else {
        $chk = $pdo->prepare('SELECT 1 FROM users WHERE email = ?');
        $chk->execute([$form['email']]);
        if ($chk->fetchColumn()) {
            $errors['email'] = 'Email này đã được đăng ký.';
        }
    }
    if ($form['phone'] !== '' && !preg_match('/^0\d{9}$/', $form['phone'])) {
        $errors['phone'] = 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.';
    }
    if (strlen($password) < 6) {
        $errors['password'] = 'Mật khẩu phải có ít nhất 6 ký tự.';
    }
    if ($password !== $password2) {
        $errors['password2'] = 'Hai lần nhập mật khẩu chưa khớp nhau.';
    }

    if (!$errors) {
        $stmt = $pdo->prepare('INSERT INTO users (full_name, email, password, phone, role) VALUES (?,?,?,?,"user")');
        $stmt->execute([
            $form['full_name'],
            $form['email'],
            password_hash($password, PASSWORD_BCRYPT),
            $form['phone'] ?: null,
        ]);

        session_regenerate_id(true);
        $_SESSION['user_id']    = (int)$pdo->lastInsertId();
        $_SESSION['user_name']  = $form['full_name'];
        $_SESSION['user_email'] = $form['email'];
        $_SESSION['user_role']  = 'user';

        flash('Tạo tài khoản thành công. Chào mừng bạn đến với HieuMini!');
        redirect('account.php');
    }
}

seo([
    'title'       => 'Đăng ký tài khoản miễn phí | ' . SITE_NAME,
    'description' => 'Tạo tài khoản HieuMini để lưu dự án yêu thích, theo dõi đơn hàng và nhận ưu đãi dành cho thành viên.',
    'robots'      => 'noindex, follow',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container auth-wrap">
  <div class="glass auth-card reveal reveal--zoom">
    <h1 style="font-size:var(--fs-2xl)">Tạo tài khoản</h1>
    <p>Đã có tài khoản? <a href="<?= e(url('login.php')) ?>">Đăng nhập ngay</a>.</p>

    <form method="post" class="form-grid" data-validate novalidate>
      <?= csrf_field() ?>

      <div class="field">
        <label for="full_name">Họ và tên <span aria-hidden="true">*</span></label>
        <input type="text" id="full_name" name="full_name" required autocomplete="name" value="<?= e($form['full_name']) ?>"
               aria-invalid="<?= isset($errors['full_name']) ? 'true' : 'false' ?>">
        <?php if (isset($errors['full_name'])): ?><span class="error"><?= e($errors['full_name']) ?></span><?php endif; ?>
      </div>

      <div class="field">
        <label for="email">Email <span aria-hidden="true">*</span></label>
        <input type="email" id="email" name="email" required autocomplete="email" value="<?= e($form['email']) ?>"
               aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>">
        <?php if (isset($errors['email'])): ?><span class="error"><?= e($errors['email']) ?></span><?php endif; ?>
      </div>

      <div class="field">
        <label for="phone">Số điện thoại</label>
        <input type="tel" id="phone" name="phone" autocomplete="tel" inputmode="numeric" value="<?= e($form['phone']) ?>"
               aria-invalid="<?= isset($errors['phone']) ? 'true' : 'false' ?>">
        <?php if (isset($errors['phone'])): ?><span class="error"><?= e($errors['phone']) ?></span>
        <?php else: ?><span class="hint">Không bắt buộc. Dùng để hỗ trợ nhanh qua Zalo.</span><?php endif; ?>
      </div>

      <div class="field">
        <label for="password">Mật khẩu <span aria-hidden="true">*</span></label>
        <input type="password" id="password" name="password" required minlength="6" autocomplete="new-password"
               aria-invalid="<?= isset($errors['password']) ? 'true' : 'false' ?>">
        <?php if (isset($errors['password'])): ?><span class="error"><?= e($errors['password']) ?></span>
        <?php else: ?><span class="hint">Tối thiểu 6 ký tự, nên gồm cả chữ và số.</span><?php endif; ?>
      </div>

      <div class="field">
        <label for="password2">Nhập lại mật khẩu <span aria-hidden="true">*</span></label>
        <input type="password" id="password2" name="password2" required minlength="6" autocomplete="new-password"
               aria-invalid="<?= isset($errors['password2']) ? 'true' : 'false' ?>">
        <?php if (isset($errors['password2'])): ?><span class="error"><?= e($errors['password2']) ?></span><?php endif; ?>
      </div>

      <label class="checkbox">
        <input type="checkbox" name="agree" value="1" required>
        <span>Tôi đồng ý với <a href="<?= e(url('about.php#license')) ?>">điều khoản sử dụng</a> của HieuMini.</span>
      </label>

      <button class="btn btn--primary btn--block btn--lg" type="submit">Đăng ký</button>
    </form>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
