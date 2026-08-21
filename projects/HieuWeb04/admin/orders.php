<?php
$admin_title = 'Quản Lý Đơn Hàng';
require_once __DIR__ . '/header.php';

// Handle Status Update & Auto Stock Adjustment
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_status'])) {
    $orderId = (int)$_POST['order_id'];
    $newStatus = clean_input($_POST['status'] ?? 'pending');

    // Get current status
    $currStmt = $pdo->prepare("SELECT status FROM orders WHERE id = ?");
    $currStmt->execute([$orderId]);
    $currentStatus = $currStmt->fetchColumn();

    if ($currentStatus && $currentStatus !== $newStatus) {
        $pdo->beginTransaction();
        try {
            // If changing to cancelled -> Return stock
            if ($newStatus === 'cancelled' && $currentStatus !== 'cancelled') {
                $itemStmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
                $itemStmt->execute([$orderId]);
                $items = $itemStmt->fetchAll();

                $retStock = $pdo->prepare("UPDATE products SET stock = stock + ? WHERE id = ?");
                foreach ($items as $it) {
                    if ($it['product_id']) {
                        $retStock->execute([(int)$it['quantity'], $it['product_id']]);
                    }
                }
            }
            // If changing from cancelled to another status -> Re-deduct stock
            elseif ($currentStatus === 'cancelled' && $newStatus !== 'cancelled') {
                $itemStmt = $pdo->prepare("SELECT product_id, quantity FROM order_items WHERE order_id = ?");
                $itemStmt->execute([$orderId]);
                $items = $itemStmt->fetchAll();

                $dedStock = $pdo->prepare("UPDATE products SET stock = GREATEST(0, stock - ?) WHERE id = ?");
                foreach ($items as $it) {
                    if ($it['product_id']) {
                        $dedStock->execute([(int)$it['quantity'], $it['product_id']]);
                    }
                }
            }

            $uStmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $uStmt->execute([$newStatus, $orderId]);

            $pdo->commit();
            set_flash('success', "Đã cập nhật trạng thái đơn hàng #$orderId thành công!");
        } catch (Exception $e) {
            $pdo->rollBack();
            set_flash('error', "Lỗi khi cập nhật đơn hàng: " . $e->getMessage());
        }
    }

    header('Location: orders.php');
    exit;
}

// Filter orders
$statusFilter = clean_input($_GET['status'] ?? '');
$search = clean_input($_GET['search'] ?? '');

$sql = "SELECT * FROM orders WHERE 1=1";
$params = [];

if (!empty($statusFilter)) {
    $sql .= " AND status = ?";
    $params[] = $statusFilter;
}

if (!empty($search)) {
    $sql .= " AND (order_code LIKE ? OR customer_name LIKE ? OR customer_phone LIKE ?)";
    $params[] = "%$search%";
    $params[] = "%$search%";
    $params[] = "%$search%";
}

$sql .= " ORDER BY id DESC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();
?>

<div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2">
  <h4 class="fw-bold m-0"><i class="fas fa-file-invoice-dollar text-primary me-2"></i> Danh Sách Đơn Hàng (<?php echo count($orders); ?>)</h4>
</div>

<?php $flash = get_flash(); if ($flash): ?>
  <div class="alert alert-<?php echo $flash['type'] === 'error' ? 'danger' : htmlspecialchars($flash['type']); ?> alert-dismissible fade show">
    <?php echo htmlspecialchars($flash['message']); ?>
    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
  </div>
<?php endif; ?>

<!-- Filters -->
<div class="bg-white p-3 rounded-4 border shadow-sm mb-4">
  <form action="orders.php" method="GET" class="row g-2 align-items-center">
    <div class="col-md-5">
      <div class="input-group">
        <span class="input-group-text bg-light"><i class="fas fa-search"></i></span>
        <input type="text" name="search" class="form-control" placeholder="Tìm theo mã đơn, tên khách, số điện thoại..." value="<?php echo htmlspecialchars($search); ?>">
      </div>
    </div>
    <div class="col-md-4">
      <select name="status" class="form-select" onchange="this.form.submit()">
        <option value="">-- Tất cả trạng thái --</option>
        <option value="pending" <?php echo $statusFilter === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
        <option value="processing" <?php echo $statusFilter === 'processing' ? 'selected' : ''; ?>>Đang chuẩn bị hàng</option>
        <option value="shipping" <?php echo $statusFilter === 'shipping' ? 'selected' : ''; ?>>Đang giao hàng</option>
        <option value="completed" <?php echo $statusFilter === 'completed' ? 'selected' : ''; ?>>Đã hoàn thành</option>
        <option value="cancelled" <?php echo $statusFilter === 'cancelled' ? 'selected' : ''; ?>>Đã hủy</option>
      </select>
    </div>
    <div class="col-md-3 d-flex gap-2">
      <button type="submit" class="btn btn-primary-custom flex-grow-1">Lọc đơn</button>
      <?php if (!empty($search) || !empty($statusFilter)): ?>
        <a href="orders.php" class="btn btn-outline-secondary">Xóa lọc</a>
      <?php endif; ?>
    </div>
  </form>
