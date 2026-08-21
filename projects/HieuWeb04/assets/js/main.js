/**
 * DatCyber - Main JavaScript Application
 * Modern AJAX Cart, Quick View, Toasts, Countdown & Animations
 */

document.addEventListener('DOMContentLoaded', () => {
  initCountdown();
  initCartDrawer();
  initQuickView();
  initScrollAnimations();
});

/* ==========================================================================
   TOAST NOTIFICATION ENGINE
   ========================================================================== */
function showToast(message, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `custom-toast toast-${type}`;
  
  const icon = type === 'success' ? 'fa-check-circle text-success' : 'fa-exclamation-circle text-danger';
  toast.innerHTML = `
    <i class="fas ${icon} fa-lg"></i>
    <div style="flex: 1;">${message}</div>
    <button onclick="this.parentElement.remove()" style="background:none;border:none;color:#94a3b8;cursor:pointer;">
      <i class="fas fa-times"></i>
    </button>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.animation = 'fadeOutRight 0.35s ease-in forwards';
    setTimeout(() => toast.remove(), 350);
  }, 3500);
}

/* ==========================================================================
   FLASH SALE COUNTDOWN TIMER
   ========================================================================== */
function initCountdown() {
  const hoursEl = document.getElementById('countHours');
  const minsEl = document.getElementById('countMins');
  const secsEl = document.getElementById('countSecs');

  if (!hoursEl || !minsEl || !secsEl) return;

  // Set target to end of today or 12 hours from now
  let targetTime = new Date().getTime() + (12 * 60 * 60 * 1000);

  function updateTimer() {
    const now = new Date().getTime();
    const diff = targetTime - now;

    if (diff <= 0) {
      targetTime = new Date().getTime() + (24 * 60 * 60 * 1000);
      return;
    }

    const hours = Math.floor((diff / (1000 * 60 * 60)) % 24);
    const minutes = Math.floor((diff / (1000 * 60)) % 60);
    const seconds = Math.floor((diff / 1000) % 60);

    hoursEl.innerText = hours.toString().padStart(2, '0');
    minsEl.innerText = minutes.toString().padStart(2, '0');
    secsEl.innerText = seconds.toString().padStart(2, '0');
  }

  updateTimer();
  setInterval(updateTimer, 1000);
}

/* ==========================================================================
   CART SLIDE-OUT DRAWER & AJAX OPERATIONS
   ========================================================================== */
function initCartDrawer() {
  const drawerOverlay = document.getElementById('cartDrawerOverlay');
  const closeBtn = document.getElementById('closeCartDrawer');
  const openBtns = document.querySelectorAll('.trigger-cart-drawer');

  if (!drawerOverlay) return;

  openBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      fetchCartDrawerContent();
      drawerOverlay.classList.add('active');
    });
  });

  if (closeBtn) {
    closeBtn.addEventListener('click', () => {
      drawerOverlay.classList.remove('active');
    });
  }

  drawerOverlay.addEventListener('click', (e) => {
    if (e.target === drawerOverlay) {
      drawerOverlay.classList.remove('active');
    }
  });
}

// Add to Cart via AJAX with Promise & Redirection support
function addToCart(productId, quantity = 1, openDrawer = true, redirectUrl = null) {
  const formData = new FormData();
  formData.append('action', 'add');
  formData.append('product_id', productId);
  formData.append('quantity', quantity);

  return fetch('ajax-cart.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.success) {
      updateCartBadge(data.total_items);
      
      if (redirectUrl) {
        window.location.href = redirectUrl;
        return;
      }

      showToast(data.message, 'success');
      
      if (openDrawer) {
        fetchCartDrawerContent();
        const drawer = document.getElementById('cartDrawerOverlay');
        if (drawer) drawer.classList.add('active');
      }
    } else {
      showToast(data.message || 'Có lỗi xảy ra khi thêm vào giỏ!', 'error');
    }
  })
  .catch(err => {
    console.error(err);
    showToast('Lỗi kết nối máy chủ!', 'error');
  });
}

// Update Cart Badge Count in Header
function updateCartBadge(count) {
  const badges = document.querySelectorAll('.cart-badge-count');
  badges.forEach(b => {
    b.innerText = count;
    b.classList.remove('animate-badge-pulse');
    void b.offsetWidth; // Trigger reflow
    b.classList.add('animate-badge-pulse');
  });
}

// Fetch Cart Drawer HTML
function fetchCartDrawerContent() {
  const body = document.getElementById('cartDrawerBody');
  const footer = document.getElementById('cartDrawerFooter');
  if (!body) return;

  body.innerHTML = '<div class="text-center py-4"><i class="fas fa-spinner fa-spin fa-2x text-primary"></i><p class="mt-2 text-muted">Đang tải giỏ hàng...</p></div>';

  fetch('ajax-cart.php?action=get_drawer')
    .then(res => res.json())
    .then(data => {
      if (data.success) {
        body.innerHTML = data.items_html;
        if (footer) footer.innerHTML = data.footer_html;
      } else {
        body.innerHTML = '<div class="alert alert-danger m-3">Không thể tải giỏ hàng</div>';
      }
    })
    .catch(() => {
      body.innerHTML = '<div class="alert alert-danger m-3">Lỗi kết nối máy chủ</div>';
    });
}

// Update Quantity from Drawer / Cart Page with Lock
let isUpdatingCart = false;
function updateCartItemQty(productId, quantity) {
  if (isUpdatingCart) return;
  isUpdatingCart = true;

  const formData = new FormData();
  formData.append('action', 'update');
  formData.append('product_id', productId);
  formData.append('quantity', quantity);

  fetch('ajax-cart.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    isUpdatingCart = false;
    if (data.success) {
      updateCartBadge(data.total_items);
      fetchCartDrawerContent();
      // If on cart page, reload table safely
      if (window.location.pathname.includes('cart.php')) {
        window.location.reload();
      }
    } else {
      showToast(data.message || 'Không thể cập nhật số lượng', 'error');
    }
  })
  .catch(() => {
    isUpdatingCart = false;
    showToast('Lỗi kết nối máy chủ!', 'error');
  });
}

// Remove Item from Cart
function removeCartItem(productId) {
  if (isUpdatingCart) return;
  isUpdatingCart = true;

  const formData = new FormData();
  formData.append('action', 'remove');
  formData.append('product_id', productId);

  fetch('ajax-cart.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    isUpdatingCart = false;
    if (data.success) {
      updateCartBadge(data.total_items);
      showToast('Đã xóa sản phẩm khỏi giỏ hàng', 'success');
      fetchCartDrawerContent();
      if (window.location.pathname.includes('cart.php')) {
        window.location.reload();
      }
    }
  })
  .catch(() => {
    isUpdatingCart = false;
    showToast('Lỗi kết nối máy chủ!', 'error');
  });
}

/* ==========================================================================
   QUICK VIEW MODAL
   ========================================================================== */
function initQuickView() {
  window.openQuickView = function(productId) {
    const modal = document.getElementById('quickViewModal');
    const content = document.getElementById('quickViewModalContent');
    if (!modal || !content) return;

    content.innerHTML = '<div class="text-center py-5"><i class="fas fa-spinner fa-spin fa-3x text-primary"></i><p class="mt-3 text-muted">Đang tải thông tin sản phẩm...</p></div>';
    
    const bsModal = new bootstrap.Modal(modal);
    bsModal.show();

    fetch(`ajax-cart.php?action=quick_view&id=${productId}`)
      .then(res => res.json())
      .then(data => {
        if (data.success) {
          content.innerHTML = data.html;
        } else {
          content.innerHTML = '<div class="alert alert-danger">Không tìm thấy sản phẩm!</div>';
        }
      });
  };
}

/* ==========================================================================
   SCROLL REVEAL ANIMATIONS
   ========================================================================== */
function initScrollAnimations() {
  const observer = new IntersectionObserver((entries) => {
    entries.forEach(entry => {
      if (entry.isIntersecting) {
        entry.target.classList.add('animate__animated', 'animate__fadeInUp');
        observer.unobserve(entry.target);
      }
    });
  }, { threshold: 0.1 });

  document.querySelectorAll('.reveal-on-scroll').forEach(el => observer.observe(el));
}
