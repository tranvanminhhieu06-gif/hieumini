<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

if (is_admin()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $user = null;
    if ($pdo) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin' AND status = 1");
            $stmt->execute([$email]);
            $user = $stmt->fetch();
        } catch (Exception $e) {}
    }

    $is_valid = false;
    if ($user && password_verify($password, $user['password'])) {
        $is_valid = true;
    } elseif ($email === 'admin@hieumini.vn' && $password === 'admin123') {
        $user = [
            'id' => 1,
            'full_name' => 'Trần Văn Minh Hiếu (Admin)',
            'email' => 'admin@hieumini.vn',
            'role' => 'admin'
        ];
        $is_valid = true;
    }

    if ($is_valid) {
        $_SESSION['user'] = $user;
        set_flash('success', 'Đăng nhập trang Quản trị thành công!');
        header("Location: index.php");
        exit;
    } else {
        set_flash('danger', 'Tài khoản hoặc mật khẩu Quản trị viên không chính xác!');
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị - HieuMini</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body style="display: flex; align-items: center; justify-content: center; min-height: 100vh;">
    <div style="width: 100%; max-width: 440px; padding: 20px;">
        <?php echo display_flash(); ?>
        <div class="glass-panel" style="padding: 40px 30px; text-align: center;">
            <div style="width: 70px; height: 70px; border-radius: var(--radius-md); background: rgba(99, 102, 241, 0.2); color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 2rem; margin: 0 auto 20px; border: 1px solid rgba(99, 102, 241, 0.4);">
                <i class="fa-solid fa-shield-halved"></i>
            </div>
            <h2 style="font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 6px;">HieuMini Admin Portal</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem; margin-bottom: 28px;">Hệ thống kiểm soát và quản trị website</p>

            <form action="login.php" method="POST" style="text-align: left;">
                <div class="form-group">
                    <label>Email Quản trị viên:</label>
                    <input type="email" name="email" class="form-control" placeholder="admin@hieumini.vn" required value="<?php echo htmlspecialchars($_POST['email'] ?? 'admin@hieumini.vn'); ?>">
                </div>

                <div class="form-group">
                    <label>Mật khẩu:</label>
                    <input type="password" name="password" class="form-control" placeholder="admin123" required value="admin123">
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; margin-top: 10px;">
                    <i class="fa-solid fa-lock-open"></i> ĐĂNG NHẬP DASHBOARD
                </button>
            </form>

            <div style="margin-top: 20px;">
                <a href="../index.php" style="color: var(--text-muted); font-size: 0.85rem;"><i class="fa-solid fa-arrow-left"></i> Quay lại website bán hàng</a>
            </div>
        </div>
    </div>
</body>
</html>
