<?php
/**
 * Đăng nhập quản trị.
 *
 * Có hai lớp chống dò mật khẩu: giới hạn 5 lần sai mỗi phiên trong 10 phút,
 * và thông báo lỗi không nói rõ sai tên hay sai mật khẩu — nói rõ là giúp
 * người dò biết tài khoản nào có thật.
 */
declare(strict_types=1);

require __DIR__ . '/guard.php';

if (current_admin()) {
    header('Location: ' . admin_url('index.php'));
    exit;
}

const MAX_ATTEMPTS = 5;
const LOCK_SECONDS = 600;

$error = null;
$attempts = $_SESSION['login_attempts'] ?? ['count' => 0, 'first' => time()];

if (time() - $attempts['first'] > LOCK_SECONDS) {
    $attempts = ['count' => 0, 'first' => time()];
}
$locked = $attempts['count'] >= MAX_ATTEMPTS;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($locked) {
        $error = 'Bạn đã thử sai quá nhiều lần. Vui lòng đợi 10 phút rồi thử lại.';
    } elseif (!csrf_check($_POST['csrf'] ?? null)) {
        $error = 'Phiên làm việc đã hết hạn. Tải lại trang rồi đăng nhập lại.';
    } else {
        $username = trim((string) ($_POST['username'] ?? ''));
        $password = (string) ($_POST['password'] ?? '');
        $row = db_one('SELECT * FROM admins WHERE username = :u', [':u' => $username]);

        if ($row && password_verify($password, $row['password_hash'])) {
            // Đổi ID phiên sau khi đăng nhập để chặn tấn công cố định phiên
            session_regenerate_id(true);
            $_SESSION['admin_id'] = (int) $row['id'];
            unset($_SESSION['login_attempts']);

            $next = $_SESSION['after_login'] ?? admin_url('index.php');
            unset($_SESSION['after_login']);
            header('Location: ' . $next);
            exit;
        }

        $attempts['count']++;
        $_SESSION['login_attempts'] = $attempts;
        $error = 'Tên đăng nhập hoặc mật khẩu không đúng. Còn '
            . max(0, MAX_ATTEMPTS - $attempts['count']) . ' lần thử.';
    }
}
?>
<!doctype html>
<html lang="vi" data-theme="dark">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title>Đăng nhập quản trị — <?= e(SITE_NAME) ?></title>
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
</head>
<body>
<main id="main" style="min-height:100vh;display:grid;place-items:center;padding:var(--space-6)">
  <div style="width:100%;max-width:420px">
    <a class="brand" href="<?= e(url('index.php')) ?>" style="display:block;text-align:center;margin-bottom:var(--space-6)">
      HieuMini<span>.</span>
    </a>

    <form method="post" style="background:var(--surface);border:1px solid var(--border);
                               border-radius:var(--r-md);padding:var(--space-7)">
      <h1 style="font-size:var(--fs-xl);margin-bottom:var(--space-5)">Đăng nhập quản trị</h1>

      <?php foreach (flash_pull() as $f): ?>
        <div class="alert alert--<?= $f['type'] === 'error' ? 'error' : 'ok' ?>"><?= e($f['message']) ?></div>
      <?php endforeach; ?>

      <?php if ($error): ?>
        <div class="alert alert--error" role="alert"><?= e($error) ?></div>
      <?php endif; ?>

      <?= csrf_field() ?>

      <div class="field">
        <label for="username">Tên đăng nhập</label>
        <input type="text" id="username" name="username" required autofocus autocomplete="username"
               value="<?= e($_POST['username'] ?? '') ?>">
      </div>

      <div class="field">
        <label for="password">Mật khẩu</label>
        <input type="password" id="password" name="password" required autocomplete="current-password">
      </div>

      <button class="btn btn--primary" type="submit" style="width:100%" <?= $locked ? 'disabled' : '' ?>>
        Đăng nhập
      </button>

      <p style="margin:var(--space-5) 0 0;font-size:var(--fs-xs);color:var(--fg-subtle);text-align:center">
        Tài khoản mặc định: <code>admin</code> / <code>hieumini2026</code> — đổi ngay sau khi cài đặt.
      </p>
    </form>

    <p style="text-align:center;margin-top:var(--space-5)">
      <a href="<?= e(url('index.php')) ?>" style="font-size:var(--fs-sm)">← Về trang chủ</a>
    </p>
  </div>
</main>
</body>
</html>
