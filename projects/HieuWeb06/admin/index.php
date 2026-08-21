<?php
/**
 * HieuMini Admin - Tổng quan
 */
require_once dirname(__DIR__) . '/includes/config.php';
require_admin();

$kpi = [
    'revenue' => (float)$pdo->query('SELECT IFNULL(SUM(total),0) FROM orders WHERE status IN ("paid","delivered")')->fetchColumn(),
    'orders'  => (int)$pdo->query('SELECT COUNT(*) FROM orders')->fetchColumn(),
    'pending' => (int)$pdo->query('SELECT COUNT(*) FROM orders WHERE status = "pending"')->fetchColumn(),
    'users'   => (int)$pdo->query('SELECT COUNT(*) FROM users WHERE role = "user"')->fetchColumn(),
    'projects'=> (int)$pdo->query('SELECT COUNT(*) FROM projects WHERE status = 1')->fetchColumn(),
    'contacts'=> (int)$pdo->query('SELECT COUNT(*) FROM contacts WHERE status = "new"')->fetchColumn(),
];

// Doanh thu 6 tháng gần nhất
$revenueByMonth = [];
for ($i = 5; $i >= 0; $i--) {
    $key = date('Y-m', strtotime("-$i month"));
    $revenueByMonth[$key] = 0.0;
}
$rows = $pdo->query('SELECT DATE_FORMAT(created_at, "%Y-%m") AS m, SUM(total) AS s
                     FROM orders WHERE status IN ("paid","delivered")
                     GROUP BY m')->fetchAll();
foreach ($rows as $r) {
    if (isset($revenueByMonth[$r['m']])) {
        $revenueByMonth[$r['m']] = (float)$r['s'];
    }
}
$maxRevenue = max(1, max($revenueByMonth));

$topProjects = $pdo->query('SELECT title, sales, rating_avg, views FROM projects
                            ORDER BY sales DESC LIMIT 5')->fetchAll();

$recentOrders = $pdo->query('SELECT * FROM orders ORDER BY created_at DESC LIMIT 6')->fetchAll();

$statusLabel = ['pending' => 'Chờ xác nhận', 'paid' => 'Đã thanh toán', 'delivered' => 'Đã bàn giao', 'cancelled' => 'Đã huỷ'];
$statusClass = ['pending' => 'badge--wait', 'paid' => 'badge--new', 'delivered' => 'badge--ok', 'cancelled' => 'badge--off'];

$adminTitle = 'Tổng quan hệ thống';
require __DIR__ . '/includes/header.php';
?>

<div class="grid grid--3 stagger" style="margin-bottom:var(--sp-5)">
  <div class="glass kpi reveal">
    <div class="kpi__label">Doanh thu đã ghi nhận</div>
    <div class="kpi__value"><?= money($kpi['revenue']) ?></div>
    <div class="kpi__trend">Từ đơn đã thanh toán và đã bàn giao</div>
  </div>
  <div class="glass kpi reveal">
    <div class="kpi__label">Tổng đơn hàng</div>
    <div class="kpi__value"><?= num($kpi['orders']) ?></div>
    <div class="kpi__trend" style="color:var(--warning)"><?= $kpi['pending'] ?> đơn chờ xác nhận</div>
  </div>
  <div class="glass kpi reveal">
    <div class="kpi__label">Thành viên</div>
    <div class="kpi__value"><?= num($kpi['users']) ?></div>
    <div class="kpi__trend"><?= $kpi['projects'] ?> dự án đang bán</div>
  </div>
</div>

<div class="grid grid--2" style="margin-bottom:var(--sp-5)">
  <section class="glass reveal" style="padding:var(--sp-5)">
    <h2 style="font-size:var(--fs-lg)">Doanh thu 6 tháng gần nhất</h2>
    <div class="bar-chart" role="img" aria-label="Biểu đồ cột doanh thu 6 tháng gần nhất">
      <?php $d = 0; foreach ($revenueByMonth as $month => $value): $d++; ?>
        <div class="bar-chart__col">
          <span style="font-size:11px;color:var(--fg-muted)"><?= $value > 0 ? number_format($value / 1000000, 1) . 'tr' : '' ?></span>
          <div class="bar-chart__bar" style="height:<?= max(3, $value / $maxRevenue * 100) ?>%;animation-delay:<?= $d * 80 ?>ms"></div>
          <span class="bar-chart__label"><?= e(date('m/y', strtotime($month . '-01'))) ?></span>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="glass reveal" style="padding:var(--sp-5)">
    <h2 style="font-size:var(--fs-lg)">Top 5 dự án bán chạy</h2>
    <div class="table-wrap" style="border:0">
      <table class="data" style="min-width:auto">
        <caption class="sr-only">Danh sách 5 dự án bán chạy nhất</caption>
        <thead><tr><th scope="col">Dự án</th><th scope="col">Lượt xem</th><th scope="col">Đã bán</th></tr></thead>
        <tbody>
          <?php foreach ($topProjects as $t): ?>
            <tr>
              <td><?= e(excerpt($t['title'], 42)) ?></td>
              <td><?= num((int)$t['views']) ?></td>
              <td><strong><?= num((int)$t['sales']) ?></strong></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>
</div>

<section class="glass reveal" style="padding:var(--sp-5)">
  <div style="display:flex;justify-content:space-between;align-items:center;gap:12px;flex-wrap:wrap;margin-bottom:var(--sp-3)">
    <h2 style="font-size:var(--fs-lg);margin:0">Đơn hàng gần đây</h2>
    <a class="btn btn--ghost btn--sm" href="<?= e(url('admin/orders.php')) ?>">Xem tất cả</a>
  </div>
  <div class="table-wrap">
    <table class="data">
      <caption class="sr-only">Sáu đơn hàng gần nhất</caption>
      <thead>
        <tr><th scope="col">Mã đơn</th><th scope="col">Khách hàng</th><th scope="col">Ngày đặt</th><th scope="col">Trạng thái</th><th scope="col">Tổng tiền</th></tr>
      </thead>
      <tbody>
        <?php foreach ($recentOrders as $o): ?>
          <tr>
            <td><a href="<?= e(url('admin/order-detail.php?id=' . $o['id'])) ?>"><?= e($o['order_code']) ?></a></td>
            <td><?= e($o['customer_name']) ?></td>
            <td><?= e(vn_date($o['created_at'], true)) ?></td>
            <td><span class="badge <?= $statusClass[$o['status']] ?>"><?= e($statusLabel[$o['status']]) ?></span></td>
            <td><strong><?= money($o['total']) ?></strong></td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</section>

<?php if ($kpi['contacts'] > 0): ?>
  <p style="margin-top:var(--sp-5)">
    <a class="btn btn--primary" href="<?= e(url('admin/contacts.php')) ?>">
      Có <?= $kpi['contacts'] ?> liên hệ mới cần xử lý →
    </a>
  </p>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
