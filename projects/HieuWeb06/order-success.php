<?php
/**
 * HieuMini - Trang xác nhận đặt hàng thành công
 */
require_once __DIR__ . '/includes/config.php';

$code = trim((string)($_GET['code'] ?? ''));
$stmt = $pdo->prepare('SELECT * FROM orders WHERE order_code = ? LIMIT 1');
$stmt->execute([$code]);
$order = $stmt->fetch();

// Chỉ cho xem đơn vừa đặt trong phiên này, hoặc đơn của chính chủ tài khoản.
$allowed = $order && (
    ($_SESSION['last_order'] ?? '') === $code
    || (is_logged_in() && (int)$order['user_id'] === current_user_id())
    || is_admin()
);

if (!$allowed) {
    http_response_code(404);
    seo(['title' => 'Không tìm thấy đơn hàng | ' . SITE_NAME, 'robots' => 'noindex, nofollow']);
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="glass empty-state"><h1>Không tìm thấy đơn hàng</h1>'
       . '<p>Mã đơn không tồn tại hoặc bạn không có quyền xem đơn hàng này.</p>'
       . '<a class="btn btn--primary" href="' . e(url('index.php')) . '">Về trang chủ</a></div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$items = $pdo->prepare('SELECT * FROM order_items WHERE order_id = ?');
$items->execute([$order['id']]);
$items = $items->fetchAll();

seo([
    'title'  => 'Đặt hàng thành công - ' . $order['order_code'] . ' | ' . SITE_NAME,
    'robots' => 'noindex, nofollow',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container section">
  <div class="glass reveal reveal--zoom" style="max-width:760px;margin-inline:auto;padding:var(--sp-7);text-align:center">
    <div class="feature__icon" style="width:72px;height:72px;margin-inline:auto">
      <svg viewBox="0 0 24 24" aria-hidden="true" style="width:36px;height:36px;stroke:var(--success)"><path d="M20 6L9 17l-5-5"/></svg>
    </div>
    <h1>Đặt hàng thành công!</h1>
    <p style="font-size:var(--fs-lg)">
      Cảm ơn <strong><?= e($order['customer_name']) ?></strong>. Đơn hàng
      <strong class="text-grad"><?= e($order['order_code']) ?></strong> đã được ghi nhận.
    </p>
    <p>HieuMini sẽ liên hệ qua số <strong><?= e($order['phone']) ?></strong> và gửi mã nguồn tới
       <strong><?= e($order['email']) ?></strong> trong vòng 30 phút làm việc.</p>

    <div class="table-wrap" style="margin:var(--sp-5) 0;text-align:left">
      <table class="data">
        <caption class="sr-only">Chi tiết đơn hàng <?= e($order['order_code']) ?></caption>
        <thead><tr><th scope="col">Dự án</th><th scope="col">Giấy phép</th><th scope="col">Thành tiền</th></tr></thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td><?= e($it['title']) ?></td>
              <td><?= e(license_label($it['license'])) ?></td>
              <td><?= money($it['price']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><th scope="row" colspan="2">Tạm tính</th><td><?= money($order['subtotal']) ?></td></tr>
          <?php if ((float)$order['discount'] > 0): ?>
            <tr><th scope="row" colspan="2">Giảm giá<?= $order['coupon_code'] ? ' (' . e($order['coupon_code']) . ')' : '' ?></th><td>- <?= money($order['discount']) ?></td></tr>
          <?php endif; ?>
          <tr><th scope="row" colspan="2">Tổng thanh toán</th><td><strong><?= money($order['total']) ?></strong></td></tr>
        </tfoot>
      </table>
    </div>

    <?php if ($order['payment_method'] === 'bank' || $order['payment_method'] === 'momo'): ?>
      <div class="demo-hint" style="text-align:center;padding:var(--sp-5);background:rgba(99,102,241,0.06);border:1.5px dashed var(--primary);border-radius:var(--r-md);margin:var(--sp-5) 0">
        <strong style="font-size:1.05rem;color:var(--primary);display:block;margin-bottom:12px">Quét Mã VietQR Thanh Toán Tự Động 24/7</strong>
        <div style="display:inline-block;background:#fff;padding:10px;border-radius:8px;box-shadow:var(--shadow-sm);margin-bottom:12px">
          <img src="https://api.vietqr.io/image/970422-888899998888-qr_only.jpg?amount=<?= (int)$order['total'] ?>&addInfo=<?= urlencode($order['order_code']) ?>" alt="VietQR" style="width:190px;height:190px;object-fit:contain">
        </div>
        <div style="font-size:0.875rem;color:var(--fg-muted);line-height:1.7;text-align:left;max-width:400px;margin-inline:auto">
          <div>Ngân hàng: <strong>MB Bank (Ngân hàng Quân Đội)</strong></div>
          <div>Số tài khoản: <strong style="font-family:monospace;color:var(--primary)">888899998888</strong></div>
          <div>Chủ tài khoản: <strong>HIEUMINI DIGITAL SOURCE</strong></div>
          <div>Nội dung CK: <strong style="color:var(--danger);font-family:monospace"><?= e($order['order_code']) ?></strong></div>
        </div>
      </div>
    <?php endif; ?>

    <div class="hero__cta" style="justify-content:center;margin-top:var(--sp-5)">
      <a class="btn btn--primary" href="<?= e(url('projects.php')) ?>">Tiếp tục khám phá</a>
      <?php if (is_logged_in()): ?>
        <a class="btn btn--ghost" href="<?= e(url('account.php?tab=orders')) ?>">Xem đơn hàng của tôi</a>
      <?php else: ?>
        <a class="btn btn--ghost" href="<?= e(url('register.php')) ?>">Tạo tài khoản để theo dõi đơn</a>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
