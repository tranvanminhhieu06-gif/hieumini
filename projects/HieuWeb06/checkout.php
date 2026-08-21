<?php
/**
 * HieuMini - Thanh toán và tạo đơn hàng
 */
require_once __DIR__ . '/includes/config.php';

$cart = cart_detail($pdo);
if (!$cart['rows']) {
    flash('Giỏ hàng đang trống.', 'warning');
    redirect('cart.php');
}

$errors = [];
$form = [
    'customer_name' => is_logged_in() ? current_user_name() : '',
    'email'         => (string)($_SESSION['user_email'] ?? ''),
    'phone'         => '',
    'note'          => '',
    'payment'       => 'bank',
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    csrf_guard();

    $form['customer_name'] = input('customer_name');
    $form['email']         = input('email');
    $form['phone']         = input('phone');
    $form['note']          = mb_substr(input('note'), 0, 500, 'UTF-8');
    $form['payment']       = in_array($_POST['payment'] ?? '', ['bank', 'momo', 'cod'], true) ? $_POST['payment'] : 'bank';

    if ($form['customer_name'] === '') {
        $errors['customer_name'] = 'Vui lòng nhập họ tên.';
    }
    if (!filter_var($form['email'], FILTER_VALIDATE_EMAIL)) {
        $errors['email'] = 'Địa chỉ email chưa đúng định dạng.';
    }
    if (!preg_match('/^0\d{9}$/', preg_replace('/\s+/', '', $form['phone']) ?? '')) {
        $errors['phone'] = 'Số điện thoại phải gồm 10 chữ số và bắt đầu bằng 0.';
    }
    if (empty($_POST['agree'])) {
        $errors['agree'] = 'Bạn cần đồng ý với điều khoản sử dụng.';
    }

    if (!$errors) {
        try {
            $pdo->beginTransaction();

            $code = generate_order_code($pdo);
            $stmt = $pdo->prepare('INSERT INTO orders
                (order_code, user_id, customer_name, email, phone, note, payment_method, coupon_code, subtotal, discount, total, status)
                VALUES (?,?,?,?,?,?,?,?,?,?,?, "pending")');
            $stmt->execute([
                $code,
                is_logged_in() ? current_user_id() : null,
                $form['customer_name'], $form['email'], $form['phone'], $form['note'],
                $form['payment'], $cart['coupon']['code'] ?? null,
                $cart['subtotal'], $cart['discount'], $cart['total'],
            ]);
            $orderId = (int)$pdo->lastInsertId();

            $itemStmt = $pdo->prepare('INSERT INTO order_items (order_id, project_id, title, license, price, quantity)
                                       VALUES (?,?,?,?,?,1)');
            $salesStmt = $pdo->prepare('UPDATE projects SET sales = sales + 1 WHERE id = ?');
            foreach ($cart['rows'] as $row) {
                $itemStmt->execute([
                    $orderId, $row['project']['id'], $row['project']['title'],
                    $row['license'], $row['line_total'],
                ]);
                $salesStmt->execute([$row['project']['id']]);
            }

            if ($cart['coupon']) {
                $pdo->prepare('UPDATE coupons SET used_count = used_count + 1 WHERE id = ?')
                    ->execute([$cart['coupon']['id']]);
            }

            $pdo->commit();
            cart_clear();
            $_SESSION['last_order'] = $code;
            redirect('order-success.php?code=' . $code);
        } catch (PDOException $e) {
            $pdo->rollBack();
            $errors['general'] = 'Không tạo được đơn hàng. Vui lòng thử lại.' . (DEBUG_MODE ? ' (' . $e->getMessage() . ')' : '');
        }
    }
}

seo([
    'title'       => 'Thanh toán đơn hàng | ' . SITE_NAME,
    'description' => 'Hoàn tất thông tin nhận bàn giao mã nguồn website.',
    'robots'      => 'noindex, nofollow',
]);

require __DIR__ . '/includes/header.php';
?>

<div class="container page-head">
  <?= breadcrumb(['Trang chủ' => 'index.php', 'Giỏ hàng' => 'cart.php', 'Thanh toán' => null]) ?>
  <h1 style="margin-top:var(--sp-4)">Thanh toán</h1>
  <p>Điền thông tin nhận bàn giao. HieuMini sẽ liên hệ xác nhận trong vòng 30 phút làm việc.</p>
</div>

<div class="container section--tight">
  <div class="detail-grid">

    <form class="glass reveal" method="post" style="padding:var(--sp-6)" data-validate novalidate>
      <?= csrf_field() ?>

      <?php if (!empty($errors['general'])): ?>
        <div class="flash flash--error" style="position:static;margin-bottom:var(--sp-4)"><?= e($errors['general']) ?></div>
      <?php endif; ?>

      <h2 style="font-size:var(--fs-xl)">Thông tin người mua</h2>
      <div class="form-grid">
        <div class="field">
          <label for="customer_name">Họ và tên <span aria-hidden="true">*</span></label>
          <input type="text" id="customer_name" name="customer_name" required autocomplete="name"
                 value="<?= e($form['customer_name']) ?>" aria-invalid="<?= isset($errors['customer_name']) ? 'true' : 'false' ?>">
          <?php if (isset($errors['customer_name'])): ?><span class="error"><?= e($errors['customer_name']) ?></span><?php endif; ?>
        </div>

        <div class="grid grid--2">
          <div class="field">
            <label for="email">Email nhận mã nguồn <span aria-hidden="true">*</span></label>
            <input type="email" id="email" name="email" required autocomplete="email"
                   value="<?= e($form['email']) ?>" aria-invalid="<?= isset($errors['email']) ? 'true' : 'false' ?>">
            <?php if (isset($errors['email'])): ?><span class="error"><?= e($errors['email']) ?></span><?php endif; ?>
          </div>
          <div class="field">
            <label for="phone">Số điện thoại <span aria-hidden="true">*</span></label>
            <input type="tel" id="phone" name="phone" required autocomplete="tel" inputmode="numeric"
                   value="<?= e($form['phone']) ?>" aria-invalid="<?= isset($errors['phone']) ? 'true' : 'false' ?>">
            <?php if (isset($errors['phone'])): ?><span class="error"><?= e($errors['phone']) ?></span>
            <?php else: ?><span class="hint">Dùng để liên hệ qua Zalo khi bàn giao.</span><?php endif; ?>
          </div>
        </div>

        <div class="field">
          <label for="note">Ghi chú thêm</label>
          <textarea id="note" name="note" maxlength="500" placeholder="Yêu cầu đổi màu thương hiệu, đổi logo, hỗ trợ cài lên hosting…"><?= e($form['note']) ?></textarea>
        </div>
      </div>

      <h2 style="font-size:var(--fs-xl);margin-top:var(--sp-6)">Phương thức thanh toán</h2>
      <fieldset style="border:0;padding:0;margin:0">
        <legend class="sr-only">Chọn phương thức thanh toán</legend>
        <label class="license-option">
          <input type="radio" name="payment" value="bank" <?= $form['payment'] === 'bank' ? 'checked' : '' ?>>
          <span><span><strong>Chuyển khoản ngân hàng</strong><br><small style="color:var(--fg-muted)"><?= e(setting('bank_info')) ?></small></span></span>
        </label>
        <label class="license-option">
          <input type="radio" name="payment" value="momo" <?= $form['payment'] === 'momo' ? 'checked' : '' ?>>
          <span><span><strong>Ví MoMo</strong><br><small style="color:var(--fg-muted)">Quét mã QR gửi kèm email xác nhận</small></span></span>
        </label>
        <label class="license-option">
          <input type="radio" name="payment" value="cod" <?= $form['payment'] === 'cod' ? 'checked' : '' ?>>
          <span><span><strong>Thanh toán khi nhận bàn giao</strong><br><small style="color:var(--fg-muted)">Áp dụng cho khách đã mua từ lần thứ hai</small></span></span>
        </label>
      </fieldset>

      <label class="checkbox" style="margin-top:var(--sp-4)">
        <input type="checkbox" name="agree" value="1" required>
        <span>Tôi đã đọc và đồng ý với <a href="<?= e(url('about.php#license')) ?>">điều khoản giấy phép sử dụng</a> của HieuMini.</span>
      </label>
      <?php if (isset($errors['agree'])): ?><span class="error"><?= e($errors['agree']) ?></span><?php endif; ?>

      <button class="btn btn--primary btn--block btn--lg" type="submit" style="margin-top:var(--sp-5)">
        Đặt hàng · <?= money($cart['total']) ?>
      </button>
    </form>

    <aside>
      <div class="glass buy-box reveal reveal--right">
        <h2 style="font-size:var(--fs-xl)">Đơn hàng của bạn</h2>
        <?php foreach ($cart['rows'] as $row): ?>
          <div class="summary-line" style="align-items:flex-start">
            <span>
              <?= e(excerpt($row['project']['title'], 40)) ?><br>
              <small style="color:var(--fg-muted)"><?= e(license_label($row['license'])) ?></small>
            </span>
            <span><?= money($row['line_total']) ?></span>
          </div>
        <?php endforeach; ?>
        <div class="summary-line"><span>Tạm tính</span><span><?= money($cart['subtotal']) ?></span></div>
        <?php if ($cart['discount'] > 0): ?>
          <div class="summary-line"><span>Giảm giá</span><span style="color:var(--success)">- <?= money($cart['discount']) ?></span></div>
        <?php endif; ?>
        <div class="summary-line summary-line--total"><span>Tổng cộng</span><span><?= money($cart['total']) ?></span></div>
        <a class="btn btn--ghost btn--block btn--sm" style="margin-top:var(--sp-4)" href="<?= e(url('cart.php')) ?>">Sửa giỏ hàng</a>
      </div>
    </aside>
  </div>
</div>

<?php require __DIR__ . '/includes/footer.php'; ?>
