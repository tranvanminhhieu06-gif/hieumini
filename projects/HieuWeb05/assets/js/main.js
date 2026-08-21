/**
 * HIEUMINI LUXURY FITNESS CLUB - MAIN JAVASCRIPT ENGINE
 * Standard: CEO Executive Edition | Micro-interactions, Scroll Reveal, AJAX Cart
 */

document.addEventListener('DOMContentLoaded', () => {
    initHeaderScroll();
    initScrollReveal();
    initStatsCounters();
    initSearchToggle();
    initMobileNav();
    initBackToTop();
    initAjaxCart();
    initBmiCalculator();
    initModals();
    initProductTabs();
});

/* ==================== 1. STICKY HEADER SCROLL ==================== */
function initHeaderScroll() {
    const header = document.querySelector('.site-header');
    if (!header) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 40) {
            header.classList.add('scrolled');
        } else {
            header.classList.remove('scrolled');
        }
    });
}

/* ==================== 2. SCROLL REVEAL (INTERSECTION OBSERVER) ==================== */
function initScrollReveal() {
    const revealElements = document.querySelectorAll('.reveal, .reveal-left, .reveal-right');
    if (!revealElements.length) return;

    // Immediately reveal elements that are already in the initial viewport
    revealElements.forEach(el => {
        const rect = el.getBoundingClientRect();
        if (rect.top < window.innerHeight + 50) {
            el.classList.add('active');
        }
    });

    if ('IntersectionObserver' in window) {
        const observerOptions = {
            root: null,
            rootMargin: '0px',
            threshold: 0.08
        };

        const revealObserver = new IntersectionObserver((entries, observer) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.classList.add('active');
                    observer.unobserve(entry.target);
                }
            });
        }, observerOptions);

        revealElements.forEach(el => {
            if (!el.classList.contains('active')) {
                revealObserver.observe(el);
            }
        });
    } else {
        // Fallback for older browsers
        revealElements.forEach(el => el.classList.add('active'));
    }
}

/* ==================== 3. ANIMATED STATS COUNTER ==================== */
function initStatsCounters() {
    const counterElements = document.querySelectorAll('.stat-number');
    if (!counterElements.length) return;

    let hasRun = false;

    const counterObserver = new IntersectionObserver((entries) => {
        entries.forEach(entry => {
            if (entry.isIntersecting && !hasRun) {
                hasRun = true;
                counterElements.forEach(counter => {
                    const target = parseInt(counter.getAttribute('data-target') || '0', 10);
                    const suffix = counter.getAttribute('data-suffix') || '';
                    const duration = 1800; // ms
                    const stepTime = 25;
                    const steps = duration / stepTime;
                    const increment = target / steps;
                    let current = 0;

                    const timer = setInterval(() => {
                        current += increment;
                        if (current >= target) {
                            counter.textContent = target.toLocaleString('vi-VN') + suffix;
                            clearInterval(timer);
                        } else {
                            counter.textContent = Math.floor(current).toLocaleString('vi-VN') + suffix;
                        }
                    }, stepTime);
                });
            }
        });
    }, { threshold: 0.3 });

    const statsSection = document.querySelector('.hero-stats-row') || document.querySelector('.stat-box');
    if (statsSection) {
        counterObserver.observe(statsSection);
    }
}

/* ==================== 4. SEARCH MODAL TOGGLE ==================== */
function initSearchToggle() {
    const toggleBtn = document.querySelector('.search-toggle-btn');
    const searchBar = document.querySelector('.search-dropdown-bar');
    if (!toggleBtn || !searchBar) return;

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        searchBar.classList.toggle('active');
        if (searchBar.classList.contains('active')) {
            const input = searchBar.querySelector('input');
            if (input) input.focus();
        }
    });

    document.addEventListener('click', (e) => {
        if (!searchBar.contains(e.target) && !toggleBtn.contains(e.target)) {
            searchBar.classList.remove('active');
        }
    });
}

