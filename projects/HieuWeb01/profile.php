<?php
/**
 * Trang Hồ Sơ Cá Nhân HieuMini
 */
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

if (!is_logged_in()) {
    redirect('login.php');
}

$user = current_user($pdo);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_profile') {
        $fullname = trim($_POST['fullname'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $address = trim($_POST['address'] ?? '');

        if (!empty($fullname)) {
            $upd = $pdo->prepare("UPDATE users SET full_name = ?, phone = ?, address = ? WHERE id = ?");
            $upd->execute([$fullname, $phone, $address, $user['id']]);
            set_flash('success', 'Cập nhật thông tin tài khoản thành công!');
            redirect('profile.php');
        } else {
            set_flash('danger', 'Họ và tên không được để trống.');
        }
    }

    if ($action === 'change_password') {
        $oldPass = $_POST['old_password'] ?? '';
        $newPass = $_POST['new_password'] ?? '';
        $confirmPass = $_POST['confirm_password'] ?? '';

        $stmt = $pdo->prepare("SELECT password FROM users WHERE id = ?");
        $stmt->execute([$user['id']]);
        $curr = $stmt->fetch();

        if (password_verify($oldPass, $curr['password'])) {
            if (strlen($newPass) >= 6 && $newPass === $confirmPass) {
                $hash = password_hash($newPass, PASSWORD_DEFAULT);
                $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$hash, $user['id']]);
                set_flash('success', 'Đổi mật khẩu thành công!');
                redirect('profile.php');
            } else {
                set_flash('danger', 'Mật khẩu mới phải từ 6 ký tự và khớp nhau.');
            }
        } else {
            set_flash('danger', 'Mật khẩu hiện tại không chính xác.');
        }
    }
}

$pageTitle = "Hồ Sơ Cá Nhân";
require_once __DIR__ . '/includes/header.php';
?>

<div class="page-header-banner">
    <div class="container">
        <h1>Tài Khoản Của Tôi</h1>
        <div class="breadcrumbs">
            <a href="index.php">Trang Chủ</a> / <span>Hồ Sơ Cá Nhân</span>
        </div>
    </div>
</div>

<div class="container" style="margin-bottom: 60px;">
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 30px;">
        <!-- Form Thông tin cá nhân -->
        <div style="background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 30px; box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 1.25rem; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                <i class="fa-solid fa-user-pen text-accent"></i> Cập Nhật Thông Tin Cá Nhân
            </h3>

            <form action="profile.php" method="POST">
                <input type="hidden" name="action" value="update_profile">

                <div class="form-group">
                    <label class="form-label">Email (Không thể thay đổi)</label>
                    <input type="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" readonly style="background: #f1f5f9; cursor: not-allowed;">
                </div>

                <div class="form-group">
                    <label class="form-label">Họ và tên <span class="text-danger">*</span></label>
                    <input type="text" name="fullname" class="form-control" value="<?= htmlspecialchars($user['full_name']) ?>" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Số điện thoại</label>
                    <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>" placeholder="0988xxxxxx">
                </div>

                <div class="form-group">
                    <label class="form-label">Địa chỉ mặc định</label>
                    <textarea name="address" rows="3" class="form-control" placeholder="Số 18 Duy Tân, Cầu Giấy, Hà Nội"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="btn btn-primary">Lưu Thay Đổi</button>
            </form>
        </div>

        <!-- Form Đổi mật khẩu -->
        <div style="background: #fff; border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 30px; box-shadow: var(--shadow-sm);">
            <h3 style="font-size: 1.25rem; margin-bottom: 20px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
                <i class="fa-solid fa-key text-accent"></i> Đổi Mật Khẩu
            </h3>

            <form action="profile.php" method="POST">
                <input type="hidden" name="action" value="change_password">

                <div class="form-group">
                    <label class="form-label">Mật khẩu hiện tại</label>
                    <input type="password" name="old_password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Mật khẩu mới (từ 6 ký tự)</label>
                    <input type="password" name="new_password" class="form-control" placeholder="••••••••" required>
                </div>

                <div class="form-group">
                    <label class="form-label">Xác nhận mật khẩu mới</label>
                    <input type="password" name="confirm_password" class="form-control" placeholder="••••••••" required>
                </div>

                <button type="submit" class="btn btn-accent">Đổi Mật Khẩu</button>
            </form>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
