<?php
/**
 * HieuMini — Trang chi tiết dự án
 * Nhúng website con ở chế độ tương tác đầy đủ, kèm hồ sơ kỹ thuật.
 */

declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';

$slug = trim((string) ($_GET['slug'] ?? ''));

$project = $slug === '' ? null : Database::one(
    "SELECT * FROM projects WHERE slug = ? AND status = 'published' LIMIT 1",
    [$slug]
);

if (!$project) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy dự án — ' . SITE_NAME;
    require __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container"><div class="empty-state">'
       . icon('search', 'ico')
       . '<h2>Không tìm thấy dự án</h2>'
       . '<p>Đường dẫn có thể đã thay đổi hoặc dự án đã được gỡ khỏi bộ sưu tập.</p>'
       . '<a class="btn btn--primary" href="' . e(url('index.php')) . '">Về trang chủ</a>'
       . '</div></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$projectId = (int) $project['id'];

Database::run('UPDATE projects SET views = views + 1 WHERE id = ?', [$projectId]);
log_visit($projectId);

$features = Database::all(
    'SELECT * FROM project_features WHERE project_id = ? ORDER BY sort_order ASC, id ASC',
    [$projectId]
);

$others = Database::all(
    "SELECT slug, code, name, tagline, accent_from, accent_to
       FROM projects
      WHERE status = 'published' AND id <> ?
      ORDER BY sort_order ASC LIMIT 3",
    [$projectId]
);

$exists  = project_exists($project);
$liveUrl = project_url($project, $project['entry_file']);
$adminUrl = project_url($project, $project['admin_path']);
$totalProjects = (int) Database::scalar("SELECT COUNT(*) FROM projects WHERE status = 'published'");

$pageTitle = $project['name'] . ' — ' . SITE_NAME;
$pageDesc  = $project['tagline'];
require __DIR__ . '/includes/header.php';
?>

<section class="detail-hero"
         style="--c1: <?= e($project['accent_from']) ?>; --c2: <?= e($project['accent_to']) ?>">
  <div class="container">
    <nav class="breadcrumb" aria-label="Đường dẫn phân cấp">
      <a href="<?= e(url('index.php')) ?>">Trang chủ</a>
      <span aria-hidden="true">/</span>
      <a href="<?= e(url('index.php#projects')) ?>">Dự án</a>
      <span aria-hidden="true">/</span>
      <span aria-current="page"><?= e($project['code']) ?></span>
    </nav>

    <span class="eyebrow"><?= icon('code', 'ico ico-sm') ?> <?= e($project['code']) ?> · <?= e($project['category']) ?></span>
    <h1 style="font-size:var(--fs-3xl)"><?= e($project['name']) ?></h1>
    <p class="hero-lead" style="max-width:70ch"><?= e($project['tagline']) ?></p>

    <div class="hero-cta">
      <?php if ($exists): ?>
        <a class="btn btn--primary" href="<?= e($liveUrl) ?>" target="_blank" rel="noopener">
          <?= icon('external', 'ico ico-sm') ?> Mở toàn màn hình
        </a>
        <a class="btn btn--ghost" href="<?= e($adminUrl) ?>" target="_blank" rel="noopener">
          <?= icon('lock', 'ico ico-sm') ?> Trang quản trị dự án
        </a>
      <?php else: ?>
        <span class="alert alert--error" style="margin:0">
          <?= icon('close', 'ico ico-sm') ?>
          Không tìm thấy thư mục <code>projects/<?= e($project['folder']) ?></code> trên máy chủ.
        </span>
      <?php endif; ?>
    </div>

    <?php if ($exists): ?>
    <div class="cred-card" role="note">
      <span class="cred-ico"><?= icon('lock') ?></span>
      <div class="cred-body">
        <b>Tài khoản quản trị demo — dùng chung cho cả <?= (int) $totalProjects ?> dự án</b>
        <div class="cred-rows">
          <span class="cred-item">Email <code><?= e(DEMO_ADMIN_USER) ?></code>
            <button type="button" class="cred-copy" data-copy="<?= e(DEMO_ADMIN_USER) ?>" aria-label="Sao chép email">
              <?= icon('external', 'ico ico-sm') ?></button>
          </span>
          <span class="cred-item">Mật khẩu <code><?= e(DEMO_ADMIN_PASS) ?></code>
            <button type="button" class="cred-copy" data-copy="<?= e(DEMO_ADMIN_PASS) ?>" aria-label="Sao chép mật khẩu">
              <?= icon('external', 'ico ico-sm') ?></button>
          </span>
        </div>
        <span class="cred-hint">Bấm “Trang quản trị dự án” rồi đăng nhập bằng tài khoản trên.</span>
      </div>
    </div>
    <?php endif; ?>
  </div>
</section>

