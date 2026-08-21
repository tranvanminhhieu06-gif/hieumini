    </main>

    <!-- Footer Section -->
    <footer class="site-footer">
        <div class="container">
            <div class="footer-grid">
                <!-- Col 1: Brand Info -->
                <div class="footer-col">
                    <img src="assets/images/logo.png" alt="HieuMini" style="height: 48px; margin-bottom: 16px;">
                    <p style="font-size: 0.875rem; line-height: 1.7; margin-bottom: 20px; color: #94a3b8;">
                        <strong>HieuMini Fashion Studio</strong> - Không ngừng sáng tạo và đổi mới để mang đến cho bạn những sản phẩm thời trang thiết kế chất lượng nhất, dẫn đầu xu hướng 2026.
                    </p>
                    <div style="display: flex; gap: 12px;">
                        <a href="#" class="action-btn" style="background: rgba(255,255,255,0.1); color: #fff;"><i class="fa-brands fa-facebook-f"></i></a>
                        <a href="#" class="action-btn" style="background: rgba(255,255,255,0.1); color: #fff;"><i class="fa-brands fa-instagram"></i></a>
                        <a href="#" class="action-btn" style="background: rgba(255,255,255,0.1); color: #fff;"><i class="fa-brands fa-tiktok"></i></a>
                        <a href="#" class="action-btn" style="background: rgba(255,255,255,0.1); color: #fff;"><i class="fa-brands fa-youtube"></i></a>
                    </div>
                </div>

                <!-- Col 2: Categories -->
                <div class="footer-col">
                    <h4>Danh Mục Nổi Bật</h4>
                    <ul class="footer-links">
                        <li><a href="products.php?cat=ao-thun-polo">Áo Thun & Polo</a></li>
                        <li><a href="products.php?cat=ao-so-mi">Áo Sơ Mi Oxford</a></li>
                        <li><a href="products.php?cat=ao-khoac-hoodie">Áo Khoác & Hoodie</a></li>
                        <li><a href="products.php?cat=quan-jeans">Quần Jeans Denim</a></li>
                        <li><a href="products.php?cat=quan-kaki">Quần Kaki & Trousers</a></li>
                        <li><a href="products.php?cat=vay-dam-nu">Váy & Đầm Thời Trang</a></li>
                    </ul>
                </div>

                <!-- Col 3: Customer Care -->
                <div class="footer-col">
                    <h4>Chăm Sóc Khách Hàng</h4>
                    <ul class="footer-links">
                        <li><a href="#" data-open-modal="size-modal">Bảng Quy Đổi Kích Cỡ (Size)</a></li>
                        <li><a href="#">Chính Sách Đổi Trả 30 Ngày</a></li>
                        <li><a href="#">Chính Sách Giao Hàng & Đồng Kiểm</a></li>
                        <li><a href="#">Hướng Dẫn Bảo Quản Quần Áo</a></li>
                        <li><a href="#">Chính Sách Bảo Mật Thông Tin</a></li>
                        <li><a href="my_orders.php">Tra Cứu Tình Trạng Đơn Hàng</a></li>
                        <li><a href="admin/index.php" style="color: #fbbf24; font-weight: 700;"><i class="fa-solid fa-shield-halved mr-1"></i> Quản Trị Hệ Thống (Admin)</a></li>
                    </ul>
                </div>

                <!-- Col 4: Contact & Newsletter -->
                <div class="footer-col">
                    <h4>Liên Hệ Với Chúng Tôi</h4>
                    <ul class="footer-contact">
                        <li><i class="fa-solid fa-location-dot" style="color: var(--accent);"></i> <span>Số 18 Duy Tân, Dịch Vọng Hậu, Cầu Giấy, Hà Nội</span></li>
                        <li><i class="fa-solid fa-phone" style="color: var(--accent);"></i> <span>0988.889.999 / 0912.345.678</span></li>
                        <li><i class="fa-solid fa-envelope" style="color: var(--accent);"></i> <span>support@hieumini.vn</span></li>
                        <li><i class="fa-solid fa-clock" style="color: var(--accent);"></i> <span>Mở cửa: 8h30 - 22h00 tất cả các ngày</span></li>
                    </ul>
                </div>
            </div>

            <!-- Footer Bottom -->
            <div class="footer-bottom">
                <p>&copy; <?= date('Y') ?> <strong>HieuMini Fashion Studio</strong>. All rights reserved. Phát triển trên nền tảng PHP & MySQL chuyên nghiệp.</p>
            </div>
        </div>
    </footer>

    <!-- Size Guide Modal Global -->
    <div id="size-modal" class="modal-overlay">
        <div class="modal-dialog">
            <div class="modal-header">
                <h3 style="font-size: 1.25rem;"><i class="fa-solid fa-ruler-combined text-accent"></i> Bảng Quy Đổi Kích Cỡ Chuẩn HieuMini</h3>
                <button type="button" class="btn-close" data-close-modal>&times;</button>
            </div>
            <div class="modal-body">
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-bottom: 12px;">
                    Số đo dưới đây được tính toán theo phom dáng chuẩn người Việt Nam. Nếu bạn phân vân giữa 2 size, vui lòng chọn size lớn hơn để mặc thoải mái.
                </p>
                <table class="size-chart-table">
                    <thead>
                        <tr>
                            <th>Size</th>
                            <th>Chiều Cao (cm)</th>
                            <th>Cân Nặng (kg)</th>
                            <th>Rộng Ngực (cm)</th>
                            <th>Dài Áo (cm)</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td><strong>S</strong></td>
                            <td>1m55 - 1m64</td>
                            <td>45 - 54 kg</td>
                            <td>96</td>
                            <td>68</td>
                        </tr>
                        <tr>
                            <td><strong>M</strong></td>
                            <td>1m65 - 1m72</td>
                            <td>55 - 65 kg</td>
                            <td>100</td>
                            <td>70</td>
                        </tr>
                        <tr>
                            <td><strong>L</strong></td>
                            <td>1m73 - 1m78</td>
                            <td>66 - 75 kg</td>
                            <td>104</td>
                            <td>72</td>
                        </tr>
                        <tr>
                            <td><strong>XL</strong></td>
                            <td>1m79 - 1m85</td>
                            <td>76 - 85 kg</td>
                            <td>108</td>
                            <td>74</td>
                        </tr>
                        <tr>
                            <td><strong>XXL</strong></td>
                            <td>1m82 - 1m90</td>
                            <td>86 - 95 kg</td>
                            <td>114</td>
                            <td>76</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <!-- Main JS Scripts -->
    <script src="assets/js/main.js"></script>
</body>
</html>
