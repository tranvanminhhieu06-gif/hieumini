</main>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div>
        <a class="brand" href="<?= e(url('index.php')) ?>">
          <span class="brand-mark" aria-hidden="true">H</span>
          <span><?= e(SITE_NAME) ?></span>
        </a>
        <p style="margin-top:16px;max-width:42ch">
          <?= e(SITE_TAGLINE) ?>. Toàn bộ dự án đều chạy trực tiếp trên máy chủ nội bộ
          và có thể thao tác ngay trong trình duyệt.
        </p>
      </div>

      <div>
        <h4>Khám phá</h4>
        <ul class="footer-links">
          <li><a href="<?= e(url('index.php')) ?>">Trang chủ</a></li>
          <li><a href="<?= e(url('index.php#projects')) ?>">Danh sách dự án</a></li>
          <li><a href="<?= e(url('about.php')) ?>">Giới thiệu</a></li>
          <li><a href="<?= e(url('contact.php')) ?>">Liên hệ</a></li>
        </ul>
      </div>

      <div>
        <h4>Tài liệu</h4>
        <ul class="footer-links">
          <li><a href="<?= e(url('README.md')) ?>">Hướng dẫn cài đặt</a></li>
          <li><a href="<?= e(url('BaoCao.docx')) ?>">Báo cáo đồ án (.docx)</a></li>
          <li><a href="mailto:<?= e(SITE_EMAIL) ?>"><?= e(SITE_EMAIL) ?></a></li>
        </ul>
      </div>
    </div>

    <div class="footer-bottom">
      <span>© <?= date('Y') ?> <?= e(SITE_NAME) ?> — <?= e(SITE_AUTHOR) ?>.</span>
      <span>Xây dựng bằng PHP <?= e(PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION) ?> &amp; MySQL, không dùng framework.</span>
    </div>
  </div>
</footer>

<button type="button" class="to-top" aria-label="Cuộn lên đầu trang"><?= icon('arrow') ?></button>

<?php /* Thư viện hoạt ảnh — để trong assets/vendor/ nên vẫn chạy khi mất mạng.
         defer giữ đúng thứ tự thực thi: GSAP → ScrollTrigger → Lenis → main.js */ ?>
<script src="<?= e(asset('vendor/gsap.min.js')) ?>" defer></script>
<script src="<?= e(asset('vendor/ScrollTrigger.min.js')) ?>" defer></script>
<script src="<?= e(asset('vendor/lenis.min.js')) ?>" defer></script>
<script src="<?= e(asset('js/main.js')) ?>?v=<?= filemtime(__DIR__ . '/../assets/js/main.js') ?: '2' ?>" defer></script>
</body>
</html>