<section class="section--tight" style="padding-top:0">
  <div class="container">
    <div class="detail-layout">

      <!-- ---------- Khung xem trực tiếp ---------- -->
      <div>
        <div class="stage" data-reveal>
          <div class="stage-bar">
            <span class="stage-dots" aria-hidden="true"><i></i><i></i><i></i></span>
            <span class="stage-url"><?= e($liveUrl) ?></span>
            <div class="viewport-switch" data-viewport-switch role="group" aria-label="Đổi kích thước khung xem">
              <button type="button" data-viewport="desktop" class="is-active" aria-pressed="true">Máy tính</button>
              <button type="button" data-viewport="tablet"  aria-pressed="false">Tablet</button>
              <button type="button" data-viewport="mobile"  aria-pressed="false">Điện thoại</button>
            </div>
          </div>

          <div class="stage-body" data-stage-body>
            <?php if ($exists): ?>
              <iframe src="<?= e($liveUrl) ?>" title="Bản chạy trực tiếp của <?= e($project['name']) ?>"
                      data-stage-frame loading="lazy"></iframe>
              <div class="stage-fallback" data-stage-fallback hidden>
                <?= icon('database', 'ico') ?>
                <h3>Chưa nạp cơ sở dữ liệu của dự án này</h3>
                <p>
                  Dự án <b><?= e($project['name']) ?></b> cần cơ sở dữ liệu
                  <code><?= e($project['db_name']) ?></code>. Hãy nạp
                  <code>database/tidb_all.sql</code> (hoặc chạy <code>import-local.bat</code>)
                  để bản chạy trực tiếp hiển thị đầy đủ.
                </p>
                <a class="btn btn--primary btn--sm" href="<?= e($liveUrl) ?>" target="_blank" rel="noopener">
                  <?= icon('external', 'ico ico-sm') ?> Vẫn mở thử ở tab mới
                </a>
              </div>
            <?php else: ?>
              <div class="empty-state" style="border:0;margin:0">
                <?= icon('inbox', 'ico') ?>
                <h3>Chưa có mã nguồn</h3>
                <p>Hãy sao chép thư mục dự án vào <code>projects/<?= e($project['folder']) ?></code>.</p>
              </div>
            <?php endif; ?>
          </div>
        </div>

        <p class="hint" style="font-size:var(--fs-xs);color:var(--fg-subtle);margin-top:10px">
          <?= icon('spark', 'ico ico-sm') ?>
          Khung trên là website thật đang chạy. Nếu trình duyệt chặn nội dung nhúng,
          hãy dùng nút “Mở toàn màn hình”.
        </p>

        <!-- ---------- Mô tả ---------- -->
        <div class="section--tight prose" data-reveal>
          <h2 style="font-size:var(--fs-2xl)">Giới thiệu dự án</h2>
          <?= paragraphs($project['description'] ?: $project['summary']) ?>
        </div>

        <!-- ---------- Điểm nổi bật ---------- -->
        <?php if ($features): ?>
        <div data-reveal>
          <h2 style="font-size:var(--fs-2xl);margin-bottom:var(--space-5)">Điểm nổi bật</h2>
          <div class="feature-list"
               style="--c1: <?= e($project['accent_from']) ?>; --c2: <?= e($project['accent_to']) ?>">
            <?php foreach ($features as $i => $f): ?>
              <div class="feature-item" data-reveal data-reveal-delay="<?= ($i % 2) * 90 ?>">
                <span class="feature-ico"><?= icon($f['icon']) ?></span>
                <div><b><?= e($f['title']) ?></b><span><?= e($f['content']) ?></span></div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
        <?php endif; ?>
      </div>

      <!-- ---------- Cột thông tin ---------- -->
      <aside>
        <div class="panel" data-reveal>
          <h3><?= icon('list', 'ico ico-sm') ?> Hồ sơ kỹ thuật</h3>
          <ul class="spec-list">
            <li><span class="k">Mã dự án</span><span class="v"><?= e($project['code']) ?></span></li>
            <li><span class="k">Lĩnh vực</span><span class="v"><?= e($project['category']) ?></span></li>
            <li><span class="k">Năm thực hiện</span><span class="v"><?= (int) $project['year'] ?></span></li>
            <li><span class="k">Số trang PHP</span><span class="v"><?= num($project['page_count']) ?></span></li>
            <li><span class="k">Số bảng CSDL</span><span class="v"><?= num($project['table_count']) ?></span></li>
            <li><span class="k">Tên CSDL</span><span class="v"><code><?= e($project['db_name']) ?></code></span></li>
            <li><span class="k">Lượt xem</span><span class="v"><?= num($project['views']) ?></span></li>
          </ul>
        </div>

        <div class="panel" data-reveal>
          <h3><?= icon('layers', 'ico ico-sm') ?> Công nghệ sử dụng</h3>
          <div class="tag-row" style="padding-top:0">
            <?php foreach (split_list($project['tech_stack']) as $tech): ?>
              <span class="tag"><?= e($tech) ?></span>
            <?php endforeach; ?>
          </div>
        </div>

        <?php if ($others): ?>
        <div class="panel" data-reveal>
          <h3><?= icon('grid', 'ico ico-sm') ?> Dự án khác</h3>
          <ul class="footer-links">
            <?php foreach ($others as $o): ?>
              <li>
                <a href="<?= e(url('project.php?slug=' . rawurlencode($o['slug']))) ?>">
                  <strong><?= e($o['code']) ?></strong> — <?= e($o['name']) ?>
                </a>
              </li>
            <?php endforeach; ?>
          </ul>
        </div>
        <?php endif; ?>
      </aside>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
