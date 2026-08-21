<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - CONTACT & VIP CONCIERGE
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/includes/config.php';

$success_msg = '';
$error_msg = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_contact'])) {
    $name = sanitize($_POST['name'] ?? '');
    $email = sanitize($_POST['email'] ?? '');
    $phone = sanitize($_POST['phone'] ?? '');
    $subject = sanitize($_POST['subject'] ?? '');
    $message = sanitize($_POST['message'] ?? '');

    if ($name && $email && $subject && $message) {
        $stmt = $pdo->prepare("
            INSERT INTO contacts (name, email, phone, subject, message, status) 
            VALUES (?, ?, ?, ?, ?, 'unread')
        ");
        $stmt->execute([$name, $email, $phone, $subject, $message]);
        set_flash('success', 'Cảm ơn quý khách! Yêu cầu của quý khách đã được chuyển tới Ban Giám Đốc HieuMini.');
        header("Location: " . BASE_URL . "/contact.php");
        exit;
    } else {
        $error_msg = 'Vui lòng điền đầy đủ các thông tin bắt buộc (*).';
    }
}

$page_title = "Liên Hệ & Đặt Lịch Tư Vấn VIP | " . SITE_NAME;
$page_desc = "Kênh liên hệ trực tiếp cùng Ban Giám Đốc và Bộ Phận Dịch Vụ Khách Hàng VIP HieuMini Luxury Fitness Club.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <!-- Page Title -->
    <div style="text-align: center; max-width: 800px; margin: 0 auto 4rem;" class="reveal">
        <span class="section-tag">DỊCH VỤ KHÁCH HÀNG VIP</span>
        <h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 1.25rem;">
            LIÊN HỆ & TƯ VẤN TRỰC TIẾP
        </h1>
        <p style="color: var(--text-secondary); font-size: 1.15rem; line-height: 1.8;">
            Chúng tôi luôn sẵn sàng lắng nghe và thiết kế những giải pháp thể lực & dinh dưỡng tối ưu nhất cho quý doanh nhân.
        </p>
    </div>

    <div class="catalog-layout" style="grid-template-columns: 1fr 1.2fr; gap: 3rem;">
        <!-- Left: Contact Details -->
        <div class="reveal">
            <div class="form-card" style="margin-bottom: 2rem;">
                <h3 style="font-size: 1.35rem; color: var(--gold-light); margin-bottom: 1.5rem;">
                    <i class="fas fa-building"></i> HỆ THỐNG TRUNG TÂM THỂ HÌNH
                </h3>

                <div style="display: flex; flex-direction: column; gap: 1.5rem; font-size: 0.95rem;">
                    <div style="display: flex; gap: 1rem;">
                        <div style="width: 44px; height: 44px; background: rgba(245,158,11,0.1); border: 1px solid var(--border-gold); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--gold-light); font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <strong style="color: #fff; display: block; font-size: 1.05rem;">Trụ Sở Chính - HieuMini Diamond</strong>
                            <p style="color: var(--text-secondary); line-height: 1.6;">Tòa nhà HieuMini Tower, 88 Nguyễn Huệ, Phường Bến Nghé, Quận 1, TP. Hồ Chí Minh</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <div style="width: 44px; height: 44px; background: rgba(6,182,212,0.1); border: 1px solid var(--border-gold); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--cyan-accent); font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div>
                            <strong style="color: #fff; display: block; font-size: 1.05rem;">Chi Nhánh 2 - HieuMini Landmark</strong>
                            <p style="color: var(--text-secondary); line-height: 1.6;">Tầng 5, Tòa Nhà Landmark Plus, 208 Nguyễn Hữu Cảnh, Bình Thạnh, TP. Hồ Chí Minh</p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <div style="width: 44px; height: 44px; background: rgba(16,185,129,0.1); border: 1px solid var(--border-gold); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--emerald-accent); font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fas fa-phone-alt"></i>
                        </div>
                        <div>
                            <strong style="color: #fff; display: block; font-size: 1.05rem;">Hotline Hỗ Trợ VIP 24/7</strong>
                            <p style="color: var(--gold-light); font-weight: 700; font-size: 1.1rem;"><?= SITE_HOTLINE ?></p>
                        </div>
                    </div>

                    <div style="display: flex; gap: 1rem;">
                        <div style="width: 44px; height: 44px; background: rgba(168,85,247,0.1); border: 1px solid var(--border-gold); border-radius: 8px; display: flex; align-items: center; justify-content: center; color: var(--purple-accent); font-size: 1.2rem; flex-shrink: 0;">
                            <i class="fas fa-envelope"></i>
                        </div>
                        <div>
                            <strong style="color: #fff; display: block; font-size: 1.05rem;">Thư Điện Tử Ban Lãnh Đạo</strong>
                            <p style="color: var(--text-secondary);"><?= SITE_EMAIL ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Right: Inquiry Form -->
        <div class="reveal delay-1">
            <div class="form-card">
                <h3 style="font-size: 1.35rem; color: #fff; margin-bottom: 1.5rem; padding-bottom: 0.75rem; border-bottom: 1px solid var(--border-subtle);">
                    <i class="fas fa-paper-plane" style="color: var(--gold-primary);"></i> GỬI YÊU CẦU TƯ VẤN TRỰC TIẾP
                </h3>

                <?php if ($error_msg): ?>
                    <div style="background: rgba(239,68,68,0.15); border: 1px solid var(--ruby-accent); border-radius: 4px; padding: 1rem; margin-bottom: 1.5rem; color: #fca5a5;">
                        <?= htmlspecialchars($error_msg) ?>
                    </div>
                <?php endif; ?>

                <form action="<?= BASE_URL ?>/contact.php" method="POST">
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Họ và Tên (*)</label>
                            <input type="text" name="name" class="form-control" required placeholder="Họ tên của bạn...">
                        </div>
                        <div class="form-group">
                            <label>Số Điện Thoại (*)</label>
                            <input type="tel" name="phone" class="form-control" required placeholder="0988 888 xxx">
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Email Liên Hệ (*)</label>
                        <input type="email" name="email" class="form-control" required placeholder="ceo@company.com">
                    </div>

                    <div class="form-group">
                        <label>Tiêu Đề / Dịch Vụ Quan Tâm (*)</label>
                        <input type="text" name="subject" class="form-control" required placeholder="Ví dụ: Tư vấn gói hội viên Doanh nghiệp Corporate Club...">
                    </div>

                    <div class="form-group">
                        <label>Nội Dung Yêu Cầu Chi Tiết (*)</label>
                        <textarea name="message" class="form-control" rows="4" required placeholder="Quý khách vui lòng để lại yêu cầu cụ thể..."></textarea>
                    </div>

                    <button type="submit" name="send_contact" class="btn btn-primary btn-block btn-lg btn-shimmer" style="margin-top: 1rem;">
                        <i class="fas fa-paper-plane"></i> GỬI THÔNG ĐIỆP ĐẾN HIEUMINI
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
