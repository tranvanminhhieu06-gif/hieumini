<?php
$page_title = 'Đặt Hàng Thành Công';
require_once __DIR__ . '/includes/header.php';

$orderCode = clean_input($_GET['code'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = ?");
$stmt->execute([$orderCode]);
$order = $stmt->fetch();

if (!$order) {
    echo '<div class="container my-5 text-center"><div class="alert alert-danger">Không tìm thấy thông tin đơn hàng!</div><a href="index.php" class="btn btn-primary-custom">Về trang chủ</a></div>';
    require_once __DIR__ . '/includes/footer.php';
    exit;
}

// Fetch order items
$itemStmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$itemStmt->execute([$order['id']]);
$orderItems = $itemStmt->fetchAll();
?>

<main class="container my-4">

  <!-- Success Announcement Box -->
  <div class="bg-white p-4 p-lg-5 rounded-4 border shadow-sm text-center mb-4">
    <div class="rounded-circle bg-success bg-opacity-10 text-success d-inline-flex align-items-center justify-content-center mb-3 animate-badge-pulse" style="width: 80px; height: 80px; font-size: 2.5rem;">
      <i class="fas fa-check"></i>
    </div>

    <h2 class="fw-bold text-dark">Đặt Hàng Thành Công!</h2>
    <p class="text-secondary mb-2">Cảm ơn bạn đã tin tưởng mua sắm tại <strong>DatCyber</strong>. Đơn hàng của bạn đã được tiếp nhận và đang được xử lý.</p>
    <div class="badge bg-light text-primary border fs-6 px-3 py-2">
      Mã đơn hàng: <strong class="font-monospace"><?php echo htmlspecialchars($order['order_code']); ?></strong>
    </div>

    <!-- Order Tracking Timeline Simulation -->
    <div class="row justify-content-center mt-5">
      <div class="col-lg-10">
        <div class="d-flex justify-content-between position-relative text-center">
          <div class="position-absolute top-50 start-0 translate-middle-y w-100 bg-light" style="height: 4px; z-index: 1;">
            <div class="bg-success h-100" style="width: 35%;"></div>
          </div>

          <div class="position-relative bg-white px-2" style="z-index: 2;">
            <div class="rounded-circle bg-success text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 36px; height: 36px;">
              <i class="fas fa-check"></i>
            </div>
            <div class="small fw-bold">Đã đặt hàng</div>
            <small class="text-muted"><?php echo date('H:i d/m', strtotime($order['created_at'])); ?></small>
          </div>

          <div class="position-relative bg-white px-2" style="z-index: 2;">
            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 36px; height: 36px;">
              <i class="fas fa-box-open"></i>
            </div>
            <div class="small fw-bold text-primary">Đang đóng gói</div>
            <small class="text-muted">Dự kiến 2 giờ</small>
          </div>

          <div class="position-relative bg-white px-2" style="z-index: 2;">
            <div class="rounded-circle bg-light text-muted border d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 36px; height: 36px;">
              <i class="fas fa-truck-fast"></i>
            </div>
            <div class="small fw-bold text-muted">Đang vận chuyển</div>
            <small class="text-muted">1 - 2 ngày</small>
          </div>

          <div class="position-relative bg-white px-2" style="z-index: 2;">
            <div class="rounded-circle bg-light text-muted border d-flex align-items-center justify-content-center mx-auto mb-2" style="width: 36px; height: 36px;">
              <i class="fas fa-circle-check"></i>
            </div>
            <div class="small fw-bold text-muted">Giao thành công</div>
            <small class="text-muted">Nhận hàng</small>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Order Details & Invoice Card -->
  <div class="row g-4">
    
    <!-- Left: Ordered Products -->
    <div class="col-lg-8">
      <div class="bg-white p-4 rounded-4 border shadow-sm">
        <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="fas fa-boxes-packing text-primary me-2"></i> Chi Tiết Sản Phẩm Đã Mua</h5>
        
        <div class="table-responsive">
          <table class="table align-middle">
            <thead class="table-light">
              <tr>
                <th>Sản phẩm</th>
                <th class="text-center">Đơn giá</th>
                <th class="text-center">Số lượng</th>
                <th class="text-end">Thành tiền</th>
              </tr>
            </thead>
            <tbody>
              <?php foreach ($orderItems as $item): ?>
                <tr>
                  <td>
                    <div class="d-flex align-items-center gap-3">
                      <img src="assets/images/products/<?php echo htmlspecialchars($item['product_image']); ?>" style="width: 55px; height: 55px; object-fit: cover;" class="rounded border">
                      <span class="fw-bold" style="font-size: 0.95rem;"><?php echo htmlspecialchars($item['product_name']); ?></span>
                    </div>
                  </td>
                  <td class="text-center text-secondary"><?php echo format_price($item['price']); ?></td>
                  <td class="text-center fw-bold">x<?php echo $item['quantity']; ?></td>
                  <td class="text-end fw-bold text-danger"><?php echo format_price($item['subtotal']); ?></td>
                </tr>
              <?php endforeach; ?>
            </tbody>
          </table>
        </div>

        <div class="d-flex justify-content-between align-items-center pt-3 border-top">
          <a href="index.php" class="btn btn-outline-primary"><i class="fas fa-arrow-left me-1"></i> Tiếp tục mua sắm</a>
          <button onclick="window.print()" class="btn btn-outline-secondary"><i class="fas fa-print me-1"></i> In hóa đơn</button>
        </div>
      </div>
    </div>

    <!-- Right: Customer & Payment Summary -->
    <div class="col-lg-4">
      <div class="bg-white p-4 rounded-4 border shadow-sm">
        <h5 class="fw-bold mb-3 border-bottom pb-2"><i class="fas fa-receipt text-primary me-2"></i> Thông Tin Nhận Hàng</h5>
        
        <p class="mb-1"><strong>Người nhận:</strong> <?php echo htmlspecialchars($order['customer_name']); ?></p>
        <p class="mb-1"><strong>Số điện thoại:</strong> <?php echo htmlspecialchars($order['customer_phone']); ?></p>
        <?php if (!empty($order['customer_email'])): ?>
          <p class="mb-1"><strong>Email:</strong> <?php echo htmlspecialchars($order['customer_email']); ?></p>
        <?php endif; ?>
        <p class="mb-1"><strong>Địa chỉ:</strong> <?php echo htmlspecialchars($order['customer_address']); ?></p>
        <?php if (!empty($order['customer_note'])): ?>
          <p class="mb-1"><strong>Ghi chú:</strong> <em><?php echo htmlspecialchars($order['customer_note']); ?></em></p>
        <?php endif; ?>
        <p class="mb-3"><strong>Thanh toán:</strong> <span class="badge bg-primary text-uppercase"><?php echo htmlspecialchars($order['payment_method']); ?></span></p>

        <div class="border-top pt-3">
          <div class="d-flex justify-content-between mb-1 small">
            <span class="text-secondary">Tạm tính:</span>
            <span><?php echo format_price($order['total_amount']); ?></span>
          </div>
          <?php if ($order['discount_amount'] > 0): ?>
            <div class="d-flex justify-content-between mb-1 text-success small">
              <span>Giảm giá:</span>
              <span>-<?php echo format_price($order['discount_amount']); ?></span>
            </div>
          <?php endif; ?>
          <div class="d-flex justify-content-between mb-2 small">
            <span class="text-secondary">Vận chuyển:</span>
            <span><?php echo $order['shipping_fee'] == 0 ? 'Miễn phí' : format_price($order['shipping_fee']); ?></span>
          </div>
          <div class="d-flex justify-content-between border-top pt-2 fw-bold fs-5 text-danger">
            <span>Tổng cộng:</span>
            <span><?php echo format_price($order['final_amount']); ?></span>
          </div>
        </div>

      </div>
    </div>

  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
