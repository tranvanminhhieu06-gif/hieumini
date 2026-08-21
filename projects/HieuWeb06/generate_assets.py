#!/usr/bin/env python3
# -*- coding: utf-8 -*-
"""
HieuMini - Sinh bộ ảnh SVG (logo, favicon, ảnh chia sẻ, ảnh đại diện dự án & bài viết).
Dùng SVG thay cho ảnh bitmap: dung lượng nhỏ, sắc nét trên mọi màn hình,
tải nhanh nên có lợi cho điểm Core Web Vitals.

Chạy:  python generate_assets.py
"""
import os
import html

BASE = os.path.dirname(os.path.abspath(__file__))
IMG = os.path.join(BASE, "assets", "images")
os.makedirs(os.path.join(IMG, "projects"), exist_ok=True)
os.makedirs(os.path.join(IMG, "blog"), exist_ok=True)

# (tệp, nhãn ngắn, tiêu đề, phụ đề, màu 1, màu 2)
PROJECTS = [
    ("hieushop-pro", "SHOP", "HieuShop Pro", "E-commerce · PHP · MySQL", "#7C3AED", "#22D3EE"),
    ("minimart", "MART", "MiniMart", "Siêu thị mini · Tồn kho", "#0EA5E9", "#22D3EE"),
    ("fashionhub", "MODE", "FashionHub", "Thời trang · Biến thể", "#F43F5E", "#A855F7"),
    ("corpvision", "CORP", "CorpVision", "Doanh nghiệp · Đa ngôn ngữ", "#6366F1", "#22D3EE"),
    ("landingx", "LAND", "LandingX", "8 mẫu landing page", "#F59E0B", "#F43F5E"),
    ("cliniccare", "CARE", "ClinicCare", "Phòng khám · Đặt lịch", "#10B981", "#22D3EE"),
    ("devfolio", "DEV", "DevFolio", "Portfolio lập trình viên", "#8B5CF6", "#EC4899"),
    ("photolens", "LENS", "PhotoLens", "Nhiếp ảnh · Masonry", "#64748B", "#22D3EE"),
    ("adminforge", "ADMIN", "AdminForge", "Khung quản trị · RBAC", "#7C3AED", "#F59E0B"),
    ("stockflow", "STOCK", "StockFlow", "Quản lý kho nhiều chi nhánh", "#0891B2", "#10B981"),
    ("hrinsight", "HR", "HR Insight", "Nhân sự · Chấm công", "#2563EB", "#A855F7"),
    ("edulearn", "EDU", "EduLearn", "LMS · Khoá học trực tuyến", "#F59E0B", "#22D3EE"),
    ("quizmaster", "QUIZ", "QuizMaster", "Thi trắc nghiệm online", "#EC4899", "#8B5CF6"),
    ("staybooking", "STAY", "StayBooking", "Khách sạn · Đặt phòng", "#0EA5E9", "#6366F1"),
    ("foodiego", "FOOD", "FoodieGo", "Nhà hàng · Đặt bàn", "#EF4444", "#F59E0B"),
    ("travelnest", "TOUR", "TravelNest", "Tour du lịch trực tuyến", "#14B8A6", "#22D3EE"),
    ("gadgetzone", "TECH", "GadgetZone", "Điện máy · So sánh", "#3B82F6", "#22D3EE"),
    ("blogverse", "BLOG", "BlogVerse", "Blog cá nhân chuẩn SEO", "#A855F7", "#22D3EE"),
]

POSTS = [
    ("post-1", "7 tiêu chí chọn mua mã nguồn", "#7C3AED", "#22D3EE"),
    ("post-2", "Checklist SEO On-page", "#22D3EE", "#10B981"),
    ("post-3", "Bảo mật website PHP", "#F43F5E", "#7C3AED"),
    ("post-4", "Tối ưu Core Web Vitals", "#F59E0B", "#EC4899"),
    ("post-5", "Từ đồ án đến sản phẩm", "#10B981", "#22D3EE"),
    ("post-6", "Dark mode chuẩn WCAG", "#6366F1", "#A855F7"),
]

FONT = "Segoe UI, Be Vietnam Pro, system-ui, sans-serif"


