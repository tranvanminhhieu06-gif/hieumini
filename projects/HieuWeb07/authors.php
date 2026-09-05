<?php
/**
 * Danh sách tác giả, kèm số đầu sách mỗi người có trong kho.
 */
declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$authors = db_all(
    'SELECT a.*, COUNT(b.id) AS book_count
       FROM authors a LEFT JOIN books b ON b.author_id = a.id
      GROUP BY a.id
      HAVING book_count > 0
      ORDER BY book_count DESC, a.name ASC'
);

$pageTitle = 'Tác giả — ' . SITE_NAME;
$pageDesc  = 'Danh sách ' . count($authors) . ' tác giả có sách trong thư viện HieuMini, kèm tiểu sử ngắn và số đầu sách.';

require __DIR__ . '/includes/header.php';
?>

<section class="section section--tight">
  <div class="container">
    <nav aria-label="Đường dẫn" style="margin-bottom:var(--space-5);font-size:var(--fs-sm);color:var(--fg-subtle)">
      <a href="<?= e(url('index.php')) ?>">Trang chủ</a><span aria-hidden="true"> / </span><span>Tác giả</span>
    </nav>

    <h1 style="font-size:var(--fs-3xl)" data-split>Tác giả trong thư viện</h1>
    <p class="lead" style="margin-bottom:var(--space-8)">
      <?= count($authors) ?> tác giả, xếp theo số đầu sách hiện có trong kho.
    </p>

    <div style="display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:var(--space-5)">
      <?php foreach ($authors as $a): ?>
        <article data-reveal style="position:relative;padding:var(--space-6);border:1px solid var(--border);
                 border-radius:var(--r-md);background:var(--surface)">
          <div style="display:flex;align-items:baseline;justify-content:space-between;gap:var(--space-3)">
            <h2 style="font-size:var(--fs-xl);margin:0">
              <a href="<?= e(url('author.php?s=' . $a['slug'])) ?>" style="color:var(--fg)">
                <?= e($a['name']) ?>
                <span style="position:absolute;inset:0"></span>
              </a>
            </h2>
            <span class="badge badge--muted"><?= (int) $a['book_count'] ?> cuốn</span>
          </div>
          <p style="margin:var(--space-2) 0 var(--space-3);font-size:var(--fs-sm);color:var(--fg-subtle)">
            <?= e($a['country']) ?><?= $a['birth_year'] ? ' · ' . e(format_year((int) $a['birth_year'])) : '' ?>
          </p>
          <p style="margin:0;font-size:var(--fs-sm)"><?= e(excerpt($a['bio'], 130)) ?></p>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
