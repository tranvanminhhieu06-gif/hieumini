<?php
// admin/orders.php - Manage Orders
$page_title = "Quản Lý Đơn Hàng";
require_once __DIR__ . '/includes/header.php';

$st_filter = clean_input($_GET['status'] ?? '');
$q = clean_input($_GET['q'] ?? '');

$sql = "SELECT * FROM orders WHERE 1=1";
$params = [];

if (!empty($st_filter)) {
    $sql .= " AND status = ?";
    $params[] = $st_filter;
}

if (!empty($q)) {
    $sql .= " AND (order_code LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ?)";
    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
    $params[] = "%{$q}%";
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<!-- Orders Filter Header -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
  <form action="orders.php" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; flex: 1; max-width: 600px;">
    <input type="text" name="q" class="form-control" placeholder="Tìm theo mã đơn, tên khách, số ĐT..." value="<?= htmlspecialchars($q) ?>" style="flex: 1; min-width: 220px;">
    <select name="status" class="form-control" style="width: auto;" onchange="this.form.submit();">
      <option value="">Tất cả trạng thái</option>
      <option value="pending" <?= $st_filter === 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
      <option value="processing" <?= $st_filter === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
      <option value="shipping" <?= $st_filter === 'shipping' ? 'selected' : '' ?>>Đang giao</option>
      <option value="completed" <?= $st_filter === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
      <option value="cancelled" <?= $st_filter === 'cancelled' ? 'selected' : '' ?>>Đã hủy</option>
    </select>
    <button type="submit" class="btn btn-outline btn-sm"><i class="bi bi-search"></i> Tìm</button>
  </form>
</div>

<!-- Orders Table -->
<div class="data-table-card">
  <div style="overflow-x: auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Mã Đơn Hàng</th>
          <th>Ngày Đặt</th>
          <th>Khách Hàng</th>
          <th>Số Điện Thoại</th>
          <th>Tổng Tiền</th>
          <th>Thanh Toán</th>
          <th>Trạng Thái</th>
          <th style="text-align: right;">Chi Tiết</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($orders as $ord): 
          $status_colors = [
            'pending' => ['Chờ xác nhận', '#f59e0b', '#fef3c7'],
            'processing' => ['Đang xử lý', '#3b82f6', '#dbeafe'],
            'shipping' => ['Đang giao', '#8b5cf6', '#ede9fe'],
            'completed' => ['Hoàn thành', '#10b981', '#d1fae5'],
            'cancelled' => ['Đã hủy', '#ef4444', '#fee2e2']
          ];
          $st = $status_colors[$ord['status']] ?? ['Chờ xử lý', '#64748b', '#f1f5f9'];
        ?>
        <tr>
          <td>
            <a href="order-detail.php?id=<?= $ord['id'] ?>" style="font-weight: 800; color: var(--primary);">
              #<?= htmlspecialchars($ord['order_code']) ?>
            </a>
          </td>
          <td style="color: var(--muted); font-size: 0.85rem;"><?= date('d/m/Y H:i', strtotime($ord['created_at'])) ?></td>
          <td style="font-weight: 600;"><?= htmlspecialchars($ord['customer_name']) ?></td>
          <td style="color: var(--muted); font-size: 0.88rem;"><?= htmlspecialchars($ord['customer_phone']) ?></td>
          <td style="font-weight: 800; color: var(--dark);"><?= format_price($ord['total_amount']) ?></td>
          <td>
            <span style="font-size: 0.8rem; font-weight: 700; text-transform: uppercase;">
              <?= $ord['payment_method'] ?>
            </span>
          </td>
          <td>
            <span style="background: <?= $st[2] ?>; color: <?= $st[1] ?>; font-weight: 700; font-size: 0.78rem; padding: 4px 10px; border-radius: var(--radius-full);">
              <?= $st[0] ?>
            </span>
          </td>
          <td style="text-align: right;">
            <a href="order-detail.php?id=<?= $ord['id'] ?>" class="btn btn-outline btn-sm" style="padding: 6px 14px;">
              <i class="bi bi-eye"></i> Xem
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
