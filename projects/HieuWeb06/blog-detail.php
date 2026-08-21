<?php
/**
 * HieuMini - Chi tiết bài viết
 */
require_once __DIR__ . '/includes/config.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$post = $slug !== '' ? get_post_by_slug($pdo, $slug) : null;

if (!$post) {
    http_response_code(404);
    seo(['title' => 'Không tìm thấy bài viết | ' . SITE_NAME, 'robots' => 'noindex, follow']);
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="glass empty-state"><h1>404 - Không tìm thấy bài viết</h1>'
       . '<a class="btn btn--primary" href="' . e(url('blog.php')) . '">Về trang blog</a></div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

if (empty($_SESSION['read'][$post['id']])) {
    $pdo->prepare('UPDATE posts SET views = views + 1 WHERE id = ?')->execute([$post['id']]);
    $_SESSION['read'][$post['id']] = true;
}

$others = $pdo->prepare('SELECT * FROM posts WHERE status = 1 AND id <> ? ORDER BY published_at DESC LIMIT 3');
$others->execute([$post['id']]);
$others = $others->fetchAll();

$paragraphs = array_filter(array_map('trim', explode("\n", (string)$post['content'])));
$words = str_word_count(strip_tags((string)$post['content']));
$readMinutes = max(2, (int)ceil(mb_strlen((string)$post['content'], 'UTF-8') / 900));

$article = [
    '@context'      => 'https://schema.org',
    '@type'         => 'BlogPosting',
    'headline'      => $post['title'],
    'description'   => $post['excerpt'],
    'image'         => asset($post['thumbnail']),
    'datePublished' => date('c', strtotime($post['published_at'])),
    'dateModified'  => date('c', strtotime($post['published_at'])),
    'author'        => ['@type' => 'Person', 'name' => $post['author']],
    'publisher'     => ['@type' => 'Organization', 'name' => SITE_NAME, 'logo' => ['@type' => 'ImageObject', 'url' => asset('assets/images/logo.svg')]],
    'mainEntityOfPage' => url('blog-detail.php?slug=' . $post['slug']),
    'inLanguage'    => 'vi-VN',
    'keywords'      => $post['tags'],
];

seo([
    'title'       => ($post['meta_title'] ?: $post['title'] . ' | ' . SITE_NAME),
    'description' => ($post['meta_description'] ?: excerpt($post['excerpt'], 155)),
    'keywords'    => (string)$post['tags'],
    'image'       => asset($post['thumbnail']),
    'og_type'     => 'article',
    'schema'      => '<script type="application/ld+json">' . json_encode($article, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container page-head">
  <?= breadcrumb(['Trang chủ' => 'index.php', 'Blog' => 'blog.php', $post['title'] => null]) ?>
</div>

<article class="container section--tight">
  <div class="article">
    <h1 class="reveal"><?= e($post['title']) ?></h1>
    <div class="meta-row reveal">
      <span>✍ <?= e($post['author']) ?></span>
      <span><time datetime="<?= date('Y-m-d', strtotime($post['published_at'])) ?>"><?= e(vn_date($post['published_at'])) ?></time></span>
      <span><?= $readMinutes ?> phút đọc</span>
      <span><?= num((int)$post['views']) ?> lượt đọc</span>
    </div>

    <img class="reveal" src="<?= e(asset($post['thumbnail'])) ?>" alt="Ảnh minh hoạ bài viết <?= e($post['title']) ?>"
         width="800" height="450" fetchpriority="high" decoding="async">

    <p class="reveal" style="font-size:var(--fs-xl);color:var(--fg);font-weight:500"><?= e($post['excerpt']) ?></p>

    <?php foreach ($paragraphs as $para): ?>
      <p class="reveal"><?= e($para) ?></p>
    <?php endforeach; ?>

    <div class="tech-list reveal" style="margin-top:var(--sp-5)">
      <?php foreach (array_filter(array_map('trim', explode(',', (string)$post['tags']))) as $tag): ?>
        <span><?= e($tag) ?></span>
      <?php endforeach; ?>
    </div>

    <div class="cta-band reveal" style="margin-top:var(--sp-7)">
      <h2 style="font-size:var(--fs-2xl)">Áp dụng ngay với mã nguồn có sẵn</h2>
      <p>Mọi dự án tại HieuMini đều được viết theo đúng những nguyên tắc trong bài này.</p>
      <a class="btn btn--primary btn--lg" href="<?= e(url('projects.php')) ?>">Xem kho dự án</a>
    </div>
  </div>
</article>

<section class="section">
  <div class="container">
    <div class="section-head reveal"><h2>Bài viết khác</h2></div>
    <div class="grid grid--3 stagger">
      <?php foreach ($others as $o): ?>
        <article class="card post-card reveal">
          <a class="post-card__media" href="<?= e(url('blog-detail.php?slug=' . $o['slug'])) ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= e(asset($o['thumbnail'])) ?>" alt="" width="800" height="450" loading="lazy">
          </a>
          <div class="project-card__body">
            <span class="project-card__cat"><?= e(vn_date($o['published_at'])) ?></span>
            <h3 class="project-card__title" style="font-size:var(--fs-lg)">
              <a href="<?= e(url('blog-detail.php?slug=' . $o['slug'])) ?>"><?= e($o['title']) ?></a>
            </h3>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
