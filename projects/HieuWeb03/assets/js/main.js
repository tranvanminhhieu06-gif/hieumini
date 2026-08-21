/**
 * HieuMini Stationery Store - Modern Interactive Engine
 * Author: Senior Frontend Lead & UI/UX Expert
 */

document.addEventListener('DOMContentLoaded', () => {
  initMobileDrawer();
  initMobileFilter();
  initLiveSearch();
  initFlashCountdown();
  initQuantityButtons();
  initAddToCartAjax();
  initThumbnailGallery();
  initConfettiIfSuccess();
});

// 1. Mobile Offcanvas Navigation Drawer
function initMobileDrawer() {
  const openBtn = document.getElementById('mobileMenuOpenBtn');
  const closeBtn = document.getElementById('mobileMenuCloseBtn');
  const drawer = document.getElementById('mobileNavDrawer');
  const backdrop = document.getElementById('mobileDrawerBackdrop');

  if (!drawer || !backdrop) return;

  function openDrawer() {
    drawer.classList.add('open');
    backdrop.classList.add('active');
    document.body.style.overflow = 'hidden';
  }

  function closeDrawer() {
    drawer.classList.remove('open');
    backdrop.classList.remove('active');
    document.body.style.overflow = '';
  }

  if (openBtn) openBtn.addEventListener('click', openDrawer);
  if (closeBtn) closeBtn.addEventListener('click', closeDrawer);
  backdrop.addEventListener('click', closeDrawer);

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape' && drawer.classList.contains('open')) {
      closeDrawer();
    }
  });
}

// 2. Mobile Catalog Filter Drawer Toggle
function initMobileFilter() {
  const toggleBtn = document.getElementById('mobileFilterToggleBtn');
  const filterSidebar = document.querySelector('.filter-sidebar');
  const backdrop = document.getElementById('mobileDrawerBackdrop');

  if (!toggleBtn || !filterSidebar) return;

  toggleBtn.addEventListener('click', () => {
    const isOpen = filterSidebar.classList.toggle('open');
    if (backdrop) {
      if (isOpen) {
        backdrop.classList.add('active');
        document.body.style.overflow = 'hidden';
      } else {
        backdrop.classList.remove('active');
        document.body.style.overflow = '';
      }
    }
  });
}

// 3. Toast Notification Manager
function showToast(message, type = 'success') {
  let container = document.querySelector('.toast-container');
  if (!container) {
    container = document.createElement('div');
    container.className = 'toast-container';
    document.body.appendChild(container);
  }

  const toast = document.createElement('div');
  toast.className = `toast-msg ${type}`;
  
  let icon = 'bi-check-circle-fill';
  if (type === 'danger') icon = 'bi-exclamation-octagon-fill';
  else if (type === 'warning') icon = 'bi-exclamation-triangle-fill';
  else if (type === 'info') icon = 'bi-info-circle-fill';

  toast.innerHTML = `
    <i class="bi ${icon}" style="font-size: 1.3rem;"></i>
    <div style="font-weight: 700; font-size: 0.94rem; line-height: 1.4;">${message}</div>
  `;

  container.appendChild(toast);

  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(50px)';
    setTimeout(() => toast.remove(), 350);
  }, 3500);
}

// 4. Live Search Suggestions
function initLiveSearch() {
  const searchInput = document.getElementById('globalSearchInput');
  const suggestionsBox = document.getElementById('searchSuggestions');

  if (!searchInput || !suggestionsBox) return;

  let debounceTimer;

  searchInput.addEventListener('input', (e) => {
    clearTimeout(debounceTimer);
    const query = e.target.value.trim();

    if (query.length < 2) {
      suggestionsBox.style.display = 'none';
      suggestionsBox.innerHTML = '';
      return;
    }

    debounceTimer = setTimeout(() => {
      fetch(`api/search.php?q=${encodeURIComponent(query)}`)
        .then(res => res.json())
        .then(data => {
          if (data.status === 'success' && data.products.length > 0) {
            let html = '';
            data.products.forEach(p => {
              html += `
                <a href="product-detail.php?id=${p.id}" class="search-suggestion-item">
                  <img src="assets/images/products/${p.image}" class="search-suggestion-img" alt="${p.name}">
                  <div>
                    <div style="font-weight: 700; font-size: 0.92rem; color: #0f172a;">${p.name}</div>
                    <div style="font-size: 0.85rem; color: #4f46e5; font-weight: 800;">${p.formatted_price}</div>
                  </div>
                </a>
              `;
            });
            suggestionsBox.innerHTML = html;
            suggestionsBox.style.display = 'block';
          } else {
            suggestionsBox.innerHTML = '<div style="padding: 16px; text-align: center; color: #64748b; font-size: 0.9rem; font-weight: 500;">Không tìm thấy đồ dùng phù hợp</div>';
            suggestionsBox.style.display = 'block';
          }
        })
        .catch(err => {
          console.error('Search error:', err);
        });
    }, 220);
  });

  // Close suggestions when clicking outside
  document.addEventListener('click', (e) => {
    if (!searchInput.contains(e.target) && !suggestionsBox.contains(e.target)) {
      suggestionsBox.style.display = 'none';
    }
  });
}

