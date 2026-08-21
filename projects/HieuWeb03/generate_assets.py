import os
import math
from PIL import Image, ImageDraw, ImageFont, ImageFilter

OUTPUT_DIR = r"c:\Users\tranv\Desktop\HieuWeb03\assets\images\products"
BANNERS_DIR = r"c:\Users\tranv\Desktop\HieuWeb03\assets\images\banners"
os.makedirs(OUTPUT_DIR, exist_ok=True)
os.makedirs(BANNERS_DIR, exist_ok=True)

# 30 Product Specifications
PRODUCTS = [
    {
        "id": 1, "code": "p1", "name": "Bút Gel Pastel Morandi (Set 6)",
        "sub": "Mực nước 0.5mm êm ái, mau khô",
        "category": "Bút & Dụng Cụ Viết", "cat_id": 1,
        "color1": "#FCE7F3", "color2": "#DB2777", "accent": "#BE185D",
        "tag": "HOT TREND", "icon": "pen"
    },
    {
        "id": 2, "code": "p2", "name": "Bút Chì Kim Pentel GraphGear 1000",
        "sub": "Thân kim loại cao cấp ngòi 0.5mm",
        "category": "Bút & Dụng Cụ Viết", "cat_id": 1,
        "color1": "#E0E7FF", "color2": "#4338CA", "accent": "#312E81",
        "tag": "PREMIUM", "icon": "pencil"
    },
    {
        "id": 3, "code": "p3", "name": "Bút Dạ Quang 2 Đầu Pastel Macaron",
        "sub": "Set 6 màu dịu mắt không lem",
        "category": "Bút & Dụng Cụ Viết", "cat_id": 1,
        "color1": "#FEF3C7", "color2": "#D97706", "accent": "#B45309",
        "tag": "BEST SELLER", "icon": "highlighter"
    },
    {
        "id": 4, "code": "p4", "name": "Bút Máy Học Sinh Kim Tinh Ngòi Mài",
        "sub": "Nét thanh nét đậm luyện chữ đẹp",
        "category": "Bút & Dụng Cụ Viết", "cat_id": 1,
        "color1": "#CFFAFE", "color2": "#0891B2", "accent": "#0E7490",
        "tag": "LUYỆN CHỮ", "icon": "fountain_pen"
    },
    {
        "id": 5, "code": "p5", "name": "Bút Lông Calligraphy Tombow",
        "sub": "Ngòi cọ linh hoạt viết thư pháp",
        "category": "Bút & Dụng Cụ Viết", "cat_id": 1,
        "color1": "#F3E8FF", "color2": "#7E22CE", "accent": "#6B21A8",
        "tag": "ARTIST CHOICE", "icon": "brush"
    },
    {
        "id": 6, "code": "p6", "name": "Sổ Còng Binder A5 Bìa Da PU Vintage",
        "sub": "Thay ruột tiện lợi, kèm túi lưu trữ",
        "category": "Sổ Tay & Vở Viết", "cat_id": 2,
        "color1": "#FEF2F2", "color2": "#DC2626", "accent": "#991B1B",
        "tag": "VINTAGE", "icon": "binder"
    },
    {
        "id": 7, "code": "p7", "name": "Sổ Bullet Journal Dot 160 Trang",
        "sub": "Giấy 100gsm dày dặn chống thấm",
        "category": "Sổ Tay & Vở Viết", "cat_id": 2,
        "color1": "#ECFCCB", "color2": "#65A30D", "accent": "#4D7C0F",
        "tag": "PLANNING", "icon": "notebook"
    },
    {
        "id": 8, "code": "p8", "name": "Tập Vở Campus Landscape 200 Trang",
        "sub": "Kẻ ô ly sắc nét, bìa chống lóa",
        "category": "Sổ Tay & Vở Viết", "cat_id": 2,
        "color1": "#E0F2FE", "color2": "#0284C7", "accent": "#0369A1",
        "tag": "HỌC SINH", "icon": "book"
    },
    {
        "id": 9, "code": "p9", "name": "Sổ Kế Hoạch Weekly & Monthly Planner",
        "sub": "Quản lý thời gian học tập hiệu quả",
        "category": "Sổ Tay & Vở Viết", "cat_id": 2,
        "color1": "#FDF4FF", "color2": "#C026D3", "accent": "#A21CAF",
        "tag": "PRODUCTIVITY", "icon": "calendar"
    },
    {
        "id": 10, "code": "p10", "name": "Sổ Phác Thảo Sketchbook A4 160gsm",
        "sub": "Giấy chuyên vẽ chì, màu sáp, marker",
        "category": "Sổ Tay & Vở Viết", "cat_id": 2,
        "color1": "#FFFBEB", "color2": "#CA8A04", "accent": "#A16207",
        "tag": "SKETCHING", "icon": "sketchbook"
    },
    {
        "id": 11, "code": "p11", "name": "Bộ Màu Nước Nén 36 Màu Kèm Cọ Vẽ",
        "sub": "Hộp thiếc cao cấp, màu sắc trong trẻo",
        "category": "Dụng Cụ Vẽ & Mỹ Thuật", "cat_id": 3,
        "color1": "#EFF6FF", "color2": "#2563EB", "accent": "#1D4ED8",
        "tag": "BEST ART", "icon": "palette"
    },
    {
        "id": 12, "code": "p12", "name": "Hộp Chì Màu Dầu Faber-Castell 48 Màu",
        "sub": "Độ bão hòa cao, mịn màng dễ blend",
        "category": "Dụng Cụ Vẽ & Mỹ Thuật", "cat_id": 3,
        "color1": "#F0FDF4", "color2": "#16A34A", "accent": "#15803D",
        "tag": "PREMIUM ART", "icon": "color_pencils"
    },
    {
        "id": 13, "code": "p13", "name": "Bộ Cọ Vẽ Nghệ Thuật 10 Cây Đa Năng",
        "sub": "Lông nhân tạo cao cấp không rụng",
        "category": "Dụng Cụ Vẽ & Mỹ Thuật", "cat_id": 3,
        "color1": "#FFF7ED", "color2": "#EA580C", "accent": "#C2410C",
        "tag": "PRO SET", "icon": "brushes"
    },
    {
        "id": 14, "code": "p14", "name": "Bút Kỹ Thuật Artline Ergoline (Set 5)",
        "sub": "Kháng nước, độ chính xác đồ họa cao",
        "category": "Dụng Cụ Vẽ & Mỹ Thuật", "cat_id": 3,
        "color1": "#F1F5F9", "color2": "#334155", "accent": "#0F172A",
        "tag": "GRAPHIC", "icon": "fineliner"
    },
    {
        "id": 15, "code": "p15", "name": "Bảng Pha Màu & Khay Rửa Cọ Gấp Gọn",
        "sub": "Silicon mềm gấp gọn mang đi học",
        "category": "Dụng Cụ Vẽ & Mỹ Thuật", "cat_id": 3,
        "color1": "#ECFEFF", "color2": "#0D9488", "accent": "#0F766E",
        "tag": "TIỆN LỢI", "icon": "palette_tray"
    },
    {
        "id": 16, "code": "p16", "name": "Cặp Đựng Tài Liệu A4 Nhiều Ngăn Quai Xách",
        "sub": "12 ngăn phân loại bài thi, chống gãy gập",
        "category": "Bìa Hồ Sơ & Lưu Trữ", "cat_id": 4,
        "color1": "#EEF2FF", "color2": "#4F46E5", "accent": "#3730A3",
        "tag": "HOT SELLER", "icon": "folder"
    },
    {
        "id": 17, "code": "p17", "name": "Bìa Cây Trong Suốt Giữ Tài Liệu (Set 10)",
        "sub": "Nhựa PP dẻo dai bảo vệ hồ sơ",
        "category": "Bìa Hồ Sơ & Lưu Trữ", "cat_id": 4,
        "color1": "#F0FDF4", "color2": "#059669", "accent": "#047857",
        "tag": "TIẾT KIỆM", "icon": "clear_file"
    },
    {
        "id": 18, "code": "p18", "name": "Kệ Sách Vở 4 Ngăn Để Bàn Học Lắp Ghép",
        "sub": "Gỗ MDF chống ẩm, bàn học ngăn nắp",
        "category": "Bìa Hồ Sơ & Lưu Trữ", "cat_id": 4,
        "color1": "#FEF3C7", "color2": "#B45309", "accent": "#78350F",
        "tag": "GỌN GÀNG", "icon": "bookshelf"
    },
    {
        "id": 19, "code": "p19", "name": "File Còng Bật 7cm Văn Phòng Chuyên Dụng",
        "sub": "Khóa còng kim loại mạ crom sáng bóng",
        "category": "Bìa Hồ Sơ & Lưu Trữ", "cat_id": 4,
        "color1": "#F1F5F9", "color2": "#475569", "accent": "#1E293B",
        "tag": "OFFICE", "icon": "lever_arch"
    },
    {
        "id": 20, "code": "p20", "name": "Túi Zip Lưới A4 Đựng Đề Thi (Set 5)",
        "sub": "Khóa kéo êm, chống thấm nước tuyệt đối",
        "category": "Bìa Hồ Sơ & Lưu Trữ", "cat_id": 4,
        "color1": "#FDF2F8", "color2": "#DB2777", "accent": "#9D174D",
        "tag": "CHỐNG NƯỚC", "icon": "mesh_bag"
    },
    {
        "id": 21, "code": "p21", "name": "Hộp Bút Vải Canvas Đa Năng Sức Chứa Khủng",
        "sub": "Chứa 50 bút, nhiều ngăn chia tiện lợi",
        "category": "Phụ Kiện Bàn Học", "cat_id": 5,
        "color1": "#F5F3FF", "color2": "#8B5CF6", "accent": "#6D28D9",
        "tag": "BEST TREND", "icon": "pencil_case"
    },
    {
        "id": 22, "code": "p22", "name": "Đèn Bàn Học LED Chống Cận 3 Chế Độ",
        "sub": "Ánh sáng tự nhiên không chớp, pin sạc 2000mAh",
        "category": "Phụ Kiện Bàn Học", "cat_id": 5,
        "color1": "#FFFBEB", "color2": "#F59E0B", "accent": "#D97706",
        "tag": "BẢO VỆ MẮT", "icon": "desk_lamp"
    },
    {
        "id": 23, "code": "p23", "name": "Máy Tính Khoa Học Casio FX-580VN X",
        "sub": "521 tính năng, ngôn ngữ Tiếng Việt",
        "category": "Phụ Kiện Bàn Học", "cat_id": 5,
        "color1": "#F8FAFC", "color2": "#0F172A", "accent": "#0284C7",
        "tag": "CHÍNH HÃNG", "icon": "calculator"
    },
    {
        "id": 24, "code": "p24", "name": "Bộ Thước Kẻ Eke Đo Độ Hợp Kim Nhôm",
        "sub": "Số khắc laser bền màu, không cong vênh",
        "category": "Phụ Kiện Bàn Học", "cat_id": 5,
        "color1": "#EFF6FF", "color2": "#3B82F6", "accent": "#1D4ED8",
        "tag": "SIÊU BỀN", "icon": "ruler_set"
    },
    {
        "id": 25, "code": "p25", "name": "Gọt Bút Chì Quay Tay Ngôi Nhà Hoạt Hình",
        "sub": "Lưỡi thép siêu bén, có khay hứng mùn",
        "category": "Phụ Kiện Bàn Học", "cat_id": 5,
        "color1": "#FDF4FF", "color2": "#E11D48", "accent": "#BE123C",
        "tag": "CUTE", "icon": "sharpener"
    },
    {
        "id": 26, "code": "p26", "name": "Kéo Cắt Giấy An Toàn Có Nắp Thép Titan",
        "sub": "Tay cầm bọc cao su êm ái chống trượt",
        "category": "Phụ Kiện Bàn Học", "cat_id": 5,
        "color1": "#FEF2F2", "color2": "#EF4444", "accent": "#B91C1C",
        "tag": "AN TOÀN", "icon": "scissors"
    },
    {
        "id": 27, "code": "p27", "name": "Dập Ghim Bấm Nhỏ Kèm 1000 Ghim Pastel",
        "sub": "Thiết kế trợ lực bấm êm, nhẹ nhàng",
        "category": "Phụ Kiện Bàn Học", "cat_id": 5,
        "color1": "#F0FDF4", "color2": "#10B981", "accent": "#047857",
        "tag": "MINI CUTE", "icon": "stapler"
    },
    {
        "id": 28, "code": "p28", "name": "Băng Xóa Kéo Mini Không Đứt Đoạn 12m",
        "sub": "Che phủ hoàn hảo, viết đè ngay tức thì",
        "category": "Phụ Kiện Bàn Học", "cat_id": 5,
        "color1": "#E0E7FF", "color2": "#6366F1", "accent": "#4338CA",
        "tag": "TIỆN ÍCH", "icon": "correction_tape"
    },
    {
        "id": 29, "code": "p29", "name": "Ba Lô Học Sinh Chống Gù & Chống Thấm Nước",
        "sub": "Đệm lưng thoáng khí, nhiều ngăn đa năng",
        "category": "Ba Lô & Cặp Học Sinh", "cat_id": 6,
        "color1": "#F1F5F9", "color2": "#0284C7", "accent": "#0F172A",
        "tag": "CHỐNG GÙ", "icon": "backpack"
    },
    {
        "id": 30, "code": "p30", "name": "Túi Tote Canvas Thời Trang Đựng Laptop A4",
        "sub": "Chất vải canvas dày 12oz, quai chịu lực",
        "category": "Ba Lô & Cặp Học Sinh", "cat_id": 6,
        "color1": "#FEF3C7", "color2": "#D97706", "accent": "#92400E",
        "tag": "HOT GENZ", "icon": "tote_bag"
    }
]

