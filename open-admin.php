<?php
/**
 * HieuMini — Cầu nối xem trang quản trị trực tiếp (chế độ trưng bày)
 * ---------------------------------------------------------------
 * Mở thẳng trang quản trị của một dự án con mà KHÔNG cần gõ mật khẩu.
 *
 * Cách hoạt động: nạp chính biểu mẫu đăng nhập thật của dự án con trong một
 * iframe cùng nguồn (same-origin), rồi dùng JavaScript tự điền email + mật
 * khẩu demo và bấm nút đăng nhập giúp. Vì đây là biểu mẫu thật của dự án đó,
 * mọi cơ chế riêng của nó — token CSRF, cấu trúc phiên, chuyển hướng sau
 * đăng nhập — đều được tôn trọng tự động. Sau khi đăng nhập, iframe tự chuyển
 * tới bảng điều khiển và hiển thị toàn màn hình.
 *
 * Đây là tính năng phục vụ trưng bày sản phẩm. Toàn bộ tài khoản quản trị của
 * sáu dự án con đã được đặt về mật khẩu demo chung (xem database/tidb_all.sql).
 */

declare(strict_types=1);
require __DIR__ . '/includes/bootstrap.php';

$slug = trim((string) ($_GET['slug'] ?? ''));

$project = $slug === '' ? null : Database::one(
    "SELECT * FROM projects WHERE slug = ? AND status = 'published' LIMIT 1",
    [$slug]
);

if (!$project) {
    http_response_code(404);
    redirect('index.php');
}

$demo = project_admin_demo($project['code']);

// Dự án không hỗ trợ đăng nhập tự động → mở thẳng trang quản trị của nó
if ($demo === null) {
    redirect(project_url($project, $project['admin_path']));
}

$loginUrl = project_url($project, $demo['login']);
$adminUrl = project_url($project, $project['admin_path']);

