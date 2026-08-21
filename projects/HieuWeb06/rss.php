<?php
/**
 * HieuMini - Nguồn cấp RSS cho blog
 */
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/rss+xml; charset=utf-8');
$posts = $pdo->query('SELECT * FROM posts WHERE status = 1 ORDER BY published_at DESC LIMIT 20')->fetchAll();

echo '<?xml version="1.0" encoding="UTF-8"?>' . "\n";
?>
<rss version="2.0">
<channel>
  <title><?= e(SITE_NAME) ?> - Blog kiến thức lập trình web</title>
  <link><?= e(url('blog.php')) ?></link>
  <description><?= e(setting('site_description')) ?></description>
  <language>vi-VN</language>
  <lastBuildDate><?= date(DATE_RSS) ?></lastBuildDate>
<?php foreach ($posts as $p): ?>
  <item>
    <title><?= e($p['title']) ?></title>
    <link><?= e(url('blog-detail.php?slug=' . $p['slug'])) ?></link>
    <guid><?= e(url('blog-detail.php?slug=' . $p['slug'])) ?></guid>
    <pubDate><?= date(DATE_RSS, strtotime($p['published_at'])) ?></pubDate>
    <description><?= e($p['excerpt']) ?></description>
  </item>
<?php endforeach; ?>
</channel>
</rss>