def card_svg(tag, title, subtitle, c1, c2, w=800, h=500):
    """Ảnh đại diện dự án: nền gradient tối + lưới + khối kính + chữ."""
    return f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w} {h}" width="{w}" height="{h}" role="img" aria-label="{html.escape(title)}">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="{c1}"/><stop offset="1" stop-color="{c2}"/>
    </linearGradient>
    <linearGradient id="bgg" x1="0" y1="0" x2="1" y2="1">
      <stop offset="0" stop-color="#12122A"/><stop offset="1" stop-color="#0B0B18"/>
    </linearGradient>
    <pattern id="grid" width="40" height="40" patternUnits="userSpaceOnUse">
      <path d="M40 0H0V40" fill="none" stroke="rgba(150,160,255,.10)" stroke-width="1"/>
    </pattern>
    <filter id="blur"><feGaussianBlur stdDeviation="60"/></filter>
  </defs>
  <rect width="{w}" height="{h}" fill="url(#bgg)"/>
  <circle cx="{w*0.18:.0f}" cy="{h*0.16:.0f}" r="150" fill="{c1}" opacity=".42" filter="url(#blur)"/>
  <circle cx="{w*0.85:.0f}" cy="{h*0.82:.0f}" r="170" fill="{c2}" opacity=".34" filter="url(#blur)"/>
  <rect width="{w}" height="{h}" fill="url(#grid)"/>
  <g transform="translate(56,80)">
    <rect width="150" height="40" rx="20" fill="none" stroke="url(#g)" stroke-width="2" opacity=".9"/>
    <text x="75" y="26" font-family="{FONT}" font-size="16" font-weight="700"
          letter-spacing="3" fill="url(#g)" text-anchor="middle">{html.escape(tag)}</text>
  </g>
  <text x="56" y="{h*0.52:.0f}" font-family="{FONT}" font-size="52" font-weight="800"
        fill="#EDEFFA">{html.escape(title)}</text>
  <text x="58" y="{h*0.62:.0f}" font-family="{FONT}" font-size="22" font-weight="500"
        fill="#A2A7C6">{html.escape(subtitle)}</text>
  <g transform="translate(56,{h*0.70:.0f})" opacity=".95">
    <rect width="120" height="10" rx="5" fill="url(#g)"/>
    <rect y="24" width="220" height="8" rx="4" fill="rgba(150,160,255,.22)"/>
    <rect y="44" width="170" height="8" rx="4" fill="rgba(150,160,255,.16)"/>
  </g>
  <g transform="translate({w-250},{h-190})" opacity=".9">
    <rect width="190" height="130" rx="16" fill="rgba(30,28,56,.72)" stroke="rgba(150,160,255,.2)"/>
    <circle cx="22" cy="22" r="5" fill="#F43F5E"/><circle cx="40" cy="22" r="5" fill="#F59E0B"/><circle cx="58" cy="22" r="5" fill="#10B981"/>
    <rect x="18" y="46" width="120" height="9" rx="4.5" fill="url(#g)"/>
    <rect x="18" y="66" width="154" height="7" rx="3.5" fill="rgba(150,160,255,.24)"/>
    <rect x="18" y="84" width="96" height="7" rx="3.5" fill="rgba(150,160,255,.18)"/>
    <rect x="18" y="102" width="60" height="14" rx="7" fill="url(#g)" opacity=".85"/>
  </g>
</svg>
'''


def post_svg(title, c1, c2, w=800, h=450):
    return f'''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 {w} {h}" width="{w}" height="{h}" role="img" aria-label="{html.escape(title)}">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="{c1}"/><stop offset="1" stop-color="{c2}"/></linearGradient>
    <linearGradient id="bgg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#141430"/><stop offset="1" stop-color="#0B0B18"/></linearGradient>
    <filter id="blur"><feGaussianBlur stdDeviation="55"/></filter>
  </defs>
  <rect width="{w}" height="{h}" fill="url(#bgg)"/>
  <circle cx="{w*0.78:.0f}" cy="{h*0.22:.0f}" r="140" fill="{c1}" opacity=".4" filter="url(#blur)"/>
  <circle cx="{w*0.2:.0f}" cy="{h*0.85:.0f}" r="150" fill="{c2}" opacity=".3" filter="url(#blur)"/>
  <g transform="translate(56,64)">
    <rect width="46" height="6" rx="3" fill="url(#g)"/>
    <text y="70" font-family="{FONT}" font-size="40" font-weight="800" fill="#EDEFFA">{html.escape(title)}</text>
    <text y="112" font-family="{FONT}" font-size="19" fill="#A2A7C6">HieuMini · Blog kiến thức lập trình web</text>
  </g>
  <g transform="translate(56,{h-140})" opacity=".9">
    <rect width="300" height="9" rx="4.5" fill="rgba(150,160,255,.2)"/>
    <rect y="24" width="240" height="9" rx="4.5" fill="rgba(150,160,255,.16)"/>
    <rect y="48" width="180" height="9" rx="4.5" fill="rgba(150,160,255,.12)"/>
    <rect y="80" width="110" height="12" rx="6" fill="url(#g)"/>
  </g>
