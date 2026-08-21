<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - ADVANCED BODY COMPOSITION & BMI/TDEE CALCULATOR
 * Standard: CEO Executive Edition
 */
require_once __DIR__ . '/includes/config.php';

$page_title = "Công Cụ Đo Chỉ Số Thể Hình & Tính Calorie CEO | " . SITE_NAME;
$page_desc = "Đo lường chính xác chỉ số BMI, BMR, TDEE và xây dựng tỷ lệ dinh dưỡng Macros cá nhân hóa chuẩn y khoa thể thao.";

// Lấy 4 sản phẩm dinh dưỡng nổi bật
$supplements = $pdo->query("SELECT * FROM products WHERE category_id = 3 ORDER BY id ASC LIMIT 4")->fetchAll();

require_once __DIR__ . '/includes/header.php';
?>

<div class="container" style="padding-top: 3.5rem; padding-bottom: 5rem;">
    <!-- Page Header -->
    <div style="text-align: center; max-width: 800px; margin: 0 auto 4rem;" class="reveal">
        <span class="section-tag">CHẨN ĐOÁN THỂ LỰC KHOA HỌC</span>
        <h1 style="font-size: 3rem; font-weight: 900; margin-bottom: 1.25rem;">
            TÍNH CHỈ SỐ THỂ HÌNH & MACROS CEO
        </h1>
        <p style="color: var(--text-secondary); font-size: 1.15rem; line-height: 1.8;">
            Thuật toán Mifflin-St Jeor y khoa chuẩn xác giúp tính toán mức tiêu hao năng lượng TDEE và phân bổ Protein/Carb/Fat tối ưu cho mục tiêu thể lực của bạn.
        </p>
    </div>

    <!-- Advanced Calculator Layout -->
    <div class="bmi-widget-card reveal" style="grid-template-columns: 1.1fr 1fr; margin-bottom: 4rem;">
        <!-- Left: Input Form -->
        <div>
            <h3 style="font-size: 1.35rem; color: var(--gold-light); margin-bottom: 1.5rem;">
                <i class="fas fa-sliders-h"></i> Nhập Chỉ Số Cá Nhân
            </h3>

            <form id="adv-calc-form">
                <div class="bmi-input-row">
                    <div class="form-group">
                        <label>Chiều Cao (cm) (*)</label>
                        <input type="number" id="calc-height" class="form-control" value="175" min="100" max="230" required>
                    </div>
                    <div class="form-group">
                        <label>Cân Nặng (kg) (*)</label>
                        <input type="number" id="calc-weight" class="form-control" value="72" min="30" max="200" required>
                    </div>
                </div>

                <div class="bmi-input-row">
                    <div class="form-group">
                        <label>Độ Tuổi (*)</label>
                        <input type="number" id="calc-age" class="form-control" value="32" min="15" max="85" required>
                    </div>
                    <div class="form-group">
                        <label>Giới Tính (*)</label>
                        <select id="calc-gender" class="form-control">
                            <option value="male">Nam Doanh Nhân</option>
                            <option value="female">Nữ Doanh Nhân</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label>Mức Độ Vận Động Hàng Ngày (*)</label>
                    <select id="calc-activity" class="form-control">
                        <option value="1.2">Ít vận động (Làm việc văn phòng, ngồi nhiều)</option>
                        <option value="1.375" selected>Vận động nhẹ (Tập thể thao 1-3 buổi/tuần)</option>
                        <option value="1.55">Vận động vừa phải (Tập luyện 3-5 buổi/tuần)</option>
                        <option value="1.725">Vận động cường độ cao (Tập 6-7 buổi/tuần)</option>
                        <option value="1.9">Vận động viên / CEO tập 2 buổi/ngày</option>
                    </select>
                </div>

                <div class="form-group">
                    <label>Mục Tiêu Thể Hình Mong Muốn (*)</label>
                    <select id="calc-goal" class="form-control">
                        <option value="lose">Giảm Mỡ & Xiết Cơ Bụng (Deficit 500 kcal)</option>
                        <option value="maintain" selected>Duy Trì Phong Độ Đỉnh Cao & Giữ Cân</option>
                        <option value="gain">Tăng Cơ Bắp Nạc Hypertrophy (Surplus 300 kcal)</option>
                    </select>
                </div>

                <button type="button" class="btn btn-primary btn-block btn-lg btn-shimmer" onclick="calculateAdvancedFitness()" style="margin-top: 1.5rem;">
                    <i class="fas fa-bolt"></i> TÍNH TOÁN KẾ HOẠCH DINH DƯỠNG NGAY
                </button>
            </form>
        </div>

        <!-- Right: Diagnostic Output -->
        <div class="bmi-result-panel" style="padding: 2rem;">
            <div style="display: flex; gap: 1.5rem; justify-content: center; margin-bottom: 1.5rem;">
                <div style="text-align: center;">
                    <div class="bmi-score-circle" id="adv-bmi-circle" style="width: 110px; height: 110px; margin: 0 auto 0.5rem;">
                        <span class="bmi-number" id="adv-bmi-val" style="font-size: 1.8rem;">23.5</span>
                    </div>
                    <span style="font-size: 0.8rem; color: var(--text-secondary);">CHỈ SỐ BMI</span>
                </div>

                <div style="text-align: center;">
                    <div class="bmi-score-circle" style="width: 110px; height: 110px; margin: 0 auto 0.5rem; border-color: var(--cyan-accent);">
                        <span class="bmi-number" id="adv-tdee-val" style="font-size: 1.5rem; color: var(--cyan-accent);">2.340</span>
                    </div>
                    <span style="font-size: 0.8rem; color: var(--text-secondary);">CALO TDEE (KCAL)</span>
                </div>
            </div>

            <h4 id="adv-status-title" style="font-size: 1.25rem; font-weight: 800; color: var(--emerald-accent); margin-bottom: 0.75rem;">
                Thể Trạng: Chuẩn Body VIP / Cân Đối
            </h4>

            <div style="background: rgba(255,255,255,0.03); border: 1px solid var(--border-subtle); border-radius: 8px; padding: 1.25rem; width: 100%; margin-bottom: 1.5rem; text-align: left;">
                <div style="font-size: 0.85rem; color: var(--gold-light); font-weight: 700; margin-bottom: 0.75rem; text-transform: uppercase;">
                    <i class="fas fa-utensils"></i> PHÂN BỔ DINH DƯỠNG HÀNG NGÀY (MACROS):
                </div>
                <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 0.75rem; text-align: center;">
                    <div style="background: rgba(245,158,11,0.1); padding: 0.5rem; border-radius: 4px;">
                        <span style="font-size: 0.75rem; color: var(--text-secondary); display: block;">ĐẠM (PROTEIN)</span>
                        <strong id="macro-protein" style="color: var(--gold-light); font-size: 1.1rem;">150g</strong>
                    </div>
                    <div style="background: rgba(6,182,212,0.1); padding: 0.5rem; border-radius: 4px;">
                        <span style="font-size: 0.75rem; color: var(--text-secondary); display: block;">TINH BỘT (CARB)</span>
                        <strong id="macro-carb" style="color: var(--cyan-accent); font-size: 1.1rem;">260g</strong>
                    </div>
                    <div style="background: rgba(16,185,129,0.1); padding: 0.5rem; border-radius: 4px;">
                        <span style="font-size: 0.75rem; color: var(--text-secondary); display: block;">CHẤT BÉO (FAT)</span>
                        <strong id="macro-fat" style="color: var(--emerald-accent); font-size: 1.1rem;">65g</strong>
                    </div>
                </div>
            </div>

            <p id="adv-rec-text" style="color: var(--text-secondary); font-size: 0.85rem; line-height: 1.6; margin-bottom: 1.25rem;">
                Mục tiêu của bạn yêu cầu nạp đủ 150g protein/ngày để duy trì khối lượng cơ nạc. Kết hợp bổ sung Whey Isolate sau buổi tập để hấp thu tối đa.
            </p>

            <button type="button" class="btn btn-gold-outline btn-sm" data-open-modal="booking-modal">
                <i class="fas fa-crown"></i> Nhận Tư Vấn Thực Đơn Chi Tiết 1:1
            </button>
        </div>
    </div>

    <!-- Recommended Supplements for the User -->
    <div class="reveal">
        <div class="section-header" style="text-align: left; margin-bottom: 2rem;">
            <span class="section-tag">DINH DƯỠNG KHUYẾN NGHỊ</span>
            <h2 class="section-title" style="font-size: 2rem;">THỰC PHẨM BỔ SUNG NÊN DÙNG</h2>
        </div>

        <div class="products-grid">
            <?php foreach ($supplements as $sup): ?>
            <div class="product-card">
                <div class="product-thumb-wrap">
                    <img src="<?= BASE_URL ?>/assets/images/products/<?= htmlspecialchars($sup['image']) ?>" alt="<?= htmlspecialchars($sup['name']) ?>">
                    <div class="product-quick-actions">
                        <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $sup['id'] ?>" class="action-btn-circle"><i class="fas fa-eye"></i></a>
                        <button type="button" class="action-btn-circle add-cart-btn-direct" data-id="<?= $sup['id'] ?>"><i class="fas fa-shopping-cart"></i></button>
                    </div>
                </div>
                <div class="product-body">
                    <h4 class="product-title">
                        <a href="<?= BASE_URL ?>/product-detail.php?id=<?= $sup['id'] ?>"><?= htmlspecialchars($sup['name']) ?></a>
                    </h4>
                    <div class="product-price-row">
                        <span class="product-price-current"><?= format_currency($sup['price']) ?></span>
                        <button type="button" class="add-cart-btn-direct" data-id="<?= $sup['id'] ?>">Chọn</button>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</div>

