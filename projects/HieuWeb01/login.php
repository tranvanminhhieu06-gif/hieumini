<?php
/**
 * Trang Đăng Nhập HieuMini
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

$redirect = $_GET['redirect'] ?? 'index.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['quick_demo_admin'])) {
        $email = 'admin@hieumini.vn';
        $password = 'admin123';
    } elseif (isset($_POST['quick_demo_customer'])) {
        $email = 'khachhang@gmail.com';
        $password = 'admin123';
    } else {
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
    }

    if (empty($email) || empty($password)) {
        set_flash('danger', 'Vui lòng nhập đầy đủ Email và Mật khẩu.');
    } else {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ?");
        $stmt->execute([$email]);
        $user = $stmt->fetch();

        if ($user && password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['user_name'] = $user['full_name'];
            $_SESSION['user_email'] = $user['email'];
            $_SESSION['user_role'] = $user['role'];

            set_flash('success', 'Đăng nhập thành công! Chào mừng ' . $user['full_name']);

            if ($user['role'] === 'admin') {
                redirect('admin/index.php');
            } else {
                redirect($redirect);
            }
        } else {
            set_flash('danger', 'Email hoặc mật khẩu không chính xác.');
        }
    }
}

$pageTitle = "Đăng Nhập Tài Khoản";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 40px 20px 80px;">
    <div style="max-width: 460px; margin: 0 auto; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 36px; box-shadow: var(--shadow-md);">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="font-size: 1.6rem; color: var(--primary);">Đăng Nhập HieuMini</h2>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 4px;">Chào mừng bạn quay trở lại với thời trang HieuMini</p>
        </div>

        <!-- 1-Click Fast Login Demo Box -->
        <div style="background: #f8fafc; border: 1.5px dashed #cbd5e1; padding: 14px; border-radius: var(--radius-md); margin-bottom: 24px;">
            <div style="font-size: 0.78rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; margin-bottom: 8px; display: flex; align-items: center; gap: 6px;">
                <i class="fa-solid fa-bolt text-accent"></i> Đăng Nhập Nhanh 1-Click (Thử Nghiệm):
            </div>
            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 8px;">
                <form action="login.php?redirect=<?= urlencode($redirect) ?>" method="POST" style="margin:0;">
                    <input type="hidden" name="quick_demo_admin" value="1">
                    <button type="submit" class="btn btn-sm btn-accent" style="width: 100%; font-size: 0.78rem; padding: 7px; justify-content: center;">
                        <i class="fa-solid fa-shield-halved"></i> Super Admin
                    </button>
                </form>
                <form action="login.php?redirect=<?= urlencode($redirect) ?>" method="POST" style="margin:0;">
                    <input type="hidden" name="quick_demo_customer" value="1">
                    <button type="submit" class="btn btn-sm btn-outline" style="width: 100%; font-size: 0.78rem; padding: 7px; justify-content: center;">
                        <i class="fa-solid fa-user"></i> Khách Hàng VIP
                    </button>
                </form>
            </div>
        </div>

        <form action="login.php?redirect=<?= urlencode($redirect) ?>" method="POST">
            <div class="form-group">
                <label class="form-label">Địa chỉ Email</label>
                <input type="email" name="email" class="form-control" placeholder="admin@hieumini.vn" value="admin@hieumini.vn" required>
            </div>

            <div class="form-group">
                <label class="form-label">Mật khẩu</label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" value="admin123" required>
            </div>

            <button type="submit" class="btn btn-accent btn-lg btn-block" style="margin-top: 10px;">
                <i class="fa-solid fa-right-to-bracket"></i> Đăng Nhập Hệ Thống
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.875rem; color: var(--text-muted); display: flex; justify-content: space-between; align-items: center; border-top: 1px solid var(--border); padding-top: 16px;">
            <a href="admin/login.php" style="color: var(--accent); font-weight: 700; font-size: 0.8rem;"><i class="fa-solid fa-shield-halved mr-1"></i> Cổng Admin riêng</a>
            <span>Chưa có tài khoản? <a href="register.php" style="color: var(--primary); font-weight: 700;">Đăng ký</a></span>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
