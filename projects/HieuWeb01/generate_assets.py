import os
import sys
import math
from PIL import Image, ImageDraw, ImageFont

if sys.stdout:
    try:
        sys.stdout.reconfigure(encoding='utf-8')
    except Exception:
        pass


# Tạo các thư mục cần thiết
BASE_DIR = r"c:\Users\tranv\Desktop\HieuWeb01"
IMG_DIR = os.path.join(BASE_DIR, "assets", "images")
PROD_IMG_DIR = os.path.join(IMG_DIR, "products")
CAT_IMG_DIR = os.path.join(IMG_DIR, "categories")
BANNER_IMG_DIR = os.path.join(IMG_DIR, "banners")

for d in [IMG_DIR, PROD_IMG_DIR, CAT_IMG_DIR, BANNER_IMG_DIR]:
    os.makedirs(d, exist_ok=True)

print("Đã tạo cấu trúc thư mục hình ảnh thành công.")

def get_font(size=24, bold=False):
    font_paths = [
        r"C:\Windows\Fonts\arialbd.ttf" if bold else r"C:\Windows\Fonts\arial.ttf",
        r"C:\Windows\Fonts\segoeuib.ttf" if bold else r"C:\Windows\Fonts\segoeui.ttf",
        r"C:\Windows\Fonts\calibrib.ttf" if bold else r"C:\Windows\Fonts\calibri.ttf"
    ]
    for p in font_paths:
        if os.path.exists(p):
            try:
                return ImageFont.truetype(p, size)
            except Exception:
                pass
    return ImageFont.load_default()

# 1. Tạo Logo HieuMini
def create_logo():
    w, h = 400, 100
    img = Image.new("RGBA", (w, h), (0, 0, 0, 0))
    draw = ImageDraw.Draw(img)
    
    # Background pill
    draw.rounded_rectangle([5, 10, w-5, h-10], radius=20, fill=(18, 24, 38, 245), outline=(218, 165, 32, 200), width=2)
    
    # Fashion Icon / Monogram
    draw.ellipse([25, 20, 75, 70], fill=(218, 165, 32, 230))
    f_icon = get_font(28, bold=True)
    draw.text((40, 26), "H", fill=(18, 24, 38, 255), font=f_icon)
    
    # Text Logo
    f_main = get_font(32, bold=True)
    f_sub = get_font(12, bold=True)
    
    draw.text((95, 22), "HieuMini", fill=(255, 255, 255, 255), font=f_main)
    draw.text((235, 30), "STUDIO", fill=(218, 165, 32, 255), font=f_sub)
    draw.text((96, 58), "PREMIUM FASHION & STREETWEAR", fill=(180, 190, 205, 255), font=f_sub)
    
    logo_path = os.path.join(IMG_DIR, "logo.png")
    img.save(logo_path, "PNG")
    print(f"-> Tạo Logo: {logo_path}")

