<?php
/**
 * Gợi ý tìm kiếm — trả JSON cho ô tìm kiếm ở trang Kho sách.
 *
 * Giữ ở mức tối giản: chỉ đọc, không nhận tham số nào ngoài từ khoá, và
 * luôn giới hạn số dòng trả về để không ai dùng nó để rút cả cơ sở dữ liệu.
 */
declare(strict_types=1);

require __DIR__ . '/../config/config.php';
require __DIR__ . '/../config/database.php';
require __DIR__ . '/../includes/functions.php';

header('Content-Type: application/json; charset=utf-8');
header('X-Content-Type-Options: nosniff');
header('Cache-Control: public, max-age=60');

$q = trim((string) ($_GET['q'] ?? ''));

// Dưới 2 ký tự thì mọi thứ đều khớp — trả rỗng cho đỡ tốn truy vấn
if (mb_strlen($q, 'UTF-8') < 2) {
    echo json_encode(['query' => $q, 'items' => []], JSON_UNESCAPED_UNICODE);
    exit;
}

$rows = db_all(
    'SELECT b.title, b.slug, b.cover, b.published_year, a.name AS author_name
       FROM books b JOIN authors a ON a.id = b.author_id
      WHERE b.title LIKE :q_title OR a.name LIKE :q_author
      ORDER BY (b.title LIKE :q_starts) DESC, b.views DESC
      LIMIT 6',
    // Mỗi vị trí một tên tham số riêng — MySQL không cho dùng lại một tên.
    // :q_starts đẩy các tựa bắt đầu bằng từ khoá lên đầu danh sách gợi ý.
    [':q_title' => '%' . $q . '%', ':q_author' => '%' . $q . '%', ':q_starts' => $q . '%']
);

$items = array_map(static fn(array $r): array => [
    'title'  => $r['title'],
    'author' => $r['author_name'],
    'year'   => (int) $r['published_year'],
    'url'    => url('book.php?s=' . $r['slug']),
    'cover'  => cover_url($r['cover']),
], $rows);

echo json_encode(['query' => $q, 'items' => $items], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES);
