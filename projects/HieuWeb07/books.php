<?php
/**
 * Kho sách — tìm kiếm, lọc theo thể loại, sắp xếp và phân trang.
 *
 * Toàn bộ điều kiện lọc nằm trên URL (?q=&the-loai=&sap-xep=&trang=) nên
 * người dùng chia sẻ được link kết quả và nút Quay lại của trình duyệt
 * hoạt động đúng như mong đợi.
 */
declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$q       = trim((string) ($_GET['q'] ?? ''));
$catSlug = trim((string) ($_GET['the-loai'] ?? ''));
$sort    = (string) ($_GET['sap-xep'] ?? 'moi');
$page    = max(1, (int) ($_GET['trang'] ?? 1));

// Danh sách trắng: giá trị sắp xếp không bao giờ được ghép thẳng vào SQL
$sortMap = [
    'moi'      => 'b.published_year DESC, b.id DESC',
    'cu'       => 'b.published_year ASC, b.id ASC',
    'ten'      => 'b.title ASC',
    'xem-nhieu' => 'b.views DESC',
];
$orderBy = $sortMap[$sort] ?? $sortMap['moi'];

$where  = [];
$params = [];

if ($q !== '') {
    // Tìm cả trong tựa, tên tác giả và tóm tắt để người dùng gõ gì cũng ra.
    // Mỗi vị trí phải có tên tham số riêng: prepared statement thật của MySQL
    // không cho tái sử dụng một tên cho nhiều dấu hỏi.
    $where[] = '(b.title LIKE :q_title OR a.name LIKE :q_author OR b.summary LIKE :q_summary)';
    $params[':q_title'] = $params[':q_author'] = $params[':q_summary'] = '%' . $q . '%';
}
if ($catSlug !== '') {
    $where[] = 'c.slug = :cat';
    $params[':cat'] = $catSlug;
}
$whereSql = $where ? ' WHERE ' . implode(' AND ', $where) : '';

$total = (int) db_value(
    'SELECT COUNT(*) FROM books b JOIN authors a ON a.id = b.author_id
     JOIN categories c ON c.id = b.category_id' . $whereSql,
    $params
);

$totalPages = max(1, (int) ceil($total / BOOKS_PER_PAGE));
$page       = min($page, $totalPages);
$offset     = ($page - 1) * BOOKS_PER_PAGE;

// LIMIT/OFFSET phải là số nguyên đã kiểm soát, không nhận trực tiếp từ URL
$books = db_all(
    book_select_sql() . $whereSql . ' ORDER BY ' . $orderBy .
    ' LIMIT ' . BOOKS_PER_PAGE . ' OFFSET ' . $offset,
    $params
);

$cats       = all_categories();
$activeCat  = null;
foreach ($cats as $c) {
    if ($c['slug'] === $catSlug) {
        $activeCat = $c;
    }
}

/** Dựng lại URL hiện tại nhưng thay một tham số — dùng cho chip lọc và phân trang. */
function build_url(array $overrides = []): string
{
    $params = array_merge($_GET, $overrides);
    $params = array_filter($params, static fn($v) => $v !== '' && $v !== null);
    return url('books.php') . ($params ? '?' . http_build_query($params) : '');
}

$pageTitle = $activeCat
    ? $activeCat['name'] . ' — Kho sách ' . SITE_NAME
    : ($q !== '' ? 'Kết quả cho “' . $q . '” — ' . SITE_NAME : 'Kho sách — ' . SITE_NAME);
$pageDesc = $activeCat
    ? excerpt($activeCat['description'], 155)
    : 'Duyệt toàn bộ ' . site_stats()['books'] . ' đầu sách của thư viện HieuMini theo thể loại, tác giả và năm xuất bản.';

require __DIR__ . '/includes/header.php';
?>

