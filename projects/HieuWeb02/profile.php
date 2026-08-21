<?php
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/includes/functions.php';
require_once __DIR__ . '/includes/auth_check.php';

require_login();

$user = current_user();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = sanitize($_POST['full_name'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $address = sanitize($_POST['address'] ?? '');
    $new_password = $_POST['new_password'] ?? '';

    if (empty($full_name)) {
        set_flash('danger', 'Họ tên không được để trống!');
    } else {
        if ($pdo) {
            try {
                if (!empty($new_password)) {
                    $hash = password_hash($new_password, PASSWORD_BCRYPT);
                    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ?, password = ? WHERE id = ?");
                    $stmt->execute([$full_name, $phone, $address, $hash, $user['id']]);
                } else {
                    $stmt = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
                    $stmt->execute([$full_name, $phone, $address, $user['id']]);
                }
            } catch (Exception $e) {}
        }

        // Cập nhật lại session
        $_SESSION['user']['full_name'] = $full_name;
        $_SESSION['user']['phone'] = $phone;
        $_SESSION['user']['address'] = $address;

        set_flash('success', 'Cập nhật thông tin tài khoản thành công!');
        header("Location: profile.php");
        exit;
    }
}

$page_title = 'Thông Tin Tài Khoản';
require_once __DIR__ . '/includes/header.php';
?>

<main class="container" style="margin: 40px auto 70px; max-width: 650px;">
    <div class="glass-panel" style="padding: 36px 30px;">
        <div style="display: flex; align-items: center; gap: 20px; margin-bottom: 28px; border-bottom: var(--border-glass); padding-bottom: 20px;">
            <div style="width: 64px; height: 64px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--accent)); display: flex; align-items: center; justify-content: center; font-size: 1.8rem; color: #fff; font-weight: 800;">
                <?php echo strtoupper(substr($user['full_name'], 0, 1)); ?>
            </div>
            <div>
                <h1 style="font-size: 1.4rem; font-weight: 800; color: #fff;"><?php echo htmlspecialchars($user['full_name']); ?></h1>
                <p style="color: var(--text-muted); font-size: 0.85rem;"><?php echo htmlspecialchars($user['email']); ?> • <span style="color: var(--accent);"><?php echo strtoupper($user['role']); ?></span></p>
            </div>
        </div>

        <form action="profile.php" method="POST">
            <div class="form-group">
                <label>Họ và tên:</label>
                <input type="text" name="full_name" class="form-control" value="<?php echo htmlspecialchars($user['full_name']); ?>" required>
            </div>

            <div class="form-group">
                <label>Địa chỉ Email (Cố định):</label>
                <input type="email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" disabled style="opacity: 0.6; cursor: not-allowed;">
            </div>

            <div class="form-group">
                <label>Số điện thoại:</label>
                <input type="tel" name="phone" class="form-control" value="<?php echo htmlspecialchars($user['phone'] ?? ''); ?>" placeholder="Nhập số điện thoại...">
            </div>

            <div class="form-group">
                <label>Địa chỉ giao hàng mặc định:</label>
                <input type="text" name="address" class="form-control" value="<?php echo htmlspecialchars($user['address'] ?? ''); ?>" placeholder="Nhập địa chỉ nhận hàng...">
            </div>

            <div class="form-group">
                <label>Đổi mật khẩu mới (Bỏ trống nếu không đổi):</label>
                <input type="password" name="new_password" class="form-control" placeholder="Nhập mật khẩu mới...">
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 30px;">
                <button type="submit" class="btn btn-primary">
                    <i class="fa-solid fa-floppy-disk"></i> Lưu thay đổi
                </button>
                <a href="my_orders.php" class="btn btn-outline">
                    <i class="fa-solid fa-receipt"></i> Đơn hàng của tôi
                </a>
            </div>
        </form>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
