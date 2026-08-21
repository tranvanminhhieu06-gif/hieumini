/**
 * ==========================================================
 * HIEUMINI TECH STORE - JAVASCRIPT XỬ LÝ TƯƠNG TÁC FRONTEND
 * ==========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Khởi tạo Toast Container
    let toastContainer = document.getElementById('toast-container');
    if (!toastContainer) {
        toastContainer = document.createElement('div');
        toastContainer.id = 'toast-container';
        document.body.appendChild(toastContainer);
    }

    // 2. Hàm hiển thị Toast thông báo hiện đại
    window.showToast = function(message, type = 'success') {
        const toast = document.createElement('div');
        toast.className = 'toast-msg animate-fade-in';
        const icon = type === 'success' ? 'fa-circle-check text-success' : 'fa-circle-exclamation text-danger';
        toast.innerHTML = `<i class="fa-solid ${icon}"></i> <span>${message}</span>`;
        toastContainer.appendChild(toast);

        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateY(10px)';
            toast.style.transition = 'all 0.3s ease';
            setTimeout(() => toast.remove(), 300);
        }, 3500);
    };

    // 3. Xử lý thêm vào giỏ hàng bằng AJAX
    document.querySelectorAll('.ajax-add-cart').forEach(btn => {
        btn.addEventListener('click', function(e) {
            e.preventDefault();
            const productId = this.getAttribute('data-id');
            const qtyInput = document.getElementById('product-qty');
            const quantity = qtyInput ? parseInt(qtyInput.value) : 1;

            fetch('cart.php?action=add', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `product_id=${productId}&quantity=${quantity}`
            })
            .then(res => res.json())
            .then(data => {
                if (data.status === 'success') {
                    showToast(data.message || 'Đã thêm sản phẩm vào giỏ hàng!', 'success');
                    // Cập nhật số lượng giỏ hàng trên Header badge
                    const badge = document.querySelector('.badge-count');
                    if (badge) {
                        badge.textContent = data.cart_count;
                        badge.style.display = 'inline-block';
                    }
                } else {
                    showToast(data.message || 'Có lỗi xảy ra, vui lòng thử lại!', 'error');
                }
            })
            .catch(err => {
                // Fallback nếu server không trả về JSON
                window.location.href = `cart.php?action=add&id=${productId}`;
            });
        });
    });

    // 4. Đồng hồ đếm ngược Flash Sale
    const countdownHours = document.getElementById('cd-hours');
    const countdownMins = document.getElementById('cd-mins');
    const countdownSecs = document.getElementById('cd-secs');

    if (countdownHours && countdownMins && countdownSecs) {
        let totalSeconds = 12 * 3600 + 45 * 60 + 30; // 12 giờ 45 phút
        setInterval(() => {
            if (totalSeconds <= 0) return;
            totalSeconds--;
            const h = Math.floor(totalSeconds / 3600);
            const m = Math.floor((totalSeconds % 3600) / 60);
            const s = totalSeconds % 60;
            countdownHours.textContent = String(h).padStart(2, '0');
            countdownMins.textContent = String(m).padStart(2, '0');
            countdownSecs.textContent = String(s).padStart(2, '0');
        }, 1000);
    }

    // 5. Thay đổi phương thức thanh toán tại trang Checkout
    const paymentMethods = document.querySelectorAll('input[name="payment_method"]');
    const qrSection = document.getElementById('bank-qr-details');
    if (paymentMethods.length > 0 && qrSection) {
        paymentMethods.forEach(radio => {
            radio.addEventListener('change', function() {
                if (this.value === 'bank_transfer' || this.value === 'momo') {
                    qrSection.style.display = 'block';
                } else {
                    qrSection.style.display = 'none';
                }
            });
        });
    }

    // 6. Xử lý tăng giảm số lượng trong Giỏ hàng
    document.querySelectorAll('.qty-adjust-btn').forEach(btn => {
        btn.addEventListener('click', function() {
            const input = this.parentElement.querySelector('.qty-input');
            let val = parseInt(input.value);
            if (this.classList.contains('plus')) {
                val++;
            } else if (this.classList.contains('minus') && val > 1) {
                val--;
            }
            input.value = val;
            const form = input.closest('form');
            if (form) form.submit();
        });
    });
});
