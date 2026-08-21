<?php
/**
 * HieuMini - Danh sách bài viết
 */
require_once __DIR__ . '/includes/config.php';

$page = max(1, (int)($_GET['page'] ?? 1));
$perPage = 6;

$total = (int)$pdo->query('SELECT COUNT(*) FROM posts WHERE status = 1')->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$posts = $pdo->query("SELECT * FROM posts WHERE status = 1
                      ORDER BY published_at DESC LIMIT $perPage OFFSET $offset")->fetchAll();

seo([
    'title'       => 'Blog kiến thức lập trình web & SEO | ' . SITE_NAME,
    'description' => 'Bài viết hướng dẫn chọn mua mã nguồn, tối ưu SEO on-page, bảo mật PHP, Core Web Vitals và thiết kế giao diện tối chuẩn khả năng tiếp cận.',
    'keywords'    => 'blog lập trình web, seo on-page, bảo mật php, core web vitals',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container page-head">
  <?= breadcrumb(['Trang chủ' => 'index.php', 'Blog' => null]) ?>
  <h1 style="margin-top:var(--sp-4)">Blog kiến thức</h1>
  <p style="max-width:70ch">Kinh nghiệm chọn mua mã nguồn, tối ưu SEO, bảo mật và hiệu năng cho website PHP - viết từ chính quá trình xây dựng các dự án đang bán tại HieuMini.</p>
</div>

<div class="container section--tight">
  <div class="grid grid--3 stagger">
    <?php foreach ($posts as $post): ?>
      <article class="card post-card reveal">
        <a class="post-card__media" href="<?= e(url('blog-detail.php?slug=' . $post['slug'])) ?>" tabindex="-1" aria-hidden="true">
          <img src="<?= e(asset($post['thumbnail'])) ?>" alt="" width="800" height="450" loading="lazy" decoding="async">
        </a>
        <div class="project-card__body">
          <span class="project-card__cat"><?= e(vn_date($post['published_at'])) ?> · <?= num((int)$post['views']) ?> lượt đọc</span>
          <h2 class="project-card__title" style="font-size:var(--fs-lg)">
            <a href="<?= e(url('blog-detail.php?slug=' . $post['slug'])) ?>"><?= e($post['title']) ?></a>
          </h2>
          <p class="project-card__desc"><?= e(excerpt($post['excerpt'], 130)) ?></p>
          <div class="tech-list">
            <?php foreach (array_filter(array_map('trim', explode(',', (string)$post['tags']))) as $tag): ?>
              <span><?= e($tag) ?></span>
            <?php endforeach; ?>
          </div>
        </div>
      </article>
    <?php endforeach; ?>
  </div>

  <?= pagination($page, $totalPages) ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
