<?php
// includes/header.php
require_once __DIR__ . '/../config/app.php';
$page_title = isset($custom_page_title) ? $custom_page_title . ' - ' . SITE_NAME : SITE_NAME . ' - ' . SITE_TAGLINE;
$cart_count = get_cart_count();
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="vi">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= htmlspecialchars($page_title) ?></title>
  <meta name="description" content="HieuMini - Cửa hàng văn phòng phẩm và đồ dùng học tập chất lượng cao: Bút viết pastel, sổ còng binder, dụng cụ vẽ mỹ thuật, cặp tài liệu và phụ kiện học sinh giá tốt nhất.">
  <link rel="icon" type="image/png" href="assets/images/favicon.png">
  <!-- Preconnect & Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Be+Vietnam+Pro:ital,wght@0,300;0,400;0,500;0,600;0,700;0,800;1,400&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

  <!-- Core Stylesheet -->
  <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<!-- Flash Notification Toast -->
<?php $flash = get_flash(); if ($flash): ?>
<div class="toast-container">
  <div class="toast-msg <?= $flash['type'] ?>">
    <i class="bi <?= $flash['type'] === 'success' ? 'bi-check-circle-fill' : 'bi-info-circle-fill' ?>" style="font-size: 1.3rem;"></i>
    <div style="font-weight: 600; font-size: 0.95rem;"><?= htmlspecialchars($flash['message']) ?></div>
  </div>
</div>
<?php endif; ?>
