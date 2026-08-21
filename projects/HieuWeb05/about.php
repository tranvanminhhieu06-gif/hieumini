<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - ABOUT US
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/includes/config.php';

$page_title = "Về HieuMini Luxury Fitness Club | Đẳng Cấp Thể Hình Thượng Lưu";
$page_desc = "Tìm hiểu câu chuyện thành lập, tầm nhìn của CEO và hệ thống tiêu chuẩn 5 sao tại HieuMini Luxury Fitness Club.";

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <!-- Header -->
    <div style="text-align: center; max-width: 800px; margin: 0 auto 4rem;" class="reveal">
        <span class="section-tag">CÂU CHUYỆN THƯƠNG HIỆU</span>
        <h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 1.25rem;">
            HIEUMINI <span class="text-gold-gradient">LUXURY FITNESS CLUB</span>
        </h1>
        <p style="color: var(--text-secondary); font-size: 1.15rem; line-height: 1.8;">
            Được kiến tạo từ khát vọng xây dựng một không gian thể chất đỉnh cao, nơi hội tụ của sự kỷ luật, sức mạnh và phong cách sống thượng lưu của giới lãnh đạo Việt Nam.
        </p>
    </div>

    <!-- CEO Vision Section -->
    <div class="bmi-widget-card reveal" style="margin-bottom: 5rem; grid-template-columns: 1fr 1.2fr; align-items: center;">
        <div style="position: relative; text-align: center;">
            <div style="width: 220px; height: 220px; margin: 0 auto 1.5rem; border-radius: 50%; border: 3px solid var(--gold-primary); padding: 6px; box-shadow: var(--shadow-gold);">
                <img src="<?= BASE_URL ?>/assets/images/ceo_avatar.jpg" alt="CEO HieuMini" style="width: 100%; height: 100%; border-radius: 50%; object-fit: cover;">
            </div>
            <h3 style="font-size: 1.4rem; color: #fff;">FOUNDER & CEO HIEUMINI</h3>
            <span style="color: var(--gold-primary); font-size: 0.9rem; font-weight: 700; text-transform: uppercase;">Chủ Tịch Sáng Lập</span>
        </div>

        <div>
            <span class="badge badge-gold" style="margin-bottom: 1rem;">THÔNG ĐIỆP TỪ LÃNH ĐẠO</span>
            <h2 style="font-size: 1.8rem; font-weight: 800; color: #fff; margin-bottom: 1rem; line-height: 1.3;">
                "SỨC KHỎE LÀ NỀN TẢNG TỐI THƯỢNG CỦA MỌI ĐẾ CHẾ THÀNH CÔNG"
            </h2>
            <p style="color: #cbd5e1; font-size: 1rem; line-height: 1.8; margin-bottom: 1.5rem;">
                Tại HieuMini, chúng tôi không đơn thuần xây dựng một phòng tập gym thông thường. Chúng tôi kiến tạo một <strong>đặc khu thể lực & tái tạo năng lượng</strong> dành riêng cho các nhà lãnh đạo bận rộn. Mọi chi tiết từ hệ thống máy cơ học tải trọng 1.000kg, phòng xông hơi đá muối Himalaya, đến nguồn dinh dưỡng Whey Isolate tinh khiết đều được kiểm định khắt khe theo chuẩn quốc tế.
            </p>
            <div style="border-left: 3px solid var(--gold-primary); padding-left: 1rem; font-style: italic; color: var(--gold-light);">
                — "Một thể lực phi thường sẽ dẫn dắt một trí tuệ phi thường."
            </div>
        </div>
    </div>

    <!-- Core Values Grid -->
    <div style="margin-bottom: 5rem;">
        <div class="section-header reveal">
            <span class="section-tag">GIÁ TRỊ CỐT LÕI</span>
            <h2 class="section-title">4 TRỤ CỘT ĐẲNG CẤP CEO</h2>
        </div>

        <div class="pricing-grid">
            <div class="pricing-card reveal">
                <div style="width: 60px; height: 60px; background: rgba(245,158,11,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--gold-light); font-size: 1.8rem; margin-bottom: 1.25rem;">
                    <i class="fas fa-gem"></i>
                </div>
                <h3 style="font-size: 1.35rem; color: #fff; margin-bottom: 0.75rem;">1. Tiêu Chuẩn 5 Sao</h3>
                <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
                    Trang thiết bị máy tập nhập khẩu chính ngạch từ Mỹ & Đức, đạt chuẩn thi đấu cử tạ IWF và thể hình Olympic.
                </p>
            </div>

            <div class="pricing-card reveal delay-1">
                <div style="width: 60px; height: 60px; background: rgba(6,182,212,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--cyan-accent); font-size: 1.8rem; margin-bottom: 1.25rem;">
                    <i class="fas fa-user-shield"></i>
                </div>
                <h3 style="font-size: 1.35rem; color: #fff; margin-bottom: 0.75rem;">2. Riêng Tư & Bảo Mật</h3>
                <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
                    Không gian giới hạn số lượng hội viên trong mỗi khung giờ, cam kết tính riêng tư tuyệt đối cho các cuộc trao đổi chiến lược.
                </p>
            </div>

            <div class="pricing-card reveal delay-2">
                <div style="width: 60px; height: 60px; background: rgba(16,185,129,0.1); border-radius: 12px; display: flex; align-items: center; justify-content: center; color: var(--emerald-accent); font-size: 1.8rem; margin-bottom: 1.25rem;">
                    <i class="fas fa-microscope"></i>
                </div>
                <h3 style="font-size: 1.35rem; color: #fff; margin-bottom: 0.75rem;">3. Dữ Liệu Khoa Học</h3>
                <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
                    Ứng dụng máy phân tích 6 tần số InBody 770 y khoa để cá nhân hóa 100% giáo án tập luyện và khẩu phần dinh dưỡng.
                </p>
            </div>
        </div>
    </div>

    <!-- AI Luxury Facility Showcase -->
    <div style="margin-bottom: 5rem;" class="reveal">
        <div class="section-header">
            <span class="section-tag">KHÔNG GIAN KIẾN TRÚC THƯỢNG LƯU</span>
            <h2 class="section-title">HỆ THỐNG PHÒNG TẬP 5 SAO CHUẨN CEO</h2>
            <p class="section-desc">Toàn cảnh không gian tập luyện ngắm trọn thành phố và tổ hợp phục hồi Sauna đá muối Jacuzzi cao cấp.</p>
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; margin-bottom: 3rem;">
            <!-- AI Gym Panoramic View -->
            <div class="category-card" style="padding: 0; overflow: hidden; border-radius: var(--radius-md); text-align: left; align-items: stretch;">
                <div style="width: 100%; height: 320px; overflow: hidden; position: relative;">
                    <img src="<?= BASE_URL ?>/assets/images/hero-gym.jpg" alt="Phòng tập HieuMini Luxury Fitness" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;">
                    <span class="badge badge-gold" style="position: absolute; top: 15px; left: 15px; z-index: 2;">SKYLINE OLYMPIC GYM</span>
                </div>
                <div style="padding: 1.75rem;">
                    <h3 style="color: #fff; font-size: 1.3rem; margin-bottom: 0.5rem;">Sàn Tập Thể Lực Tầm Nhìn Panorama</h3>
                    <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
                        Hơn 1.500m² sàn tập trang bị đòn tạ Olympic Eleiko, giàn tạ Power Rack Monster và máy chạy bộ thương mại AC 6.0 HP hướng trọn thành phố hoa lệ.
                    </p>
                </div>
            </div>

            <!-- AI Sauna & Jacuzzi Spa -->
            <div class="category-card" style="padding: 0; overflow: hidden; border-radius: var(--radius-md); text-align: left; align-items: stretch;">
                <div style="width: 100%; height: 320px; overflow: hidden; position: relative;">
                    <img src="<?= BASE_URL ?>/assets/images/sauna-spa.jpg" alt="Sauna Đá Muối & Jacuzzi HieuMini" style="width: 100%; height: 100%; object-fit: cover; transition: transform 0.6s ease;">
                    <span class="badge badge-cyan" style="position: absolute; top: 15px; left: 15px; z-index: 2;">WELLNESS SAUNA & SPA</span>
                </div>
                <div style="padding: 1.75rem;">
                    <h3 style="color: #fff; font-size: 1.3rem; margin-bottom: 0.5rem;">Khu Phục Hồi Sauna Đá Muối & Bể Sục Jacuzzi</h3>
                    <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.6;">
                        Phòng xông hơi đá muối Himalaya kết hợp bồn sục thủy lực Jacuzzi nước nóng giải tỏa 100% căng thẳng, thúc đẩy tái tạo tế bào cơ bắp sau giờ làm việc.
                    </p>
                </div>
            </div>
        </div>
    </div>

    <!-- Facility Highlights -->
    <div class="form-card reveal" style="padding: 3.5rem; text-align: center;">
        <h2 style="font-size: 2.2rem; font-weight: 800; color: #fff; margin-bottom: 1rem;">
            TRẢI NGHIỆM KHÔNG GIAN THỰC TẾ CÙNG HIEUMINI
        </h2>
        <p style="color: var(--text-secondary); font-size: 1.05rem; max-width: 700px; margin: 0 auto 2.5rem; line-height: 1.7;">
            Hệ thống 3 chi nhánh cao cấp tại trung tâm TP.HCM và Hà Nội luôn sẵn sàng chào đón quý doanh nhân đến tham quan và trải nghiệm.
        </p>
        <button type="button" class="btn btn-primary btn-lg btn-shimmer" data-open-modal="booking-modal">
            <i class="fas fa-crown"></i> ĐẶT LỊCH THAM QUAN PHÒNG TẬP VIP
        </button>
    </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
