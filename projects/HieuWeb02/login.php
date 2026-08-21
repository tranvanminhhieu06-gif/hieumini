<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth_check.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = sanitize($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        set_flash('danger', 'Vui lòng nhập đầy đủ Email và Mật khẩu!');
    } else {
        $user = null;
        if ($pdo) {
            try {
                $stmt = $pdo->prepare("SELECT * FROM users WHERE email = ? AND status = 1");
                $stmt->execute([$email]);
                $user = $stmt->fetch();
            } catch (Exception $e) {}
        }

        // Kiểm tra mật khẩu (Hỗ trợ cả CSDL và tài khoản demo)
        $is_valid = false;
        if ($user && password_verify($password, $user['password'])) {
            $is_valid = true;
        } elseif ($email === 'admin@hieumini.vn' && $password === 'admin123') {
            // Demo Admin
            $user = [
                'id' => 1,
                'full_name' => 'Trần Văn Minh Hiếu (Admin)',
                'email' => 'admin@hieumini.vn',
                'phone' => '0988889999',
                'address' => 'Hà Nội',
                'role' => 'admin'
            ];
            $is_valid = true;
        } elseif ($email === 'user@gmail.com' && $password === 'user123') {
            // Demo Customer
            $user = [
                'id' => 2,
                'full_name' => 'Khách Hàng Thân Thiết',
                'email' => 'user@gmail.com',
                'phone' => '0912345678',
                'address' => 'Hà Nội',
                'role' => 'customer'
            ];
            $is_valid = true;
        }

        if ($is_valid) {
            $_SESSION['user'] = [
                'id' => $user['id'],
                'full_name' => $user['full_name'],
                'email' => $user['email'],
                'phone' => $user['phone'] ?? '',
                'address' => $user['address'] ?? '',
                'role' => $user['role'] ?? 'customer'
            ];

            set_flash('success', 'Đăng nhập thành công! Chào mừng ' . $user['full_name']);
            if ($user['role'] === 'admin') {
                header("Location: admin/index.php");
            } else {
                $redirect = isset($_SESSION['redirect_after_login']) ? $_SESSION['redirect_after_login'] : 'index.php';
                unset($_SESSION['redirect_after_login']);
                header("Location: " . $redirect);
            }
            exit;
        } else {
            set_flash('danger', 'Email hoặc mật khẩu không chính xác!');
        }
    }
}

$page_title = 'Đăng Nhập Tài Khoản';
require_once __DIR__ . '/includes/header.php';
?>

<main class="container" style="margin: 50px auto 80px; max-width: 480px;">
    <div class="glass-panel" style="padding: 36px 30px;">
        <div style="text-align: center; margin-bottom: 28px;">
            <div class="brand-logo" style="justify-content: center; margin-bottom: 8px;">
                <i class="fa-solid fa-microchip"></i>
                <span>HieuMini</span>
            </div>
            <h2 style="font-size: 1.4rem; font-weight: 800; color: #fff;">Đăng Nhập Hệ Thống</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Truy cập tài khoản để theo dõi đơn hàng và ưu đãi</p>
        </div>

        <form action="login.php" method="POST">
            <div class="form-group">
                <label><i class="fa-solid fa-envelope" style="color: var(--primary);"></i> Địa chỉ Email:</label>
                <input type="email" name="email" class="form-control" placeholder="VD: admin@hieumini.vn" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 6px;">
                    <label style="margin-bottom: 0;"><i class="fa-solid fa-lock" style="color: var(--accent);"></i> Mật khẩu:</label>
                    <a href="#" style="font-size: 0.8rem; color: var(--accent);">Quên mật khẩu?</a>
                </div>
                <input type="password" name="password" class="form-control" placeholder="Nhập mật khẩu..." required>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; font-size: 1rem; margin-top: 10px;">
                <i class="fa-solid fa-right-to-bracket"></i> ĐĂNG NHẬP
            </button>
        </form>

        <div style="background: rgba(255,255,255,0.03); border: var(--border-glass); border-radius: var(--radius-sm); padding: 14px; margin-top: 20px; font-size: 0.82rem; color: var(--text-muted);">
            <div style="font-weight: 700; color: var(--accent); margin-bottom: 4px;"><i class="fa-solid fa-key"></i> Tài khoản Demo:</div>
            <div>• Admin: <code>admin@hieumini.vn</code> | Pass: <code>admin123</code></div>
            <div>• Khách: <code>user@gmail.com</code> | Pass: <code>user123</code></div>
        </div>

        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-muted);">
            Chưa có tài khoản? <a href="register.php" style="color: var(--primary); font-weight: 700;">Đăng ký ngay</a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
