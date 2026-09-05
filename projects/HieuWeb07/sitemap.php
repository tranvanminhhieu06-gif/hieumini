<?php
/**
 * Sơ đồ trang cho công cụ tìm kiếm.
 *
 * Sinh động từ cơ sở dữ liệu để mỗi khi thêm sách mới là sitemap tự có ngay,
 * khỏi phải nhớ cập nhật tay. Truy cập qua /sitemap.xml nhờ luật viết lại
 * trong .htaccess (hoặc mở thẳng /sitemap.php).
 */
declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

header('Content-Type: application/xml; charset=utf-8');

$urls = [
    ['loc' => abs_url('index.php'),   'priority' => '1.0', 'freq' => 'weekly'],
    ['loc' => abs_url('books.php'),   'priority' => '0.9', 'freq' => 'weekly'],
    ['loc' => abs_url('authors.php'), 'priority' => '0.7', 'freq' => 'monthly'],
    ['loc' => abs_url('about.php'),   'priority' => '0.5', 'freq' => 'yearly'],
    ['loc' => abs_url('contact.php'), 'priority' => '0.4', 'freq' => 'yearly'],
];

foreach (db_all('SELECT slug FROM categories ORDER BY sort_order') as $c) {
    $urls[] = ['loc' => abs_url('books.php?the-loai=' . $c['slug']), 'priority' => '0.7', 'freq' => 'monthly'];
}
foreach (db_all('SELECT slug FROM books ORDER BY id') as $b) {
    $urls[] = ['loc' => abs_url('book.php?s=' . $b['slug']), 'priority' => '0.8', 'freq' => 'monthly'];
}
foreach (db_all('SELECT slug FROM authors ORDER BY id') as $a) {
    $urls[] = ['loc' => abs_url('author.php?s=' . $a['slug']), 'priority' => '0.6', 'freq' => 'yearly'];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u): ?>
  <url>
    <loc><?= e($u['loc']) ?></loc>
    <lastmod><?= date('Y-m-d') ?></lastmod>
    <changefreq><?= $u['freq'] ?></changefreq>
    <priority><?= $u['priority'] ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
