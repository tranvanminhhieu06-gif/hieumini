<?php
// includes/footer.php
?>
<!-- Footer -->
<footer class="main-footer">
  <div class="container">
    <div class="footer-grid">
      <!-- Col 1: Brand Info -->
      <div>
        <a href="index.php" class="brand-logo" style="color: var(--white); margin-bottom: 16px;">
          <div class="logo-icon">
            <i class="bi bi-feather"></i>
          </div>
          <div class="brand-name" style="color: var(--white);">
            Hieu<span style="color: #818cf8;">Mini</span>
          </div>
        </a>
        <p style="font-size: 0.92rem; line-height: 1.7; margin-bottom: 20px; color: #94a3b8;">
          HieuMini là thương hiệu phân phối văn phòng phẩm, đồ dùng học tập và dụng cụ mỹ thuật sáng tạo chính hãng hàng đầu cho học sinh, sinh viên và giới trẻ.
        </p>
        <div style="display: flex; gap: 12px;">
          <a href="#" class="action-btn" style="background: #1e293b; color: #fff; border-color: #334155;"><i class="bi bi-facebook"></i></a>
          <a href="#" class="action-btn" style="background: #1e293b; color: #fff; border-color: #334155;"><i class="bi bi-tiktok"></i></a>
          <a href="#" class="action-btn" style="background: #1e293b; color: #fff; border-color: #334155;"><i class="bi bi-instagram"></i></a>
          <a href="#" class="action-btn" style="background: #1e293b; color: #fff; border-color: #334155;"><i class="bi bi-youtube"></i></a>
        </div>
      </div>

      <!-- Col 2: Categories -->
      <div>
        <h4 class="footer-title">Danh Mục Đồ Dùng</h4>
        <ul class="footer-links">
          <li><a href="products.php?category=1">Bút & Dụng Cụ Viết</a></li>
          <li><a href="products.php?category=2">Sổ Tay & Vở Viết</a></li>
          <li><a href="products.php?category=3">Dụng Cụ Vẽ & Mỹ Thuật</a></li>
          <li><a href="products.php?category=4">Bìa Hồ Sơ & Lưu Trữ</a></li>
          <li><a href="products.php?category=5">Phụ Kiện Bàn Học</a></li>
          <li><a href="products.php?category=6">Ba Lô & Cặp Học Sinh</a></li>
        </ul>
      </div>

      <!-- Col 3: Quick Links -->
      <div>
        <h4 class="footer-title">Hỗ Trợ Khách Hàng</h4>
        <ul class="footer-links">
          <li><a href="about.php">Về HieuMini</a></li>
          <li><a href="contact.php">Trung Tâm Trợ Giúp</a></li>
          <li><a href="cart.php">Kiểm Tra Giỏ Hàng</a></li>
          <li><a href="login.php">Tài Khoản & Đơn Hàng</a></li>
          <li><a href="admin/index.php">Cổng Quản Trị Admin</a></li>
        </ul>
      </div>

      <!-- Col 4: Contact & Newsletter -->
      <div>
        <h4 class="footer-title">Đăng Ký Nhận Tin</h4>
        <p style="font-size: 0.88rem; margin-bottom: 14px; color: #94a3b8;">
          Nhận ngay voucher giảm giá <strong>15%</strong> cho đơn hàng đầu tiên cùng thông tin ưu đãi tựu trường mới nhất.
        </p>
        <form onsubmit="event.preventDefault(); showToast('Đăng ký nhận ưu đãi thành công!', 'success');" style="display: flex; gap: 8px; margin-bottom: 16px;">
          <input type="email" required placeholder="Nhập email của bạn..." class="form-control" style="background: #1e293b; border-color: #334155; color: #fff; padding: 10px 14px; font-size: 0.9rem;">
          <button type="submit" class="btn btn-primary btn-sm" style="white-space: nowrap;">Đăng Ký</button>
        </form>
        <div style="font-size: 0.82rem; color: #64748b;">
          <i class="bi bi-shield-check text-emerald"></i> Cam kết bảo mật thông tin 100%
        </div>
      </div>
    </div>

    <!-- Bottom Copyright -->
    <div class="footer-bottom">
      <p>© <?= date('Y') ?> <strong>HieuMini Stationery</strong>. Tất cả quyền được bảo lưu. Thiết kế & Xây dựng bởi Chuyên Gia Website.</p>
    </div>
  </div>
</footer>

<!-- Interactive Scripts -->
<script src="assets/js/main.js"></script>
</body>
</html>
