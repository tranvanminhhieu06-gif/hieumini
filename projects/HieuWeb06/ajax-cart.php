<?php
/**
 * HieuMini - Điểm cuối AJAX cho giỏ hàng và danh sách yêu thích.
 * Luôn trả về JSON.
 */
require_once __DIR__ . '/includes/config.php';

header('Content-Type: application/json; charset=utf-8');

/** Trả JSON rồi kết thúc. */
function json_out(array $data): never
{
    echo json_encode($data, JSON_UNESCAPED_UNICODE);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    json_out(['ok' => false, 'message' => 'Phương thức không được hỗ trợ.']);
}

if (!csrf_verify($_POST['csrf_token'] ?? null)) {
    http_response_code(419);
    json_out(['ok' => false, 'message' => 'Phiên làm việc hết hạn, vui lòng tải lại trang.']);
}

$action = input('action');
$id     = (int)($_POST['id'] ?? 0);

switch ($action) {

    case 'add':
        $stmt = $pdo->prepare('SELECT id, title FROM projects WHERE id = ? AND status = 1');
        $stmt->execute([$id]);
        $project = $stmt->fetch();
        if (!$project) {
            json_out(['ok' => false, 'message' => 'Dự án không tồn tại hoặc đã ngừng bán.']);
        }
        $license = in_array($_POST['license'] ?? '', ['personal', 'commercial', 'extended'], true)
            ? $_POST['license'] : 'personal';
        cart_add($id, $license);
        json_out([
            'ok'      => true,
            'count'   => cart_count(),
            'message' => 'Đã thêm “' . $project['title'] . '” vào giỏ hàng.',
        ]);

    case 'remove':
        cart_remove($id);
        json_out(['ok' => true, 'count' => cart_count(), 'message' => 'Đã xoá khỏi giỏ hàng.']);

    case 'wish':
        if (!is_logged_in()) {
            json_out(['ok' => false, 'needLogin' => true, 'message' => 'Vui lòng đăng nhập.']);
        }
        $uid = current_user_id();
        if (in_wishlist($pdo, $uid, $id)) {
            $pdo->prepare('DELETE FROM wishlists WHERE user_id = ? AND project_id = ?')->execute([$uid, $id]);
            json_out(['ok' => true, 'active' => false, 'message' => 'Đã bỏ khỏi danh sách yêu thích.']);
        }
        $chk = $pdo->prepare('SELECT 1 FROM projects WHERE id = ? AND status = 1');
        $chk->execute([$id]);
        if (!$chk->fetchColumn()) {
            json_out(['ok' => false, 'message' => 'Dự án không tồn tại.']);
        }
        $pdo->prepare('INSERT INTO wishlists (user_id, project_id) VALUES (?,?)')->execute([$uid, $id]);
        json_out(['ok' => true, 'active' => true, 'message' => 'Đã lưu vào danh sách yêu thích.']);

    default:
        http_response_code(400);
        json_out(['ok' => false, 'message' => 'Hành động không hợp lệ.']);
}
