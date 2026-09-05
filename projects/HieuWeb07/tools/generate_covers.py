# -*- coding: utf-8 -*-
"""
Sinh 30 bìa sách cho HieuMini Books.

Vì sao tự vẽ thay vì tải ảnh về: bìa sách thật đều có bản quyền của nhà xuất
bản, dùng trong đồ án là rủi ro. Bìa ở đây là thiết kế chữ (typographic) do
chính dự án tạo ra — an toàn pháp lý và đồng bộ phong cách với giao diện tối
kiểu tạp chí của website.

Xuất WebP cho web (nhẹ hơn PNG khoảng 30-40%) và PNG cho báo cáo Word,
vì python-docx không chèn được WebP.

Chạy:  python tools/generate_covers.py
"""
import os
import sys
import random

from PIL import Image, ImageDraw, ImageFont, ImageFilter

sys.path.insert(0, os.path.dirname(os.path.abspath(__file__)))
from catalog import BOOKS, CATEGORY_BY_SLUG, cover_filename, slugify  # noqa: E402

W, H = 600, 900
ROOT = os.path.dirname(os.path.dirname(os.path.abspath(__file__)))
WEB_DIR = os.path.join(ROOT, "assets", "img", "covers")
PNG_DIR = os.environ.get("COVER_PNG_DIR", os.path.join(ROOT, "..", "hieu07-report-assets"))

FONT_SERIF = "/usr/share/fonts/truetype/google-fonts/Lora-Variable.ttf"
FONT_SERIF_ITALIC = "/usr/share/fonts/truetype/google-fonts/Lora-Italic-Variable.ttf"
FONT_FALLBACK = "/usr/share/fonts/truetype/dejavu/DejaVuSerif.ttf"


def load_font(path, size, weight=None):
    """Nạp font; Lora là font biến thiên nên chỉnh được độ đậm qua trục wght."""
    try:
        f = ImageFont.truetype(path, size)
        if weight is not None:
            try:
                f.set_variation_by_axes([weight])
            except Exception:
                pass
        return f
    except OSError:
        return ImageFont.truetype(FONT_FALLBACK, size)


def hex_rgb(h):
    h = h.lstrip("#")
    return tuple(int(h[i:i + 2], 16) for i in (0, 2, 4))


def mix(c1, c2, t):
    return tuple(round(a + (b - a) * t) for a, b in zip(c1, c2))


def wrap(draw, text, font, max_w):
    """Ngắt dòng theo bề rộng thực đo được, không đoán theo số ký tự."""
    words, lines, cur = text.split(), [], ""
    for w in words:
        trial = f"{cur} {w}".strip()
        if draw.textlength(trial, font=font) <= max_w or not cur:
            cur = trial
        else:
            lines.append(cur)
            cur = w
    if cur:
        lines.append(cur)
    return lines


