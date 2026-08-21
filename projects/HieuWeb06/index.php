<?php
/**
 * HieuMini - Trang chủ
 */
require_once __DIR__ . '/includes/config.php';

// ---------- Dữ liệu ----------
$featured = $pdo->query('SELECT p.*, c.name AS category_name FROM projects p
                         JOIN categories c ON c.id = p.category_id
                         WHERE p.status = 1 AND p.is_featured = 1
                         ORDER BY p.sales DESC LIMIT 6')->fetchAll();

$newest = $pdo->query('SELECT p.*, c.name AS category_name FROM projects p
                       JOIN categories c ON c.id = p.category_id
                       WHERE p.status = 1 ORDER BY p.created_at DESC, p.id DESC LIMIT 3')->fetchAll();

$categories = get_categories($pdo);

$posts = $pdo->query('SELECT * FROM posts WHERE status = 1 ORDER BY published_at DESC LIMIT 3')->fetchAll();

$topReviews = $pdo->query('SELECT r.*, u.full_name, p.title AS project_title, p.slug AS project_slug
                           FROM reviews r JOIN users u ON u.id = r.user_id
                           JOIN projects p ON p.id = r.project_id
                           WHERE r.status = 1 AND r.rating >= 4
                           ORDER BY r.created_at DESC LIMIT 3')->fetchAll();

$stats = [
    'projects' => (int)$pdo->query('SELECT COUNT(*) FROM projects WHERE status = 1')->fetchColumn(),
    'sales'    => (int)$pdo->query('SELECT IFNULL(SUM(sales),0) FROM projects')->fetchColumn(),
    'users'    => (int)$pdo->query('SELECT COUNT(*) FROM users')->fetchColumn(),
    'reviews'  => (int)$pdo->query('SELECT COUNT(*) FROM reviews WHERE status = 1')->fetchColumn(),
];

// ---------- SEO ----------
$itemList = [
    '@context'        => 'https://schema.org',
    '@type'           => 'ItemList',
    'name'            => 'Dự án website nổi bật tại HieuMini',
    'itemListElement' => [],
];
foreach ($featured as $i => $p) {
    $itemList['itemListElement'][] = [
        '@type'    => 'ListItem',
        'position' => $i + 1,
        'url'      => url('project-detail.php?slug=' . $p['slug']),
        'name'     => $p['title'],
    ];
}

seo([
    'title'       => 'HieuMini - Mua bán mã nguồn website PHP MySQL chuẩn SEO',
    'description' => 'HieuMini là chợ mã nguồn website PHP MySQL chất lượng cao: thương mại điện tử, doanh nghiệp, portfolio, quản trị, giáo dục, du lịch. Code sạch, bảo mật, chuẩn SEO, tài liệu đầy đủ và hỗ trợ trọn đời.',
    'keywords'    => 'mã nguồn website, source code php, website bán hàng php, đồ án website php, mua bán mã nguồn',
    'schema'      => '<script type="application/ld+json">' . json_encode($itemList, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>',
]);

require __DIR__ . '/includes/header.php';
?>

<!-- ==================== HERO ==================== -->
<section class="hero">
  <div class="container hero__grid">
    <div class="reveal reveal--left">
      <span class="eyebrow">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true"><path d="M13 2L4 14h7l-1 8 9-12h-7z"/></svg>
        <?= $stats['projects'] ?> dự án sẵn sàng bàn giao
      </span>
      <h1>Mã nguồn website <span class="text-grad">chuẩn SEO</span>,<br>bàn giao trong 5 phút</h1>
      <p class="hero__lead">
        Mỗi dự án tại HieuMini đều viết bằng PHP 8 thuần và MySQL, code sạch có chú thích tiếng Việt,
        bảo mật PDO - CSRF - BCRYPT, kèm cơ sở dữ liệu mẫu và tài liệu cài đặt.
        Mua một lần, dùng trọn đời, hỗ trợ kỹ thuật không giới hạn.
      </p>
      <div class="hero__cta">
        <a class="btn btn--primary btn--lg" href="<?= e(url('projects.php')) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 6h16M4 12h16M4 18h10"/></svg>
          Khám phá kho dự án
        </a>
        <a class="btn btn--ghost btn--lg" href="<?= e(url('contact.php')) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M4 7l8 5 8-5"/></svg>
          Yêu cầu báo giá riêng
        </a>
      </div>
      <dl class="hero__stats">
        <div class="stat"><dd class="stat__num" data-count="<?= $stats['projects'] ?>">0</dd><dt class="stat__label">Dự án</dt></div>
        <div class="stat"><dd class="stat__num" data-count="<?= $stats['sales'] ?>" data-suffix="+">0</dd><dt class="stat__label">Lượt mua</dt></div>
        <div class="stat"><dd class="stat__num" data-count="<?= max($stats['users'] * 137, 500) ?>" data-suffix="+">0</dd><dt class="stat__label">Thành viên</dt></div>
        <div class="stat"><dd class="stat__num">4.8<span aria-hidden="true">★</span></dd><dt class="stat__label">Điểm hài lòng</dt></div>
      </dl>
    </div>

    <div class="hero__visual reveal reveal--right">
      <div class="glass hero__card">
        <div class="mock-window">
          <div class="mock-window__bar"><i></i><i></i><i></i><span>hieumini.vn/projects</span></div>
          <div class="mock-window__body">
            <div class="mock-line mock-line--60"></div>
            <div class="mock-line mock-line--80"></div>
            <div class="mock-grid"><i></i><i></i><i></i><i></i><i></i><i></i></div>
            <div class="mock-line mock-line--40"></div>
          </div>
        </div>
      </div>
      <div class="glass hero__card hero__card--2">
        <div class="kpi__label">Doanh thu tháng này</div>
        <div class="kpi__value"><?= money(48600000) ?></div>
        <div class="kpi__trend">▲ 18,4% so với tháng trước</div>
      </div>
    </div>
  </div>
</section>

<!-- ==================== CÔNG NGHỆ ==================== -->
<section class="section--tight">
  <div class="container">
    <div class="marquee" aria-hidden="true">
      <div class="marquee__track">
        <?php $techs = ['PHP 8.2', 'MySQL 8', 'PDO', 'JavaScript ES6', 'CSS Grid', 'Chart.js', 'AJAX', 'Schema.org', 'WebP', 'Responsive', 'BCRYPT', 'CSRF']; ?>
        <?php for ($k = 0; $k < 2; $k++): foreach ($techs as $t): ?>
          <span class="marquee__item"><?= e($t) ?></span>
        <?php endforeach; endfor; ?>
      </div>
    </div>
  </div>
</section>

<!-- ==================== DANH MỤC ==================== -->
<section class="section" id="danh-muc">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Danh mục</span>
      <h2>Chọn đúng loại website bạn cần</h2>
      <p>Sáu nhóm dự án bao phủ hầu hết nhu cầu của doanh nghiệp nhỏ, freelancer và sinh viên làm đồ án.</p>
    </div>
    <div class="grid grid--3 stagger">
      <?php foreach ($categories as $cat): ?>
        <a class="card cat-tile reveal" href="<?= e(url('projects.php?cat=' . $cat['slug'])) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="4" width="18" height="16" rx="3"/><path d="M3 9h18M8 4v5"/></svg>
          <span>
            <strong><?= e($cat['name']) ?></strong>
            <small><?= (int)$cat['total'] ?> dự án · <?= e(excerpt($cat['description'], 52)) ?></small>
          </span>
        </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==================== DỰ ÁN NỔI BẬT ==================== -->
<section class="section" id="noi-bat">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Bán chạy nhất</span>
      <h2>Dự án được chọn nhiều nhất</h2>
      <p>Đã có <?= num($stats['sales']) ?> lượt bàn giao. Mỗi dự án đều có bản demo và tài liệu hướng dẫn đi kèm.</p>
    </div>
    <div class="grid grid--3 stagger">
      <?php foreach ($featured as $p) { include __DIR__ . '/includes/project-card.php'; } ?>
    </div>
    <p style="text-align:center;margin-top:var(--sp-6)">
      <a class="btn btn--outline btn--lg" href="<?= e(url('projects.php')) ?>">Xem toàn bộ <?= $stats['projects'] ?> dự án</a>
    </p>
  </div>
</section>

<!-- ==================== VÌ SAO CHỌN ==================== -->
<section class="section" id="vi-sao">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Cam kết</span>
      <h2>Vì sao nên mua tại HieuMini?</h2>
    </div>
    <div class="grid grid--3 stagger">
      <?php
      $features = [
          ['Code sạch, chú thích tiếng Việt', 'Cấu trúc thư mục rõ ràng, tách bạch cấu hình - thư viện - giao diện - quản trị. Người mới đọc cũng hiểu và sửa được.', 'M8 6l-6 6 6 6M16 6l6 6-6 6'],
          ['Bảo mật đúng chuẩn', 'PDO Prepared Statement chống SQL Injection, htmlspecialchars chống XSS, token CSRF cho mọi biểu mẫu, mật khẩu băm BCRYPT.', 'M12 3l8 4v5c0 5-3.4 8.4-8 9-4.6-.6-8-4-8-9V7z'],
          ['Chuẩn SEO ngay từ đầu', 'Thẻ tiêu đề - mô tả riêng từng trang, đường dẫn thân thiện, sitemap.xml, robots.txt và dữ liệu có cấu trúc Schema.org.', 'M11 4a7 7 0 1 0 0 14 7 7 0 0 0 0-14zM20 20l-4-4'],
          ['Tốc độ và trải nghiệm', 'Ảnh SVG/WebP, tải chậm ngoài màn hình đầu, hoạt ảnh chỉ dùng transform và opacity nên giữ được 60 khung hình mỗi giây.', 'M13 2L4 14h7l-1 8 9-12h-7z'],
          ['Cơ sở dữ liệu mẫu đầy đủ', 'Mỗi dự án kèm tệp database.sql có sẵn dữ liệu thật, nhập một lần là chạy được ngay, không phải ngồi nhập tay.', 'M4 6c0-1.7 3.6-3 8-3s8 1.3 8 3-3.6 3-8 3-8-1.3-8-3zM4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6'],
          ['Hỗ trợ trọn đời', 'Hướng dẫn cài đặt qua Zalo hoặc TeamViewer, sửa lỗi miễn phí, cập nhật phiên bản mới không tính thêm phí.', 'M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z'],
      ];
      foreach ($features as $f): ?>
        <div class="card feature reveal">
          <div class="feature__icon">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="<?= $f[2] ?>"/></svg>
          </div>
          <h3><?= e($f[0]) ?></h3>
          <p><?= e($f[1]) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==================== QUY TRÌNH ==================== -->
<section class="section" id="quy-trinh">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Quy trình</span>
      <h2>Bốn bước để có website của riêng bạn</h2>
    </div>
    <div class="grid grid--4 stagger">
      <?php
      $steps = [
          ['01', 'Chọn dự án', 'Lọc theo danh mục, công nghệ và mức giá. Xem ảnh chụp màn hình và bản demo trực tuyến trước khi quyết định.'],
          ['02', 'Chọn giấy phép', 'Cá nhân để học tập, Thương mại để dùng cho doanh nghiệp, Mở rộng khi bạn bàn giao lại cho khách hàng.'],
          ['03', 'Thanh toán', 'Chuyển khoản ngân hàng, ví MoMo hoặc thanh toán khi nhận bàn giao. Đơn hàng được xác nhận trong vòng 30 phút.'],
          ['04', 'Nhận bàn giao', 'Nhận mã nguồn, tệp cơ sở dữ liệu và tài liệu. Được hỗ trợ cài đặt lên máy chủ hoặc hosting của bạn.'],
      ];
      foreach ($steps as $s): ?>
        <div class="card step reveal">
          <div class="step__num"><?= e($s[0]) ?></div>
          <h3><?= e($s[1]) ?></h3>
          <p style="font-size:var(--fs-sm);color:var(--fg-muted);margin:0"><?= e($s[2]) ?></p>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==================== MỚI NHẤT ==================== -->
<section class="section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Vừa lên kệ</span>
      <h2>Dự án mới nhất</h2>
    </div>
    <div class="grid grid--3 stagger">
      <?php foreach ($newest as $p) { include __DIR__ . '/includes/project-card.php'; } ?>
    </div>
  </div>
</section>

<!-- ==================== ĐÁNH GIÁ ==================== -->
<?php if ($topReviews): ?>
<section class="section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Khách hàng nói gì</span>
      <h2>Đánh giá từ người đã mua</h2>
    </div>
    <div class="grid grid--3 stagger">
      <?php foreach ($topReviews as $r): ?>
        <figure class="card testimonial reveal" style="margin:0">
          <?= stars((float)$r['rating']) ?>
          <blockquote class="testimonial__text" style="margin:0">“<?= e($r['content']) ?>”</blockquote>
          <figcaption class="testimonial__who">
            <span class="avatar" aria-hidden="true"><?= e(mb_substr($r['full_name'], 0, 1, 'UTF-8')) ?></span>
            <span>
              <strong style="display:block;font-size:var(--fs-sm)"><?= e($r['full_name']) ?></strong>
              <a style="font-size:var(--fs-xs)" href="<?= e(url('project-detail.php?slug=' . $r['project_slug'])) ?>"><?= e($r['project_title']) ?></a>
            </span>
          </figcaption>
        </figure>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>

<!-- ==================== BLOG ==================== -->
<section class="section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Blog</span>
      <h2>Kiến thức lập trình web</h2>
      <p>Những bài viết giúp bạn chọn, triển khai và tối ưu website hiệu quả hơn.</p>
    </div>
    <div class="grid grid--3 stagger">
      <?php foreach ($posts as $post): ?>
        <article class="card post-card reveal">
          <a class="post-card__media" href="<?= e(url('blog-detail.php?slug=' . $post['slug'])) ?>" tabindex="-1" aria-hidden="true">
            <img src="<?= e(asset($post['thumbnail'])) ?>" alt="" width="800" height="450" loading="lazy" decoding="async">
          </a>
          <div class="project-card__body">
            <span class="project-card__cat"><?= e(vn_date($post['published_at'])) ?> · <?= num((int)$post['views']) ?> lượt đọc</span>
            <h3 class="project-card__title"><a href="<?= e(url('blog-detail.php?slug=' . $post['slug'])) ?>"><?= e($post['title']) ?></a></h3>
            <p class="project-card__desc"><?= e(excerpt($post['excerpt'], 110)) ?></p>
          </div>
        </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ==================== CTA ==================== -->
<section class="section">
  <div class="container">
    <div class="cta-band reveal reveal--zoom">
      <h2>Chưa tìm được dự án phù hợp?</h2>
      <p style="max-width:60ch;margin-inline:auto">
        Gửi mô tả yêu cầu của bạn, HieuMini sẽ tư vấn dự án gần nhất hoặc báo giá tuỳ chỉnh riêng
        trong vòng 24 giờ làm việc.
      </p>
      <div class="hero__cta" style="justify-content:center">
        <a class="btn btn--primary btn--lg" href="<?= e(url('contact.php')) ?>">Gửi yêu cầu tư vấn</a>
        <a class="btn btn--ghost btn--lg" href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone'))) ?>">Gọi <?= e(setting('contact_phone')) ?></a>
      </div>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