# 2. Tạo Banners
def create_banners():
    banners = [
        {
            "name": "hero_banner_1.jpg",
            "title": "BỘ SƯU TẬP HÈ 2026",
            "subtitle": "THỜI TRANG ĐỈNH CAO - PHONG CÁCH TỎA SÁNG",
            "badge": "NEW COLLECTION",
            "bg_color1": (22, 28, 45),
            "bg_color2": (44, 62, 80),
            "accent": (218, 165, 32)
        },
        {
            "name": "hero_banner_2.jpg",
            "title": "FLASH SALE UP TO 50%",
            "subtitle": "SĂN DEAL HÀNG HIỆU - FREESHIP TOÀN QUỐC ĐƠN TỪ 300K",
            "badge": "ƯU ĐÃI ĐẶC BIỆT",
            "bg_color1": (139, 30, 63),
            "bg_color2": (40, 20, 50),
            "accent": (255, 215, 0)
        },
        {
            "name": "hero_banner_3.jpg",
            "title": "STREETWEAR & CASUAL WEAR",
            "subtitle": "THIẾT KẾ ĐỘC BẢN - CHẤT LIỆU COTTON 100% CAO CẤP",
            "badge": "TRENDING NOW",
            "bg_color1": (15, 32, 67),
            "bg_color2": (27, 85, 131),
            "accent": (74, 222, 128)
        }
    ]
    
    w, h = 1200, 480
    for b in banners:
        img = Image.new("RGB", (w, h), b["bg_color1"])
        draw = ImageDraw.Draw(img)
        
        # Gradient effect
        for x in range(w):
            ratio = x / w
            r = int(b["bg_color1"][0] * (1 - ratio) + b["bg_color2"][0] * ratio)
            g = int(b["bg_color1"][1] * (1 - ratio) + b["bg_color2"][1] * ratio)
            bl = int(b["bg_color1"][2] * (1 - ratio) + b["bg_color2"][2] * ratio)
            draw.line([(x, 0), (x, h)], fill=(r, g, bl))
            
        # Decorative shapes
        draw.ellipse([800, -100, 1350, 450], outline=(255, 255, 255, 30), width=3)
        draw.ellipse([850, -50, 1300, 400], outline=(b["accent"][0], b["accent"][1], b["accent"][2]), width=2)
        draw.polygon([(900, 100), (1150, 100), (1100, 400), (850, 400)], fill=(255, 255, 255, 10))
        
        # Badge
        draw.rounded_rectangle([80, 90, 260, 125], radius=8, fill=b["accent"])
        f_badge = get_font(14, bold=True)
        draw.text((95, 98), b["badge"], fill=(20, 20, 20), font=f_badge)
        
        # Title
        f_title = get_font(46, bold=True)
        draw.text((80, 150), b["title"], fill=(255, 255, 255), font=f_title)
        
        # Subtitle
        f_sub = get_font(20, bold=False)
        draw.text((80, 225), b["subtitle"], fill=(220, 230, 242), font=f_sub)
        
        # CTA Button
        draw.rounded_rectangle([80, 290, 280, 345], radius=10, fill=b["accent"])
        f_btn = get_font(18, bold=True)
        draw.text((105, 306), "MUA NGAY HÔM NAY", fill=(15, 20, 30), font=f_btn)
        
        # Free delivery badge
        draw.rounded_rectangle([300, 290, 520, 345], radius=10, outline=(255, 255, 255), width=2)
        draw.text((320, 306), "XEM BỘ SƯU TẬP →", fill=(255, 255, 255), font=f_btn)
        
        banner_path = os.path.join(BANNER_IMG_DIR, b["name"])
        img.save(banner_path, "JPEG", quality=95)
        print(f"-> Tạo Banner: {banner_path}")