def paper_grain(size, seed, strength=7):
    """Lớp nhiễu mảnh khiến nền phẳng bớt cảm giác nhựa, giống mặt giấy in."""
    rnd = random.Random(seed)
    small = Image.new("L", (size[0] // 3, size[1] // 3))
    small.putdata([128 + rnd.randint(-strength, strength) for _ in range(small.width * small.height)])
    return small.resize(size, Image.BICUBIC).filter(ImageFilter.GaussianBlur(0.4))


def make_cover(title, author, cat_slug, publisher, year, seed):
    cat = CATEGORY_BY_SLUG[cat_slug]
    bg, accent = hex_rgb(cat[3]), hex_rgb(cat[4])

    # Nền chuyển sắc dọc, sáng dần lên phía trên cho có chiều sâu
    img = Image.new("RGB", (W, H), bg)
    d = ImageDraw.Draw(img)
    top = mix(bg, (255, 255, 255), 0.07)
    for y in range(H):
        d.line([(0, y), (W, y)], fill=mix(top, bg, y / H))

    img = Image.composite(img, Image.new("RGB", (W, H), mix(bg, (255, 255, 255), 0.05)),
                          paper_grain((W, H), seed))
    d = ImageDraw.Draw(img)

    # Gáy sách: dải tối sát mép trái để khối ảnh đọc ra là "quyển sách"
    for x in range(26):
        d.line([(x, 0), (x, H)], fill=mix(mix(bg, (0, 0, 0), 0.55), bg, x / 26))
    d.line([(27, 0), (27, H)], fill=mix(bg, (255, 255, 255), 0.10))

    m = 64  # lề trong
    d.rectangle([m, 52, W - 40, H - 52], outline=mix(accent, bg, 0.62), width=1)

    # Nhãn danh mục dạng chữ hoa giãn rộng
    f_label = load_font(FONT_SERIF, 15, 600)
    label = " ".join(cat[1].upper())
    d.text((m + 26, 92), label[:46], font=f_label, fill=mix(accent, bg, 0.25))

    # Tựa sách: giảm cỡ dần cho tới khi vừa 5 dòng, tránh tràn khung
    size, lines = 62, []
    while size >= 30:
        f_title = load_font(FONT_SERIF, size, 600)
        lines = wrap(d, title, f_title, W - m * 2 - 52)
        if len(lines) <= 5:
            break
        size -= 4
    f_title = load_font(FONT_SERIF, size, 600)

    y = 250
    for ln in lines:
        d.text((m + 26, y), ln, font=f_title, fill=(244, 241, 236))
        y += int(size * 1.22)

    d.line([(m + 26, y + 26), (m + 116, y + 26)], fill=accent, width=2)

    f_author = load_font(FONT_SERIF_ITALIC, 27, 500)
    for i, ln in enumerate(wrap(d, author, f_author, W - m * 2 - 52)[:2]):
        d.text((m + 26, y + 56 + i * 34), ln, font=f_author, fill=mix(accent, (255, 255, 255), 0.45))

    f_foot = load_font(FONT_SERIF, 16, 400)
    foot = f"{publisher} · {year}" if year and year > 0 else publisher
    d.text((m + 26, H - 104), foot, font=f_foot, fill=mix(bg, (255, 255, 255), 0.42))
    d.text((W - 40 - 92, H - 104), "HIEUMINI", font=load_font(FONT_SERIF, 14, 600),
           fill=mix(accent, bg, 0.42))
    return img


def main():
    os.makedirs(WEB_DIR, exist_ok=True)
    os.makedirs(PNG_DIR, exist_ok=True)

    made = []
    for i, b in enumerate(BOOKS):
        title, author, cat_slug, publisher, year = b[0], b[1], b[2], b[3], b[4]
        img = make_cover(title, author, cat_slug, publisher, year, seed=i * 977 + 13)
        stem = slugify(title)
        img.save(os.path.join(WEB_DIR, f"{stem}.webp"), "WEBP", quality=86, method=6)
        img.save(os.path.join(PNG_DIR, f"{stem}.png"), "PNG", optimize=True)
        made.append(stem)

    # Ảnh chia sẻ mạng xã hội 1200x630: xếp 7 bìa nghiêng trên nền tối
    og = Image.new("RGB", (1200, 630), (18, 17, 20))
    for k, stem in enumerate(made[:7]):
        c = Image.open(os.path.join(WEB_DIR, f"{stem}.webp")).resize((186, 279))
        og.paste(c, (54 + k * 158, 176 + (k % 2) * 18))
    dd = ImageDraw.Draw(og)
    dd.text((54, 64), "HieuMini Books", font=load_font(FONT_SERIF, 54, 600), fill=(244, 241, 236))
    dd.text((58, 132), "Thư viện sách trực tuyến · PHP & MySQL",
            font=load_font(FONT_SERIF, 23, 400), fill=(190, 160, 96))
    og.save(os.path.join(WEB_DIR, "og-default.webp"), "WEBP", quality=88, method=6)
    og.save(os.path.join(PNG_DIR, "og-default.png"), "PNG", optimize=True)

    total = sum(os.path.getsize(os.path.join(WEB_DIR, f)) for f in os.listdir(WEB_DIR))
    print(f"Đã sinh {len(made)} bìa sách + 1 ảnh OG")
    print(f"WebP: {WEB_DIR} — tổng {total / 1024:.0f} KB, trung bình {total / 1024 / (len(made) + 1):.0f} KB/ảnh")
    print(f"PNG cho báo cáo: {os.path.abspath(PNG_DIR)}")


if __name__ == "__main__":
    main()
