# 🛒 HieuMini — Chợ mã nguồn website chuẩn SEO

<div align="center">

![PHP](https://img.shields.io/badge/PHP-8.2%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)
![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)
![No Framework](https://img.shields.io/badge/Framework-Không%20dùng-10B981?style=for-the-badge)
![SEO](https://img.shields.io/badge/SEO-19%2F19%20hạng%20mục-22D3EE?style=for-the-badge&logo=google&logoColor=white)
![Security](https://img.shields.io/badge/Bảo%20mật-PDO%20|%20CSRF%20|%20BCRYPT-F59E0B?style=for-the-badge)
![UI](https://img.shields.io/badge/UI-Dark%20Neon%20Glassmorphism-7C3AED?style=for-the-badge)
![Theme](https://img.shields.io/badge/Giao%20diện-Sáng%20%2B%20Tối-0E7490?style=for-the-badge)

**Website thương mại điện tử bán mã nguồn dự án website — viết bằng PHP 8 thuần và MySQL, không dùng framework.**

[Trang chủ](#) • [Kho dự án](#) • [Quản trị](#-tài-khoản-dùng-thử) • [Báo cáo đồ án](BaoCao.docx)

</div>

---

## 📑 Mục lục

1. [Giới thiệu](#-1-giới-thiệu)
2. [Tính năng](#-2-tính-năng)
3. [Công nghệ sử dụng](#-3-công-nghệ-sử-dụng)
4. [Yêu cầu hệ thống](#-4-yêu-cầu-hệ-thống)
5. [Cài đặt trong 5 phút](#-5-cài-đặt-trong-5-phút)
6. [Tài khoản dùng thử](#-6-tài-khoản-dùng-thử)
7. [Cấu trúc thư mục](#-7-cấu-trúc-thư-mục)
8. [Cơ sở dữ liệu](#-8-cơ-sở-dữ-liệu)
9. [Hệ thống thiết kế & hoạt ảnh](#-9-hệ-thống-thiết-kế--hoạt-ảnh)
10. [Tối ưu SEO](#-10-tối-ưu-seo)
11. [Bảo mật](#-11-bảo-mật)
12. [Tuỳ biến thương hiệu](#-12-tuỳ-biến-thương-hiệu)
13. [Kiểm thử](#-13-kiểm-thử)
14. [Triển khai lên máy chủ thật](#-14-triển-khai-lên-máy-chủ-thật)
15. [Xử lý sự cố thường gặp](#-15-xử-lý-sự-cố-thường-gặp)
16. [Giấy phép](#-16-giấy-phép)

---

## 🌟 1. Giới thiệu

**HieuMini** là website thương mại điện tử chuyên bán **mã nguồn dự án website**. Đây là đồ án môn học Lập trình Web, đồng thời là một sản phẩm hoàn chỉnh có thể triển khai thực tế.

Điểm đặc biệt: HieuMini **tự chứng minh những gì nó bán**. Website cam kết bán mã nguồn *sạch, bảo mật và chuẩn SEO*, và chính bản thân nó được viết theo đúng những tiêu chuẩn đó — mã nguồn PHP 8 thuần có chú thích tiếng Việt, truy vấn tham số hoá bằng PDO, biểu mẫu có token CSRF, và bộ tối ưu SEO 19 hạng mục.

| Chỉ số | Giá trị |
|---|---|
| Tệp PHP | 41 tệp, tất cả vượt qua `php -l` |
| Dòng CSS hệ thống thiết kế | ~980 dòng, 3 lớp token |
| Bảng cơ sở dữ liệu | 12 bảng, chuẩn 3NF, InnoDB |
| Dữ liệu mẫu | 18 dự án · 6 danh mục · 6 bài viết · 4 tài khoản |
| Hiệu ứng chuyển động | 14 hiệu ứng, 60 FPS, tôn trọng `prefers-reduced-motion` |
| Hạng mục SEO hoàn thành | 19/19 |
| Chế độ hiển thị | Sáng + Tối, 19/19 cặp màu đạt WCAG 2.1 AA |
| Trường hợp kiểm thử đạt | 24/24 |

---

## ⚡ 2. Tính năng

### 👤 Phía khách hàng

| Nhóm | Chi tiết |
|---|---|
| **Duyệt sản phẩm** | Trang chủ theo phễu chuyển đổi, 6 danh mục, dự án nổi bật, dự án mới nhất |
| **Tìm kiếm & lọc** | Từ khoá, danh mục, khoảng giá, 5 tiêu chí sắp xếp, phân trang giữ nguyên bộ lọc |
| **Chi tiết dự án** | 4 thẻ nội dung, 3 mức giấy phép, ảnh, thông số kỹ thuật, đánh giá |
| **Giỏ hàng** | Thêm/xoá bằng AJAX không tải lại trang, đổi giấy phép tại chỗ, mã giảm giá |
| **Đặt hàng** | 3 phương thức thanh toán, ghi đơn bằng giao dịch CSDL, trang cảm ơn kèm mã đơn |
| **Tài khoản** | Đăng ký, đăng nhập, hồ sơ, đổi mật khẩu, lịch sử đơn hàng, tải lại mã nguồn |
| **Tương tác** | Danh sách yêu thích (AJAX), viết đánh giá 1–5 sao |
| **Nội dung** | Blog 6 bài chuẩn SEO, trang giới thiệu, bảng so sánh giấy phép, FAQ |
| **Liên hệ** | Biểu mẫu yêu cầu báo giá, lưu vào CSDL và hiện trong hộp thư quản trị |

### 👑 Phía quản trị

| Trang | Chức năng |
|---|---|
| `admin/index.php` | Bảng điều khiển: doanh thu, đơn hàng, thành viên, biểu đồ 6 tháng (CSS thuần), top 5 bán chạy |
| `admin/projects.php` | Danh sách dự án, tìm kiếm, ẩn/hiện, xoá |
| `admin/project-form.php` | Thêm/sửa dự án: 3 nhóm trường gồm thông tin, giá & hiển thị, SEO |
| `admin/categories.php` | CRUD danh mục, chặn xoá khi còn dự án bên trong |
| `admin/orders.php` | Lọc theo 4 trạng thái, đổi trạng thái ngay trên danh sách |
| `admin/order-detail.php` | Chi tiết đơn, thông tin khách, cập nhật trạng thái |
| `admin/users.php` | Khoá/mở khoá, đổi quyền, đặt lại mật khẩu |
| `admin/reviews.php` | Duyệt/ẩn/xoá đánh giá, tự tính lại điểm trung bình |
| `admin/posts.php` · `post-form.php` | CRUD bài viết blog kèm trường SEO riêng |
| `admin/contacts.php` | Hộp thư liên hệ, 3 trạng thái xử lý, trả lời qua email |
| `admin/settings.php` | Cấu hình 4 nhóm: chung, SEO, liên hệ, mạng xã hội |

---

## 🛠 3. Công nghệ sử dụng

| Lớp | Công nghệ | Ghi chú |
|---|---|---|
| Máy chủ | PHP 8.2+ | Thuần, không framework, `declare(strict_types=1)` |
| Cơ sở dữ liệu | MySQL 8 / MariaDB 10.4+ | InnoDB, utf8mb4_unicode_ci, khoá ngoại, FULLTEXT |
| Truy cập dữ liệu | PDO | Prepared Statement, `ATTR_EMULATE_PREPARES = false` |
| Giao diện | HTML5, CSS3 | Design tokens, CSS Grid, Flexbox, `backdrop-filter` |
| Tương tác | JavaScript ES6 | `fetch`, `IntersectionObserver`, `requestAnimationFrame` |
| Ảnh | SVG | 27 tệp tự sinh, tổng ~120 KB |
| Phông chữ | Space Grotesk + Be Vietnam Pro | Google Fonts, `display=swap` |
| Máy chủ web | Apache | `.htaccess`: rewrite, deflate, expires, security headers |

> **Không phụ thuộc thư viện ngoài.** Biểu đồ doanh thu, hiệu ứng cuộn, lightbox, accordion — tất cả viết tay bằng CSS và JavaScript thuần.

---

## 💻 4. Yêu cầu hệ thống

- PHP **8.0 trở lên** (khuyến nghị 8.2), bật `pdo_mysql`, `mbstring`, `json`
- MySQL **5.7+** hoặc MariaDB **10.4+**
- Apache có `mod_rewrite` (hoặc Nginx với cấu hình tương đương)
- Trình duyệt: Chrome, Edge, Firefox, Safari — hai phiên bản gần nhất

---

## 🚀 5. Cài đặt trong 5 phút

### Bước 1 — Chép mã nguồn

```
C:\xampp\htdocs\DoAnWebsite\projects\HieuMini\
```

### Bước 2 — Khởi động XAMPP

Mở **XAMPP Control Panel** → bấm **Start** ở dòng **Apache** và **MySQL**.

### Bước 3 — Nhập cơ sở dữ liệu

1. Mở <http://localhost/phpmyadmin>
2. Thẻ **Import** → **Choose File** → chọn `database.sql`
3. Character set để **utf-8** → bấm **Go**

Tệp `database.sql` tự tạo cơ sở dữ liệu `hieumini_market_db`, 12 bảng và toàn bộ dữ liệu mẫu. Có thể chạy lại nhiều lần vì mỗi bảng đều có `DROP TABLE IF EXISTS`.

### Bước 4 — Kiểm tra cấu hình

Mở `includes/config.php`, xác nhận 4 hằng số khớp với máy bạn:

```php
define('DB_HOST', '127.0.0.1');
define('DB_NAME', 'hieumini_market_db');
define('DB_USER', 'root');
define('DB_PASS', '');        // XAMPP mặc định để trống
```

### Bước 5 — Truy cập

| Địa chỉ | Mô tả |
|---|---|
| <http://localhost/DoAnWebsite/projects/HieuMini/> | Trang chủ |
| <http://localhost/DoAnWebsite/projects/HieuMini/admin/login.php> | Đăng nhập quản trị |
| <http://localhost/DoAnWebsite/projects/HieuMini/test_system.php> | **Tự kiểm tra hệ thống (37 mục)** |

> 💡 Chạy `test_system.php` trước tiên. Trang này kiểm tra phiên bản PHP, các phần mở rộng, kết nối CSDL, sự tồn tại của 12 bảng, 17 tệp lõi, ảnh đại diện dự án và các cơ chế bảo mật.

---

## 🔑 6. Tài khoản dùng thử

| Vai trò | Email | Mật khẩu | Vào ở đâu |
|---|---|---|---|
| 👑 Quản trị viên | `admin@hieumini.vn` | `admin123` | `admin/login.php` hoặc `login.php` |
| 👤 Thành viên | `user@hieumini.vn` | `user123` | `login.php` |
| 👤 Thành viên | `trang.le@example.com` | `user123` | `login.php` |
| 👤 Thành viên | `dat.pham@example.com` | `user123` | `login.php` |

**Mã giảm giá có sẵn:** `HIEUMINI10` (giảm 10%, đơn từ 1 triệu) · `SINHVIEN20` (giảm 20%, đơn từ 500 nghìn) · `GIAM300K` (giảm 300 nghìn, đơn từ 2 triệu)

---

## 📁 7. Cấu trúc thư mục

```
HieuMini/
├── .antigravityrules          # Quy tắc áp dụng skill ui-ux-pro-max
├── .htaccess                  # Rewrite, nén, cache, security headers
├── README.md                  # Tài liệu này
├── BaoCao.docx                # Báo cáo đồ án 48 trang
├── mucluc.txt                 # Mục lục yêu cầu của báo cáo
├── database.sql               # 12 bảng + dữ liệu mẫu
├── generate_assets.py         # Script sinh 27 ảnh SVG
├── test_system.php            # Trang tự kiểm tra 37 hạng mục
├── robots.txt                 # Khai báo cho robot tìm kiếm
│
├── index.php                  # Trang chủ
├── projects.php               # Kho dự án: tìm kiếm, lọc, sắp xếp, phân trang
├── project-detail.php         # Chi tiết dự án + đánh giá + schema Product
├── cart.php                   # Giỏ hàng
├── ajax-cart.php              # Điểm cuối AJAX (JSON) cho giỏ & yêu thích
├── checkout.php               # Thanh toán, ghi đơn bằng giao dịch
├── order-success.php          # Xác nhận đặt hàng
├── wishlist.php               # Danh sách yêu thích
├── login.php · register.php · logout.php · account.php
├── blog.php · blog-detail.php # Blog chuẩn SEO
├── about.php                  # Giới thiệu, giấy phép, FAQ (schema FAQPage)
├── contact.php                # Liên hệ & báo giá
├── sitemap.php                # Sitemap XML sinh động
├── rss.php                    # Nguồn cấp RSS
├── 404.php                    # Trang lỗi thân thiện
│
├── includes/
│   ├── config.php             # Kết nối PDO, phiên, hằng số, tự phát hiện BASE_URL
│   ├── functions.php          # 6 nhóm hàm dùng chung
│   ├── header.php             # Toàn bộ thẻ SEO + JSON-LD + điều hướng
│   ├── footer.php             # Chân trang + script
│   └── project-card.php       # Thẻ dự án dùng lại
│
├── admin/
│   ├── includes/header.php · footer.php
│   ├── index.php              # Bảng điều khiển
│   ├── login.php
│   ├── projects.php · project-form.php
│   ├── categories.php
│   ├── orders.php · order-detail.php
│   ├── users.php · reviews.php
│   ├── posts.php · post-form.php
│   ├── contacts.php
│   └── settings.php
│
├── assets/
│   ├── css/style.css          # Hệ thống thiết kế 17 phần
│   ├── js/main.js             # 11 nhóm tương tác
│   └── images/
│       ├── logo.svg · favicon.svg · og-cover.svg
│       ├── projects/          # 18 ảnh đại diện dự án
│       └── blog/              # 6 ảnh bài viết
│
└── uploads/                   # Thư mục tải lên (đã tắt thực thi PHP)
```

---

## 🗄 8. Cơ sở dữ liệu

**12 bảng, chuẩn 3NF, InnoDB, utf8mb4_unicode_ci**

| # | Bảng | Chức năng | Quan hệ chính |
|---|---|---|---|
| 1 | `users` | Thành viên & quản trị viên | 1–n với `orders`, `reviews`, `wishlists` |
| 2 | `categories` | Danh mục dự án | 1–n với `projects` |
| 3 | `projects` | Mã nguồn được bán *(bảng trung tâm)* | FK → `categories` |
| 4 | `project_images` | Thư viện ảnh của dự án | FK → `projects` |
| 5 | `coupons` | Mã giảm giá | — |
| 6 | `orders` | Đơn hàng | FK → `users` (cho phép NULL) |
| 7 | `order_items` | Chi tiết đơn hàng | FK → `orders`, `projects` |
| 8 | `reviews` | Đánh giá | UNIQUE(`project_id`,`user_id`) |
| 9 | `wishlists` | Yêu thích *(bảng trung gian n–n)* | UNIQUE(`user_id`,`project_id`) |
| 10 | `posts` | Bài viết blog | — |
| 11 | `contacts` | Liên hệ & báo giá | — |
| 12 | `settings` | Cấu hình khoá–giá trị | — |

**Chỉ mục:** khoá ngoại, `UNIQUE` trên `email`/`slug`/`order_code`, chỉ mục tổ hợp `(status, is_featured)`, và **FULLTEXT** trên 4 cột của `projects`.

**Phi chuẩn hoá có chủ đích (vì hiệu năng & nghiệp vụ):**
- `projects.rating_avg` / `rating_count` — lưu sẵn, cập nhật bằng `refresh_rating()` sau mỗi đánh giá.
- `order_items.title` — sao chép tên dự án tại thời điểm mua, để hoá đơn cũ không đổi khi dự án đổi tên hoặc bị xoá.

Sơ đồ ERD đầy đủ nằm ở **mục 2.2.1 của `BaoCao.docx`**.

---

## 🎨 9. Hệ thống thiết kế & hoạt ảnh

Phong cách: **Dark Neon Glassmorphism** — nền tối làm nổi bật ảnh sản phẩm, khối kính mờ, gradient tím–lam.

### Bảng token chính

```css
--bg: #0B0B18;          --surface: rgba(30,28,56,.62);
--primary: #7C3AED;     --accent: #22D3EE;
--fg: #E9EAF6;          --fg-muted: #A2A7C6;
--success: #10B981;     --warning: #F59E0B;   --danger: #F43F5E;
--font-display: 'Space Grotesk', 'Be Vietnam Pro';
--font-body: 'Be Vietnam Pro';
--sp-1…--sp-9: 4px…96px;    --r-sm…--r-full: 8px…999px;
--dur-fast/--dur/--dur-slow: 150ms/280ms/520ms;
```

### Hai chế độ hiển thị: Sáng và Tối

Nhấn nút ☀/🌙 trên thanh điều hướng (hoặc góc phải khu quản trị) để chuyển. Lựa chọn được ghi nhớ trong `localStorage`.

**Thứ tự ưu tiên khi chọn chế độ:**

1. Lựa chọn thủ công của người dùng (`localStorage['hm-theme']`)
2. Thiết lập hệ điều hành (`prefers-color-scheme`)
3. Mặc định: chế độ tối

Toàn bộ khác biệt nằm trong **một khối `[data-theme="light"]`** ở cuối `style.css` — chỉ ghi đè token màu, không nhân đôi một dòng bố cục nào.

| Token | Tối | Sáng |
|---|---|---|
| `--bg` | `#0B0B18` | `#F6F6FB` |
| `--fg` | `#E9EAF6` | `#14142B` |
| `--fg-muted` | `#A2A7C6` | `#55586F` |
| `--primary` | `#7C3AED` | `#6D28D9` |
| `--primary-300` (liên kết) | `#A78BFA` | `#5B21B6` |
| `--accent` | `#22D3EE` | `#0E7490` |
| `--grad-brand` | tím → lam neon | tím đậm → lam đậm |
| `--aurora-op` | `.34` | `.16` |

> ✅ **Không nháy màu khi tải trang** — một đoạn script ngắn trong `<head>` gán `data-theme` trước khi trình duyệt vẽ khung hình đầu tiên.
> ✅ **19/19 cặp màu đạt WCAG 2.1 AA** — thấp nhất 4,97:1, cao nhất 17,74:1.
> ✅ **Đồng bộ `<meta name="theme-color">`** để thanh trạng thái trên điện thoại đổi màu theo.

### 14 hiệu ứng đã cài đặt

| Hiệu ứng | Kích hoạt | Kỹ thuật |
|---|---|---|
| Xuất hiện khi cuộn | Lọt khung nhìn | `IntersectionObserver` + `opacity`/`translateY` |
| Xuất hiện nối tiếp | Nhóm thẻ | Biến `--i` → `transition-delay` 70ms/phần tử |
| Nâng thẻ khi rê chuột | Hover | `translateY(-6px)` + glow + viền gradient |
| Phóng ảnh trong thẻ | Hover | `scale(1.07)` trong khung `overflow:hidden` |
| Vệt sáng trên nút | Hover | `::after` chạy ngang bằng `translateX` + `skewX` |
| Thanh tiến trình cuộn | Cuộn | `requestAnimationFrame` |
| Thanh điều hướng dính | Cuộn > 12px | Lớp `.is-stuck` |
| Đếm số tăng dần | Lọt khung nhìn | Nội suy ease-out-cubic 1400ms |
| Nền cực quang | Liên tục | 3 khối `blur(90px)` + `translate3d`, 22s |
| Dải công nghệ chạy | Liên tục | Nhân đôi nội dung, dịch 50%, 26s |
| Huy hiệu giỏ hàng | Thêm vào giỏ | `pop` với gia tốc nảy |
| Thông báo trượt vào | Có thông báo | `translateX` + tự ẩn sau 4,2s |
| Mở khối hỏi đáp | Bấm câu hỏi | `grid-template-rows: 0fr → 1fr` |
| Menu điện thoại | Bấm ☰ | `translateY(-120% → 0)`, ☰ biến thành ✕ |

> ✅ **Chỉ dùng `transform` và `opacity`** → giữ 60 FPS, không gây tính lại bố cục.
> ✅ **Tôn trọng `prefers-reduced-motion`** → tắt toàn bộ hoạt ảnh khi hệ điều hành yêu cầu.
> ✅ **Không JavaScript vẫn đọc được** → lớp `.reveal` chỉ ẩn khi `<html>` có lớp `js`.

---

## 🔍 10. Tối ưu SEO

**19/19 hạng mục hoàn thành, không dùng plugin.**

| ✅ | Hạng mục | Cài đặt ở |
|---|---|---|
| ✅ | Thẻ `<title>` riêng từng trang | `seo()` + `includes/header.php` |
| ✅ | `meta description` riêng từng trang | Ưu tiên `meta_description` trong CSDL |
| ✅ | `meta keywords` | Trường `meta_keywords` |
| ✅ | Thẻ `canonical` | Sinh tự động, loại bỏ query string |
| ✅ | `meta robots` theo trang | Trang riêng tư & tìm kiếm → `noindex` |
| ✅ | Open Graph (7 thẻ) | `header.php` |
| ✅ | Twitter Card (4 thẻ) | `header.php` |
| ✅ | Schema **Organization** + **WebSite** | JSON-LD `@graph`, kèm `SearchAction` |
| ✅ | Schema **Product** + **Offer** + **AggregateRating** | `project-detail.php` |
| ✅ | Schema **BreadcrumbList** | Hàm `breadcrumb()` — vừa vẽ UI vừa sinh JSON-LD |
| ✅ | Schema **BlogPosting** | `blog-detail.php` |
| ✅ | Schema **FAQPage** | `about.php` |
| ✅ | Sitemap XML động | `sitemap.php` — quét toàn bộ CSDL |
| ✅ | `robots.txt` | Chặn admin/includes/trang riêng tư |
| ✅ | Nguồn cấp RSS | `rss.php` |
| ✅ | Đường dẫn thân thiện tiếng Việt | `slugify()` + rewrite `/du-an/<slug>` |
| ✅ | Cấu trúc heading | Mỗi trang đúng 1 `<h1>` |
| ✅ | Ảnh có `alt`, `width`, `height` | Tránh giật bố cục (CLS) |
| ✅ | `loading="lazy"` + `fetchpriority="high"` | Tối ưu LCP |

---

## 🔐 11. Bảo mật

| Lỗ hổng | Biện pháp |
|---|---|
| SQL Injection | 100% truy vấn dùng **PDO Prepared Statement**, `ATTR_EMULATE_PREPARES = false` |
| XSS | Mọi dữ liệu xuất ra đi qua `e()` → `htmlspecialchars(ENT_QUOTES)` |
| CSRF | Token 64 ký tự từ `random_bytes(32)`, kiểm tra bằng `hash_equals()` |
| Lộ mật khẩu | `password_hash()` với **BCRYPT hệ số 12** |
| Cố định phiên | `session_regenerate_id(true)` ngay sau đăng nhập |
| Dò mật khẩu | Giới hạn **5 lần sai / 10 phút** |
| Cướp cookie | Cookie phiên `HttpOnly` + `SameSite=Lax` |
| Tải tệp độc hại | `uploads/` tắt bộ thông dịch PHP qua `.htaccess` |
| Lộ thông tin lỗi | Hằng `DEBUG_MODE` |
| Vượt quyền | `require_admin()` kiểm tra **phía máy chủ** trên mọi trang admin |
| Clickjacking / MIME sniffing | Header `X-Frame-Options`, `X-Content-Type-Options` |

---

## 🎯 12. Tuỳ biến thương hiệu

### Đổi màu chủ đạo
Mở `assets/css/style.css`, sửa **4 dòng** trong khối `:root`:

```css
--primary: #7C3AED;     /* Màu thương hiệu chính */
--accent:  #22D3EE;     /* Màu nhấn */
--bg:      #0B0B18;     /* Nền trang */
--grad-brand: linear-gradient(135deg, #7C3AED 0%, #22D3EE 100%);
```

### Đổi tên, logo, thông tin liên hệ
Vào **Quản trị → Cấu hình SEO** — không cần sửa mã nguồn. Hoặc thay tệp `assets/images/logo.svg` và `favicon.svg`.

### Sinh lại toàn bộ ảnh minh hoạ
```bash
python generate_assets.py
```

### Tích hợp vào hub HIEU CEO (`DoAnWebsite`)

HieuMini đã tự động xuất hiện ở thẻ **“Kho Thư Mục projects/”** của `explore.php` (hub quét thư mục). Để nó xuất hiện thêm ở **“Bộ Sưu Tập Giao Diện”** và xem được bằng `live-view.php`, chạy **một trong hai** cách:

```
# Cách 1 — mở bằng trình duyệt (khuyến nghị)
http://localhost/DoAnWebsite/database/register_hieumini.php

# Cách 2 — phpMyAdmin → chọn CSDL hieu_ceo_db → Import → register_hieumini.sql
```

Cả hai đều **chỉ thêm bản ghi**, không sửa bất kỳ tệp nào của hub, và chạy lại nhiều lần vẫn an toàn.

> ⚠️ **Lưu ý về tên cơ sở dữ liệu:** HieuMini dùng `hieumini_market_db`, **không** dùng `hieumini_db` — vì tên đó đã thuộc về dự án HieuWeb01 trong cùng hub.

### Đổi phông chữ
Sửa 2 biến `--font-display`, `--font-body` trong `style.css` và dòng `<link>` Google Fonts trong `includes/header.php`.

---

## ✅ 13. Kiểm thử

**24/24 trường hợp kiểm thử đạt.** Chi tiết ở mục 3.1.12 của `BaoCao.docx`.

| Nhóm | Trường hợp |
|---|---|
| Truy cập | 19 trang công khai (200) · 16 trang quản trị (200) · trang riêng tư khi chưa đăng nhập (302) |
| Nghiệp vụ | Thêm giỏ AJAX · đổi giấy phép ×1,6 · mã giảm giá 20% · đặt hàng đầy đủ · đánh giá · yêu thích |
| Bảo mật | Thiếu token CSRF → 419 · đăng nhập sai 6 lần → khoá tạm |
| Dữ liệu | Thêm dự án · trùng slug → báo lỗi · lọc + sắp xếp + phân trang |
| SEO | `sitemap.php` (29 URL) · `rss.php` · đủ 6 loại schema |
| Giao diện | 390px · 768px · 1440px — không tràn ngang |
| Giao diện | Chuyển sáng/tối · ghi nhớ lựa chọn · không nháy màu · 19 cặp màu đạt AA |
| Mã nguồn | 41/41 tệp PHP vượt qua `php -l` |

---

## 🌐 14. Triển khai lên máy chủ thật

- [ ] Cập nhật `DB_HOST`, `DB_NAME`, `DB_USER`, `DB_PASS` trong `includes/config.php`
- [ ] Đặt `DEBUG_MODE = false`
- [ ] **Xoá** `test_system.php` và `generate_assets.py`
- [ ] Cài SSL (Let's Encrypt) và chuyển hướng HTTP → HTTPS
- [ ] Sửa `site_url` trong **Quản trị → Cấu hình** và dòng `Sitemap:` trong `robots.txt`
- [ ] Sửa `ErrorDocument 404` trong `.htaccess` cho khớp đường dẫn mới
- [ ] Xác minh Google Search Console và gửi `sitemap.php`
- [ ] Điền mã Google Analytics vào trường `ga_id` (nếu dùng)

---

## 🩺 15. Xử lý sự cố thường gặp

<details>
<summary><b>Lỗi "Không kết nối được cơ sở dữ liệu"</b></summary>

MySQL trong XAMPP chưa bật, hoặc chưa nhập `database.sql`, hoặc sai thông tin trong `includes/config.php`. Chạy `test_system.php` để biết chính xác nguyên nhân.
</details>

<details>
<summary><b>Chữ tiếng Việt bị lỗi font (hiển thị ???)</b></summary>

Khi nhập `database.sql` phải chọn **Character set of the file = utf-8**. Kiểm tra lại collation của CSDL phải là `utf8mb4_unicode_ci`.
</details>

<details>
<summary><b>Giao diện mất định dạng (chỉ còn chữ đen trên nền trắng)</b></summary>

Tệp CSS không tải được. Kiểm tra `BASE_URL` bằng cách xem mã nguồn trang, hoặc đặt lại thư mục đúng đường dẫn `htdocs/DoAnWebsite/projects/HieuMini`.
</details>

<details>
<summary><b>Đường dẫn thân thiện /du-an/... trả về 404</b></summary>

Chưa bật `mod_rewrite`. Mở `httpd.conf`, bỏ dấu `#` ở dòng `LoadModule rewrite_module`, và đổi `AllowOverride None` thành `AllowOverride All` cho thư mục `htdocs`. Khởi động lại Apache.
</details>

<details>
<summary><b>Bấm "Thêm giỏ" không có phản hồi</b></summary>

Mở Console của trình duyệt (F12). Nếu thấy lỗi 419 nghĩa là token CSRF hết hạn — tải lại trang. Nếu lỗi 404, kiểm tra `ajax-cart.php` có tồn tại không.
</details>

<details>
<summary><b>Phông chữ hiển thị khác thiết kế</b></summary>

Google Fonts cần Internet. Nếu máy không có mạng, hệ thống tự dùng phông dự phòng `system-ui` — giao diện vẫn hoạt động bình thường.
</details>

---

## 📜 16. Giấy phép

Sản phẩm được bán theo **3 mức giấy phép** (chi tiết tại trang Giới thiệu của website):

| | Cá nhân | Thương mại (×1,6) | Mở rộng (×2,4) |
|---|:---:|:---:|:---:|
| Học tập, làm đồ án | ✅ | ✅ | ✅ |
| Chỉnh sửa mã nguồn | ✅ | ✅ | ✅ |
| Triển khai tên miền thật | ❌ | 1 tên miền | Không giới hạn |
| Bàn giao lại cho khách hàng | ❌ | ❌ | ✅ |
| Hỗ trợ kỹ thuật | Trọn đời | Trọn đời | Trọn đời, ưu tiên |
| Bán lại nguyên bản mã nguồn | ❌ | ❌ | ❌ |

---

<div align="center">

**Đồ án môn học Lập trình Web — 2026**
Thực hiện bởi **Trần Văn Minh Hiếu**

📄 Báo cáo đầy đủ 48 trang: [`BaoCao.docx`](BaoCao.docx)

</div>
