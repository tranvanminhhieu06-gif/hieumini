<?php
/**
 * HieuMini — Trang chủ
 * Trưng bày toàn bộ dự án trong thư mục projects/ dưới dạng xem trực tiếp.
 */

declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';

$projects = Database::all(
    "SELECT * FROM projects WHERE status = 'published' ORDER BY sort_order ASC, id ASC"
);

$categories = Database::all(
    "SELECT category, COUNT(*) AS total
       FROM projects WHERE status = 'published'
      GROUP BY category ORDER BY category ASC"
);

$totalViews = (int) Database::scalar("SELECT COALESCE(SUM(views), 0) FROM projects");
$totalTables = (int) Database::scalar("SELECT COALESCE(SUM(table_count), 0) FROM projects");
$totalPages  = (int) Database::scalar("SELECT COALESCE(SUM(page_count), 0) FROM projects");

log_visit();

$pageTitle = SITE_NAME . ' — ' . SITE_TAGLINE;
$pageDesc  = 'Sáu website thương mại điện tử viết bằng PHP thuần và MySQL: thời trang, công nghệ, văn phòng phẩm, gia dụng, thể hình và chợ mã nguồn. Xem trực tiếp ngay tại đây.';
require __DIR__ . '/includes/header.php';

$heroWords = ['Dự', 'án', 'website', 'của', 'HieuMini,', 'khám', 'phá', 'ngay', 'nào'];
$heroGradientFrom = 5; // Từ "khám" trở đi tô màu gradient
?>

<!-- ================= BANNER CÓ HIỆU ỨNG MORPH ================= -->
<section class="hero" data-parallax-root>
  <div class="hero-grid" aria-hidden="true"></div>

  <div class="hero-canvas" aria-hidden="true">
    <svg viewBox="0 0 1200 700" preserveAspectRatio="xMidYMid slice" role="presentation">
      <defs>
        <linearGradient id="mg1" x1="0" y1="0" x2="1" y2="1">
          <stop offset="0%"   stop-color="#6366F1"/>
          <stop offset="100%" stop-color="#7C3AED"/>
        </linearGradient>
        <linearGradient id="mg2" x1="1" y1="0" x2="0" y2="1">
          <stop offset="0%"   stop-color="#06B6D4"/>
          <stop offset="100%" stop-color="#8B5CF6"/>
        </linearGradient>
        <linearGradient id="mg3" x1="0" y1="1" x2="1" y2="0">
          <stop offset="0%"   stop-color="#F472B6"/>
          <stop offset="100%" stop-color="#6366F1"/>
        </linearGradient>
        <filter id="soften" x="-40%" y="-40%" width="180%" height="180%">
          <feGaussianBlur stdDeviation="46"/>
        </filter>
      </defs>

      <g filter="url(#soften)" opacity=".62">
        <path data-morph data-cx="880" data-cy="230" data-radius="230" data-wobble="0.46"
              fill="url(#mg1)" data-depth="26"/>
        <path data-morph data-cx="1060" data-cy="470" data-radius="180" data-wobble="0.52"
              fill="url(#mg2)" data-depth="42"/>
        <path data-morph data-cx="180" data-cy="560" data-radius="200" data-wobble="0.5"
              fill="url(#mg3)" data-depth="18" opacity=".7"/>
      </g>
    </svg>
  </div>

  <div class="container">
    <div class="hero-inner">
      <div>
        <span class="eyebrow"><?= icon('spark', 'ico ico-sm') ?> Đồ án lập trình Web · PHP &amp; MySQL</span>

        <h1>
          <?php foreach ($heroWords as $i => $word): ?>
            <span class="reveal-word<?= $i >= $heroGradientFrom ? ' gradient-text' : '' ?>"
                  style="animation-delay: <?= 90 + $i * 62 ?>ms"><?= e($word) ?></span>
          <?php endforeach; ?>
        </h1>

        <p class="hero-lead">
          <?= e(SITE_NAME) ?> là không gian trưng bày <?= count($projects) ?> hệ thống thương mại điện tử
          do <?= e(SITE_AUTHOR) ?> xây dựng bằng PHP thuần và MySQL. Mỗi dự án được nhúng
          <strong>trực tiếp</strong> vào trang — bạn thao tác thật với giao diện thật, không phải ảnh chụp màn hình.
        </p>

        <div class="hero-cta">
          <a class="btn btn--primary" href="#projects">
            <?= icon('grid', 'ico ico-sm') ?> Xem <?= count($projects) ?> dự án
          </a>
          <a class="btn btn--ghost" href="<?= e(url('BaoCao.docx')) ?>">
            <?= icon('list', 'ico ico-sm') ?> Tải báo cáo (.docx)
          </a>
          <a class="btn btn--ghost" href="<?= e(url('about.php')) ?>">
            <?= icon('code', 'ico ico-sm') ?> Kiến trúc hệ thống
          </a>
        </div>

        <div class="hero-stats">
          <div>
            <div class="stat-num" data-count="<?= count($projects) ?>">0</div>
            <div class="stat-label">Dự án live</div>
          </div>
          <div>
            <div class="stat-num" data-count="<?= $totalPages ?>" data-suffix="+">0</div>
            <div class="stat-label">Trang PHP</div>
          </div>
          <div>
            <div class="stat-num" data-count="<?= $totalTables ?>">0</div>
            <div class="stat-label">Bảng CSDL</div>
          </div>
          <div>
            <div class="stat-num" data-count="<?= $totalViews ?>">0</div>
            <div class="stat-label">Lượt xem</div>
          </div>
        </div>
      </div>

      <div class="hero-visual" aria-hidden="true">
        <div class="float-card" data-depth="22">
          <?= icon('database') ?>
          <div><b>MySQL / InnoDB</b><span>PDO prepared statement</span></div>
        </div>
        <div class="float-card" data-depth="34">
          <?= icon('shield') ?>
          <div><b>Bcrypt &amp; CSRF</b><span>Chống SQL Injection, XSS</span></div>
        </div>
        <div class="float-card" data-depth="14">
          <?= icon('bolt') ?>
          <div><b>AJAX thời gian thực</b><span>Giỏ hàng không tải lại trang</span></div>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ================= DANH SÁCH DỰ ÁN ================= -->