def hex_to_rgb(hex_str):
    hex_str = hex_str.lstrip('#')
    return tuple(int(hex_str[i:i+2], 16) for i in (0, 2, 4))

def draw_gradient(draw, width, height, c1, c2):
    r1, g1, b1 = c1
    r2, g2, b2 = c2
    for y in range(height):
        ratio = y / float(height)
        r = int(r1 + (r2 - r1) * ratio)
        g = int(g1 + (g2 - g1) * ratio)
        b = int(b1 + (b2 - b1) * ratio)
        draw.line([(0, y), (width, y)], fill=(r, g, b))

def draw_icon_graphic(draw, icon_type, cx, cy, primary_color, accent_color):
    p_rgb = hex_to_rgb(primary_color)
    a_rgb = hex_to_rgb(accent_color)
    
    # Backdrop circle with shadow
    draw.ellipse([cx-170, cy-170, cx+170, cy+170], fill=(255, 255, 255, 220), outline=p_rgb, width=4)
    draw.ellipse([cx-150, cy-150, cx+150, cy+150], fill=(*p_rgb, 40))

    if "pen" in icon_type or "pencil" in icon_type or "fineliner" in icon_type or "brush" in icon_type or "highlighter" in icon_type:
        # Draw dynamic stylized stationery pens
        draw.rounded_rectangle([cx-40, cy-110, cx+40, cy+70], radius=15, fill=p_rgb, outline=a_rgb, width=4)
        draw.polygon([(cx-40, cy+70), (cx+40, cy+70), (cx, cy+130)], fill=a_rgb)
        draw.polygon([(cx-15, cy+110), (cx+15, cy+110), (cx, cy+135)], fill=(250, 204, 21))
        # Pen clip
        draw.rounded_rectangle([cx+25, cy-90, cx+45, cy-20], radius=8, fill=(240, 240, 240), outline=a_rgb, width=2)
        # Grip lines
        for offset in range(-20, 50, 15):
            draw.line([(cx-30, cy+offset), (cx+30, cy+offset)], fill=(255, 255, 255, 180), width=3)
        # Cap detail
        draw.rounded_rectangle([cx-44, cy-115, cx+44, cy-70], radius=8, fill=a_rgb)
    elif "notebook" in icon_type or "binder" in icon_type or "book" in icon_type or "sketchbook" in icon_type or "calendar" in icon_type:
        # Draw realistic notebook/binder
        draw.rounded_rectangle([cx-100, cy-110, cx+90, cy+110], radius=16, fill=p_rgb, outline=a_rgb, width=5)
        draw.rounded_rectangle([cx-85, cy-95, cx+75, cy+95], radius=10, fill=(255, 255, 255), outline=(220, 220, 220), width=2)
        # Spiral binder or line details
        for y_pos in range(cy-80, cy+90, 22):
            draw.line([(cx-70, y_pos), (cx+60, y_pos)], fill=(*p_rgb, 120), width=3)
            # binder rings
            draw.ellipse([cx-108, y_pos-6, cx-92, y_pos+6], fill=(220, 220, 220), outline=a_rgb, width=2)
        # Ribbon bookmark
        draw.polygon([(cx+30, cy-95), (cx+45, cy-95), (cx+45, cy+40), (cx+37, cy+25), (cx+30, cy+40)], fill=(239, 68, 68))
    elif "palette" in icon_type or "brushes" in icon_type or "color_pencils" in icon_type:
        # Artist Palette / paint tray
        draw.ellipse([cx-110, cy-90, cx+110, cy+90], fill=(254, 243, 199), outline=a_rgb, width=5)
        # Thumb hole
        draw.ellipse([cx+40, cy+10, cx+80, cy+50], fill=(255, 255, 255), outline=a_rgb, width=3)
        # Paint color dabs
        colors = [(239, 68, 68), (59, 130, 246), (16, 185, 129), (245, 158, 11), (168, 85, 247), (236, 72, 153)]
        coords = [(-70, -40), (-30, -60), (20, -50), (-80, 10), (-40, 40), (0, 50)]
        for (dx, dy), col in zip(coords, colors):
            draw.ellipse([cx+dx-18, cy+dy-18, cx+dx+18, cy+dy+18], fill=col)
        # Paintbrush
        draw.line([(cx-90, cy+80), (cx+70, cy-80)], fill=(120, 53, 15), width=10)
        draw.ellipse([cx-105, cy+70, cx-80, cy+95], fill=(239, 68, 68))
    elif "folder" in icon_type or "clear_file" in icon_type or "lever_arch" in icon_type or "mesh_bag" in icon_type:
        # Folder Organizer
        draw.polygon([(cx-100, cy-70), (cx-40, cy-70), (cx-20, cy-90), (cx+100, cy-90), (cx+100, cy+90), (cx-100, cy+90)], fill=p_rgb, outline=a_rgb)
        draw.polygon([(cx-90, cy-50), (cx+90, cy-50), (cx+80, cy+80), (cx-80, cy+80)], fill=(255, 255, 255, 230), outline=(200, 200, 200), width=2)
        # Paper sheets inside
        for y_pos in range(cy-30, cy+60, 20):
            draw.line([(cx-60, y_pos), (cx+60, y_pos)], fill=(*p_rgb, 140), width=3)
    elif "lamp" in icon_type or "calculator" in icon_type or "pencil_case" in icon_type or "sharpener" in icon_type or "stapler" in icon_type or "scissors" in icon_type or "ruler" in icon_type or "tape" in icon_type:
        if "calculator" in icon_type:
            # Smart Casio Calc
            draw.rounded_rectangle([cx-75, cy-115, cx+75, cy+115], radius=16, fill=(30, 41, 59), outline=(15, 23, 42), width=5)
            # LCD screen
            draw.rounded_rectangle([cx-60, cy-95, cx+60, cy-50], radius=6, fill=(203, 213, 225), outline=(100, 116, 139), width=2)
            # Keys grid
            for row in range(4):
                for col in range(3):
                    kx = cx - 50 + col * 38
                    ky = cy - 30 + row * 34
                    draw.rounded_rectangle([kx-14, ky-12, kx+14, ky+12], radius=5, fill=(71, 85, 105) if row<3 else (59, 130, 246))
        elif "scissors" in icon_type:
            # Scissors
            draw.line([(cx-70, cy-80), (cx+60, cy+60)], fill=(148, 163, 184), width=12)
            draw.line([(cx+70, cy-80), (cx-60, cy+60)], fill=(148, 163, 184), width=12)
            draw.ellipse([cx-80, cy+40, cx-40, cy+80], outline=p_rgb, width=12)
            draw.ellipse([cx+40, cy+40, cx+80, cy+80], outline=p_rgb, width=12)
            draw.ellipse([cx-8, cy-10, cx+8, cy+6], fill=(234, 179, 8))
        else:
            # Multi-gadget accessories
            draw.rounded_rectangle([cx-100, cy-65, cx+100, cy+65], radius=24, fill=p_rgb, outline=a_rgb, width=5)
            draw.line([(cx-90, cy-20), (cx+90, cy-20)], fill=(255, 255, 255), width=6)
            draw.ellipse([cx+70, cy-23, cx+85, cy-17], fill=(250, 204, 21))
            draw.rounded_rectangle([cx-60, cy+5, cx+60, cy+45], radius=10, fill=(255, 255, 255, 200))
    elif "backpack" in icon_type or "bag" in icon_type:
        # Modern School Backpack
        draw.rounded_rectangle([cx-90, cy-95, cx+90, cy+95], radius=35, fill=p_rgb, outline=a_rgb, width=6)
        # Front pocket
        draw.rounded_rectangle([cx-70, cy+5, cx+70, cy+75], radius=18, fill=a_rgb, outline=(255, 255, 255), width=3)
        # Top handle
        draw.arc([cx-40, cy-125, cx+40, cy-75], start=180, end=0, fill=a_rgb, width=8)
        # Zipper & Badge
        draw.line([(cx-60, cy+25), (cx+60, cy+25)], fill=(250, 204, 21), width=4)
        draw.ellipse([cx-15, cy+40, cx+15, cy+65], fill=(255, 255, 255))
    else:
        # General premium stationery
        draw.rounded_rectangle([cx-80, cy-80, cx+80, cy+80], radius=20, fill=p_rgb, outline=a_rgb, width=5)
        draw.text((cx-30, cy-20), "HIEU", fill=(255, 255, 255))

