/**
 * ==========================================================
 * HIEUMINI TECH STORE - JAVASCRIPT CHO ADMIN DASHBOARD
 * ==========================================================
 */

document.addEventListener('DOMContentLoaded', () => {
    // 1. Xác nhận khi xóa sản phẩm / danh mục
    document.querySelectorAll('.btn-delete-confirm').forEach(btn => {
        btn.addEventListener('click', function(e) {
            if (!confirm('Bạn có chắc chắn muốn xóa bản ghi này không? Hành động này không thể hoàn tác!')) {
                e.preventDefault();
            }
        });
    });

    // 2. Preview hình ảnh trước khi Upload
    const imageInput = document.getElementById('image-upload-input');
    const imagePreview = document.getElementById('image-preview-target');
    if (imageInput && imagePreview) {
        imageInput.addEventListener('change', function() {
            const file = this.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    imagePreview.src = e.target.result;
                    imagePreview.style.display = 'block';
                };
                reader.readAsDataURL(file);
            }
        });
    }

    // 3. Vẽ biểu đồ doanh thu đơn giản bằng HTML5 Canvas
    const canvas = document.getElementById('revenueChart');
    if (canvas) {
        const ctx = canvas.getContext('2d');
        const labels = ['T2', 'T3', 'T4', 'T5', 'T6', 'T7', 'CN'];
        const data = [35, 52, 48, 75, 90, 120, 145]; // Triệu VNĐ

        // Thiết lập kích thước
        canvas.width = canvas.parentElement.clientWidth;
        canvas.height = 240;

        const maxVal = Math.max(...data) * 1.2;
        const padX = 40;
        const padY = 30;
        const graphW = canvas.width - padX * 2;
        const graphH = canvas.height - padY * 2;
        const stepX = graphW / (data.length - 1);

        // Vẽ gradient background
        const grad = ctx.createLinearGradient(0, padY, 0, canvas.height - padY);
        grad.addColorStop(0, 'rgba(99, 102, 241, 0.4)');
        grad.addColorStop(1, 'rgba(99, 102, 241, 0.0)');

        // Bắt đầu vẽ vùng đổ bóng
        ctx.beginPath();
        data.forEach((val, i) => {
            const x = padX + i * stepX;
            const y = canvas.height - padY - (val / maxVal) * graphH;
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.lineTo(padX + (data.length - 1) * stepX, canvas.height - padY);
        ctx.lineTo(padX, canvas.height - padY);
        ctx.closePath();
        ctx.fillStyle = grad;
        ctx.fill();

        // Vẽ đường line chính
        ctx.beginPath();
        data.forEach((val, i) => {
            const x = padX + i * stepX;
            const y = canvas.height - padY - (val / maxVal) * graphH;
            if (i === 0) ctx.moveTo(x, y);
            else ctx.lineTo(x, y);
        });
        ctx.strokeStyle = '#6366f1';
        ctx.lineWidth = 3;
        ctx.stroke();

        // Vẽ các điểm mốc (Points) và nhãn ngày
        data.forEach((val, i) => {
            const x = padX + i * stepX;
            const y = canvas.height - padY - (val / maxVal) * graphH;

            // Điểm tròn
            ctx.beginPath();
            ctx.arc(x, y, 5, 0, Math.PI * 2);
            ctx.fillStyle = '#38bdf8';
            ctx.fill();
            ctx.strokeStyle = '#0f172a';
            ctx.lineWidth = 2;
            ctx.stroke();

            // Nhãn nhãn trục X
            ctx.fillStyle = '#94a3b8';
            ctx.font = '12px sans-serif';
            ctx.textAlign = 'center';
            ctx.fillText(labels[i], x, canvas.height - 10);

            // Nhãn giá trị
            ctx.fillStyle = '#ffffff';
            ctx.font = 'bold 11px sans-serif';
            ctx.fillText(val + 'M', x, y - 10);
        });
    }
});
