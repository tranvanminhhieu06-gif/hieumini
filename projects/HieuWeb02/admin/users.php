<?php
require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth_check.php';

require_admin();

// Đổi vai trò role
if (isset($_GET['toggle_role'])) {
    $uid = (int)$_GET['toggle_role'];
    if ($pdo && $uid != current_user()['id']) {
        try {
            $stmt = $pdo->prepare("UPDATE users SET role = IF(role = 'admin', 'customer', 'admin') WHERE id = ?");
            $stmt->execute([$uid]);
            set_flash('success', 'Đã thay đổi quyền tài khoản!');
        } catch (Exception $e) {}
    }
    header("Location: users.php");
    exit;
}

$users = [];
if ($pdo) {
    try {
        $stmt = $pdo->query("SELECT * FROM users ORDER BY id ASC");
        $users = $stmt->fetchAll();
    } catch (Exception $e) {}
}

if (empty($users)) {
    $users = [
        ['id' => 1, 'full_name' => 'Trần Văn Minh Hiếu', 'email' => 'admin@hieumini.vn', 'phone' => '0988889999', 'role' => 'admin', 'status' => 1, 'created_at' => date('Y-m-d')],
        ['id' => 2, 'full_name' => 'Nguyễn Hoàng Nam', 'email' => 'nam.nguyen@gmail.com', 'phone' => '0912345678', 'role' => 'customer', 'status' => 1, 'created_at' => date('Y-m-d')],
        ['id' => 3, 'full_name' => 'Lê Thị Thu Thảo', 'email' => 'thao.le@gmail.com', 'phone' => '0934567890', 'role' => 'customer', 'status' => 1, 'created_at' => date('Y-m-d')]
    ];
}
?>
<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quản Lý Người Dùng - HieuMini Admin</title>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
</head>
<body class="admin-body">

    <?php require_once __DIR__ . '/sidebar.php'; ?>

    <div class="admin-main">
        <header class="admin-header">
            <h2 style="font-size: 1.3rem; font-weight: 800; color: #fff;">Quản Lý Người Dùng & Phân Quyền</h2>
        </header>

        <main class="admin-content">
            <?php echo display_flash(); ?>

            <div class="admin-card">
                <div class="admin-card-header">
                    <div class="admin-card-title">
                        <i class="fa-solid fa-users" style="color: var(--accent);"></i> Danh Sách Thành Viên (<?php echo count($users); ?>)
                    </div>
                </div>

                <div class="table-responsive">
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>ID</th>
                                <th>Họ và tên</th>
                                <th>Email</th>
                                <th>Số điện thoại</th>
                                <th>Vai trò</th>
                                <th>Trạng thái</th>
                                <th>Thao tác</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($users as $u): ?>
                                <tr>
                                    <td><strong>#<?php echo $u['id']; ?></strong></td>
                                    <td><strong style="color: #fff;"><?php echo htmlspecialchars($u['full_name']); ?></strong></td>
                                    <td><span style="color: var(--accent);"><?php echo htmlspecialchars($u['email']); ?></span></td>
                                    <td><?php echo htmlspecialchars($u['phone'] ?? 'Chưa cập nhật'); ?></td>
                                    <td>
                                        <span class="badge <?php echo $u['role'] === 'admin' ? 'badge-primary' : 'badge-info'; ?>">
                                            <?php echo strtoupper($u['role']); ?>
                                        </span>
                                    </td>
                                    <td><span class="badge badge-success"><i class="fa-solid fa-circle-check"></i> Hoạt động</span></td>
                                    <td>
                                        <?php if ($u['id'] != current_user()['id']): ?>
                                            <a href="users.php?toggle_role=<?php echo $u['id']; ?>" class="btn btn-outline btn-sm" style="font-size: 0.78rem; padding: 4px 8px;" onclick="return confirm('Bạn có chắc muốn đổi quyền tài khoản này?');">
                                                <i class="fa-solid fa-arrows-rotate"></i> Chuyển vai trò
                                            </a>
                                        <?php else: ?>
                                            <span style="font-size: 0.8rem; color: var(--text-muted);">Đang đăng nhập</span>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </div>

    <script src="../assets/js/admin.js"></script>
</body>
</html>
