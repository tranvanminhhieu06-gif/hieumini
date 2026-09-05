<?php
/**
 * Thẻ sách dùng lại ở trang chủ, kho sách, trang tác giả và sách liên quan.
 * Yêu cầu biến $b là một dòng lấy từ book_select_sql().
 *
 * width/height khai báo sẵn trên thẻ img để trình duyệt chừa đúng chỗ trước
 * khi ảnh tải xong — nếu thiếu, cả lưới sẽ giật xuống khi ảnh vào (CLS).
 */
?>
<article class="book-card" data-reveal>
  <div class="book-cover">
    <img src="<?= e(cover_url($b['cover'])) ?>"
         alt="Bìa sách <?= e($b['title']) ?> của <?= e($b['author_name']) ?>"
         width="600" height="900" loading="lazy" decoding="async">
  </div>
  <h3><a href="<?= e(url('book.php?s=' . $b['slug'])) ?>"><?= e($b['title']) ?></a></h3>
  <p class="book-author"><?= e($b['author_name']) ?></p>
  <p style="margin:var(--space-2) 0 0;display:flex;align-items:center;gap:var(--space-3);font-size:var(--fs-xs);color:var(--fg-subtle)">
    <span><?= e(format_year(isset($b['published_year']) ? (int) $b['published_year'] : null)) ?></span>
    <?php if (!empty($b['rating_count'])): ?>
      <span aria-hidden="true">·</span>
      <span><?= e(number_format((float) $b['rating_avg'], 1)) ?>/5 · <?= (int) $b['rating_count'] ?> đánh giá</span>
    <?php endif; ?>
  </p>
</article>
