<?php
// profile.php - User Account Profile & Order History
$custom_page_title = "Tài Khoản Của Tôi";
require_once __DIR__ . '/config/app.php';
require_login();

$user_id = current_user()['id'];

// Handle Profile Update
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_profile'])) {
    $fullname = clean_input($_POST['fullname'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $address = clean_input($_POST['address'] ?? '');

    $upd = $pdo->prepare("UPDATE users SET fullname = ?, phone = ?, address = ? WHERE id = ?");
    $upd->execute([$fullname, $phone, $address, $user_id]);

    $_SESSION['user']['fullname'] = $fullname;
    $_SESSION['user']['phone'] = $phone;
    $_SESSION['user']['address'] = $address;

    set_flash('success', 'Cập nhật thông tin cá nhân thành công!');
    header('Location: profile.php');
    exit;
}

// Fetch current user details
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$user_id]);
$user = $stmt->fetch();

// Fetch user orders
$order_stmt = $pdo->prepare("SELECT * FROM orders WHERE user_id = ? OR customer_email = ? ORDER BY id DESC");
$order_stmt->execute([$user_id, $user['email']]);
$user_orders = $order_stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="margin: 40px auto 70px;">
  <!-- Breadcrumb -->
  <div style="padding: 10px 0 20px; font-size: 0.88rem; color: var(--muted); display: flex; align-items: center; gap: 8px;">
    <a href="index.php" style="color: var(--muted);"><i class="bi bi-house"></i> Trang chủ</a>
    <span>/</span>
    <span style="color: var(--dark); font-weight: 700;">Tài khoản của tôi</span>
  </div>

  <div class="profile-layout">
    <!-- Left: Profile Summary & Update Form -->
    <aside style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 28px; box-shadow: var(--shadow-sm); height: fit-content;">
      <div style="text-align: center; margin-bottom: 24px; padding-bottom: 20px; border-bottom: 1px solid var(--border);">
        <div style="width: 80px; height: 80px; border-radius: 50%; background: linear-gradient(135deg, var(--primary), var(--secondary)); color: #fff; display: flex; align-items: center; justify-content: center; font-size: 2.2rem; margin: 0 auto 12px; font-weight: 800;">
          <?= strtoupper(mb_substr($user['fullname'], 0, 1)) ?>
        </div>
        <h3 style="font-size: 1.2rem; font-weight: 800; color: var(--dark); margin-bottom: 4px;"><?= htmlspecialchars($user['fullname']) ?></h3>
        <p style="font-size: 0.85rem; color: var(--muted);"><?= htmlspecialchars($user['email']) ?></p>
        <span class="badge-tag badge-new" style="margin-top: 6px; display: inline-block;"><?= $user['role'] === 'admin' ? 'Quản Trị Viên' : 'Khách Hàng Thân Thiết' ?></span>
      </div>

      <h4 style="font-size: 1rem; font-weight: 800; margin-bottom: 16px; color: var(--dark);">Cập Nhật Thông Tin</h4>
      <form action="profile.php" method="POST">
        <div class="form-group">
          <label class="form-label" style="font-size: 0.85rem;">Họ và tên</label>
          <input type="text" name="fullname" required class="form-control" value="<?= htmlspecialchars($user['fullname']) ?>">
        </div>
        <div class="form-group">
          <label class="form-label" style="font-size: 0.85rem;">Số điện thoại</label>
          <input type="tel" name="phone" class="form-control" value="<?= htmlspecialchars($user['phone'] ?? '') ?>">
        </div>
        <div class="form-group">
          <label class="form-label" style="font-size: 0.85rem;">Địa chỉ nhận hàng</label>
          <textarea name="address" rows="3" class="form-control"><?= htmlspecialchars($user['address'] ?? '') ?></textarea>
        </div>
        <button type="submit" name="update_profile" value="1" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">
          <i class="bi bi-floppy"></i> Lưu Thay Đổi
        </button>
      </form>
    </aside>

    <!-- Right: Order History -->
    <main>
      <div style="background: var(--white); border-radius: var(--radius-lg); border: 1px solid var(--border); padding: 30px; box-shadow: var(--shadow-sm);">
        <h2 style="font-size: 1.4rem; font-weight: 800; margin-bottom: 24px; color: var(--dark); display: flex; align-items: center; gap: 10px;">
          <i class="bi bi-clock-history" style="color: var(--primary);"></i> Lịch Sử Đơn Hàng (<?= count($user_orders) ?>)
        </h2>

        <?php if (!empty($user_orders)): ?>
          <div style="display: flex; flex-direction: column; gap: 20px;">
            <?php foreach ($user_orders as $ord): 
              $items_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
              $items_stmt->execute([$ord['id']]);
              $ord_items = $items_stmt->fetchAll();

              $status_labels = [
                'pending' => ['Chờ xác nhận', '#f59e0b', '#fef3c7'],
                'processing' => ['Đang xử lý', '#3b82f6', '#dbeafe'],
                'shipping' => ['Đang giao hàng', '#8b5cf6', '#ede9fe'],
                'completed' => ['Hoàn thành', '#10b981', '#d1fae5'],
                'cancelled' => ['Đã hủy', '#ef4444', '#fee2e2']
              ];
              $st = $status_labels[$ord['status']] ?? ['Chờ xử lý', '#6b7280', '#f3f4f6'];
            ?>
            <div style="border: 1px solid var(--border); border-radius: var(--radius-md); padding: 20px; background: var(--bg-light);">
              <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 14px; padding-bottom: 12px; border-bottom: 1px solid var(--border); flex-wrap: wrap; gap: 10px;">
                <div>
                  <span style="font-weight: 800; font-size: 1.05rem; color: var(--dark);">Mã: #<?= htmlspecialchars($ord['order_code']) ?></span>
                  <span style="font-size: 0.82rem; color: var(--muted); margin-left: 10px;"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></span>
                </div>
                <span style="background: <?= $st[2] ?>; color: <?= $st[1] ?>; font-weight: 800; font-size: 0.8rem; padding: 4px 12px; border-radius: var(--radius-full);">
                  <?= $st[0] ?>
                </span>
              </div>

              <!-- Order items -->
              <div style="margin-bottom: 14px;">
                <?php foreach ($ord_items as $oi): ?>
                <div style="display: flex; align-items: center; justify-content: space-between; font-size: 0.9rem; margin-bottom: 8px;">
                  <div style="display: flex; align-items: center; gap: 10px;">
                    <img src="assets/images/products/<?= htmlspecialchars($oi['product_image']) ?>" alt="" style="width: 38px; height: 38px; border-radius: 6px; object-fit: contain; background: #fff;">
                    <span><?= htmlspecialchars($oi['product_name']) ?> <strong style="color: var(--muted);">x<?= $oi['quantity'] ?></strong></span>
                  </div>
                  <span style="font-weight: 700;"><?= format_price($oi['total_price']) ?></span>
                </div>
                <?php endforeach; ?>
              </div>

              <div style="display: flex; justify-content: space-between; align-items: center; padding-top: 12px; border-top: 1px dashed var(--border); font-size: 0.92rem;">
                <span style="color: var(--muted);">Phương thức: <strong><?= strtoupper($ord['payment_method']) ?></strong></span>
                <div>
                  Tổng tiền: <strong style="font-size: 1.15rem; color: var(--primary);"><?= format_price($ord['total_amount']) ?></strong>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          </div>
        <?php else: ?>
          <div style="text-align: center; padding: 40px 20px;">
            <i class="bi bi-bag" style="font-size: 2.5rem; color: var(--muted); display: block; margin-bottom: 10px;"></i>
            <p style="color: var(--muted);">Bạn chưa có đơn hàng nào.</p>
            <a href="products.php" class="btn btn-primary btn-sm" style="margin-top: 12px;">Mua Sắm Ngay</a>
          </div>
        <?php endif; ?>
      </div>
    </main>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
