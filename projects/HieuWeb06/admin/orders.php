<?php
/**
 * HieuMini Admin - Quản lý đơn hàng
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $id = (int)($_POST['id'] ?? 0);
    $status = $_POST['status'] ?? '';
    if (in_array($status, ['pending', 'paid', 'delivered', 'cancelled'], true)) {
        $pdo->prepare('UPDATE orders SET status = ? WHERE id = ?')->execute([$status, $id]);
        flash('Đã cập nhật trạng thái đơn hàng.');
    }
    redirect('admin/orders.php' . (($_POST['back'] ?? '') !== '' ? '' : ''));
}

$filter = (string)($_GET['status'] ?? '');
$sql = 'SELECT o.*, (SELECT COUNT(*) FROM order_items oi WHERE oi.order_id = o.id) AS items FROM orders o';
$params = [];
if (in_array($filter, ['pending', 'paid', 'delivered', 'cancelled'], true)) {
    $sql .= ' WHERE o.status = ?';
    $params[] = $filter;
}
$sql .= ' ORDER BY o.created_at DESC';
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$orders = $stmt->fetchAll();

$statusLabel = ['pending' => 'Chờ xác nhận', 'paid' => 'Đã thanh toán', 'delivered' => 'Đã bàn giao', 'cancelled' => 'Đã huỷ'];
$statusClass = ['pending' => 'badge--wait', 'paid' => 'badge--new', 'delivered' => 'badge--ok', 'cancelled' => 'badge--off'];

$adminTitle = 'Quản lý đơn hàng';
require __DIR__ . '/includes/header.php';
?>

<div class="tabs" style="border:0;margin-bottom:var(--sp-4)">
  <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/orders.php')) ?>">Tất cả</a>
  <?php foreach ($statusLabel as $key => $label): ?>
    <a class="btn btn--ghost btn--sm" style="<?= $filter === $key ? 'border-color:var(--border-strong);color:var(--fg)' : '' ?>"
       href="<?= e(url('admin/orders.php?status=' . $key)) ?>"><?= e($label) ?></a>
  <?php endforeach; ?>
</div>

<div class="table-wrap">
  <table class="data">
    <caption class="sr-only">Danh sách đơn hàng</caption>
    <thead>
      <tr>
        <th scope="col">Mã đơn</th><th scope="col">Khách hàng</th><th scope="col">Liên hệ</th>
        <th scope="col">Số mục</th><th scope="col">Tổng tiền</th><th scope="col">Ngày đặt</th><th scope="col">Trạng thái</th>
      </tr>
    </thead>
    <tbody>
      <?php foreach ($orders as $o): ?>
        <tr>
          <td><a href="<?= e(url('admin/order-detail.php?id=' . $o['id'])) ?>"><strong><?= e($o['order_code']) ?></strong></a></td>
          <td><?= e($o['customer_name']) ?></td>
          <td style="font-size:var(--fs-xs)"><?= e($o['email']) ?><br><?= e($o['phone']) ?></td>
          <td><?= (int)$o['items'] ?></td>
          <td><strong><?= money($o['total']) ?></strong></td>
          <td><?= e(vn_date($o['created_at'], true)) ?></td>
          <td>
            <form method="post" style="display:flex;gap:6px;justify-content:flex-end;align-items:center">
              <?= csrf_field() ?>
              <input type="hidden" name="id" value="<?= (int)$o['id'] ?>">
              <label class="sr-only" for="st<?= (int)$o['id'] ?>">Trạng thái đơn <?= e($o['order_code']) ?></label>
              <select class="input" id="st<?= (int)$o['id'] ?>" name="status" onchange="this.form.submit()" style="min-width:170px">
                <?php foreach ($statusLabel as $key => $label): ?>
                  <option value="<?= e($key) ?>" <?= $o['status'] === $key ? 'selected' : '' ?>><?= e($label) ?></option>
                <?php endforeach; ?>
              </select>
              <noscript><button class="btn btn--ghost btn--sm" type="submit">Lưu</button></noscript>
            </form>
          </td>
        </tr>
      <?php endforeach; ?>
      <?php if (!$orders): ?>
        <tr><td colspan="7" style="text-align:center;color:var(--fg-muted)">Không có đơn hàng nào.</td></tr>
      <?php endif; ?>
    </tbody>
  </table>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
