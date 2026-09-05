<?php
/**
 * Trang chủ — giới thiệu thư viện, sách nổi bật và các thể loại.
 */
declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$stats    = site_stats();
$featured = db_all(book_select_sql() . ' WHERE b.is_featured = 1 ORDER BY b.views DESC LIMIT 6');
$newest   = db_all(book_select_sql() . ' ORDER BY b.published_year DESC, b.id DESC LIMIT 6');
$cats     = db_all('SELECT c.*, COUNT(b.id) AS book_count
                    FROM categories c LEFT JOIN books b ON b.category_id = c.id
                    GROUP BY c.id ORDER BY c.sort_order');

// Ba bìa xếp chồng ở banner — lấy sách nổi bật nhiều lượt xem nhất
$stack = array_slice($featured, 0, 3);

$pageTitle = SITE_NAME . ' — ' . SITE_TAGLINE;
$pageDesc  = SITE_DESC;

// Dữ liệu có cấu trúc giúp Google hiểu đây là website có ô tìm kiếm nội bộ
$jsonLd = [
    '@context' => 'https://schema.org',
    '@type'    => 'WebSite',
    'name'     => SITE_NAME,
    'url'      => abs_url(),
    'description' => SITE_DESC,
    'inLanguage'  => 'vi-VN',
    'potentialAction' => [
        '@type'       => 'SearchAction',
        'target'      => ['@type' => 'EntryPoint', 'urlTemplate' => abs_url('books.php') . '?q={search_term_string}'],
        'query-input' => 'required name=search_term_string',
    ],
];

require __DIR__ . '/includes/header.php';
?>

<section class="hero">
  <div class="container">
    <div class="hero-grid">
      <div class="hero-copy">
        <span class="eyebrow">Đồ án lập trình Web · PHP &amp; MySQL</span>
        <h1>Một thư viện nhỏ,<br>đọc kỹ <em>từng cuốn</em>.</h1>
        <p class="lead">
          <?= format_number($stats['books']) ?> đầu sách được chọn lọc qua sáu thể loại, mỗi cuốn có
          thông tin xuất bản đầy đủ, tóm tắt nội dung và đánh giá thật của bạn đọc. Không quảng cáo,
          không giỏ hàng — chỉ có sách và chỗ để tra cứu.
        </p>

        <div class="hero-cta" style="display:flex;gap:var(--space-3);flex-wrap:wrap;margin-top:var(--space-6)">
          <a class="btn btn--primary" href="<?= e(url('books.php')) ?>">
            <?= icon('grid') ?> Vào kho sách
          </a>
          <a class="btn btn--ghost" href="<?= e(url('about.php')) ?>">
            <?= icon('book') ?> Về dự án này
          </a>
        </div>

        <div class="hero-meta">
          <div><b><?= format_number($stats['books']) ?></b><span>Đầu sách</span></div>
          <div><b><?= format_number($stats['authors']) ?></b><span>Tác giả</span></div>
          <div><b><?= format_number($stats['categories']) ?></b><span>Thể loại</span></div>
          <div><b><?= format_number($stats['reviews']) ?></b><span>Đánh giá</span></div>
        </div>
      </div>

      <?php if ($stack): ?>
      <div class="hero-stack" aria-hidden="true">
        <?php foreach ($stack as $b): ?>
          <img src="<?= e(cover_url($b['cover'])) ?>" alt="" width="600" height="900" loading="eager" decoding="async">
        <?php endforeach; ?>
      </div>
      <?php endif; ?>
    </div>
  </div>
</section>

<section class="section section--soft">
  <div class="container">
    <div class="section-head" data-reveal>
      <div>
        <span class="eyebrow">Được chọn</span>
        <h2 data-split>Những cuốn đáng đọc trước tiên</h2>
        <p>Sáu đầu sách có lượt xem cao nhất trong kho, trải đều các thể loại.</p>
      </div>
      <a class="btn btn--ghost" href="<?= e(url('books.php')) ?>">Xem tất cả <?= icon('arrow') ?></a>
    </div>

    <div class="book-grid">
      <?php foreach ($featured as $b): ?>
        <?php require __DIR__ . '/includes/book-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head" data-reveal>
      <div>
        <span class="eyebrow">Duyệt theo</span>
        <h2 data-split>Sáu thể loại trong kho</h2>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:var(--space-4)">
      <?php foreach ($cats as $c): ?>
        <a href="<?= e(url('books.php?the-loai=' . $c['slug'])) ?>" data-reveal
           style="display:block;padding:var(--space-6);border:1px solid var(--border);border-radius:var(--r-md);
                  background:var(--surface);transition:border-color var(--t-base),background var(--t-base)"
           onmouseover="this.style.borderColor='var(--accent)'" onmouseout="this.style.borderColor='var(--border)'">
          <span class="badge" style="color:<?= e($c['accent']) ?>;border-color:<?= e($c['accent']) ?>44">
            <?= format_number((int) $c['book_count']) ?> cuốn
          </span>
          <h3 style="margin:var(--space-4) 0 var(--space-2);color:var(--fg)"><?= e($c['name']) ?></h3>
          <p style="margin:0;font-size:var(--fs-sm)"><?= e(excerpt($c['description'], 96)) ?></p>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section section--soft">
  <div class="container">
    <div class="section-head" data-reveal>
      <div>
        <span class="eyebrow">Mới cập nhật</span>
        <h2 data-split>Bổ sung gần đây</h2>
        <p>Sắp theo năm xuất bản, cuốn mới nhất đứng trước.</p>
      </div>
    </div>

    <div class="book-grid">
      <?php foreach ($newest as $b): ?>
        <?php require __DIR__ . '/includes/book-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
