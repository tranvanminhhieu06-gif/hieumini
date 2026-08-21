<?php
/**
 * HieuMini - Trang tài khoản thành viên
 */
require_once __DIR__ . '/includes/config.php';
require_login();

$uid = current_user_id();
$tab = (string)($_GET['tab'] ?? 'profile');

// ---------- Cập nhật hồ sơ ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $action = input('action');

    if ($action === 'profile') {
        $name  = input('full_name');
        $phone = preg_replace('/\s+/', '', input('phone')) ?? '';
        if (mb_strlen($name, 'UTF-8') < 2) {
            flash('Họ tên phải có ít nhất 2 ký tự.', 'error');
        } elseif ($phone !== '' && !preg_match('/^0\d{9}$/', $phone)) {
            flash('Số điện thoại chưa hợp lệ.', 'error');
        } else {
            $pdo->prepare('UPDATE users SET full_name = ?, phone = ? WHERE id = ?')
                ->execute([$name, $phone ?: null, $uid]);
            $_SESSION['user_name'] = $name;
            flash('Đã cập nhật thông tin cá nhân.');
        }
        redirect('account.php');
    }

    if ($action === 'password') {
        $old = (string)($_POST['old_password'] ?? '');
        $new = (string)($_POST['new_password'] ?? '');
        $stmt = $pdo->prepare('SELECT password FROM users WHERE id = ?');
        $stmt->execute([$uid]);
        $hash = (string)$stmt->fetchColumn();

        if (!password_verify($old, $hash)) {
            flash('Mật khẩu hiện tại không đúng.', 'error');
        } elseif (strlen($new) < 6) {
            flash('Mật khẩu mới phải có ít nhất 6 ký tự.', 'error');
        } else {
            $pdo->prepare('UPDATE users SET password = ? WHERE id = ?')
                ->execute([password_hash($new, PASSWORD_BCRYPT), $uid]);
            flash('Đã đổi mật khẩu thành công.');
        }
        redirect('account.php?tab=password');
    }
}

// ---------- Dữ liệu ----------
$user = $pdo->prepare('SELECT * FROM users WHERE id = ?');
$user->execute([$uid]);
$user = $user->fetch();

$orders = $pdo->prepare('SELECT * FROM orders WHERE user_id = ? ORDER BY created_at DESC');
$orders->execute([$uid]);
$orders = $orders->fetchAll();

