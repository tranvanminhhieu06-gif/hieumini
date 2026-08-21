<?php
/**
 * HieuMini Admin - Quản lý người dùng
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $id = (int)($_POST['id'] ?? 0);
    $action = input('action');

    if ($id === current_user_id()) {
        flash('Không thể thao tác trên chính tài khoản đang đăng nhập.', 'error');
    } elseif ($action === 'toggle') {
        $pdo->prepare('UPDATE users SET status = 1 - status WHERE id = ?')->execute([$id]);
        flash('Đã đổi trạng thái tài khoản.');
    } elseif ($action === 'role') {
        $pdo->prepare('UPDATE users SET role = IF(role = "admin", "user", "admin") WHERE id = ?')->execute([$id]);
        flash('Đã đổi quyền của tài khoản.');
    } elseif ($action === 'reset') {
        $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
            ->execute([password_hash('hieumini123', PASSWORD_BCRYPT), $id]);
        flash('Đã đặt lại mật khẩu về: hieumini123');
    }
    redirect('admin/users.php');
}

$users = $pdo->query('SELECT u.*,
        (SELECT COUNT(*) FROM orders o WHERE o.user_id = u.id) AS orders,
        (SELECT IFNULL(SUM(o.total),0) FROM orders o WHERE o.user_id = u.id AND o.status IN ("paid","delivered")) AS spent
        FROM users u ORDER BY u.created_at DESC')->fetchAll();

$adminTitle = 'Quản lý người dùng';
require __DIR__ . '/includes/header.php';
?>

<div class="table-wrap">
  <table class="data">
    <caption class="sr-only">Danh sách tài khoản người dùng</caption>
    <thead>
      <tr>
        <th scope="col">#</th><th scope="col">Họ tên</th><th scope="col">Email</th>
        <th scope="col">Điện thoại</th><th scope="col">Đơn hàng</th><th scope="col">Đã chi</th>
        <th scope="col">Quyền</th><th scope="col">Thao tác</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($users as $u): ?>
        <tr>
          <td><?= (int)$u['id'] ?></td>
          <td>
            <strong><?= e($u['full_name']) ?></strong>
            <?php if (!$u['status']): ?><span class="badge badge--off" style="margin-left:6px">Đã khoá</span><?php endif; ?>
            <div style="font-size:var(--fs-xs);color:var(--fg-muted)">Tham gia <?= e(vn_date($u['created_at'])) ?></div>
          </td>
          <td><?= e($u['email']) ?></td>
          <td><?= e((string)$u['phone'] ?: '—') ?></td>
          <td><?= (int)$u['orders'] ?></td>
          <td><?= money($u['spent']) ?></td>
          <td><span class="badge <?= $u['role'] === 'admin' ? 'badge--best' : '' ?>"><?= $u['role'] === 'admin' ? 'Quản trị' : 'Thành viên' ?></span></td>
          <td>
            <?php if ((int)$u['id'] === current_user_id()): ?>
              <span style="color:var(--fg-muted);font-size:var(--fs-xs)">Tài khoản của bạn</span>
            <?php else: ?>
              <div style="display:flex;gap:6px;justify-content:flex-end;flex-wrap:wrap">
                <form method="post"><?= csrf_field() ?>
                  <input type="hidden" name="action" value="toggle"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                  <button class="btn btn--ghost btn--sm" type="submit"><?= $u['status'] ? 'Khoá' : 'Mở khoá' ?></button>
                </form>
                <form method="post" onsubmit="return confirm('Đổi quyền tài khoản này?')"><?= csrf_field() ?>
                  <input type="hidden" name="action" value="role"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                  <button class="btn btn--ghost btn--sm" type="submit">Đổi quyền</button>
                </form>
                <form method="post" onsubmit="return confirm('Đặt lại mật khẩu về hieumini123?')"><?= csrf_field() ?>
                  <input type="hidden" name="action" value="reset"><input type="hidden" name="id" value="<?= (int)$u['id'] ?>">
                  <button class="btn btn--ghost btn--sm" type="submit">Đặt lại MK</button>
                </form>
              </div>
            <?php endif; ?>
          </td>
        </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
