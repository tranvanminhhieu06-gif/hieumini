<?php
/**
 * Thẻ hiển thị một dự án.
 * Cần biến $p (mảng dữ liệu dự án) trước khi include.
 */
$__fav = in_wishlist($pdo, current_user_id(), (int)$p['id']);
$__off = discount_percent($p);
?>
<article class="card project-card reveal">
  <div class="project-card__media">
    <div class="project-card__flags">
      <?php if (!empty($p['badge'])): ?>
        <?php
          $b = strtoupper((string)$p['badge']);
          $cls = $b === 'HOT' ? 'badge--hot' : ($b === 'NEW' ? 'badge--new' : 'badge--best');
        ?>
        <span class="badge <?= $cls ?>"><?= e($p['badge']) ?></span>
      <?php endif; ?>
      <?php if ($__off > 0): ?>
        <span class="badge badge--sale">-<?= $__off ?>%</span>
      <?php endif; ?>
    </div>

    <button class="project-card__fav <?= $__fav ? 'is-active' : '' ?>"
            data-wish="<?= (int)$p['id'] ?>" data-csrf="<?= e(csrf_token()) ?>"
            aria-pressed="<?= $__fav ? 'true' : 'false' ?>"
            aria-label="<?= $__fav ? 'Bỏ khỏi' : 'Thêm vào' ?> danh sách yêu thích: <?= e($p['title']) ?>">
      <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 20s-7-4.4-7-9.2A4 4 0 0 1 12 8a4 4 0 0 1 7 2.8C19 15.6 12 20 12 20z"/></svg>
    </button>

    <a href="<?= e(url('project-detail.php?slug=' . $p['slug'])) ?>" tabindex="-1" aria-hidden="true">
      <img src="<?= e(asset($p['thumbnail'] ?: 'assets/images/og-cover.svg')) ?>"
           alt="Ảnh minh hoạ dự án <?= e($p['title']) ?>"
           width="800" height="500" loading="lazy" decoding="async">
    </a>

    <div class="project-card__overlay">
      <a class="btn btn--primary btn--sm" href="<?= e(url('project-detail.php?slug=' . $p['slug'])) ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M2 12s3.6-7 10-7 10 7 10 7-3.6 7-10 7-10-7-10-7z"/><circle cx="12" cy="12" r="3"/></svg>
        Xem chi tiết
      </a>
    </div>
  </div>

  <div class="project-card__body">
    <span class="project-card__cat"><?= e($p['category_name'] ?? '') ?></span>
    <h3 class="project-card__title">
      <a href="<?= e(url('project-detail.php?slug=' . $p['slug'])) ?>"><?= e($p['title']) ?></a>
    </h3>
    <p class="project-card__desc"><?= e(excerpt($p['short_desc'], 96)) ?></p>

    <div class="tech-list">
      <?php foreach (array_slice(array_filter(array_map('trim', explode(',', (string)$p['tech_stack']))), 0, 3) as $tech): ?>
        <span><?= e($tech) ?></span>
      <?php endforeach; ?>
    </div>

    <div class="project-card__meta">
      <span><?= stars((float)$p['rating_avg'], (int)$p['rating_count']) ?></span>
      <span><?= num((int)$p['sales']) ?> lượt mua</span>
    </div>

    <div class="project-card__foot">
      <div class="price">
        <span class="price__now"><?= money(final_price($p)) ?></span>
        <?php if ($__off > 0): ?><span class="price__old"><?= money($p['price']) ?></span><?php endif; ?>
      </div>
      <button class="btn btn--ghost btn--sm" data-cart-add="<?= (int)$p['id'] ?>" data-csrf="<?= e(csrf_token()) ?>">
        <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M4 5h2l2.2 9.4a2 2 0 0 0 2 1.6h6.2a2 2 0 0 0 2-1.5L20 8H7"/><circle cx="10" cy="19" r="1.4"/><circle cx="17" cy="19" r="1.4"/></svg>
        Thêm giỏ
      </button>
    </div>
  </div>
</article>
