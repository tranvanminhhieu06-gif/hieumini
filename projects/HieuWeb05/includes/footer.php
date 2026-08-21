<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - FOOTER & MODALS TEMPLATE
 * Standard: CEO Executive Edition
 */
?>
    <!-- Site Footer -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Col 1: Brand Info -->
                <div>
                    <a href="<?= BASE_URL ?>/index.php" class="brand-logo" style="margin-bottom: 1.25rem;">
                        <div class="logo-icon">HM</div>
                        <div class="logo-text">
                            <span class="brand-name">HIEUMINI</span>
                            <span class="brand-tagline">LUXURY FITNESS</span>
                        </div>
                    </a>
                    <p style="color: var(--text-secondary); font-size: 0.95rem; line-height: 1.7; margin-bottom: 1.5rem;">
                        Tổ hợp thể hình thượng lưu chuẩn 5 sao dành riêng cho các nhà lãnh đạo, doanh nhân và người đam mê phong cách sống đỉnh cao.
                    </p>
                    <div style="display: flex; gap: 0.75rem;">
                        <a href="#" class="action-btn-circle" title="Facebook"><i class="fab fa-facebook-f"></i></a>
                        <a href="#" class="action-btn-circle" title="Instagram"><i class="fab fa-instagram"></i></a>
                        <a href="#" class="action-btn-circle" title="YouTube"><i class="fab fa-youtube"></i></a>
                        <a href="#" class="action-btn-circle" title="TikTok"><i class="fab fa-tiktok"></i></a>
                    </div>
                </div>

                <!-- Col 2: Quick Links -->
                <div>
                    <h4 class="footer-widget-title">Khám Phá</h4>
                    <ul class="footer-links-list">
                        <li><a href="<?= BASE_URL ?>/about.php"><i class="fas fa-angle-right" style="color: var(--gold-primary); font-size: 0.8rem;"></i> Về HieuMini Club</a></li>
                        <li><a href="<?= BASE_URL ?>/products.php"><i class="fas fa-angle-right" style="color: var(--gold-primary); font-size: 0.8rem;"></i> Cửa Hàng Thể Hình</a></li>
                        <li><a href="<?= BASE_URL ?>/services.php"><i class="fas fa-angle-right" style="color: var(--gold-primary); font-size: 0.8rem;"></i> Thẻ Hội Viên VIP</a></li>
                        <li><a href="<?= BASE_URL ?>/contact.php"><i class="fas fa-angle-right" style="color: var(--gold-primary); font-size: 0.8rem;"></i> Đăng Ký Trải Nghiệm</a></li>
                    </ul>
                </div>

                <!-- Col 3: Customer Care -->
                <div>
                    <h4 class="footer-widget-title">Hỗ Trợ VIP</h4>
                    <ul class="footer-links-list">
                        <li><a href="<?= BASE_URL ?>/contact.php">Tư Vấn Cá Nhân 1:1</a></li>
                        <li><a href="#">Chính Sách Hội Viên VIP</a></li>
                        <li><a href="#">Bảo Hành Thiết Bị 5 Năm</a></li>
                        <li><a href="#">Cam Kết 100% Dinh Dưỡng Thật</a></li>
                        <li><a href="#">Điều Khoản Dịch Vụ & Bảo Mật</a></li>
                    </ul>
                </div>

                <!-- Col 4: Newsletter & Contact -->
                <div>
                    <h4 class="footer-widget-title">Liên Hệ & Đăng Ký</h4>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                        <i class="fas fa-map-marker-alt" style="color: var(--gold-primary); margin-right: 0.5rem;"></i> <?= SITE_ADDRESS ?>
                    </p>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1rem;">
                        <i class="fas fa-phone-alt" style="color: var(--gold-primary); margin-right: 0.5rem;"></i> Hotline: <strong style="color: var(--gold-light);"><?= SITE_HOTLINE ?></strong>
                    </p>
                    <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.25rem;">
                        <i class="fas fa-envelope" style="color: var(--gold-primary); margin-right: 0.5rem;"></i> Email: <?= SITE_EMAIL ?>
                    </p>
                    
                    <form onsubmit="event.preventDefault(); showToast('success', 'Đã đăng ký nhận tin ưu đãi VIP!'); this.reset();" style="display: flex; gap: 0.4rem;">
                        <input type="email" placeholder="Email nhận ưu đãi CEO..." required style="padding: 0.6rem 0.8rem; background: var(--bg-secondary); border: 1px solid var(--border-medium); border-radius: var(--radius-sm); color: #fff; font-size: 0.85rem; flex-grow: 1;">
                        <button type="submit" class="btn btn-primary btn-sm"><i class="fas fa-paper-plane"></i></button>
                    </form>
                </div>
            </div>

            <!-- Bottom Bar -->
            <div class="footer-bottom-bar">
                <div>
                    &copy; <?= date('Y') ?> <strong><?= SITE_NAME ?></strong>. Thiết kế chuẩn CEO Executive Edition. Bảo lưu mọi quyền.
                </div>
                <div style="display: flex; gap: 1.5rem;">
                    <span>Giờ mở cửa: <strong>05:30 - 22:30</strong> (Thứ 2 - CN)</span>
                    <span style="color: var(--emerald-accent);"><i class="fas fa-shield-alt"></i> Chứng nhận Tiêu chuẩn 5 Sao</span>
                </div>
            </div>
        </div>
    </footer>

    <!-- VIP Booking Modal Popup -->
    <div id="booking-modal" class="modal-overlay">
        <div class="modal-container">
            <div class="modal-header">
                <h3 style="color: var(--gold-light); font-size: 1.35rem; display: flex; align-items: center; gap: 0.6rem;">
                    <i class="fas fa-crown"></i> ĐẶT LỊCH TRẢI NGHIỆM VIP CEO
                </h3>
                <button type="button" class="modal-close-btn">&times;</button>
            </div>
            <div class="modal-body">
                <p style="color: var(--text-secondary); font-size: 0.9rem; margin-bottom: 1.5rem;">
                    Trải nghiệm miễn phí 01 ngày tập luyện đẳng cấp 5 sao, đo chỉ số InBody 770 y khoa và tư vấn lộ trình cùng Huấn Luyện Viên Trưởng.
                </p>
                <form id="vip-booking-form">
                    <div class="form-group">
                        <label>Họ và Tên Doanh Nhân / Hội Viên (*)</label>
                        <input type="text" name="full_name" class="form-control" placeholder="Ví dụ: Nguyễn Hoàng Long" required>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Số Điện Thoại (*)</label>
                            <input type="tel" name="phone" class="form-control" placeholder="0988 888 xxx" required>
                        </div>
                        <div class="form-group">
                            <label>Email Liên Hệ</label>
                            <input type="email" name="email" class="form-control" placeholder="ceo@company.com">
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Dịch Vụ Quan Tâm</label>
                            <select name="service_type" class="form-control">
                                <option value="Gói Hội Viên CEO Diamond Elite">Gói Hội Viên CEO Diamond Elite</option>
                                <option value="Huấn Luyện Viên Cá Nhân 1:1 VIP">Huấn Luyện Viên Cá Nhân 1:1 VIP</option>
                                <option value="Trị Liệu Thể Thao & Giãn Cơ Phục Hồi">Trị Liệu Thể Thao & Phục Hồi</option>
                                <option value="Yoga & Thiền Định Doanh Nhân">Yoga & Thiền Định Doanh Nhân</option>
                                <option value="Tư Vấn Thiết Bị Phòng Gym Doanh Nghiệp">Tư Vấn Thiết Bị Doanh Nghiệp</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label>Chi Nhánh Trải Nghiệm</label>
                            <select name="branch" class="form-control">
                                <option value="HieuMini Diamond - Quận 1, TP.HCM">HieuMini Diamond - Quận 1, TP.HCM</option>
                                <option value="HieuMini Landmark - Bình Thạnh, TP.HCM">HieuMini Landmark - Bình Thạnh</option>
                                <option value="HieuMini Premier - Hoàn Kiếm, Hà Nội">HieuMini Premier - Hà Nội</option>
                            </select>
                        </div>
                    </div>
                    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 1rem;">
                        <div class="form-group">
                            <label>Ngày Tập Dự Kiến</label>
                            <input type="date" name="booking_date" class="form-control" value="<?= date('Y-m-d', strtotime('+1 day')) ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Khung Giờ Thuận Tiện</label>
                            <select name="booking_time" class="form-control">
                                <option value="06:00 - 08:00 (Sáng sớm)">06:00 - 08:00 (Sáng sớm)</option>
                                <option value="09:00 - 11:30 (Buổi sáng)">09:00 - 11:30 (Buổi sáng)</option>
                                <option value="14:00 - 17:00 (Buổi chiều)">14:00 - 17:00 (Buổi chiều)</option>
                                <option value="18:00 - 20:30 (Buổi tối)">18:00 - 20:30 (Buổi tối)</option>
                            </select>
                        </div>
                    </div>
                    <div class="form-group">
                        <label>Mục Tiêu Thể Hình / Yêu Cầu Riêng</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="Ví dụ: Giảm mỡ bụng, tăng thể lực lãnh đạo, muốn phòng tập riêng tư..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary btn-block btn-shimmer" style="margin-top: 1rem;">
                        <i class="fas fa-check-circle"></i> XÁC NHẬN ĐĂNG KÝ TRẢI NGHIỆM VIP
                    </button>
                </form>
            </div>
        </div>
    </div>

    <!-- Floating VIP Quick Actions & Back to Top -->
    <div class="floating-quick-actions">
        <a href="tel:0988889999" class="floating-btn float-hotline" title="Gọi Hotline VIP 24/7">
            <i class="fas fa-phone-alt"></i>
            <span class="floating-tooltip">Hotline: 0988.889.999</span>
        </a>
        <button type="button" class="floating-btn float-booking" data-open-modal="booking-modal" title="Đặt Lịch Tập Thử VIP">
            <i class="fas fa-crown"></i>
            <span class="floating-tooltip">Đặt Lịch Trải Nghiệm VIP</span>
        </button>
        <button type="button" id="back-to-top" class="floating-btn float-top" title="Lên đầu trang">
            <i class="fas fa-chevron-up"></i>
        </button>
    </div>

    <!-- Toast Notification Container -->
    <div class="toast-container"></div>

    <?php
    // Kiểm tra và hiển thị flash message từ PHP nếu có
    $flash = get_flash();
    if ($flash):
    ?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            showToast('<?= $flash['type'] ?>', '<?= addslashes($flash['message']) ?>');
        });
    </script>
    <?php endif; ?>

    <!-- Main JavaScript Engine -->
    <script src="<?= BASE_URL ?>/assets/js/main.js"></script>
</body>
</html>
