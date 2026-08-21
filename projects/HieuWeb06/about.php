<?php
/**
 * HieuMini - Giới thiệu, giấy phép và câu hỏi thường gặp
 */
require_once __DIR__ . '/includes/config.php';

$faqs = [
    ['Mua xong tôi nhận được những gì?', 'Bạn nhận được toàn bộ mã nguồn không mã hoá, tệp database.sql chứa cấu trúc và dữ liệu mẫu, tài liệu hướng dẫn cài đặt, cùng danh sách tài khoản quản trị dùng thử. Tất cả được nén trong một tệp ZIP gửi qua email trong vòng 30 phút sau khi xác nhận thanh toán.'],
    ['Tôi có thể chỉnh sửa mã nguồn không?', 'Hoàn toàn có thể. Mã nguồn được viết bằng PHP thuần, không mã hoá, không ràng buộc bản quyền kỹ thuật. Bạn được phép đổi màu sắc, phông chữ, logo, thêm hoặc bớt tính năng theo nhu cầu.'],
    ['Ba loại giấy phép khác nhau thế nào?', 'Giấy phép Cá nhân dành cho học tập và làm đồ án, không dùng để kinh doanh. Giấy phép Thương mại cho phép triển khai trên một tên miền thật của chính bạn. Giấy phép Mở rộng cho phép bạn bàn giao lại sản phẩm cho khách hàng của mình.'],
    ['Tôi cần chuẩn bị gì để chạy được website?', 'Chỉ cần một máy tính cài XAMPP hoặc Laragon với PHP 8.0 trở lên và MySQL 5.7 trở lên. Bạn giải nén mã nguồn vào thư mục htdocs, nhập tệp database.sql qua phpMyAdmin, chỉnh thông tin kết nối trong includes/config.php là chạy được.'],
    ['Có được hỗ trợ cài đặt không?', 'Có. HieuMini hỗ trợ cài đặt miễn phí qua Zalo hoặc TeamViewer, cả trên máy cá nhân lẫn trên hosting và VPS. Thời gian hỗ trợ không giới hạn trong suốt vòng đời sản phẩm.'],
    ['Website đã tối ưu SEO tới mức nào?', 'Mỗi trang có thẻ tiêu đề và mô tả riêng, đường dẫn thân thiện, dữ liệu có cấu trúc Schema.org, sitemap.xml sinh tự động, robots.txt, thẻ Open Graph, ảnh có thuộc tính alt và kích thước cố định để tránh giật bố cục.'],
    ['Tôi có thể yêu cầu tính năng riêng không?', 'Được. Gửi mô tả yêu cầu qua trang Liên hệ, HieuMini sẽ báo giá phần tuỳ chỉnh trong vòng 24 giờ làm việc. Chi phí tuỳ chỉnh tính riêng ngoài giá mã nguồn gốc.'],
    ['Nếu phát hiện lỗi sau khi mua thì sao?', 'Mọi lỗi thuộc về mã nguồn gốc đều được sửa miễn phí trọn đời. Bạn chỉ cần mô tả lỗi kèm ảnh chụp màn hình, HieuMini sẽ gửi bản vá trong vòng 48 giờ làm việc.'],
];

$faqSchema = [
    '@context'   => 'https://schema.org',
    '@type'      => 'FAQPage',
    'mainEntity' => array_map(static fn($f) => [
        '@type'          => 'Question',
        'name'           => $f[0],
        'acceptedAnswer' => ['@type' => 'Answer', 'text' => $f[1]],
    ], $faqs),
];

