<?php
/** Trang 404 — luôn mở lối đi tiếp thay vì để người dùng mắc kẹt. */
declare(strict_types=1);
require __DIR__ . '/config/config.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

http_response_code(404);
$pageTitle = 'Không tìm thấy trang — ' . SITE_NAME;
$suggest = db_all(book_select_sql() . ' ORDER BY b.views DESC LIMIT 4');
require __DIR__ . '/includes/header.php';
?>
<section class="section">
  <div class="container">
    <div style="max-width:60ch">
      <span class="eyebrow">Lỗi 404</span>
      <h1 data-split>Trang này không tồn tại</h1>
      <p class="lead">Có thể đường dẫn đã cũ, hoặc bạn gõ nhầm một ký tự. Thử vài cuốn được xem nhiều nhất bên dưới.</p>
      <div style="display:flex;gap:var(--space-3);flex-wrap:wrap;margin-top:var(--space-5)">
        <a class="btn btn--primary" href="<?= e(url('books.php')) ?>">Vào kho sách</a>
        <a class="btn btn--ghost" href="<?= e(url('index.php')) ?>">Về trang chủ</a>
      </div>
    </div>
    <hr class="rule">
    <div class="book-grid">
      <?php foreach ($suggest as $b) { require __DIR__ . '/includes/book-card.php'; } ?>
    </div>
  </div>
</section>
<?php require __DIR__ . '/includes/footer.php'; ?>