# 3. Tạo hình ảnh sản phẩm chi tiết
def create_product_images():
    products = [
        # Cat 1: Áo thun & Polo
        {
            "file": "ao_thun_streetwear.jpg",
            "name": "Áo Thun Streetwear",
            "cat": "ÁO THUN COTTON",
            "color_bg": (245, 247, 250),
            "apparel_color": (33, 37, 41), # Black Tee
            "accent_text": "STREETWEAR VIBE",
            "type": "tshirt"
        },
        {
            "file": "ao_polo_dệt_bo.jpg",
            "name": "Áo Polo Phối Cổ Bo",
            "cat": "ÁO POLO NAM",
            "color_bg": (240, 244, 248),
            "apparel_color": (28, 54, 84), # Navy Polo
            "accent_text": "PREMIUM POLO",
            "type": "polo"
        },
        {
            "file": "ao_thun_graphic_hieu.jpg",
            "name": "Áo Thun Graphic Oversize",
            "cat": "HIEUMINI LIMITED",
            "color_bg": (248, 249, 250),
            "apparel_color": (230, 230, 230), # Off-white
            "accent_text": "HIEUMINI 2026",
            "type": "tshirt"
        },
        # Cat 2: Sơ mi
        {
            "file": "ao_so_mi_oxford.jpg",
            "name": "Áo Sơ Mi Oxford Dài Tay",
            "cat": "SƠ MI CÔNG SỞ",
            "color_bg": (242, 246, 252),
            "apparel_color": (173, 204, 235), # Light Blue Shirt
            "accent_text": "OXFORD COTTON",
            "type": "shirt"
        },
        {
            "file": "ao_so_mi_cubano.jpg",
            "name": "Áo Sơ Mi Lụa Cổ Cubano",
            "cat": "SƠ MI HÀN QUỐC",
            "color_bg": (250, 246, 240),
            "apparel_color": (217, 198, 172), # Beige silk
            "accent_text": "KOREAN SILK",
            "type": "shirt"
        },
        {
            "file": "ao_so_mi_caro.jpg",
            "name": "Áo Sơ Mi Kẻ Caro Classic",
            "cat": "VINTAGE CASUAL",
            "color_bg": (245, 245, 245),
            "apparel_color": (150, 45, 45), # Red Plaid
            "accent_text": "VINTAGE FLANNEL",
            "type": "shirt"
        },
        # Cat 3: Áo khoác & Hoodie
        {
            "file": "ao_khoac_bomber.jpg",
            "name": "Áo Khoác Bomber Kaki",
            "cat": "OUTERWEAR",
            "color_bg": (238, 242, 238),
            "apparel_color": (46, 68, 48), # Army Green
            "accent_text": "BOMBER JACKET",
            "type": "jacket"
        },
        {
            "file": "ao_hoodie_ni_bong.jpg",
            "name": "Áo Hoodie Nỉ Bông Unisex",
            "cat": "HOODIE WINTER",
            "color_bg": (246, 246, 248),
            "apparel_color": (70, 70, 80), # Charcoal Grey
            "accent_text": "WARM FLEECE",
            "type": "hoodie"
        },
        {
            "file": "ao_blazer_han_quoc.jpg",
            "name": "Áo Blazer Dáng Suông",
            "cat": "SMART CASUAL",
            "color_bg": (245, 242, 238),
            "apparel_color": (130, 110, 95), # Mocha Brown
            "accent_text": "ELEGANT BLAZER",
            "type": "blazer"
        },
        # Cat 4: Jeans
        {
            "file": "quan_jean_slimfit.jpg",
            "name": "Quần Jean Slimfit Co Giãn",
            "cat": "DENIM PANTS",
            "color_bg": (240, 245, 250),
            "apparel_color": (30, 60, 110), # Indigo Denim
            "accent_text": "STRETCH DENIM",
            "type": "jeans"
        },
        {
            "file": "quan_jean_baggy.jpg",
            "name": "Quần Jeans Ống Rộng Baggy",
            "cat": "STREET DENIM",
            "color_bg": (245, 248, 250),
            "apparel_color": (95, 145, 190), # Light Wash Denim
            "accent_text": "BAGGY FIT",
            "type": "jeans"
        },
        # Cat 5: Kaki & Trousers
        {
            "file": "quan_kaki_chino.jpg",
            "name": "Quần Kaki Chino Form Chuẩn",
            "cat": "CHINO TROUSERS",
            "color_bg": (248, 246, 242),
            "apparel_color": (195, 170, 130), # Khaki Tan
            "accent_text": "CASUAL CHINO",
            "type": "pants"
        },
        {
            "file": "quan_tay_au.jpg",
            "name": "Quần Tây Âu Xếp Ly Dáng Suông",
            "cat": "FORMAL PANTS",
            "color_bg": (244, 244, 246),
            "apparel_color": (40, 45, 55), # Dark Navy
            "accent_text": "TAILORED FIT",
            "type": "pants"
        },
        # Cat 6: Váy đầm nữ
        {
            "file": "dam_hoa_nhi.jpg",
            "name": "Đầm Hoa Nhí Dáng Xòe Vintage",
            "cat": "VINTAGE DRESS",
            "color_bg": (255, 245, 248),
            "apparel_color": (210, 120, 140), # Pastel Rose
            "accent_text": "FLORAL VINTAGE",
            "type": "dress"
        },
        {
            "file": "chan_vay_chu_a.jpg",
            "name": "Chân Váy Chữ A Lưng Cao",
            "cat": "KOREAN SKIRT",
            "color_bg": (248, 245, 250),
            "apparel_color": (60, 50, 70), # Dark Plum
            "accent_text": "A-LINE SKIRT",
            "type": "skirt"
        },
        # Cat 7: Phụ kiện
        {
            "file": "that_lung_da.jpg",
            "name": "Thắt Lưng Da Bò Cao Cấp",
            "cat": "ACCESSORIES",
            "color_bg": (248, 245, 240),
            "apparel_color": (90, 50, 25), # Leather Brown
            "accent_text": "GENUINE LEATHER",
            "type": "belt"
        },
        {
            "file": "non_ket_hieumini.jpg",
            "name": "Mũ Lưỡi Trai Nón Kết Thêu",
            "cat": "HEADWEAR",
            "color_bg": (242, 245, 248),
            "apparel_color": (25, 28, 35), # Black Cap
            "accent_text": "HIEUMINI CAP",
            "type": "cap"
        }
    ]

    w, h = 600, 600
    for p in products:
        img = Image.new("RGB", (w, h), p["color_bg"])
        draw = ImageDraw.Draw(img)
        
        # Soft background glow card
        draw.rounded_rectangle([30, 30, w-30, h-30], radius=24, fill=(255, 255, 255), outline=(225, 230, 238), width=2)
        
        # Category Tag badge top right
        draw.rounded_rectangle([w-190, 45, w-45, 78], radius=8, fill=(240, 243, 248))
        f_cat = get_font(12, bold=True)
        draw.text((w-180, 54), p["cat"], fill=(70, 85, 110), font=f_cat)
        
        # Brand Mark top left
        f_brand = get_font(14, bold=True)
        draw.text((50, 50), "HIEUMINI", fill=(218, 165, 32), font=f_brand)
        
        # Apparel Silhouette Drawing
        app_c = p["apparel_color"]
        ptype = p["type"]
        
        if ptype in ["tshirt", "polo"]:
            # T-shirt body
            draw.polygon([(200, 140), (260, 170), (340, 170), (400, 140), (460, 230), (400, 265), (380, 235), (380, 450), (220, 450), (220, 235), (200, 265), (140, 230)], fill=app_c)
            # Neck collar
            if ptype == "polo":
                draw.polygon([(260, 170), (300, 250), (340, 170), (300, 190)], fill=(app_c[0]+30, app_c[1]+30, app_c[2]+30))
                draw.polygon([(285, 170), (285, 260), (315, 260), (315, 170)], fill=(255, 255, 255, 80))
            else:
                draw.arc([255, 145, 345, 195], 0, 180, fill=(255, 255, 255), width=4)
        elif ptype == "shirt":
            # Shirt body
            draw.polygon([(210, 135), (265, 160), (335, 160), (390, 135), (460, 390), (410, 400), (375, 240), (375, 455), (225, 455), (225, 240), (190, 400), (140, 390)], fill=app_c)
            # Collar & button strip
            draw.polygon([(250, 140), (300, 205), (350, 140), (300, 165)], fill=(255, 255, 255, 180))
            draw.line([(300, 205), (300, 455)], fill=(255, 255, 255, 150), width=3)
            for by in range(230, 450, 45):
                draw.ellipse([296, by, 304, by+8], fill=(255, 255, 255))
        elif ptype in ["jacket", "hoodie", "blazer"]:
            # Jacket / Hoodie body
            draw.polygon([(190, 140), (260, 165), (340, 165), (410, 140), (475, 410), (420, 425), (385, 260), (385, 460), (215, 460), (215, 260), (180, 425), (125, 410)], fill=app_c)
            if ptype == "hoodie":
                draw.ellipse([230, 120, 370, 210], outline=(255, 255, 255), width=3)
                draw.rounded_rectangle([250, 340, 350, 420], radius=10, outline=(255, 255, 255, 100), width=2)
            elif ptype == "blazer":
                draw.polygon([(250, 150), (300, 290), (350, 150), (300, 175)], fill=(255, 255, 255, 120))
                draw.line([(300, 290), (300, 460)], fill=(20, 20, 20, 100), width=3)
            else: # Bomber
                draw.line([(300, 165), (300, 460)], fill=(218, 165, 32), width=4)
        elif ptype in ["jeans", "pants"]:
            # Pants
            draw.polygon([(220, 140), (380, 140), (380, 190), (370, 470), (310, 470), (300, 250), (290, 470), (230, 470), (220, 190)], fill=app_c)
            # Belt loops & pockets
            draw.line([(220, 165), (380, 165)], fill=(255, 255, 255, 150), width=2)
            draw.arc([235, 165, 275, 220], 0, 180, fill=(255, 255, 255, 150), width=2)
            draw.arc([325, 165, 365, 220], 0, 180, fill=(255, 255, 255, 150), width=2)
        elif ptype == "dress":
            # Dress
            draw.polygon([(250, 140), (350, 140), (330, 230), (440, 470), (160, 470), (270, 230)], fill=app_c)
            draw.ellipse([270, 130, 330, 170], fill=(255, 255, 255))
        elif ptype == "skirt":
            # Skirt
            draw.polygon([(240, 170), (360, 170), (430, 440), (170, 440)], fill=app_c)
            draw.line([(240, 195), (360, 195)], fill=(255, 255, 255, 180), width=3)
        elif ptype == "belt":
            # Belt coil
            for r in range(120, 40, -25):
                draw.ellipse([300-r, 290-r, 300+r, 290+r], outline=app_c, width=18)
            draw.rounded_rectangle([255, 260, 345, 320], radius=8, outline=(218, 165, 32), width=6)
        elif ptype == "cap":
            # Cap
            draw.ellipse([200, 180, 400, 340], fill=app_c)
            draw.ellipse([170, 280, 330, 380], fill=(app_c[0]+15, app_c[1]+15, app_c[2]+15))
            draw.rounded_rectangle([270, 230, 330, 265], radius=6, fill=(218, 165, 32))
            f_cp = get_font(12, bold=True)
            draw.text((280, 238), "HM", fill=(10, 10, 10), font=f_cp)

        # Bottom Product Label Banner
        draw.rounded_rectangle([50, h-105, w-50, h-45], radius=12, fill=(18, 24, 38))
        f_pname = get_font(17, bold=True)
        f_badge2 = get_font(12, bold=True)
        draw.text((70, h-95), p["name"], fill=(255, 255, 255), font=f_pname)
        draw.text((70, h-68), "AUTHENTIC QUALITY • HIEUMINI", fill=(218, 165, 32), font=f_badge2)
        
        # Save product image
        prod_path = os.path.join(PROD_IMG_DIR, p["file"])
        img.save(prod_path, "JPEG", quality=95)
        print(f"-> Tạo Ảnh SP: {prod_path}")

