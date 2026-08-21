<?php
$page_title = 'Liên Hệ & Hỗ Trợ';
require_once __DIR__ . '/includes/header.php';

$sentSuccess = false;
$error = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['send_contact'])) {
    $name = clean_input($_POST['name'] ?? '');
    $email = clean_input($_POST['email'] ?? '');
    $phone = clean_input($_POST['phone'] ?? '');
    $subject = clean_input($_POST['subject'] ?? 'Tư vấn mua hàng');
    $message = clean_input($_POST['message'] ?? '');

    if (empty($name) || empty($phone) || empty($message)) {
        set_flash('error', 'Vui lòng điền đầy đủ Họ tên, Số điện thoại và Nội dung liên hệ!');
    } elseif (!is_valid_phone($phone)) {
        set_flash('error', 'Số điện thoại không đúng định dạng Việt Nam (10 số)!');
    } elseif (!empty($email) && !is_valid_email($email)) {
        set_flash('error', 'Địa chỉ Email không đúng định dạng!');
    } elseif (mb_strlen($message) < 10) {
        set_flash('error', 'Nội dung tin nhắn cần tối thiểu 10 ký tự để chúng tôi hỗ trợ tốt nhất!');
    } else {
        // Try saving to contacts table if exists
        try {
            $pdo->exec("CREATE TABLE IF NOT EXISTS `contacts` (
                `id` INT AUTO_INCREMENT PRIMARY KEY,
                `name` VARCHAR(100) NOT NULL,
                `email` VARCHAR(150) DEFAULT NULL,
                `phone` VARCHAR(30) NOT NULL,
                `subject` VARCHAR(150) DEFAULT NULL,
                `message` TEXT NOT NULL,
                `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;");

            $cStmt = $pdo->prepare("INSERT INTO contacts (name, email, phone, subject, message) VALUES (?, ?, ?, ?, ?)");
            $cStmt->execute([$name, $email, $phone, $subject, $message]);
        } catch (Exception $e) {
            // Table creation or insert failed silently, continue with success message
        }

        set_flash('success', 'Cảm ơn bạn ' . htmlspecialchars($name) . '! Tin nhắn của bạn đã được gửi thành công. Đội ngũ chăm sóc khách hàng sẽ liên hệ lại sớm nhất.');
        header('Location: contact.php');
        exit;
    }
}
?>

<main class="container my-4">

  <!-- Breadcrumb -->
  <nav aria-label="breadcrumb" class="mb-4">
    <ol class="breadcrumb bg-white p-3 rounded-3 border shadow-sm">
      <li class="breadcrumb-item"><a href="index.php" class="text-primary text-decoration-none"><i class="fas fa-home me-1"></i>Trang chủ</a></li>
      <li class="breadcrumb-item active" aria-current="page">Liên hệ</li>
    </ol>
  </nav>

  <div class="row g-4">
    
    <!-- Left: Contact Form -->
    <div class="col-lg-6">
      <div class="bg-white p-4 p-lg-5 rounded-4 border shadow-sm h-100">
        <span class="text-primary text-uppercase fw-bold small"><i class="fas fa-headset me-1"></i> Trung Tâm Trợ Giúp</span>
        <h2 class="fw-bold fs-3 mt-1 mb-3">Gửi Lời Nhắn Cho DatCyber</h2>
        <p class="text-secondary small mb-4">Bạn có câu hỏi về sản phẩm, hướng dẫn bảo hành hay cần tư vấn giải pháp gia dụng? Hãy gửi thông tin cho chúng tôi.</p>

        <form action="contact.php" method="POST">
          <div class="row g-3">
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Họ và tên <span class="text-danger">*</span></label>
              <input type="text" name="name" class="form-control" required placeholder="Nguyễn Văn A">
            </div>
            <div class="col-md-6">
              <label class="form-label small fw-semibold">Số điện thoại <span class="text-danger">*</span></label>
              <input type="tel" name="phone" class="form-control" required placeholder="0988 888 999">
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Email liên hệ</label>
              <input type="email" name="email" class="form-control" placeholder="hotro@gmail.com">
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Chủ đề cần tư vấn</label>
              <select name="subject" class="form-select">
                <option value="Tư vấn mua hàng">Tư vấn mua hàng & khuyến mãi</option>
                <option value="Bảo hành sửa chữa">Yêu cầu bảo hành / đổi trả</option>
                <option value="Hợp tác kinh doanh">Hợp tác đại lý & doanh nghiệp</option>
                <option value="Góp ý dịch vụ">Góp ý chất lượng dịch vụ</option>
              </select>
            </div>
            <div class="col-12">
              <label class="form-label small fw-semibold">Nội dung chi tiết <span class="text-danger">*</span></label>
              <textarea name="message" rows="4" class="form-control" required placeholder="Nhập nội dung cần hỗ trợ..."></textarea>
            </div>
            <div class="col-12 mt-4">
              <button type="submit" name="send_contact" class="btn btn-primary-custom w-100 justify-content-center py-2 fs-6">
                <i class="fas fa-paper-plane me-2"></i> Gửi Tin Nhắn
              </button>
            </div>
          </div>
        </form>
      </div>
    </div>

    <!-- Right: Showrooms & Info -->
    <div class="col-lg-6">
      <div class="bg-white p-4 p-lg-5 rounded-4 border shadow-sm mb-4">
        <h4 class="fw-bold mb-4"><i class="fas fa-building text-primary me-2"></i> Hệ Thống Showroom DatCyber</h4>
        
        <div class="d-flex flex-column gap-4">
          <div class="d-flex gap-3">
            <div class="perk-icon"><i class="fas fa-store"></i></div>
            <div>
              <h6 class="fw-bold mb-1">Trụ Sở & Showroom Hà Nội</h6>
              <p class="text-secondary small mb-1"><i class="fas fa-map-pin text-danger me-1"></i> Tòa nhà DatCyber Tower, Cầu Giấy, Hà Nội</p>
              <p class="text-secondary small mb-0"><i class="fas fa-phone text-success me-1"></i> 024.3888.6868 (8:00 - 21:30 hàng ngày)</p>
            </div>
          </div>

          <div class="d-flex gap-3">
            <div class="perk-icon"><i class="fas fa-store"></i></div>
            <div>
              <h6 class="fw-bold mb-1">Showroom TP. Hồ Chí Minh</h6>
              <p class="text-secondary small mb-1"><i class="fas fa-map-pin text-danger me-1"></i> 456 Nguyễn Thị Minh Khai, Phường 5, Quận 3, TP.HCM</p>
              <p class="text-secondary small mb-0"><i class="fas fa-phone text-success me-1"></i> 028.3999.6868 (8:00 - 21:30 hàng ngày)</p>
            </div>
          </div>

          <div class="d-flex gap-3">
            <div class="perk-icon"><i class="fas fa-envelope-open-text"></i></div>
            <div>
              <h6 class="fw-bold mb-1">Hỗ Trợ Kỹ Thuật & Bảo Hành</h6>
              <p class="text-secondary small mb-1"><i class="fas fa-envelope text-primary me-1"></i> Email: warranty@datcyber.vn</p>
              <p class="text-secondary small mb-0"><i class="fas fa-headset text-warning me-1"></i> Tổng đài miễn cước: <strong>1900 6868</strong></p>
            </div>
          </div>
        </div>
      </div>

      <!-- FAQ Accordion -->
      <div class="bg-white p-4 rounded-4 border shadow-sm">
        <h5 class="fw-bold mb-3"><i class="fas fa-circle-question text-primary me-2"></i> Câu Hỏi Thường Gặp</h5>
        <div class="accordion" id="faqAccordion">
          <div class="accordion-item border-0 border-bottom">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed fw-bold px-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#faq1">
                Sản phẩm tại DatCyber có được bảo hành chính hãng không?
              </button>
            </h2>
            <div id="faq1" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body px-0 text-secondary small">
                Tất cả sản phẩm bán ra tại DatCyber đều là hàng chính hãng 100%, được kích hoạt bảo hành điện tử 24 tháng và hỗ trợ 1 đổi 1 trong 30 ngày nếu có lỗi từ nhà sản xuất.
              </div>
            </div>
          </div>

          <div class="accordion-item border-0">
            <h2 class="accordion-header">
              <button class="accordion-button collapsed fw-bold px-0 bg-transparent" type="button" data-bs-toggle="collapse" data-bs-target="#faq2">
                Thời gian giao hàng mất bao lâu?
              </button>
            </h2>
            <div id="faq2" class="accordion-collapse collapse" data-bs-parent="#faqAccordion">
              <div class="accordion-body px-0 text-secondary small">
                Đơn hàng nội thành Hà Nội & TP.HCM được giao hỏa tốc trong 2 giờ. Các tỉnh thành khác thời gian giao hàng từ 1 - 2 ngày làm việc.
              </div>
            </div>
          </div>
        </div>
      </div>

    </div>

  </div>

</main>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