$pageTitle = 'Quản trị ' . $project['name'] . ' — ' . SITE_NAME;
?>
<!doctype html>
<html lang="vi" data-theme="light">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">
<meta name="robots" content="noindex, nofollow">
<title><?= e($pageTitle) ?></title>
<link rel="stylesheet" href="<?= e(asset('css/style.css')) ?>">
<script>
(function () {
  try {
    var t = localStorage.getItem('hieumini-theme');
    if (t) document.documentElement.setAttribute('data-theme', t);
  } catch (e) {}
})();
</script>
<style>
  body { margin: 0; background: var(--bg); }
  .oa-bar {
    position: sticky; top: 0; z-index: 10; display: flex; align-items: center; gap: var(--space-3);
    padding: 10px 16px; background: var(--glass); border-bottom: 1px solid var(--border);
    -webkit-backdrop-filter: blur(14px); backdrop-filter: blur(14px);
  }
  .oa-bar .brand { font-size: var(--fs-sm); }
  .oa-title { font-family: var(--font-display); font-weight: 600; font-size: var(--fs-sm); color: var(--fg); }
  .oa-badge {
    display: inline-flex; align-items: center; gap: 6px; padding: 4px 11px; border-radius: var(--r-full);
    background: var(--accent-soft); color: var(--accent); font-size: 11.5px; font-weight: 700;
    text-transform: uppercase; letter-spacing: .05em;
  }
  .oa-badge i { width: 7px; height: 7px; border-radius: 50%; background: #34D399; animation: pulse 1.9s infinite; }
  .oa-spacer { flex: 1; }
  .oa-stage { position: relative; height: calc(100vh - 57px); background: var(--bg-soft); }
  .oa-stage iframe { width: 100%; height: 100%; border: 0; background: #fff; display: block; }
  .oa-cover {
    position: absolute; inset: 0; z-index: 5; display: grid; place-items: center; gap: 16px;
    background: var(--bg); transition: opacity .5s var(--ease-soft);
  }
  .oa-cover.is-done { opacity: 0; pointer-events: none; }
  .oa-cover .frame-spinner {
    width: 34px; height: 34px; border-radius: 50%;
    border: 3px solid var(--border-strong); border-top-color: var(--primary);
    animation: spin .8s linear infinite;
  }
  .oa-cover p { margin: 0; color: var(--fg-muted); font-size: var(--fs-sm); }
  .oa-cover .step { font-family: var(--font-display); font-weight: 600; color: var(--fg); }
  .oa-manual { display: none; margin-top: 8px; }
  .oa-manual.is-shown { display: block; }
</style>
</head>
<body>

<div class="oa-bar">
  <a class="brand" href="<?= e(url('index.php')) ?>">
    <span class="brand-mark" aria-hidden="true">H</span>
    <span><?= e(SITE_NAME) ?></span>
  </a>
  <span class="oa-title"><?= icon('lock', 'ico ico-sm') ?> Quản trị · <?= e($project['name']) ?></span>
  <span class="oa-spacer"></span>
  <span class="oa-badge"><i></i> Đăng nhập tự động</span>
  <a class="btn btn--ghost btn--sm" href="<?= e($adminUrl) ?>" target="_blank" rel="noopener">
    <?= icon('external', 'ico ico-sm') ?> Tab mới
  </a>
  <a class="btn btn--soft btn--sm" href="<?= e(url('project.php?slug=' . rawurlencode($project['slug']))) ?>">
    <?= icon('arrow', 'ico ico-sm') ?> Về dự án
  </a>
</div>

<div class="oa-stage">
  <div class="oa-cover" id="oaCover">
    <span class="frame-spinner"></span>
    <p class="step" id="oaStep">Đang mở trang đăng nhập…</p>
    <p>Hệ thống đang tự đăng nhập bằng tài khoản demo, vui lòng chờ.</p>
    <a class="btn btn--primary btn--sm oa-manual" id="oaManual" href="<?= e($loginUrl) ?>" target="_blank" rel="noopener">
      <?= icon('external', 'ico ico-sm') ?> Mở trang đăng nhập thủ công
    </a>
  </div>

  <iframe id="oaFrame" title="Quản trị <?= e($project['name']) ?>"
          src="<?= e($loginUrl) ?>"></iframe>
</div>

<script>
(function () {
  var frame  = document.getElementById('oaFrame');
  var cover  = document.getElementById('oaCover');
  var step   = document.getElementById('oaStep');
  var manual = document.getElementById('oaManual');

  var EMAIL = <?= json_encode($demo['email']) ?>;
  var PASS  = <?= json_encode($demo['password']) ?>;
  var LOGIN_URL = <?= json_encode($loginUrl) ?>;

  var submitted = false;   // đã bấm đăng nhập chưa
  var loads = 0;           // đếm số lần iframe tải xong

  function looksLikeLogin(doc, url) {
    if (/login\.php/i.test(url)) return true;
    return !!(doc && doc.querySelector('input[type="password"]'));
  }

  function fillAndSubmit(doc) {
    // Điền ô mật khẩu
    var pass = doc.querySelector('input[type="password"]');
    if (!pass) return false;

    // Điền ô email / tên đăng nhập
    var email = doc.querySelector('input[type="email"]')
             || doc.querySelector('input[name="email"]')
             || doc.querySelector('input[name="username"]');

    var setVal = function (el, val) {
      if (!el) return;
      el.value = val;
      el.dispatchEvent(new Event('input',  { bubbles: true }));
      el.dispatchEvent(new Event('change', { bubbles: true }));
    };
    setVal(email, EMAIL);
    setVal(pass, PASS);

    // Tìm đúng biểu mẫu chứa ô mật khẩu rồi gửi
    var form = pass.form || pass.closest('form');
    if (!form) return false;

    // Ưu tiên bấm nút submit thật (một số biểu mẫu gắn xử lý vào sự kiện click)
    var btn = form.querySelector('button[type="submit"], input[type="submit"], button:not([type])');
    submitted = true;
    step.textContent = 'Đang đăng nhập…';
    if (btn) { btn.click(); } else { form.submit(); }
    return true;
  }

  function showManual(msg) {
    step.textContent = msg || 'Không thể tự đăng nhập.';
    manual.classList.add('is-shown');
  }

  frame.addEventListener('load', function () {
    loads++;
    var doc, url;
    try {
      doc = frame.contentDocument || frame.contentWindow.document;
      url = frame.contentWindow.location.href;
    } catch (e) {
      // Không đọc được nội dung iframe (khác nguồn) → để người dùng tự thao tác
      showManual('Trình duyệt chặn nội dung nhúng.');
      return;
    }

    if (looksLikeLogin(doc, url)) {
      if (!submitted) {
        // Lần đầu thấy trang đăng nhập → điền và gửi
        if (!fillAndSubmit(doc)) {
          showManual('Không tìm thấy biểu mẫu đăng nhập.');
        }
      } else {
        // Đã gửi nhưng vẫn quay lại trang đăng nhập → sai thông tin
        showManual('Đăng nhập demo chưa sẵn sàng. Có thể chưa nạp mật khẩu demo vào CSDL.');
      }
    } else {
      // Không còn ở trang đăng nhập → đã vào được khu quản trị
      step.textContent = 'Đã đăng nhập!';
      cover.classList.add('is-done');
      setTimeout(function () { cover.style.display = 'none'; }, 550);
    }
  });

  // Dự phòng: sau 12 giây vẫn chưa xong thì hiện nút thủ công
  setTimeout(function () {
    if (!cover.classList.contains('is-done')) showManual();
  }, 12000);
})();
</script>
</body>
</html>
