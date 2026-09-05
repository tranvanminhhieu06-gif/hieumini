<?php
/** Bảng điều khiển — số liệu tổng quan và những việc đang chờ xử lý. */
declare(strict_types=1);

require __DIR__ . '/guard.php';
$admin = require_admin();

$stats = [
    'books'    => (int) db_value('SELECT COUNT(*) FROM books'),
    'authors'  => (int) db_value('SELECT COUNT(*) FROM authors'),
    'pending'  => (int) db_value('SELECT COUNT(*) FROM reviews WHERE is_approved = 0'),
    'unread'   => (int) db_value('SELECT COUNT(*) FROM messages WHERE is_read = 0'),
    'views'    => (int) db_value('SELECT COALESCE(SUM(views), 0) FROM books'),
];

$topBooks = db_all(book_select_sql() . ' ORDER BY b.views DESC LIMIT 5');
$byCat    = db_all('SELECT c.name, COUNT(b.id) AS n FROM categories c
                    LEFT JOIN books b ON b.category_id = c.id
                    GROUP BY c.id ORDER BY n DESC');
$maxCat   = max(array_map(static fn($r) => (int) $r['n'], $byCat) ?: [1]);

$adminTitle = 'Bảng điều khiển';
require __DIR__ . '/layout.php';
?>

<div class="admin-head">
  <div>
    <h1 style="font-size:var(--fs-2xl);margin:0">Bảng điều khiển</h1>
    <p style="margin:var(--space-2) 0 0">Chào <?= e($admin['full_name'] ?: $admin['username']) ?>, hôm nay là <?= date('d/m/Y') ?>.</p>
  </div>
  <a class="btn btn--primary btn-sm" href="<?= e(admin_url('book_form.php')) ?>">+ Thêm sách</a>
</div>

<div class="stat-grid">
  <div class="stat-card"><b><?= format_number($stats['books']) ?></b><span>Đầu sách</span></div>
  <div class="stat-card"><b><?= format_number($stats['authors']) ?></b><span>Tác giả</span></div>
  <div class="stat-card"><b><?= format_number($stats['views']) ?></b><span>Lượt xem</span></div>
  <div class="stat-card" <?= $stats['pending'] ? 'style="border-color:var(--accent)"' : '' ?>>
    <b><?= format_number($stats['pending']) ?></b><span>Đánh giá chờ duyệt</span>
  </div>
  <div class="stat-card" <?= $stats['unread'] ? 'style="border-color:var(--accent)"' : '' ?>>
    <b><?= format_number($stats['unread']) ?></b><span>Tin nhắn chưa đọc</span>
  </div>
</div>

<div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(320px,1fr));gap:var(--space-7)">
  <section>
    <h2 style="font-size:var(--fs-lg)">Sách được xem nhiều nhất</h2>
    <div class="table-wrap">
      <table class="data-table">
        <thead><tr><th></th><th>Tựa sách</th><th>Lượt xem</th></tr></thead>
        <tbody>
          <?php foreach ($topBooks as $b): ?>
            <tr>
              <td><img src="<?= e(cover_url($b['cover'])) ?>" alt=""></td>
              <td>
                <a href="<?= e(url('book.php?s=' . $b['slug'])) ?>" target="_blank" rel="noopener"><?= e($b['title']) ?></a>
                <div style="color:var(--fg-subtle);font-size:var(--fs-xs)"><?= e($b['author_name']) ?></div>
              </td>
              <td><?= format_number((int) $b['views']) ?></td>
            </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </section>

  <section>
    <h2 style="font-size:var(--fs-lg)">Phân bố theo thể loại</h2>
    <?php foreach ($byCat as $r): ?>
      <div style="margin-bottom:var(--space-4)">
        <div style="display:flex;justify-content:space-between;font-size:var(--fs-sm);margin-bottom:var(--space-2)">
          <span><?= e($r['name']) ?></span>
          <span style="color:var(--fg-subtle)"><?= (int) $r['n'] ?> cuốn</span>
        </div>
        <?php /* Thanh tỉ lệ kèm nhãn số ở trên — không bắt người xem đoán độ dài */ ?>
        <div style="height:6px;background:var(--surface);border-radius:99px;overflow:hidden">
          <div style="height:100%;width:<?= $maxCat ? round((int) $r['n'] / $maxCat * 100) : 0 ?>%;background:var(--accent)"></div>
        </div>
      </div>
    <?php endforeach; ?>
  </section>
</div>

<?php require __DIR__ . '/layout_end.php'; ?>
