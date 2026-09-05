<?php
/**
 * Trang chi tiết một cuốn sách: thông tin xuất bản, tóm tắt, đánh giá
 * của bạn đọc và biểu mẫu gửi đánh giá mới.
 */
declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$slug = trim((string) ($_GET['s'] ?? ''));
$book = $slug === '' ? null : db_one(book_select_sql() . ' WHERE b.slug = :s', [':s' => $slug]);

if (!$book) {
    http_response_code(404);
    $pageTitle = 'Không tìm thấy sách — ' . SITE_NAME;
    require __DIR__ . '/includes/header.php';
    echo '<section class="section"><div class="container"><div class="empty-state">'
        . icon('book')
        . '<h1>Không tìm thấy cuốn sách này</h1>'
        . '<p>Đường dẫn có thể đã cũ hoặc cuốn sách đã được gỡ khỏi kho.</p>'
        . '<div class="empty-suggest"><a class="btn btn--primary" href="' . e(url('books.php')) . '">Về kho sách</a></div>'
        . '</div></div></section>';
    require __DIR__ . '/includes/footer.php';
    exit;
}

$errors = [];

// ---------- Nhận đánh giá mới ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check($_POST['csrf'] ?? null)) {
        $errors['form'] = 'Phiên làm việc đã hết hạn. Vui lòng tải lại trang và gửi lại.';
    } else {
        $name    = trim((string) ($_POST['reader_name'] ?? ''));
        $rating  = (int) ($_POST['rating'] ?? 0);
        $content = trim((string) ($_POST['content'] ?? ''));

        if (mb_strlen($name) < 2)                 $errors['reader_name'] = 'Vui lòng nhập tên từ 2 ký tự trở lên.';
        if ($rating < 1 || $rating > 5)           $errors['rating']      = 'Chọn số sao từ 1 đến 5.';
        if (mb_strlen($content) < 10)             $errors['content']     = 'Nhận xét cần ít nhất 10 ký tự.';
        if (mb_strlen($content) > 1000)           $errors['content']     = 'Nhận xét tối đa 1000 ký tự.';

        if (!$errors) {
            db_query(
                'INSERT INTO reviews (book_id, reader_name, rating, content, is_approved)
                 VALUES (:b, :n, :r, :c, 0)',
                [':b' => $book['id'], ':n' => $name, ':r' => $rating, ':c' => $content]
            );
            flash('ok', 'Cảm ơn bạn! Đánh giá đã được gửi và sẽ hiển thị sau khi quản trị viên duyệt.');
            header('Location: ' . url('book.php?s=' . $book['slug']));
            exit;
        }
    }
}

// Đếm lượt xem. Mỗi phiên chỉ tính một lần cho mỗi cuốn để số liệu không bị
// thổi phồng khi người đọc bấm tải lại trang.
if (empty($_SESSION['viewed'][$book['id']])) {
    db_query('UPDATE books SET views = views + 1 WHERE id = :id', [':id' => $book['id']]);
    $_SESSION['viewed'][$book['id']] = true;
    $book['views']++;
}

$reviews = db_all(
    'SELECT * FROM reviews WHERE book_id = :b AND is_approved = 1 ORDER BY created_at DESC',
    [':b' => $book['id']]
);

$related = db_all(
    book_select_sql() . ' WHERE b.category_id = :c AND b.id <> :id ORDER BY RAND() LIMIT 4',
    [':c' => $book['category_id'], ':id' => $book['id']]
);

$ratingAvg = $book['rating_avg'] !== null ? (float) $book['rating_avg'] : 0.0;

$pageTitle = $book['title'] . ' — ' . $book['author_name'] . ' | ' . SITE_NAME;
$pageDesc  = excerpt($book['summary'], 155);
$pageImage = abs_url('assets/img/covers/' . $book['cover']);
$canonical = abs_url('book.php?s=' . $book['slug']);

