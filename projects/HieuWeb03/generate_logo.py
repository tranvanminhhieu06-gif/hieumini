import os
from PIL import Image, ImageDraw, ImageFont

LOGO_DIR = r"c:\Users\tranv\Desktop\HieuWeb03\assets\images"

def generate_logo():
    W, H = 320, 80
    img = Image.new("RGBA", (W, H), (255, 255, 255, 0))
    draw = ImageDraw.Draw(img)
    
    # Draw logo icon badge
    draw.rounded_rectangle([10, 10, 70, 70], radius=16, fill=(79, 70, 229, 255))
    # Icon inner pen / book graphic
    draw.polygon([(40, 20), (55, 55), (40, 50), (25, 55)], fill=(255, 255, 255, 255))
    draw.ellipse([36, 56, 44, 64], fill=(250, 204, 21, 255))
    
    try:
        f_main = ImageFont.truetype("arialbd.ttf", 36)
        f_sub = ImageFont.truetype("arial.ttf", 13)
    except:
        f_main = ImageFont.load_default()
        f_sub = f_main
        
    draw.text((85, 12), "HieuMini", fill=(30, 41, 59, 255), font=f_main)
    draw.text((87, 50), "STATIONERY STORE", fill=(99, 102, 241, 255), font=f_sub)
    
    img.save(os.path.join(LOGO_DIR, "logo.png"), "PNG")
    
    # Favicon 64x64
    fav = Image.new("RGBA", (64, 64), (79, 70, 229, 255))
    draw_fav = ImageDraw.Draw(fav)
    draw_fav.polygon([(32, 14), (46, 46), (32, 42), (18, 46)], fill=(255, 255, 255, 255))
    draw_fav.ellipse([29, 48, 35, 54], fill=(250, 204, 21, 255))
    fav.save(os.path.join(LOGO_DIR, "favicon.png"), "PNG")
    print("Logo and Favicon created!")

generate_logo()
