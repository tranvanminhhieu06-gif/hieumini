<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - SERVICES & VIP MEMBERSHIPS
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/includes/config.php';

$page_title = "Dịch Vụ & Gói Hội Viên Thượng Lưu | " . SITE_NAME;
$page_desc = "Khám phá các gói hội viên VIP, dịch vụ huấn luyện viên cá nhân 1:1 và trị liệu thể thao phục hồi chuẩn CEO.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="text-align: center; max-width: 800px; margin: 0 auto 4rem;" class="reveal">
        <span class="section-tag">DỊCH VỤ ĐẲNG CẤP CEO</span>
        <h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 1.25rem;">
            ĐẶC QUYỀN HỘI VIÊN & DỊCH VỤ THƯỢNG LƯU
        </h1>
        <p style="color: var(--text-secondary); font-size: 1.15rem; line-height: 1.8;">
            Giải pháp chăm sóc toàn diện từ thể lực, vóc dáng đến sự cân bằng tâm trí dành cho giới tinh hoa.
        </p>
    </div>

    <!-- Service Lines Detailed -->
    <div style="display: flex; flex-direction: column; gap: 3rem; margin-bottom: 5rem;">
        <!-- 1. VIP Membership -->
        <div class="bmi-widget-card reveal" style="grid-template-columns: 1fr 1fr;">
            <div>
                <span class="badge badge-gold" style="margin-bottom: 0.75rem;">DỊCH VỤ TRỌNG TÂM</span>
                <h2 style="font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 1rem;">1. Gói Hội Viên CEO Diamond Elite</h2>
                <p style="color: #cbd5e1; line-height: 1.7; margin-bottom: 1.5rem;">
                    Thẻ tập quyền lực nhất tại HieuMini Luxury Fitness. Hội viên sở hữu thẻ được tiếp cận không giới hạn phòng tập chuẩn 5 sao, hồ bơi vô cực, xông hơi đá muối Himalaya, phòng tắm VIP riêng biệt và miễn phí 12 buổi huấn luyện Master Trainer 1:1.
                </p>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.6rem; margin-bottom: 1.5rem; font-size: 0.95rem;">
                    <li><i class="fas fa-check-circle" style="color: var(--gold-primary);"></i> Quyền sử dụng VIP Lounge tiếp khách đối tác</li>
                    <li><i class="fas fa-check-circle" style="color: var(--gold-primary);"></i> Dịch vụ giặt ủi và bảo quản trang phục thể thao riêng</li>
                    <li><i class="fas fa-check-circle" style="color: var(--gold-primary);"></i> Ưu đãi 20% khi mua dinh dưỡng & thiết bị tại Store</li>
                </ul>
                <button type="button" class="btn btn-primary btn-shimmer" data-open-modal="booking-modal">
                    <i class="fas fa-crown"></i> ĐĂNG KÝ GÓI DIAMOND
                </button>
            </div>
            <div style="background: #11141c; border-radius: var(--radius-md); padding: 2rem; border: 1px solid var(--border-subtle); text-align: center;">
                <img src="<?= BASE_URL ?>/assets/images/products/01_membership_diamond.jpg" alt="CEO Diamond" style="max-height: 280px; margin: 0 auto 1.5rem; border-radius: 8px;">
                <div style="font-size: 1.8rem; font-weight: 900; color: var(--gold-light);">24.500.000 ₫ <span style="font-size: 0.9rem; color: var(--text-secondary);">/ Năm</span></div>
            </div>
        </div>

        <!-- 2. Master Trainer 1:1 -->
        <div class="bmi-widget-card reveal" style="grid-template-columns: 1fr 1fr;">
            <div style="order: 2;">
                <span class="badge badge-cyan" style="margin-bottom: 0.75rem;">HUẤN LUYỆN VIÊN CÁ NHÂN</span>
                <h2 style="font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 1rem;">2. Huấn Luyện Viên 1:1 VIP Master Trainer</h2>
                <p style="color: #cbd5e1; line-height: 1.7; margin-bottom: 1.5rem;">
                    Chương trình huấn luyện cá nhân chuyên sâu 30 buổi. Huấn luyện viên đạt chuẩn NASM/ISSA quốc tế sẽ đồng hành trực tiếp, điều chỉnh từng góc độ chuyển động khớp, tối đa hóa hiệu quả đốt mỡ và tăng cơ nạc trong thời gian ngắn nhất.
                </p>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.6rem; margin-bottom: 1.5rem; font-size: 0.95rem;">
                    <li><i class="fas fa-check-circle" style="color: var(--cyan-accent);"></i> Lập thực đơn dinh dưỡng chi tiết theo lịch sinh hoạt</li>
                    <li><i class="fas fa-check-circle" style="color: var(--cyan-accent);"></i> Đo kiểm tra InBody 770 y khoa mỗi tuần</li>
                    <li><i class="fas fa-check-circle" style="color: var(--cyan-accent);"></i> Cam kết đạt mục tiêu hình thể sau lộ trình 90 ngày</li>
                </ul>
                <button type="button" class="btn btn-primary btn-shimmer" data-open-modal="booking-modal">
                    <i class="fas fa-dumbbell"></i> ĐẶT LỊCH PT 1:1 NGAY
                </button>
            </div>
            <div style="order: 1; background: #11141c; border-radius: var(--radius-md); padding: 2rem; border: 1px solid var(--border-subtle); text-align: center;">
                <img src="<?= BASE_URL ?>/assets/images/products/27_master_trainer.jpg" alt="Master Trainer" style="max-height: 280px; margin: 0 auto 1.5rem; border-radius: 8px;">
                <div style="font-size: 1.8rem; font-weight: 900; color: var(--gold-light);">18.500.000 ₫ <span style="font-size: 0.9rem; color: var(--text-secondary);">/ 30 Buổi</span></div>
            </div>
        </div>

        <!-- 3. Sports Therapy & Recovery -->
        <div class="bmi-widget-card reveal" style="grid-template-columns: 1fr 1fr;">
            <div>
                <span class="badge badge-emerald" style="margin-bottom: 0.75rem;">TRỊ LIỆU & PHỤC HỒI</span>
                <h2 style="font-size: 2rem; font-weight: 800; color: #fff; margin-bottom: 1rem;">3. Liệu Trình Trị Liệu & Giãn Cơ Phục Hồi</h2>
                <p style="color: #cbd5e1; line-height: 1.7; margin-bottom: 1.5rem;">
                    Phương pháp Myofascial Release kết hợp súng massage Theragun PRO và bốt nén khí y khoa. Giải phóng áp lực đè nặng lên các đốt sống cổ, lưng và khớp vai do ngồi làm việc nhiều giờ liền.
                </p>
                <ul style="list-style: none; display: flex; flex-direction: column; gap: 0.6rem; margin-bottom: 1.5rem; font-size: 0.95rem;">
                    <li><i class="fas fa-check-circle" style="color: var(--emerald-accent);"></i> Xóa tan đau nhức thắt lưng, căng cứng bả vai</li>
                    <li><i class="fas fa-check-circle" style="color: var(--emerald-accent);"></i> Tăng tuần hoàn máu và độ dẻo dai của cơ bắp</li>
                    <li><i class="fas fa-check-circle" style="color: var(--emerald-accent);"></i> Thực hiện bởi Cử nhân Vật lý trị liệu thể thao</li>
                </ul>
                <button type="button" class="btn btn-gold-outline" data-open-modal="booking-modal">
                    <i class="fas fa-hand-holding-heart"></i> ĐĂNG KÝ TRỊ LIỆU VIP
                </button>
            </div>
            <div style="background: #11141c; border-radius: var(--radius-md); padding: 1.5rem; border: 1px solid var(--border-subtle); text-align: center; overflow: hidden;">
                <img src="<?= BASE_URL ?>/assets/images/sauna-spa.jpg" alt="Sauna Đá Muối & Phục Hồi Jacuzzi" style="width: 100%; max-height: 250px; object-fit: cover; margin: 0 auto 1.25rem; border-radius: 8px;">
                <div style="font-size: 1.8rem; font-weight: 900; color: var(--gold-light);">6.900.000 ₫ <span style="font-size: 0.9rem; color: var(--text-secondary);">/ 10 Buổi</span></div>
            </div>
        </div>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