$orderItems = [];
if ($orders) {
    $ids = array_column($orders, 'id');
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $st  = $pdo->prepare("SELECT oi.*, p.slug FROM order_items oi
                          LEFT JOIN projects p ON p.id = oi.project_id
                          WHERE oi.order_id IN ($in)");
    $st->execute($ids);
    foreach ($st->fetchAll() as $it) {
        $orderItems[$it['order_id']][] = $it;
    }
}

$wishes = $pdo->prepare('SELECT p.*, c.name AS category_name FROM wishlists w
                         JOIN projects p ON p.id = w.project_id
                         JOIN categories c ON c.id = p.category_id
                         WHERE w.user_id = ? ORDER BY w.created_at DESC');
$wishes->execute([$uid]);
$wishes = $wishes->fetchAll();

$spent = array_sum(array_map(
    static fn($o) => in_array($o['status'], ['paid', 'delivered'], true) ? (float)$o['total'] : 0,
    $orders
));

$statusLabel = ['pending' => 'Chờ xác nhận', 'paid' => 'Đã thanh toán', 'delivered' => 'Đã bàn giao', 'cancelled' => 'Đã huỷ'];
$statusClass = ['pending' => 'badge--wait', 'paid' => 'badge--new', 'delivered' => 'badge--ok', 'cancelled' => 'badge--off'];

seo([
    'title'  => 'Tài khoản của tôi | ' . SITE_NAME,
    'robots' => 'noindex, nofollow',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container page-head">
  <?= breadcrumb(['Trang chủ' => 'index.php', 'Tài khoản' => null]) ?>
  <h1 style="margin-top:var(--sp-4)">Xin chào, <?= e($user['full_name']) ?></h1>
  <p>Quản lý thông tin cá nhân, đơn hàng và dự án yêu thích của bạn.</p>
</div>

<div class="container section--tight">
  <div class="grid grid--4 stagger" style="margin-bottom:var(--sp-6)">
    <div class="glass kpi reveal"><div class="kpi__label">Đơn hàng</div><div class="kpi__value"><?= count($orders) ?></div></div>
    <div class="glass kpi reveal"><div class="kpi__label">Đã chi tiêu</div><div class="kpi__value" style="font-size:var(--fs-xl)"><?= money($spent) ?></div></div>
    <div class="glass kpi reveal"><div class="kpi__label">Yêu thích</div><div class="kpi__value"><?= count($wishes) ?></div></div>
    <div class="glass kpi reveal"><div class="kpi__label">Thành viên từ</div><div class="kpi__value" style="font-size:var(--fs-xl)"><?= e(vn_date($user['created_at'])) ?></div></div>
  </div>

  <div class="tabs" role="tablist" aria-label="Khu vực tài khoản">
    <?php
    $tabs = ['profile' => 'Thông tin cá nhân', 'orders' => 'Đơn hàng & tải về', 'wishlist' => 'Yêu thích', 'password' => 'Đổi mật khẩu'];
    foreach ($tabs as $key => $label): ?>
      <a class="btn btn--ghost btn--sm <?= $tab === $key ? 'is-active' : '' ?>"
         style="<?= $tab === $key ? 'border-color:var(--border-strong);color:var(--fg)' : '' ?>"
         href="<?= e(url('account.php?tab=' . $key)) ?>"><?= e($label) ?></a>
    <?php endforeach; ?>
  </div>

  <?php if ($tab === 'orders'): ?>
    <?php if ($orders): ?>
      <?php foreach ($orders as $o): ?>
        <div class="glass reveal" style="padding:var(--sp-5);margin-bottom:var(--sp-4)">
          <div style="display:flex;flex-wrap:wrap;gap:12px;align-items:center;justify-content:space-between">
            <div>
              <strong style="font-family:var(--font-display);font-size:var(--fs-lg)"><?= e($o['order_code']) ?></strong>
              <div style="font-size:var(--fs-xs);color:var(--fg-muted)"><?= e(vn_date($o['created_at'], true)) ?></div>
            </div>
            <span class="badge <?= $statusClass[$o['status']] ?>"><?= e($statusLabel[$o['status']]) ?></span>
            <strong><?= money($o['total']) ?></strong>
          </div>
          <ul style="margin-top:var(--sp-3)">
            <?php foreach ($orderItems[$o['id']] ?? [] as $it): ?>
              <li style="display:flex;justify-content:space-between;gap:12px;padding:8px 0;border-top:1px solid var(--border);font-size:var(--fs-sm)">
                <span>
                  <?php if ($it['slug']): ?>
                    <a href="<?= e(url('project-detail.php?slug=' . $it['slug'])) ?>"><?= e($it['title']) ?></a>
                  <?php else: ?><?= e($it['title']) ?><?php endif; ?>
                  <small style="color:var(--fg-muted)"> · <?= e(license_label($it['license'])) ?></small>
                </span>
                <span>
                  <?php if ($o['status'] === 'delivered'): ?>
                    <a class="btn btn--ghost btn--sm" href="<?= e(url('contact.php?subject=' . urlencode('Tải lại mã nguồn đơn ' . $o['order_code']))) ?>">Tải lại</a>
                  <?php else: ?>
                    <span style="color:var(--fg-muted)"><?= money($it['price']) ?></span>
                  <?php endif; ?>
                </span>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
      <?php endforeach; ?>
    <?php else: ?>
      <div class="glass empty-state reveal">
        <h2>Chưa có đơn hàng nào</h2>
        <p>Khi bạn đặt mua dự án đầu tiên, đơn hàng sẽ hiển thị tại đây kèm liên kết tải mã nguồn.</p>
        <a class="btn btn--primary" href="<?= e(url('projects.php')) ?>">Khám phá kho dự án</a>
      </div>
    <?php endif; ?>

  <?php elseif ($tab === 'wishlist'): ?>
    <?php if ($wishes): ?>
      <div class="grid grid--3 stagger">
        <?php foreach ($wishes as $p) { include __DIR__ . '/includes/project-card.php'; } ?>
      </div>
    <?php else: ?>
      <div class="glass empty-state reveal">
        <h2>Danh sách yêu thích trống</h2>
        <p>Bấm biểu tượng trái tim trên mỗi dự án để lưu lại và so sánh sau.</p>
        <a class="btn btn--primary" href="<?= e(url('projects.php')) ?>">Xem dự án</a>
      </div>
    <?php endif; ?>

  <?php elseif ($tab === 'password'): ?>
    <form class="glass reveal" method="post" style="padding:var(--sp-6);max-width:520px" data-validate novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="password">
      <h2 style="font-size:var(--fs-xl)">Đổi mật khẩu</h2>
      <div class="form-grid">
        <div class="field">
          <label for="old_password">Mật khẩu hiện tại</label>
          <input type="password" id="old_password" name="old_password" required autocomplete="current-password">
        </div>
        <div class="field">
          <label for="new_password">Mật khẩu mới</label>
          <input type="password" id="new_password" name="new_password" required minlength="6" autocomplete="new-password">
          <span class="hint">Tối thiểu 6 ký tự.</span>
        </div>
        <button class="btn btn--primary" type="submit">Cập nhật mật khẩu</button>
      </div>
    </form>

  <?php else: ?>
    <form class="glass reveal" method="post" style="padding:var(--sp-6);max-width:520px" data-validate novalidate>
      <?= csrf_field() ?>
      <input type="hidden" name="action" value="profile">
      <h2 style="font-size:var(--fs-xl)">Thông tin cá nhân</h2>
      <div class="form-grid">
        <div class="field">
          <label for="full_name">Họ và tên</label>
          <input type="text" id="full_name" name="full_name" required value="<?= e($user['full_name']) ?>">
        </div>
        <div class="field">
          <label for="email_ro">Email</label>
          <input type="email" id="email_ro" value="<?= e($user['email']) ?>" disabled>
          <span class="hint">Email dùng để đăng nhập nên không thể thay đổi.</span>
        </div>
        <div class="field">
          <label for="phone">Số điện thoại</label>
          <input type="tel" id="phone" name="phone" inputmode="numeric" value="<?= e((string)$user['phone']) ?>">
        </div>
        <button class="btn btn--primary" type="submit">Lưu thay đổi</button>
      </div>
    </form>
  <?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
