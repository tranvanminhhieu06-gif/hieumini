<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - ADMIN LOGIN
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/../includes/config.php';

$error = '';

if (is_admin_logged_in()) {
    header("Location: " . BASE_URL . "/admin/index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!empty($email) && !empty($password)) {
        // Tìm user với email
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $admin = $stmt->fetch();

        // Kiểm tra mật khẩu (hỗ trợ hash và mật khẩu mặc định Admin@123)
        if ($admin && ($password === 'Admin@123' || password_verify($password, $admin['password']))) {
            $_SESSION['admin_user'] = [
                'id' => $admin['id'],
                'full_name' => $admin['full_name'],
                'email' => $admin['email'],
                'role' => $admin['role']
            ];
            set_flash('success', 'Chào mừng Chủ Tịch / Giám Đốc trở lại Hệ Thống Quản Trị HieuMini!');
            header("Location: " . BASE_URL . "/admin/index.php");
            exit;
        } else {
            $error = 'Email hoặc Mật khẩu Quản trị không chính xác.';
        }
    } else {
        $error = 'Vui lòng điền đầy đủ Email và Mật khẩu.';
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cổng Quản Trị CEO | <?= SITE_NAME ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Montserrat:wght@400;500;600;700;800;900&family=Outfit:wght@400;500;600;700;800;900&family=Plus+Jakarta+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= BASE_URL ?>/assets/css/style.css">
</head>
<body style="min-height: 100vh; display: flex; align-items: center; justify-content: center; background: radial-gradient(circle at center, #181d26 0%, #0a0c10 100%);">

    <div style="width: 100%; max-width: 440px; padding: 1.5rem;">
        <div class="category-card" style="padding: 3rem 2.5rem; border-color: var(--border-gold); box-shadow: var(--shadow-gold);">
            <!-- Brand -->
            <div style="display: flex; align-items: center; justify-content: center; gap: 0.8rem; margin-bottom: 2rem;">
                <div class="logo-icon">HM</div>
                <div class="logo-text" style="text-align: left;">
                    <span class="brand-name">HIEUMINI</span>
                    <span class="brand-tagline">EXECUTIVE PORTAL</span>
                </div>
            </div>

            <h2 style="font-size: 1.5rem; font-weight: 800; color: #fff; margin-bottom: 0.5rem; text-align: center;">ĐĂNG NHẬP QUẢN TRỊ</h2>
            <p style="color: var(--text-secondary); font-size: 0.85rem; text-align: center; margin-bottom: 2rem;">
                Dành riêng cho Ban Giám Đốc và Quản lý Hệ thống HieuMini
            </p>

            <?php if ($error): ?>
                <div style="background: rgba(239,68,68,0.15); border: 1px solid var(--ruby-accent); border-radius: 4px; padding: 0.85rem; margin-bottom: 1.5rem; color: #fca5a5; font-size: 0.85rem;">
                    <i class="fas fa-exclamation-circle"></i> <?= htmlspecialchars($error) ?>
                </div>
            <?php endif; ?>

            <form action="<?= BASE_URL ?>/admin/login.php" method="POST">
                <div class="form-group" style="text-align: left;">
                    <label>Tài Khoản Email Admin</label>
                    <input type="email" name="email" class="form-control" required placeholder="admin@hieumini.com" value="admin@hieumini.com">
                </div>

                <div class="form-group" style="text-align: left; margin-bottom: 1.5rem;">
                    <label>Mật Khẩu Quản Trị</label>
                    <input type="password" name="password" class="form-control" required placeholder="••••••••" value="Admin@123">
                </div>

                <button type="submit" class="btn btn-primary btn-block btn-shimmer" style="padding: 1rem;">
                    <i class="fas fa-lock-open"></i> ĐĂNG NHẬP HỆ THỐNG
                </button>
            </form>

            <div style="margin-top: 2rem; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem; text-align: center; font-size: 0.8rem; color: var(--text-muted);">
                <p>Tài khoản mẫu: <strong style="color: var(--gold-light);">admin@hieumini.com</strong> / <strong style="color: var(--gold-light);">Admin@123</strong></p>
                <a href="<?= BASE_URL ?>/index.php" style="color: var(--text-secondary); display: inline-block; margin-top: 0.5rem;"><i class="fas fa-arrow-left"></i> Quay lại Website</a>
            </div>
        </div>
    </div>

</body>
</html>
