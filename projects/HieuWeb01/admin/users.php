<?php
/**
 * Quản Lý Người Dùng & Phân Quyền - Admin HieuMini
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Thay đổi phân quyền
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'change_role') {
    $userId = (int)$_POST['user_id'];
    $newRole = $_POST['role'];
    
    // Tránh tự hạ quyền chính mình nếu là tài khoản đang đăng nhập
    if ($userId === (int)$_SESSION['user_id'] && $newRole !== 'admin') {
        set_flash('danger', 'Bạn không thể tự hạ quyền của chính mình.');
    } else {
        $pdo->prepare("UPDATE users SET role = ? WHERE id = ?")->execute([$newRole, $userId]);
        set_flash('success', 'Đã cập nhật phân quyền người dùng!');
    }
    redirect('users.php');
}

$users = $pdo->query("SELECT * FROM users ORDER BY id ASC")->fetchAll();

$adminTitle = "Quản Lý Khách Hàng & Phân Quyền";
require_once __DIR__ . '/includes/header.php';
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
    <div>
        <h2 style="font-size: 1.4rem; color: var(--primary); margin-bottom: 4px;">Danh Sách Người Dùng (<?= count($users) ?>)</h2>
        <p style="font-size: 0.85rem; color: var(--text-muted);">Quản lý tài khoản khách hàng và phân quyền Quản trị viên</p>
    </div>
</div>

<div class="admin-card">
    <div class="admin-card-body" style="padding: 0;">
        <table class="admin-table">
            <thead>
                <tr>
                    <th style="width: 50px;">ID</th>
                    <th>Họ và tên</th>
                    <th>Email</th>
                    <th>Số điện thoại</th>
                    <th>Địa chỉ</th>
                    <th>Vai trò</th>
                    <th>Ngày tạo</th>
                    <th style="text-align: center;">Thao tác phân quyền</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $u): ?>
                    <tr>
                        <td>#<?= $u['id'] ?></td>
                        <td><strong><?= htmlspecialchars($u['full_name']) ?></strong></td>
                        <td><?= htmlspecialchars($u['email']) ?></td>
                        <td><?= htmlspecialchars($u['phone'] ?: 'Chưa có') ?></td>
                        <td>
                            <div style="max-width: 200px; white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                <?= htmlspecialchars($u['address'] ?: 'Chưa có') ?>
                            </div>
                        </td>
                        <td>
                            <span class="badge <?= $u['role'] === 'admin' ? 'badge-danger' : 'badge-primary' ?>">
                                <?= $u['role'] === 'admin' ? 'Quản Trị Viên (Admin)' : 'Khách Hàng' ?>
                            </span>
                        </td>
                        <td><?= format_datetime($u['created_at']) ?></td>
                        <td style="text-align: center;">
                            <form action="users.php" method="POST" style="margin: 0; display: inline-flex; gap: 6px;">
                                <input type="hidden" name="action" value="change_role">
                                <input type="hidden" name="user_id" value="<?= $u['id'] ?>">
                                <select name="role" onchange="this.form.submit()" style="font-size: 0.8rem; padding: 4px 8px; border-radius: 4px; border: 1px solid var(--border); background: #fff;">
                                    <option value="customer" <?= $u['role'] === 'customer' ? 'selected' : '' ?>>Khách hàng</option>
                                    <option value="admin" <?= $u['role'] === 'admin' ? 'selected' : '' ?>>Quản trị viên</option>
                                </select>
                            </form>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
