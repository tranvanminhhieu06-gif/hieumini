<?php
/**
 * Quản Lý Mã Giảm Giá - Admin HieuMini
 */
require_once __DIR__ . '/../../config/db.php';
require_once __DIR__ . '/../../includes/functions.php';

// Thêm mã giảm giá
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'add_coupon') {
    $code = strtoupper(trim($_POST['code'] ?? ''));
    $type = $_POST['discount_type'] ?? 'percentage';
    $value = (float)($_POST['discount_value'] ?? 0);
    $minOrder = (float)($_POST['min_order_amount'] ?? 0);
    $expiry = !empty($_POST['expiry_date']) ? $_POST['expiry_date'] : null;

    if (!empty($code) && $value > 0) {
        $stmt = $pdo->prepare("INSERT INTO coupons (code, discount_type, discount_value, min_order_amount, expiry_date, status) VALUES (?, ?, ?, ?, ?, 1)");
        $stmt->execute([$code, $type, $value, $minOrder, $expiry]);
        set_flash('success', 'Đã thêm mã ưu đãi "' . $code . '" thành công!');
        redirect('coupons.php');
    } else {
        set_flash('danger', 'Vui lòng nhập đầy đủ mã và giá trị giảm.');
    }
}

// Xóa mã
if (isset($_GET['delete'])) {
    $delId = (int)$_GET['delete'];
    $pdo->prepare("DELETE FROM coupons WHERE id = ?")->execute([$delId]);
    set_flash('success', 'Đã xóa mã giảm giá!');
    redirect('coupons.php');
}

$coupons = $pdo->query("SELECT * FROM coupons ORDER BY id DESC")->fetchAll();

$adminTitle = "Quản Lý Mã Khuyến Mãi";
require_once __DIR__ . '/includes/header.php';
?>

<div style="display: grid; grid-template-columns: 1fr 1.6fr; gap: 30px;">
    <!-- Form Thêm Mã -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-solid fa-ticket text-accent"></i> Tạo Mã Khuyến Mãi Mới</h3>
        </div>
        <div class="admin-card-body">
            <form action="coupons.php" method="POST">
                <input type="hidden" name="action" value="add_coupon">

                <div class="admin-form-group">
                    <label class="admin-form-label">Mã code ưu đãi <span class="text-danger">*</span></label>
                    <input type="text" name="code" class="form-control" placeholder="VD: SUMMER2026" style="text-transform: uppercase;" required>
                </div>

                <div class="form-row">
                    <div class="admin-form-group">
                        <label class="admin-form-label">Loại giảm giá</label>
                        <select name="discount_type" class="form-control">
                            <option value="percentage">Phần trăm (%)</option>
                            <option value="fixed">Số tiền cố định (VNĐ)</option>
                        </select>
                    </div>
                    <div class="admin-form-group">
                        <label class="admin-form-label">Giá trị giảm <span class="text-danger">*</span></label>
                        <input type="number" name="discount_value" class="form-control" placeholder="VD: 10 hoặc 50000" required>
                    </div>
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">Đơn hàng tối thiểu (VNĐ)</label>
                    <input type="number" name="min_order_amount" class="form-control" placeholder="200000" value="0">
                </div>

                <div class="admin-form-group">
                    <label class="admin-form-label">Hạn sử dụng</label>
                    <input type="date" name="expiry_date" class="form-control" value="2026-12-31">
                </div>

                <button type="submit" class="btn btn-accent btn-block">
                    <i class="fa-solid fa-plus"></i> Tạo Mã Giảm Giá
                </button>
            </form>
        </div>
    </div>

    <!-- Bảng Danh Sách Mã -->
    <div class="admin-card">
        <div class="admin-card-header">
            <h3 class="admin-card-title"><i class="fa-solid fa-tags text-accent"></i> Danh Sách Mã Ưu Đãi (<?= count($coupons) ?>)</h3>
        </div>
        <div class="admin-card-body" style="padding: 0;">
            <table class="admin-table">
                <thead>
                    <tr>
                        <th>Mã</th>
                        <th>Loại / Giá Trị</th>
                        <th>Đơn Tối Thiểu</th>
                        <th>Đã Dùng</th>
                        <th>Hết Hạn</th>
                        <th style="text-align: center;">Xóa</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($coupons as $cp): ?>
                        <tr>
                            <td><strong style="color: var(--accent);"><?= htmlspecialchars($cp['code']) ?></strong></td>
                            <td>
                                <?= $cp['discount_type'] === 'percentage' ? $cp['discount_value'] . '%' : format_price($cp['discount_value']) ?>
                            </td>
                            <td><?= format_price($cp['min_order_amount']) ?></td>
                            <td><span class="badge badge-secondary"><?= $cp['used_count'] ?> lượt</span></td>
                            <td><?= $cp['expiry_date'] ? date('d/m/Y', strtotime($cp['expiry_date'])) : 'Vô thời hạn' ?></td>
                            <td style="text-align: center;">
                                <a href="coupons.php?delete=<?= $cp['id'] ?>" class="btn btn-outline btn-sm btn-delete-confirm" style="color: #ef4444;" title="Xóa">
                                    <i class="fa-solid fa-trash-can"></i>
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
