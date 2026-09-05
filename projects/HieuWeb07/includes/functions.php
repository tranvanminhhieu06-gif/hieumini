<?php
/**
 * HieuMini Books — Hàm dùng chung
 */

declare(strict_types=1);

/* ---------------------------------------------------------------------
 | 1. AN TOÀN ĐẦU RA
 * ------------------------------------------------------------------- */

/**
 * Escape trước khi in ra HTML. Mọi giá trị lấy từ CSDL hoặc từ người dùng
 * đều phải đi qua hàm này — đây là hàng phòng thủ chính chống XSS.
 */
function e(?string $s): string
{
    return htmlspecialchars($s ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/* ---------------------------------------------------------------------
 | 2. ĐƯỜNG DẪN
 * ------------------------------------------------------------------- */

function url(string $path = ''): string
{
    return base_url() . '/' . ltrim($path, '/');
}

/**
 * Đường dẫn tới tệp tĩnh, kèm dấu thời gian sửa đổi để trình duyệt tự nạp
 * lại khi tệp đổi mà không cần người dùng xoá bộ nhớ đệm.
 */
function asset(string $path): string
{
    $file = ROOT_PATH . '/assets/' . ltrim($path, '/');
    $v = is_file($file) ? (string) filemtime($file) : '1';
    return url('assets/' . ltrim($path, '/')) . '?v=' . $v;
}

function cover_url(?string $file): string
{
    if ($file && is_file(ROOT_PATH . '/assets/img/covers/' . $file)) {
        return asset('img/covers/' . $file);
    }
    return asset('img/covers/placeholder.webp');
}

function current_page(): string
{
    return basename($_SERVER['SCRIPT_NAME'] ?? '');
}

function nav_active(string $file): string
{
    return current_page() === $file ? 'is-active' : '';
}

/** URL tuyệt đối — cần cho thẻ canonical, Open Graph và sitemap. */
function abs_url(string $path = ''): string
{
    $scheme = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off' ? 'https' : 'http';
    $host   = $_SERVER['HTTP_HOST'] ?? 'localhost';
    return $scheme . '://' . $host . url($path);
}

/* ---------------------------------------------------------------------
 | 3. XỬ LÝ CHUỖI
 * ------------------------------------------------------------------- */

/** Bỏ dấu tiếng Việt để tạo slug hoặc để so khớp khi tìm kiếm. */
function strip_accents(string $s): string
{
    $map = [
        'a' => 'áàảãạăắằẳẵặâấầẩẫậ', 'e' => 'éèẻẽẹêếềểễệ', 'i' => 'íìỉĩị',
        'o' => 'óòỏõọôốồổỗộơớờởỡợ', 'u' => 'úùủũụưứừửữự', 'y' => 'ýỳỷỹỵ', 'd' => 'đ',
    ];
    $s = mb_strtolower($s, 'UTF-8');
    foreach ($map as $plain => $accented) {
        $s = preg_replace('/[' . $accented . ']/u', $plain, $s);
    }
    return $s;
}

function slugify(string $s): string
{
    $s = strip_accents($s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    return trim($s, '-');
}

/** Cắt ngắn theo ranh giới từ, không cắt giữa chừng một chữ. */
function excerpt(?string $s, int $limit = 160): string
{
    $s = trim(preg_replace('/\s+/u', ' ', $s ?? ''));
    if (mb_strlen($s, 'UTF-8') <= $limit) {
        return $s;
    }
    $cut = mb_substr($s, 0, $limit, 'UTF-8');
    $sp  = mb_strrpos($cut, ' ', 0, 'UTF-8');
    return rtrim($sp ? mb_substr($cut, 0, $sp, 'UTF-8') : $cut, ' ,.;:') . '…';
}

/** Bôi đậm phần khớp từ khoá trong gợi ý tìm kiếm. */
function highlight(string $text, string $needle): string
{
    if ($needle === '') {
        return e($text);
    }
    $pos = mb_stripos($text, $needle, 0, 'UTF-8');
    if ($pos === false) {
        return e($text);
    }
    $len = mb_strlen($needle, 'UTF-8');
    return e(mb_substr($text, 0, $pos, 'UTF-8'))
        . '<em>' . e(mb_substr($text, $pos, $len, 'UTF-8')) . '</em>'
        . e(mb_substr($text, $pos + $len, null, 'UTF-8'));
}

function format_year(?int $y): string
{
    if (!$y) {
        return '—';
    }
    return $y < 0 ? abs($y) . ' TCN' : (string) $y;
}

function format_number(int $n): string
{
    return number_format($n, 0, ',', '.');
}

/* ---------------------------------------------------------------------
 | 4. BẢO MẬT BIỂU MẪU
 * ------------------------------------------------------------------- */

/** Sinh (hoặc lấy lại) mã chống giả mạo biểu mẫu cho phiên hiện tại. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf'];
}

function csrf_field(): string
{
    return '<input type="hidden" name="csrf" value="' . e(csrf_token()) . '">';
}

/** So sánh bằng hash_equals để không bị dò mã qua thời gian phản hồi. */
function csrf_check(?string $token): bool
{
    return !empty($_SESSION['csrf']) && is_string($token) && hash_equals($_SESSION['csrf'], $token);
}

/* ---------------------------------------------------------------------
 | 5. THÔNG BÁO CHỚP NHOÁNG
 * ------------------------------------------------------------------- */

function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

function flash_pull(): array
{
    $f = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $f;
}

/* ---------------------------------------------------------------------
 | 6. GIAO DIỆN
 * ------------------------------------------------------------------- */

/**
 * Biểu tượng SVG nội tuyến. Dùng SVG chứ không dùng emoji: emoji hiển thị
 * khác nhau trên từng hệ điều hành và trình đọc màn hình đọc thành câu dài.
 */
function icon(string $name, string $class = 'ico'): string
{
    $paths = [
        'search'  => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'moon'    => '<path d="M21 12.8A9 9 0 1 1 11.2 3a7 7 0 0 0 9.8 9.8Z"/>',
        'sun'     => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2m0 16v2M4.9 4.9l1.4 1.4m11.4 11.4 1.4 1.4M2 12h2m16 0h2M4.9 19.1l1.4-1.4M17.7 6.3l1.4-1.4"/>',
        'menu'    => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close'   => '<path d="M6 6l12 12M18 6 6 18"/>',
        'arrow'   => '<path d="M5 12h14m-6-6 6 6-6 6"/>',
        'book'    => '<path d="M4 5a2 2 0 0 1 2-2h12v18H6a2 2 0 0 1-2-2Z"/><path d="M8 3v18"/>',
        'user'    => '<circle cx="12" cy="8" r="4"/><path d="M4 21c0-4 3.6-6 8-6s8 2 8 6"/>',
        'tag'     => '<path d="M3 12V5a2 2 0 0 1 2-2h7l9 9-9 9-9-9Z"/><circle cx="8" cy="8" r="1.4"/>',
        'star'    => '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.2-5.4-2.9-5.4 2.9 1-6.2L3.2 9.5l6.1-.9Z"/>',
        'mail'    => '<rect x="3" y="5" width="18" height="14" rx="2"/><path d="m3 7 9 6 9-6"/>',
        'check'   => '<path d="m5 13 4 4L19 7"/>',
        'grid'    => '<rect x="3" y="3" width="7" height="7"/><rect x="14" y="3" width="7" height="7"/><rect x="3" y="14" width="7" height="7"/><rect x="14" y="14" width="7" height="7"/>',
        'quote'   => '<path d="M9 7H5v6h4l-2 4h3l2-4V7Zm10 0h-4v6h4l-2 4h3l2-4V7Z"/>',
    ];
    $d = $paths[$name] ?? $paths['book'];
    return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
        . ' stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"'
        . ' focusable="false">' . $d . '</svg>';
}

/** Dải sao đánh giá. Có kèm chữ cho người dùng trình đọc màn hình. */
function stars(float $rating, bool $label = true): string
{
    $full = (int) round($rating);
    $out = '<span class="rating"';
    $out .= $label ? ' role="img" aria-label="' . e(number_format($rating, 1)) . ' trên 5 sao">' : ' aria-hidden="true">';
    for ($i = 1; $i <= 5; $i++) {
        $cls = $i <= $full ? 'rating-full' : 'rating-empty';
        $out .= '<svg class="' . $cls . '" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">'
            . '<path d="m12 3 2.7 5.6 6.1.9-4.4 4.3 1 6.2-5.4-2.9-5.4 2.9 1-6.2L3.2 9.5l6.1-.9Z"/></svg>';
    }
    return $out . '</span>';
}

/* ---------------------------------------------------------------------
 | 7. PHÂN TRANG
 * ------------------------------------------------------------------- */

/**
 * Vẽ thanh phân trang dạng 1 … 4 5 6 … 20 để không tràn dòng khi
 * số trang lớn.
 */
function pagination(int $current, int $total, callable $link): string
{
    if ($total < 2) {
        return '';
    }
    $out = '<nav class="pagination" aria-label="Phân trang danh sách sách">';
    if ($current > 1) {
        $out .= '<a href="' . e($link($current - 1)) . '" rel="prev">Trước</a>';
    }

    $window = [1, $total];
    for ($i = $current - 1; $i <= $current + 1; $i++) {
        if ($i > 1 && $i < $total) {
            $window[] = $i;
        }
    }
    $window = array_unique($window);
    sort($window);

    $prev = 0;
    foreach ($window as $p) {
        if ($prev && $p - $prev > 1) {
            $out .= '<span class="is-gap" aria-hidden="true">…</span>';
        }
        $out .= $p === $current
            ? '<span class="is-current" aria-current="page">' . $p . '</span>'
            : '<a href="' . e($link($p)) . '">' . $p . '</a>';
        $prev = $p;
    }

    if ($current < $total) {
        $out .= '<a href="' . e($link($current + 1)) . '" rel="next">Sau</a>';
    }
    return $out . '</nav>';
}

/* ---------------------------------------------------------------------
 | 8. TRUY VẤN NGHIỆP VỤ
 * ------------------------------------------------------------------- */

function all_categories(): array
{
    static $c = null;
    return $c ??= db_all('SELECT * FROM categories ORDER BY sort_order, name');
}

function site_stats(): array
{
    static $s = null;
    return $s ??= [
        'books'      => (int) db_value('SELECT COUNT(*) FROM books'),
        'authors'    => (int) db_value('SELECT COUNT(*) FROM authors'),
        'categories' => (int) db_value('SELECT COUNT(*) FROM categories'),
        'reviews'    => (int) db_value('SELECT COUNT(*) FROM reviews WHERE is_approved = 1'),
    ];
}

/** Câu SELECT dùng lại ở mọi nơi cần kèm tên tác giả và thể loại. */
function book_select_sql(): string
{
    return 'SELECT b.*, a.name AS author_name, a.slug AS author_slug,
                   c.name AS category_name, c.slug AS category_slug, c.accent AS category_accent,
                   (SELECT ROUND(AVG(r.rating), 1) FROM reviews r
                     WHERE r.book_id = b.id AND r.is_approved = 1) AS rating_avg,
                   (SELECT COUNT(*) FROM reviews r
                     WHERE r.book_id = b.id AND r.is_approved = 1) AS rating_count
            FROM books b
            JOIN authors a    ON a.id = b.author_id
            JOIN categories c ON c.id = b.category_id';
}