<section class="section" id="projects">
  <div class="container">
    <div class="section-head" data-reveal>
      <span class="eyebrow"><?= icon('grid', 'ico ico-sm') ?> Bộ sưu tập</span>
      <h2>Mỗi thẻ dưới đây là một website đang chạy thật</h2>
      <p>
        Khung xem trực tiếp bên dưới nạp thẳng mã nguồn PHP từ thư mục
        <code>projects/</code> trên máy chủ của bạn. Bấm “Trải nghiệm” để mở chế độ tương tác đầy đủ.
      </p>
    </div>

    <div class="filter-bar" data-filter-bar role="group" aria-label="Lọc dự án theo lĩnh vực">
      <button type="button" class="chip is-active" data-filter="all" aria-pressed="true">
        Tất cả (<?= count($projects) ?>)
      </button>
      <?php foreach ($categories as $cat): ?>
        <button type="button" class="chip" data-filter="<?= e($cat['category']) ?>" aria-pressed="false">
          <?= e($cat['category']) ?> (<?= (int) $cat['total'] ?>)
        </button>
      <?php endforeach; ?>
    </div>

    <?php if (!$projects): ?>
      <div class="empty-state">
        <?= icon('inbox', 'ico') ?>
        <h3>Chưa có dự án nào</h3>
        <p>Hãy nạp tệp <code>database/hieumini_portfolio.sql</code> vào phpMyAdmin để có dữ liệu mẫu.</p>
      </div>
    <?php else: ?>
      <div class="project-grid">
        <?php foreach ($projects as $i => $p):
            $exists = project_exists($p);
            $detail = url('project.php?slug=' . rawurlencode($p['slug']));
        ?>
        <article class="project-card"
                 data-category="<?= e($p['category']) ?>"
                 data-reveal data-reveal-delay="<?= ($i % 3) * 90 ?>"
                 style="--c1: <?= e($p['accent_from']) ?>; --c2: <?= e($p['accent_to']) ?>">

          <div class="live-frame<?= $exists ? '' : ' is-ready' ?>"
               <?= $exists ? 'data-live-src="' . e(project_url($p, $p['entry_file'])) . '"' : '' ?>>
            <span class="live-dot"><i></i> <?= $exists ? 'Live' : 'Offline' ?></span>

            <?php if ($exists): ?>
              <iframe title="Xem trước <?= e($p['name']) ?>" loading="lazy" tabindex="-1"></iframe>
              <div class="frame-skeleton">
                <span class="frame-spinner"></span>
                Đang nạp <?= e($p['code']) ?>…
              </div>
            <?php else: ?>
              <div class="frame-skeleton" style="opacity:1">
                Không tìm thấy thư mục projects/<?= e($p['folder']) ?>
              </div>
            <?php endif; ?>

            <div class="frame-overlay">
              <a class="btn btn--primary btn--sm" href="<?= e($detail) ?>">
                <?= icon('eye', 'ico ico-sm') ?> Trải nghiệm
              </a>
            </div>
          </div>

          <div class="project-body">
            <div class="project-meta">
              <span class="project-code"><?= e($p['code']) ?></span>
              <span aria-hidden="true">•</span>
              <span><?= e($p['category']) ?></span>
              <span aria-hidden="true">•</span>
              <span><?= (int) $p['year'] ?></span>
            </div>

            <h3><a href="<?= e($detail) ?>"><?= e($p['name']) ?></a></h3>
            <p><?= e($p['tagline']) ?></p>

            <div class="tag-row">
              <?php foreach (array_slice(split_list($p['tech_stack']), 0, 4) as $tech): ?>
                <span class="tag"><?= e($tech) ?></span>
              <?php endforeach; ?>
            </div>
          </div>

          <div class="project-foot">
            <a class="btn btn--ghost btn--sm" href="<?= e($detail) ?>">
              <?= icon('arrow', 'ico ico-sm') ?> Chi tiết
            </a>
            <?php if ($exists): ?>
              <a class="btn btn--soft btn--sm" href="<?= e(project_url($p, $p['entry_file'])) ?>"
                 target="_blank" rel="noopener">
                <?= icon('external', 'ico ico-sm') ?> Tab mới
              </a>
            <?php endif; ?>
          </div>
        </article>
        <?php endforeach; ?>
      </div>

      <div class="empty-state" data-filter-empty hidden style="margin-top:24px">
        <?= icon('search', 'ico') ?>
        <h3>Không có dự án nào thuộc lĩnh vực này</h3>
        <p>Hãy chọn lại một thẻ lọc khác phía trên.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<!-- ================= QUY TRÌNH KỸ THUẬT ================= -->
