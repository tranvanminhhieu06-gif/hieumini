<?php
/**
 * HieuMini - Thư viện hàm dùng chung
 * -----------------------------------------------------------------
 * Gồm 6 nhóm: tiện ích chung, bảo mật, phiên/người dùng,
 * giỏ hàng, truy vấn dữ liệu và SEO.
 */

declare(strict_types=1);

/* =================================================================
 * NHÓM 1 - TIỆN ÍCH CHUNG
 * ============================================================== */

/** Escape HTML - dùng cho MỌI dữ liệu in ra màn hình (chống XSS). */
function e(?string $value): string
{
    return htmlspecialchars((string)$value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Sinh URL tuyệt đối từ đường dẫn tương đối. */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** Đường dẫn tới tài nguyên tĩnh (ảnh, css, js). */
function asset(string $path): string
{
    return url($path);
}

/** Định dạng số kiểu Việt Nam: 5212 -> "5.212" */
function num(float|int|string|null $number): string
{
    return number_format((float)$number, 0, ',', '.');
}

/** Định dạng tiền Việt: 1890000 -> "1.890.000 ₫" */
function money(float|int|string|null $amount): string
{
    return number_format((float)$amount, 0, ',', '.') . ' ₫';
}

/** Rút gọn chuỗi theo số ký tự, cắt tại khoảng trắng gần nhất. */
function excerpt(?string $text, int $limit = 120): string
{
    $text = trim(preg_replace('/\s+/u', ' ', (string)$text) ?? '');
    if (mb_strlen($text, 'UTF-8') <= $limit) {
        return $text;
    }
    $cut = mb_substr($text, 0, $limit, 'UTF-8');
    $pos = mb_strrpos($cut, ' ', 0, 'UTF-8');
    return rtrim($pos ? mb_substr($cut, 0, $pos, 'UTF-8') : $cut, ' ,.;:') . '…';
}

/** Chuyển tiêu đề tiếng Việt có dấu thành slug thân thiện SEO. */
function slugify(string $text): string
{
    $map = [
        'à','á','ạ','ả','ã','â','ầ','ấ','ậ','ẩ','ẫ','ă','ằ','ắ','ặ','ẳ','ẵ',
        'è','é','ẹ','ẻ','ẽ','ê','ề','ế','ệ','ể','ễ',
        'ì','í','ị','ỉ','ĩ',
        'ò','ó','ọ','ỏ','õ','ô','ồ','ố','ộ','ổ','ỗ','ơ','ờ','ớ','ợ','ở','ỡ',
        'ù','ú','ụ','ủ','ũ','ư','ừ','ứ','ự','ử','ữ',
        'ỳ','ý','ỵ','ỷ','ỹ','đ',
    ];
    $rep = [
        'a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a','a',
        'e','e','e','e','e','e','e','e','e','e','e',
        'i','i','i','i','i',
        'o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o','o',
        'u','u','u','u','u','u','u','u','u','u','u',
        'y','y','y','y','y','d',
    ];
    $text = str_replace($map, $rep, mb_strtolower(trim($text), 'UTF-8'));
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? '';
    return trim($text, '-');
}

/** Chuyển hướng và dừng script. */
function redirect(string $path): never
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

/** Ngày giờ kiểu Việt Nam. */
function vn_date(?string $datetime, bool $withTime = false): string
{
    if (!$datetime) {
        return '';
    }
    $ts = strtotime($datetime);
    return date($withTime ? 'H:i d/m/Y' : 'd/m/Y', $ts ?: time());
}

/** Giá bán thực tế (ưu tiên giá khuyến mãi). */
function final_price(array $project): float
{
    return (float)($project['sale_price'] ?? 0) > 0
        ? (float)$project['sale_price']
        : (float)$project['price'];
}

/** Phần trăm giảm giá, trả về 0 nếu không giảm. */
function discount_percent(array $project): int
{
    $price = (float)$project['price'];
    $sale  = (float)($project['sale_price'] ?? 0);
    if ($sale <= 0 || $price <= 0 || $sale >= $price) {
        return 0;
    }
    return (int)round(($price - $sale) / $price * 100);
}

/* =================================================================
 * NHÓM 2 - BẢO MẬT
 * ============================================================== */

/** Lấy (hoặc tạo) token CSRF của phiên hiện tại. */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** In ra thẻ input ẩn chứa token CSRF. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Kiểm tra token CSRF gửi lên từ biểu mẫu. */
function csrf_verify(?string $token): bool
{
    return !empty($_SESSION['csrf_token'])
        && is_string($token)
        && hash_equals($_SESSION['csrf_token'], $token);
}

/** Dừng ngay nếu POST không kèm token hợp lệ. */
function csrf_guard(): void
{
    if ($_SERVER['REQUEST_METHOD'] === 'POST' && !csrf_verify($_POST['csrf_token'] ?? null)) {
        http_response_code(419);
        exit('Phiên làm việc đã hết hạn. Vui lòng tải lại trang và thử lại.');
    }
}

/** Làm sạch dữ liệu đầu vào dạng chuỗi. */
function input(string $key, string $default = '', string $method = 'POST'): string
{
    $src = $method === 'GET' ? $_GET : $_POST;
    return trim((string)($src[$key] ?? $default));
}

/* =================================================================
 * NHÓM 3 - PHIÊN LÀM VIỆC & NGƯỜI DÙNG
 * ============================================================== */

/** Ghi một thông báo hiển thị ở lần tải trang kế tiếp. */
function flash(string $message, string $type = 'success'): void
{
    $_SESSION['flash'][] = ['message' => $message, 'type' => $type];
}

/** Lấy và xoá toàn bộ thông báo đang chờ. */
function take_flash(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

function is_logged_in(): bool
{
    return !empty($_SESSION['user_id']);
}

function is_admin(): bool
{
    return ($_SESSION['user_role'] ?? '') === 'admin';
}

function current_user_id(): int
{
    return (int)($_SESSION['user_id'] ?? 0);
}

function current_user_name(): string
{
    return (string)($_SESSION['user_name'] ?? '');
}

/** Bắt buộc đăng nhập, nhớ lại trang đang xem để quay lại sau. */
function require_login(): void
{
    if (!is_logged_in()) {
        $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'] ?? '';
        flash('Vui lòng đăng nhập để tiếp tục.', 'warning');
        redirect('login.php');
    }
}

/** Bắt buộc quyền quản trị. */
function require_admin(): void
{
    if (!is_logged_in() || !is_admin()) {
        redirect('admin/login.php');
    }
}

/* =================================================================
 * NHÓM 4 - GIỎ HÀNG (lưu trong session)
 * Cấu trúc: $_SESSION['cart'][project_id] = ['license'=>..., 'qty'=>1]
 * ============================================================== */

function cart_items(): array
{
    return $_SESSION['cart'] ?? [];
}

function cart_count(): int
{
    return count(cart_items());
}

function cart_add(int $projectId, string $license = 'personal'): void
{
    $_SESSION['cart'][$projectId] = ['license' => $license, 'qty' => 1];
}

function cart_remove(int $projectId): void
{
    unset($_SESSION['cart'][$projectId]);
}

function cart_clear(): void
{
    unset($_SESSION['cart'], $_SESSION['coupon']);
}

/** Hệ số nhân giá theo loại giấy phép sử dụng. */
function license_multiplier(string $license): float
{
    return match ($license) {
        'commercial' => 1.6,
        'extended'   => 2.4,
        default      => 1.0,
    };
}

function license_label(string $license): string
{
    return match ($license) {
        'commercial' => 'Thương mại',
        'extended'   => 'Mở rộng (bàn giao khách hàng)',
        default      => 'Cá nhân',
    };
}

/** Lấy chi tiết giỏ hàng kèm dữ liệu sản phẩm và tổng tiền. */
function cart_detail(PDO $pdo): array
{
    $items = cart_items();
    if (!$items) {
        return ['rows' => [], 'subtotal' => 0.0, 'discount' => 0.0, 'total' => 0.0, 'coupon' => null];
    }

    $ids = array_map('intval', array_keys($items));
    $in  = implode(',', array_fill(0, count($ids), '?'));
    $stmt = $pdo->prepare("SELECT * FROM projects WHERE id IN ($in) AND status = 1");
    $stmt->execute($ids);

    $rows = [];
    $subtotal = 0.0;
    foreach ($stmt->fetchAll() as $p) {
        $license = $items[$p['id']]['license'] ?? 'personal';
        $line    = round(final_price($p) * license_multiplier($license));
        $subtotal += $line;
        $rows[] = ['project' => $p, 'license' => $license, 'line_total' => $line];
    }

    [$discount, $coupon] = coupon_discount($pdo, $subtotal);

    return [
        'rows'     => $rows,
        'subtotal' => $subtotal,
        'discount' => $discount,
        'total'    => max(0, $subtotal - $discount),
        'coupon'   => $coupon,
    ];
}

/** Tính tiền giảm từ mã giảm giá đang áp dụng trong phiên. */
function coupon_discount(PDO $pdo, float $subtotal): array
{
    $code = $_SESSION['coupon'] ?? null;
    if (!$code) {
        return [0.0, null];
    }
    $stmt = $pdo->prepare('SELECT * FROM coupons WHERE code = ? AND status = 1
                           AND (expires_at IS NULL OR expires_at >= CURDATE())
                           AND used_count < usage_limit');
    $stmt->execute([$code]);
    $c = $stmt->fetch();
    if (!$c || $subtotal < (float)$c['min_total']) {
        return [0.0, null];
    }
    $discount = $c['type'] === 'percent'
        ? round($subtotal * (float)$c['value'] / 100)
        : (float)$c['value'];
    return [min($discount, $subtotal), $c];
}

/* =================================================================
 * NHÓM 5 - TRUY VẤN DỮ LIỆU DÙNG LẠI
 * ============================================================== */

function load_settings(PDO $pdo): array
{
    try {
        $rows = $pdo->query('SELECT setting_key, setting_value FROM settings')->fetchAll();
    } catch (PDOException) {
        return [];
    }
    return array_column($rows, 'setting_value', 'setting_key');
}

function setting(string $key, string $default = ''): string
{
    global $SETTINGS;
    return (string)($SETTINGS[$key] ?? $default);
}

function get_categories(PDO $pdo): array
{
    static $cache = null;
    if ($cache === null) {
        $cache = $pdo->query('SELECT c.*, (SELECT COUNT(*) FROM projects p WHERE p.category_id = c.id AND p.status = 1) AS total
                              FROM categories c WHERE c.status = 1 ORDER BY c.sort_order, c.id')->fetchAll();
    }
    return $cache;
}

function get_project_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT p.*, c.name AS category_name, c.slug AS category_slug
                           FROM projects p JOIN categories c ON c.id = p.category_id
                           WHERE p.slug = ? AND p.status = 1 LIMIT 1');
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

function get_post_by_slug(PDO $pdo, string $slug): ?array
{
    $stmt = $pdo->prepare('SELECT * FROM posts WHERE slug = ? AND status = 1 LIMIT 1');
    $stmt->execute([$slug]);
    return $stmt->fetch() ?: null;
}

/** Cập nhật lại điểm đánh giá trung bình của một dự án. */
function refresh_rating(PDO $pdo, int $projectId): void
{
    $stmt = $pdo->prepare('UPDATE projects p SET
            p.rating_avg   = (SELECT IFNULL(AVG(r.rating),0) FROM reviews r WHERE r.project_id = p.id AND r.status = 1),
            p.rating_count = (SELECT COUNT(*) FROM reviews r WHERE r.project_id = p.id AND r.status = 1)
        WHERE p.id = ?');
    $stmt->execute([$projectId]);
}

/** Người dùng đã mua dự án này chưa (dùng để cho phép đánh giá). */
function has_purchased(PDO $pdo, int $userId, int $projectId): bool
{
    $stmt = $pdo->prepare('SELECT 1 FROM order_items oi
                           JOIN orders o ON o.id = oi.order_id
                           WHERE o.user_id = ? AND oi.project_id = ? AND o.status IN ("paid","delivered") LIMIT 1');
    $stmt->execute([$userId, $projectId]);
    return (bool)$stmt->fetchColumn();
}

function in_wishlist(PDO $pdo, int $userId, int $projectId): bool
{
    if (!$userId) {
        return false;
    }
    $stmt = $pdo->prepare('SELECT 1 FROM wishlists WHERE user_id = ? AND project_id = ? LIMIT 1');
    $stmt->execute([$userId, $projectId]);
    return (bool)$stmt->fetchColumn();
}

/** Sinh mã đơn hàng dạng HM + ngày + số thứ tự. */
function generate_order_code(PDO $pdo): string
{
    $prefix = 'HM' . date('Ymd');
    $stmt = $pdo->prepare('SELECT COUNT(*) FROM orders WHERE order_code LIKE ?');
    $stmt->execute([$prefix . '%']);
    return $prefix . str_pad((string)((int)$stmt->fetchColumn() + 1), 3, '0', STR_PAD_LEFT);
}

/* =================================================================
 * NHÓM 6 - SEO
 * ============================================================== */

/**
 * Khai báo dữ liệu SEO cho trang hiện tại.
 * Gọi TRƯỚC khi nạp header.php.
 */
function seo(array $data): void
{
    global $SEO;
    $SEO = array_merge($SEO ?? [], $data);
}

/** Lấy một trường SEO đã khai báo. */
function seo_get(string $key, string $default = ''): string
{
    global $SEO;
    return (string)($SEO[$key] ?? $default);
}

/** Vẽ chuỗi đường dẫn phân cấp kèm schema BreadcrumbList. */
function breadcrumb(array $items): string
{
    $html = '<nav class="breadcrumb" aria-label="Đường dẫn"><ol>';
    $list = [];
    $i = 1;
    foreach ($items as $label => $href) {
        $isLast = ($i === count($items));
        $html .= '<li>' . ($isLast || !$href
            ? '<span aria-current="page">' . e((string)$label) . '</span>'
            : '<a href="' . e($href) . '">' . e((string)$label) . '</a>') . '</li>';
        $list[] = [
            '@type'    => 'ListItem',
            'position' => $i,
            'name'     => (string)$label,
            'item'     => $href ? (str_starts_with($href, 'http') ? $href : url($href)) : url(),
        ];
        $i++;
    }
    $html .= '</ol></nav>';
    $html .= '<script type="application/ld+json">' . json_encode([
        '@context'        => 'https://schema.org',
        '@type'           => 'BreadcrumbList',
        'itemListElement' => $list,
    ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>';
    return $html;
}

/** Vẽ khối sao đánh giá (có nhãn cho trình đọc màn hình). */
function stars(float $rating, int $count = 0): string
{
    $full = (int)round($rating);
    $html = '<span class="stars" role="img" aria-label="' . number_format($rating, 1) . ' trên 5 sao">';
    for ($i = 1; $i <= 5; $i++) {
        $html .= '<svg class="star ' . ($i <= $full ? 'is-on' : '') . '" viewBox="0 0 24 24" aria-hidden="true" focusable="false"><path d="M12 2.5l2.9 6.1 6.6.9-4.8 4.6 1.2 6.6L12 17.6 6.1 20.7l1.2-6.6L2.5 9.5l6.6-.9z"/></svg>';
    }
    $html .= '</span>';
    if ($count > 0) {
        $html .= '<span class="rating-count">' . number_format($rating, 1) . ' (' . $count . ' đánh giá)</span>';
    }
    return $html;
}

/** Bộ đánh số trang. */
function pagination(int $current, int $totalPages, string $baseQuery = ''): string
{
    if ($totalPages < 2) {
        return '';
    }
    $q = $baseQuery !== '' ? $baseQuery . '&' : '';
    $html = '<nav class="pagination" aria-label="Phân trang"><ul>';
    if ($current > 1) {
        $html .= '<li><a href="?' . $q . 'page=' . ($current - 1) . '" rel="prev">Trước</a></li>';
    }
    $start = max(1, $current - 2);
    $end   = min($totalPages, $current + 2);
    if ($start > 1) {
        $html .= '<li><a href="?' . $q . 'page=1">1</a></li>' . ($start > 2 ? '<li><span>…</span></li>' : '');
    }
    for ($i = $start; $i <= $end; $i++) {
        $html .= $i === $current
            ? '<li><span class="is-current" aria-current="page">' . $i . '</span></li>'
            : '<li><a href="?' . $q . 'page=' . $i . '">' . $i . '</a></li>';
    }
    if ($end < $totalPages) {
        $html .= ($end < $totalPages - 1 ? '<li><span>…</span></li>' : '')
              . '<li><a href="?' . $q . 'page=' . $totalPages . '">' . $totalPages . '</a></li>';
    }
    if ($current < $totalPages) {
        $html .= '<li><a href="?' . $q . 'page=' . ($current + 1) . '" rel="next">Sau</a></li>';
    }
    return $html . '</ul></nav>';
}
