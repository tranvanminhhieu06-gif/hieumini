<?php
// admin/index.php - Admin Dashboard
$page_title = "Bảng Điều Khiển Tổng Quan";
require_once __DIR__ . '/includes/header.php';

// Quick order status update handler
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['update_order_status'])) {
    $order_id = (int)$_POST['order_id'];
    $new_status = clean_input($_POST['status']);
    $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?")->execute([$new_status, $order_id]);
    set_flash('success', "Đã cập nhật trạng thái đơn hàng #{$order_id} thành công!");
    header('Location: index.php');
    exit;
}

// KPI Calculations
$total_rev = (float)$pdo->query("SELECT COALESCE(SUM(total_amount), 0) FROM orders WHERE status != 'cancelled'")->fetchColumn();
$total_orders = (int)$pdo->query("SELECT count(*) FROM orders")->fetchColumn();
$total_products = (int)$pdo->query("SELECT count(*) FROM products")->fetchColumn();
$total_users = (int)$pdo->query("SELECT count(*) FROM users WHERE role = 'customer'")->fetchColumn();

// Recent 6 Orders
$orders_stmt = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 6");
$recent_orders = $orders_stmt->fetchAll();

// Top 5 Products
$top_products_stmt = $pdo->query("
    SELECT p.name, p.image, p.price, p.stock_quantity, c.name as category_name
    FROM products p
    JOIN categories c ON p.category_id = c.id
    ORDER BY p.rating DESC, p.review_count DESC
    LIMIT 5
");
$top_products = $top_products_stmt->fetchAll();
?>

<!-- KPI Metric Cards -->
<div class="kpi-grid">
  <div class="kpi-card">
    <div class="kpi-icon" style="background: #e0e7ff; color: var(--primary);">
      <i class="bi bi-wallet2"></i>
    </div>
    <div>
      <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600;">Tổng Doanh Thu</div>
      <div style="font-size: 1.4rem; font-weight: 800; color: var(--dark);"><?= format_price($total_rev) ?></div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: #fdf2f8; color: var(--secondary);">
      <i class="bi bi-cart-check"></i>
    </div>
    <div>
      <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600;">Tổng Đơn Hàng</div>
      <div style="font-size: 1.4rem; font-weight: 800; color: var(--dark);"><?= $total_orders ?> đơn</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: #ecfdf5; color: var(--accent-emerald);">
      <i class="bi bi-box-seam"></i>
    </div>
    <div>
      <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600;">Tổng Sản Phẩm</div>
      <div style="font-size: 1.4rem; font-weight: 800; color: var(--dark);"><?= $total_products ?> món</div>
    </div>
  </div>

  <div class="kpi-card">
    <div class="kpi-icon" style="background: #fef3c7; color: var(--accent-amber);">
      <i class="bi bi-people"></i>
    </div>
    <div>
      <div style="font-size: 0.85rem; color: var(--muted); font-weight: 600;">Khách Hàng</div>
      <div style="font-size: 1.4rem; font-weight: 800; color: var(--dark);"><?= $total_users ?> người</div>
    </div>
  </div>
</div>

<!-- Dashboard 2-column layout -->
<div style="display: grid; grid-template-columns: 1.5fr 1fr; gap: 28px; margin-bottom: 30px;">
  <!-- Recent Orders Card -->
  <div class="data-table-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--dark);">Đơn Hàng Mới Nhất</h3>
      <a href="orders.php" style="font-size: 0.85rem; color: var(--primary); font-weight: 700;">Xem tất cả</a>
    </div>

    <div style="overflow-x: auto;">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Mã Đơn</th>
            <th>Khách Hàng</th>
            <th>Tổng Tiền</th>
            <th>Trạng Thái</th>
            <th>Cập Nhật</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($recent_orders as $ord): 
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
              <a href="order-detail.php?id=<?= $ord['id'] ?>" style="font-weight: 700; color: var(--primary);">
                #<?= htmlspecialchars($ord['order_code']) ?>
              </a>
            </td>
            <td>
              <div style="font-weight: 600;"><?= htmlspecialchars($ord['customer_name']) ?></div>
              <div style="font-size: 0.8rem; color: var(--muted);"><?= htmlspecialchars($ord['customer_phone']) ?></div>
            </td>
            <td style="font-weight: 800; color: var(--dark);"><?= format_price($ord['total_amount']) ?></td>
            <td>
              <span style="background: <?= $st[2] ?>; color: <?= $st[1] ?>; font-weight: 700; font-size: 0.78rem; padding: 4px 10px; border-radius: var(--radius-full);">
                <?= $st[0] ?>
              </span>
            </td>
            <td>
              <form action="index.php" method="POST" style="display: flex; gap: 4px;">
                <input type="hidden" name="order_id" value="<?= $ord['id'] ?>">
                <select name="status" onchange="this.form.submit();" class="form-control" style="padding: 4px 8px; font-size: 0.8rem; border-radius: 6px;">
                  <option value="pending" <?= $ord['status'] === 'pending' ? 'selected' : '' ?>>Chờ xác nhận</option>
                  <option value="processing" <?= $ord['status'] === 'processing' ? 'selected' : '' ?>>Đang xử lý</option>
                  <option value="shipping" <?= $ord['status'] === 'shipping' ? 'selected' : '' ?>>Đang giao</option>
                  <option value="completed" <?= $ord['status'] === 'completed' ? 'selected' : '' ?>>Hoàn thành</option>
                  <option value="cancelled" <?= $ord['status'] === 'cancelled' ? 'selected' : '' ?>>Hủy đơn</option>
                </select>
                <input type="hidden" name="update_order_status" value="1">
              </form>
            </td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>

  <!-- Top Products Card -->
  <div class="data-table-card">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
      <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--dark);">Sản Phẩm Đánh Giá Cao</h3>
      <a href="products.php" style="font-size: 0.85rem; color: var(--primary); font-weight: 700;">Quản lý kho</a>
    </div>

    <div style="display: flex; flex-direction: column; gap: 14px;">
      <?php foreach ($top_products as $tp): ?>
      <div style="display: flex; align-items: center; justify-content: space-between; gap: 12px; padding-bottom: 12px; border-bottom: 1px solid var(--border);">
        <div style="display: flex; align-items: center; gap: 12px;">
          <img src="../assets/images/products/<?= htmlspecialchars($tp['image']) ?>" alt="" style="width: 44px; height: 44px; border-radius: 8px; object-fit: contain; background: #f8fafc; border: 1px solid var(--border);">
          <div>
            <div style="font-size: 0.9rem; font-weight: 700; color: var(--dark);"><?= htmlspecialchars($tp['name']) ?></div>
            <div style="font-size: 0.78rem; color: var(--muted);"><?= htmlspecialchars($tp['category_name']) ?></div>
          </div>
        </div>
        <div style="text-align: right;">
          <div style="font-size: 0.88rem; font-weight: 800; color: var(--primary);"><?= format_price($tp['price']) ?></div>
          <div style="font-size: 0.78rem; color: var(--accent-emerald); font-weight: 600;">Kho: <?= $tp['stock_quantity'] ?></div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
