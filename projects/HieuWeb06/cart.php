<?php
/**
 * HieuMini - Giỏ hàng
 */
require_once __DIR__ . '/includes/config.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();
    $action = input('action');

    if ($action === 'remove') {
        cart_remove((int)($_POST['id'] ?? 0));
        flash('Đã xoá dự án khỏi giỏ hàng.');
    } elseif ($action === 'clear') {
        cart_clear();
        flash('Đã xoá toàn bộ giỏ hàng.');
    } elseif ($action === 'license') {
        $id = (int)($_POST['id'] ?? 0);
        $license = in_array($_POST['license'] ?? '', ['personal', 'commercial', 'extended'], true) ? $_POST['license'] : 'personal';
        if (isset($_SESSION['cart'][$id])) {
            $_SESSION['cart'][$id]['license'] = $license;
            flash('Đã cập nhật giấy phép sử dụng.');
        }
    } elseif ($action === 'coupon') {
        $code = mb_strtoupper(input('code'), 'UTF-8');
        if ($code === '') {
            unset($_SESSION['coupon']);
            flash('Đã gỡ mã giảm giá.', 'info');
        } else {
            $stmt = $pdo->prepare('SELECT * FROM coupons WHERE code = ? AND status = 1
                                   AND (expires_at IS NULL OR expires_at >= CURDATE())
                                   AND used_count < usage_limit');
            $stmt->execute([$code]);
            if ($stmt->fetch()) {
                $_SESSION['coupon'] = $code;
                flash('Áp dụng mã ' . $code . ' thành công.');
            } else {
                flash('Mã giảm giá không hợp lệ hoặc đã hết hạn.', 'error');
            }
        }
    }
    redirect('cart.php');
}

$cart = cart_detail($pdo);

seo([
    'title'       => 'Giỏ hàng | ' . SITE_NAME,
    'description' => 'Xem lại các dự án bạn đã chọn, đổi giấy phép sử dụng và áp dụng mã giảm giá trước khi thanh toán.',
    'robots'      => 'noindex, nofollow',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container page-head">
  <?= breadcrumb(['Trang chủ' => 'index.php', 'Giỏ hàng' => null]) ?>
  <h1 style="margin-top:var(--sp-4)">Giỏ hàng của bạn</h1>
  <p><?= cart_count() ?> dự án đang chờ thanh toán.</p>
</div>

<div class="container section--tight">
<?php if (!$cart['rows']): ?>
  <div class="glass empty-state reveal">
    <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h2l2.2 9.4a2 2 0 0 0 2 1.6h6.2a2 2 0 0 0 2-1.5L20 8H7"/><circle cx="10" cy="19" r="1.4"/><circle cx="17" cy="19" r="1.4"/></svg>
    <h2>Giỏ hàng đang trống</h2>
    <p>Hãy khám phá kho dự án và chọn mã nguồn phù hợp với nhu cầu của bạn.</p>
    <a class="btn btn--primary btn--lg" href="<?= e(url('projects.php')) ?>">Khám phá kho dự án</a>
  </div>
<?php else: ?>
  <div class="detail-grid">
    <div class="glass reveal">
      <?php foreach ($cart['rows'] as $row): $pr = $row['project']; ?>
        <div class="cart-row">
          <img src="<?= e(asset($pr['thumbnail'])) ?>" alt="" width="110" height="69" loading="lazy">
          <div>
            <h3 style="font-size:var(--fs-base);margin-bottom:6px">
              <a href="<?= e(url('project-detail.php?slug=' . $pr['slug'])) ?>"><?= e($pr['title']) ?></a>
            </h3>
            <form method="post" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="license">
              <input type="hidden" name="id" value="<?= (int)$pr['id'] ?>">
              <label class="sr-only" for="lic<?= (int)$pr['id'] ?>">Giấy phép sử dụng</label>
              <select class="input" id="lic<?= (int)$pr['id'] ?>" name="license" onchange="this.form.submit()" style="max-width:280px">
                <option value="personal"   <?= $row['license'] === 'personal' ? 'selected' : '' ?>>Cá nhân · Học tập</option>
                <option value="commercial" <?= $row['license'] === 'commercial' ? 'selected' : '' ?>>Thương mại · 1 tên miền (×1,6)</option>
                <option value="extended"   <?= $row['license'] === 'extended' ? 'selected' : '' ?>>Mở rộng · Bàn giao khách (×2,4)</option>
              </select>
              <noscript><button class="btn btn--ghost btn--sm" type="submit">Cập nhật</button></noscript>
            </form>
          </div>
          <div style="text-align:right">
            <strong style="display:block;font-family:var(--font-display);font-size:var(--fs-lg)"><?= money($row['line_total']) ?></strong>
            <form method="post" style="margin-top:8px">
              <?= csrf_field() ?>
              <input type="hidden" name="action" value="remove">
              <input type="hidden" name="id" value="<?= (int)$pr['id'] ?>">
              <button class="btn btn--ghost btn--sm" type="submit">Xoá</button>
            </form>
          </div>
        </div>
      <?php endforeach; ?>

      <div style="display:flex;justify-content:space-between;gap:12px;padding:var(--sp-4);flex-wrap:wrap">
        <a class="btn btn--ghost btn--sm" href="<?= e(url('projects.php')) ?>">← Tiếp tục chọn dự án</a>
        <form method="post">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="clear">
          <button class="btn btn--ghost btn--sm" type="submit">Xoá toàn bộ</button>
        </form>
      </div>
    </div>

    <aside>
      <div class="glass buy-box reveal reveal--right">
        <h2 style="font-size:var(--fs-xl)">Tóm tắt đơn hàng</h2>

        <form method="post" style="margin:var(--sp-4) 0">
          <?= csrf_field() ?>
          <input type="hidden" name="action" value="coupon">
          <div class="field">
            <label for="code">Mã giảm giá</label>
            <div style="display:flex;gap:8px">
              <input type="text" id="code" name="code" value="<?= e($_SESSION['coupon'] ?? '') ?>" placeholder="VD: HIEUMINI10">
              <button class="btn btn--ghost btn--sm" type="submit">Áp dụng</button>
            </div>
            <span class="hint">Thử <strong>SINHVIEN20</strong> để giảm 20% cho đơn từ 500.000 ₫.</span>
          </div>
        </form>

        <div class="summary-line"><span>Tạm tính</span><span><?= money($cart['subtotal']) ?></span></div>
        <div class="summary-line">
          <span>Giảm giá<?= $cart['coupon'] ? ' (' . e($cart['coupon']['code']) . ')' : '' ?></span>
          <span style="color:var(--success)">- <?= money($cart['discount']) ?></span>
        </div>
        <div class="summary-line"><span>Phí bàn giao</span><span style="color:var(--success)">Miễn phí</span></div>
        <div class="summary-line summary-line--total"><span>Tổng cộng</span><span><?= money($cart['total']) ?></span></div>

        <a class="btn btn--primary btn--block btn--lg" style="margin-top:var(--sp-4)" href="<?= e(url('checkout.php')) ?>">
          Tiến hành thanh toán
        </a>
        <p class="demo-hint" style="margin-top:var(--sp-4);margin-bottom:0">
          Đây là hệ thống demo phục vụ đồ án: đơn hàng được ghi nhận vào cơ sở dữ liệu, không phát sinh thanh toán thật.
        </p>
      </div>
    </aside>
  </div>
<?php endif; ?>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
