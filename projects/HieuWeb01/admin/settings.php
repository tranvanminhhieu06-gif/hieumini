<?php
/**
 * Cài Đặt Hệ Thống & Cửa Hàng (Settings) - HieuMini Fashion
 */
$adminTitle = "Cài Đặt Cửa Hàng & Hệ Thống";
require_once __DIR__ . '/includes/header.php';

// File lưu trữ cấu hình JSON nếu cần persist hoặc lấy mặc định
$settingsFile = __DIR__ . '/../../config/shop_settings.json';
$defaultSettings = [
    'shop_name' => 'HieuMini Luxury Fashion Studio',
    'hotline' => '0988.889.999',
    'support_email' => 'support@hieumini.vn',
    'address' => 'Số 18 Duy Tân, Dịch Vọng Hậu, Cầu Giấy, Hà Nội',
    'open_hours' => '8h30 - 22h00 hàng ngày',
    'shipping_fee' => 30000,
    'freeship_threshold' => 300000,
    'bank_name' => 'MBBank (Ngân Hàng Quân Đội)',
    'bank_account' => '0988889999',
    'bank_owner' => 'TRAN VAN MINH HIEU',
    'bank_code' => 'MB',
    'topbar_announcement' => 'ƯU ĐÃI ĐẶC BIỆT: Miễn phí vận chuyển toàn quốc cho đơn hàng từ 300.000đ',
    'default_coupon' => 'HIEU10'
];

$settings = $defaultSettings;
if (file_exists($settingsFile)) {
    $loaded = json_decode(file_get_contents($settingsFile), true);
    if (is_array($loaded)) {
        $settings = array_merge($defaultSettings, $loaded);
    }
}

$message = '';
$messageType = 'success';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $settings['shop_name'] = trim($_POST['shop_name'] ?? $defaultSettings['shop_name']);
    $settings['hotline'] = trim($_POST['hotline'] ?? $defaultSettings['hotline']);
    $settings['support_email'] = trim($_POST['support_email'] ?? $defaultSettings['support_email']);
    $settings['address'] = trim($_POST['address'] ?? $defaultSettings['address']);
    $settings['open_hours'] = trim($_POST['open_hours'] ?? $defaultSettings['open_hours']);
    $settings['shipping_fee'] = (float)($_POST['shipping_fee'] ?? 30000);
    $settings['freeship_threshold'] = (float)($_POST['freeship_threshold'] ?? 300000);
    $settings['bank_name'] = trim($_POST['bank_name'] ?? $defaultSettings['bank_name']);
    $settings['bank_account'] = trim($_POST['bank_account'] ?? $defaultSettings['bank_account']);
    $settings['bank_owner'] = trim($_POST['bank_owner'] ?? $defaultSettings['bank_owner']);
    $settings['topbar_announcement'] = trim($_POST['topbar_announcement'] ?? $defaultSettings['topbar_announcement']);
    $settings['default_coupon'] = trim($_POST['default_coupon'] ?? $defaultSettings['default_coupon']);

    file_put_contents($settingsFile, json_encode($settings, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));
    set_flash('success', 'Lưu thông tin cài đặt cửa hàng thành công!');
    redirect('settings.php');
}
?>

