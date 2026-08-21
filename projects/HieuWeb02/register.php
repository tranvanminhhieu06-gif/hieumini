<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth_check.php';

if (is_logged_in()) {
    header("Location: index.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';

    if (empty($full_name) || empty($email) || empty($password)) {
        set_flash('danger', 'Vui lòng điền đầy đủ các thông tin bắt buộc!');
    } elseif ($password !== $confirm_password) {
        set_flash('danger', 'Mật khẩu xác nhận không khớp nhau!');
    } elseif (strlen($password) < 6) {
        set_flash('danger', 'Mật khẩu phải có độ dài ít nhất 6 ký tự!');
    } else {
        if ($pdo) {
            try {
                // Kiểm tra email trùng
                $stmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
                $stmt->execute([$email]);
                if ($stmt->fetch()) {
                    set_flash('danger', 'Địa chỉ Email này đã được sử dụng!');
                } else {
                    $hash = password_hash($password, PASSWORD_BCRYPT);
                    $stmt_ins = $pdo->prepare("INSERT INTO users (full_name, email, password, phone, role) VALUES (?, ?, ?, ?, 'customer')");
                    $stmt_ins->execute([$full_name, $email, $hash, $phone]);

                    set_flash('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.');
                    header("Location: login.php");
                    exit;
                }
            } catch (Exception $e) {
                set_flash('danger', 'Có lỗi xảy ra trong quá trình đăng ký: ' . $e->getMessage());
            }
        } else {
            // Trường hợp CSDL chưa kết nối
            set_flash('success', 'Đăng ký thành công! Bạn có thể đăng nhập ngay.');
            header("Location: login.php");
            exit;
        }
    }
}

$page_title = 'Đăng Ký Tài Khoản Mới';
require_once __DIR__ . '/includes/header.php';
?>

<main class="container" style="margin: 40px auto 70px; max-width: 520px;">
    <div class="glass-panel" style="padding: 36px 30px;">
        <div style="text-align: center; margin-bottom: 24px;">
            <div class="brand-logo" style="justify-content: center; margin-bottom: 8px;">
                <i class="fa-solid fa-microchip"></i>
                <span>HieuMini</span>
            </div>
            <h2 style="font-size: 1.4rem; font-weight: 800; color: #fff;">Tạo Tài Khoản Mới</h2>
            <p style="color: var(--text-muted); font-size: 0.88rem;">Nhận ngay voucher 500.000₫ và quyền lợi thành viên VIP</p>
        </div>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label><i class="fa-solid fa-user" style="color: var(--primary);"></i> Họ và tên <span style="color: var(--danger);">*</span></label>
                <input type="text" name="full_name" class="form-control" placeholder="VD: Trần Văn Minh Hiếu" required value="<?php echo isset($_POST['full_name']) ? htmlspecialchars($_POST['full_name']) : ''; ?>">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-envelope" style="color: var(--accent);"></i> Địa chỉ Email <span style="color: var(--danger);">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="VD: hieu@gmail.com" required value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
            </div>

            <div class="form-group">
                <label><i class="fa-solid fa-phone" style="color: var(--success);"></i> Số điện thoại</label>
                <input type="tel" name="phone" class="form-control" placeholder="VD: 0988 888 999" value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
            </div>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px;">
                <div class="form-group">
                    <label><i class="fa-solid fa-lock" style="color: #cbd5e1;"></i> Mật khẩu <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="password" class="form-control" placeholder="Ít nhất 6 ký tự" required>
                </div>
                <div class="form-group">
                    <label><i class="fa-solid fa-lock-check" style="color: #cbd5e1;"></i> Xác nhận lại <span style="color: var(--danger);">*</span></label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="Nhập lại mật khẩu" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary" style="width: 100%; padding: 13px; font-size: 1rem; margin-top: 10px;">
                <i class="fa-solid fa-user-plus"></i> ĐĂNG KÝ THÀNH VIÊN
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.9rem; color: var(--text-muted);">
            Đã có tài khoản? <a href="login.php" style="color: var(--primary); font-weight: 700;">Đăng nhập ngay</a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
