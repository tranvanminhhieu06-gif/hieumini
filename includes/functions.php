<?php
/**
 * HieuMini — Bộ hàm tiện ích dùng chung
 */

declare(strict_types=1);

/** Escape dữ liệu trước khi in ra HTML (chống XSS). */
function e(?string $value): string
{
    return htmlspecialchars((string) $value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Tạo URL tuyệt đối tính từ gốc website. */
function url(string $path = ''): string
{
    return BASE_URL . '/' . ltrim($path, '/');
}

/** Đường dẫn tới tài nguyên tĩnh, kèm tham số chống cache. */
function asset(string $path): string
{
    $file = ROOT_PATH . '/assets/' . ltrim($path, '/');
    $version = is_file($file) ? (string) filemtime($file) : '1';
    return url('assets/' . ltrim($path, '/')) . '?v=' . $version;
}

/** Đánh dấu mục điều hướng đang được chọn. */
function nav_active(string $file): string
{
    return basename($_SERVER['SCRIPT_NAME'] ?? '') === $file ? ' is-active' : '';
}

/** Chuyển chuỗi tiếng Việt thành slug thân thiện URL. */
function slugify(string $text): string
{
    $map = [
        'a' => 'áàảãạăắằẳẵặâấầẩẫậ', 'e' => 'éèẻẽẹêếềểễệ', 'i' => 'íìỉĩị',
        'o' => 'óòỏõọôốồổỗộơớờởỡợ', 'u' => 'úùủũụưứừửữự', 'y' => 'ýỳỷỹỵ', 'd' => 'đ',
    ];
    $text = mb_strtolower(trim($text), 'UTF-8');
    foreach ($map as $ascii => $accented) {
        $text = preg_replace('/[' . $accented . ']/u', $ascii, $text) ?? $text;
    }
    $text = preg_replace('/[^a-z0-9]+/', '-', $text) ?? $text;
    return trim($text, '-') ?: 'du-an';
}

/** Định dạng số theo kiểu Việt Nam: 1.234. */
function num(int|float|string|null $value): string
{
    return number_format((float) $value, 0, ',', '.');
}

/** Ngày giờ kiểu Việt Nam. */
function vn_date(?string $datetime, bool $withTime = false): string
{
    if (!$datetime) {
        return '—';
    }
    $ts = strtotime($datetime);
    return $ts ? date($withTime ? 'H:i d/m/Y' : 'd/m/Y', $ts) : '—';
}

/** Cắt chuỗi dài kèm dấu ba chấm. */
function excerpt(?string $text, int $limit = 160): string
{
    $text = trim(preg_replace('/\s+/u', ' ', (string) $text) ?? '');
    if (mb_strlen($text, 'UTF-8') <= $limit) {
        return $text;
    }
    return rtrim(mb_substr($text, 0, $limit, 'UTF-8')) . '…';
}

/** Tách chuỗi "PHP 8,MySQL,PDO" thành mảng đã trim. */
function split_list(?string $text): array
{
    if (!$text) {
        return [];
    }
    return array_values(array_filter(array_map('trim', explode(',', $text)), 'strlen'));
}

/** Chuyển văn bản nhiều đoạn (\n\n) thành các thẻ <p>. */
function paragraphs(?string $text): string
{
    $blocks = preg_split('/\R{2,}/u', trim((string) $text)) ?: [];
    $html = '';
    foreach ($blocks as $block) {
        $block = trim($block);
        if ($block !== '') {
            $html .= '<p>' . nl2br(e($block)) . '</p>';
        }
    }
    return $html;
}

/** Đường dẫn công khai tới thư mục của một dự án con. */
function project_url(array $project, string $suffix = ''): string
{
    return url('projects/' . rawurlencode($project['folder']) . '/' . ltrim($suffix, '/'));
}

/**
 * Cấu hình đăng nhập quản trị "chế độ trưng bày" cho từng dự án con.
 * Sáu dự án dùng biểu mẫu đăng nhập khác nhau (email + mật khẩu), đường dẫn
 * đăng nhập khác nhau, và HieuWeb06 còn yêu cầu token CSRF. Bảng dưới đây chỉ
 * lưu ba thứ tối thiểu: đường dẫn biểu mẫu, email quản trị và mật khẩu demo.
 * Cầu nối open-admin.php sẽ tự điền và gửi chính biểu mẫu thật của dự án đó,
 * nhờ vậy không cần biết cấu trúc phiên hay xử lý CSRF của từng dự án.
 *
 * Trả về null nếu dự án không nằm trong danh sách hỗ trợ đăng nhập tự động.
 */
function project_admin_demo(string $code): ?array
{
    static $map = [
        'HieuWeb01' => ['login' => 'admin/login.php', 'email' => 'admin@hieumini.vn'],
        'HieuWeb02' => ['login' => 'login.php',       'email' => 'admin@hieumini.vn'],
        'HieuWeb03' => ['login' => 'login.php',       'email' => 'admin@hieumini.vn'],
        'HieuWeb04' => ['login' => 'admin/login.php', 'email' => 'admin@datcyber.vn'],
        'HieuWeb05' => ['login' => 'admin/login.php', 'email' => 'admin@hieumini.com'],
        'HieuWeb06' => ['login' => 'admin/login.php', 'email' => 'admin@hieumini.vn'],
    ];

    if (!isset($map[$code])) {
        return null;
    }

    return $map[$code] + ['password' => 'demo123'];
}

/** Kiểm tra thư mục dự án con có thực sự tồn tại trên đĩa hay không. */
function project_exists(array $project): bool
{
    return is_dir(PROJECTS_DIR . DIRECTORY_SEPARATOR . $project['folder']);
}

/**
 * Bộ icon SVG nội bộ (không dùng emoji làm icon).
 * Tất cả icon đều nét 1.6px, khung 24×24, kế thừa currentColor.
 */
function icon(string $name, string $class = 'ico'): string
{
    $paths = [
        'bolt'       => '<path d="M13 2 4.5 13.5H11l-1 8.5 8.5-11.5H12z"/>',
        'filter'     => '<path d="M3 5h18M6 12h12M10 19h4"/>',
        'ruler'      => '<path d="M3 15 15 3l6 6L9 21z"/><path d="M7 11l2 2M10 8l2 2M13 5l2 2"/>',
        'wallet'     => '<path d="M3 7a2 2 0 0 1 2-2h13v4"/><path d="M3 7v10a2 2 0 0 0 2 2h14a2 2 0 0 0 2-2v-6a2 2 0 0 0-2-2H5"/><circle cx="16.5" cy="14" r="1.2"/>',
        'layers'     => '<path d="m12 3 9 5-9 5-9-5z"/><path d="m3 13 9 5 9-5"/>',
        'refresh'    => '<path d="M21 12a9 9 0 1 1-3-6.7"/><path d="M21 4v5h-5"/>',
        'chart'      => '<path d="M4 20V10M10 20V4M16 20v-7M22 20H2"/>',
        'shield'     => '<path d="M12 3l8 3v6c0 5-3.4 8.3-8 9-4.6-.7-8-4-8-9V6z"/><path d="m9 12 2 2 4-4"/>',
        'database'   => '<ellipse cx="12" cy="6" rx="8" ry="3"/><path d="M4 6v12c0 1.7 3.6 3 8 3s8-1.3 8-3V6"/><path d="M4 12c0 1.7 3.6 3 8 3s8-1.3 8-3"/>',
        'grid'       => '<rect x="3" y="3" width="7" height="7" rx="1.5"/><rect x="14" y="3" width="7" height="7" rx="1.5"/><rect x="3" y="14" width="7" height="7" rx="1.5"/><rect x="14" y="14" width="7" height="7" rx="1.5"/>',
        'search'     => '<circle cx="11" cy="11" r="7"/><path d="m20 20-3.5-3.5"/>',
        'star'       => '<path d="m12 3 2.7 5.7 6.3.9-4.5 4.4 1 6.2-5.5-2.9-5.5 2.9 1-6.2L3 9.6l6.3-.9z"/>',
        'list'       => '<path d="M8 6h13M8 12h13M8 18h13M3.5 6h.01M3.5 12h.01M3.5 18h.01"/>',
        'spark'      => '<path d="M12 3v4M12 17v4M3 12h4M17 12h4M6 6l2.5 2.5M15.5 15.5 18 18M18 6l-2.5 2.5M8.5 15.5 6 18"/>',
        'calculator' => '<rect x="4" y="2" width="16" height="20" rx="2.5"/><path d="M8 6h8M8 11h.01M12 11h.01M16 11h.01M8 15h.01M12 15h.01M16 15h.01M8 19h4"/>',
        'calendar'   => '<rect x="3" y="5" width="18" height="16" rx="2.5"/><path d="M3 10h18M8 3v4M16 3v4"/>',
        'box'        => '<path d="m12 3 8 4.5v9L12 21l-8-4.5v-9z"/><path d="m4 7.5 8 4.5 8-4.5M12 12v9"/>',
        'moon'       => '<path d="M20 14.5A8.5 8.5 0 0 1 9.5 4 8.5 8.5 0 1 0 20 14.5z"/>',
        'sun'        => '<circle cx="12" cy="12" r="4"/><path d="M12 2v2M12 20v2M2 12h2M20 12h2M4.9 4.9l1.4 1.4M17.7 17.7l1.4 1.4M19.1 4.9l-1.4 1.4M6.3 17.7l-1.4 1.4"/>',
        'check'      => '<circle cx="12" cy="12" r="9"/><path d="m8.5 12 2.5 2.5 4.5-5"/>',
        'external'   => '<path d="M14 4h6v6"/><path d="M20 4 10 14"/><path d="M19 14v5a1.6 1.6 0 0 1-1.6 1.6H5.6A1.6 1.6 0 0 1 4 19V6.6A1.6 1.6 0 0 1 5.6 5H10"/>',
        'arrow'      => '<path d="M5 12h14"/><path d="m13 6 6 6-6 6"/>',
        'code'       => '<path d="m9 18-6-6 6-6"/><path d="m15 6 6 6-6 6"/>',
        'mail'       => '<rect x="3" y="5" width="18" height="14" rx="2.5"/><path d="m3.5 7 8.5 6 8.5-6"/>',
        'eye'        => '<path d="M2 12s3.8-6.5 10-6.5S22 12 22 12s-3.8 6.5-10 6.5S2 12 2 12z"/><circle cx="12" cy="12" r="2.8"/>',
        'menu'       => '<path d="M4 7h16M4 12h16M4 17h16"/>',
        'close'      => '<path d="M6 6 18 18M18 6 6 18"/>',
        'logout'     => '<path d="M9 21H6a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h3"/><path d="M16 17l5-5-5-5M21 12H9"/>',
        'lock'       => '<rect x="4" y="10" width="16" height="11" rx="2.5"/><path d="M8 10V7a4 4 0 0 1 8 0v3"/>',
        'inbox'      => '<path d="M3 13h5l1.5 3h5L16 13h5"/><path d="M5.4 4h13.2l2.4 9v5a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-5z"/>',
        'plus'       => '<path d="M12 5v14M5 12h14"/>',
        'edit'       => '<path d="M12 20h9"/><path d="M16.5 3.5a2.1 2.1 0 0 1 3 3L7 19l-4 1 1-4z"/>',
        'trash'      => '<path d="M4 7h16M10 11v6M14 11v6"/><path d="M6 7l1 13a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2l1-13M9 7V4h6v3"/>',
    ];

    $body = $paths[$name] ?? $paths['spark'];

    return '<svg class="' . e($class) . '" viewBox="0 0 24 24" fill="none" stroke="currentColor"'
         . ' stroke-width="1.6" stroke-linecap="round" stroke-linejoin="round" aria-hidden="true"'
         . ' focusable="false">' . $body . '</svg>';
}

/** Sinh và lưu token chống giả mạo yêu cầu (CSRF). */
function csrf_token(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Trường ẩn chứa token CSRF, chèn vào mọi biểu mẫu POST. */
function csrf_field(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(csrf_token()) . '">';
}

/** Đối chiếu token gửi lên với token trong phiên. */
function csrf_check(): bool
{
    $sent = $_POST['csrf_token'] ?? '';
    return is_string($sent)
        && !empty($_SESSION['csrf_token'])
        && hash_equals($_SESSION['csrf_token'], $sent);
}

/** Ghi một thông báo chớp nhoáng hiển thị ở lần tải trang kế tiếp. */
function flash(string $type, string $message): void
{
    $_SESSION['flash'][] = ['type' => $type, 'message' => $message];
}

/** Lấy ra và xóa toàn bộ thông báo chớp nhoáng. */
function flash_pull(): array
{
    $items = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $items;
}

/** Chuyển hướng và dừng thực thi. */
function redirect(string $path): never
{
    header('Location: ' . (str_starts_with($path, 'http') ? $path : url($path)));
    exit;
}

/** Ghi nhật ký lượt xem phục vụ biểu đồ Dashboard. */
function log_visit(?int $projectId = null): void
{
    try {
        Database::run(
            'INSERT INTO visit_logs (project_id, path, referer, ip) VALUES (?, ?, ?, ?)',
            [
                $projectId,
                mb_substr((string) ($_SERVER['REQUEST_URI'] ?? '/'), 0, 255),
                mb_substr((string) ($_SERVER['HTTP_REFERER'] ?? ''), 0, 255),
                mb_substr((string) ($_SERVER['REMOTE_ADDR'] ?? ''), 0, 45),
            ]
        );
    } catch (Throwable $e) {
        // Nhật ký truy cập không phải chức năng cốt lõi:
        // nếu ghi thất bại thì bỏ qua, không làm gián đoạn trang.
    }
}
