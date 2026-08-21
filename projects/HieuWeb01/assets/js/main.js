/**
 * HieuMini - Client Javascript Interactions (UI/UX Pro Max Edition)
 */

document.addEventListener('DOMContentLoaded', () => {
  // ==========================================
  // 1. Top Announcement Bar Carousel Ticker
  // ==========================================
  const topbarItems = document.querySelectorAll('.topbar-item');
  if (topbarItems.length > 1) {
    let activeTopbarIdx = 0;
    setInterval(() => {
      topbarItems[activeTopbarIdx].classList.remove('active');
      activeTopbarIdx = (activeTopbarIdx + 1) % topbarItems.length;
      topbarItems[activeTopbarIdx].classList.add('active');
    }, 4500);
  }

  // ==========================================
  // 2. Sticky Header Elevation on Scroll
  // ==========================================
  const siteHeader = document.getElementById('site-header');
  if (siteHeader) {
    const handleScroll = () => {
      if (window.scrollY > 20) {
        siteHeader.classList.add('scrolled');
      } else {
        siteHeader.classList.remove('scrolled');
      }
    };
    window.addEventListener('scroll', handleScroll, { passive: true });
    handleScroll();
  }

  // ==========================================
  // 3. Mobile Offcanvas Navigation Drawer
  // ==========================================
  const mobileMenuToggle = document.getElementById('mobile-menu-toggle');
  const mobileDrawer = document.getElementById('mobile-drawer');
  const mobileDrawerBackdrop = document.getElementById('mobile-drawer-backdrop');
  const drawerCloseBtn = document.getElementById('drawer-close-btn');

  function openDrawer() {
    if (mobileDrawer && mobileDrawerBackdrop) {
      mobileDrawer.classList.add('active');
      mobileDrawerBackdrop.classList.add('active');
      document.body.style.overflow = 'hidden';
      if (mobileMenuToggle) mobileMenuToggle.setAttribute('aria-expanded', 'true');
    }
  }

  function closeDrawer() {
    if (mobileDrawer && mobileDrawerBackdrop) {
      mobileDrawer.classList.remove('active');
      mobileDrawerBackdrop.classList.remove('active');
      document.body.style.overflow = '';
      if (mobileMenuToggle) mobileMenuToggle.setAttribute('aria-expanded', 'false');
    }
  }

  if (mobileMenuToggle) mobileMenuToggle.addEventListener('click', openDrawer);
  if (drawerCloseBtn) drawerCloseBtn.addEventListener('click', closeDrawer);
  if (mobileDrawerBackdrop) mobileDrawerBackdrop.addEventListener('click', closeDrawer);

  // ==========================================
  // 4. Mobile Fullscreen Search Overlay
  // ==========================================
  const mobileSearchBtn = document.getElementById('mobile-search-btn');
  const mobileSearchOverlay = document.getElementById('mobile-search-overlay');
  const mobileSearchClose = document.getElementById('mobile-search-close');
  const mobileSearchInput = document.getElementById('mobile-search-input');

  function openMobileSearch() {
    if (mobileSearchOverlay) {
      mobileSearchOverlay.classList.add('active');
      document.body.style.overflow = 'hidden';
      setTimeout(() => {
        if (mobileSearchInput) mobileSearchInput.focus();
      }, 100);
    }
  }

  function closeMobileSearch() {
    if (mobileSearchOverlay) {
      mobileSearchOverlay.classList.remove('active');
      document.body.style.overflow = '';
    }
  }

  if (mobileSearchBtn) mobileSearchBtn.addEventListener('click', openMobileSearch);
  if (mobileSearchClose) mobileSearchClose.addEventListener('click', closeMobileSearch);
  if (mobileSearchOverlay) {
    mobileSearchOverlay.addEventListener('click', (e) => {
      if (e.target === mobileSearchOverlay) closeMobileSearch();
    });
  }

  // ==========================================
  // 5. Desktop Live Search & AJAX Autocomplete
  // ==========================================
  const headerSearchForm = document.getElementById('header-search-form');
  const headerSearchInput = document.getElementById('header-search-input');
  const searchClearBtn = document.getElementById('search-clear-btn');
  const searchSuggestions = document.getElementById('search-suggestions');
  const defaultBox = document.getElementById('suggestions-default-box');
  const liveResultsBox = document.getElementById('search-live-results');
  const suggestionsFooter = document.getElementById('suggestions-footer');
  const keywordDisplay = document.getElementById('search-keyword-display');
  const viewAllBtn = document.getElementById('view-all-search-btn');

  let searchDebounceTimer = null;

  if (headerSearchInput) {
    const performLiveSearch = (keyword) => {
      const trimmed = keyword.trim();

      if (trimmed.length === 0) {
        if (defaultBox) defaultBox.style.display = 'block';
        if (liveResultsBox) liveResultsBox.style.display = 'none';
        if (suggestionsFooter) suggestionsFooter.style.display = 'none';
        return;
      }

      // Switch to live search mode
      if (defaultBox) defaultBox.style.display = 'none';
      if (liveResultsBox) {
        liveResultsBox.style.display = 'flex';
        liveResultsBox.innerHTML = `
          <div class="live-search-loading">
            <i class="fa-solid fa-spinner fa-spin text-accent"></i> Đang tìm kiếm sản phẩm...
          </div>
        `;
      }
      if (suggestionsFooter) {
        suggestionsFooter.style.display = 'block';
        if (keywordDisplay) keywordDisplay.textContent = trimmed;
        if (viewAllBtn) viewAllBtn.href = `products.php?keyword=${encodeURIComponent(trimmed)}`;
      }

      // Fetch live results from API
      fetch(`api/search.php?keyword=${encodeURIComponent(trimmed)}`)
        .then(res => res.json())
        .then(res => {
          if (!liveResultsBox) return;

          if (res.status === 'success' && Array.isArray(res.data) && res.data.length > 0) {
            let html = '';
            res.data.forEach(p => {
              const oldPriceHtml = p.has_discount ? `<span class="live-search-old-price">${p.price_formatted}</span>` : '';
              const badgeHtml = p.has_discount ? `<span class="badge-ceo" style="font-size:0.65rem;color:#ef4444;border-color:rgba(239,68,68,0.3);margin-left:4px;">-${p.discount_percent}%</span>` : '';

              html += `
                <a href="${p.url}" class="live-search-item">
                  <img src="${p.image}" alt="${p.name}" class="live-search-thumb" onerror="this.src='assets/images/logo.png'">
                  <div class="live-search-meta">
                    <span class="live-search-title">${p.name}</span>
                    <span class="live-search-category">${p.category_name} ${badgeHtml}</span>
                  </div>
                  <div class="live-search-prices">
                    <div>${p.current_price_formatted}</div>
                    ${oldPriceHtml}
                  </div>
                </a>
              `;
            });
            liveResultsBox.innerHTML = html;
          } else {
            liveResultsBox.innerHTML = `
              <div class="live-search-empty">
                <i class="fa-solid fa-magnifying-glass"></i>
                <div>Không tìm thấy sản phẩm nào khớp với "<strong>${trimmed}</strong>"</div>
                <div style="font-size: 0.75rem; margin-top: 4px; color: var(--text-light);">Thử tìm từ khóa khác như "Áo thun", "Polo", "Sơ mi", "Jean"</div>
              </div>
            `;
          }
        })
        .catch(err => {
          console.error('Search error:', err);
          if (liveResultsBox) {
            liveResultsBox.innerHTML = `
              <div class="live-search-empty" style="color: #ef4444;">
                <i class="fa-solid fa-triangle-exclamation"></i>
                <div>Không thể tải kết quả lúc này. Vui lòng bấm Enter để tìm kiếm.</div>
              </div>
            `;
          }
        });
    };

    const updateClearBtn = () => {
      if (searchClearBtn) {
        searchClearBtn.style.display = headerSearchInput.value.trim().length > 0 ? 'flex' : 'none';
      }
    };

    headerSearchInput.addEventListener('input', (e) => {
      updateClearBtn();
      clearTimeout(searchDebounceTimer);
      searchDebounceTimer = setTimeout(() => {
        performLiveSearch(e.target.value);
      }, 200);
    });

    headerSearchInput.addEventListener('focus', () => {
      if (headerSearchInput.value.trim().length > 0) {
        performLiveSearch(headerSearchInput.value);
      } else {
        if (defaultBox) defaultBox.style.display = 'block';
        if (liveResultsBox) liveResultsBox.style.display = 'none';
        if (suggestionsFooter) suggestionsFooter.style.display = 'none';
      }
    });

    if (searchClearBtn) {
      searchClearBtn.addEventListener('click', () => {
        headerSearchInput.value = '';
        updateClearBtn();
        performLiveSearch('');
        headerSearchInput.focus();
      });
    }

    updateClearBtn();
  }

  // Global Keyboard Shortcut: '/' to focus search
  document.addEventListener('keydown', (e) => {
    // Focus search on '/' when not in input/textarea
    if (e.key === '/' && !['INPUT', 'TEXTAREA', 'SELECT'].includes(document.activeElement.tagName)) {
      e.preventDefault();
      if (window.innerWidth <= 868) {
        openMobileSearch();
      } else if (headerSearchInput) {
        headerSearchInput.focus();
        headerSearchInput.select();
      }
    }

    // Escape key closes modals, search, and drawers
    if (e.key === 'Escape') {
      closeDrawer();
      closeMobileSearch();
      if (headerSearchInput) headerSearchInput.blur();
      const userCard = document.getElementById('user-dropdown-card');
      if (userCard) userCard.classList.remove('show');
    }
  });

  // ==========================================
  // 6. User Dropdown Toggle on Mobile/Touch
  // ==========================================
  const userDropdownBtn = document.getElementById('user-dropdown-btn');
  const userDropdownCard = document.getElementById('user-dropdown-card');

  if (userDropdownBtn && userDropdownCard) {
    userDropdownBtn.addEventListener('click', (e) => {
      e.stopPropagation();
      userDropdownCard.classList.toggle('show');
    });

    document.addEventListener('click', (e) => {
      if (!userDropdownCard.contains(e.target) && !userDropdownBtn.contains(e.target)) {
        userDropdownCard.classList.remove('show');
      }
    });
  }

  // ==========================================
  // 7. Hero Slider
  // ==========================================
  const slides = document.querySelectorAll('.hero-slide');
  const prevBtn = document.querySelector('.slider-btn.prev');
  const nextBtn = document.querySelector('.slider-btn.next');
  let currentSlide = 0;

  function showSlide(index) {
    if (!slides.length) return;
    slides.forEach((s, i) => {
      s.classList.toggle('active', i === index);
    });
  }

  function nextSlide() {
    currentSlide = (currentSlide + 1) % slides.length;
    showSlide(currentSlide);
  }

  function prevSlide() {
    currentSlide = (currentSlide - 1 + slides.length) % slides.length;
    showSlide(currentSlide);
  }

  if (nextBtn) nextBtn.addEventListener('click', nextSlide);
  if (prevBtn) prevBtn.addEventListener('click', prevSlide);

  if (slides.length > 1) {
    setInterval(nextSlide, 5000);
  }

  // ==========================================
  // 8. Flash Sale Countdown Timer
  // ==========================================
  const countdownEl = document.getElementById('flash-countdown');
  if (countdownEl) {
    let targetTime = new Date().getTime() + (24 * 3600 * 1000) + (14 * 60 * 1000);
    setInterval(() => {
      const now = new Date().getTime();
      const diff = targetTime - now;
      if (diff > 0) {
        const hours = Math.floor((diff % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
        const minutes = Math.floor((diff % (1000 * 60 * 60)) / (1000 * 60));
        const seconds = Math.floor((diff % (1000 * 60)) / 1000);
        
        const hEl = document.getElementById('cd-hours');
        const mEl = document.getElementById('cd-mins');
        const sEl = document.getElementById('cd-secs');
        if (hEl) hEl.textContent = String(hours).padStart(2, '0');
        if (mEl) mEl.textContent = String(minutes).padStart(2, '0');
        if (sEl) sEl.textContent = String(seconds).padStart(2, '0');
      }
    }, 1000);
  }

  // ==========================================
  // 9. Tab Switcher
  // ==========================================
  const tabBtns = document.querySelectorAll('.tab-btn');
  const tabPanes = document.querySelectorAll('.tab-pane');
  tabBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const targetId = btn.dataset.tab;
      tabBtns.forEach(b => b.classList.remove('active'));
      tabPanes.forEach(p => p.classList.remove('active'));
      btn.classList.add('active');
      const pane = document.getElementById(targetId);
      if (pane) pane.classList.add('active');
    });
  });

  // ==========================================
  // 10. Modal Size Guide
  // ==========================================
  const openModalBtns = document.querySelectorAll('[data-open-modal]');
  const closeModalBtns = document.querySelectorAll('[data-close-modal]');
  
  openModalBtns.forEach(btn => {
    btn.addEventListener('click', (e) => {
      e.preventDefault();
      const modalId = btn.dataset.openModal;
      const modal = document.getElementById(modalId);
      if (modal) modal.classList.add('active');
    });
  });

  closeModalBtns.forEach(btn => {
    btn.addEventListener('click', () => {
      const modal = btn.closest('.modal-overlay');
      if (modal) modal.classList.remove('active');
    });
  });

  document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', (e) => {
      if (e.target === overlay) overlay.classList.remove('active');
    });
  });

  // ==========================================
  // 11. Quantity Stepper
  // ==========================================
  document.querySelectorAll('.qty-btn').forEach(btn => {
    btn.addEventListener('click', () => {
      const input = btn.parentElement.querySelector('.qty-input');
      if (!input) return;
      let val = parseInt(input.value) || 1;
      if (btn.classList.contains('plus')) {
        val++;
      } else if (btn.classList.contains('minus')) {
        if (val > 1) val--;
      }
      input.value = val;
    });
  });
});

// Toast notification helper
function showToast(message, type = 'info') {
  let container = document.getElementById('toast-container');
  if (!container) {
    container = document.createElement('div');
    container.id = 'toast-container';
    document.body.appendChild(container);
  }
  const toast = document.createElement('div');
  toast.className = `toast toast-${type}`;
  toast.innerHTML = `<span>${message}</span>`;
  container.appendChild(toast);
  setTimeout(() => {
    toast.style.opacity = '0';
    toast.style.transform = 'translateX(100%)';
    setTimeout(() => toast.remove(), 300);
  }, 3500);
}
