<?php
$page_title = 'Giới Thiệu Về DatCyber';
require_once __DIR__ . '/includes/header.php';
?>

<main class="container my-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-3 border shadow-sm">
      <li class="breadcrumb-item"><a href="index.php" class="text-primary text-decoration-none"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
      <li class="breadcrumb-item active" aria-current="page">Giới thiệu</li>
    </ol>
  </nav>

  <!-- Hero Intro -->
  <section class="bg-white p-4 p-lg-5 rounded-4 border shadow-sm mb-5">
    <div class="row align-items-center g-5">
      <div class="col-lg-6">
        <span class="text-primary text-uppercase fw-bold small"><i class="fas fa-sparkles me-1"></i> Thương Hiệu Gia Dụng Thông Minh</span>
        <h1 class="fw-bold fs-2 mt-2 mb-3">DatCyber - Kiến Tạo Cuộc Sống Tiện Nghi Đỉnh Cao</h1>
        <p class="text-secondary" style="line-height: 1.8;">
          Được thành lập với khát vọng mang đến những giải pháp công nghệ gia dụng hiện đại, thông minh và tinh tế nhất cho mỗi gia đình Việt, <strong>DatCyber</strong> không ngừng nghiên cứu và tuyển chọn những thiết bị gia dụng đạt tiêu chuẩn chất lượng quốc tế.
        </p>
        <p class="text-secondary" style="line-height: 1.8;">
          Chúng tôi tin rằng ngôi nhà không chỉ là nơi để ở, mà là tổ ấm tận hưởng những khoảnh khắc hạnh phúc cùng bữa ăn ngon, không khí trong lành và không gian sạch sẽ thảnh thơi.
        </p>
      </div>
      <div class="col-lg-6">
        <img src="assets/images/products/air_purifier.jpg" alt="About DatCyber" class="img-fluid rounded-4 shadow-lg">
      </div>
    </div>
  </section>

  <!-- Core Values -->
  <section class="my-5">
    <div class="text-center max-w-600 mx-auto mb-5">
      <span class="text-primary text-uppercase fw-bold small">Giá Trị Cốt Lõi</span>
      <h2 class="fw-bold mt-1">4 Trụ Cột Vững Chắc Của DatCyber</h2>
    </div>

    <div class="row g-4">
      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm text-center h-100">
          <div class="category-icon-wrap mb-3"><i class="fas fa-gem"></i></div>
          <h5 class="fw-bold">Chất Lượng Thượng Hạng</h5>
          <p class="text-secondary small m-0">100% linh kiện cao cấp, đạt chứng nhận an toàn thực phẩm và CE/RoHS quốc tế.</p>
        </div>
      </div>

      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm text-center h-100">
          <div class="category-icon-wrap mb-3"><i class="fas fa-microchip"></i></div>
          <h5 class="fw-bold">Công Nghệ Đột Phá</h5>
          <p class="text-secondary small m-0">Tích hợp AI cảm biến nhiệt, kết nối điều khiển qua Smartphone và tiết kiệm năng lượng.</p>
        </div>
      </div>

      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm text-center h-100">
          <div class="category-icon-wrap mb-3"><i class="fas fa-shield-heart"></i></div>
          <h5 class="fw-bold">Bảo Hành 24 Tháng</h5>
          <p class="text-secondary small m-0">Chính sách 1 đổi 1 trong 30 ngày đầu tiên và hỗ trợ bảo hành chính hãng toàn quốc.</p>
        </div>
      </div>

      <div class="col-md-3">
        <div class="p-4 bg-white rounded-4 border shadow-sm text-center h-100">
          <div class="category-icon-wrap mb-3"><i class="fas fa-hand-holding-heart"></i></div>
          <h5 class="fw-bold">Tận Tâm Phục Vụ</h5>
          <p class="text-secondary small m-0">Đội ngũ kỹ thuật viên am hiểu, tư vấn và hỗ trợ khách hàng chu đáo 24/7.</p>
        </div>
      </div>
    </div>
  </section>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
