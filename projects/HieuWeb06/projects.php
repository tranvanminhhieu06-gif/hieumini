<?php
/**
 * HieuMini - Danh sách dự án (kèm tìm kiếm, lọc, sắp xếp, phân trang)
 */
require_once __DIR__ . '/includes/config.php';

$categories = get_categories($pdo);

// ---------- Tham số lọc ----------
$q       = trim((string)($_GET['q'] ?? ''));
$catSlug = trim((string)($_GET['cat'] ?? ''));
$sort    = (string)($_GET['sort'] ?? 'newest');
$priceRange = (string)($_GET['price'] ?? '');
$page    = max(1, (int)($_GET['page'] ?? 1));
$perPage = 9;

$where  = ['p.status = 1'];
$params = [];

if ($q !== '') {
    $where[] = '(p.title LIKE ? OR p.short_desc LIKE ? OR p.tech_stack LIKE ?)';
    $like = '%' . $q . '%';
    array_push($params, $like, $like, $like);
}

$activeCat = null;
if ($catSlug !== '') {
    foreach ($categories as $c) {
        if ($c['slug'] === $catSlug) {
            $activeCat = $c;
            break;
        }
    }
    if ($activeCat) {
        $where[] = 'p.category_id = ?';
        $params[] = $activeCat['id'];
    }
}

$priceRanges = [
    'duoi-1tr' => ['Dưới 1 triệu', 0, 999999],
    '1-2tr'    => ['1 - 2 triệu', 1000000, 2000000],
    '2-3tr'    => ['2 - 3 triệu', 2000000, 3000000],
    'tren-3tr' => ['Trên 3 triệu', 3000000, 99999999],
];
if (isset($priceRanges[$priceRange])) {
    $where[] = 'IFNULL(NULLIF(p.sale_price,0), p.price) BETWEEN ? AND ?';
    $params[] = $priceRanges[$priceRange][1];
    $params[] = $priceRanges[$priceRange][2];
}

$orderBy = match ($sort) {
    'price-asc'  => 'IFNULL(NULLIF(p.sale_price,0), p.price) ASC',
    'price-desc' => 'IFNULL(NULLIF(p.sale_price,0), p.price) DESC',
    'popular'    => 'p.sales DESC',
    'rating'     => 'p.rating_avg DESC, p.rating_count DESC',
    default      => 'p.created_at DESC, p.id DESC',
};

$whereSql = implode(' AND ', $where);

$countStmt = $pdo->prepare("SELECT COUNT(*) FROM projects p WHERE $whereSql");
$countStmt->execute($params);
$total = (int)$countStmt->fetchColumn();
$totalPages = max(1, (int)ceil($total / $perPage));
$page = min($page, $totalPages);
$offset = ($page - 1) * $perPage;

