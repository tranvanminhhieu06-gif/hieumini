<?php
/**
 * HieuMini Admin - Chi tiết đơn hàng
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

$id = (int)($_GET['id'] ?? 0);
$stmt = $pdo->prepare('SELECT * FROM orders WHERE id = ?');
$stmt->execute([$id]);
$order = $stmt->fetch();

if (!$order) {
    flash('Không tìm thấy đơn hàng.', 'error');
    redirect('admin/orders.php');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $status = $_POST['status'] ?? '';
    if (in_array($status, ['pending', 'paid', 'delivered', 'cancelled'], true)) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, $id]);
        flash('Đã cập nhật trạng thái đơn ' . $order['order_code'] . '.');
    }
    redirect('admin/order-detail.php?id=' . $id);
}

$items = $pdo->prepare('SELECT oi.*, p.slug FROM order_items oi LEFT JOIN projects p ON p.id = oi.project_id WHERE oi.order_id = ?');
$items->execute([$id]);
$items = $items->fetchAll();

$statusLabel = ['pending' => 'Chờ xác nhận', 'paid' => 'Đã thanh toán', 'delivered' => 'Đã bàn giao', 'cancelled' => 'Đã huỷ'];
$payLabel = ['bank' => 'Chuyển khoản ngân hàng', 'momo' => 'Ví MoMo', 'cod' => 'Thanh toán khi nhận'];

$adminTitle = 'Đơn hàng ' . $order['order_code'];
$adminActions = '<a class="btn btn--ghost" href="' . e(url('admin/orders.php')) . '">← Về danh sách</a>';
require __DIR__ . '/includes/header.php';
?>

<div class="grid grid--2" style="align-items:start">
  <section class="glass" style="padding:var(--sp-5)">
    <h2 style="font-size:var(--fs-lg)">Sản phẩm trong đơn</h2>
    <div class="table-wrap">
      <table class="data" style="min-width:auto">
        <caption class="sr-only">Chi tiết các dự án trong đơn hàng</caption>
        <thead><tr><th scope="col">Dự án</th><th scope="col">Giấy phép</th><th scope="col">Thành tiền</th></tr></thead>
        <tbody>
          <?php foreach ($items as $it): ?>
            <tr>
              <td>
                <?php if ($it['slug']): ?>
                  <a href="<?= e(url('project-detail.php?slug=' . $it['slug'])) ?>" target="_blank" rel="noopener"><?= e($it['title']) ?></a>
                <?php else: ?><?= e($it['title']) ?><?php endif; ?>
              </td>
              <td><?= e(license_label($it['license'])) ?></td>
              <td><?= money($it['price']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
        <tfoot>
          <tr><th scope="row" colspan="2">Tạm tính</th><td><?= money($order['subtotal']) ?></td></tr>
          <tr><th scope="row" colspan="2">Giảm giá<?= $order['coupon_code'] ? ' (' . e($order['coupon_code']) . ')' : '' ?></th><td>- <?= money($order['discount']) ?></td></tr>
          <tr><th scope="row" colspan="2">Tổng thanh toán</th><td><strong><?= money($order['total']) ?></strong></td></tr>
        </tfoot>
      </table>
    </div>
  </section>

  <section class="glass" style="padding:var(--sp-5)">
    <h2 style="font-size:var(--fs-lg)">Thông tin khách hàng</h2>
    <div class="table-wrap" style="border:0">
      <table class="data" style="min-width:auto">
        <caption class="sr-only">Thông tin liên hệ và thanh toán</caption>
        <tbody>
          <tr><th scope="row">Họ tên</th><td><?= e($order['customer_name']) ?></td></tr>
          <tr><th scope="row">Email</th><td><a href="mailto:<?= e($order['email']) ?>"><?= e($order['email']) ?></a></td></tr>
          <tr><th scope="row">Điện thoại</th><td><a href="tel:<?= e($order['phone']) ?>"><?= e($order['phone']) ?></a></td></tr>
          <tr><th scope="row">Thanh toán</th><td><?= e($payLabel[$order['payment_method']] ?? '') ?></td></tr>
          <tr><th scope="row">Ngày đặt</th><td><?= e(vn_date($order['created_at'], true)) ?></td></tr>
          <tr><th scope="row">Ghi chú</th><td><?= e($order['note'] ?: '—') ?></td></tr>
        </tbody>
      </table>
    </div>

    <form method="post" style="margin-top:var(--sp-4)">
      <?= csrf_field() ?>
      <div class="field">
        <label for="status">Cập nhật trạng thái</label>
        <select id="status" name="status">
          <?php foreach ($statusLabel as $key => $label): ?>
            <option value="<?= e($key) ?>" <?= $order['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <button class="btn btn--primary" style="margin-top:10px" type="submit">Lưu trạng thái</button>
    </form>
  </section>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
