# HieuMini Books — Thư viện sách trực tuyến

> Website tra cứu sách viết bằng **PHP 8 thuần** và **MySQL**, không dùng framework, không Composer.
> 30 đầu sách qua 6 thể loại, có tìm kiếm, lọc, đánh giá của bạn đọc và trang quản trị riêng.

**Tác giả:** Trần Văn Minh Hiếu · **Môn học:** Lập trình phát triển ứng dụng Web
**Dự án số:** HieuWeb07 trong bộ sưu tập HieuMini

---

## Mục lục

1. [Yêu cầu hệ thống](#1-yêu-cầu-hệ-thống)
2. [Cài đặt trong 5 phút](#2-cài-đặt-trong-5-phút)
3. [Đăng nhập quản trị](#3-đăng-nhập-quản-trị)
4. [Cấu trúc thư mục](#4-cấu-trúc-thư-mục)
5. [Cơ sở dữ liệu](#5-cơ-sở-dữ-liệu)
6. [Hệ thống thiết kế](#6-hệ-thống-thiết-kế)
7. [Hiệu ứng chuyển động](#7-hiệu-ứng-chuyển-động)
8. [Tối ưu cho công cụ tìm kiếm](#8-tối-ưu-cho-công-cụ-tìm-kiếm)
9. [Bảo mật](#9-bảo-mật)
10. [Sinh lại bìa sách và dữ liệu mẫu](#10-sinh-lại-bìa-sách-và-dữ-liệu-mẫu)
11. [Kết quả kiểm thử](#11-kết-quả-kiểm-thử)
12. [Xử lý sự cố](#12-xử-lý-sự-cố)

---

## 1. Yêu cầu hệ thống

| Thành phần | Tối thiểu | Ghi chú |
|---|---|---|
| PHP | 8.0 (khuyến nghị 8.1+) | Cần bật `pdo_mysql`, `mbstring` |
| MySQL / MariaDB | 5.7 / 10.4 | Bộ mã `utf8mb4` |
| Máy chủ web | Apache 2.4 (XAMPP) | Hoặc `php -S` khi phát triển |
| Trình duyệt | Chrome / Edge / Firefox bản mới | Không bắt buộc bật JavaScript |

Dự án **không cần cài thêm gói phụ thuộc nào**. Các thư viện hoạt ảnh đã nằm sẵn
trong `assets/vendor/`, phông chữ nằm trong `assets/fonts/` — chạy được cả khi máy không có mạng.

---

## 2. Cài đặt trong 5 phút

### Bước 1 — Đặt mã nguồn

```
C:\xampp\htdocs\HieuWebsite\projects\HieuWeb07\
```

### Bước 2 — Khởi động XAMPP

Mở **XAMPP Control Panel**, bấm **Start** ở hai dòng **Apache** và **MySQL**.

### Bước 3 — Nạp cơ sở dữ liệu

Cách nhanh nhất — dùng dòng lệnh:

```bash
cd C:\xampp\mysql\bin
mysql -u root < "C:\xampp\htdocs\HieuWebsite\projects\HieuWeb07\database\hieumini_books_db.sql"
```

Hoặc qua phpMyAdmin: mở <http://localhost/phpmyadmin> → tab **Import** →
chọn tệp `database/hieumini_books_db.sql` → **Go**.

> Tệp SQL tự tạo cơ sở dữ liệu `hieumini_books_db`, tạo 6 bảng và nạp sẵn
> 30 sách, 20 tác giả, 66 đánh giá mẫu. Chạy lại tệp này sẽ **xoá và tạo mới**
> toàn bộ — tiện khi muốn về trạng thái ban đầu.

### Bước 4 — Mở website

<http://localhost/HieuWebsite/projects/HieuWeb07/>

### Bước 5 (nếu cần) — Đổi thông tin kết nối

Sửa 4 hằng số đầu tệp `config/config.php`:

```php
const DB_HOST = '127.0.0.1';
const DB_NAME = 'hieumini_books_db';
const DB_USER = 'root';
const DB_PASS = '';        // XAMPP mặc định để trống
```

---

## 3. Đăng nhập quản trị

| | |
|---|---|
| Địa chỉ | `/admin/login.php` |
| Tên đăng nhập | `admin` |
| Mật khẩu | `hieumini2026` |

**Đổi mật khẩu ngay sau khi cài xong.** Sinh chuỗi băm mới rồi cập nhật vào bảng `admins`:

```bash
php -r "echo password_hash('mat-khau-moi-cua-ban', PASSWORD_DEFAULT), PHP_EOL;"
```

```sql
UPDATE admins SET password_hash = '<chuỗi vừa sinh>' WHERE username = 'admin';
```

Khu vực quản trị có: bảng điều khiển thống kê, quản lý sách (thêm/sửa/xoá),
duyệt đánh giá và hộp thư liên hệ.

---

## 4. Cấu trúc thư mục

```
HieuWeb07/
├── index.php              Trang chủ
├── books.php              Kho sách — tìm kiếm, lọc, phân trang
├── book.php               Chi tiết một cuốn + đánh giá
├── authors.php            Danh sách tác giả
├── author.php             Trang một tác giả
├── about.php              Giới thiệu dự án
├── contact.php            Liên hệ
├── 404.php                Trang không tìm thấy
├── sitemap.php            Sơ đồ trang cho công cụ tìm kiếm
├── robots.txt             Hướng dẫn cho robot
├── .htaccess              Rewrite, nén, cache, tiêu đề bảo mật
├── .antigravityrules      Quy tắc UI/UX cho công cụ sinh mã
│
├── config/
│   ├── config.php         Hằng số, phiên làm việc, dò đường dẫn gốc
│   └── database.php       Kết nối PDO + các hàm truy vấn ngắn
│
├── includes/
│   ├── functions.php      Hàm dùng chung (escape, slug, CSRF, phân trang…)
│   ├── header.php         Đầu trang + toàn bộ thẻ SEO
│   ├── footer.php         Chân trang + nạp thư viện JS
│   └── book-card.php      Thẻ sách dùng lại ở nhiều trang
│
├── admin/
│   ├── guard.php          Chốt chặn đăng nhập cho cả thư mục
│   ├── layout.php         Khung giao diện quản trị
│   ├── login.php          Đăng nhập (có chặn dò mật khẩu)
│   ├── index.php          Bảng điều khiển
│   ├── books.php          Danh sách sách
│   ├── book_form.php      Thêm / sửa sách
│   ├── reviews.php        Duyệt đánh giá
│   └── messages.php       Hộp thư liên hệ
│
├── api/
│   └── suggest.php        Gợi ý tìm kiếm (JSON)
│
├── assets/
│   ├── css/style.css      Toàn bộ hệ thiết kế
│   ├── js/main.js         Lớp tương tác
│   ├── fonts/             Cormorant Garamond + Crimson Pro (woff2)
│   ├── vendor/            GSAP, ScrollTrigger, SplitText, Lenis
│   └── img/covers/        30 bìa sách + ảnh chia sẻ mạng xã hội
│
├── database/
│   └── hieumini_books_db.sql
│
└── tools/                 Script sinh dữ liệu (không cần khi chạy web)
    ├── catalog.py         Nguồn dữ liệu 30 cuốn sách
    ├── generate_covers.py Sinh bìa sách
    └── generate_sql.py    Sinh tệp SQL
```

---

## 5. Cơ sở dữ liệu

Sáu bảng InnoDB, bộ mã `utf8mb4_unicode_ci`.

### `categories` — thể loại

| Cột | Kiểu | Ý nghĩa |
|---|---|---|
| `id` | INT PK | |
| `slug` | VARCHAR(80) UNIQUE | Dùng trên URL |
| `name` | VARCHAR(120) | Tên hiển thị |
| `description` | TEXT | Mô tả ngắn |
| `bg_color`, `accent` | CHAR(7) | Màu nền và màu nhấn của bìa sách thuộc thể loại |
| `sort_order` | SMALLINT | Thứ tự hiển thị |

### `authors` — tác giả

`id`, `slug` (UNIQUE), `name`, `country`, `birth_year`, `bio`

### `books` — sách

`id`, `slug` (UNIQUE), `title`, `author_id` → `authors`, `category_id` → `categories`,
`publisher`, `published_year`, `pages`, `language`, `isbn`, `cover`, `summary`,
`is_featured`, `views`, `created_at`

Có chỉ mục trên `category_id`, `author_id`, `title`, `is_featured`.

### `reviews` — đánh giá

`id`, `book_id` → `books`, `reader_name`, `rating` (CHECK 1–5), `content`,
`is_approved`, `created_at`

Đánh giá mới mặc định `is_approved = 0`, phải được duyệt trong trang quản trị mới hiển thị công khai.

### `admins` — tài khoản quản trị

`id`, `username` (UNIQUE), `password_hash` (bcrypt), `full_name`, `created_at`

### `messages` — tin nhắn liên hệ

`id`, `name`, `email`, `subject`, `content`, `is_read`, `created_at`

### Sơ đồ quan hệ

```
categories ──1:N──> books <──N:1── authors
                      │
                      └──1:N──> reviews

admins (độc lập)      messages (độc lập)
```

Khoá ngoại đều đặt `ON DELETE CASCADE`: xoá một cuốn sách thì các đánh giá của nó
tự dọn theo, không để lại dữ liệu mồ côi.

---

## 6. Hệ thống thiết kế

Phong cách **Swiss Modernism biến thể tối** — lưới 12 cột, khoảng thở rộng,
gần như không trang trí, mọi nhấn nhá dồn vào chữ và một sắc đồng thau duy nhất.

### Token màu

Khai báo tập trung ở đầu `assets/css/style.css`, không viết mã màu trực tiếp
trong component. Đổi tông cả trang chỉ cần sửa một khối.

| Token | Tối (mặc định) | Sáng |
|---|---|---|
| `--bg` | `#0E0D0C` | `#FBF9F4` |
| `--fg` | `#EFEADF` | `#16130E` |
| `--fg-muted` | `#B4AB99` | `#4A443A` |
| `--accent` | `#D8A94A` | `#8A6412` |
| `--border` | `#2B2620` | `#E0D9CA` |

Sắc nhấn ở giao diện sáng phải đậm hơn hẳn thì mới đủ tương phản 4.5:1 trên nền giấy —
đây là lý do hai giá trị `--accent` khác nhau nhiều đến vậy.

### Chữ

- **Cormorant Garamond** — tiêu đề, số liệu, nhãn
- **Crimson Pro** — thân bài

Cả hai tự host trong `assets/fonts/`, mỗi font 2 lát cắt (latin và vietnamese).
Nhờ `unicode-range`, phần tiếng Việt (~6 KB) chỉ được tải khi trên trang có dấu.

> Bộ font gốc do skill `ui-ux-pro-max` đề xuất còn có **Cinzel** cho nhãn chữ hoa.
> Dự án đã bỏ Cinzel vì font này **không có bộ ký tự tiếng Việt** — nhãn kiểu
> "THỂ LOẠI" sẽ vỡ dấu. Thay bằng Cormorant Garamond giãn chữ.

### Giao diện sáng / tối

Tối là mặc định vì đó là hướng thiết kế chủ đạo. Chỉ đổi sang sáng khi người đọc
tự bấm nút, và lựa chọn được lưu trong `localStorage`.

---

## 7. Hiệu ứng chuyển động

| Thư viện | Vai trò | Kích thước |
|---|---|---|
| **Lenis** 1.3 | Cuộn mượt toàn trang | 19 KB |
| **GSAP** 3.15 | Nền tảng hoạt ảnh | 71 KB |
| **ScrollTrigger** | Kích hoạt hoạt ảnh theo vị trí cuộn | 44 KB |
| **SplitText** | Tách tiêu đề theo dòng/từ để chạy hiệu ứng | 8 KB |

### Nguyên tắc: hiệu ứng là lớp phủ thêm, không phải điều kiện để đọc được nội dung

Ba tầng phòng thủ:

1. **CSS chỉ ẩn nội dung khi có JavaScript.** Lớp `.js` do chính JS gắn vào thẻ
   `<html>`; quy tắc `[data-reveal] { opacity: 0 }` nằm sau bộ chọn `.js`. Tắt JS
   hoặc bot ghé qua thì chữ hiện bình thường.
2. **Thiếu thư viện thì tự lui về cách viết tay.** `main.js` dò `window.gsap`,
   `window.Lenis`… trước khi dùng; không có thì dùng `IntersectionObserver`.
3. **Tôn trọng `prefers-reduced-motion`.** Bật giảm chuyển động thì Lenis không
   khởi tạo, không tách chữ, nội dung hiện ngay.

### Vì sao tách chữ theo TỪ chứ không theo ký tự

Tiếng Việt đặt dấu thanh trên nguyên âm. Chẻ tới từng ký tự rồi cho chúng chuyển
động riêng thì dấu dễ tách rời khỏi chữ trong lúc chạy, đọc rất khó chịu.
Tách theo từ giữ nguyên khối chữ có dấu.

---

## 8. Tối ưu cho công cụ tìm kiếm

| Hạng mục | Cách làm |
|---|---|
| Tiêu đề & mô tả | Mỗi trang có `<title>` và `meta description` riêng, sinh từ dữ liệu thật |
| Canonical | Thẻ `<link rel="canonical">` tuyệt đối trên mọi trang |
| Open Graph | Đủ `og:title`, `og:description`, `og:image`, `og:url` + thẻ Twitter |
| Dữ liệu có cấu trúc | JSON-LD kiểu `Book` (kèm `aggregateRating`), `Person`, `WebSite` + `SearchAction` |
| Sơ đồ trang | `/sitemap.xml` sinh động từ CSDL — thêm sách là có ngay |
| robots.txt | Chặn `/admin/`, `/api/` và các URL lọc/sắp xếp trùng nội dung |
| Ảnh | Đủ `alt` mô tả, có `width`/`height`, `loading="lazy"` ngoài màn hình đầu |
| Cấu trúc | Đúng một `<h1>` mỗi trang, phân cấp heading liền mạch, có breadcrumb |
| Nội dung | **Không ẩn mặc định** — bot đọc được toàn bộ dù không chạy JavaScript |

---

## 9. Bảo mật

| Nguy cơ | Cách chặn |
|---|---|
| SQL Injection | PDO prepared statement thật (`ATTR_EMULATE_PREPARES = false`); giá trị người dùng không bao giờ nối vào chuỗi SQL. Tham số sắp xếp đi qua danh sách trắng. |
| XSS | Mọi giá trị in ra HTML đều qua `e()` (`htmlspecialchars` với `ENT_QUOTES`) |
| CSRF | Mọi biểu mẫu ghi dữ liệu đều có mã một lần theo phiên, so sánh bằng `hash_equals()` |
| Cố định phiên | `session_regenerate_id(true)` ngay sau khi đăng nhập |
| Dò mật khẩu | Tối đa 5 lần sai trong 10 phút; thông báo lỗi không nói rõ sai tên hay sai mật khẩu |
| Trộm cookie | Cookie phiên đặt `HttpOnly` và `SameSite=Lax` |
| Mật khẩu | Băm bcrypt qua `password_hash()`, không bao giờ lưu dạng thường |
| Lộ đường dẫn | Đặt `IS_DEV = false` khi lên máy chủ thật → lỗi ghi vào log thay vì in ra màn hình |
| Truy cập tệp | `.htaccess` chặn `.sql`, `.md`, `.py`, `.log` và thư mục `tools/` |

---

## 10. Sinh lại bìa sách và dữ liệu mẫu

Toàn bộ 30 bìa sách là **thiết kế chữ do dự án tự tạo**, không phải ảnh bìa thật của
nhà xuất bản — tránh rắc rối bản quyền và giữ cho cả kho có chung ngôn ngữ thị giác.

Sửa danh sách sách trong `tools/catalog.py`, rồi chạy:

```bash
cd projects/HieuWeb07
python tools/generate_covers.py    # sinh lại toàn bộ bìa (WebP + PNG)
python tools/generate_sql.py       # sinh lại tệp SQL khớp với bìa
```

Hai script dùng chung `catalog.py` nên tên tệp bìa và cột `cover` trong CSDL
không bao giờ lệch nhau — lỗi hay gặp nhất khi dựng dữ liệu mẫu bằng tay.

Yêu cầu: Python 3 và thư viện Pillow (`pip install Pillow`).

---

## 11. Kết quả kiểm thử

Đã chạy trên PHP 8.4 + MariaDB 10.11 với trình duyệt Chromium điều khiển tự động.

| Hạng mục | Kết quả |
|---|---|
| Cú pháp PHP (26 tệp) | Không lỗi |
| Lỗi console trên 8 trang chính | Không có |
| Tràn ngang tại 375 / 768 / 1024 / 1440 px | Không có |
| Tắt JavaScript | 12/12 thẻ sách vẫn hiện, tiêu đề H1 còn nguyên |
| `prefers-reduced-motion` | Lenis không khởi tạo, nội dung hiện ngay, không lỗi |
| Tương phản màu (8 cặp × 2 giao diện) | Đạt WCAG AA, thấp nhất 4.77:1 |
| Thẻ SEO trang chi tiết | Đủ title, description, canonical, OG, JSON-LD `Book` |
| Ảnh thiếu `alt` | 0 |
| Chống SQL Injection (`' OR 1=1 --`) | Trả 0 kết quả, không lỗi |
| CSRF (gửi biểu mẫu không mã) | Bị chặn đúng |
| Đăng nhập sai / đúng | Đếm lần thử đúng, đăng nhập đúng vào được |
| Đánh giá mới | Lưu với `is_approved = 0`, chưa hiện công khai |

**Một lỗi thật đã phát hiện và sửa trong quá trình kiểm thử:** câu truy vấn tìm kiếm
ban đầu dùng lại cùng một tên tham số (`:q`) ở ba vị trí. Với prepared statement thật
của MySQL, cách này ném lỗi `SQLSTATE[HY093] Invalid parameter number` — nhưng chỉ ở
đường tìm kiếm, bấm dạo qua trang sẽ không bao giờ thấy. Đã tách thành ba tên riêng.

---

## 12. Xử lý sự cố

**Trang trắng hoặc báo "Không kết nối được cơ sở dữ liệu"**
MySQL chưa bật, hoặc chưa nạp tệp SQL. Kiểm tra XAMPP Control Panel và làm lại Bước 3.

**Chữ tiếng Việt hiện thành dấu hỏi**
Cơ sở dữ liệu chưa dùng `utf8mb4`. Nạp lại tệp SQL — trong đó đã có sẵn khai báo bộ mã đúng.

**Ảnh bìa không hiện**
Kiểm tra thư mục `assets/img/covers/` có đủ tệp `.webp` không. Thiếu thì chạy
`python tools/generate_covers.py`.

**Hiệu ứng cuộn không chạy**
Mở Developer Tools → tab Console xem có lỗi tải tệp trong `assets/vendor/` không.
Kể cả khi thiếu, trang vẫn đọc được bình thường — chỉ mất phần hoạt ảnh.

**Đăng nhập quản trị báo sai mật khẩu dù gõ đúng**
Đã thử sai quá 5 lần. Đợi 10 phút, hoặc xoá cookie phiên của trình duyệt.

**Trang quản trị bị đá về màn hình đăng nhập liên tục**
Thư mục lưu phiên của PHP không ghi được. Kiểm tra giá trị `session.save_path` trong `php.ini`.

---

© <!--năm-->2026 HieuMini Books — Trần Văn Minh Hiếu.
Bìa sách và mã nguồn thuộc về dự án; thông tin thư mục sách là dữ liệu tra cứu công khai.