</svg>
'''


LOGO = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 260 64" width="260" height="64" role="img" aria-label="HieuMini">
  <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#7C3AED"/><stop offset="1" stop-color="#22D3EE"/></linearGradient></defs>
  <rect x="4" y="12" width="40" height="40" rx="12" fill="url(#g)" opacity=".18"/>
  <rect x="4" y="12" width="40" height="40" rx="12" fill="none" stroke="url(#g)" stroke-width="2"/>
  <path d="M15 44V20h4v9.6h10V20h4v24h-4v-10.4H19V44z" fill="url(#g)"/>
  <text x="58" y="41" font-family="Segoe UI, system-ui, sans-serif" font-size="26" font-weight="800" fill="#E9EAF6">Hieu</text>
  <text x="122" y="41" font-family="Segoe UI, system-ui, sans-serif" font-size="26" font-weight="800" fill="url(#g)">Mini</text>
</svg>
'''

FAVICON = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 64 64" width="64" height="64">
  <defs><linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#7C3AED"/><stop offset="1" stop-color="#22D3EE"/></linearGradient></defs>
  <rect width="64" height="64" rx="16" fill="#0B0B18"/>
  <rect x="4" y="4" width="56" height="56" rx="14" fill="url(#g)" opacity=".2"/>
  <path d="M18 48V16h6v12h16V16h6v32h-6V34H24v14z" fill="url(#g)"/>
</svg>
'''

OG = '''<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 1200 630" width="1200" height="630" role="img" aria-label="HieuMini - Chợ mã nguồn website chuẩn SEO">
  <defs>
    <linearGradient id="g" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#7C3AED"/><stop offset="1" stop-color="#22D3EE"/></linearGradient>
    <linearGradient id="bgg" x1="0" y1="0" x2="1" y2="1"><stop offset="0" stop-color="#14142E"/><stop offset="1" stop-color="#0B0B18"/></linearGradient>
    <pattern id="grid" width="48" height="48" patternUnits="userSpaceOnUse"><path d="M48 0H0V48" fill="none" stroke="rgba(150,160,255,.09)"/></pattern>
    <filter id="blur"><feGaussianBlur stdDeviation="90"/></filter>
  </defs>
  <rect width="1200" height="630" fill="url(#bgg)"/>
  <circle cx="200" cy="120" r="220" fill="#7C3AED" opacity=".45" filter="url(#blur)"/>
  <circle cx="1020" cy="520" r="240" fill="#22D3EE" opacity=".35" filter="url(#blur)"/>
  <rect width="1200" height="630" fill="url(#grid)"/>
  <g transform="translate(90,150)">
    <rect width="58" height="58" rx="16" fill="url(#g)" opacity=".2"/>
    <rect width="58" height="58" rx="16" fill="none" stroke="url(#g)" stroke-width="2.5"/>
    <path d="M16 46V12h6v13h14V12h6v34h-6V31H22v15z" fill="url(#g)"/>
    <text x="76" y="42" font-family="Segoe UI, system-ui, sans-serif" font-size="38" font-weight="800" fill="#E9EAF6">Hieu<tspan fill="url(#g)">Mini</tspan></text>
    <text y="150" font-family="Segoe UI, system-ui, sans-serif" font-size="66" font-weight="800" fill="#EDEFFA">Chợ mã nguồn website</text>
    <text y="228" font-family="Segoe UI, system-ui, sans-serif" font-size="66" font-weight="800" fill="url(#g)">chuẩn SEO cho người Việt</text>
    <text y="292" font-family="Segoe UI, system-ui, sans-serif" font-size="27" fill="#A2A7C6">PHP 8 · MySQL 8 · Code sạch · Bảo mật · Tài liệu đầy đủ · Hỗ trợ trọn đời</text>
  </g>
</svg>
'''


def write(path, content):
    with open(path, "w", encoding="utf-8") as f:
        f.write(content)
    print("  +", os.path.relpath(path, BASE).replace("\\", "/"))


def main():
    print("Đang sinh bộ ảnh SVG cho HieuMini…")
    write(os.path.join(IMG, "logo.svg"), LOGO)
    write(os.path.join(IMG, "favicon.svg"), FAVICON)
    write(os.path.join(IMG, "og-cover.svg"), OG)
    for slug, tag, title, sub, c1, c2 in PROJECTS:
        write(os.path.join(IMG, "projects", slug + ".svg"), card_svg(tag, title, sub, c1, c2))
    for slug, title, c1, c2 in POSTS:
        write(os.path.join(IMG, "blog", slug + ".svg"), post_svg(title, c1, c2))
    print(f"Hoàn tất: {len(PROJECTS) + len(POSTS) + 3} tệp.")


if __name__ == "__main__":
    main()