def generate_product_image(p_info):
    W, H = 800, 800
    img = Image.new("RGB", (W, H), color="#FFFFFF")
    draw = ImageDraw.Draw(img)

    # Background gradient
    c1 = hex_to_rgb(p_info["color1"])
    c2 = hex_to_rgb("#FFFFFF")
    draw_gradient(draw, W, H, c1, c2)

    # Decorative geometric background elements
    p_rgb = hex_to_rgb(p_info["color2"])
    for i in range(5):
        angle = i * (math.pi / 2.5)
        dx = int(math.cos(angle) * 320)
        dy = int(math.sin(angle) * 320)
        draw.ellipse([400+dx-40, 400+dy-40, 400+dx+40, 400+dy+40], fill=(*p_rgb, 25))

    # Outer decorative card border
    draw.rounded_rectangle([25, 25, W-25, H-25], radius=32, outline=(*p_rgb, 70), width=3)
    draw.rounded_rectangle([35, 35, W-35, H-35], radius=24, outline=(255, 255, 255, 200), width=2)

    # Brand Header Watermark
    try:
        font_brand = ImageFont.truetype("arialbd.ttf", 26)
        font_cat = ImageFont.truetype("arial.ttf", 20)
        font_title = ImageFont.truetype("arialbd.ttf", 28)
        font_sub = ImageFont.truetype("arial.ttf", 20)
        font_badge = ImageFont.truetype("arialbd.ttf", 18)
    except:
        font_brand = ImageFont.load_default()
        font_cat = font_brand
        font_title = font_brand
        font_sub = font_brand
        font_badge = font_brand

    # Store branding top left
    draw.text((60, 55), "HieuMini", fill=hex_to_rgb(p_info["accent"]), font=font_brand)
    draw.text((185, 60), "| Stationery & School Supplies", fill=(100, 116, 139), font=font_cat)

    # Badge Pill top right
    badge_text = p_info["tag"]
    badge_w = len(badge_text) * 11 + 30
    draw.rounded_rectangle([W - 60 - badge_w, 50, W - 60, 86], radius=18, fill=p_rgb)
    draw.text((W - 50 - badge_w + 10, 58), badge_text, fill=(255, 255, 255), font=font_badge)

    # Center Illustration Graphics
    draw_icon_graphic(draw, p_info["icon"], 400, 370, p_info["color2"], p_info["accent"])

    # Bottom Information Card Container
    card_top = 580
    card_bot = 745
    draw.rounded_rectangle([55, card_top, W-55, card_bot], radius=20, fill=(255, 255, 255, 240), outline=(226, 232, 240), width=2)

    # Category Pill in Info Card
    draw.rounded_rectangle([80, card_top + 20, 80 + len(p_info["category"])*9 + 20, card_top + 48], radius=12, fill=(*p_rgb, 35))
    draw.text((90, card_top + 24), p_info["category"], fill=hex_to_rgb(p_info["accent"]), font=font_cat)

    # Product Title (Wrap if too long)
    title = p_info["name"]
    draw.text((80, card_top + 58), title, fill=(15, 23, 42), font=font_title)
    draw.text((80, card_top + 100), p_info["sub"], fill=(100, 116, 139), font=font_sub)

    # Save to path
    out_file = os.path.join(OUTPUT_DIR, f"{p_info['code']}.png")
    img.save(out_file, "PNG", quality=95)
    print(f"Generated: {out_file}")

