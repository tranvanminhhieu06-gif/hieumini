import os
from PIL import Image, ImageDraw, ImageFont

OUTPUT_DIR = r"c:\Users\tranv\Desktop\HieuWeb03\assets\images\products"
os.makedirs(OUTPUT_DIR, exist_ok=True)

FONT_BOLD = "C:\\Windows\\Fonts\\segoeuib.ttf"
FONT_REG = "C:\\Windows\\Fonts\\segoeui.ttf"
if not os.path.exists(FONT_BOLD):
    FONT_BOLD = "C:\\Windows\\Fonts\\arialbd.ttf"
    FONT_REG = "C:\\Windows\\Fonts\\arial.ttf"

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

# Generate Detail and Box/Combo shots for all 30 products
from fix_images import PRODUCTS, draw_icon_graphic

for p in PRODUCTS:
    W, H = 800, 800
    
    # 1. Detail Shot (Macro / Feature Zoom)
    img_det = Image.new("RGB", (W, H), color="#FFFFFF")
    draw_det = ImageDraw.Draw(img_det)
    draw_gradient(draw_det, W, H, hex_to_rgb(p["color1"]), (255, 255, 255))
    
    # Border & Badges
    p_rgb = hex_to_rgb(p["color2"])
    draw_det.rounded_rectangle([25, 25, W-25, H-25], radius=32, outline=(*p_rgb, 70), width=3)
    
    f_b = ImageFont.truetype(FONT_BOLD, 26)
    f_r = ImageFont.truetype(FONT_REG, 20)
    f_sub = ImageFont.truetype(FONT_REG, 18)

    draw_det.text((60, 55), "HieuMini", fill=hex_to_rgb(p["accent"]), font=f_b)
    draw_det.text((185, 60), "| Chi Tiết Tính Năng & Chất Liệu", fill=(100, 116, 139), font=f_r)

    # Large Center Graphic with Zoom Focus
    draw_icon_graphic(draw_det, p["icon"], 400, 350, p["color2"], p["accent"])

    # Detail callout bubbles
    draw_det.rounded_rectangle([70, 200, 260, 260], radius=16, fill=(255, 255, 255, 230), outline=p_rgb, width=2)
    draw_det.text((85, 218), "🔍 Độ Bền Cao", fill=hex_to_rgb(p["accent"]), font=f_sub)

    draw_det.rounded_rectangle([540, 200, 730, 260], radius=16, fill=(255, 255, 255, 230), outline=p_rgb, width=2)
    draw_det.text((555, 218), "✨ Chuẩn Thiết Kế", fill=hex_to_rgb(p["accent"]), font=f_sub)

    # Bottom Info
    draw_det.rounded_rectangle([55, 590, W-55, 740], radius=20, fill=(255, 255, 255, 240), outline=(226, 232, 240), width=2)
    draw_det.text((80, 615), f"Cận Cảnh Chi Tiết: {p['name']}", fill=(15, 23, 42), font=f_b)
    draw_det.text((80, 665), "Chất liệu cao cấp, an toàn cho học sinh & trải nghiệm sử dụng vượt trội", fill=(100, 116, 139), font=f_r)

    out_det = os.path.join(OUTPUT_DIR, f"{p['code']}_detail.png")
    img_det.save(out_det, "PNG", quality=95)

    # 2. Combo / Box Packaging Shot
    img_box = Image.new("RGB", (W, H), color="#FFFFFF")
    draw_box = ImageDraw.Draw(img_box)
    draw_gradient(draw_box, W, H, (241, 245, 249), hex_to_rgb(p["color1"]))
    draw_box.rounded_rectangle([25, 25, W-25, H-25], radius=32, outline=(*p_rgb, 70), width=3)

    draw_box.text((60, 55), "HieuMini", fill=hex_to_rgb(p["accent"]), font=f_b)
    draw_box.text((185, 60), "| Quy Cách Đóng Hộp & Phụ Kiện", fill=(100, 116, 139), font=f_r)

    # Center Dual Graphic
    draw_icon_graphic(draw_box, p["icon"], 330, 360, p["color2"], p["accent"])
    draw_icon_graphic(draw_box, "notebook" if "pen" in p["icon"] else "pen", 480, 370, "#EC4899", "#BE185D")

    draw_box.rounded_rectangle([55, 590, W-55, 740], radius=20, fill=(255, 255, 255, 240), outline=(226, 232, 240), width=2)
    draw_box.text((80, 615), f"Hộp Đựng & Phụ Kiện: {p['name']}", fill=(15, 23, 42), font=f_b)
    draw_box.text((80, 665), "Đóng gói bọc xốp chống sốc nguyên hộp, tặng kèm sticker xinh xắn", fill=(100, 116, 139), font=f_r)

    out_box = os.path.join(OUTPUT_DIR, f"{p['code']}_box.png")
    img_box.save(out_box, "PNG", quality=95)

print("Generated 60 additional detail and packaging gallery images successfully!")
