<?php
/**
 * HieuMini - Trang lỗi 404
 */
require_once __DIR__ . '/includes/config.php';
http_response_code(404);

$hot = $pdo->query('SELECT p.*, c.name AS category_name FROM projects p
                    JOIN categories c ON c.id = p.category_id
                    WHERE p.status = 1 ORDER BY p.sales DESC LIMIT 3')->fetchAll();

seo([
    'title'       => 'Không tìm thấy trang (404) | ' . SITE_NAME,
    'description' => 'Trang bạn tìm không tồn tại. Khám phá kho dự án website PHP MySQL của HieuMini.',
    'robots'      => 'noindex, follow',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container section">
  <div class="glass reveal reveal--zoom" style="text-align:center;padding:var(--sp-8) var(--sp-5);max-width:720px;margin-inline:auto">
    <p class="stat__num" style="font-size:5rem;line-height:1">404</p>
    <h1>Trang này không tồn tại</h1>
    <p>Có thể đường dẫn đã thay đổi, hoặc bạn gõ nhầm một ký tự. Thử tìm kiếm hoặc quay về trang chủ nhé.</p>
    <form action="<?= e(url('projects.php')) ?>" method="get" style="display:flex;gap:8px;max-width:420px;margin:var(--sp-5) auto">
      <label class="sr-only" for="q404">Từ khoá tìm kiếm</label>
      <input class="input" type="search" id="q404" name="q" placeholder="Tìm dự án…">
      <button class="btn btn--primary" type="submit">Tìm</button>
    </form>
    <a class="btn btn--ghost" href="<?= e(url('index.php')) ?>">← Về trang chủ</a>
  </div>
</div>

<section class="section">
  <div class="container">
    <div class="section-head reveal"><h2>Có thể bạn quan tâm</h2></div>
    <div class="grid grid--3 stagger">
      <?php foreach ($hot as $p) { include __DIR__ . '/includes/project-card.php'; } ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
