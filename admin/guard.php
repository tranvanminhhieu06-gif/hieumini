<?php
/**
 * HieuMini — CỔNG BẢO VỆ PHÂN HỆ QUẢN TRỊ
 * =====================================================================
 * Nguyên tắc: trang quản trị MẶC ĐỊNH TẮT HOÀN TOÀN.
 *
 * Hệ thống chỉ mở phân hệ này khi máy chủ được khởi chạy kèm biến
 * môi trường ADMIN_PASSWORD có giá trị khác rỗng. Khi biến này không
 * tồn tại, mọi tệp trong thư mục admin/ đều trả về 404 — giống hệt
 * như thư mục không hề tồn tại trên máy chủ. Cách này an toàn hơn
 * việc trả về 403, vì kẻ tấn công không biết được có trang quản trị
 * hay không (nguyên tắc "security through correct defaults").
 *
 * Cách bật: xem README.md, mục "Bật trang quản trị".
 * =====================================================================
 */

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';

/* --- Bước 1: Không có ADMIN_PASSWORD → coi như không có trang này --- */
if (!ADMIN_ENABLED) {
    http_response_code(404);
    header('X-Robots-Tag: noindex, nofollow');
    echo '<!doctype html><html lang="vi"><head><meta charset="utf-8">'
       . '<meta name="viewport" content="width=device-width,initial-scale=1">'
       . '<title>404 — Không tìm thấy trang</title>'
       . '<style>body{font-family:system-ui,Segoe UI,sans-serif;background:#F7F8FC;color:#0B1020;'
       . 'display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0;padding:24px;'
       . 'text-align:center}h1{font-size:76px;margin:0;letter-spacing:-.04em}'
       . 'p{color:#55607A;max-width:44ch}a{color:#4F46E5}</style></head><body><div>'
       . '<h1>404</h1><p>Không tìm thấy trang bạn yêu cầu trên máy chủ này.</p>'
       . '<p><a href="' . e(url('index.php')) . '">Quay về trang chủ</a></p>'
       . '</div></body></html>';
    exit;
}

/* --- Bước 2: Trang đăng nhập tự xử lý phần xác thực --- */
$currentFile = basename($_SERVER['SCRIPT_NAME'] ?? '');
if ($currentFile === 'login.php') {
    return;
}

/* --- Bước 3: Các trang còn lại bắt buộc phải có phiên đăng nhập --- */
if (empty($_SESSION['admin_ok'])) {
    redirect('admin/login.php');
}

/* --- Bước 4: Tự đăng xuất sau 60 phút không thao tác --- */
$idleLimit = 60 * 60;
if (isset($_SESSION['admin_seen']) && (time() - (int) $_SESSION['admin_seen']) > $idleLimit) {
    unset($_SESSION['admin_ok'], $_SESSION['admin_seen']);
    flash('error', 'Phiên quản trị đã hết hạn do không thao tác. Vui lòng đăng nhập lại.');
    redirect('admin/login.php');
}
$_SESSION['admin_seen'] = time();
