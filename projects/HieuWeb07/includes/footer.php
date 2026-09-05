</main>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <a class="brand" href="<?= e(url('index.php')) ?>">HieuMini<span>.</span></a>
        <p style="margin-top:var(--space-4);max-width:44ch">
          <?= e(SITE_TAGLINE) ?> — tra cứu <?= format_number(site_stats()['books']) ?> đầu sách theo
          thể loại và tác giả, kèm đánh giá của bạn đọc. Đồ án môn Lập trình phát triển ứng dụng Web.
        </p>
      </div>

      <div>
        <h4>Khám phá</h4>
        <ul class="footer-links">
          <li><a href="<?= e(url('books.php')) ?>">Toàn bộ kho sách</a></li>
          <li><a href="<?= e(url('authors.php')) ?>">Danh sách tác giả</a></li>
          <?php foreach (array_slice(all_categories(), 0, 3) as $c): ?>
            <li><a href="<?= e(url('books.php?the-loai=' . $c['slug'])) ?>"><?= e($c['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </div>

      <div>
        <h4>Thông tin</h4>
        <ul class="footer-links">
          <li><a href="<?= e(url('about.php')) ?>">Về dự án</a></li>
          <li><a href="<?= e(url('contact.php')) ?>">Liên hệ</a></li>
          <li><a href="<?= e(url('sitemap.xml')) ?>">Sơ đồ trang</a></li>
          <li><a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <span>© <?= date('Y') ?> <?= e(SITE_NAME) ?> — <?= e(SITE_AUTHOR) ?>.</span>
      <span>PHP <?= e(PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION) ?> &amp; MySQL, không dùng framework.</span>
    </div>
  </div>
</footer>

<button type="button" class="to-top" aria-label="Cuộn lên đầu trang"><?= icon('arrow') ?></button>

<?php /* Thư viện hoạt ảnh đặt trong assets/vendor/ nên trang vẫn chạy khi mất mạng.
         defer giữ đúng thứ tự: GSAP → ScrollTrigger → SplitText → Lenis → main.js */ ?>
<script src="<?= e(asset('vendor/gsap.min.js')) ?>" defer></script>
<script src="<?= e(asset('vendor/ScrollTrigger.min.js')) ?>" defer></script>
<script src="<?= e(asset('vendor/SplitText.min.js')) ?>" defer></script>
<script src="<?= e(asset('vendor/lenis.min.js')) ?>" defer></script>
<script src="<?= e(asset('js/main.js')) ?>" defer></script>
</body>
</html>