<section class="section section--tight" style="background:var(--bg-soft)">
  <div class="container">
    <div class="section-head is-center" data-reveal>
      <span class="eyebrow"><?= icon('layers', 'ico ico-sm') ?> Nền tảng chung</span>
      <h2>Một bộ nguyên tắc kỹ thuật cho cả sáu dự án</h2>
      <p>Dù khác nhau về ngành hàng và phong cách đồ họa, sáu hệ thống đều tuân thủ cùng một chuẩn mã nguồn.</p>
    </div>

    <div class="feature-list">
      <?php
      $pillars = [
          ['database', 'Kiến trúc PHP thuần module hóa', 'Tách bạch config, includes, assets và admin. Không dùng framework nên dễ đọc, dễ chấm và dễ chuyển giao.'],
          ['shield',   'Bảo mật nhiều lớp',              'PDO prepared statement chặn SQL Injection, escape đầu ra chặn XSS, mật khẩu băm Bcrypt, biểu mẫu có token CSRF.'],
          ['bolt',     'Tương tác bất đồng bộ',          'Giỏ hàng, tìm kiếm và bộ lọc xử lý bằng AJAX, phản hồi tức thì bằng toast thay vì tải lại toàn trang.'],
          ['grid',     'Giao diện đáp ứng',              'Thiết kế mobile-first, kiểm thử ở bốn điểm ngắt 375px, 768px, 1024px và 1440px.'],
          ['chart',    'Quản trị có số liệu',            'Mỗi dự án đều có dashboard thống kê doanh thu, đơn hàng và tồn kho phục vụ ra quyết định.'],
          ['check',    'Sẵn sàng chấm điểm',             'Kèm script CSDL chạy lại được nhiều lần, tài khoản mẫu và tài liệu báo cáo theo đúng mục lục.'],
      ];
      foreach ($pillars as $i => [$ico, $title, $desc]): ?>
        <div class="feature-item" data-reveal data-reveal-delay="<?= ($i % 3) * 80 ?>"
             style="--c1:var(--indigo-600); --c2:var(--violet-600)">
          <span class="feature-ico"><?= icon($ico) ?></span>
          <div><b><?= e($title) ?></b><span><?= e($desc) ?></span></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