/* ==================== 5. MOBILE DRAWER NAVIGATION ==================== */
function initMobileNav() {
    const toggleBtn = document.querySelector('.mobile-toggle-btn');
    const navMenu = document.querySelector('.nav-menu');
    if (!toggleBtn || !navMenu) return;

    toggleBtn.addEventListener('click', (e) => {
        e.stopPropagation();
        navMenu.classList.toggle('mobile-open');
        const icon = toggleBtn.querySelector('i');
        if (icon) {
            icon.classList.toggle('fa-bars');
            icon.classList.toggle('fa-times');
        }
    });

    // Close mobile menu when clicking any nav link
    navMenu.querySelectorAll('.nav-link').forEach(link => {
        link.addEventListener('click', () => {
            navMenu.classList.remove('mobile-open');
            const icon = toggleBtn.querySelector('i');
            if (icon) {
                icon.classList.add('fa-bars');
                icon.classList.remove('fa-times');
            }
        });
    });

    // Close when clicking outside
    document.addEventListener('click', (e) => {
        if (!navMenu.contains(e.target) && !toggleBtn.contains(e.target)) {
            navMenu.classList.remove('mobile-open');
            const icon = toggleBtn.querySelector('i');
            if (icon) {
                icon.classList.add('fa-bars');
                icon.classList.remove('fa-times');
            }
        }
    });
}

/* ==================== 5.1 BACK TO TOP BUTTON ==================== */
function initBackToTop() {
    const topBtn = document.getElementById('back-to-top');
    if (!topBtn) return;

    window.addEventListener('scroll', () => {
        if (window.scrollY > 350) {
            topBtn.classList.add('visible');
        } else {
            topBtn.classList.remove('visible');
        }
    });

    topBtn.addEventListener('click', () => {
        window.scrollTo({
            top: 0,
            behavior: 'smooth'
        });
    });
}

/* ==================== 6. AJAX CART & QUICK INTERACTIONS ==================== */
function initAjaxCart() {
    // Add to cart buttons (Catalog & Product Detail)
    document.addEventListener('click', function(e) {
        const btn = e.target.closest('.add-cart-btn-direct, .btn-add-cart-detail');
        if (!btn) return;

        e.preventDefault();
        const productId = btn.getAttribute('data-id');
        let quantity = 1;

        const qtyInput = document.querySelector('.qty-input');
        if (qtyInput) {
            quantity = parseInt(qtyInput.value, 10) || 1;
        }

        const originalText = btn.innerHTML;
        btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang thêm...';
        btn.disabled = true;

        fetch('ajax-cart.php', {
            method: 'POST',
            headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
            body: new URLSearchParams({
                action: 'add',
                product_id: productId,
                quantity: quantity
            })
        })
        .then(res => res.json())
        .then(data => {
            btn.innerHTML = originalText;
            btn.disabled = false;

            if (data.status === 'success') {
                updateCartCountBadge(data.cart_count);
                showToast('success', data.message || 'Đã thêm sản phẩm vào giỏ hàng!');
            } else {
                showToast('error', data.message || 'Có lỗi xảy ra!');
            }
        })
        .catch(err => {
            btn.innerHTML = originalText;
            btn.disabled = false;
            showToast('error', 'Lỗi kết nối máy chủ!');
        });
    });

    // Quantity Stepper on detail & cart pages
    document.addEventListener('click', function(e) {
        const plusBtn = e.target.closest('.qty-plus');
        const minusBtn = e.target.closest('.qty-minus');

        if (plusBtn) {
            const input = plusBtn.parentElement.querySelector('.qty-input');
            if (input) {
                input.value = parseInt(input.value || '1', 10) + 1;
                input.dispatchEvent(new Event('change'));
            }
        }
        if (minusBtn) {
            const input = minusBtn.parentElement.querySelector('.qty-input');
            if (input) {
                const val = parseInt(input.value || '1', 10);
                if (val > 1) {
                    input.value = val - 1;
                    input.dispatchEvent(new Event('change'));
                }
            }
        }
    });

    // Cart Quantity change AJAX
    const cartQtyInputs = document.querySelectorAll('.cart-qty-input');
    cartQtyInputs.forEach(input => {
        input.addEventListener('change', function() {
            const productId = this.getAttribute('data-id');
            const qty = parseInt(this.value, 10);

            if (qty < 1) return;

            fetch('ajax-cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'update',
                    product_id: productId,
                    quantity: qty
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    showToast('error', data.message);
                }
            });
        });
    });

    // Coupon Apply Form
    const couponForm = document.querySelector('#coupon-form');
    if (couponForm) {
        couponForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const code = this.querySelector('input[name="coupon_code"]').value.trim();
            if (!code) {
                showToast('error', 'Vui lòng nhập mã ưu đãi!');
                return;
            }

            fetch('ajax-cart.php', {
                method: 'POST',
                headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
                body: new URLSearchParams({
                    action: 'apply_coupon',
                    coupon_code: code
                })
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast('success', data.message);
                    setTimeout(() => window.location.reload(), 800);
                } else {
                    showToast('error', data.message);
                }
            });
        });
    }
}

