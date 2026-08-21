<?php
$admin_title = 'Tổng Quan Doanh Thu & Hệ Thống';
require_once __DIR__ . '/header.php';

// Stats queries
$totalRevenue = $pdo->query("SELECT SUM(final_amount) FROM orders WHERE status != 'cancelled'")->fetchColumn() ?: 0;
$totalOrders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn() ?: 0;
$totalProducts = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn() ?: 0;
$totalCategories = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn() ?: 0;

// Recent Orders
$recentOrders = $pdo->query("SELECT * FROM orders ORDER BY id DESC LIMIT 5")->fetchAll();

// Top Products
$topProducts = $pdo->query("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id ORDER BY p.rating DESC, p.id DESC LIMIT 5")->fetchAll();
?>

<!-- Metric Cards -->
<div class="row g-4 mb-4">
  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat-card border-start border-primary border-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <span class="text-muted small fw-bold text-uppercase">Tổng Doanh Thu</span>
          <h3 class="fw-bold text-primary mt-2 mb-0"><?php echo format_price($totalRevenue); ?></h3>
        </div>
        <div class="rounded-circle bg-primary bg-opacity-10 text-primary p-3">
          <i class="fas fa-coins fa-2x"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat-card border-start border-success border-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <span class="text-muted small fw-bold text-uppercase">Tổng Đơn Hàng</span>
          <h3 class="fw-bold text-success mt-2 mb-0"><?php echo $totalOrders; ?></h3>
        </div>
        <div class="rounded-circle bg-success bg-opacity-10 text-success p-3">
          <i class="fas fa-truck-ramp-box fa-2x"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat-card border-start border-warning border-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <span class="text-muted small fw-bold text-uppercase">Sản Phẩm Đang Bán</span>
          <h3 class="fw-bold text-warning mt-2 mb-0"><?php echo $totalProducts; ?></h3>
        </div>
        <div class="rounded-circle bg-warning bg-opacity-10 text-warning p-3">
          <i class="fas fa-cubes fa-2x"></i>
        </div>
      </div>
    </div>
  </div>

  <div class="col-sm-6 col-xl-3">
    <div class="admin-stat-card border-start border-info border-4">
      <div class="d-flex justify-content-between align-items-center">
        <div>
          <span class="text-muted small fw-bold text-uppercase">Danh Mục Gia Dụng</span>
          <h3 class="fw-bold text-info mt-2 mb-0"><?php echo $totalCategories; ?></h3>
        </div>
        <div class="rounded-circle bg-info bg-opacity-10 text-info p-3">
          <i class="fas fa-tags fa-2x"></i>
        </div>
      </div>
    </div>
  </div>
</div>

<div class="row g-4">
  
  <!-- Left: Recent Orders -->
  <div class="col-lg-8">
    <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
      <div class="d-flex justify-content-between align-items-center mb-3">
        <h5 class="fw-bold m-0"><i class="fas fa-list-check text-primary me-2"></i> Đơn Hàng Mới Nhất</h5>
        <a href="orders.php" class="btn btn-sm btn-outline-primary">Xem tất cả đơn</a>
      </div>

      <div class="table-responsive">
        <table class="table table-hover align-middle">
          <thead class="table-light">
            <tr>
              <th>Mã Đơn</th>
              <th>Khách Hàng</th>
              <th>Tổng Tiền</th>
              <th>Trạng Thái</th>
              <th>Ngày Tạo</th>
            </tr>
          </thead>
          <tbody>
            <?php foreach ($recentOrders as $ord): ?>
              <tr>
                <td><span class="badge bg-light text-dark border font-monospace"><?php echo htmlspecialchars($ord['order_code']); ?></span></td>
                <td>
                  <div class="fw-bold"><?php echo htmlspecialchars($ord['customer_name']); ?></div>
                  <small class="text-muted"><?php echo htmlspecialchars($ord['customer_phone']); ?></small>
                </td>
                <td class="fw-bold text-danger"><?php echo format_price($ord['final_amount']); ?></td>
                <td>
                  <?php
                    $statusMap = [
                      'pending' => ['bg-warning', 'Chờ xử lý'],
                      'processing' => ['bg-info', 'Đang đóng gói'],
                      'shipping' => ['bg-primary', 'Đang giao hàng'],
                      'completed' => ['bg-success', 'Hoàn thành'],
                      'cancelled' => ['bg-danger', 'Đã hủy']
                    ];
                    $st = $statusMap[$ord['status']] ?? ['bg-secondary', $ord['status']];
                  ?>
                  <span class="badge <?php echo $st[0]; ?>"><?php echo $st[1]; ?></span>
                </td>
                <td class="small text-muted"><?php echo date('d/m/Y H:i', strtotime($ord['created_at'])); ?></td>
              </tr>
            <?php endforeach; ?>
          </tbody>
        </table>
      </div>
    </div>
  </div>

  <!-- Right: Top Products -->
  <div class="col-lg-4">
    <div class="bg-white p-4 rounded-4 border shadow-sm h-100">
      <h5 class="fw-bold mb-3"><i class="fas fa-fire text-danger me-2"></i> Sản Phẩm Đánh Giá Cao</h5>
      <div class="d-flex flex-column gap-3">
        <?php foreach ($topProducts as $tp): ?>
          <div class="d-flex align-items-center gap-3 p-2 rounded-3 border bg-light">
            <img src="../assets/images/products/<?php echo htmlspecialchars($tp['image']); ?>" style="width: 50px; height: 50px; object-fit: cover;" class="rounded border">
            <div class="flex-grow-1 min-w-0">
              <div class="fw-bold text-truncate small"><?php echo htmlspecialchars($tp['name']); ?></div>
              <div class="text-danger fw-bold small"><?php echo format_price($tp['price']); ?></div>
            </div>
            <div class="badge bg-warning text-dark"><i class="fas fa-star text-warning"></i> <?php echo $tp['rating']; ?></div>
          </div>
        <?php endforeach; ?>
      </div>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/footer.php'; ?>