<form action="settings.php" method="POST">
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: 24px;">
        
        <!-- Main Settings Column -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            
            <!-- Card 1: Thông tin thương hiệu & Liên hệ -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title"><i class="fa-solid fa-store text-accent"></i> Thông Tin Thương Hiệu & Cửa Hàng</h3>
                </div>
                <div class="admin-card-body">
                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label">Tên Cửa Hàng / Thương Hiệu</label>
                        <input type="text" name="shop_name" class="form-control" value="<?= htmlspecialchars($settings['shop_name']) ?>" required>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label class="form-label">Hotline Chăm Sóc</label>
                            <input type="text" name="hotline" class="form-control" value="<?= htmlspecialchars($settings['hotline']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Email Hỗ Trợ</label>
                            <input type="email" name="support_email" class="form-control" value="<?= htmlspecialchars($settings['support_email']) ?>" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 16px;">
                        <label class="form-label">Địa Chỉ Showroom Trụ Sở</label>
                        <input type="text" name="address" class="form-control" value="<?= htmlspecialchars($settings['address']) ?>" required>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Thời Gian Mở Cửa</label>
                        <input type="text" name="open_hours" class="form-control" value="<?= htmlspecialchars($settings['open_hours']) ?>">
                    </div>
                </div>
            </div>

            <!-- Card 2: Vận chuyển & Giao hàng -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title"><i class="fa-solid fa-truck-fast text-accent"></i> Chính Sách Vận Chuyển & Miễn Phí Ship</h3>
                </div>
                <div class="admin-card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label class="form-label">Phí Vận Chuyển Tiêu Chuẩn (VNĐ)</label>
                            <input type="number" name="shipping_fee" class="form-control" value="<?= $settings['shipping_fee'] ?>" required>
                            <span style="font-size: 0.75rem; color: var(--admin-text-muted);">Áp dụng cho các đơn hàng chưa đạt ngưỡng freeship</span>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Ngưỡng Miễn Phí Vận Chuyển (Freeship VNĐ)</label>
                            <input type="number" name="freeship_threshold" class="form-control" value="<?= $settings['freeship_threshold'] ?>" required>
                            <span style="font-size: 0.75rem; color: #10b981;">Đơn hàng từ mức này trở lên sẽ được miễn phí 100% cước</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="form-label">Thông Báo Chạy Chữ Topbar</label>
                        <input type="text" name="topbar_announcement" class="form-control" value="<?= htmlspecialchars($settings['topbar_announcement']) ?>">
                    </div>
                </div>
            </div>

            <!-- Card 3: Thanh toán VietQR & Ngân hàng -->
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title"><i class="fa-solid fa-qrcode text-accent"></i> Cấu Hình Thanh Toán VietQR Tự Động</h3>
                </div>
                <div class="admin-card-body">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 16px;">
                        <div class="form-group">
                            <label class="form-label">Ngân Hàng Nhận Tiền</label>
                            <input type="text" name="bank_name" class="form-control" value="<?= htmlspecialchars($settings['bank_name']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Số Tài Khoản</label>
                            <input type="text" name="bank_account" class="form-control" value="<?= htmlspecialchars($settings['bank_account']) ?>" required>
                        </div>
                    </div>

                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
                        <div class="form-group">
                            <label class="form-label">Tên Chủ Tài Khoản (In hoa không dấu)</label>
                            <input type="text" name="bank_owner" class="form-control" value="<?= htmlspecialchars($settings['bank_owner']) ?>" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label">Mã Giảm Giá Mặc Định (Voucher)</label>
                            <input type="text" name="default_coupon" class="form-control" value="<?= htmlspecialchars($settings['default_coupon']) ?>">
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Sidebar Actions Column -->
        <div style="display: flex; flex-direction: column; gap: 24px;">
            <div class="admin-card">
                <div class="admin-card-header">
                    <h3 class="admin-card-title"><i class="fa-solid fa-floppy-disk text-accent"></i> Thao Tác</h3>
                </div>
                <div class="admin-card-body">
                    <p style="font-size: 0.8rem; color: var(--admin-text-muted); line-height: 1.5; margin-bottom: 16px;">
                        Các thay đổi cài đặt sẽ áp dụng ngay lập tức cho toàn bộ giao diện khách hàng và quy trình thanh toán.
                    </p>

                    <button type="submit" class="btn btn-accent btn-block" style="padding: 12px; font-weight: 700;">
                        <i class="fa-solid fa-check mr-1"></i> Lưu Cài Đặt Hệ Thống
                    </button>
                    
                    <a href="index.php" class="btn btn-outline btn-block" style="margin-top: 10px;">
                        Hủy Bỏ
                    </a>
                </div>
            </div>

            <!-- VietQR Preview Box -->
            <div class="admin-card" style="text-align: center; padding: 20px;">
                <h4 style="font-size: 0.9rem; font-weight: 700; margin-bottom: 8px;">Mô Phỏng VietQR MBBank</h4>
                <div style="background: #ffffff; padding: 12px; border-radius: 12px; border: 1px solid var(--admin-border); display: inline-block; margin-bottom: 12px;">
                    <img src="https://api.qrserver.com/v1/create-qr-code/?size=160x160&data=<?= urlencode('2|99|0988889999|TRAN VAN MINH HIEU||0|0|HIEUMINI') ?>" alt="VietQR" style="width: 140px; height: 140px; display: block;">
                </div>
                <div style="font-size: 0.8rem; font-weight: 700; color: var(--admin-text);"><?= htmlspecialchars($settings['bank_owner']) ?></div>
                <div style="font-size: 0.75rem; color: var(--admin-text-muted);"><?= htmlspecialchars($settings['bank_account']) ?> • MBBank</div>
            </div>
        </div>

    </div>
</form>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