// 5. AJAX Add to Cart with Micro-Animations
function initAddToCartAjax() {
  document.querySelectorAll('.ajax-add-to-cart').forEach(btn => {
    btn.addEventListener('click', function(e) {
      e.preventDefault();
      const productId = this.getAttribute('data-product-id');
      const qtyInput = document.getElementById(`qty_${productId}`) || document.getElementById('productQuantity');
      const quantity = qtyInput ? parseInt(qtyInput.value) : 1;

      // Animate button
      const originalHtml = this.innerHTML;
      this.innerHTML = '<i class="bi bi-arrow-repeat spin"></i> Đang thêm...';
      this.disabled = true;
      this.classList.add('is-loading');

      const formData = new FormData();
      formData.append('action', 'add');
      formData.append('product_id', productId);
      formData.append('quantity', quantity);

      fetch('api/cart.php', {
        method: 'POST',
        body: formData
      })
      .then(res => res.json())
      .then(data => {
        this.innerHTML = originalHtml;
        this.disabled = false;
        this.classList.remove('is-loading');

        if (data.status === 'success') {
          // Update cart badge across navbar
          const badges = document.querySelectorAll('.cart-badge');
          badges.forEach(b => {
            b.textContent = data.cart_count;
            b.classList.add('shake');
            setTimeout(() => b.classList.remove('shake'), 600);
          });

          showToast(data.message || 'Đã thêm đồ dùng học tập vào giỏ hàng!', 'success');
        } else {
          showToast(data.message || 'Có lỗi xảy ra', 'danger');
        }
      })
      .catch(err => {
        this.innerHTML = originalHtml;
        this.disabled = false;
        this.classList.remove('is-loading');
        showToast('Lỗi kết nối máy chủ', 'danger');
      });
    });
  });
}

// 6. Quantity Adjuster Buttons
function initQuantityButtons() {
  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', function() {
      const input = this.parentElement.querySelector('.qty-input');
      if (!input) return;

      let val = parseInt(input.value) || 1;
      if (this.classList.contains('qty-plus')) {
        val += 1;
      } else if (this.classList.contains('qty-minus') && val > 1) {
        val -= 1;
      }
      input.value = val;

      // Auto update if on cart page
      if (this.getAttribute('data-cart-update') === 'true') {
        const row = this.closest('tr');
        const productId = row ? row.getAttribute('data-product-id') : null;
        if (productId) {
          updateCartQuantity(productId, val);
        }
      }
    });
  });
}

function updateCartQuantity(productId, quantity) {
  const formData = new FormData();
  formData.append('action', 'update');
  formData.append('product_id', productId);
  formData.append('quantity', quantity);

  fetch('api/cart.php', {
    method: 'POST',
    body: formData
  })
  .then(res => res.json())
  .then(data => {
    if (data.status === 'success') {
      window.location.reload();
    }
  });
}

// 7. Flash Sale Countdown Timer
function initFlashCountdown() {
  const hoursEl = document.getElementById('saleHours');
  const minsEl = document.getElementById('saleMins');
  const secsEl = document.getElementById('saleSecs');

  if (!hoursEl || !minsEl || !secsEl) return;

  let totalSeconds = 14 * 3600 + 45 * 60 + 30; // 14h 45m 30s

  setInterval(() => {
    if (totalSeconds <= 0) totalSeconds = 24 * 3600;
    totalSeconds--;

    const h = Math.floor(totalSeconds / 3600);
    const m = Math.floor((totalSeconds % 3600) / 60);
    const s = totalSeconds % 60;

    hoursEl.textContent = h.toString().padStart(2, '0');
    minsEl.textContent = m.toString().padStart(2, '0');
    secsEl.textContent = s.toString().padStart(2, '0');
  }, 1000);
}

// 8. Thumbnail Gallery Click
function initThumbnailGallery() {
  const mainImg = document.getElementById('mainDetailImage');
  const thumbs = document.querySelectorAll('.thumb-item');

  if (!mainImg || thumbs.length === 0) return;

  thumbs.forEach(thumb => {
    thumb.addEventListener('click', function() {
      thumbs.forEach(t => t.classList.remove('active'));
      this.classList.add('active');
      const targetSrc = this.getAttribute('data-full-src');
      mainImg.style.opacity = '0.35';
      mainImg.src = targetSrc;
      setTimeout(() => {
        mainImg.style.opacity = '1';
      }, 180);
    });
  });
}

// 9. Confetti Celebration for Order Success Page
function initConfettiIfSuccess() {
  const successBox = document.getElementById('orderSuccessConfetti');
  if (!successBox) return;

  for (let i = 0; i < 60; i++) {
    const confetti = document.createElement('div');
    confetti.style.position = 'fixed';
    confetti.style.width = `${Math.random() * 10 + 6}px`;
    confetti.style.height = `${Math.random() * 10 + 6}px`;
    confetti.style.backgroundColor = ['#4f46e5', '#ec4899', '#f59e0b', '#10b981', '#06b6d4'][Math.floor(Math.random() * 5)];
    confetti.style.top = '-20px';
    confetti.style.left = `${Math.random() * 100}vw`;
    confetti.style.opacity = '0.9';
    confetti.style.borderRadius = Math.random() > 0.5 ? '50%' : '2px';
    confetti.style.zIndex = '9999';
    confetti.style.pointerEvents = 'none';
    confetti.style.transition = `transform ${Math.random() * 3 + 2}s cubic-bezier(0.25, 0.46, 0.45, 0.94), opacity 3s ease-out`;

    document.body.appendChild(confetti);

    setTimeout(() => {
      confetti.style.transform = `translate(${Math.random() * 200 - 100}px, ${window.innerHeight + 50}px) rotate(${Math.random() * 720}deg)`;
      confetti.style.opacity = '0';
    }, 100);

    setTimeout(() => confetti.remove(), 5500);
  }
}
