<?php
/**
 * HieuMini - Sơ đồ website dạng XML (sinh tự động cho công cụ tìm kiếm)
 * Khai báo trong robots.txt và Google Search Console.
 */
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/xml; charset=utf-8');

$urls = [
    ['loc' => url('index.php'),    'priority' => '1.0', 'freq' => 'daily'],
    ['loc' => url('projects.php'), 'priority' => '0.9', 'freq' => 'daily'],
    ['loc' => url('blog.php'),     'priority' => '0.7', 'freq' => 'weekly'],
    ['loc' => url('about.php'),    'priority' => '0.6', 'freq' => 'monthly'],
    ['loc' => url('contact.php'),  'priority' => '0.6', 'freq' => 'monthly'],
];

foreach (get_categories($pdo) as $c) {
    $urls[] = ['loc' => url('projects.php?cat=' . $c['slug']), 'priority' => '0.8', 'freq' => 'weekly'];
}

foreach ($pdo->query('SELECT slug, updated_at FROM projects WHERE status = 1')->fetchAll() as $p) {
    $urls[] = [
        'loc'      => url('project-detail.php?slug=' . $p['slug']),
        'priority' => '0.9',
        'freq'     => 'weekly',
        'lastmod'  => date('Y-m-d', strtotime($p['updated_at'])),
    ];
}

foreach ($pdo->query('SELECT slug, published_at FROM posts WHERE status = 1')->fetchAll() as $p) {
    $urls[] = [
        'loc'      => url('blog-detail.php?slug=' . $p['slug']),
        'priority' => '0.7',
        'freq'     => 'monthly',
        'lastmod'  => date('Y-m-d', strtotime($p['published_at'])),
    ];
}

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
<?php foreach ($urls as $u): ?>
  <url>
    <loc><?= e($u['loc']) ?></loc>
    <lastmod><?= e($u['lastmod'] ?? date('Y-m-d')) ?></lastmod>
    <changefreq><?= e($u['freq']) ?></changefreq>
    <priority><?= e($u['priority']) ?></priority>
  </url>
<?php endforeach; ?>
</urlset>