function updateCartCountBadge(count) {
    const badges = document.querySelectorAll('.cart-count');
    badges.forEach(b => {
        b.textContent = count;
        b.classList.remove('bounce');
        void b.offsetWidth; // trigger reflow
        b.classList.add('bounce');
    });
}

/* ==================== 7. INTERACTIVE BMI CALCULATOR ==================== */
function initBmiCalculator() {
    const form = document.querySelector('#bmi-calculator-form');
    if (!form) return;

    form.addEventListener('submit', (e) => {
        e.preventDefault();
        const weight = parseFloat(form.querySelector('#bmi-weight').value);
        const height = parseFloat(form.querySelector('#bmi-height').value) / 100; // to meters
        const age = parseInt(form.querySelector('#bmi-age').value, 10);
        const gender = form.querySelector('#bmi-gender').value;

        if (!weight || !height || height <= 0) {
            showToast('error', 'Vui lòng nhập đầy đủ chiều cao và cân nặng hợp lệ!');
            return;
        }

        const bmi = (weight / (height * height)).toFixed(1);
        const numberEl = document.querySelector('#bmi-score-number');
        const statusEl = document.querySelector('#bmi-status-text');
        const recEl = document.querySelector('#bmi-rec-text');
        const circleEl = document.querySelector('#bmi-score-circle');

        if (numberEl) numberEl.textContent = bmi;

        let status = '';
        let color = '';
        let advice = '';

        if (bmi < 18.5) {
            status = 'Gầy / Thiếu Cân';
            color = '#06b6d4';
            advice = 'Bạn nên tăng cường dinh dưỡng với Whey Isolate & Mass Gainer kết hợp giáo án tăng cơ Hypertrophy cùng Master Trainer HieuMini.';
        } else if (bmi >= 18.5 && bmi < 24.9) {
            status = 'Chuẩn Body VIP / Lý Tưởng';
            color = '#10b981';
            advice = 'Thể trạng của bạn đang ở mức rất tuyệt vời! Tiếp tục duy trì phong độ rèn luyện và phục hồi với gói CEO Diamond Elite.';
        } else if (bmi >= 25.0 && bmi < 29.9) {
            status = 'Thừa Cân / Cần Giảm Mỡ';
            color = '#f59e0b';
            advice = 'Khuyến nghị tham gia các buổi tập HIIT, Cardio X9 Pro kết hợp thực đơn kiểm soát calo và bổ sung BCAA/Fat Burner.';
        } else {
            status = 'Béo Phì / Nguy Cơ Cao';
            color = '#ef4444';
            advice = 'Nên đặt lịch khám thành phần cơ thể InBody 770 và theo sát lộ trình cá nhân hóa 1:1 cùng Master Trainer để bảo vệ tim mạch.';
        }

        if (statusEl) {
            statusEl.textContent = status;
            statusEl.style.color = color;
        }
        if (recEl) recEl.textContent = advice;
        if (circleEl) circleEl.style.borderColor = color;
    });
}

