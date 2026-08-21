</main>

<footer class="site-footer">
  <div class="container">
    <div class="footer-grid">
      <div class="footer-brand reveal">
        <a class="brand" href="<?= e(url('index.php')) ?>">
          <svg class="brand__mark" viewBox="0 0 40 40" aria-hidden="true" focusable="false">
            <defs><linearGradient id="bg2" x1="0" y1="0" x2="1" y2="1">
              <stop offset="0" stop-color="#7C3AED"/><stop offset="1" stop-color="#22D3EE"/>
            </linearGradient></defs>
            <rect x="1.5" y="1.5" width="37" height="37" rx="11" fill="url(#bg2)" opacity=".18"/>
            <rect x="1.5" y="1.5" width="37" height="37" rx="11" fill="none" stroke="url(#bg2)" stroke-width="2"/>
            <path d="M12 28V12h3.6v6.2h8.8V12H28v16h-3.6v-6.4h-8.8V28z" fill="url(#bg2)"/>
          </svg>
          <span class="brand__text"><span class="brand__hieu">Hieu</span><span class="brand__mini">Mini</span></span>
        </a>
        <p><?= e(setting('site_description')) ?></p>
        <ul class="social">
          <?php if (setting('facebook')): ?>
          <li><a href="<?= e(setting('facebook')) ?>" rel="noopener nofollow" target="_blank" aria-label="Facebook">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M14 9h3V6h-3c-2.2 0-4 1.8-4 4v2H8v3h2v7h3v-7h3l1-3h-4v-2c0-.6.4-1 1-1z"/></svg></a></li>
          <?php endif; ?>
          <?php if (setting('youtube')): ?>
          <li><a href="<?= e(setting('youtube')) ?>" rel="noopener nofollow" target="_blank" aria-label="YouTube">
            <svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="6" width="18" height="12" rx="4"/><path d="M11 10l4 2-4 2z"/></svg></a></li>
          <?php endif; ?>
          <?php if (setting('github')): ?>
          <li><a href="<?= e(setting('github')) ?>" rel="noopener nofollow" target="_blank" aria-label="GitHub">
            <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 3a9 9 0 0 0-2.8 17.5c.4.1.6-.2.6-.5v-1.7c-2.5.5-3-1.2-3-1.2-.4-1-1-1.3-1-1.3-.9-.6 0-.6 0-.6 1 .1 1.5 1 1.5 1 .9 1.5 2.3 1.1 2.8.8.1-.6.3-1.1.6-1.3-2-.2-4.1-1-4.1-4.4 0-1 .3-1.8.9-2.4-.1-.2-.4-1.1.1-2.3 0 0 .8-.2 2.5 1a8.6 8.6 0 0 1 4.5 0c1.7-1.2 2.5-1 2.5-1 .5 1.2.2 2.1.1 2.3.6.6.9 1.4.9 2.4 0 3.4-2.1 4.2-4.1 4.4.3.3.6.9.6 1.8v2.6c0 .3.2.6.6.5A9 9 0 0 0 12 3z"/></svg></a></li>
          <?php endif; ?>
        </ul>
      </div>

      <nav class="footer-col reveal" aria-labelledby="ft-cat">
        <h3 id="ft-cat">Danh mục dự án</h3>
        <ul>
          <?php foreach (get_categories($pdo) as $cat): ?>
            <li><a href="<?= e(url('projects.php?cat=' . $cat['slug'])) ?>"><?= e($cat['name']) ?></a></li>
          <?php endforeach; ?>
        </ul>
      </nav>

      <nav class="footer-col reveal" aria-labelledby="ft-help">
        <h3 id="ft-help">Hỗ trợ khách hàng</h3>
        <ul>
          <li><a href="<?= e(url('about.php')) ?>">Về HieuMini</a></li>
          <li><a href="<?= e(url('about.php#license')) ?>">Giấy phép sử dụng</a></li>
          <li><a href="<?= e(url('about.php#faq')) ?>">Câu hỏi thường gặp</a></li>
          <li><a href="<?= e(url('blog.php')) ?>">Blog kiến thức</a></li>
          <li><a href="<?= e(url('contact.php')) ?>">Liên hệ &amp; báo giá</a></li>
          <li><a href="<?= e(url('sitemap.php')) ?>">Sơ đồ website</a></li>
        </ul>
      </nav>

      <div class="footer-col reveal">
        <h3>Liên hệ</h3>
        <ul class="contact-list">
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 21s-7-5.6-7-10a7 7 0 1 1 14 0c0 4.4-7 10-7 10z"/><circle cx="12" cy="11" r="2.5"/></svg><span><?= e(setting('contact_address')) ?></span></li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><path d="M5 4h4l2 5-2.5 1.5a11 11 0 0 0 5 5L15 13l5 2v4a1 1 0 0 1-1 1A16 16 0 0 1 4 5a1 1 0 0 1 1-1z"/></svg><a href="tel:<?= e(preg_replace('/\s+/', '', setting('contact_phone'))) ?>"><?= e(setting('contact_phone')) ?></a></li>
          <li><svg viewBox="0 0 24 24" aria-hidden="true"><rect x="3" y="5" width="18" height="14" rx="3"/><path d="M4 7l8 5 8-5"/></svg><a href="mailto:<?= e(setting('contact_email')) ?>"><?= e(setting('contact_email')) ?></a></li>
        </ul>
        <p class="footer-note">Thanh toán: <?= e(setting('bank_info')) ?></p>
      </div>
    </div>

    <div class="footer-bottom">
      <p>© <?= date('Y') ?> <?= e(SITE_NAME) ?>. Đồ án môn học Lập trình Web - Trần Văn Minh Hiếu.</p>
      <p>Xây dựng bằng PHP <?= PHP_MAJOR_VERSION . '.' . PHP_MINOR_VERSION ?> &amp; MySQL - Không dùng framework.</p>
    </div>
  </div>
</footer>

<button class="to-top" id="toTop" aria-label="Lên đầu trang" hidden>
  <svg viewBox="0 0 24 24" aria-hidden="true"><path d="M12 19V6M6 12l6-6 6 6"/></svg>
</button>

<script src="<?= e(asset('assets/js/main.js')) ?>" defer></script>
</body>
</html>
