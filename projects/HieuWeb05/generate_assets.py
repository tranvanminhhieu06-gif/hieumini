import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

base_dir = r"c:\xampp\htdocs\HieuWeb05\assets\images"
prod_dir = os.path.join(base_dir, "products")
train_dir = os.path.join(base_dir, "trainers")

os.makedirs(prod_dir, exist_ok=True)
os.makedirs(train_dir, exist_ok=True)

def get_font(size, bold=False):
    font_paths = [
        r"C:\Windows\Fonts\arialbd.ttf" if bold else r"C:\Windows\Fonts\arial.ttf",
        r"C:\Windows\Fonts\segoeuib.ttf" if bold else r"C:\Windows\Fonts\segoeui.ttf",
        r"C:\Windows\Fonts\tahoma.ttf",
    ]
    for p in font_paths:
        if os.path.exists(p):
            try:
                return ImageFont.truetype(p, size)
            except:
                pass
    return ImageFont.load_default()

def create_radial_glow(width, height, center_x, center_y, radius, glow_color):
    """Tạo hiệu ứng ánh sáng tỏa tròn (Radial Glow/Lighting Shader)"""
    img = Image.new("RGBA", (width, height), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    for r in range(radius, 0, -6):
        alpha = int(glow_color[3] * (1 - (r / radius) ** 1.5))
        draw.ellipse(
            [(center_x - r, center_y - r), (center_x + r, center_y + r)],
            fill=(glow_color[0], glow_color[1], glow_color[2], alpha)
        )
    return img

def create_carbon_texture(width, height):
    """Tạo vân carbon fiber sợi kim loại cao cấp"""
    img = Image.new("RGBA", (width, height), (12, 14, 18, 255))
    draw = ImageDraw.Draw(img)
    step = 8
    for x in range(0, width, step):
        for y in range(0, height, step):
            if (x // step + y // step) % 2 == 0:
                draw.rectangle([(x, y), (x + step - 1, y + step - 1)], fill=(18, 22, 28, 255))
            else:
                draw.rectangle([(x, y), (x + step - 1, y + step - 1)], fill=(10, 12, 15, 255))
    return img

# ==================== 30 SẢN PHẨM & DỊCH VỤ FITNESS CHUẨN CEO ====================
products = [
    # Gói Hội Viên (Gold & Obsidian VIP Theme)
    {
        "filename": "01_membership_diamond.jpg",
        "category": "GÓI HỘI VIÊN VIP",
        "badge": "CEO DIAMOND 1 NĂM",
        "title": "HIEUMINI DIAMOND ELITE",
        "subtitle": "Đặc quyền thượng lưu 365 ngày 5 sao",
        "accent": (245, 158, 11),
        "symbol": "DIAMOND MEMBERSHIP",
        "tag": "24.500.000đ",
        "icon_text": "VIP 365D",
        "theme": "gold"
    },
    {
        "filename": "02_membership_gold.jpg",
        "category": "GÓI HỘI VIÊN VIP",
        "badge": "EXECUTIVE GOLD 6T",
        "title": "EXECUTIVE GOLD CLUB",
        "subtitle": "Không giới hạn khung giờ & Sauna VIP",
        "accent": (234, 179, 8),
        "symbol": "GOLD MEMBERSHIP",
        "tag": "13.900.000đ",
        "icon_text": "GOLD 180D",
        "theme": "gold"
    },
    {
        "filename": "03_membership_platinum.jpg",
        "category": "GÓI HỘI VIÊN VIP",
        "badge": "PLATINUM ALL-ACCESS",
        "title": "PLATINUM 90 DAYS",
        "subtitle": "90 Ngày bứt phá thể lực đỉnh cao",
        "accent": (148, 163, 184),
        "symbol": "PLATINUM VIP",
        "tag": "7.900.000đ",
        "icon_text": "PLATINUM",
        "theme": "silver"
    },
    {
        "filename": "04_day_pass.jpg",
        "category": "GÓI HỘI VIÊN VIP",
        "badge": "VIP DAY PASS",
        "title": "VIP 1-DAY EXPERIENCE",
        "subtitle": "Trải nghiệm trọn vẹn tiện ích 5 sao",
        "accent": (16, 185, 129),
        "symbol": "1 DAY PASS",
        "tag": "350.000đ",
        "icon_text": "DAY PASS",
        "theme": "emerald"
    },
    {
        "filename": "05_corporate_club.jpg",
        "category": "GÓI HỘI VIÊN VIP",
        "badge": "CORPORATE ELITE",
        "title": "DOANH NGHIỆP 5 TV",
        "subtitle": "Giải pháp sức khỏe cho ban lãnh đạo",
        "accent": (99, 102, 241),
        "symbol": "CORPORATE PASS",
        "tag": "39.900.000đ",
        "icon_text": "CORPORATE",
        "theme": "indigo"
    },

    # Thiết Bị & Máy Tập Thể Hình (Cyan & Heavy Steel Theme)
    {
        "filename": "06_commercial_treadmill.jpg",
        "category": "THIẾT BỊ PHÒNG GYM",
        "badge": "COMMERCIAL X9 PRO",
        "title": "MÁY CHẠY BỘ THƯƠNG MẠI",
        "subtitle": "Động cơ AC 6.0 HP | Màn hình 21.5\" 4K",
        "accent": (6, 182, 212),
        "symbol": "TREADMILL AC 6.0",
        "tag": "59.900.000đ",
        "icon_text": "AC 6.0 HP",
        "theme": "cyan"
    },
    {
        "filename": "07_power_rack.jpg",
        "category": "THIẾT BỊ PHÒNG GYM",
        "badge": "MONSTER POWER RACK",
        "title": "KHUNG GÁNH ĐA NĂNG PRO",
        "subtitle": "Thép hộp 75x75mm dày 3.5mm tải 1000kg",
        "accent": (6, 182, 212),
        "symbol": "POWER RACK 1000KG",
        "tag": "36.500.000đ",
        "icon_text": "1000KG MAX",
        "theme": "cyan"
    },
    {
        "filename": "08_smith_machine.jpg",
        "category": "THIẾT BỊ PHÒNG GYM",
        "badge": "SMITH MACHINE 3D",
        "title": "MÁY SMITH 3D CAO CẤP",
        "subtitle": "Ray trượt bi hợp kim tuyến tính đa hướng",
        "accent": (14, 165, 233),
        "symbol": "SMITH 3D LINEAR",
        "tag": "32.900.000đ",
        "icon_text": "3D SYSTEM",
        "theme": "cyan"
    },
    {
        "filename": "09_cable_crossover.jpg",
        "category": "THIẾT BỊ PHÒNG GYM",
        "badge": "DUAL CABLE CROSS",
        "title": "GIÀN KÉO CÁP ĐÔI 200KG",
        "subtitle": "Tạ lá 200kg | Ròng rọc nhôm CNC xoay 180°",
        "accent": (6, 182, 212),
        "symbol": "DUAL CABLE 200KG",
        "tag": "46.000.000đ",
        "icon_text": "200KG CNC",
        "theme": "cyan"
    },
    {
        "filename": "10_olympic_barbell_set.jpg",
        "category": "THIẾT BỊ PHÒNG GYM",
        "badge": "OLYMPIC BARBELL SET",
        "title": "BỘ ĐÒN TẠ & BÁNH 150KG",
        "subtitle": "Thép đàn hồi 215k PSI chuẩn thi đấu IWF",
        "accent": (245, 158, 11),
        "symbol": "BARBELL & BUMPERS",
        "tag": "16.200.000đ",
        "icon_text": "150KG SET",
        "theme": "gold"
    },
    {
        "filename": "11_smart_dumbbells.jpg",
        "category": "THIẾT BỊ PHÒNG GYM",
        "badge": "SMART QUICKLOCK 40KG",
        "title": "TẠ ĐƠN THÔNG MINH 40KG",
        "subtitle": "Xoay khóa 1s thay thế 16 cặp tạ thường",
        "accent": (6, 182, 212),
        "symbol": "QUICKLOCK 40KG",
        "tag": "10.800.000đ",
        "icon_text": "40KG SMART",
        "theme": "cyan"
    },
    {
        "filename": "12_water_rower.jpg",
        "category": "THIẾT BỊ PHÒNG GYM",
        "badge": "WATER ROWER PRO",
        "title": "MÁY CHÈO KHÁNG LỰC NƯỚC",
        "subtitle": "Gỗ sồi Bắc Mỹ | Đốt 1000 Calo/giờ",
        "accent": (59, 130, 246),
        "symbol": "WATER ROWER",
        "tag": "22.900.000đ",
        "icon_text": "OAK WOOD",
        "theme": "blue"
    },
    {
        "filename": "13_kettlebell_set.jpg",
        "category": "THIẾT BỊ PHÒNG GYM",
        "badge": "COMPETITION KETTLEBELL",
        "title": "TẠ ẤM THI ĐẤU 24KG",
        "subtitle": "Gang đúc nguyên khối CNC sơn mờ nhám",
        "accent": (16, 185, 129),
        "symbol": "KETTLEBELL 24KG",
        "tag": "2.650.000đ",
        "icon_text": "24KG CAST",
        "theme": "emerald"
    },

    # Dinh Dưỡng Thể Hình & Thực Phẩm Bổ Sung (Ruby / Amber Luxury Jar Theme)
    {
        "filename": "14_whey_isolate.jpg",
        "category": "DINH DƯỠNG THỂ HÌNH",
        "badge": "100% HYDROLYZED WHEY",
        "title": "HIEUMINI WHEY ISOLATE 5LBS",
        "subtitle": "28g Protein tinh khiết | 0 Đường 0 Fat",
        "accent": (245, 158, 11),
        "symbol": "HYDROLYZED WHEY",
        "tag": "2.150.000đ",
        "icon_text": "28G PROTEIN",
        "theme": "gold"
    },
    {
        "filename": "15_creatine_creapure.jpg",
        "category": "DINH DƯỠNG THỂ HÌNH",
        "badge": "CREAPURE® GERMANY",
        "title": "CREATINE CREAPURE 500G",
        "subtitle": "Độ tinh khiết 99.99% tăng sức mạnh bùng nổ",
        "accent": (16, 185, 129),
        "symbol": "PURE CREAPURE",
        "tag": "820.000đ",
        "icon_text": "99.99% PURE",
        "theme": "emerald"
    },
    {
        "filename": "16_pre_workout.jpg",
        "category": "DINH DƯỠNG THỂ HÌNH",
        "badge": "EXPLOSIVE ENERGY",
        "title": "PRE-WORKOUT ENERGY BOOSTER",
        "subtitle": "300mg Caffeine | 6g Citrulline | Bơm cơ đỉnh cao",
        "accent": (239, 68, 68),
        "symbol": "PRE-WORKOUT 60SV",
        "tag": "990.000đ",
        "icon_text": "300MG CAFF",
        "theme": "ruby"
    },
    {
        "filename": "17_bcaa_eaa.jpg",
        "category": "DINH DƯỠNG THỂ HÌNH",
        "badge": "EAA + BCAA MATRIX",
        "title": "PHỤC HỒI CƠ BCAA MATRIX",
        "subtitle": "9 EAA thiết yếu + Nước dừa bù khoáng",
        "accent": (168, 85, 247),
        "symbol": "EAA & BCAA 30SV",
        "tag": "1.080.000đ",
        "icon_text": "9 EAA MATRIX",
        "theme": "purple"
    },
    {
        "filename": "18_mass_gainer.jpg",
        "category": "DINH DƯỠNG THỂ HÌNH",
        "badge": "MASS GAINER 12LBS",
        "title": "SỮA TĂNG CÂN MASS GAINER",
        "subtitle": "1250 Calo | 55g Protein đa tầng cao cấp",
        "accent": (245, 158, 11),
        "symbol": "MASS GAINER 12LBS",
        "tag": "1.950.000đ",
        "icon_text": "1250 KCAL",
        "theme": "gold"
    },
    {
        "filename": "19_omega3_fishoil.jpg",
        "category": "DINH DƯỠNG THỂ HÌNH",
        "badge": "TRIPLE STRENGTH OMEGA-3",
        "title": "DẦU CÁ OMEGA-3 GOLD",
        "subtitle": "1000mg EPA / 500mg DHA | Tim mạch & Khớp",
        "accent": (234, 179, 8),
        "symbol": "OMEGA-3 120 SOFTGELS",
        "tag": "650.000đ",
        "icon_text": "1500MG EPA/DHA",
        "theme": "gold"
    },
    {
        "filename": "20_multivitamin.jpg",
        "category": "DINH DƯỠNG THỂ HÌNH",
        "badge": "CEO ELITE MULTI",
        "title": "VITAMIN TỔNG HỢP & SÂM",
        "subtitle": "30+ Vi chất dinh dưỡng & Chiết xuất Maca",
        "accent": (16, 185, 129),
        "symbol": "MULTI VITAMIN 90T",
        "tag": "720.000đ",
        "icon_text": "30+ VITAMINS",
        "theme": "emerald"
    },
    {
        "filename": "21_protein_bars.jpg",
        "category": "DINH DƯỠNG THỂ HÌNH",
        "badge": "PROTEIN BARS 12 PACK",
        "title": "BÁNH PROTEIN BAR THƯỢNG HẠNG",
        "subtitle": "20g Protein | Phủ socola Bỉ giòn tan",
        "accent": (217, 119, 6),
        "symbol": "PROTEIN BAR 12X",
        "tag": "620.000đ",
        "icon_text": "20G PROTEIN",
        "theme": "gold"
    },

    # Phụ Kiện & Trang Phục Tập (Leather & Carbon Theme)
    {
        "filename": "22_lever_belt.jpg",
        "category": "PHỤ KIỆN TẬP LUYỆN",
        "badge": "LEATHER LEVER BELT 13MM",
        "title": "ĐAI LƯNG CỨNG DA BÒ THẬT",
        "subtitle": "Khóa đòn bẩy thép CNC chuẩn thi đấu IPF",
        "accent": (245, 158, 11),
        "symbol": "IPF LEVER BELT",
        "tag": "1.390.000đ",
        "icon_text": "13MM LEATHER",
        "theme": "gold"
    },
    {
        "filename": "23_knee_sleeves.jpg",
        "category": "PHỤ KIỆN TẬP LUYỆN",
        "badge": "NEOPRENE SLEEVES 7MM",
        "title": "BĂNG GỐI TRỢ LỰC 7MM",
        "subtitle": "Bảo vệ khớp gối & Bứt phá mức tạ Squat",
        "accent": (6, 182, 212),
        "symbol": "KNEE SLEEVES 7MM",
        "tag": "990.000đ",
        "icon_text": "7MM NEOPRENE",
        "theme": "cyan"
    },
    {
        "filename": "24_deadlift_straps.jpg",
        "category": "PHỤ KIỆN TẬP LUYỆN",
        "badge": "FIGURE-8 STRAPS",
        "title": "DÂY KÉO LƯNG FIGURE-8",
        "subtitle": "Sợi Cotton đan 4 lớp chịu lực kéo 600kg",
        "accent": (239, 68, 68),
        "symbol": "FIGURE-8 STRAPS",
        "tag": "350.000đ",
        "icon_text": "600KG RATED",
        "theme": "ruby"
    },
    {
        "filename": "25_steel_shaker.jpg",
        "category": "PHỤ KIỆN TẬP LUYỆN",
        "badge": "STEEL SHAKER 800ML",
        "title": "BÌNH LẮC INOX GIỮ NHIỆT 24H",
        "subtitle": "Thép 304 không gỉ | Nắp chống rò rỉ 100%",
        "accent": (148, 163, 184),
        "symbol": "STEEL SHAKER 800ML",
        "tag": "420.000đ",
        "icon_text": "INOX 304 24H",
        "theme": "silver"
    },
    {
        "filename": "26_dryfit_tee.jpg",
        "category": "PHỤ KIỆN TẬP LUYỆN",
        "badge": "DRY-FIT PRO TEE",
        "title": "ÁO TẬP NAM DRY-FIT CEO",
        "subtitle": "Vải sợi Poly-Spandex co giãn 4 chiều",
        "accent": (16, 185, 129),
        "symbol": "DRY-FIT TEE",
        "tag": "390.000đ",
        "icon_text": "4-WAY STRETCH",
        "theme": "emerald"
    },

    # Huấn Luyện Viên & Trị Liệu Thể Thao (Royal Purple / Emerald Theme)
    {
        "filename": "27_master_trainer.jpg",
        "category": "HUẤN LUYỆN VIÊN 1:1",
        "badge": "MASTER COACH VIP",
        "title": "GÓI PT 1:1 MASTER COACH (30B)",
        "subtitle": "Huấn luyện viên chứng chỉ NASM/ISSA quốc tế",
        "accent": (245, 158, 11),
        "symbol": "MASTER PT 30 SESSIONS",
        "tag": "18.500.000đ",
        "icon_text": "NASM COACH",
        "theme": "gold"
    },
    {
        "filename": "28_sports_therapy.jpg",
        "category": "TRỊ LIỆU THỂ THAO",
        "badge": "MYOFASCIAL RELEASE",
        "title": "LIỆU TRÌNH GIÃN CƠ & TRỊ LIỆU",
        "subtitle": "Giải phóng căng cứng đốt sống cổ & vai gáy",
        "accent": (16, 185, 129),
        "symbol": "THERAPY 10 SESSIONS",
        "tag": "6.900.000đ",
        "icon_text": "THERAGUN PRO",
        "theme": "emerald"
    },
    {
        "filename": "29_inbody_scan.jpg",
        "category": "TƯ VẤN THỂ LỰC",
        "badge": "INBODY 770 SCAN",
        "title": "ĐO INBODY 770 & THỰC ĐƠN",
        "subtitle": "Phân tích 6 tần số y khoa & Lập khẩu phần",
        "accent": (6, 182, 212),
        "symbol": "INBODY 770 SCAN",
        "tag": "990.000đ",
        "icon_text": "6-FREQ SCAN",
        "theme": "cyan"
    },
    {
        "filename": "30_yoga_meditation.jpg",
        "category": "YOGA & THIỀN ĐỊNH",
        "badge": "EXECUTIVE YOGA 20B",
        "title": "YOGA & THIỀN DOANH NHÂN",
        "subtitle": "Phòng tập riêng tư | Phục hồi năng lượng lãnh đạo",
        "accent": (168, 85, 247),
        "symbol": "YOGA 20 SESSIONS",
        "tag": "13.900.000đ",
        "icon_text": "ZEN STUDIO",
        "theme": "purple"
    }
]

print("Rendering 30 Ultra-Luxury AI-Style Product Images (800x800)...")

W, H = 800, 800
font_brand = get_font(20, bold=True)
font_cat = get_font(13, bold=True)
font_title = get_font(28, bold=True)
font_sub = get_font(16)
font_symbol = get_font(30, bold=True)
font_badge = get_font(15, bold=True)
font_icon = get_font(32, bold=True)

for idx, p in enumerate(products, 1):
    accent = p["accent"]
    
    # 1. Base Carbon Fiber Background
    img = create_carbon_texture(W, H)
    
    # 2. Lighting Shaders & Radial Glow
    glow_top = create_radial_glow(W, H, W // 2, 200, 320, (accent[0], accent[1], accent[2], 85))
    glow_bot = create_radial_glow(W, H, 180, 700, 260, (accent[0], accent[1], accent[2], 55))
    img = Image.alpha_composite(img, glow_top)
    img = Image.alpha_composite(img, glow_bot)
    
    draw = ImageDraw.Draw(img)
    
    # 3. Outer Luxury Metallic Double Frame
    draw.rectangle([(18, 18), (W - 18, H - 18)], outline=(accent[0], accent[1], accent[2], 180), width=2)
    draw.rectangle([(26, 26), (W - 26, H - 26)], outline=(255, 255, 255, 40), width=1)
    
    # Corner Accents
    c_len = 30
    for cx, cy in [(26, 26), (W - 26, 26), (26, H - 26), (W - 26, H - 26)]:
        draw.line([(cx - 10, cy), (cx + 10, cy)], fill=accent, width=3)
        draw.line([(cx, cy - 10), (cx, cy + 10)], fill=accent, width=3)
    
    # 4. Header Bar: Logo & SKU
    draw.rounded_rectangle([(45, 45), (140, 80)], radius=6, fill=(245, 158, 11, 240))
    draw.text((92, 62), "HIEUMINI", fill=(10, 12, 16), font=get_font(14, bold=True), anchor="mm")
    
    draw.text((155, 58), "LUXURY FITNESS CLUB", fill=(255, 255, 255, 220), font=get_font(14, bold=True))
    draw.text((W - 180, 58), f"SKU: {p['filename'][:2].upper()}-{idx:02d}", fill=(160, 174, 192), font=get_font(14, bold=True))
    
    # Category Tag
    draw.rounded_rectangle([(45, 95), (280, 128)], radius=6, fill=(22, 28, 38, 220), outline=accent, width=1)
    draw.text((60, 102), p["category"], fill=(245, 245, 245), font=font_cat)
    
    # 5. Center 3D Object Rendering Stage
    center_y = 380
    
    # Orbital Glow Rings
    for r in [170, 140, 110]:
        alpha = int(60 * (r / 170))
        draw.ellipse([(W//2 - r, center_y - r), (W//2 + r, center_y + r)], outline=(accent[0], accent[1], accent[2], alpha), width=2)
    
    # Central 3D Glassmorphism Showcase Box
    box_w, box_h = 480, 250
    box_x1 = (W - box_w) // 2
    box_y1 = center_y - box_h // 2
    draw.rounded_rectangle([(box_x1, box_y1), (box_x1 + box_w, box_y1 + box_h)], radius=20, fill=(18, 23, 32, 235), outline=accent, width=2)
    
    # Specular Top Highlight
    draw.line([(box_x1 + 25, box_y1 + 3), (box_x1 + box_w - 25, box_y1 + 3)], fill=(255, 255, 255, 120), width=2)
    
    # 5-Star Luxury Rating in Box
    draw.text((W//2, center_y - 65), "★ ★ ★ ★ ★", fill=accent, font=get_font(22), anchor="mm")
    
    # Product Main 3D Text Tag
    draw.text((W//2, center_y - 10), p["symbol"], fill=(255, 255, 255), font=font_symbol, anchor="mm")
    
    # Spec/Feature Icon Badge
    draw.rounded_rectangle([(W//2 - 160, center_y + 45), (W//2 + 160, center_y + 85)], radius=8, fill=(accent[0], accent[1], accent[2], 230))
    draw.text((W//2, center_y + 65), p["badge"], fill=(10, 12, 16), font=font_badge, anchor="mm")
    
    # 6. Lower Card Info: Title, Subtitle, Price
    draw.text((45, 560), p["title"], fill=(255, 255, 255), font=font_title)
    draw.text((45, 610), p["subtitle"], fill=(200, 215, 235), font=font_sub)
    
    # Luxury Price Badge
    price_box_w = 270
    draw.rounded_rectangle([(45, 670), (45 + price_box_w, 740)], radius=12, fill=(15, 20, 28, 250), outline=accent, width=2)
    draw.text((60, 680), "GIÁ ƯU ĐÃI DOANH NHÂN", fill=(160, 174, 192), font=get_font(12, bold=True))
    draw.text((60, 700), p["tag"], fill=accent, font=get_font(24, bold=True))
    
    # Official CEO Certification Stamp
    draw.text((W - 250, 690), "✔ CHÍNH HÃNG 100%", fill=(34, 197, 94), font=get_font(15, bold=True))
    draw.text((W - 250, 715), "✔ BẢO HÀNH 5 SAO CEO", fill=(245, 158, 11), font=get_font(13, bold=True))
    
    # Save Image
    out_path = os.path.join(prod_dir, p["filename"])
    rgb_img = Image.new("RGB", (W, H), (10, 12, 16))
    rgb_img.paste(img, (0, 0), img)
    rgb_img.save(out_path, "JPEG", quality=95)
    print(f"[{idx:02d}/30] Successfully rendered: {p['filename']}")

# ==================== TẠO 5 ẢNH DANH MỤC (CATEGORY COVERS) ====================
print("Rendering 5 Category Covers...")
categories = [
    ("cat_membership.jpg", "GÓI HỘI VIÊN VIP", "Đặc quyền 365 ngày thượng lưu 5 sao", (245, 158, 11)),
    ("cat_equipment.jpg", "THIẾT BỊ PHÒNG GYM", "Máy tập Olympic nhập khẩu Mỹ & Đức", (6, 182, 212)),
    ("cat_supplements.jpg", "DINH DƯỠNG THỂ HÌNH", "Đạm Whey Isolate & Creapure tinh khiết", (245, 158, 11)),
    ("cat_apparel.jpg", "PHỤ KIỆN & TRANG PHỤC", "Đai da bò thật, băng gối & sợi carbon", (16, 185, 129)),
    ("cat_pt.jpg", "HUẤN LUYỆN VIÊN & TRỊ LIỆU", "Master Trainer 1:1 & Giãn cơ y khoa", (168, 85, 247))
]

for cat_file, cat_name, cat_desc, cat_acc in categories:
    c_img = create_carbon_texture(800, 500)
    glow = create_radial_glow(800, 500, 400, 250, 300, (cat_acc[0], cat_acc[1], cat_acc[2], 80))
    c_img = Image.alpha_composite(c_img, glow)
    d_cat = ImageDraw.Draw(c_img)
    d_cat.rectangle([(15, 15), (785, 485)], outline=cat_acc, width=2)
    d_cat.rounded_rectangle([(150, 140), (650, 340)], radius=18, fill=(18, 22, 30, 230), outline=cat_acc, width=2)
    d_cat.text((400, 200), cat_name, fill=(255, 255, 255), font=get_font(32, bold=True), anchor="mm")
    d_cat.text((400, 260), cat_desc, fill=(200, 215, 235), font=get_font(18), anchor="mm")
    d_cat.text((400, 305), "★ ★ ★ ★ ★ HIEUMINI 5-STAR", fill=cat_acc, font=get_font(16, bold=True), anchor="mm")
    
    c_rgb = Image.new("RGB", (800, 500), (10, 12, 16))
    c_rgb.paste(c_img, (0, 0), c_img)
    c_rgb.save(os.path.join(base_dir, cat_file), "JPEG", quality=95)

# ==================== TẠO 4 ẢNH HUẤN LUYỆN VIÊN (TRAINERS) ====================
print("Rendering 4 Master Trainers...")
trainers = [
    ("trainer_1.jpg", "ALEXANDER VU", "Master Trainer NASM - 12 Năm Kinh Nghiệm", (245, 158, 11)),
    ("trainer_2.jpg", "ELENA NGUYEN", "Chuyên Gia Dinh Dưỡng & Giảm Mỡ Nữ CEO", (6, 182, 212)),
    ("trainer_3.jpg", "MARCUS TRAN", "VĐV Thể Hình & Trị Liệu Thể Thao", (16, 185, 129)),
    ("trainer_4.jpg", "SARAH PHAM", "Master Yoga & Thiền Định Doanh Nhân", (168, 85, 247))
]

for t_file, t_name, t_title, t_acc in trainers:
    t_img = create_carbon_texture(600, 800)
    glow = create_radial_glow(600, 800, 300, 280, 250, (t_acc[0], t_acc[1], t_acc[2], 90))
    t_img = Image.alpha_composite(t_img, glow)
    dt = ImageDraw.Draw(t_img)
    dt.rectangle([(15, 15), (585, 785)], outline=t_acc, width=2)
    dt.ellipse([(150, 130), (450, 430)], fill=(20, 26, 36, 240), outline=t_acc, width=3)
    dt.text((300, 260), "MASTER COACH", fill=t_acc, font=get_font(26, bold=True), anchor="mm")
    dt.text((300, 310), "★ ★ ★ ★ ★", fill=(255, 255, 255), font=get_font(24), anchor="mm")
    dt.text((300, 560), t_name, fill=(255, 255, 255), font=get_font(32, bold=True), anchor="mm")
    dt.text((300, 620), t_title, fill=(200, 215, 235), font=get_font(18), anchor="mm")
    dt.rounded_rectangle([(120, 680), (480, 730)], radius=8, fill=(t_acc[0], t_acc[1], t_acc[2], 220))
    dt.text((300, 705), "HIEUMINI MASTER TRAINER", fill=(10, 12, 16), font=get_font(16, bold=True), anchor="mm")
    
    tr_rgb = Image.new("RGB", (600, 800), (10, 12, 18))
    tr_rgb.paste(t_img, (0, 0), t_img)
    tr_rgb.save(os.path.join(train_dir, t_file), "JPEG", quality=95)

# ==================== TẠO LOGO & AVATAR ====================
print("Rendering Logo & Avatars...")
logo_img = Image.new("RGBA", (500, 140), (0, 0, 0, 0))
d_logo = ImageDraw.Draw(logo_img)
d_logo.rounded_rectangle([(10, 10), (120, 120)], radius=20, fill=(245, 158, 11), outline=(255, 255, 255), width=2)
d_logo.text((65, 65), "HM", fill=(15, 20, 28), font=get_font(48, bold=True), anchor="mm")
d_logo.text((145, 40), "HIEUMINI", fill=(255, 255, 255), font=get_font(38, bold=True))
d_logo.text((148, 85), "LUXURY FITNESS CLUB", fill=(245, 158, 11), font=get_font(18, bold=True))
logo_img.save(os.path.join(base_dir, "logo.png"), "PNG")

ceo_img = create_carbon_texture(400, 400)
d_ceo = ImageDraw.Draw(ceo_img)
d_ceo.ellipse([(60, 60), (340, 340)], fill=(245, 158, 11), outline=(255, 255, 255), width=3)
d_ceo.text((200, 200), "CEO", fill=(15, 20, 28), font=get_font(60, bold=True), anchor="mm")
ceo_rgb = Image.new("RGB", (400, 400), (15, 18, 25))
ceo_rgb.paste(ceo_img, (0, 0), ceo_img)
ceo_rgb.save(os.path.join(base_dir, "ceo_avatar.jpg"), "JPEG", quality=95)
ceo_rgb.save(os.path.join(base_dir, "member_avatar.jpg"), "JPEG", quality=95)

print("\n>>> ALL 30 PRODUCT IMAGES, 5 CATEGORY COVERS & TRAINER PORTRAITS GENERATED SUCCESSFULLY! <<<")
