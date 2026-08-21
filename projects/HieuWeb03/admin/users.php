<?php
// admin/users.php - Manage Users
$page_title = "Quản Lý Khách Hàng & Thành Viên";
require_once __DIR__ . '/includes/header.php';

$users = $pdo->query("SELECT * FROM users ORDER BY id DESC")->fetchAll();
?>

<div class="data-table-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--dark);">Danh Sách Tài Khoản Người Dùng (<?= count($users) ?>)</h3>
  </div>

  <div style="overflow-x: auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Họ Và Tên</th>
          <th>Email</th>
          <th>Số Điện Thoại</th>
          <th>Địa Chỉ</th>
          <th>Vai Trò</th>
          <th>Ngày Đăng Ký</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($users as $u): ?>
        <tr>
          <td style="font-weight: 700; color: var(--muted);">#<?= $u['id'] ?></td>
          <td>
            <div style="font-weight: 700; color: var(--dark);"><?= htmlspecialchars($u['fullname']) ?></div>
          </td>
          <td><?= htmlspecialchars($u['email']) ?></td>
          <td><?= htmlspecialchars($u['phone'] ?? 'Chưa cập nhật') ?></td>
          <td style="max-width: 250px; font-size: 0.85rem; color: var(--muted);"><?= htmlspecialchars($u['address'] ?? 'Chưa có') ?></td>
          <td>
            <span class="badge-tag <?= $u['role'] === 'admin' ? 'badge-hot' : 'badge-new' ?>">
              <?= $u['role'] === 'admin' ? 'Quản Trị Viên' : 'Khách Hàng' ?>
            </span>
          </td>
          <td style="color: var(--muted); font-size: 0.85rem;"><?= date('d/m/Y', strtotime($u['created_at'])) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
