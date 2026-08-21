/* ===================================================================
   HIEUMINI - Tương tác & hiệu ứng
   Nguyên tắc:
   - Không chặn hiển thị nội dung: mọi nội dung vẫn đọc được khi tắt JS.
   - Chỉ hoạt ảnh transform/opacity để giữ 60 khung hình mỗi giây.
   - Tôn trọng tuỳ chọn "giảm chuyển động" của hệ điều hành.
   ================================================================ */
(function () {
  'use strict';

  const reduceMotion = window.matchMedia('(prefers-reduced-motion: reduce)').matches;
  const $  = (sel, ctx = document) => ctx.querySelector(sel);
  const $$ = (sel, ctx = document) => Array.from(ctx.querySelectorAll(sel));

  /* ---------- 1. Thanh tiến trình cuộn + Header dính ---------- */
  const bar    = $('#scrollBar');
  const header = $('#siteHeader');
  const toTop  = $('#toTop');
  let ticking  = false;

  function onScroll() {
    const y   = window.scrollY;
    const max = document.documentElement.scrollHeight - window.innerHeight;
    if (bar)    bar.style.width = (max > 0 ? (y / max) * 100 : 0) + '%';
    if (header) header.classList.toggle('is-stuck', y > 12);
    if (toTop)  toTop.hidden = y < 480;
    ticking = false;
  }
  window.addEventListener('scroll', () => {
    if (!ticking) { window.requestAnimationFrame(onScroll); ticking = true; }
  }, { passive: true });
  onScroll();

  if (toTop) {
    toTop.addEventListener('click', () => {
      window.scrollTo({ top: 0, behavior: reduceMotion ? 'auto' : 'smooth' });
    });
  }

  /* ---------- 1b. Chuyển giao diện sáng / tối ----------
     Giao diện đã được đặt sẵn bằng đoạn script nội tuyến trong <head>
     để tránh nháy màu. Phần này chỉ xử lý thao tác bấm nút. */
  const themeBtn = $('#themeToggle');

  function currentTheme() {
    return document.documentElement.getAttribute('data-theme') === 'light' ? 'light' : 'dark';
  }

  function applyTheme(theme, save) {
    const isLight = theme === 'light';
    document.documentElement.toggleAttribute('data-theme', false);
    if (isLight) document.documentElement.setAttribute('data-theme', 'light');

    // Đồng bộ màu thanh trạng thái trình duyệt trên điện thoại
    const meta = document.querySelector('meta[name="theme-color"]');
    if (meta) meta.setAttribute('content', isLight ? '#F6F6FB' : '#0B0B18');

    if (themeBtn) {
      themeBtn.setAttribute('aria-pressed', String(isLight));
      themeBtn.setAttribute('aria-label', isLight ? 'Chuyển sang giao diện tối' : 'Chuyển sang giao diện sáng');
    }
    if (save) {
      try { localStorage.setItem('hm-theme', theme); } catch (e) { /* bỏ qua */ }
    }
  }

  applyTheme(currentTheme(), false);

  if (themeBtn) {
    themeBtn.addEventListener('click', () => {
      const next = currentTheme() === 'light' ? 'dark' : 'light';
      applyTheme(next, true);
      notify(next === 'light' ? 'Đã chuyển sang giao diện sáng.' : 'Đã chuyển sang giao diện tối.', 'info');
    });
  }

  // Người dùng chưa chọn thủ công thì đi theo cài đặt của hệ điều hành
  window.matchMedia('(prefers-color-scheme: light)').addEventListener('change', (ev) => {
    let saved = null;
    try { saved = localStorage.getItem('hm-theme'); } catch (e) { /* bỏ qua */ }
    if (!saved) applyTheme(ev.matches ? 'light' : 'dark', false);
  });

  /* ---------- 2. Menu điện thoại ---------- */
  const navToggle = $('#navToggle');
  const primaryNav = $('#primaryNav');
  if (navToggle && primaryNav) {
    navToggle.addEventListener('click', () => {
      const open = navToggle.getAttribute('aria-expanded') === 'true';
      navToggle.setAttribute('aria-expanded', String(!open));
      navToggle.setAttribute('aria-label', open ? 'Mở menu điều hướng' : 'Đóng menu điều hướng');
      primaryNav.classList.toggle('is-open', !open);
    });
    // Đóng menu khi bấm Esc
    document.addEventListener('keydown', (e) => {
      if (e.key === 'Escape' && primaryNav.classList.contains('is-open')) {
        navToggle.click();
        navToggle.focus();
      }
    });
  }

  /* ---------- 3. Hiệu ứng xuất hiện khi cuộn tới ---------- */
  const revealEls = $$('.reveal');
  if (reduceMotion || !('IntersectionObserver' in window)) {
    revealEls.forEach((el) => el.classList.add('is-visible'));
  } else {
    // Gán chỉ số cho hiệu ứng nối tiếp trong nhóm .stagger
    $$('.stagger').forEach((group) => {
      Array.from(group.children).forEach((child, i) => child.style.setProperty('--i', String(i)));
    });
    const io = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          entry.target.classList.add('is-visible');
          io.unobserve(entry.target);
        }
      });
    }, { threshold: 0.12, rootMargin: '0px 0px -8% 0px' });
    revealEls.forEach((el) => io.observe(el));
  }

  /* ---------- 4. Đếm số tăng dần ---------- */
  const counters = $$('[data-count]');
  if (counters.length) {
    const run = (el) => {
      const target = parseFloat(el.dataset.count) || 0;
      const suffix = el.dataset.suffix || '';
      if (reduceMotion) { el.textContent = target.toLocaleString('vi-VN') + suffix; return; }
      const duration = 1400;
      const start = performance.now();
      const step = (now) => {
        const p = Math.min((now - start) / duration, 1);
        const eased = 1 - Math.pow(1 - p, 3);
        el.textContent = Math.round(target * eased).toLocaleString('vi-VN') + suffix;
        if (p < 1) requestAnimationFrame(step);
      };
      requestAnimationFrame(step);
    };
    const co = new IntersectionObserver((entries) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) { run(entry.target); co.unobserve(entry.target); }
      });
    }, { threshold: 0.4 });
    counters.forEach((el) => co.observe(el));
  }

  /* ---------- 5. Thông báo tự ẩn ---------- */
  $$('.flash').forEach((el, i) => {
    setTimeout(() => {
      el.classList.add('is-hiding');
      setTimeout(() => el.remove(), 400);
    }, 4200 + i * 500);
  });

  /** Hiện một thông báo mới bằng JavaScript. */
  function notify(message, type = 'success') {
    let stack = $('.flash-stack');
    if (!stack) {
      stack = document.createElement('div');
      stack.className = 'flash-stack';
      stack.setAttribute('role', 'status');
      stack.setAttribute('aria-live', 'polite');
      document.body.appendChild(stack);
    }
    const box = document.createElement('div');
    box.className = 'flash flash--' + type;
    box.textContent = message;
    stack.appendChild(box);
    setTimeout(() => {
      box.classList.add('is-hiding');
      setTimeout(() => box.remove(), 400);
    }, 3600);
  }
  window.hmNotify = notify;

  /* ---------- 6. Giỏ hàng bằng AJAX ---------- */
  const cartBadge = $('#cartBadge');
  function setCartCount(n) {
    if (!cartBadge) return;
    cartBadge.textContent = n;
    cartBadge.hidden = n < 1;
    cartBadge.style.animation = 'none';
    void cartBadge.offsetWidth;      // buộc trình duyệt vẽ lại để chạy lại hoạt ảnh
    cartBadge.style.animation = '';
  }

  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-cart-add]');
    if (!btn) return;
    e.preventDefault();

    const id = btn.dataset.cartAdd;
    const license = (document.querySelector('input[name="license"]:checked') || {}).value || 'personal';
    const original = btn.innerHTML;
    btn.classList.add('is-disabled');
    btn.textContent = 'Đang thêm…';

    try {
      const res = await fetch(btn.dataset.endpoint || 'ajax-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({ action: 'add', id, license, csrf_token: btn.dataset.csrf || '' })
      });
      const data = await res.json();
      if (data.ok) {
        setCartCount(data.count);
        notify(data.message || 'Đã thêm vào giỏ hàng.');
      } else {
        notify(data.message || 'Không thêm được vào giỏ hàng.', 'error');
      }
    } catch (err) {
      notify('Lỗi kết nối máy chủ. Vui lòng thử lại.', 'error');
    } finally {
      btn.classList.remove('is-disabled');
      btn.innerHTML = original;
    }
  });

  /* ---------- 7. Yêu thích ---------- */
  document.addEventListener('click', async (e) => {
    const btn = e.target.closest('[data-wish]');
    if (!btn) return;
    e.preventDefault();

    try {
      const res = await fetch('ajax-cart.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded', 'X-Requested-With': 'XMLHttpRequest' },
        body: new URLSearchParams({ action: 'wish', id: btn.dataset.wish, csrf_token: btn.dataset.csrf || '' })
      });
      const data = await res.json();
      if (data.needLogin) { window.location.href = 'login.php'; return; }
      if (data.ok) {
        btn.classList.toggle('is-active', data.active);
        btn.setAttribute('aria-pressed', String(data.active));
        notify(data.message);
      } else {
        notify(data.message || 'Thao tác thất bại.', 'error');
      }
    } catch (err) {
      notify('Lỗi kết nối máy chủ.', 'error');
    }
  });

  /* ---------- 8. Tab chi tiết dự án ---------- */
  $$('[data-tabs]').forEach((wrap) => {
    const buttons = $$('[role="tab"]', wrap);
    buttons.forEach((btn) => {
      btn.addEventListener('click', () => {
        buttons.forEach((b) => {
          const on = b === btn;
          b.classList.toggle('is-active', on);
          b.setAttribute('aria-selected', String(on));
          const panel = document.getElementById(b.getAttribute('aria-controls'));
          if (panel) panel.hidden = !on;
        });
      });
      // Điều hướng tab bằng phím mũi tên
      btn.addEventListener('keydown', (ev) => {
        const i = buttons.indexOf(btn);
        if (ev.key === 'ArrowRight') buttons[(i + 1) % buttons.length].focus();
        if (ev.key === 'ArrowLeft')  buttons[(i - 1 + buttons.length) % buttons.length].focus();
      });
    });
  });

  /* ---------- 9. Câu hỏi thường gặp (accordion) ---------- */
  $$('.accordion__btn').forEach((btn) => {
    btn.addEventListener('click', () => {
      const open = btn.getAttribute('aria-expanded') === 'true';
      btn.setAttribute('aria-expanded', String(!open));
      const panel = document.getElementById(btn.getAttribute('aria-controls'));
      if (panel) panel.dataset.open = String(!open);
    });
  });

  /* ---------- 10. Ảnh tải chậm dự phòng ---------- */
  $$('img[data-src]').forEach((img) => {
    const lo = new IntersectionObserver((entries, obs) => {
      entries.forEach((entry) => {
        if (entry.isIntersecting) {
          img.src = img.dataset.src;
          img.removeAttribute('data-src');
          obs.disconnect();
        }
      });
    }, { rootMargin: '200px' });
    lo.observe(img);
  });

  /* ---------- 11. Kiểm tra biểu mẫu ngay tại chỗ ---------- */
  $$('form[data-validate]').forEach((form) => {
    form.addEventListener('submit', (e) => {
      let firstInvalid = null;
      $$('[required]', form).forEach((field) => {
        const ok = field.value.trim() !== '' && field.checkValidity();
        field.setAttribute('aria-invalid', String(!ok));
        if (!ok && !firstInvalid) firstInvalid = field;
      });
      if (firstInvalid) {
        e.preventDefault();
        firstInvalid.focus();
        notify('Vui lòng kiểm tra lại các ô còn thiếu.', 'warning');
      }
    });
  });
})();