<section class="section section--tight">
  <div class="container">

    <nav aria-label="Đường dẫn" style="margin-bottom:var(--space-5);font-size:var(--fs-sm);color:var(--fg-subtle)">
      <a href="<?= e(url('index.php')) ?>">Trang chủ</a>
      <span aria-hidden="true"> / </span>
      <span>Kho sách<?= $activeCat ? ' / ' . e($activeCat['name']) : '' ?></span>
    </nav>

    <h1 style="font-size:var(--fs-3xl)" data-split>
      <?= $activeCat ? e($activeCat['name']) : ($q !== '' ? 'Kết quả tìm kiếm' : 'Toàn bộ kho sách') ?>
    </h1>
    <p class="lead" style="margin-bottom:var(--space-7)">
      <?php if ($q !== ''): ?>
        Tìm thấy <strong><?= format_number($total) ?></strong> kết quả cho từ khoá “<?= e($q) ?>”.
      <?php elseif ($activeCat): ?>
        <?= e($activeCat['description']) ?>
      <?php else: ?>
        <?= format_number($total) ?> đầu sách, sắp xếp và lọc theo ý bạn.
      <?php endif; ?>
    </p>

    <form class="toolbar" method="get" action="<?= e(url('books.php')) ?>" role="search">
      <div class="search-box">
        <?= icon('search', 'search-ico') ?>
        <label for="q" class="skip-link">Từ khoá tìm sách</label>
        <input type="search" id="q" name="q" value="<?= e($q) ?>"
               placeholder="Tìm theo tựa sách, tác giả…"
               autocomplete="off" role="combobox" aria-expanded="false"
               aria-controls="suggestBox" aria-autocomplete="list">
        <div class="suggest" id="suggestBox" role="listbox" aria-label="Gợi ý tìm kiếm" hidden></div>
      </div>

      <?php if ($catSlug !== ''): ?>
        <input type="hidden" name="the-loai" value="<?= e($catSlug) ?>">
      <?php endif; ?>

      <label for="sap-xep" class="skip-link">Sắp xếp theo</label>
      <select class="sort-select" name="sap-xep" id="sap-xep" onchange="this.form.submit()">
        <option value="moi"       <?= $sort === 'moi' ? 'selected' : '' ?>>Mới xuất bản trước</option>
        <option value="cu"        <?= $sort === 'cu' ? 'selected' : '' ?>>Xuất bản lâu nhất trước</option>
        <option value="ten"       <?= $sort === 'ten' ? 'selected' : '' ?>>Theo tên A → Z</option>
        <option value="xem-nhieu" <?= $sort === 'xem-nhieu' ? 'selected' : '' ?>>Xem nhiều nhất</option>
      </select>

      <noscript><button class="btn btn--primary" type="submit">Tìm</button></noscript>
    </form>

    <div class="filter-chips" style="margin-bottom:var(--space-7)">
      <a class="chip <?= $catSlug === '' ? 'is-active' : '' ?>" href="<?= e(build_url(['the-loai' => null, 'trang' => null])) ?>">
        Tất cả
      </a>
      <?php foreach ($cats as $c): ?>
        <a class="chip <?= $catSlug === $c['slug'] ? 'is-active' : '' ?>"
           href="<?= e(build_url(['the-loai' => $c['slug'], 'trang' => null])) ?>"><?= e($c['name']) ?></a>
      <?php endforeach; ?>
    </div>

    <div class="book-grid">
      <?php if (!$books): ?>
        <?php /* Không bao giờ để màn hình trắng — nói rõ vì sao rỗng và mở lối đi tiếp */ ?>
        <div class="empty-state">
          <?= icon('search', 'ico') ?>
          <h3>Không tìm thấy cuốn nào khớp</h3>
          <p>
            <?php if ($q !== ''): ?>
              Từ khoá “<?= e($q) ?>” chưa khớp với tựa sách, tác giả hay tóm tắt nào trong kho.
              Thử rút ngắn từ khoá, hoặc bắt đầu từ một thể loại bên dưới.
            <?php else: ?>
              Thể loại này chưa có sách nào. Thử xem các thể loại khác.
            <?php endif; ?>
          </p>
          <div class="empty-suggest">
            <?php foreach (array_slice($cats, 0, 4) as $c): ?>
              <a class="chip" href="<?= e(url('books.php?the-loai=' . $c['slug'])) ?>"><?= e($c['name']) ?></a>
            <?php endforeach; ?>
            <a class="chip" href="<?= e(url('books.php')) ?>">Xem toàn bộ kho</a>
          </div>
        </div>
      <?php else: ?>
        <?php foreach ($books as $b): ?>
          <?php require __DIR__ . '/includes/book-card.php'; ?>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <?= pagination($page, $totalPages, static fn(int $p) => build_url(['trang' => $p > 1 ? $p : null])) ?>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
