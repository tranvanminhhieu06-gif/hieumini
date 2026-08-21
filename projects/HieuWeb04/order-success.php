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

$isBankTransfer = in_array($order['payment_method'], ['banking', 'momo'], true);
$bankName = 'MB Bank (Ngân hàng Quân Đội)';
$bankAccount = '888899998888';
$accountHolder = 'DATCYBER VIETNAM';
$transferMemo = $order['order_code'];
$qrUrl = "https://api.vietqr.io/image/970422-{$bankAccount}-qr_only.jpg?amount=" . (int)$order['final_amount'] . "&addInfo=" . urlencode($transferMemo);
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
      Mã đơn hàng: <strong class="font-monospace text-danger"><?php echo htmlspecialchars($order['order_code']); ?></strong>
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

  <?php if ($isBankTransfer): ?>
  <!-- VIETQR PAYMENT CARD -->
  <div class="card border-primary border-2 shadow-sm rounded-4 mb-4 overflow-hidden" style="background: linear-gradient(to bottom, #f0f9ff, #ffffff);">
    <div class="card-header bg-primary text-white p-3 d-flex align-items-center justify-content-between">
      <div class="fw-bold fs-5">
        <i class="fas fa-qrcode me-2"></i> Quét Mã VietQR Chuyển Khoản Tự Động 24/7
      </div>
      <span class="badge bg-warning text-dark"><i class="fas fa-bolt me-1"></i>Xác nhận tức thì</span>
    </div>
    <div class="card-body p-4">
      <div class="row align-items-center g-4">
        
        <!-- QR Code Column -->
        <div class="col-md-5 text-center">
          <div class="bg-white p-3 rounded-4 shadow-sm border d-inline-block">
            <img src="<?php echo $qrUrl; ?>" alt="Mã VietQR" class="img-fluid rounded-3" style="max-width: 220px; height: auto;">
          </div>
          <div class="mt-2 text-muted small">
            <i class="fas fa-camera me-1"></i> Mở App Ngân hàng hoặc MoMo để quét mã
          </div>
        </div>

        <!-- Transfer Information Column -->
        <div class="col-md-7">
          <div class="bg-white p-3 p-md-4 rounded-4 border shadow-sm">
            <h6 class="fw-bold text-primary mb-3"><i class="fas fa-building-columns me-2"></i>Thông Tin Tài Khoản Thụ Hưởng</h6>

            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <span class="text-muted">Ngân hàng:</span>
              <strong class="text-dark"><?php echo $bankName; ?></strong>
            </div>

            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <span class="text-muted">Số tài khoản:</span>
              <div class="d-flex align-items-center gap-2">
                <strong class="text-primary fs-5 font-monospace" id="accNum"><?php echo $bankAccount; ?></strong>
                <button type="button" class="btn btn-sm btn-outline-primary py-0 px-2" onclick="copyText('<?php echo $bankAccount; ?>', this)" title="Sao chép">
                  <i class="fas fa-copy"></i>
                </button>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <span class="text-muted">Chủ tài khoản:</span>
              <strong class="text-dark"><?php echo $accountHolder; ?></strong>
            </div>

            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
              <span class="text-muted">Số tiền:</span>
              <div class="d-flex align-items-center gap-2">
                <strong class="text-danger fs-5"><?php echo format_price($order['final_amount']); ?></strong>
                <button type="button" class="btn btn-sm btn-outline-danger py-0 px-2" onclick="copyText('<?php echo (int)$order['final_amount']; ?>', this)" title="Sao chép số tiền">
                  <i class="fas fa-copy"></i>
                </button>
              </div>
            </div>

            <div class="d-flex justify-content-between align-items-center py-2">
              <span class="text-muted">Nội dung CK (Bắt buộc):</span>
              <div class="d-flex align-items-center gap-2">
                <strong class="badge bg-danger fs-6 font-monospace" id="memoText"><?php echo htmlspecialchars($transferMemo); ?></strong>
                <button type="button" class="btn btn-sm btn-outline-secondary py-0 px-2" onclick="copyText('<?php echo htmlspecialchars($transferMemo); ?>', this)" title="Sao chép nội dung">
                  <i class="fas fa-copy"></i>
                </button>
              </div>
            </div>

            <div class="alert alert-warning py-2 px-3 small mt-3 mb-0">
              <i class="fas fa-circle-info me-1"></i> Quý khách vui lòng nhập chính xác <strong>Nội dung chuyển khoản</strong> để hệ thống tự động kích hoạt đơn hàng trong 1-3 phút.
            </div>
          </div>
        </div>

      </div>
    </div>
  </div>
  <?php endif; ?>

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
        <p class="mb-3">
          <strong>Thanh toán:</strong> 
          <?php if ($order['payment_method'] === 'banking'): ?>
            <span class="badge bg-primary text-uppercase"><i class="fas fa-qrcode me-1"></i>VietQR Chuyển khoản</span>
          <?php elseif ($order['payment_method'] === 'momo'): ?>
            <span class="badge bg-danger text-uppercase"><i class="fas fa-wallet me-1"></i>Ví MoMo</span>
          <?php else: ?>
            <span class="badge bg-success text-uppercase"><i class="fas fa-money-bill-wave me-1"></i>COD Tiền mặt</span>
          <?php endif; ?>
        </p>

        <div class="border-top pt-3">
          <div class="d-flex justify-content-between mb-1 small">
            <span class="text-secondary">Tạm tính:</span>
            <span><?php echo format_price($order['total_amount']); ?></span>
          </div>
          <?php if ($order['discount_amount'] > 0): ?>
            <div class="d-flex justify-content-between mb-1 text-success small">
              <span>Giảm giá Voucher:</span>
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

<script>
function copyText(val, btn) {
  navigator.clipboard.writeText(val).then(() => {
    const origHtml = btn.innerHTML;
    btn.innerHTML = '<i class="fas fa-check text-success"></i>';
    if (typeof showToast === 'function') {
      showToast('Đã sao chép: ' + val, 'success');
    }
    setTimeout(() => { btn.innerHTML = origHtml; }, 2000);
  }).catch(() => {
    alert('Đã sao chép: ' + val);
  });
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
