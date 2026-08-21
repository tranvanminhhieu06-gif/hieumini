<?php
/**
 * HieuMini Admin — Bảng điều khiển
 */

declare(strict_types=1);
require __DIR__ . '/guard.php';
require __DIR__ . '/layout.php';

$stats = [
    'projects'  => (int) Database::scalar("SELECT COUNT(*) FROM projects WHERE status = 'published'"),
    'views'     => (int) Database::scalar('SELECT COALESCE(SUM(views), 0) FROM projects'),
    'messages'  => (int) Database::scalar('SELECT COUNT(*) FROM messages'),
    'unread'    => (int) Database::scalar('SELECT COUNT(*) FROM messages WHERE is_read = 0'),
];

/* Lượt truy cập 14 ngày gần nhất, phục vụ biểu đồ cột */
$rows = Database::all(
    "SELECT DATE(created_at) AS d, COUNT(*) AS total
       FROM visit_logs
      WHERE created_at >= (CURDATE() - INTERVAL 13 DAY)
      GROUP BY DATE(created_at)"
);
$byDate = array_column($rows, 'total', 'd');

$chart = [];
for ($i = 13; $i >= 0; $i--) {
    $day = date('Y-m-d', strtotime("-$i day"));
    $chart[] = ['label' => date('d/m', strtotime($day)), 'value' => (int) ($byDate[$day] ?? 0)];
}
$chartMax = max(1, max(array_column($chart, 'value')));

$topProjects = Database::all(
    "SELECT code, name, views, accent_from FROM projects ORDER BY views DESC LIMIT 6"
);

$latestMessages = Database::all(
    'SELECT id, name, subject, is_read, created_at FROM messages ORDER BY created_at DESC LIMIT 5'
);

admin_head('Bảng điều khiển');
?>

<div class="kpi-grid">
  <?php
  $kpis = [
      ['grid',  'Dự án đang hiển thị', $stats['projects'], 'var(--indigo-600)'],
      ['eye',   'Tổng lượt xem',       $stats['views'],    'var(--violet-600)'],
      ['inbox', 'Tin nhắn',            $stats['messages'], 'var(--cyan-500)'],
      ['mail',  'Tin chưa đọc',        $stats['unread'],   'var(--amber-500)'],
  ];
  foreach ($kpis as [$ico, $label, $value, $color]): ?>
    <div class="kpi" data-reveal>
      <span class="kpi-ico" style="background:<?= $color ?>"><?= icon($ico) ?></span>
      <div>
        <div class="kpi-value" data-count="<?= (int) $value ?>">0</div>
        <div class="kpi-label"><?= e($label) ?></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

<div class="admin-grid">
  <section class="panel" data-reveal>
    <h3><?= icon('chart', 'ico ico-sm') ?> Lượt truy cập 14 ngày gần nhất</h3>
    <div class="bar-chart" role="img"
         aria-label="Biểu đồ cột lượt truy cập 14 ngày, cao nhất <?= $chartMax ?> lượt">
      <?php foreach ($chart as $c): ?>
        <div class="bar-col">
          <div class="bar" style="height: <?= max(3, round($c['value'] / $chartMax * 100)) ?>%"
               title="<?= e($c['label']) ?>: <?= $c['value'] ?> lượt">
            <span><?= $c['value'] ?></span>
          </div>
          <small><?= e($c['label']) ?></small>
        </div>
      <?php endforeach; ?>
    </div>
  </section>

  <section class="panel" data-reveal>
    <h3><?= icon('star', 'ico ico-sm') ?> Dự án được xem nhiều</h3>
    <?php $topMax = max(1, (int) ($topProjects[0]['views'] ?? 1)); ?>
    <ul class="rank-list">
      <?php foreach ($topProjects as $i => $p): ?>
        <li>
          <span class="rank-no"><?= $i + 1 ?></span>
          <div class="rank-body">
            <b><?= e($p['name']) ?></b>
            <div class="rank-bar">
              <i style="width: <?= round(((int) $p['views']) / $topMax * 100) ?>%; background: <?= e($p['accent_from']) ?>"></i>
            </div>
          </div>
          <span class="rank-value"><?= num($p['views']) ?></span>
        </li>
      <?php endforeach; ?>
    </ul>
  </section>
</div>

<section class="panel" data-reveal>
  <h3><?= icon('inbox', 'ico ico-sm') ?> Tin nhắn mới nhất</h3>
  <?php if (!$latestMessages): ?>
    <p style="margin:0">Chưa có tin nhắn nào từ khách truy cập.</p>
  <?php else: ?>
    <div style="overflow-x:auto">
      <table class="admin-table">
        <thead>
          <tr><th>Người gửi</th><th>Tiêu đề</th><th>Thời gian</th><th>Trạng thái</th></tr>
        </thead>
        <tbody>
          <?php foreach ($latestMessages as $m): ?>
            <tr>
              <td><b><?= e($m['name']) ?></b></td>
              <td><?= e(excerpt($m['subject'], 60)) ?></td>
              <td><?= e(vn_date($m['created_at'], true)) ?></td>
              <td>
                <span class="badge <?= $m['is_read'] ? 'badge--muted' : 'badge--warn' ?>">
                  <?= $m['is_read'] ? 'Đã đọc' : 'Chưa đọc' ?>
                </span>
              </td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
    <a class="btn btn--ghost btn--sm" style="margin-top:16px" href="<?= e(url('admin/messages.php')) ?>">
      <?= icon('arrow', 'ico ico-sm') ?> Xem tất cả tin nhắn
    </a>
  <?php endif; ?>
</section>

<?php admin_foot(); ?>