$stmt = $pdo->prepare("SELECT p.*, c.name AS category_name FROM projects p
                       JOIN categories c ON c.id = p.category_id
                       WHERE $whereSql ORDER BY $orderBy LIMIT $perPage OFFSET $offset");
$stmt->execute($params);
$projects = $stmt->fetchAll();

// ---------- SEO ----------
$title = $activeCat
    ? $activeCat['name'] . ' - Mã nguồn website PHP MySQL | ' . SITE_NAME
    : 'Kho dự án website PHP MySQL chuẩn SEO | ' . SITE_NAME;
if ($q !== '') {
    $title = 'Kết quả tìm kiếm "' . $q . '" | ' . SITE_NAME;
}
seo([
    'title'       => $title,
    'description' => $activeCat
        ? 'Danh sách mã nguồn website ' . mb_strtolower($activeCat['name']) . ': ' . $activeCat['description'] . '. Code PHP 8 sạch, bảo mật, chuẩn SEO, có tài liệu và hỗ trợ.'
        : 'Kho ' . $total . ' dự án website PHP MySQL: thương mại điện tử, doanh nghiệp, portfolio, quản trị, giáo dục, du lịch. Lọc theo danh mục, giá và công nghệ.',
    'robots'      => $q !== '' ? 'noindex, follow' : 'index, follow, max-image-preview:large',
]);

$queryBase = http_build_query(array_filter([
    'q' => $q, 'cat' => $catSlug, 'sort' => $sort !== 'newest' ? $sort : '', 'price' => $priceRange,
]));

require __DIR__ . '/includes/header.php';
?>

<div class="container page-head">
  <?php
  $crumbs = ['Trang chủ' => 'index.php'];
  if ($activeCat) {
      $crumbs['Kho dự án'] = 'projects.php';
      $crumbs[$activeCat['name']] = null;
  } else {
      $crumbs['Kho dự án'] = null;
  }
  ?>
  <?= breadcrumb($crumbs) ?>
  <h1 class="reveal" style="margin-top:var(--sp-4)">
    <?= $q !== '' ? 'Kết quả cho “' . e($q) . '”' : e($activeCat['name'] ?? 'Toàn bộ kho dự án') ?>
  </h1>
  <p class="reveal" style="max-width:70ch">
    <?= e($activeCat['description'] ?? 'Mọi dự án đều viết bằng PHP 8 thuần và MySQL, kèm cơ sở dữ liệu mẫu, tài liệu cài đặt và hỗ trợ kỹ thuật trọn đời.') ?>
  </p>
</div>

<div class="container section--tight">
  <div class="layout-sidebar">

    <!-- ===== Bộ lọc ===== -->
    <aside class="glass filter-panel reveal reveal--left" aria-label="Bộ lọc dự án">
      <form method="get" action="<?= e(url('projects.php')) ?>" class="filter-group">
        <h3>Tìm kiếm</h3>
        <label class="sr-only" for="sideSearch">Từ khoá</label>
        <input class="input" type="search" id="sideSearch" name="q" value="<?= e($q) ?>" placeholder="Tên dự án, công nghệ…">
        <?php if ($catSlug): ?><input type="hidden" name="cat" value="<?= e($catSlug) ?>"><?php endif; ?>
        <button class="btn btn--primary btn--sm btn--block" style="margin-top:10px" type="submit">Tìm</button>
      </form>

      <div class="filter-group">
        <h3>Danh mục</h3>
        <ul class="filter-list">
          <li><a class="<?= $catSlug === '' ? 'is-active' : '' ?>" href="<?= e(url('projects.php')) ?>">
            <span>Tất cả</span><small><?= (int)$pdo->query('SELECT COUNT(*) FROM projects WHERE status=1')->fetchColumn() ?></small></a></li>
          <?php foreach ($categories as $c): ?>
            <li><a class="<?= $catSlug === $c['slug'] ? 'is-active' : '' ?>" href="<?= e(url('projects.php?cat=' . $c['slug'])) ?>">
              <span><?= e($c['name']) ?></span><small><?= (int)$c['total'] ?></small></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="filter-group">
        <h3>Khoảng giá</h3>
        <ul class="filter-list">
          <?php foreach ($priceRanges as $key => $r): ?>
            <?php $qs = http_build_query(array_filter(['q' => $q, 'cat' => $catSlug, 'sort' => $sort, 'price' => $priceRange === $key ? '' : $key])); ?>
            <li><a class="<?= $priceRange === $key ? 'is-active' : '' ?>" href="<?= e(url('projects.php?' . $qs)) ?>"><span><?= e($r[0]) ?></span></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div class="filter-group">
        <h3>Cam kết</h3>
        <ul class="check-list">
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Mã nguồn đầy đủ, không mã hoá</li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Kèm database.sql dữ liệu mẫu</li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M20 6L9 17l-5-5"/></svg>Hỗ trợ cài đặt miễn phí</li>
        </ul>
      </div>
    </aside>

    <!-- ===== Kết quả ===== -->
    <div>
      <div class="toolbar reveal">
        <p class="result-count" role="status">
          Tìm thấy <strong><?= num($total) ?></strong> dự án<?= $q !== '' ? ' cho từ khoá “' . e($q) . '”' : '' ?>
        </p>
        <form method="get" action="<?= e(url('projects.php')) ?>">
          <?php if ($q): ?><input type="hidden" name="q" value="<?= e($q) ?>"><?php endif; ?>
          <?php if ($catSlug): ?><input type="hidden" name="cat" value="<?= e($catSlug) ?>"><?php endif; ?>
          <?php if ($priceRange): ?><input type="hidden" name="price" value="<?= e($priceRange) ?>"><?php endif; ?>
          <label class="sr-only" for="sortSelect">Sắp xếp theo</label>
          <select class="input" id="sortSelect" name="sort" onchange="this.form.submit()" style="min-width:220px">
            <option value="newest"     <?= $sort === 'newest' ? 'selected' : '' ?>>Mới nhất</option>
            <option value="popular"    <?= $sort === 'popular' ? 'selected' : '' ?>>Bán chạy nhất</option>
            <option value="rating"     <?= $sort === 'rating' ? 'selected' : '' ?>>Đánh giá cao nhất</option>
            <option value="price-asc"  <?= $sort === 'price-asc' ? 'selected' : '' ?>>Giá thấp đến cao</option>
            <option value="price-desc" <?= $sort === 'price-desc' ? 'selected' : '' ?>>Giá cao đến thấp</option>
          </select>
          <noscript><button class="btn btn--ghost btn--sm" type="submit">Áp dụng</button></noscript>
        </form>
      </div>

      <?php if ($projects): ?>
        <div class="grid grid--3 stagger">
          <?php foreach ($projects as $p) { include __DIR__ . '/includes/project-card.php'; } ?>
        </div>
        <?= pagination($page, $totalPages, $queryBase) ?>
      <?php else: ?>
        <div class="glass empty-state reveal">
          <svg viewBox="0 0 24 24" aria-hidden="true"><circle cx="11" cy="11" r="7"/><path d="M20 20l-3.5-3.5"/></svg>
          <h2>Không tìm thấy dự án phù hợp</h2>
          <p>Thử bỏ bớt bộ lọc, dùng từ khoá ngắn hơn, hoặc gửi yêu cầu để được tư vấn dự án gần nhất.</p>
          <div class="hero__cta" style="justify-content:center">
            <a class="btn btn--ghost" href="<?= e(url('projects.php')) ?>">Xoá bộ lọc</a>
            <a class="btn btn--primary" href="<?= e(url('contact.php')) ?>">Yêu cầu tư vấn</a>
          </div>
        </div>
      <?php endif; ?>
    </div>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