# Generate all 30 products
for p in PRODUCTS:
    generate_product_image(p)

# Generate Banners & Hero Images
def generate_hero_banner():
    W, H = 1200, 500
    img = Image.new("RGB", (W, H), color="#FFFFFF")
    draw = ImageDraw.Draw(img)
    draw_gradient(draw, W, H, (238, 242, 255), (255, 255, 255))
    
    # Decorative bubbles
    for (x, y, r, c) in [(1000, 150, 180, (99, 102, 241, 40)), (1100, 380, 120, (236, 72, 153, 40)), (200, 420, 90, (16, 185, 129, 30))]:
        draw.ellipse([x-r, y-r, x+r, y+r], fill=c)
    
    try:
        f_hero = ImageFont.truetype("arialbd.ttf", 46)
        f_sub = ImageFont.truetype("arial.ttf", 22)
    except:
        f_hero = ImageFont.load_default()
        f_sub = f_hero

    draw.text((80, 100), "HIEUMINI STATIONERY", fill=(79, 70, 229), font=f_hero)
    draw.text((80, 170), "Thiên Đường Đồ Dùng Học Tập & Sáng Tạo Học Đường", fill=(15, 23, 42), font=f_hero)
    draw.text((80, 245), "Hơn 500+ sản phẩm bút viết, sổ tay, dụng cụ vẽ & phụ kiện chính hãng chất lượng cao", fill=(71, 85, 105), font=f_sub)
    draw.text((80, 285), "Ưu đãi tựu trường giảm tới 50% - Freeship toàn quốc cho đơn từ 250.000 đ", fill=(217, 70, 239), font=f_sub)
    
    # Hero visual on right
    draw_icon_graphic(draw, "pen", 950, 250, "#6366F1", "#4338CA")
    
    img.save(os.path.join(BANNERS_DIR, "hero-banner.png"), "PNG")
    print("Generated Hero Banner")

