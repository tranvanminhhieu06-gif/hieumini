<?php
/**
 * HieuMini — Giới thiệu & kiến trúc hệ thống
 */

declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';

$projects = Database::all(
    "SELECT code, name, category, tech_stack, table_count, page_count, accent_from, accent_to, slug
       FROM projects WHERE status = 'published' ORDER BY sort_order ASC"
);

log_visit();

$pageTitle = 'Giới thiệu — ' . SITE_NAME;
$pageDesc  = 'Kiến trúc, quy ước mã nguồn và công nghệ đứng sau bộ sưu tập website HieuMini.';
require __DIR__ . '/includes/header.php';
?>

<section class="section" style="padding-bottom:var(--space-7)">
  <div class="container">
    <div class="section-head" data-reveal>
      <span class="eyebrow"><?= icon('code', 'ico ico-sm') ?> Về dự án</span>
      <h1 style="font-size:var(--fs-3xl)">Một chuẩn mã nguồn, sáu ngành hàng khác nhau</h1>
      <p>
        <?= e(SITE_NAME) ?> ra đời từ nhu cầu rất thực tế: gom các đồ án website rời rạc về một nơi,
        để người xem có thể mở, bấm và thao tác thật thay vì đọc mô tả suông.
      </p>
    </div>

    <div class="prose" style="max-width:74ch" data-reveal>
      <p>
        Toàn bộ sáu hệ thống trong bộ sưu tập đều được viết bằng <strong>PHP thuần</strong> theo hướng
        module hóa, không sử dụng framework. Lựa chọn này là có chủ đích: khi học lập trình web,
        việc tự tay xây dựng lớp kết nối cơ sở dữ liệu, lớp xác thực phiên và lớp định tuyến
        giúp hiểu rõ điều gì đang thực sự diễn ra ở phía máy chủ — thứ mà framework thường che đi.
      </p>
      <p>
        Cổng trưng bày <?= e(SITE_NAME) ?> mà bạn đang xem cũng tuân theo đúng nguyên tắc đó. Nó là một
        ứng dụng PHP – MySQL độc lập gồm bốn bảng dữ liệu, một lớp truy vấn PDO theo mẫu Singleton,
        một hệ thống thiết kế dựa trên biến CSS và một phân hệ quản trị chỉ tồn tại khi máy chủ
        được cấu hình biến môi trường <code>ADMIN_PASSWORD</code>.
      </p>
      <p>
        Điểm kỹ thuật thú vị nhất của cổng trưng bày nằm ở cách hiển thị dự án. Thay vì chụp ảnh màn hình,
        mỗi thẻ dự án nhúng thẳng website con qua thẻ <code>iframe</code>, được nạp trễ bằng
        <code>IntersectionObserver</code> để trang chủ không phải tải sáu website cùng lúc.
        Ở trang chi tiết, khung nhúng chuyển sang chế độ tương tác đầy đủ và có thể đổi
        giữa ba kích thước máy tính, máy tính bảng và điện thoại để kiểm tra tính đáp ứng.
      </p>
    </div>
  </div>
</section>

<section class="section--tight" style="background:var(--bg-soft);padding:var(--space-8) 0">
  <div class="container">
    <div class="section-head is-center" data-reveal>
      <span class="eyebrow"><?= icon('layers', 'ico ico-sm') ?> Kiến trúc</span>
      <h2>Ba tầng tách bạch</h2>
    </div>

    <div class="feature-list" style="--c1:var(--indigo-600); --c2:var(--violet-600)">
      <?php
      $layers = [
          ['grid',     'Tầng trình bày',   'index.php, project.php, about.php, contact.php cùng includes/header.php và includes/footer.php. Chỉ chịu trách nhiệm hiển thị, không chứa truy vấn phức tạp.'],
          ['code',     'Tầng nghiệp vụ',   'includes/functions.php cung cấp các hàm dùng chung: escape dữ liệu, sinh slug, định dạng số, token CSRF, thông báo chớp nhoáng và ghi nhật ký truy cập.'],
          ['database', 'Tầng dữ liệu',     'config/database.php bọc PDO trong lớp Database với các phương thức all(), one(), scalar() và run(). Mọi câu lệnh SQL đều dùng tham số ràng buộc.'],
      ];
      foreach ($layers as $i => [$ico, $t, $d]): ?>
        <div class="feature-item" data-reveal data-reveal-delay="<?= $i * 90 ?>">
          <span class="feature-ico"><?= icon($ico) ?></span>
          <div><b><?= e($t) ?></b><span><?= e($d) ?></span></div>
        </div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="section-head" data-reveal>
      <span class="eyebrow"><?= icon('list', 'ico ico-sm') ?> So sánh</span>
      <h2>Bảng đối chiếu sáu dự án</h2>
      <p>Quy mô và công nghệ của từng hệ thống trong bộ sưu tập.</p>
    </div>

    <div class="panel" style="overflow-x:auto;padding:0" data-reveal>
      <table style="width:100%;border-collapse:collapse;min-width:760px">
        <thead>
          <tr style="background:var(--surface-2);text-align:left">
            <th style="padding:14px 18px;font-size:var(--fs-sm)">Mã</th>
            <th style="padding:14px 18px;font-size:var(--fs-sm)">Tên dự án</th>
            <th style="padding:14px 18px;font-size:var(--fs-sm)">Lĩnh vực</th>
            <th style="padding:14px 18px;font-size:var(--fs-sm)">Công nghệ chính</th>
            <th style="padding:14px 18px;font-size:var(--fs-sm);text-align:right">Bảng</th>
            <th style="padding:14px 18px;font-size:var(--fs-sm);text-align:right">Trang</th>
          </tr>
        </thead>
        <tbody>
          <?php foreach ($projects as $p): ?>
          <tr style="border-top:1px solid var(--border)">
            <td style="padding:14px 18px">
              <span class="project-code" style="color:<?= e($p['accent_from']) ?>"><?= e($p['code']) ?></span>
            </td>
            <td style="padding:14px 18px;font-weight:600">
              <a href="<?= e(url('project.php?slug=' . rawurlencode($p['slug']))) ?>"><?= e($p['name']) ?></a>
            </td>
            <td style="padding:14px 18px;color:var(--fg-muted);font-size:var(--fs-sm)"><?= e($p['category']) ?></td>
            <td style="padding:14px 18px;color:var(--fg-muted);font-size:var(--fs-sm)">
              <?= e(implode(' · ', array_slice(split_list($p['tech_stack']), 0, 3))) ?>
            </td>
            <td style="padding:14px 18px;text-align:right;font-weight:600"><?= (int) $p['table_count'] ?></td>
            <td style="padding:14px 18px;text-align:right;font-weight:600"><?= (int) $p['page_count'] ?></td>
          </tr>
          <?php endforeach; ?>
        </tbody>
      </table>
    </div>
  </div>
</section>

<?php require __DIR__ . '/includes/footer.php'; ?>