seo([
    'title'       => 'Giới thiệu HieuMini - Giấy phép sử dụng & Câu hỏi thường gặp',
    'description' => 'Tìm hiểu về HieuMini, ba loại giấy phép sử dụng mã nguồn (Cá nhân, Thương mại, Mở rộng), quy trình bàn giao và giải đáp các thắc mắc thường gặp khi mua mã nguồn website.',
    'keywords'    => 'giới thiệu hieumini, giấy phép mã nguồn, câu hỏi thường gặp mua source code',
    'schema'      => '<script type="application/ld+json">' . json_encode($faqSchema, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES) . '</script>',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container page-head">
  <?= breadcrumb(['Trang chủ' => 'index.php', 'Giới thiệu' => null]) ?>
  <h1 class="reveal" style="margin-top:var(--sp-4)">Về <span class="text-grad">HieuMini</span></h1>
  <p class="reveal" style="max-width:72ch;font-size:var(--fs-lg)">
    HieuMini ra đời từ một nhu cầu rất thật: sinh viên và freelancer Việt Nam cần mã nguồn website
    đủ tốt để học, để làm đồ án và để bàn giao cho khách hàng - nhưng phần lớn mã nguồn trên thị trường
    lại thiếu tài liệu, thiếu bảo mật và gần như không quan tâm tới SEO.
  </p>
</div>

<section class="container section--tight">
  <div class="grid grid--3 stagger">
    <?php
    $values = [
        ['Minh bạch', 'Giá niêm yết công khai, không phát sinh chi phí ẩn. Mã nguồn bàn giao đầy đủ, không mã hoá, không chèn liên kết ngầm.'],
        ['Chất lượng kỹ thuật', 'Mỗi dự án đều đi qua danh sách kiểm tra gồm bảo mật, hiệu năng, SEO và khả năng tiếp cận trước khi được đưa lên kệ.'],
        ['Đồng hành lâu dài', 'Hỗ trợ kỹ thuật trọn đời và cập nhật miễn phí. Một lần mua là một mối quan hệ, không phải một giao dịch.'],
    ];
    foreach ($values as $v): ?>
      <div class="card feature reveal">
        <h2 style="font-size:var(--fs-xl)"><?= e($v[0]) ?></h2>
        <p><?= e($v[1]) ?></p>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<!-- ============ GIẤY PHÉP ============ -->
<section class="container section" id="license">
  <div class="section-head reveal">
    <span class="eyebrow">Giấy phép</span>
    <h2>Ba loại giấy phép sử dụng</h2>
    <p>Chọn đúng loại giấy phép giúp bạn dùng sản phẩm hợp pháp và yên tâm khi bàn giao cho khách.</p>
  </div>

  <div class="table-wrap reveal">
    <table class="data">
      <caption class="sr-only">So sánh ba loại giấy phép sử dụng mã nguồn</caption>
      <thead>
        <tr>
          <th scope="col">Quyền sử dụng</th>
          <th scope="col">Cá nhân</th>
          <th scope="col">Thương mại (×1,6)</th>
          <th scope="col">Mở rộng (×2,4)</th>
        </tr>
      </thead>
      <tbody>
        <tr><th scope="row">Học tập, làm đồ án</th><td>Có</td><td>Có</td><td>Có</td></tr>
        <tr><th scope="row">Chỉnh sửa mã nguồn</th><td>Có</td><td>Có</td><td>Có</td></tr>
        <tr><th scope="row">Triển khai tên miền thật</th><td>Không</td><td>1 tên miền</td><td>Không giới hạn</td></tr>
        <tr><th scope="row">Dùng cho mục đích kinh doanh</th><td>Không</td><td>Có</td><td>Có</td></tr>
        <tr><th scope="row">Bàn giao lại cho khách hàng</th><td>Không</td><td>Không</td><td>Có</td></tr>
        <tr><th scope="row">Hỗ trợ kỹ thuật</th><td>Trọn đời</td><td>Trọn đời</td><td>Trọn đời, ưu tiên</td></tr>
        <tr><th scope="row">Bán lại nguyên bản mã nguồn</th><td>Không</td><td>Không</td><td>Không</td></tr>
      </tbody>
    </table>
  </div>
  <p class="reveal" style="margin-top:var(--sp-4);font-size:var(--fs-sm);color:var(--fg-muted)">
    Lưu ý chung: mọi giấy phép đều không cho phép bán lại nguyên bản mã nguồn dưới bất kỳ hình thức nào,
    kể cả sau khi đã chỉnh sửa giao diện.
  </p>
</section>

<!-- ============ FAQ ============ -->
<section class="container section" id="faq">
  <div class="section-head reveal">
    <span class="eyebrow">Hỏi đáp</span>
    <h2>Câu hỏi thường gặp</h2>
  </div>

  <div class="accordion reveal" style="max-width:860px;margin-inline:auto">
    <?php foreach ($faqs as $i => $f): ?>
      <div class="accordion__item">
        <button class="accordion__btn" aria-expanded="<?= $i === 0 ? 'true' : 'false' ?>" aria-controls="faq-<?= $i ?>" id="faqbtn-<?= $i ?>">
          <span><?= e($f[0]) ?></span>
          <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 5v14M5 12h14"/></svg>
        </button>
        <div class="accordion__panel" id="faq-<?= $i ?>" role="region" aria-labelledby="faqbtn-<?= $i ?>" data-open="<?= $i === 0 ? 'true' : 'false' ?>">
          <div><p><?= e($f[1]) ?></p></div>
        </div>
      </div>
    <?php endforeach; ?>
  </div>
</section>

<section class="container section">
  <div class="cta-band reveal reveal--zoom">
    <h2>Vẫn còn câu hỏi khác?</h2>
    <p>Gửi câu hỏi cho HieuMini, bạn sẽ nhận được phản hồi trong vòng 24 giờ làm việc.</p>
    <a class="btn btn--primary btn--lg" href="<?= e(url('contact.php')) ?>">Liên hệ ngay</a>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