<script>
function calculateAdvancedFitness() {
    const h = parseFloat(document.getElementById('calc-height').value) || 175;
    const w = parseFloat(document.getElementById('calc-weight').value) || 72;
    const age = parseInt(document.getElementById('calc-age').value, 10) || 32;
    const gender = document.getElementById('calc-gender').value;
    const act = parseFloat(document.getElementById('calc-activity').value);
    const goal = document.getElementById('calc-goal').value;

    const hm = h / 100;
    const bmi = (w / (hm * hm)).toFixed(1);

    // BMR (Mifflin-St Jeor)
    let bmr = 0;
    if (gender === 'male') {
        bmr = 10 * w + 6.25 * h - 5 * age + 5;
    } else {
        bmr = 10 * w + 6.25 * h - 5 * age - 161;
    }

    let tdee = bmr * act;
    if (goal === 'lose') tdee -= 450;
    if (goal === 'gain') tdee += 350;

    tdee = Math.round(tdee);

    // Macros: Protein 2g/kg bodyweight, Fat 25% of calories, rest Carbs
    const proteinGrams = Math.round(w * 2.1);
    const fatGrams = Math.round((tdee * 0.25) / 9);
    const carbGrams = Math.max(50, Math.round((tdee - (proteinGrams * 4 + fatGrams * 9)) / 4));

    document.getElementById('adv-bmi-val').textContent = bmi;
    document.getElementById('adv-tdee-val').textContent = tdee.toLocaleString('vi-VN');
    document.getElementById('macro-protein').textContent = proteinGrams + 'g';
    document.getElementById('macro-carb').textContent = carbGrams + 'g';
    document.getElementById('macro-fat').textContent = fatGrams + 'g';

    let status = 'Chuẩn Body VIP / Cân Đối';
    let color = '#10b981';
    if (bmi < 18.5) { status = 'Thiếu Cân / Cần Tăng Cơ'; color = '#06b6d4'; }
    else if (bmi >= 25 && bmi < 30) { status = 'Thừa Cân / Cần Giảm Mỡ'; color = '#f59e0b'; }
    else if (bmi >= 30) { status = 'Béo Phì / Nguy Cơ Cao'; color = '#ef4444'; }

    const stEl = document.getElementById('adv-status-title');
    stEl.textContent = 'Thể Trạng: ' + status;
    stEl.style.color = color;
    document.getElementById('adv-bmi-circle').style.borderColor = color;

    showToast('success', 'Đã phân tích xong các chỉ số thể hình & Macros!');
}
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
