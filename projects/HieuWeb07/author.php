<?php
/**
 * Trang một tác giả: tiểu sử và toàn bộ sách của người đó trong kho.
 */
declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$slug   = trim((string) ($_GET['s'] ?? ''));
$author = $slug === '' ? null : db_one('SELECT * FROM authors WHERE slug = :s', [':s' => $slug]);

if (!$author) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy tác giả — ' . SITE_NAME;
    require __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container"><div class="empty-state">' . icon('user')
        . '<h1>Không tìm thấy tác giả này</h1><p>Đường dẫn có thể đã cũ.</p>'
        . '<div class="empty-suggest"><a class="btn btn--primary" href="' . e(url('authors.php')) . '">Xem danh sách tác giả</a></div>'
        . '</div></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$books = db_all(book_select_sql() . ' WHERE a.slug = :s ORDER BY b.published_year DESC', [':s' => $slug]);

$pageTitle = $author['name'] . ' — Tác giả | ' . SITE_NAME;
$pageDesc  = excerpt($author['bio'], 155);
$canonical = abs_url('author.php?s=' . $author['slug']);

$jsonLd = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Person',
    'name'        => $author['name'],
    'description' => $author['bio'],
    'nationality' => $author['country'],
    'url'         => $canonical,
];
if ($author['birth_year']) {
    $jsonLd['birthDate'] = (string) $author['birth_year'];
}

require __DIR__ . '/includes/header.php';
?>

<section class="section section--tight">
  <div class="container">
    <nav aria-label="Đường dẫn" style="margin-bottom:var(--space-5);font-size:var(--fs-sm);color:var(--fg-subtle)">
      <a href="<?= e(url('index.php')) ?>">Trang chủ</a><span aria-hidden="true"> / </span>
      <a href="<?= e(url('authors.php')) ?>">Tác giả</a><span aria-hidden="true"> / </span>
      <span><?= e($author['name']) ?></span>
    </nav>

    <div style="max-width:70ch">
      <span class="eyebrow"><?= e($author['country']) ?><?= $author['birth_year'] ? ' · sinh ' . e(format_year((int) $author['birth_year'])) : '' ?></span>
      <h1 style="font-size:var(--fs-3xl)" data-split><?= e($author['name']) ?></h1>
      <p class="lead"><?= e($author['bio']) ?></p>
    </div>

    <hr class="rule">

    <div class="section-head">
      <div>
        <span class="eyebrow">Trong kho</span>
        <h2 style="font-size:var(--fs-2xl)"><?= count($books) ?> cuốn của tác giả này</h2>
      </div>
    </div>

    <div class="book-grid">
      <?php foreach ($books as $b): ?>
        <?php require __DIR__ . '/includes/book-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
