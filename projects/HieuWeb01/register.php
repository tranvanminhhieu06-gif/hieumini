<?php
/**
 * Trang Đăng Ký Tài Khoản HieuMini
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (is_logged_in()) {
    redirect('index.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullname = trim($_POST['fullname'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirmPassword = $_POST['confirm_password'] ?? '';

    $errors = [];
    if (empty($fullname)) $errors[] = "Vui lòng nhập họ và tên.";
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Email không hợp lệ.";
    if (strlen($password) < 6) $errors[] = "Mật khẩu phải từ 6 ký tự trở lên.";
    if ($password !== $confirmPassword) $errors[] = "Mật khẩu xác nhận không khớp.";

    // Kiểm tra trùng email
    $checkStmt = $pdo->prepare("SELECT id FROM users WHERE email = ?");
    $checkStmt->execute([$email]);
    if ($checkStmt->fetch()) {
        $errors[] = "Email này đã được đăng ký trên hệ thống.";
    }

    if (empty($errors)) {
        $hashedPass = password_hash($password, PASSWORD_DEFAULT);
        $ins = $pdo->prepare("INSERT INTO users (full_name, email, password, phone, role) VALUES (?, ?, ?, ?, 'customer')");
        $ins->execute([$fullname, $email, $hashedPass, $phone]);

        set_flash('success', 'Đăng ký tài khoản thành công! Vui lòng đăng nhập.');
        redirect('login.php');
    } else {
        set_flash('danger', implode('<br>', $errors));
    }
}

$pageTitle = "Đăng Ký Tài Khoản Mới";
require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding: 40px 20px 80px;">
    <div style="max-width: 480px; margin: 0 auto; background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 36px; box-shadow: var(--shadow-md);">
        <div style="text-align: center; margin-bottom: 24px;">
            <h2 style="font-size: 1.6rem; color: var(--primary);">Tạo Tài Khoản Mới</h2>
            <p style="color: var(--text-muted); font-size: 0.875rem; margin-top: 4px;">Nhận ngay voucher giảm 50K cho đơn hàng đầu tiên</p>
        </div>

        <form action="register.php" method="POST">
            <div class="form-group">
                <label class="form-label">Họ và tên của bạn <span class="text-danger">*</span></label>
                <input type="text" name="fullname" class="form-control" placeholder="Nguyễn Văn A" required>
            </div>

            <div class="form-group">
                <label class="form-label">Địa chỉ Email <span class="text-danger">*</span></label>
                <input type="email" name="email" class="form-control" placeholder="email@gmail.com" required>
            </div>

            <div class="form-group">
                <label class="form-label">Số điện thoại</label>
                <input type="tel" name="phone" class="form-control" placeholder="0988xxxxxx">
            </div>

            <div class="form-group">
                <label class="form-label">Mật khẩu (tối thiểu 6 ký tự) <span class="text-danger">*</span></label>
                <input type="password" name="password" class="form-control" placeholder="••••••••" required>
            </div>

            <div class="form-group">
                <label class="form-label">Xác nhận lại mật khẩu <span class="text-danger">*</span></label>
                <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
            </div>

            <button type="submit" class="btn btn-accent btn-lg btn-block" style="margin-top: 10px;">
                <i class="fa-solid fa-user-plus"></i> Đăng Ký Tài Khoản
            </button>
        </form>

        <div style="text-align: center; margin-top: 24px; font-size: 0.875rem; color: var(--text-muted);">
            Đã có tài khoản? <a href="login.php" style="color: var(--accent); font-weight: 700;">Đăng nhập ngay</a>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