// Schema.org kiểu Book: giúp kết quả tìm kiếm hiện tác giả, nhà xuất bản,
// số sao đánh giá ngay dưới tiêu đề.
$jsonLd = [
    '@context'        => 'https://schema.org',
    '@type'           => 'Book',
    'name'            => $book['title'],
    'author'          => ['@type' => 'Person', 'name' => $book['author_name']],
    'publisher'       => ['@type' => 'Organization', 'name' => $book['publisher']],
    'datePublished'   => (string) $book['published_year'],
    'numberOfPages'   => (int) $book['pages'],
    'inLanguage'      => $book['language'],
    'isbn'            => $book['isbn'],
    'genre'           => $book['category_name'],
    'description'     => $book['summary'],
    'image'           => $pageImage,
    'url'             => $canonical,
];
if ((int) $book['rating_count'] > 0) {
    $jsonLd['aggregateRating'] = [
        '@type'       => 'AggregateRating',
        'ratingValue' => number_format($ratingAvg, 1, '.', ''),
        'reviewCount' => (int) $book['rating_count'],
        'bestRating'  => 5,
        'worstRating' => 1,
    ];
}

require __DIR__ . '/includes/header.php';
?>

<div class="container">
  <nav aria-label="Đường dẫn" style="padding-top:var(--space-6);font-size:var(--fs-sm);color:var(--fg-subtle)">
    <a href="<?= e(url('index.php')) ?>">Trang chủ</a>
    <span aria-hidden="true"> / </span>
    <a href="<?= e(url('books.php')) ?>">Kho sách</a>
    <span aria-hidden="true"> / </span>
    <a href="<?= e(url('books.php?the-loai=' . $book['category_slug'])) ?>"><?= e($book['category_name']) ?></a>
  </nav>

  <article class="book-detail">
    <div class="book-detail__cover">
      <img src="<?= e(cover_url($book['cover'])) ?>"
           alt="Bìa sách <?= e($book['title']) ?>"
           width="600" height="900" loading="eager" decoding="async">
    </div>

    <div class="book-detail__body">
      <span class="badge" style="color:<?= e($book['category_accent']) ?>"><?= e($book['category_name']) ?></span>

      <h1 style="margin-top:var(--space-4)" data-split><?= e($book['title']) ?></h1>

      <p style="font-size:var(--fs-lg);font-style:italic;color:var(--fg-muted);margin-bottom:var(--space-5)">
        <a href="<?= e(url('author.php?s=' . $book['author_slug'])) ?>"><?= e($book['author_name']) ?></a>
      </p>

      <?php if ((int) $book['rating_count'] > 0): ?>
        <p style="display:flex;align-items:center;gap:var(--space-3);flex-wrap:wrap">
          <?= stars($ratingAvg) ?>
          <span style="color:var(--fg)"><?= e(number_format($ratingAvg, 1)) ?>/5</span>
          <span style="color:var(--fg-subtle)">· <?= (int) $book['rating_count'] ?> đánh giá</span>
          <span style="color:var(--fg-subtle)">· <?= format_number((int) $book['views']) ?> lượt xem</span>
        </p>
      <?php else: ?>
        <p style="color:var(--fg-subtle)">Chưa có đánh giá · <?= format_number((int) $book['views']) ?> lượt xem</p>
      <?php endif; ?>

      <p style="font-size:var(--fs-lg)"><?= e($book['summary']) ?></p>

      <table class="spec-table">
        <caption class="skip-link">Thông tin xuất bản</caption>
        <tbody>
          <tr><th scope="row">Tác giả</th>       <td><?= e($book['author_name']) ?></td></tr>
          <tr><th scope="row">Thể loại</th>      <td><?= e($book['category_name']) ?></td></tr>
          <tr><th scope="row">Nhà xuất bản</th>  <td><?= e($book['publisher']) ?></td></tr>
          <tr><th scope="row">Năm xuất bản</th>  <td><?= e(format_year((int) $book['published_year'])) ?></td></tr>
          <tr><th scope="row">Số trang</th>      <td><?= format_number((int) $book['pages']) ?> trang</td></tr>
          <tr><th scope="row">Ngôn ngữ</th>      <td><?= e($book['language']) ?></td></tr>
          <tr><th scope="row">ISBN</th>          <td><code style="font-family:var(--font-mono);font-size:var(--fs-xs)"><?= e($book['isbn']) ?></code></td></tr>
        </tbody>
      </table>
    </div>
  </article>

  <section class="section" aria-labelledby="danh-gia">
    <div class="section-head">
      <div>
        <span class="eyebrow">Bạn đọc nói gì</span>
        <h2 id="danh-gia" style="font-size:var(--fs-2xl)">
          <?= $reviews ? format_number(count($reviews)) . ' đánh giá' : 'Chưa có đánh giá' ?>
        </h2>
      </div>
    </div>

    <div style="display:grid;grid-template-columns:minmax(0,1.4fr) minmax(0,1fr);gap:var(--space-8);align-items:start"
         class="review-layout">
      <div>
        <?php if (!$reviews): ?>
          <p style="color:var(--fg-subtle)">Cuốn này chưa có đánh giá nào được duyệt. Bạn là người đầu tiên nhé.</p>
        <?php else: ?>
          <?php foreach ($reviews as $r): ?>
            <div class="review">
              <div class="review-head">
                <b><?= e($r['reader_name']) ?></b>
                <?= stars((float) $r['rating'], false) ?>
                <time datetime="<?= e($r['created_at']) ?>"><?= e(date('d/m/Y', strtotime($r['created_at']))) ?></time>
              </div>
              <p><?= e($r['content']) ?></p>
            </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>

      <form method="post" style="background:var(--surface);border:1px solid var(--border);
                                 border-radius:var(--r-md);padding:var(--space-6)">
        <h3 style="font-size:var(--fs-lg);margin-bottom:var(--space-4)">Gửi đánh giá của bạn</h3>

        <?php if (!empty($errors['form'])): ?>
          <div class="alert alert--error" role="alert"><?= e($errors['form']) ?></div>
        <?php endif; ?>

        <?= csrf_field() ?>

        <div class="field">
          <label for="reader_name">Tên của bạn</label>
          <input type="text" id="reader_name" name="reader_name" required maxlength="120"
                 value="<?= e($_POST['reader_name'] ?? '') ?>"
                 <?= isset($errors['reader_name']) ? 'aria-invalid="true" aria-describedby="err-name"' : '' ?>>
          <?php if (isset($errors['reader_name'])): ?>
            <span class="error" id="err-name"><?= e($errors['reader_name']) ?></span>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="rating">Chấm điểm</label>
          <select id="rating" name="rating" required
                  <?= isset($errors['rating']) ? 'aria-invalid="true" aria-describedby="err-rating"' : '' ?>>
            <option value="">— Chọn số sao —</option>
            <?php foreach ([5 => 'Rất hay', 4 => 'Hay', 3 => 'Bình thường', 2 => 'Chưa hợp', 1 => 'Không thích'] as $v => $t): ?>
              <option value="<?= $v ?>" <?= (int) ($_POST['rating'] ?? 0) === $v ? 'selected' : '' ?>><?= $v ?> sao — <?= e($t) ?></option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($errors['rating'])): ?>
            <span class="error" id="err-rating"><?= e($errors['rating']) ?></span>
          <?php endif; ?>
        </div>

        <div class="field">
          <label for="content">Nhận xét</label>
          <textarea id="content" name="content" required minlength="10" maxlength="1000"
                    <?= isset($errors['content']) ? 'aria-invalid="true" aria-describedby="err-content"' : '' ?>
          ><?= e($_POST['content'] ?? '') ?></textarea>
          <span class="hint">Từ 10 đến 1000 ký tự. Đánh giá hiển thị sau khi được duyệt.</span>
          <?php if (isset($errors['content'])): ?>
            <span class="error" id="err-content"><?= e($errors['content']) ?></span>
          <?php endif; ?>
        </div>

        <button class="btn btn--primary" type="submit" style="width:100%"><?= icon('check') ?> Gửi đánh giá</button>
      </form>
    </div>
  </section>

  <?php if ($related): ?>
  <section class="section section--tight" aria-labelledby="lien-quan">
    <div class="section-head">
      <div>
        <span class="eyebrow">Cùng thể loại</span>
        <h2 id="lien-quan" style="font-size:var(--fs-2xl)">Có thể bạn cũng thích</h2>
      </div>
    </div>
    <div class="book-grid">
      <?php foreach ($related as $b): ?>
        <?php require __DIR__ . '/includes/book-card.php'; ?>
      <?php endforeach; ?>
    </div>
  </section>
  <?php endif; ?>
</div>

<style>
  @media (max-width: 900px) { .review-layout { grid-template-columns: 1fr !important; } }
</style>

<?php require __DIR__ . '/includes/footer.php'; ?>