def generate_promo_banner(name, title, sub, c1, c2, file_name):
    W, H = 600, 300
    img = Image.new("RGB", (W, H), color="#FFFFFF")
    draw = ImageDraw.Draw(img)
    draw_gradient(draw, W, H, hex_to_rgb(c1), hex_to_rgb(c2))
    
    try:
        f_title = ImageFont.truetype("arialbd.ttf", 28)
        f_sub = ImageFont.truetype("arial.ttf", 18)
    except:
        f_title = ImageFont.load_default()
        f_sub = f_title
        
    draw.text((40, 50), "ƯU ĐÃI ĐẶC BIỆT", fill=(255, 255, 255), font=f_sub)
    draw.text((40, 90), title, fill=(255, 255, 255), font=f_title)
    draw.text((40, 140), sub, fill=(241, 245, 249), font=f_sub)
    
    img.save(os.path.join(BANNERS_DIR, file_name), "PNG")
    print(f"Generated Promo Banner: {file_name}")

generate_hero_banner()
generate_promo_banner("promo1", "Bộ Sưu Tập Bút Pastel", "Giảm 30% khi mua theo Combo", "#4F46E5", "#7C3AED", "promo-1.png")
generate_promo_banner("promo2", "Sổ Bullet Journal & Planner", "Tặng kèm bộ sticker & washi tape", "#EC4899", "#F43F5E", "promo-2.png")
generate_promo_banner("promo3", "Dụng Cụ Mỹ Thuật Chuyên Nghiệp", "Ưu đãi học sinh - sinh viên 25%", "#0D9488", "#059669", "promo-3.png")

print("All 30 product images and banners generated successfully!")
