# -*- coding: utf-8 -*-
"""
Script tải và tối ưu hóa bộ hình ảnh thời trang thực tế chất lượng cao từ CDN Unsplash cho HieuMini
"""
import os
import sys
import urllib.request
import io
from PIL import Image, ImageOps, ImageDraw, ImageFont

if sys.stdout:
    try:
        sys.stdout.reconfigure(encoding='utf-8')
    except Exception:
        pass

BASE_DIR = r"c:\Users\tranv\Desktop\HieuWeb01"
IMG_DIR = os.path.join(BASE_DIR, "assets", "images")
PROD_DIR = os.path.join(IMG_DIR, "products")
CAT_DIR = os.path.join(IMG_DIR, "categories")
BANNER_DIR = os.path.join(IMG_DIR, "banners")

for d in [IMG_DIR, PROD_DIR, CAT_DIR, BANNER_DIR]:
    os.makedirs(d, exist_ok=True)

HEADERS = {'User-Agent': 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36'}

def fetch_and_save(url, target_path, target_size=(800, 800), quality=90):
    try:
        req = urllib.request.Request(url, headers=HEADERS)
        with urllib.request.urlopen(req, timeout=12) as response:
            img_bytes = response.read()
            img = Image.open(io.BytesIO(img_bytes))
            if img.mode != 'RGB':
                img = img.convert('RGB')
            # Fit crop to exact target size
            img_cropped = ImageOps.fit(img, target_size, Image.Resampling.LANCZOS)
            img_cropped.save(target_path, "JPEG", quality=quality, optimize=True)
            print(f"✓ Đã tải & xử lý thành công: {os.path.basename(target_path)} ({target_size[0]}x{target_size[1]})")
            return True
    except Exception as e:
        print(f"✗ Lỗi tải {os.path.basename(target_path)}: {e}")
        return False

# 1. Danh sách sản phẩm thực tế
PRODUCT_PHOTOS = {
    # Áo thun & Polo
    "ao_thun_streetwear.jpg": "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=900&auto=format&fit=crop&q=85",
    "ao_polo_dệt_bo.jpg": "https://images.unsplash.com/photo-1586363104862-3a5e2ab60d99?w=900&auto=format&fit=crop&q=85",
    "ao_thun_graphic_hieu.jpg": "https://images.unsplash.com/photo-1583743814966-8936f5b7be1a?w=900&auto=format&fit=crop&q=85",
    
    # Sơ mi
    "ao_so_mi_oxford.jpg": "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=900&auto=format&fit=crop&q=85",
    "ao_so_mi_cubano.jpg": "https://images.unsplash.com/photo-1607345366928-199ea26cfe3e?w=900&auto=format&fit=crop&q=85",
    "ao_so_mi_caro.jpg": "https://images.unsplash.com/photo-1618354691373-d851c5c3a990?w=900&auto=format&fit=crop&q=85",
    
    # Áo khoác & Hoodie
    "ao_khoac_bomber.jpg": "https://images.unsplash.com/photo-1551028719-00167b16eac5?w=900&auto=format&fit=crop&q=85",
    "ao_hoodie_ni_bong.jpg": "https://images.unsplash.com/photo-1556905055-8f358a7a47b2?w=900&auto=format&fit=crop&q=85",
    "ao_blazer_han_quoc.jpg": "https://images.unsplash.com/photo-1591047139829-d91aecb6caea?w=900&auto=format&fit=crop&q=85",
    
    # Quần Jeans
    "quan_jean_slimfit.jpg": "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=900&auto=format&fit=crop&q=85",
    "quan_jean_baggy.jpg": "https://images.unsplash.com/photo-1582552938357-32b906df40cb?w=900&auto=format&fit=crop&q=85",
    
    # Quần Kaki & Trousers
    "quan_kaki_chino.jpg": "https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=900&auto=format&fit=crop&q=85",
    "quan_tay_au.jpg": "https://images.unsplash.com/photo-1506629082955-511b1aa562c8?w=900&auto=format&fit=crop&q=85",
    
    # Váy đầm nữ
    "dam_hoa_nhi.jpg": "https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=900&auto=format&fit=crop&q=85",
    "chan_vay_chu_a.jpg": "https://images.unsplash.com/photo-1583496661160-fb5886a0aaaa?w=900&auto=format&fit=crop&q=85",
    
    # Phụ kiện
    "that_lung_da.jpg": "https://images.unsplash.com/photo-1624222247344-550fb60583dc?w=900&auto=format&fit=crop&q=85",
    "non_ket_hieumini.jpg": "https://images.unsplash.com/photo-1588850561407-ed78c282e89b?w=900&auto=format&fit=crop&q=85"
}

# 2. Danh mục
CATEGORY_PHOTOS = {
    "cat_ao_thun.jpg": "https://images.unsplash.com/photo-1521572267360-ee0c2909d518?w=600&auto=format&fit=crop&q=80",
    "cat_ao_somi.jpg": "https://images.unsplash.com/photo-1596755094514-f87e34085b2c?w=600&auto=format&fit=crop&q=80",
    "cat_ao_khoac.jpg": "https://images.unsplash.com/photo-1551028719-00167b16eac5?w=600&auto=format&fit=crop&q=80",
    "cat_quan_jeans.jpg": "https://images.unsplash.com/photo-1541099649105-f69ad21f3246?w=600&auto=format&fit=crop&q=80",
    "cat_quan_kaki.jpg": "https://images.unsplash.com/photo-1473966968600-fa801b869a1a?w=600&auto=format&fit=crop&q=80",
    "cat_vay_dam.jpg": "https://images.unsplash.com/photo-1572804013309-59a88b7e92f1?w=600&auto=format&fit=crop&q=80",
    "cat_phu_kien.jpg": "https://images.unsplash.com/photo-1624222247344-550fb60583dc?w=600&auto=format&fit=crop&q=80"
}

# 3. Banners
BANNER_PHOTOS = {
    "hero_banner_1.jpg": "https://images.unsplash.com/photo-1441986300917-64674bd600d8?w=1400&auto=format&fit=crop&q=85",
    "hero_banner_2.jpg": "https://images.unsplash.com/photo-1483985988355-763728e1935b?w=1400&auto=format&fit=crop&q=85",
    "hero_banner_3.jpg": "https://images.unsplash.com/photo-1490481651871-ab68de25d43d?w=1400&auto=format&fit=crop&q=85"
}

print("--- 1. ĐANG TẢI ẢNH SẢN PHẨM THỜI TRANG THỰC TẾ ---")
for fname, url in PRODUCT_PHOTOS.items():
    t_path = os.path.join(PROD_DIR, fname)
    fetch_and_save(url, t_path, target_size=(700, 700))

print("\n--- 2. ĐANG TẢI ẢNH DANH MỤC ---")
for fname, url in CATEGORY_PHOTOS.items():
    t_path = os.path.join(CAT_DIR, fname)
    fetch_and_save(url, t_path, target_size=(500, 350))

print("\n--- 3. ĐANG TẢI ẢNH BANNERS QUẢNG BÁ ---")
for fname, url in BANNER_PHOTOS.items():
    t_path = os.path.join(BANNER_DIR, fname)
    fetch_and_save(url, t_path, target_size=(1300, 520))

print("\n=== HOÀN TẤT CẬP NHẬT TOÀN BỘ ẢNH SẢN PHẨM THỜI TRANG CHUYÊN NGHIỆP ===")
