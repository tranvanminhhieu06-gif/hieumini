/* =====================================================================
   HieuMini Books — Lớp tương tác

   Thư viện ngoài (GSAP, ScrollTrigger, SplitText, Lenis) nằm trong
   assets/vendor/ nên trang chạy được cả khi máy không có Internet. Thiếu
   thư viện nào thì phần đó tự lui về cách viết tay — trang không bao giờ vỡ.

   Nguyên tắc xuyên suốt: hiệu ứng là lớp phủ thêm, không phải điều kiện
   để đọc được nội dung.
   ===================================================================== */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  var hasGSAP  = !!(window.gsap && window.ScrollTrigger);
  var hasSplit = !!(hasGSAP && window.SplitText);
  var hasLenis = !!window.Lenis;
  var lenis    = null;

  if (hasGSAP) window.gsap.registerPlugin(window.ScrollTrigger);
  if (hasSplit) window.gsap.registerPlugin(window.SplitText);

  /* -----------------------------------------------------------------
   | 1. GIAO DIỆN SÁNG / TỐI
   * ----------------------------------------------------------------- */
  function initTheme() {
    var root = document.documentElement;
    var btn  = document.getElementById('themeToggle');
    if (!btn) return;

    function apply(theme) {
      root.setAttribute('data-theme', theme);
      try { localStorage.setItem('hieumini-books-theme', theme); } catch (e) {}
      btn.setAttribute('aria-label', theme === 'dark' ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối');
      // Hai biểu tượng nằm sẵn trong nút; chỉ hiện cái ứng với hành động kế tiếp
      btn.querySelectorAll('svg').forEach(function (svg, i) {
        svg.style.display = (theme === 'dark' ? i === 1 : i === 0) ? 'block' : 'none';
      });
    }

    apply(root.getAttribute('data-theme') || 'dark');
    btn.addEventListener('click', function () {
      apply(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
    });
  }

  /* -----------------------------------------------------------------
   | 2. MENU TRÊN MÀN HÌNH HẸP
   * ----------------------------------------------------------------- */
  function initNav() {
    var btn = document.querySelector('.nav-toggle');
    var nav = document.getElementById('primaryNav');
    if (!btn || !nav) return;

    btn.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      btn.setAttribute('aria-expanded', open ? 'true' : 'false');
    });

    // Phím Esc đóng menu — người dùng bàn phím cần lối thoát khỏi mọi lớp phủ
    document.addEventListener('keydown', function (ev) {
      if (ev.key === 'Escape' && nav.classList.contains('is-open')) {
        nav.classList.remove('is-open');
        btn.setAttribute('aria-expanded', 'false');
        btn.focus();
      }
    });
  }

  /* -----------------------------------------------------------------
   | 3. CUỘN MƯỢT (Lenis)
   |
   | Lenis bọc lại thao tác cuộn của trình duyệt chứ không thay thế, nên
   | thanh cuộn, phím Home/End và liên kết neo vẫn hoạt động bình thường.
   * ----------------------------------------------------------------- */
  function initSmoothScroll() {
    if (!hasLenis || reduceMotion) return;

    lenis = new window.Lenis({
      duration: 1.05,
      easing: function (t) { return Math.min(1, 1.001 - Math.pow(2, -10 * t)); },
      smoothWheel: true
    });

    if (hasGSAP) {
      // Dồn Lenis và GSAP về chung một vòng lặp khung hình; để hai bộ đếm
      // thời gian chạy song song sẽ khiến hoạt ảnh theo cuộn giật nhẹ.
      lenis.on('scroll', window.ScrollTrigger.update);
      window.gsap.ticker.add(function (time) { lenis.raf(time * 1000); });
      window.gsap.ticker.lagSmoothing(0);
    } else {
      requestAnimationFrame(function loop(t) { lenis.raf(t); requestAnimationFrame(loop); });
    }

    document.addEventListener('click', function (ev) {
      var a = ev.target.closest('a[href*="#"]');
      if (!a || a.target === '_blank') return;

      var href = a.getAttribute('href') || '';
      var hash = href.indexOf('#') >= 0 ? href.slice(href.indexOf('#')) : '';
      if (hash.length < 2) return;
      if (href.charAt(0) !== '#' && (a.pathname !== window.location.pathname || a.host !== window.location.host)) return;

      var target = document.querySelector(hash);
      if (!target) return;
      ev.preventDefault();
      lenis.scrollTo(target, { offset: -90 });
      history.pushState(null, '', hash);
    });
  }

  /* -----------------------------------------------------------------
   | 4. HIỆN DẦN KHI CUỘN
   * ----------------------------------------------------------------- */
  function initReveal() {
    var items = document.querySelectorAll('[data-reveal]');
    if (!items.length) return;

    if (reduceMotion) {
      items.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }

    if (hasGSAP) {
      // batch gom các phần tử cùng lọt vào khung nhìn trong một khung hình
      // rồi chạy chung một stagger, nên độ trễ nối tiếp bám theo thứ tự
      // người xem thực sự nhìn thấy chứ không phải thứ tự cứng trong DOM.
      window.ScrollTrigger.batch(items, {
        start: 'top 88%',
        once: true,
        onEnter: function (batch) {
          window.gsap.to(batch, {
            opacity: 1, y: 0, duration: 0.6, ease: 'power2.out',
            stagger: 0.07, overwrite: true,
            onComplete: function () {
              batch.forEach(function (el) {
                el.classList.add('is-visible');
                window.gsap.set(el, { clearProps: 'opacity,transform' });
              });
            }
          });
        }
      });
      return;
    }

    if (!('IntersectionObserver' in window)) {
      items.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        io.unobserve(entry.target);
      });
    }, { threshold: 0.1, rootMargin: '0px 0px -8% 0px' });

    items.forEach(function (el, i) {
      el.style.setProperty('--delay', (i % 4) * 70 + 'ms');
      io.observe(el);
    });
  }

  /* -----------------------------------------------------------------
   | 5. TÁCH CHỮ CHO TIÊU ĐỀ (SplitText)
   |
   | Chỉ nhận phần tử gắn data-split. Tách theo TỪ chứ không theo ký tự:
   | tiếng Việt có dấu nằm trên nguyên âm, chẻ tới từng ký tự dễ làm dấu
   | rời khỏi chữ khi chuyển động, đọc rất khó chịu.
   * ----------------------------------------------------------------- */
  function initSplitHeadings() {
    if (reduceMotion || !hasSplit) return;

    var targets = document.querySelectorAll('[data-split]');
    if (!targets.length) return;

    // Chờ font hiển thị xong mới đo dòng: tách sớm hơn thì vị trí ngắt dòng
    // tính theo font dự phòng và sẽ lệch khi font thật vào.
    var ready = (document.fonts && document.fonts.ready) ? document.fonts.ready : Promise.resolve();

    ready.then(function () {
      Array.prototype.forEach.call(targets, function (el) {
        window.SplitText.create(el, {
          type: 'words,lines',
          mask: 'lines',     // bọc mỗi dòng trong khung cắt để chữ trồi lên từ dưới
          autoSplit: true,   // đổi cỡ màn hình thì tự tách lại theo dòng mới
          aria: 'auto',      // trình đọc màn hình vẫn đọc nguyên câu gốc
          onSplit: function (self) {
            return window.gsap.from(self.words, {
              yPercent: 115, opacity: 0, duration: 0.8,
              ease: 'power4.out', stagger: 0.03,
              scrollTrigger: { trigger: el, start: 'top 90%', once: true }
            });
          }
        });
      });
    });
  }

  /* -----------------------------------------------------------------
   | 6. THANH TIẾN ĐỘ CUỘN, HEADER DÍNH, NÚT LÊN ĐẦU
   * ----------------------------------------------------------------- */
  function initScrollUI() {
    var bar    = document.querySelector('.scroll-progress');
    var header = document.querySelector('.site-header');
    var toTop  = document.querySelector('.to-top');
    var ticking = false;

    function paint(y, progress) {
      if (bar) bar.style.transform = 'scaleX(' + progress + ')';
      if (header) header.classList.toggle('is-stuck', y > 8);
      if (toTop) toTop.classList.toggle('is-shown', y > 600);
      ticking = false;
    }

    function update() {
      var y = window.scrollY;
      var max = document.documentElement.scrollHeight - window.innerHeight;
      paint(y, max > 0 ? y / max : 0);
    }

    if (lenis) {
      // Lenis đã tính sẵn tiến trình, khỏi đo lại chiều cao tài liệu mỗi khung hình
      lenis.on('scroll', function (e) { paint(e.scroll, e.progress || 0); });
    } else {
      window.addEventListener('scroll', function () {
        if (!ticking) { ticking = true; requestAnimationFrame(update); }
      }, { passive: true });
    }
    update();

    if (toTop) {
      toTop.addEventListener('click', function () {
        if (lenis) lenis.scrollTo(0);
        else window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
      });
    }
  }

  /* -----------------------------------------------------------------
   | 7. GỢI Ý TÌM KIẾM
   |
   | Chờ 220ms sau phím cuối rồi mới gọi máy chủ. Gọi ngay mỗi phím sẽ bắn
   | hàng chục truy vấn cho một lần gõ, phần lớn bị bỏ đi ngay sau đó.
   * ----------------------------------------------------------------- */
  function initSuggest() {
    var input = document.getElementById('q');
    var box   = document.getElementById('suggestBox');
    if (!input || !box) return;

    var timer = null, controller = null, items = [], cursor = -1;

    function close() {
      box.hidden = true;
      input.setAttribute('aria-expanded', 'false');
      cursor = -1;
    }

    function render(data) {
      items = data.items || [];
      if (!items.length) {
        box.innerHTML = '<div class="suggest-empty">Không có gợi ý nào cho “'
          + escapeHtml(data.query) + '”. Nhấn Enter để tìm đầy đủ.</div>';
      } else {
        box.innerHTML = items.map(function (it, i) {
          return '<a role="option" id="sg-' + i + '" aria-selected="false" href="' + it.url + '">'
            + '<img src="' + it.cover + '" alt="" width="30" height="45" loading="lazy">'
            + '<span>' + mark(it.title, data.query) + '<br>'
            + '<span style="color:var(--fg-subtle);font-size:var(--fs-xs)">'
            + escapeHtml(it.author) + (it.year ? ' · ' + it.year : '') + '</span></span></a>';
        }).join('');
      }
      box.hidden = false;
      input.setAttribute('aria-expanded', 'true');
      cursor = -1;
    }

    function escapeHtml(s) {
      return String(s).replace(/[&<>"']/g, function (c) {
        return { '&': '&amp;', '<': '&lt;', '>': '&gt;', '"': '&quot;', "'": '&#39;' }[c];
      });
    }

    function mark(text, q) {
      var i = text.toLowerCase().indexOf(String(q).toLowerCase());
      if (i < 0) return escapeHtml(text);
      return escapeHtml(text.slice(0, i)) + '<em>' + escapeHtml(text.slice(i, i + q.length))
        + '</em>' + escapeHtml(text.slice(i + q.length));
    }

    function move(step) {
      var links = box.querySelectorAll('a');
      if (!links.length) return;
      if (cursor >= 0) links[cursor].classList.remove('is-active');
      cursor = (cursor + step + links.length) % links.length;
      links[cursor].classList.add('is-active');
      links[cursor].setAttribute('aria-selected', 'true');
      input.setAttribute('aria-activedescendant', links[cursor].id);
    }

    input.addEventListener('input', function () {
      var q = input.value.trim();
      clearTimeout(timer);
      if (q.length < 2) { close(); return; }

      timer = setTimeout(function () {
        // Huỷ yêu cầu cũ để kết quả của lần gõ trước không ghi đè lần sau
        if (controller) controller.abort();
        controller = new AbortController();

        fetch('api/suggest.php?q=' + encodeURIComponent(q), { signal: controller.signal })
          .then(function (r) { return r.ok ? r.json() : Promise.reject(r.status); })
          .then(render)
          .catch(function (err) { if (err && err.name !== 'AbortError') close(); });
      }, 220);
    });

    input.addEventListener('keydown', function (ev) {
      if (box.hidden) return;
      if (ev.key === 'ArrowDown')      { ev.preventDefault(); move(1); }
      else if (ev.key === 'ArrowUp')   { ev.preventDefault(); move(-1); }
      else if (ev.key === 'Enter' && cursor >= 0) {
        ev.preventDefault();
        box.querySelectorAll('a')[cursor].click();
      } else if (ev.key === 'Escape')  { close(); }
    });

    document.addEventListener('click', function (ev) {
      if (!ev.target.closest('.search-box')) close();
    });
  }

  /* ----------------------------------------------------------------- */
  function boot() {
    initTheme();
    initNav();
    initSmoothScroll();
    initReveal();
    initSplitHeadings();
    initScrollUI();
    initSuggest();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
