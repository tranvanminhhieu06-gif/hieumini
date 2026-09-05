<?php
/**
 * Giới thiệu dự án: mục tiêu, kỹ thuật sử dụng và nguyên tắc thiết kế.
 */
declare(strict_types=1);

require __DIR__ . '/config/config.php';
require __DIR__ . '/config/database.php';
require __DIR__ . '/includes/functions.php';

$stats = site_stats();

$pageTitle = 'Giới thiệu — ' . SITE_NAME;
$pageDesc  = 'HieuMini Books là đồ án môn Lập trình phát triển ứng dụng Web: thư viện sách viết bằng PHP thuần và MySQL, không dùng framework.';

require __DIR__ . '/includes/header.php';
?>

<section class="section">
  <div class="container">
    <div style="max-width:72ch">
      <span class="eyebrow">Về dự án</span>
      <h1 data-split>Một thư viện dựng bằng tay, từ đầu đến cuối</h1>
      <p class="lead">
        HieuMini Books là đồ án môn Lập trình phát triển ứng dụng Web. Toàn bộ mã nguồn viết bằng
        PHP thuần và MySQL — không Composer, không framework, không thư viện dựng sẵn cho phần
        xử lý phía máy chủ. Mục tiêu là hiểu rõ từng lớp: từ câu truy vấn, phiên làm việc,
        cho tới cách trình duyệt vẽ ra trang.
      </p>
    </div>

    <hr class="rule">

    <div style="display:grid;grid-template-columns:repeat(auto-fit,minmax(240px,1fr));gap:var(--space-6)">
      <div data-reveal>
        <h3><?= icon('book') ?> Phía máy chủ</h3>
        <p>
          PHP 8 với PDO và prepared statement — tham số luôn được tách khỏi câu lệnh SQL nên
          không có đường cho SQL Injection. Mật khẩu quản trị băm bằng bcrypt qua
          <code>password_hash()</code>. Biểu mẫu đều có mã CSRF dùng một lần.
        </p>
      </div>
      <div data-reveal>
        <h3><?= icon('grid') ?> Cơ sở dữ liệu</h3>
        <p>
          Sáu bảng InnoDB, bộ mã <code>utf8mb4</code> để giữ đủ dấu tiếng Việt. Khoá ngoại
          ràng buộc sách với tác giả và thể loại, đánh giá gắn với sách; xoá sách thì đánh giá
          của nó tự dọn theo.
        </p>
      </div>
      <div data-reveal>
        <h3><?= icon('star') ?> Giao diện</h3>
        <p>
          Bố cục lưới 12 cột theo tinh thần Swiss Modernism, chữ Cormorant Garamond và Crimson Pro
          tự host. Hiệu ứng cuộn dùng GSAP với Lenis, nhưng nội dung chỉ ẩn khi có JavaScript —
          tắt JS thì trang vẫn đọc được đầy đủ.
        </p>
      </div>
    </div>

    <hr class="rule">

    <div class="hero-meta" style="border-top:0;padding-top:0">
      <div><b><?= format_number($stats['books']) ?></b><span>Đầu sách</span></div>
      <div><b><?= format_number($stats['authors']) ?></b><span>Tác giả</span></div>
      <div><b><?= format_number($stats['categories']) ?></b><span>Thể loại</span></div>
      <div><b><?= format_number($stats['reviews']) ?></b><span>Đánh giá đã duyệt</span></div>
    </div>

    <hr class="rule">

    <div style="max-width:72ch">
      <h2 style="font-size:var(--fs-2xl)">Về hình ảnh bìa sách</h2>
      <p>
        Toàn bộ <?= format_number($stats['books']) ?> bìa sách trên trang là thiết kế chữ do chính
        dự án tạo ra bằng một script Python (<code>tools/generate_covers.py</code>), không phải ảnh
        bìa thật của nhà xuất bản. Lý do đơn giản: bìa sách thật thuộc bản quyền của đơn vị phát
        hành, dùng lại trong đồ án là rủi ro không cần thiết. Cách làm này còn giữ cho cả kho sách
        có chung một ngôn ngữ thị giác.
      </p>

      <h2 style="font-size:var(--fs-2xl)">Dữ liệu sách</h2>
      <p>
        Thông tin thư mục (tựa, tác giả, nhà xuất bản, năm, số trang) là dữ liệu tra cứu về các đầu
        sách có thật. Phần tóm tắt do dự án viết lại ngắn gọn, không sao chép nội dung tác phẩm.
        Các đánh giá trong cơ sở dữ liệu mẫu là dữ liệu minh hoạ phục vụ demo chức năng.
      </p>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
