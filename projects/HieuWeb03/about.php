<?php
// about.php - About HieuMini Stationery
$custom_page_title = "Về Chúng Tôi - Câu Chuyện HieuMini";
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/includes/navbar.php';
?>

<div class="container" style="margin: 40px auto 70px;">
  <!-- Breadcrumb -->
  <div style="padding: 10px 0 20px; font-size: 0.88rem; color: var(--muted); display: flex; align-items: center; gap: 8px;">
    <a href="index.php" style="color: var(--muted);"><i class="bi bi-house"></i> Trang chủ</a>
    <span>/</span>
    <span style="color: var(--dark); font-weight: 700;">Giới thiệu</span>
  </div>

  <div style="background: var(--white); border-radius: var(--radius-xl); border: 1px solid var(--border); padding: 50px 40px; box-shadow: var(--shadow-sm); margin-bottom: 50px;">
    <div style="max-width: 800px; margin: 0 auto; text-align: center;">
      <span class="section-pill">CÂU CHUYỆN THƯƠNG HIỆU</span>
      <h1 style="font-size: 2.4rem; font-weight: 800; margin-bottom: 20px; color: var(--dark);">
        HieuMini - Đồng Hành Cùng Mọi Ước Mơ Học Đường
      </h1>
      <p style="font-size: 1.1rem; line-height: 1.8; color: #475569; margin-bottom: 30px;">
        Được sáng lập với niềm đam mê dành cho những cây bút xinh xắn, cuốn sổ còng tinh tế và những mảng màu nghệ thuật rực rỡ, <strong>HieuMini</strong> không đơn thuần chỉ là một cửa hàng văn phòng phẩm, mà là không gian khơi nguồn cảm hứng sáng tạo bất tận cho hàng triệu học sinh, sinh viên trên khắp cả nước.
      </p>
    </div>

    <!-- 3 Pillars Grid -->
    <div class="about-pillars-grid">
      <div style="background: var(--bg-light); border-radius: var(--radius-lg); padding: 30px 24px; text-align: center; border: 1px solid var(--border);">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: #e0e7ff; color: var(--primary); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 18px;">
          <i class="bi bi-stars"></i>
        </div>
        <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 10px; color: var(--dark);">Chất Lượng Vượt Trội</h3>
        <p style="font-size: 0.92rem; color: var(--muted); line-height: 1.6;">
          100% sản phẩm được tuyển chọn kỹ lưỡng từ các thương hiệu hàng đầu như Pentel, Faber-Castell, Tombow, Kokuyo Campus, Casio.
        </p>
      </div>

      <div style="background: var(--bg-light); border-radius: var(--radius-lg); padding: 30px 24px; text-align: center; border: 1px solid var(--border);">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: #fdf2f8; color: var(--secondary); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 18px;">
          <i class="bi bi-palette"></i>
        </div>
        <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 10px; color: var(--dark);">Thiết Kế Sáng Tạo</h3>
        <p style="font-size: 0.92rem; color: var(--muted); line-height: 1.6;">
          Phong cách Pastel Morandi hiện đại, tone màu ngọt ngào, tối giản nhưng đậm chất nghệ thuật, truyền năng lượng tích cực khi học tập.
        </p>
      </div>

      <div style="background: var(--bg-light); border-radius: var(--radius-lg); padding: 30px 24px; text-align: center; border: 1px solid var(--border);">
        <div style="width: 60px; height: 60px; border-radius: 50%; background: #ecfdf5; color: var(--accent-emerald); display: flex; align-items: center; justify-content: center; font-size: 1.6rem; margin: 0 auto 18px;">
          <i class="bi bi-heart-pulse"></i>
        </div>
        <h3 style="font-size: 1.2rem; font-weight: 800; margin-bottom: 10px; color: var(--dark);">Tận Tâm Phục Vụ</h3>
        <p style="font-size: 0.92rem; color: var(--muted); line-height: 1.6;">
          Giao hàng hỏa tốc, đóng gói bọc xốp chống sốc tỉ mỉ kèm thiệp cảm ơn và sticker dễ thương trong mỗi kiện hàng gửi tới bạn.
        </p>
      </div>
    </div>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
