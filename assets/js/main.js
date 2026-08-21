/* =====================================================================
   HieuMini — Lớp tương tác & hoạt ảnh
   Không phụ thuộc thư viện ngoài: chạy được cả khi máy không có Internet.
   Mọi hiệu ứng đều bị vô hiệu hóa khi người dùng bật "giảm chuyển động".
   ===================================================================== */
(function () {
  'use strict';

  var reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;

  /* -----------------------------------------------------------------
   | 1. CHẾ ĐỘ SÁNG / TỐI
   * ----------------------------------------------------------------- */
  function initTheme() {
    var root = document.documentElement;
    var btn = document.getElementById('themeToggle');

    function apply(theme) {
      root.setAttribute('data-theme', theme);
      try { localStorage.setItem('hieumini-theme', theme); } catch (e) {}
      if (btn) {
        btn.setAttribute('aria-label', theme === 'dark' ? 'Chuyển sang giao diện sáng' : 'Chuyển sang giao diện tối');
        btn.querySelectorAll('svg').forEach(function (svg, i) {
          svg.style.display = (theme === 'dark' ? i === 1 : i === 0) ? 'block' : 'none';
        });
      }
    }

    var saved = null;
    try { saved = localStorage.getItem('hieumini-theme'); } catch (e) {}
    apply(saved || (window.matchMedia('(prefers-color-scheme: dark)').matches ? 'dark' : 'light'));

    if (btn) {
      btn.addEventListener('click', function () {
        apply(root.getAttribute('data-theme') === 'dark' ? 'light' : 'dark');
      });
    }
  }

  /* -----------------------------------------------------------------
   | 2. HIỆU ỨNG MORPH CHO BANNER
   |    Sinh ngẫu nhiên các hình blob rồi nội suy từng điểm neo giữa
   |    hai hình liên tiếp bằng hàm easing, tạo chuyển động biến hình
   |    liên tục và mượt. Đường cong dùng Catmull-Rom quy đổi sang Bézier.
   * ----------------------------------------------------------------- */
  var POINTS = 8;          // số điểm neo của mỗi blob
  var MORPH_MS = 5200;     // thời lượng biến hình giữa hai trạng thái

  function makeBlob(radius, wobble) {
    var pts = [];
    for (var i = 0; i < POINTS; i++) {
      var angle = (Math.PI * 2 * i) / POINTS;
      var r = radius * (1 - wobble / 2 + Math.random() * wobble);
      pts.push([Math.cos(angle) * r, Math.sin(angle) * r]);
    }
    return pts;
  }

  /** Chuyển danh sách điểm thành đường cong khép kín mượt (Catmull-Rom → Bézier). */
  function toPath(pts, cx, cy) {
    var n = pts.length;
    var d = 'M' + (pts[0][0] + cx).toFixed(2) + ' ' + (pts[0][1] + cy).toFixed(2);
    for (var i = 0; i < n; i++) {
      var p0 = pts[(i - 1 + n) % n];
      var p1 = pts[i];
      var p2 = pts[(i + 1) % n];
      var p3 = pts[(i + 2) % n];
      var c1x = p1[0] + (p2[0] - p0[0]) / 6;
      var c1y = p1[1] + (p2[1] - p0[1]) / 6;
      var c2x = p2[0] - (p3[0] - p1[0]) / 6;
      var c2y = p2[1] - (p3[1] - p1[1]) / 6;
      d += 'C' + (c1x + cx).toFixed(2) + ' ' + (c1y + cy).toFixed(2) +
           ',' + (c2x + cx).toFixed(2) + ' ' + (c2y + cy).toFixed(2) +
           ',' + (p2[0] + cx).toFixed(2) + ' ' + (p2[1] + cy).toFixed(2);
    }
    return d + 'Z';
  }

  function easeInOut(t) {
    return t < 0.5 ? 4 * t * t * t : 1 - Math.pow(-2 * t + 2, 3) / 2;
  }

  function initMorph() {
    var shapes = Array.prototype.slice.call(document.querySelectorAll('[data-morph]'));
    if (!shapes.length) return;

    var blobs = shapes.map(function (el) {
      var radius = parseFloat(el.getAttribute('data-radius')) || 180;
      var wobble = parseFloat(el.getAttribute('data-wobble')) || 0.42;
      var cx = parseFloat(el.getAttribute('data-cx')) || 0;
      var cy = parseFloat(el.getAttribute('data-cy')) || 0;
      var from = makeBlob(radius, wobble);
      var to = makeBlob(radius, wobble);
      el.setAttribute('d', toPath(from, cx, cy));
      return { el: el, from: from, to: to, cx: cx, cy: cy, radius: radius, wobble: wobble,
               offset: Math.random() * MORPH_MS };
    });

    // Người dùng giảm chuyển động: vẽ một trạng thái tĩnh rồi dừng.
    if (reduceMotion) return;

    var start = performance.now();

    function frame(now) {
      blobs.forEach(function (b) {
        var t = ((now - start + b.offset) % MORPH_MS) / MORPH_MS;
        var k = easeInOut(t);
        var pts = b.from.map(function (p, i) {
          return [p[0] + (b.to[i][0] - p[0]) * k, p[1] + (b.to[i][1] - p[1]) * k];
        });
        b.el.setAttribute('d', toPath(pts, b.cx, b.cy));
        // Hoàn tất một chu kỳ → chốt hình đích và bốc hình mới.
        if (t < b.lastT) {
          b.from = b.to;
          b.to = makeBlob(b.radius, b.wobble);
        }
        b.lastT = t;
      });
      requestAnimationFrame(frame);
    }
    requestAnimationFrame(frame);
  }

  /* -----------------------------------------------------------------
   | 3. PARALLAX NHẸ THEO CHUỘT CHO BANNER
   * ----------------------------------------------------------------- */
  function initParallax() {
    var hero = document.querySelector('[data-parallax-root]');
    if (!hero || reduceMotion || window.matchMedia('(hover: none)').matches) return;

    var layers = Array.prototype.slice.call(hero.querySelectorAll('[data-depth]'));
    var tx = 0, ty = 0, cx = 0, cy = 0, raf = null;

    hero.addEventListener('pointermove', function (ev) {
      var rect = hero.getBoundingClientRect();
      tx = (ev.clientX - rect.left) / rect.width - 0.5;
      ty = (ev.clientY - rect.top) / rect.height - 0.5;
      if (!raf) raf = requestAnimationFrame(tick);
    });
    hero.addEventListener('pointerleave', function () { tx = 0; ty = 0; if (!raf) raf = requestAnimationFrame(tick); });

    function tick() {
      cx += (tx - cx) * 0.08;
      cy += (ty - cy) * 0.08;
      layers.forEach(function (el) {
        var d = parseFloat(el.getAttribute('data-depth')) || 10;
        el.style.transform = 'translate3d(' + (-cx * d).toFixed(2) + 'px,' + (-cy * d).toFixed(2) + 'px,0)';
      });
      raf = (Math.abs(tx - cx) > 0.001 || Math.abs(ty - cy) > 0.001) ? requestAnimationFrame(tick) : null;
    }
  }

  /* -----------------------------------------------------------------
   | 4. HIỆN DẦN KHI CUỘN (IntersectionObserver)
   * ----------------------------------------------------------------- */
  function initReveal() {
    var items = document.querySelectorAll('[data-reveal]');
    if (!items.length) return;

    if (reduceMotion || !('IntersectionObserver' in window)) {
      items.forEach(function (el) { el.classList.add('is-visible'); });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        entry.target.classList.add('is-visible');
        io.unobserve(entry.target);
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });

    items.forEach(function (el, i) {
      // Xếp tầng độ trễ trong cùng một nhóm cha để tạo hiệu ứng nối tiếp.
      var stagger = el.hasAttribute('data-reveal-delay')
        ? el.getAttribute('data-reveal-delay')
        : String((i % 4) * 70);
      el.style.setProperty('--delay', stagger + 'ms');
      io.observe(el);
    });
  }

  /* -----------------------------------------------------------------
   | 5. ĐẾM SỐ TĂNG DẦN
   * ----------------------------------------------------------------- */
  function initCounters() {
    var nodes = document.querySelectorAll('[data-count]');
    if (!nodes.length) return;

    if (reduceMotion || !('IntersectionObserver' in window)) {
      nodes.forEach(function (el) { el.textContent = el.getAttribute('data-count'); });
      return;
    }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        var el = entry.target;
        io.unobserve(el);
        var target = parseFloat(el.getAttribute('data-count')) || 0;
        var suffix = el.getAttribute('data-suffix') || '';
        var t0 = performance.now();
        (function step(now) {
          var p = Math.min((now - t0) / 1100, 1);
          var v = Math.round(target * (1 - Math.pow(1 - p, 3)));
          el.textContent = v.toLocaleString('vi-VN') + suffix;
          if (p < 1) requestAnimationFrame(step);
        })(t0);
      });
    }, { threshold: 0.5 });

    nodes.forEach(function (el) { io.observe(el); });
  }

  /* -----------------------------------------------------------------
   | 6. THANH TIẾN ĐỘ CUỘN + HEADER DÍNH + NÚT LÊN ĐẦU
   * ----------------------------------------------------------------- */
  function initScrollUI() {
    var bar = document.querySelector('.scroll-progress');
    var header = document.querySelector('.site-header');
    var toTop = document.querySelector('.to-top');
    var ticking = false;

    function update() {
      var y = window.scrollY;
      var max = document.documentElement.scrollHeight - window.innerHeight;
      if (bar) bar.style.transform = 'scaleX(' + (max > 0 ? y / max : 0) + ')';
      if (header) header.classList.toggle('is-stuck', y > 8);
      if (toTop) toTop.classList.toggle('is-shown', y > 700);
      ticking = false;
    }

    window.addEventListener('scroll', function () {
      if (!ticking) { ticking = true; requestAnimationFrame(update); }
    }, { passive: true });
    update();

    if (toTop) {
      toTop.addEventListener('click', function () {
        window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
      });
    }
  }

  /* -----------------------------------------------------------------
   | 7. MENU DI ĐỘNG
   * ----------------------------------------------------------------- */
  function initNav() {
    var toggle = document.querySelector('.nav-toggle');
    var nav = document.getElementById('primaryNav');
    if (!toggle || !nav) return;

    toggle.addEventListener('click', function () {
      var open = nav.classList.toggle('is-open');
      toggle.setAttribute('aria-expanded', String(open));
    });
    nav.addEventListener('click', function (ev) {
      if (ev.target.closest('a')) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
      }
    });
    document.addEventListener('keydown', function (ev) {
      if (ev.key === 'Escape' && nav.classList.contains('is-open')) {
        nav.classList.remove('is-open');
        toggle.setAttribute('aria-expanded', 'false');
        toggle.focus();
      }
    });
  }

  /* -----------------------------------------------------------------
   | 8. GỢN SÓNG KHI BẤM NÚT
   * ----------------------------------------------------------------- */
  function initRipple() {
    if (reduceMotion) return;
    document.addEventListener('pointerdown', function (ev) {
      var btn = ev.target.closest('.btn');
      if (!btn) return;
      var rect = btn.getBoundingClientRect();
      var size = Math.max(rect.width, rect.height);
      var span = document.createElement('span');
      span.className = 'ripple';
      span.style.width = span.style.height = size + 'px';
      span.style.left = (ev.clientX - rect.left - size / 2) + 'px';
      span.style.top = (ev.clientY - rect.top - size / 2) + 'px';
      btn.appendChild(span);
      setTimeout(function () { span.remove(); }, 600);
    });
  }

  /* -----------------------------------------------------------------
   | 9. NẠP KHUNG XEM TRỰC TIẾP THEO NHU CẦU (lazy iframe)
   |    Chỉ nạp khi thẻ dự án sắp lọt vào khung nhìn, tránh 6 iframe
   |    cùng tải một lúc làm chậm trang chủ.
   * ----------------------------------------------------------------- */
  function initLiveFrames() {
    var frames = Array.prototype.slice.call(document.querySelectorAll('[data-live-src]'));
    if (!frames.length) return;

    // Dấu hiệu cho thấy dự án con đang báo lỗi kết nối cơ sở dữ liệu
    var ERR_RE = /kết nối được cơ sở dữ liệu|cơ sở dữ liệu|SQLSTATE|Fatal error|Connection refused|database/i;

    function inspect(wrap, iframe) {
      // Cùng nguồn nên đọc được nội dung; nếu trang trống hoặc là trang lỗi
      // CSDL thì hiện thẻ thông báo gọn thay cho khung trắng.
      try {
        var doc = iframe.contentDocument || iframe.contentWindow.document;
        var text = (doc && doc.body ? doc.body.innerText : '').trim();
        if (text.length < 12 || (text.length < 400 && ERR_RE.test(text))) {
          wrap.classList.add('is-fallback');
          var fb = wrap.querySelector('.frame-fallback');
          if (fb) fb.hidden = false;
        }
      } catch (e) {
        // Khác nguồn (hiếm khi xảy ra) → giữ nguyên khung
      }
    }

    function load(wrap) {
      var iframe = wrap.querySelector('iframe');
      if (!iframe || iframe.src) return;
      iframe.addEventListener('load', function () {
        wrap.classList.add('is-ready');
        inspect(wrap, iframe);
      }, { once: true });
      // Nếu dự án con không phản hồi trong 8 giây thì vẫn gỡ lớp chờ.
      setTimeout(function () { wrap.classList.add('is-ready'); }, 8000);
      iframe.src = wrap.getAttribute('data-live-src');
    }

    if (!('IntersectionObserver' in window)) { frames.forEach(load); return; }

    var io = new IntersectionObserver(function (entries) {
      entries.forEach(function (entry) {
        if (!entry.isIntersecting) return;
        io.unobserve(entry.target);
        load(entry.target);
      });
    }, { rootMargin: '400px 0px' });

    frames.forEach(function (el) { io.observe(el); });
  }

  /* -----------------------------------------------------------------
   | 10. BỘ LỌC DANH MỤC TRÊN TRANG CHỦ
   * ----------------------------------------------------------------- */
  function initFilter() {
    var bar = document.querySelector('[data-filter-bar]');
    if (!bar) return;
    var cards = Array.prototype.slice.call(document.querySelectorAll('[data-category]'));
    var empty = document.querySelector('[data-filter-empty]');

    bar.addEventListener('click', function (ev) {
      var chip = ev.target.closest('.chip');
      if (!chip) return;

      bar.querySelectorAll('.chip').forEach(function (c) {
        c.classList.toggle('is-active', c === chip);
        c.setAttribute('aria-pressed', String(c === chip));
      });

      var want = chip.getAttribute('data-filter');
      var shown = 0;
      cards.forEach(function (card) {
        var match = want === 'all' || card.getAttribute('data-category') === want;
        card.classList.toggle('is-hidden', !match);
        if (match) shown++;
      });
      if (empty) empty.hidden = shown > 0;
    });
  }

  /* -----------------------------------------------------------------
   | 10b. KIỂM TRA KHUNG NHÚNG TRÊN TRANG CHI TIẾT
   |      Nếu dự án con trả về trang trống hoặc lỗi CSDL thì hiện thông
   |      báo hướng dẫn thay vì để khung trắng.
   * ----------------------------------------------------------------- */
  function initStageInspect() {
    var iframe = document.querySelector('[data-stage-frame]');
    var fallback = document.querySelector('[data-stage-fallback]');
    if (!iframe || !fallback) return;

    var ERR_RE = /kết nối được cơ sở dữ liệu|SQLSTATE|Fatal error|Connection refused|Uncaught|Parse error/i;

    function check() {
      try {
        var doc = iframe.contentDocument || iframe.contentWindow.document;
        var text = (doc && doc.body ? doc.body.innerText : '').trim();
        if (text.length < 12 || (text.length < 600 && ERR_RE.test(text))) {
          fallback.hidden = false;
          iframe.style.visibility = 'hidden';
        } else {
          fallback.hidden = true;
          iframe.style.visibility = '';
        }
      } catch (e) { /* khác nguồn: giữ nguyên */ }
    }

    iframe.addEventListener('load', check);
    // Kiểm tra lần đầu nếu iframe đã tải xong trước khi gắn sự kiện
    if (iframe.contentDocument && iframe.contentDocument.readyState === 'complete') check();
  }

  /* -----------------------------------------------------------------
   | 11. CHUYỂN KHUNG NHÌN MÁY TÍNH / MÁY TÍNH BẢNG / ĐIỆN THOẠI
   * ----------------------------------------------------------------- */
  function initViewportSwitch() {
    var group = document.querySelector('[data-viewport-switch]');
    var stage = document.querySelector('[data-stage-body]');
    if (!group || !stage) return;

    group.addEventListener('click', function (ev) {
      var btn = ev.target.closest('button');
      if (!btn) return;
      var mode = btn.getAttribute('data-viewport');
      group.querySelectorAll('button').forEach(function (b) {
        b.classList.toggle('is-active', b === btn);
        b.setAttribute('aria-pressed', String(b === btn));
      });
      stage.classList.remove('is-tablet', 'is-mobile');
      if (mode !== 'desktop') stage.classList.add('is-' + mode);
    });
  }

  /* -----------------------------------------------------------------
   | 12. CHUYỂN TRANG MƯỢT
   |     Ưu tiên View Transitions API; nếu không có thì làm mờ dần.
   * ----------------------------------------------------------------- */
  function initPageTransition() {
    if (reduceMotion || document.startViewTransition) return;

    document.addEventListener('click', function (ev) {
      var a = ev.target.closest('a');
      if (!a || ev.metaKey || ev.ctrlKey || ev.shiftKey || ev.button !== 0) return;
      if (a.target === '_blank' || a.hasAttribute('download')) return;

      var href = a.getAttribute('href') || '';
      if (!href || href.charAt(0) === '#' || /^(mailto:|tel:|javascript:)/i.test(href)) return;
      if (a.origin !== window.location.origin) return;

      ev.preventDefault();
      document.body.classList.add('is-leaving');
      setTimeout(function () { window.location.href = a.href; }, 200);
    });

    window.addEventListener('pageshow', function (ev) {
      if (ev.persisted) document.body.classList.remove('is-leaving');
    });
  }

  /* -----------------------------------------------------------------
   | 13. TỰ ẨN THÔNG BÁO CHỚP NHOÁNG
   * ----------------------------------------------------------------- */
  function initToasts() {
    document.querySelectorAll('.toast').forEach(function (el, i) {
      setTimeout(function () {
        el.style.transition = 'opacity .3s, transform .3s';
        el.style.opacity = '0';
        el.style.transform = 'translateX(24px)';
        setTimeout(function () { el.remove(); }, 320);
      }, 4200 + i * 400);
    });
  }

  /* -----------------------------------------------------------------
   | 14. SAO CHÉP TÀI KHOẢN DEMO
   * ----------------------------------------------------------------- */
  function initCopy() {
    document.addEventListener('click', function (ev) {
      var btn = ev.target.closest('[data-copy]');
      if (!btn) return;
      var text = btn.getAttribute('data-copy');

      var done = function () {
        btn.classList.add('is-copied');
        setTimeout(function () { btn.classList.remove('is-copied'); }, 1400);
      };

      if (navigator.clipboard && navigator.clipboard.writeText) {
        navigator.clipboard.writeText(text).then(done).catch(function () { fallback(text, done); });
      } else {
        fallback(text, done);
      }
    });

    function fallback(text, done) {
      var ta = document.createElement('textarea');
      ta.value = text;
      ta.style.position = 'fixed';
      ta.style.opacity = '0';
      document.body.appendChild(ta);
      ta.select();
      try { document.execCommand('copy'); done(); } catch (e) {}
      ta.remove();
    }
  }

  /* ----------------------------------------------------------------- */
  function boot() {
    initTheme();
    initMorph();
    initParallax();
    initReveal();
    initCounters();
    initScrollUI();
    initNav();
    initRipple();
    initLiveFrames();
    initFilter();
    initStageInspect();
    initViewportSwitch();
    initPageTransition();
    initToasts();
    initCopy();
  }

  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', boot);
  } else {
    boot();
  }
})();
