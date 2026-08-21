<?php
// admin/order-detail.php - Order Detail & Status Updater
$page_title = "Chi Tiết Đơn Hàng";
require_once __DIR__ . '/includes/header.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    set_flash('danger', 'Đơn hàng không tồn tại.');
    header('Location: orders.php');
    exit;
}

// Handle Status & Payment Updates
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order'])) {
    $status = clean_input($_POST['status']);
    $payment_status = clean_input($_POST['payment_status']);

    $upd = $pdo->prepare("UPDATE orders SET status = ?, payment_status = ? WHERE id = ?");
    $upd->execute([$status, $payment_status, $id]);

    set_flash('success', 'Đã cập nhật trạng thái đơn hàng thành công!');
    header("Location: order-detail.php?id={$id}");
    exit;
}

$items_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items_stmt->execute([$id]);
$order_items = $items_stmt->fetchAll();
?>

<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px;">
  <div>
    <h2 style="font-size: 1.5rem; font-weight: 800; color: var(--dark);">Đơn Hàng #<?= htmlspecialchars($order['order_code']) ?></h2>
    <span style="font-size: 0.85rem; color: var(--muted);">Ngày tạo: <?= date('d/m/Y H:i:s', strtotime($order['created_at'])) ?></span>
  </div>
  <div style="display: flex; gap: 10px;">
    <button onclick="window.print();" class="btn btn-outline btn-sm"><i class="bi bi-printer"></i> In Đơn Hàng</button>
    <a href="orders.php" class="btn btn-outline btn-sm"><i class="bi bi-arrow-left"></i> Quay lại</a>
  </div>
</div>

<div style="display: grid; grid-template-columns: 2fr 1fr; gap: 28px;">
  <!-- Left: Ordered Products Table -->
  <div class="data-table-card">
    <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 20px; color: var(--dark);">Danh Sách Đồ Dùng Học Tập</h3>
    <table class="admin-table">
      <thead>
        <tr>
          <th>Sản Phẩm</th>
          <th>Đơn Giá</th>
          <th>Số Lượng</th>
          <th style="text-align: right;">Thành Tiền</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($order_items as $oi): ?>
        <tr>
          <td>
            <div style="display: flex; align-items: center; gap: 12px;">
              <img src="../assets/images/products/<?= htmlspecialchars($oi['product_image']) ?>" alt="" style="width: 44px; height: 44px; border-radius: 6px; object-fit: contain; background: #f8fafc; border: 1px solid var(--border);">
              <span style="font-weight: 700; color: var(--dark);"><?= htmlspecialchars($oi['product_name']) ?></span>
            </div>
          </td>
          <td><?= format_price($oi['price']) ?></td>
          <td style="font-weight: 700;">x<?= $oi['quantity'] ?></td>
          <td style="text-align: right; font-weight: 800; color: var(--dark);"><?= format_price($oi['total_price']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>

    <div style="border-top: 2px solid var(--border); margin-top: 20px; padding-top: 16px; font-size: 0.95rem;">
      <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
        <span style="color: var(--muted);">Tạm tính:</span>
        <span><?= format_price($order['subtotal']) ?></span>
      </div>
      <?php if ($order['discount_amount'] > 0): ?>
      <div style="display: flex; justify-content: space-between; margin-bottom: 8px; color: var(--accent-emerald);">
        <span>Giảm giá voucher:</span>
        <span>-<?= format_price($order['discount_amount']) ?></span>
      </div>
      <?php endif; ?>
      <div style="display: flex; justify-content: space-between; margin-bottom: 8px;">
        <span style="color: var(--muted);">Phí vận chuyển:</span>
        <span><?= format_price($order['shipping_fee']) ?></span>
      </div>
      <div style="display: flex; justify-content: space-between; font-size: 1.25rem; font-weight: 800; color: var(--primary); padding-top: 12px; border-top: 1px dashed var(--border);">
        <span>Tổng thanh toán:</span>
        <span><?= format_price($order['total_amount']) ?></span>
      </div>
    </div>
  </div>

  <!-- Right: Customer Info & Status Controls -->
  <div>
    <!-- Status Control Box -->
    <div class="data-table-card" style="margin-bottom: 24px;">
      <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 18px; color: var(--dark);">Cập Nhật Trạng Thái</h3>
      <form action="order-detail.php?id=<?= $order['id'] ?>" method="POST">
        <div class="form-group">
          <label class="form-label">Trạng thái đơn hàng</label>
          <select name="status" class="form-control">
            <option value="pending" <?= $order['status'] === 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
            <option value="processing" <?= $order['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
            <option value="shipping" <?= $order['status'] === 'shipping' ? 'selected' : '' ?>>Đang giao</option>
            <option value="completed" <?= $order['status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
            <option value="cancelled" <?= $order['status'] === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label">Trạng thái thanh toán</label>
          <select name="payment_status" class="form-control">
            <option value="unpaid" <?= $order['payment_status'] === 'unpaid' ? 'selected' : '' ?>>Chưa thanh toán</option>
            <option value="paid" <?= $order['payment_status'] === 'paid' ? 'selected' : '' ?>>Đã thanh toán</option>
            <option value="refunded" <?= $order['payment_status'] === 'refunded' ? 'selected' : '' ?>>Đã hoàn tiền</option>
          </select>
        </div>

        <button type="submit" name="update_order" value="1" class="btn btn-primary btn-sm" style="width: 100%; justify-content: center;">
          <i class="bi bi-floppy"></i> Cập Nhật
        </button>
      </form>
    </div>

    <!-- Customer Information Box -->
    <div class="data-table-card">
      <h3 style="font-size: 1.15rem; font-weight: 800; margin-bottom: 16px; color: var(--dark);">Thông Tin Khách Hàng</h3>
      <div style="font-size: 0.92rem; display: flex; flex-direction: column; gap: 10px;">
        <div><strong>Họ tên:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
        <div><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['customer_phone']) ?></div>
        <div><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></div>
        <div><strong>Địa chỉ:</strong> <?= htmlspecialchars($order['shipping_address']) ?></div>
        <div><strong>Phương thức:</strong> <?= strtoupper($order['payment_method']) ?></div>
        <?php if (!empty($order['order_notes'])): ?>
        <div style="background: var(--bg-light); padding: 10px; border-radius: 6px;">
          <strong>Ghi chú:</strong> <?= htmlspecialchars($order['order_notes']) ?>
        </div>
        <?php endif; ?>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
