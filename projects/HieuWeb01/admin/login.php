<?php
/**
 * Trang Đăng Nhập Quản Trị Viên Chuyên Dụng - HieuMini PRO CONTROL
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

if (is_logged_in() && is_admin()) {
    redirect('index.php');
}

$error = '';
$redirect = $_GET['redirect'] ?? 'index.php';

// Xử lý đăng nhập thông thường hoặc 1-Click Quick Demo Login
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $isDemoQuick = isset($_POST['quick_demo_admin']);
    
    if ($isDemoQuick) {
        $email = 'admin@hieumini.vn';
        $password = 'admin123';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
    }

    if (empty($email) || empty($password)) {
        $error = 'Vui lòng nhập đầy đủ Email và Mật khẩu Quản trị.';
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND role = 'admin'");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            set_flash('success', 'Chào mừng Quản trị viên ' . $user['full_name'] . ' trở lại hệ thống!');
            redirect($redirect);
        } else {
            $error = 'Tài khoản Quản trị không tồn tại hoặc mật khẩu không chính xác.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng Nhập Quản Trị Hệ Thống - HieuMini PRO CONTROL</title>
    <!-- Google Fonts: Outfit & Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- FontAwesome Pro/Free Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <!-- Styles -->
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        body.admin-login-body {
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            background: radial-gradient(circle at top right, #1e1b4b 0%, #0b0f19 60%, #030712 100%);
            margin: 0;
            padding: 24px;
            font-family: 'Plus Jakarta Sans', sans-serif;
            color: #ffffff;
            position: relative;
            overflow: hidden;
        }
        .login-ambient-orb {
            position: absolute;
            width: 400px;
            height: 400px;
            border-radius: 50%;
            background: radial-gradient(circle, rgba(245, 158, 11, 0.25) 0%, transparent 70%);
            top: -100px;
            right: -100px;
            pointer-events: none;
            filter: blur(40px);
        }
        .login-card {
            width: 100%;
            max-width: 440px;
            background: rgba(19, 27, 46, 0.85);
            backdrop-filter: blur(20px);
            -webkit-backdrop-filter: blur(20px);
            border: 1px solid rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            padding: 40px;
            box-shadow: 0 25px 50px -12px rgba(0, 0, 0, 0.6), 0 0 0 1px rgba(245, 158, 11, 0.15);
            position: relative;
            z-index: 10;
        }
        .login-brand {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
            margin-bottom: 24px;
        }
        .login-brand-icon {
            width: 48px;
            height: 48px;
            border-radius: 14px;
            background: var(--admin-accent-gradient);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: #0f172a;
            box-shadow: 0 0 20px rgba(245, 158, 11, 0.4);
        }
        .login-title {
            font-family: 'Outfit', sans-serif;
            font-size: 1.6rem;
            font-weight: 800;
            text-align: center;
            margin: 0 0 6px 0;
            letter-spacing: -0.5px;
        }
        .login-subtitle {
            text-align: center;
            font-size: 0.85rem;
            color: #94a3b8;
            margin-bottom: 28px;
        }
        .admin-form-input {
            width: 100%;
            padding: 12px 16px;
            background: rgba(11, 15, 25, 0.8);
            border: 1.5px solid rgba(255, 255, 255, 0.12);
            border-radius: 10px;
            color: #ffffff;
            font-size: 0.9rem;
            box-sizing: border-box;
            transition: all 0.25s ease;
        }
        .admin-form-input:focus {
            outline: none;
            border-color: var(--admin-accent);
            box-shadow: 0 0 0 4px rgba(245, 158, 11, 0.2);
            background: rgba(11, 15, 25, 0.95);
        }
        .btn-admin-submit {
            width: 100%;
            padding: 13px;
            background: var(--admin-accent-gradient);
            color: #0f172a;
            font-weight: 800;
            font-size: 0.95rem;
            border: none;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            transition: all 0.25s ease;
            box-shadow: 0 4px 15px rgba(245, 158, 11, 0.35);
        }
        .btn-admin-submit:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(245, 158, 11, 0.45);
        }
        .btn-quick-demo {
            width: 100%;
            padding: 11px;
            background: rgba(255, 255, 255, 0.08);
            border: 1px dashed rgba(245, 158, 11, 0.5);
            color: #fbbf24;
            font-weight: 700;
            font-size: 0.88rem;
            border-radius: 10px;
            cursor: pointer;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            margin-top: 14px;
            transition: all 0.2s ease;
        }
        .btn-quick-demo:hover {
            background: rgba(245, 158, 11, 0.15);
            border-color: #fbbf24;
        }
        .alert-admin-danger {
            background: rgba(239, 68, 68, 0.15);
            border: 1px solid rgba(239, 68, 68, 0.4);
            color: #fca5a5;
            padding: 10px 14px;
            border-radius: 8px;
            font-size: 0.85rem;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }
    </style>
</head>
<body class="admin-login-body">
    <div class="login-ambient-orb"></div>

    <div class="login-card">
        <div class="login-brand">
            <div class="login-brand-icon">
                <i class="fa-solid fa-cube"></i>
            </div>
            <div>
                <div style="font-family: 'Outfit', sans-serif; font-weight: 800; font-size: 1.3rem; line-height: 1;">HieuMini</div>
                <div style="font-size: 0.7rem; color: #fbbf24; font-weight: 800; letter-spacing: 1px; margin-top: 2px;">PRO CONTROL PANEL</div>
            </div>
        </div>

        <h1 class="login-title">Đăng Nhập Quản Trị</h1>
        <p class="login-subtitle">Hệ thống điều hành thương mại thời trang may mặc cao cấp</p>

        <?php if (!empty($error)): ?>
            <div class="alert-admin-danger">
                <i class="fa-solid fa-circle-exclamation"></i>
                <span><?= htmlspecialchars($error) ?></span>
            </div>
        <?php endif; ?>

        <form action="login.php?redirect=<?= urlencode($redirect) ?>" method="POST">
            <div style="margin-bottom: 16px;">
                <label style="display: block; font-size: 0.8rem; font-weight: 700; color: #cbd5e1; margin-bottom: 6px;">Email Quản Trị Viên</label>
                <input type="email" name="email" class="admin-form-input" value="admin@hieumini.vn" required placeholder="admin@hieumini.vn">
            </div>

            <div style="margin-bottom: 24px;">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label style="font-size: 0.8rem; font-weight: 700; color: #cbd5e1;">Mật Khẩu</label>
                    <span style="font-size: 0.75rem; color: #94a3b8;">Mặc định: <code>admin123</code></span>
                </div>
                <input type="password" name="password" class="admin-form-input" value="admin123" required placeholder="••••••••">
            </div>

            <button type="submit" class="btn-admin-submit">
                <i class="fa-solid fa-shield-halved"></i> Đăng Nhập Quản Trị
            </button>
        </form>

        <!-- 1-Click Fast Login for Testing/Demo -->
        <form action="login.php?redirect=<?= urlencode($redirect) ?>" method="POST">
            <input type="hidden" name="quick_demo_admin" value="1">
            <button type="submit" class="btn-quick-demo" title="Bấm để đăng nhập ngay mà không cần nhập mật khẩu">
                <i class="fa-solid fa-bolt"></i> 1-Click Đăng Nhập Nhanh (Super Admin Demo)
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.82rem; border-top: 1px solid rgba(255, 255, 255, 0.08); padding-top: 18px;">
            <a href="../index.php" style="color: #94a3b8; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; transition: color 0.2s;">
                <i class="fa-solid fa-arrow-left"></i> Quay lại website bán hàng HieuMini
            </a>
        </div>
    </div>
</body>
</html>