# 4. Tạo ảnh danh mục
def create_category_images():
    cats = [
        ("cat_ao_thun.jpg", "ÁO THUN & POLO", (24, 43, 73)),
        ("cat_ao_somi.jpg", "ÁO SƠ MI", (45, 60, 80)),
        ("cat_ao_khoac.jpg", "ÁO KHOÁC & HOODIE", (35, 45, 55)),
        ("cat_quan_jeans.jpg", "QUẦN JEANS", (20, 50, 90)),
        ("cat_quan_kaki.jpg", "QUẦN KAKI & TÂY", (70, 60, 50)),
        ("cat_vay_dam.jpg", "VÁY & ĐẦM NỮ", (120, 45, 75)),
        ("cat_phu_kien.jpg", "PHỤ KIỆN THỜI TRANG", (40, 40, 40)),
    ]
    w, h = 400, 260
    for file, title, bg in cats:
        img = Image.new("RGB", (w, h), bg)
        draw = ImageDraw.Draw(img)
        draw.rectangle([10, 10, w-10, h-10], outline=(218, 165, 32), width=2)
        draw.rectangle([20, 20, w-20, h-20], outline=(255, 255, 255, 50), width=1)
        
        f_t = get_font(20, bold=True)
        f_sub = get_font(12, bold=False)
        
        draw.text((35, 100), title, fill=(255, 255, 255), font=f_t)
        draw.text((35, 135), "XEM BỘ SƯU TẬP →", fill=(218, 165, 32), font=f_sub)
        
        cat_path = os.path.join(CAT_IMG_DIR, file)
        img.save(cat_path, "JPEG", quality=95)
        print(f"-> Tạo Ảnh Danh Mục: {cat_path}")

create_logo()
create_banners()
create_product_images()
create_category_images()
print("Hoàn tất tạo bộ hình ảnh chuyên nghiệp cho HieuMini!")
