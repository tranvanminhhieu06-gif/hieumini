<?php
/**
 * HieuMini - Chi tiết dự án
 */
require_once __DIR__ . '/includes/config.php';

$slug = trim((string)($_GET['slug'] ?? ''));
$p = $slug !== '' ? get_project_by_slug($pdo, $slug) : null;

if (!$p) {
    http_response_code(404);
    seo(['title' => 'Không tìm thấy dự án | ' . SITE_NAME, 'robots' => 'noindex, follow']);
    require __DIR__ . '/includes/header.php';
    echo '<div class="container section"><div class="glass empty-state"><h1>404 - Không tìm thấy dự án</h1>'
       . '<p>Dự án bạn tìm có thể đã được đổi tên hoặc gỡ khỏi kho.</p>'
       . '<a class="btn btn--primary" href="' . e(url('projects.php')) . '">Về kho dự án</a></div></div>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

// Tăng lượt xem (mỗi phiên chỉ tính một lần cho mỗi dự án)
if (empty($_SESSION['viewed'][$p['id']])) {
    $pdo->prepare('UPDATE projects SET views = views + 1 WHERE id = ?')->execute([$p['id']]);
    $_SESSION['viewed'][$p['id']] = true;
}

// ---------- Gửi đánh giá ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST' && ($_POST['action'] ?? '') === 'review') {
    csrf_guard();
    require_login();
    $rating  = max(1, min(5, (int)($_POST['rating'] ?? 5)));
    $content = mb_substr(input('content'), 0, 1000, 'UTF-8');

    if ($content === '') {
        flash('Vui lòng nhập nội dung đánh giá.', 'warning');
    } else {
        $stmt = $pdo->prepare('INSERT INTO reviews (project_id, user_id, rating, content)
                               VALUES (?,?,?,?)
                               ON DUPLICATE KEY UPDATE rating = VALUES(rating), content = VALUES(content)');
        $stmt->execute([$p['id'], current_user_id(), $rating, $content]);
        refresh_rating($pdo, (int)$p['id']);
        flash('Cảm ơn bạn đã gửi đánh giá!');
    }
    redirect('project-detail.php?slug=' . $p['slug'] . '#danh-gia');
}

// ---------- Dữ liệu liên quan ----------
$reviews = $pdo->prepare('SELECT r.*, u.full_name FROM reviews r JOIN users u ON u.id = r.user_id
                          WHERE r.project_id = ? AND r.status = 1 ORDER BY r.created_at DESC');
$reviews->execute([$p['id']]);
$reviews = $reviews->fetchAll();

$related = $pdo->prepare('SELECT p.*, c.name AS category_name FROM projects p
                          JOIN categories c ON c.id = p.category_id
                          WHERE p.category_id = ? AND p.id <> ? AND p.status = 1
                          ORDER BY p.sales DESC LIMIT 3');
$related->execute([$p['category_id'], $p['id']]);
$related = $related->fetchAll();

$features = array_filter(array_map('trim', explode("\n", (string)$p['features'])));
$techs    = array_filter(array_map('trim', explode(',', (string)$p['tech_stack'])));
$isFav    = in_wishlist($pdo, current_user_id(), (int)$p['id']);
$canReview = is_logged_in();

// ---------- SEO: schema Product ----------
$product = [
    '@context'    => 'https://schema.org',
    '@type'       => 'Product',
    'name'        => $p['title'],
    'description' => $p['short_desc'],
    'image'       => asset($p['thumbnail']),
    'sku'         => 'HM-' . str_pad((string)$p['id'], 4, '0', STR_PAD_LEFT),
    'brand'       => ['@type' => 'Brand', 'name' => SITE_NAME],
    'category'    => $p['category_name'],
    'offers'      => [
        '@type'         => 'Offer',
        'url'           => url('project-detail.php?slug=' . $p['slug']),
        'price'         => (string)(int)final_price($p),
        'priceCurrency' => 'VND',
        'availability'  => 'https://schema.org/InStock',
        'priceValidUntil' => date('Y-12-31'),
    ],
];
if ((int)$p['rating_count'] > 0) {
    $product['aggregateRating'] = [
        '@type'       => 'AggregateRating',
        'ratingValue' => number_format((float)$p['rating_avg'], 1, '.', ''),
        'reviewCount' => (int)$p['rating_count'],
        'bestRating'  => '5',
    ];
}

seo([
    'title'       => ($p['meta_title'] ?: $p['title'] . ' | ' . SITE_NAME),
    'description' => ($p['meta_description'] ?: excerpt($p['short_desc'], 155)),
    'keywords'    => (string)$p['meta_keywords'],
    'image'       => asset($p['thumbnail']),
    'og_type'     => 'product',
    'schema'      => '<script type="application/ld+json">' . json_encode($product, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container page-head">
  <?= breadcrumb([
      'Trang chủ' => 'index.php',
      'Kho dự án' => 'projects.php',
      $p['category_name'] => 'projects.php?cat=' . $p['category_slug'],
      $p['title'] => null,
  ]) ?>
</div>

<div class="container section--tight">
  <div class="detail-grid">

    <!-- ===== Cột nội dung ===== -->
    <div>
      <div class="detail-hero reveal">
        <img src="<?= e(asset($p['thumbnail'])) ?>" alt="Ảnh giao diện dự án <?= e($p['title']) ?>"
             width="800" height="500" fetchpriority="high" decoding="async">
      </div>

      <div class="reveal" style="margin-top:var(--sp-5)">
        <div style="display:flex;flex-wrap:wrap;gap:8px;margin-bottom:var(--sp-3)">
          <span class="chip"><?= e($p['category_name']) ?></span>
          <?php if ($p['badge']): ?><span class="badge badge--hot"><?= e($p['badge']) ?></span><?php endif; ?>
          <span class="badge"><?= num((int)$p['views']) ?> lượt xem</span>
          <span class="badge"><?= num((int)$p['sales']) ?> lượt mua</span>
        </div>
        <h1><?= e($p['title']) ?></h1>
        <p style="font-size:var(--fs-lg)"><?= e($p['short_desc']) ?></p>
        <div style="margin-bottom:var(--sp-5)"><?= stars((float)$p['rating_avg'], (int)$p['rating_count']) ?></div>
      </div>

      <!-- ===== Tabs ===== -->
      <div class="reveal" data-tabs>
        <div class="tabs" role="tablist" aria-label="Thông tin dự án">
          <button role="tab" id="tab-mota" aria-controls="panel-mota" aria-selected="true" class="is-active">Mô tả chi tiết</button>
          <button role="tab" id="tab-tinhnang" aria-controls="panel-tinhnang" aria-selected="false">Tính năng</button>
          <button role="tab" id="tab-congnghe" aria-controls="panel-congnghe" aria-selected="false">Công nghệ</button>
          <button role="tab" id="tab-danhgia" aria-controls="panel-danhgia" aria-selected="false">Đánh giá (<?= count($reviews) ?>)</button>
        </div>

        <section class="tab-panel" id="panel-mota" role="tabpanel" aria-labelledby="tab-mota">
          <?php foreach (array_filter(array_map('trim', explode("\n", (string)$p['description']))) as $para): ?>
            <p><?= e($para) ?></p>
          <?php endforeach; ?>
        </section>

        <section class="tab-panel" id="panel-tinhnang" role="tabpanel" aria-labelledby="tab-tinhnang" hidden>
          <ul class="check-list">
            <?php foreach ($features as $f): ?>
              <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg><?= e($f) ?></li>
            <?php endforeach; ?>
            <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Tệp <code>database.sql</code> kèm dữ liệu mẫu</li>
            <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Tài liệu cài đặt và hướng dẫn tuỳ biến</li>
          </ul>
        </section>

        <section class="tab-panel" id="panel-congnghe" role="tabpanel" aria-labelledby="tab-congnghe" hidden>
          <div class="tech-list" style="margin-bottom:var(--sp-4)">
            <?php foreach ($techs as $t): ?><span><?= e($t) ?></span><?php endforeach; ?>
          </div>
          <div class="table-wrap">
            <table class="data">
              <caption class="sr-only">Yêu cầu hệ thống của dự án</caption>
              <tbody>
                <tr><th scope="row">Ngôn ngữ máy chủ</th><td>PHP 8.0 trở lên (khuyến nghị 8.2)</td></tr>
                <tr><th scope="row">Cơ sở dữ liệu</th><td>MySQL 5.7+ hoặc MariaDB 10.4+</td></tr>
                <tr><th scope="row">Máy chủ web</th><td>Apache có mod_rewrite, hoặc Nginx</td></tr>
                <tr><th scope="row">Môi trường thử nghiệm</th><td>XAMPP, Laragon, Docker</td></tr>
                <tr><th scope="row">Trình duyệt hỗ trợ</th><td>Chrome, Edge, Firefox, Safari phiên bản hai năm gần nhất</td></tr>
              </tbody>
            </table>
          </div>
        </section>

        <section class="tab-panel" id="panel-danhgia" role="tabpanel" aria-labelledby="tab-danhgia" hidden>
          <div id="danh-gia">
            <?php if ($reviews): ?>
              <?php foreach ($reviews as $r): ?>
                <article class="review">
                  <div class="review__head">
                    <span class="avatar" aria-hidden="true"><?= e(mb_substr($r['full_name'], 0, 1, 'UTF-8')) ?></span>
                    <div>
                      <strong style="display:block"><?= e($r['full_name']) ?></strong>
                      <small style="color:var(--fg-muted)"><?= e(vn_date($r['created_at'], true)) ?></small>
                    </div>
                    <span style="margin-left:auto"><?= stars((float)$r['rating']) ?></span>
                  </div>
                  <p style="margin:0"><?= e($r['content']) ?></p>
                </article>
              <?php endforeach; ?>
            <?php else: ?>
              <p>Chưa có đánh giá nào. Hãy là người đầu tiên chia sẻ trải nghiệm.</p>
            <?php endif; ?>
          </div>

          <?php if ($canReview): ?>
            <form class="glass" method="post" style="padding:var(--sp-5);margin-top:var(--sp-5)" data-validate>
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="review">
              <h3>Viết đánh giá của bạn</h3>
              <div class="form-grid">
                <div class="field">
                  <label for="rating">Số sao</label>
                  <select id="rating" name="rating">
                    <?php for ($i = 5; $i >= 1; $i--): ?>
                      <option value="<?= $i ?>"><?= $i ?> sao</option>
                    <?php endfor; ?>
                  </select>
                </div>
                <div class="field">
                  <label for="content">Nội dung</label>
                  <textarea id="content" name="content" required maxlength="1000"
                            placeholder="Bạn thấy mã nguồn này thế nào? Cài đặt có dễ không?"></textarea>
                  <span class="hint">Tối đa 1000 ký tự. Đánh giá của bạn giúp người mua sau quyết định dễ hơn.</span>
                </div>
                <button class="btn btn--primary" type="submit">Gửi đánh giá</button>
              </div>
            </form>
          <?php else: ?>
            <p style="margin-top:var(--sp-4)">
              <a class="btn btn--ghost" href="<?= e(url('login.php')) ?>">Đăng nhập để viết đánh giá</a>
            </p>
          <?php endif; ?>
        </section>
      </div>
    </div>

    <!-- ===== Hộp mua hàng ===== -->
    <aside>
      <div class="glass buy-box reveal reveal--right">
        <div class="buy-box__price">
          <b><?= money(final_price($p)) ?></b>
          <?php if (discount_percent($p) > 0): ?>
            <span class="price__old"><?= money($p['price']) ?></span>
            <span class="badge badge--sale">-<?= discount_percent($p) ?>%</span>
          <?php endif; ?>
        </div>

        <fieldset style="border:0;padding:0;margin:0 0 var(--sp-4)">
          <legend style="font-size:var(--fs-sm);font-weight:600;margin-bottom:10px">Chọn giấy phép sử dụng</legend>
          <label class="license-option">
            <input type="radio" name="license" value="personal" checked>
            <span><span>Cá nhân · Học tập</span><b><?= money(final_price($p)) ?></b></span>
          </label>
          <label class="license-option">
            <input type="radio" name="license" value="commercial">
            <span><span>Thương mại · 1 tên miền</span><b><?= money(final_price($p) * 1.6) ?></b></span>
          </label>
          <label class="license-option">
            <input type="radio" name="license" value="extended">
            <span><span>Mở rộng · Bàn giao khách</span><b><?= money(final_price($p) * 2.4) ?></b></span>
          </label>
        </fieldset>

        <button class="btn btn--primary btn--block btn--lg" data-cart-add="<?= (int)$p['id'] ?>" data-csrf="<?= e(csrf_token()) ?>">
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h2l2.2 9.4a2 2 0 0 0 2 1.6h6.2a2 2 0 0 0 2-1.5L20 8H7"/><circle cx="10" cy="19" r="1.4"/><circle cx="17" cy="19" r="1.4"/></svg>
          Thêm vào giỏ hàng
        </button>
        <a class="btn btn--ghost btn--block" style="margin-top:10px" href="<?= e(url('cart.php')) ?>">Xem giỏ hàng</a>
        <button class="btn btn--outline btn--block" style="margin-top:10px"
                data-wish="<?= (int)$p['id'] ?>" data-csrf="<?= e(csrf_token()) ?>"
                aria-pressed="<?= $isFav ? 'true' : 'false' ?>">
          <?= $isFav ? 'Đã lưu yêu thích' : 'Lưu vào yêu thích' ?>
        </button>

        <ul class="check-list" style="margin-top:var(--sp-4)">
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Mã nguồn đầy đủ, không mã hoá</li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Bàn giao trong 30 phút sau xác nhận</li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Hỗ trợ cài đặt và sửa lỗi trọn đời</li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Cập nhật phiên bản mới miễn phí</li>
        </ul>

        <p class="demo-hint" style="margin-top:var(--sp-4);margin-bottom:0">
          Cần xem thử trước khi mua? Nhắn Zalo <strong><?= e(setting('contact_phone')) ?></strong> để được cấp tài khoản demo quản trị.
        </p>
      </div>
    </aside>
  </div>
</div>

<?php if ($related): ?>
<section class="section">
  <div class="container">
    <div class="section-head reveal">
      <span class="eyebrow">Gợi ý</span>
      <h2>Dự án cùng danh mục</h2>
    </div>
    <div class="grid grid--3 stagger">
      <?php foreach ($related as $p) { include __DIR__ . '/includes/project-card.php'; } ?>
    </div>
  </div>
</section>
<?php endif; ?>

<?php require __DIR__ . '/includes/footer.php'; ?>
