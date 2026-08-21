<?php
// order-success.php - Order Confirmation & Invoice
$custom_page_title = "Đặt Hàng Thành Công!";
require_once __DIR__ . '/config/app.php';

$order_code = clean_input($_GET['code'] ?? '');

$stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = ?");
$stmt->execute([$order_code]);
$order = $stmt->fetch();

if (!$order) {
    header('Location: index.php');
    exit;
}

$items_stmt = $pdo->prepare("SELECT * FROM order_items WHERE order_id = ?");
$items_stmt->execute([$order['id']]);
$order_items = $items_stmt->fetchAll();

require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container" id="orderSuccessConfetti">
  <div class="success-card">
    <div class="success-icon-wrap">
      <i class="bi bi-check-lg"></i>
    </div>
    
    <span class="badge-tag badge-new" style="margin-bottom: 12px; display: inline-block;">ĐẶT HÀNG THÀNH CÔNG</span>
    <h1 style="font-size: 2.2rem; font-weight: 800; margin-bottom: 12px; color: var(--dark);">Cảm Ơn Bạn Đã Mua Sắm!</h1>
    <p style="color: var(--muted); font-size: 1.05rem; margin-bottom: 24px;">
      Đơn hàng <strong>#<?= htmlspecialchars($order['order_code']) ?></strong> của bạn đã được ghi nhận và đang được đóng gói chuẩn bị giao tới bạn!
    </p>

    <!-- Tracking status timeline -->
    <div style="display: flex; justify-content: space-between; margin: 36px 0; position: relative;">
      <div style="text-align: center; flex: 1; z-index: 2;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: var(--primary); color: #fff; display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
          <i class="bi bi-check2"></i>
        </div>
        <div style="font-size: 0.82rem; font-weight: 700; color: var(--dark);">Đã Đặt Hàng</div>
      </div>
      <div style="text-align: center; flex: 1; z-index: 2;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #e0e7ff; color: var(--primary); display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
          <i class="bi bi-box-seam"></i>
        </div>
        <div style="font-size: 0.82rem; font-weight: 600; color: var(--muted);">Đang Đóng Gói</div>
      </div>
      <div style="text-align: center; flex: 1; z-index: 2;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; color: var(--muted); display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
          <i class="bi bi-truck"></i>
        </div>
        <div style="font-size: 0.82rem; font-weight: 600; color: var(--muted);">Đang Vận Chuyển</div>
      </div>
      <div style="text-align: center; flex: 1; z-index: 2;">
        <div style="width: 36px; height: 36px; border-radius: 50%; background: #f1f5f9; color: var(--muted); display: flex; align-items: center; justify-content: center; margin: 0 auto 8px;">
          <i class="bi bi-house-check"></i>
        </div>
        <div style="font-size: 0.82rem; font-weight: 600; color: var(--muted);">Giao Thành Công</div>
      </div>
    </div>

    <!-- Order Details Box -->
    <div style="background: var(--bg-light); border-radius: var(--radius-md); padding: 24px; text-align: left; margin-bottom: 30px; border: 1px solid var(--border);">
      <h3 style="font-size: 1.1rem; font-weight: 800; margin-bottom: 16px; color: var(--dark); border-bottom: 1px solid var(--border); padding-bottom: 10px;">
        Chi Tiết Hóa Đơn
      </h3>

      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 12px; font-size: 0.9rem; margin-bottom: 18px;">
        <div><strong>Khách hàng:</strong> <?= htmlspecialchars($order['customer_name']) ?></div>
        <div><strong>Số điện thoại:</strong> <?= htmlspecialchars($order['customer_phone']) ?></div>
        <div><strong>Email:</strong> <?= htmlspecialchars($order['customer_email']) ?></div>
        <div><strong>Phương thức:</strong> <?= strtoupper($order['payment_method']) ?></div>
        <div style="grid-column: span 2;"><strong>Địa chỉ nhận:</strong> <?= htmlspecialchars($order['shipping_address']) ?></div>
      </div>

      <div style="border-top: 1px dashed var(--border); padding-top: 14px;">
        <?php foreach ($order_items as $item): ?>
        <div style="display: flex; justify-content: space-between; font-size: 0.88rem; margin-bottom: 8px;">
          <span><?= htmlspecialchars($item['product_name']) ?> x <?= $item['quantity'] ?></span>
          <span style="font-weight: 700;"><?= format_price($item['total_price']) ?></span>
        </div>
        <?php endforeach; ?>
        
        <div style="border-top: 1px solid var(--border); margin-top: 12px; padding-top: 12px; display: flex; justify-content: space-between; font-size: 1.15rem; font-weight: 800; color: var(--primary);">
          <span>Tổng tiền thanh toán:</span>
          <span><?= format_price($order['total_amount']) ?></span>
        </div>
      </div>
    </div>

    <!-- VietQR Chuyển Khoản nếu chọn Banking -->
    <?php if (in_array($order['payment_method'] ?? '', ['banking', 'bank', 'bank_transfer', 'momo'], true)): ?>
    <div style="background: #f0fdf4; border: 1.5px dashed #22c55e; border-radius: var(--radius-md); padding: 24px; margin-bottom: 30px; text-align: center;">
      <h4 style="color: #15803d; font-size: 1.1rem; font-weight: 700; margin-bottom: 12px;"><i class="bi bi-qr-code-scan"></i> Quét Mã VietQR Thanh Toán Tự Động 24/7</h4>
      <div style="display: inline-block; background: #fff; padding: 12px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); margin-bottom: 14px;">
        <img src="https://api.vietqr.io/image/970422-888899998888-qr_only.jpg?amount=<?= (int)$order['total_amount'] ?>&addInfo=<?= urlencode($order['order_code']) ?>" alt="VietQR" style="width: 190px; height: 190px; object-fit: contain;">
      </div>
      <div style="font-size: 0.9rem; color: #334155; line-height: 1.8;">
        <div>Ngân hàng: <strong>MB Bank (Ngân hàng Quân Đội)</strong> | STK: <strong style="color: #15803d; font-family: monospace;">888899998888</strong></div>
        <div>Chủ tài khoản: <strong>HIEUMINI FURNITURE STORE</strong></div>
        <div>Nội dung CK: <strong style="color: #ef4444; font-family: monospace; font-size: 1rem;"><?= htmlspecialchars($order['order_code']) ?></strong></div>
      </div>
    </div>
    <?php endif; ?>

    <div style="display: flex; gap: 16px; justify-content: center; flex-wrap: wrap;">
      <a href="index.php" class="btn btn-primary btn-lg">
        <i class="bi bi-shop"></i> Tiếp Tục Mua Sắm
      </a>
      <button onclick="window.print();" class="btn btn-outline btn-lg">
        <i class="bi bi-printer"></i> In Hóa Đơn
      </button>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
