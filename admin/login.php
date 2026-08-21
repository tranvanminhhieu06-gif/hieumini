<?php
/**
 * HieuMini Admin — Đăng nhập
 * Đối chiếu mật khẩu người dùng nhập với biến môi trường ADMIN_PASSWORD.
 * Không có tài khoản nào được lưu trong cơ sở dữ liệu.
 */

declare(strict_types=1);
require __DIR__ . '/guard.php';

if (!empty($_SESSION['admin_ok'])) {
    redirect('admin/index.php');
}

$error = '';

/* Giới hạn 5 lần thử sai trong mỗi 10 phút để chống dò mật khẩu. */
$attempts = $_SESSION['admin_attempts'] ?? ['count' => 0, 'until' => 0];
$lockedFor = max(0, (int) $attempts['until'] - time());

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($lockedFor > 0) {
        $error = 'Bạn đã nhập sai quá nhiều lần. Vui lòng thử lại sau ' . ceil($lockedFor / 60) . ' phút.';
    } elseif (!csrf_check()) {
        $error = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang.';
    } else {
        $input = (string) ($_POST['password'] ?? '');

        if ($input !== '' && hash_equals(ADMIN_PASSWORD, $input)) {
            session_regenerate_id(true);
            $_SESSION['admin_ok']   = true;
            $_SESSION['admin_seen'] = time();
            unset($_SESSION['admin_attempts']);
            flash('success', 'Đăng nhập thành công. Chào mừng trở lại!');
            redirect('admin/index.php');
        }

        $attempts['count']++;
        if ($attempts['count'] >= 5) {
            $attempts = ['count' => 0, 'until' => time() + 600];
            $error = 'Sai mật khẩu 5 lần. Tài khoản tạm khóa 10 phút.';
        } else {
            $error = 'Mật khẩu không đúng. Còn ' . (5 - $attempts['count']) . ' lần thử.';
        }
        $_SESSION['admin_attempts'] = $attempts;
    }
}
?>
<!doctype html>
<html lang="vi" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Đăng nhập quản trị — <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<link rel="stylesheet" href="<?= e(asset('css/admin.css')) ?>">
</head>
<body class="admin-login">

<div class="login-canvas" aria-hidden="true">
  <svg viewBox="0 0 900 700" preserveAspectRatio="xMidYMid slice">
    <defs>
      <linearGradient id="lg1" x1="0" y1="0" x2="1" y2="1">
        <stop offset="0%" stop-color="#6366F1"/><stop offset="100%" stop-color="#7C3AED"/>
      </linearGradient>
      <linearGradient id="lg2" x1="1" y1="0" x2="0" y2="1">
        <stop offset="0%" stop-color="#06B6D4"/><stop offset="100%" stop-color="#8B5CF6"/>
      </linearGradient>
      <filter id="lsoft" x="-40%" y="-40%" width="180%" height="180%">
        <feGaussianBlur stdDeviation="52"/>
      </filter>
    </defs>
    <g filter="url(#lsoft)" opacity=".55">
      <path data-morph data-cx="250" data-cy="230" data-radius="210" data-wobble="0.5" fill="url(#lg1)"/>
      <path data-morph data-cx="650" data-cy="470" data-radius="190" data-wobble="0.5" fill="url(#lg2)"/>
    </g>
  </svg>
</div>

<div class="login-box">
  <span class="brand-mark" aria-hidden="true" style="width:48px;height:48px;border-radius:15px;font-size:19px">H</span>
  <h1>Khu vực quản trị</h1>
  <p>Nhập mật khẩu được cấu hình trong biến môi trường <code>ADMIN_PASSWORD</code> của máy chủ.</p>

  <?php foreach (flash_pull() as $f): ?>
    <div class="alert alert--<?= $f['type'] === 'error' ? 'error' : 'success' ?>" role="status">
      <?= icon($f['type'] === 'error' ? 'close' : 'check', 'ico ico-sm') ?> <?= e($f['message']) ?>
    </div>
  <?php endforeach; ?>

  <?php if ($error !== ''): ?>
    <div class="alert alert--error" role="alert">
      <?= icon('close', 'ico ico-sm') ?> <?= e($error) ?>
    </div>
  <?php endif; ?>

  <form method="post" novalidate>
    <?= csrf_field() ?>
    <div class="field">
      <label for="pw">Mật khẩu quản trị</label>
      <input type="password" id="pw" name="password" required autofocus autocomplete="current-password"
             <?= $lockedFor > 0 ? 'disabled' : '' ?>>
    </div>
    <button type="submit" class="btn btn--primary btn--block" <?= $lockedFor > 0 ? 'disabled' : '' ?>>
      <?= icon('lock', 'ico ico-sm') ?> Đăng nhập
    </button>
  </form>

  <a class="login-back" href="<?= e(url('index.php')) ?>">← Quay về website</a>
</div>

<script src="<?= e(asset('js/main.js')) ?>" defer></script>
</body>
</html>