</div>

<div class="bg-white p-4 rounded-4 border shadow-sm">
  <div class="table-responsive">
    <table class="table table-hover align-middle">
      <thead class="table-light">
        <tr>
          <th>Mã Đơn Hàng</th>
          <th>Khách Hàng</th>
          <th>Sản Phẩm</th>
          <th>Tổng Tiền</th>
          <th>Thanh Toán</th>
          <th>Trạng Thái</th>
          <th>Cập Nhật</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($orders)): ?>
          <tr>
            <td colspan="7" class="text-center py-4 text-muted">Không tìm thấy đơn hàng nào!</td>
          </tr>
        <?php else: ?>
          <?php foreach ($orders as $ord): 
            // Fetch items for this order
            $itSt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
            $itSt->execute([$ord['id']]);
            $orderItems = $itSt->fetchAll();
          ?>
            <tr>
              <td>
                <span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($ord['order_code']); ?></span>
                <div class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($ord['created_at'])); ?></div>
              </td>
              <td>
                <div class="fw-bold"><?php echo htmlspecialchars($ord['customer_name']); ?></div>
                <small class="text-primary d-block"><i class="fas fa-phone me-1"></i><?php echo htmlspecialchars($ord['customer_phone']); ?></small>
                <small class="text-muted d-block text-truncate" style="max-width: 180px;" title="<?php echo htmlspecialchars($ord['customer_address']); ?>">
                  <?php echo htmlspecialchars($ord['customer_address']); ?>
                </small>
              </td>
              <td>
                <div class="d-flex flex-column gap-1">
                  <?php foreach ($orderItems as $it): ?>
                    <small class="text-secondary">
                      • <?php echo htmlspecialchars($it['product_name']); ?> <strong>x<?php echo $it['quantity']; ?></strong>
                    </small>
                  <?php endforeach; ?>
                </div>
              </td>
              <td>
                <strong class="text-danger"><?php echo format_price($ord['final_amount']); ?></strong>
              </td>
              <td>
                <span class="badge bg-secondary text-uppercase"><?php echo htmlspecialchars($ord['payment_method']); ?></span>
              </td>
              <td>
                <?php
                  $statusMap = [
                    'pending' => ['bg-warning text-dark', 'Chờ xử lý'],
                    'processing' => ['bg-info text-white', 'Đang chuẩn bị'],
                    'shipping' => ['bg-primary text-white', 'Đang giao hàng'],
                    'completed' => ['bg-success text-white', 'Đã hoàn thành'],
                    'cancelled' => ['bg-danger text-white', 'Đã hủy']
                  ];
                  $st = $statusMap[$ord['status']] ?? ['bg-secondary', $ord['status']];
                ?>
                <span class="badge <?php echo $st[0]; ?>"><?php echo $st[1]; ?></span>
              </td>
              <td>
                <form action="orders.php" method="POST" class="d-flex gap-2">
                  <input type="hidden" name="order_id" value="<?php echo $ord['id']; ?>">
                  <select name="status" class="form-select form-select-sm" style="width: 130px;">
                    <option value="pending" <?php echo $ord['status'] === 'pending' ? 'selected' : ''; ?>>Chờ xử lý</option>
                    <option value="processing" <?php echo $ord['status'] === 'processing' ? 'selected' : ''; ?>>Đang chuẩn bị</option>
                    <option value="shipping" <?php echo $ord['status'] === 'shipping' ? 'selected' : ''; ?>>Đang giao</option>
                    <option value="completed" <?php echo $ord['status'] === 'completed' ? 'selected' : ''; ?>>Hoàn thành</option>
                    <option value="cancelled" <?php echo $ord['status'] === 'cancelled' ? 'selected' : ''; ?>>Hủy đơn</option>
                  </select>
                  <button type="submit" name="update_status" class="btn btn-sm btn-primary">Lưu</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/footer.php'; ?>

