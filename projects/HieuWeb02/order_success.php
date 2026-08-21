<?php
$page_title = 'Đặt Hàng Thành Công';
require_once __DIR__ . '/includes/header.php';

$order_code = isset($_GET['code']) ? trim($_GET['code']) : 'HM-2026-DEMO';
$order = null;

if ($pdo) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM orders WHERE order_code = ?");
        $stmt->execute([$order_code]);
        $order = $stmt->fetch();
    } catch (Exception $e) {}
}

if (!$order && isset($_SESSION['last_order'])) {
    $order = $_SESSION['last_order'];
}
?>

<main class="container" style="margin: 40px auto 70px; max-width: 800px;">
    <div class="glass-panel" style="padding: 40px 30px; text-align: center;">
        
        <!-- Icon Success -->
        <div style="width: 80px; height: 80px; border-radius: 50%; background: rgba(16, 185, 129, 0.2); border: 2px solid var(--success); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px;">
            <i class="fa-solid fa-check" style="font-size: 2.5rem; color: var(--success);"></i>
        </div>

        <h1 style="font-size: 1.8rem; font-weight: 800; color: #fff; margin-bottom: 8px;">CẢM ƠN BẠN ĐÃ ĐẶT HÀNG!</h1>
        <p style="color: var(--text-muted); font-size: 1rem; margin-bottom: 24px;">Đơn hàng của bạn đã được tiếp nhận và nhân viên HieuMini sẽ liên hệ xác nhận trong ít phút.</p>

        <!-- Mã đơn hàng -->
        <div style="background: rgba(99, 102, 241, 0.1); border: 1px dashed var(--primary); padding: 14px 20px; border-radius: var(--radius-md); display: inline-block; margin-bottom: 30px;">
            <span style="color: var(--text-muted); font-size: 0.9rem;">Mã đơn hàng của bạn: </span>
            <strong style="color: var(--accent); font-size: 1.1rem; letter-spacing: 0.5px;"><?php echo htmlspecialchars($order_code); ?></strong>
        </div>

        <!-- Thông tin đơn hàng -->
        <div style="background: rgba(255, 255, 255, 0.02); border: var(--border-glass); border-radius: var(--radius-md); padding: 24px; text-align: left; margin-bottom: 30px;">
            <h3 style="font-size: 1.1rem; font-weight: 700; color: #fff; margin-bottom: 16px; border-bottom: var(--border-glass); padding-bottom: 10px;">
                <i class="fa-solid fa-file-invoice" style="color: var(--primary);"></i> Chi Tiết Đơn Hàng
            </h3>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 14px; font-size: 0.92rem;">
                <div><span style="color: var(--text-muted);">Người nhận:</span> <strong style="color: #fff;"><?php echo htmlspecialchars($order['customer_name'] ?? 'Khách hàng'); ?></strong></div>
                <div><span style="color: var(--text-muted);">Số điện thoại:</span> <strong style="color: #fff;"><?php echo htmlspecialchars($order['customer_phone'] ?? ''); ?></strong></div>
                <div style="grid-column: span 2;"><span style="color: var(--text-muted);">Địa chỉ giao:</span> <strong style="color: #fff;"><?php echo htmlspecialchars($order['customer_address'] ?? ''); ?></strong></div>
                <div><span style="color: var(--text-muted);">Phương thức:</span> <strong style="color: var(--accent);"><?php echo ($order['payment_method'] ?? '') === 'bank_transfer' ? 'Chuyển khoản VietQR' : 'Thanh toán COD'; ?></strong></div>
                <div><span style="color: var(--text-muted);">Tổng thanh toán:</span> <strong style="color: #f43f5e; font-size: 1.1rem;"><?php echo format_currency($order['total_amount'] ?? 0); ?></strong></div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div style="display: flex; justify-content: center; gap: 16px; flex-wrap: wrap;">
            <a href="index.php" class="btn btn-primary">
                <i class="fa-solid fa-house"></i> Về trang chủ
            </a>
            <a href="my_orders.php" class="btn btn-outline">
                <i class="fa-solid fa-clock-rotate-left"></i> Tra cứu đơn hàng
            </a>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