/* ==================== 8. MODAL ENGINE & VIP TRIAL BOOKING ==================== */
function initModals() {
    // Open Booking Modal
    document.querySelectorAll('[data-open-modal="booking-modal"]').forEach(btn => {
        btn.addEventListener('click', (e) => {
            e.preventDefault();
            const modal = document.getElementById('booking-modal');
            if (modal) modal.classList.add('active');
        });
    });

    // Close Modal Buttons
    document.querySelectorAll('.modal-close-btn, .modal-overlay').forEach(el => {
        el.addEventListener('click', function(e) {
            if (e.target === this || e.target.closest('.modal-close-btn')) {
                const modal = this.closest('.modal-overlay') || this;
                modal.classList.remove('active');
            }
        });
    });

    // VIP Booking Form Submit AJAX
    const bookingForm = document.getElementById('vip-booking-form');
    if (bookingForm) {
        bookingForm.addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            const originalText = btn.innerHTML;
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Đang đăng ký...';
            btn.disabled = true;

            const formData = new FormData(this);
            formData.append('action', 'book_trial');

            fetch('ajax-cart.php', {
                method: 'POST',
                body: formData
            })
            .then(res => res.json())
            .then(data => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                if (data.status === 'success') {
                    showToast('success', data.message || 'Đăng ký trải nghiệm VIP thành công! Chuyên viên HieuMini sẽ liên hệ ngay.');
                    bookingForm.reset();
                    const modal = document.getElementById('booking-modal');
                    if (modal) modal.classList.remove('active');
                } else {
                    showToast('error', data.message || 'Không thể đăng ký, vui lòng thử lại.');
                }
            })
            .catch(() => {
                btn.innerHTML = originalText;
                btn.disabled = false;
                showToast('error', 'Lỗi kết nối máy chủ!');
            });
        });
    }
}

/* ==================== 9. PRODUCT TABS FILTER ==================== */
function initProductTabs() {
    const tabBtns = document.querySelectorAll('.tab-btn');
    const productCards = document.querySelectorAll('.product-tab-item');
    if (!tabBtns.length || !productCards.length) return;

    tabBtns.forEach(btn => {
        btn.addEventListener('click', function() {
            tabBtns.forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const catId = this.getAttribute('data-category');

            productCards.forEach(card => {
                if (catId === 'all' || card.getAttribute('data-category') === catId) {
                    card.style.display = 'flex';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });
}

/* ==================== 10. TOAST NOTIFICATION POPUP ==================== */
function showToast(type, message) {
    let container = document.querySelector('.toast-container');
    if (!container) {
        container = document.createElement('div');
        container.className = 'toast-container';
        document.body.appendChild(container);
    }

    const toast = document.createElement('div');
    toast.className = `toast-item ${type}`;
    
    let iconClass = 'fa-check-circle';
    if (type === 'error') iconClass = 'fa-exclamation-circle';
    if (type === 'warning') iconClass = 'fa-exclamation-triangle';
    if (type === 'info') iconClass = 'fa-info-circle';

    toast.innerHTML = `
        <i class="fas ${iconClass}" style="font-size: 1.25rem;"></i>
        <div style="flex-grow: 1; font-size: 0.95rem; font-weight: 600;">${message}</div>
        <button type="button" style="background:none; border:none; color: #94a3b8; cursor:pointer; font-size: 1rem;" onclick="this.parentElement.remove()">
            <i class="fas fa-times"></i>
        </button>
    `;

    container.appendChild(toast);

    setTimeout(() => {
        toast.style.opacity = '0';
        toast.style.transform = 'translateX(110%)';
        toast.style.transition = 'all 0.4s ease';
        setTimeout(() => toast.remove(), 400);
    }, 4500);
}
