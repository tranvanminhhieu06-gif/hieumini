# HieuMini — Cổng trưng bày website chạy trực tiếp

> Website danh mục dự án viết bằng **PHP 8 thuần** và **MySQL**, không dùng framework.
> Sáu website thương mại điện tử trong thư mục `projects/` được nhúng **live** ngay trên trang —
> người xem thao tác với giao diện thật, không phải ảnh chụp màn hình.

**Tác giả:** Trần Văn Minh Hiếu · **Môn học:** Lập trình phát triển ứng dụng Web

---

## Mục lục

1. [Yêu cầu hệ thống](#1-yêu-cầu-hệ-thống)
2. [Cài đặt trong 5 phút](#2-cài-đặt-trong-5-phút)
3. [Bật trang quản trị (quan trọng)](#3-bật-trang-quản-trị-quan-trọng)
4. [Cấu trúc thư mục](#4-cấu-trúc-thư-mục)
5. [Cơ sở dữ liệu](#5-cơ-sở-dữ-liệu)
6. [Cách website nhúng dự án "live"](#6-cách-website-nhúng-dự-án-live)
7. [Hệ thống thiết kế, chuyển cảnh và hiệu ứng Morph](#7-hệ-thống-thiết-kế-chuyển-cảnh-và-hiệu-ứng-morph)
8. [Thêm một dự án mới](#8-thêm-một-dự-án-mới)
9. [Bảo mật](#9-bảo-mật)
10. [Xử lý sự cố thường gặp](#10-xử-lý-sự-cố-thường-gặp)
11. [Triển khai lên máy chủ thật](#11-triển-khai-lên-máy-chủ-thật)
12. [Bảng tra nhanh](#12-bảng-tra-nhanh)

---

## 1. Yêu cầu hệ thống

| Thành phần | Phiên bản tối thiểu | Ghi chú |
|---|---|---|
| PHP | 8.0 (khuyến nghị 8.1+) | Cần bật `pdo_mysql`, `mbstring` |
| MySQL / MariaDB | 5.7 / 10.4 | Bộ mã `utf8mb4` |
| Máy chủ web | Apache 2.4 (XAMPP) | Hoặc `php -S` cho môi trường phát triển |
| Trình duyệt | Chrome / Edge / Firefox bản mới | Cần hỗ trợ `IntersectionObserver` |

> Mã nguồn **không dùng Composer**, không cần cài thêm gói phụ thuộc nào.

---

## 2. Cài đặt trong 5 phút

### Bước 1 — Đặt mã nguồn vào thư mục web

Toàn bộ tệp của HieuMini nằm ngay tại thư mục gốc, cạnh thư mục `projects/`:

```
C:\xampp\htdocs\HieuWebsite\
├── index.php          ← HieuMini
├── projects\          ← 6 dự án con
└── ...
```

### Bước 2 — Khởi động XAMPP

Mở **XAMPP Control Panel**, bấm **Start** ở hai dòng **Apache** và **MySQL**.

### Bước 3 — Nạp cơ sở dữ liệu

Có 2 cách nạp dữ liệu:

- **Cách 1 (Nhanh nhất):** Chạy tệp `import-local.bat` ở thư mục gốc để nạp toàn bộ 7 CSDL (cổng chính + 6 website con).
- **Cách 2 (phpMyAdmin):**
  1. Mở <http://localhost/phpmyadmin>
  2. Chọn thẻ **Import** (Nhập)
  3. Bấm **Choose File**, chọn tệp `database/tidb_all.sql` (nạp toàn bộ 7 CSDL) hoặc `database/hieumini_portfolio.sql` (chỉ nạp cổng chính)
  4. Bấm **Go** / **Import** (Thực hiện)

Script tự động khởi tạo CSDL, các bảng và toàn bộ dữ liệu mẫu. Có thể chạy lại nhiều lần an toàn nhờ cơ chế `DROP TABLE IF EXISTS`.

### Bước 4 — Mở website

<http://localhost/HieuWebsite/>

> Nếu bạn đặt mã nguồn ở thư mục khác, hệ thống **tự dò đường dẫn gốc** —
> không cần sửa cấu hình. Ví dụ đặt tại `htdocs\DoAn\` thì địa chỉ là
> `http://localhost/DoAn/`.

### Bước 5 (tùy chọn) — Đổi thông tin kết nối MySQL

Mặc định dùng cấu hình chuẩn của XAMPP. Nếu MySQL của bạn có mật khẩu,
sửa trong `config/config.php`:

```php
define('DB_USER', env_value('DB_USER', 'root'));
define('DB_PASS', env_value('DB_PASS', ''));   // ← điền mật khẩu tại đây
```

---

## 3. Bật trang quản trị (quan trọng)

### Mặc định: TẮT HOÀN TOÀN

Thư mục `admin/` **không hoạt động** khi máy chủ chưa được cấu hình biến môi trường
`ADMIN_PASSWORD`. Mọi tệp trong đó đều trả về **HTTP 404** — trông y hệt như thư mục
không hề tồn tại. Đây là lựa chọn có chủ đích: kẻ tấn công không thể biết website
này có trang quản trị hay không.

```
http://localhost/HieuWebsite/admin/         → 404 Không tìm thấy trang
http://localhost/HieuWebsite/admin/login.php → 404 Không tìm thấy trang
```

### Cách bật

Chọn **một** trong ba cách dưới đây, tùy môi trường bạn đang chạy.

#### Cách A — Máy chủ tích hợp của PHP (nhanh nhất, dùng khi phát triển)

**Windows — Command Prompt (cmd):**

```bat
cd C:\xampp\htdocs\HieuWebsite
set ADMIN_PASSWORD=MatKhauCuaBan@2026
php -S localhost:8080
```

**Windows — PowerShell:**

```powershell
cd C:\xampp\htdocs\HieuWebsite
$env:ADMIN_PASSWORD = "MatKhauCuaBan@2026"
php -S localhost:8080
```

**Linux / macOS:**

```bash
cd /var/www/HieuWebsite
ADMIN_PASSWORD='MatKhauCuaBan@2026' php -S localhost:8080
```

Sau đó mở <http://localhost:8080/admin/login.php> và nhập đúng mật khẩu vừa đặt.

> **Lưu ý:** biến môi trường đặt bằng `set` / `$env:` chỉ tồn tại trong cửa sổ dòng lệnh đó.
> Đóng cửa sổ là mật khẩu biến mất và trang quản trị tự động tắt trở lại — đúng như thiết kế.

#### Cách B — Apache của XAMPP, dùng tệp `.htaccess`

Tạo (hoặc mở) tệp `.htaccess` tại thư mục gốc `HieuWebsite\` và thêm:

```apache
SetEnv ADMIN_PASSWORD "MatKhauCuaBan@2026"
```

Điều kiện: `httpd.conf` phải bật `mod_env` và cho phép ghi đè
(`AllowOverride All` cho thư mục `htdocs`). XAMPP bật sẵn cả hai.

Khởi động lại Apache trong XAMPP Control Panel rồi mở
<http://localhost/HieuWebsite/admin/login.php>.

#### Cách C — Cấu hình trực tiếp trong `httpd.conf` (máy chủ thật)

```apache
<Directory "/var/www/HieuWebsite">
    SetEnv ADMIN_PASSWORD "MatKhauRatManh@2026"
</Directory>
```

### Cách tắt lại

Xóa dòng `SetEnv` (Cách B/C) rồi khởi động lại Apache, hoặc đóng cửa sổ dòng lệnh (Cách A).
Trang quản trị lập tức trở về trạng thái 404.

### Bên trong phân hệ quản trị có gì

| Trang | Chức năng |
|---|---|
| `admin/index.php` | Bảng điều khiển: 4 chỉ số KPI, biểu đồ cột 14 ngày, xếp hạng dự án, tin nhắn mới |
| `admin/projects.php` | Danh sách dự án, tìm kiếm, cảnh báo thư mục thiếu, xóa |
| `admin/project_form.php` | Thêm / sửa dự án (đầy đủ trường, tự sinh slug tiếng Việt) |
| `admin/messages.php` | Hộp thư liên hệ, lọc đã đọc / chưa đọc, đánh dấu, xóa |
| `admin/logout.php` | Đăng xuất, hủy phiên |

**Cơ chế bảo vệ bổ sung:**

- Không có tài khoản nào lưu trong CSDL — mật khẩu chỉ đến từ biến môi trường.
- So sánh mật khẩu bằng `hash_equals()` để chống tấn công đo thời gian.
- Sai 5 lần liên tiếp → khóa 10 phút.
- Tự đăng xuất sau 60 phút không thao tác.
- `session_regenerate_id(true)` sau khi đăng nhập thành công (chống Session Fixation).
- Mọi thao tác ghi đều yêu cầu token CSRF.
- Thẻ `<meta name="robots" content="noindex, nofollow">` trên mọi trang admin.

---

## 4. Cấu trúc thư mục

```
HieuWebsite/
│
├── index.php                  # Trang chủ: banner morph + lưới 6 dự án live
├── project.php                # Chi tiết dự án: khung nhúng tương tác đầy đủ
├── about.php                  # Giới thiệu kiến trúc + bảng đối chiếu 6 dự án
├── contact.php                # Biểu mẫu liên hệ, lưu vào bảng messages
├── README.md                  # Tệp bạn đang đọc
├── BaoCao.docx                # Báo cáo đồ án theo đúng mucluc.txt
├── mucluc.txt                 # Mục lục yêu cầu
│
├── config/
│   ├── config.php             # Hằng số, đọc biến môi trường, khởi tạo session
│   └── database.php           # Lớp Database (PDO Singleton) + trang báo lỗi thân thiện
│
├── includes/
│   ├── bootstrap.php          # Điểm khởi động chung (nạp config + database + functions)
│   ├── functions.php          # e(), url(), icon(), csrf_*(), flash(), slugify()...
│   ├── header.php             # Thanh điều hướng, chuyển sáng/tối, thanh tiến độ cuộn
│   └── footer.php             # Chân trang, nút lên đầu trang, nạp main.js
│
├── assets/
│   ├── css/
│   │   ├── style.css          # Hệ thống thiết kế: token, thành phần, hoạt ảnh
│   │   └── admin.css          # Giao diện phân hệ quản trị
│   └── js/
│       └── main.js            # Morph banner, parallax, reveal, lazy iframe, chuyển trang
│
├── admin/                     # ⚠ Chỉ hoạt động khi có biến ADMIN_PASSWORD
│   ├── guard.php              # Cổng bảo vệ: không có mật khẩu → trả 404
│   ├── layout.php             # Khung giao diện quản trị dùng chung
│   ├── login.php              # Đăng nhập (giới hạn số lần thử)
│   ├── logout.php
│   ├── index.php              # Bảng điều khiển
│   ├── projects.php           # Danh sách + xóa dự án
│   ├── project_form.php       # Thêm / sửa dự án
│   └── messages.php           # Hộp thư liên hệ
│
├── database/
│   └── hieumini_portfolio.sql # Script tạo CSDL + dữ liệu mẫu 6 dự án
│
└── projects/                  # 6 website con được nhúng live
    ├── HieuWeb01/  …  HieuWeb06/
```

---

## 5. Cơ sở dữ liệu

CSDL `hieumini_portfolio` gồm **4 bảng**, bộ mã `utf8mb4_unicode_ci`, engine InnoDB.

### `projects` — hồ sơ từng dự án

| Cột | Kiểu | Ý nghĩa |
|---|---|---|
| `id` | INT PK AI | Khóa chính |
| `code` | VARCHAR(20) UNIQUE | Mã dự án, ví dụ `HieuWeb01` |
| `slug` | VARCHAR(120) UNIQUE | Định danh trên URL |
| `name`, `tagline` | VARCHAR | Tên và mô tả một dòng |
| `summary`, `description` | TEXT | Nội dung trang chi tiết |
| `category` | VARCHAR(80) | Lĩnh vực, dùng cho bộ lọc |
| `tech_stack` | VARCHAR(255) | Danh sách công nghệ, phân tách bởi dấu phẩy |
| `folder` | VARCHAR(120) | **Thư mục con trong `projects/` — dùng để nhúng live** |
| `entry_file` | VARCHAR(80) | Tệp khởi đầu, mặc định `index.php` |
| `admin_path` | VARCHAR(120) | Đường dẫn trang quản trị của dự án con |
| `db_name` | VARCHAR(80) | Tên CSDL mà dự án con dùng |
| `accent_from`, `accent_to` | VARCHAR(9) | Hai màu gradient nhận diện dự án |
| `table_count`, `page_count` | SMALLINT | Quy mô dự án |
| `status` | ENUM | `published` / `draft` / `archived` |
| `sort_order`, `views` | INT | Thứ tự hiển thị, lượt xem |

### `project_features` — điểm nổi bật (1 dự án ↔ N mục)

Khóa ngoại `project_id → projects.id`, `ON DELETE CASCADE`.

### `messages` — tin nhắn liên hệ

Lưu họ tên, email, tiêu đề, nội dung, IP, trạng thái đã đọc.

### `visit_logs` — nhật ký lượt xem

Khóa ngoại `project_id → projects.id`, `ON DELETE SET NULL`.
Cung cấp dữ liệu cho biểu đồ cột 14 ngày trên Dashboard.

---

## 6. Cách website nhúng dự án "live"

Đây là điểm kỹ thuật trung tâm của HieuMini.

### Trên trang chủ — chế độ xem trước

Mỗi thẻ dự án chứa một `<iframe>` trỏ thẳng tới `projects/<folder>/<entry_file>`:

```css
.live-frame iframe {
  width: 400%; height: 400%; max-width: none;
  transform: scale(.25); transform-origin: 0 0;
  pointer-events: none;
}
```

Iframe được vẽ với chiều rộng gấp **4 lần** khung chứa rồi thu nhỏ đúng **1/4**.
Nhờ vậy dự án con "nghĩ" rằng nó đang chạy trên màn hình rộng ~1300px nên hiển thị
bố cục desktop đầy đủ, trong khi thẻ tóm tắt vẫn vừa khít ở mọi kích thước màn hình.
`pointer-events: none` khiến chuột đi xuyên qua khung xem trước để bấm vào thẻ dự án.

Iframe **không nạp ngay khi mở trang**. Hàm `initLiveFrames()` trong `main.js` dùng
`IntersectionObserver` với `rootMargin: 400px` để chỉ nạp khi thẻ sắp lọt vào khung nhìn.
Nếu dự án con không phản hồi trong 8 giây, lớp chờ vẫn được gỡ để không kẹt vòng quay.

### Trên trang chi tiết — chế độ tương tác đầy đủ

Khung nhúng bỏ `pointer-events: none`, tỉ lệ 1:1 và có thanh công cụ giả lập trình duyệt
với ba nút đổi khung nhìn:

| Nút | Chiều rộng iframe | Dùng để |
|---|---|---|
| Máy tính | 100% | Kiểm tra bố cục desktop |
| Tablet | 820px | Kiểm tra điểm ngắt trung bình |
| Điện thoại | 400px | Kiểm tra giao diện di động |

Nếu thư mục dự án chưa tồn tại trên đĩa, hàm `project_exists()` phát hiện và website
hiển thị trạng thái rỗng có hướng dẫn thay vì một iframe lỗi.

---

## 7. Hệ thống thiết kế, chuyển cảnh và hiệu ứng Morph

### Hệ thống token màu

Bảng màu **Light Premium**: nền `#F7F8FC`, chữ `#0B1020`, điểm nhấn chàm `#4F46E5` → tím `#7C3AED` → lam `#06B6D4`.
Toàn bộ được khai báo bằng biến CSS trong `:root`, chế độ tối chỉ định nghĩa lại giá trị nên
không cần viết lại bất kỳ quy tắc nào. Lựa chọn sáng/tối được ghi nhớ qua `localStorage`
và áp dụng ngay trước khi trang được vẽ để tránh nhấp nháy nền.

Chữ: **Space Grotesk** cho tiêu đề, **Inter** cho nội dung, đều có dự phòng
`Segoe UI` / `system-ui` nên vẫn đẹp khi máy không có Internet.

### Hiệu ứng Morph cho banner

Banner chứa ba khối SVG `<path>` mang thuộc tính `data-morph`. Hàm `initMorph()` trong
`main.js` hoạt động theo bốn bước:

1. **Sinh hình** — `makeBlob()` tạo 8 điểm neo trên một đường tròn, bán kính mỗi điểm
   dao động ngẫu nhiên theo tham số `data-wobble`, cho ra một khối bất định tự nhiên.
2. **Vẽ đường cong** — `toPath()` quy đổi 8 điểm rời rạc thành đường cong Bézier bậc ba
   khép kín theo công thức Catmull-Rom, nên đường viền luôn liền mạch, không gãy góc.
3. **Nội suy** — mỗi khung hình, tọa độ từng điểm được nội suy tuyến tính giữa hình
   hiện tại và hình đích, qua hàm easing `easeInOut` (cubic) để tốc độ biến hình
   nhanh ở giữa, chậm ở hai đầu.
4. **Nối chu kỳ** — khi hoàn tất một chu kỳ 5,2 giây, hình đích trở thành hình xuất phát
   và một hình mới được sinh ra. Vòng lặp chạy vô hạn mà không bao giờ lặp lại y hệt.

Ba khối được đặt lệch pha ngẫu nhiên, phủ bộ lọc `feGaussianBlur` và mặt nạ
`radial-gradient` nên tan dần ở rìa, tạo cảm giác ánh sáng khuếch tán thay vì hình khối cứng.

> Cách làm này **không cần thư viện ngoài** (không GSAP MorphSVG, không anime.js).
> Toàn bộ chưa tới 60 dòng JavaScript và chạy được ngoại tuyến.

### Chuyển cảnh và hoạt ảnh

| Hiệu ứng | Kỹ thuật | Vị trí |
|---|---|---|
| Chuyển trang | View Transitions API, dự phòng làm mờ 200ms | Toàn site |
| Tiêu đề hiện theo từng từ | `@keyframes wordIn` + độ trễ tăng dần 62ms | Banner |
| Parallax theo chuột | `pointermove` + nội suy 8%/khung, `translate3d` | Banner |
| Hiện dần khi cuộn | `IntersectionObserver`, xếp tầng 70ms | Toàn site |
| Đếm số tăng dần | `requestAnimationFrame` + easing cubic-out | Chỉ số banner, KPI admin |
| Thanh tiến độ cuộn | `transform: scaleX()` — không gây reflow | Đầu trang |
| Gợn sóng khi bấm | Phần tử `.ripple` sinh tại vị trí con trỏ | Mọi nút |
| Thẻ nâng lên khi rê chuột | `translateY(-6px)` + đổ bóng | Thẻ dự án |
| Khối kính trôi nổi | `@keyframes floatCard` 7s | Banner |
| Biểu đồ cột mọc lên | `@keyframes barGrow` `scaleY` | Dashboard |

**Nguyên tắc hiệu năng:** mọi hoạt ảnh chỉ chạy trên `transform` và `opacity` —
hai thuộc tính được GPU xử lý, không gây tính lại bố cục (layout thrashing).

**Khả năng tiếp cận:** toàn bộ hiệu ứng tự tắt khi hệ điều hành bật
"giảm chuyển động" (`prefers-reduced-motion: reduce`). Vòng lặp morph dừng hẳn,
mọi phần tử hiển thị ngay ở trạng thái cuối.

---

## 8. Thêm một dự án mới

### Cách 1 — Qua trang quản trị (khuyến nghị)

1. Chép thư mục dự án vào `projects/HieuWeb07/`
2. Bật trang quản trị (mục 3), đăng nhập
3. Vào **Quản lý dự án → Thêm dự án**
4. Điền tối thiểu ba trường bắt buộc: **Tên dự án**, **Mã dự án**, **Thư mục trong projects/**
5. Bấm **Thêm dự án** — bỏ trống Slug thì hệ thống tự sinh từ tên tiếng Việt
   (ví dụ "Dự án Kiểm Thử" → `du-an-kiem-thu`)

### Cách 2 — Chèn thẳng bằng SQL

```sql
INSERT INTO projects (code, slug, name, tagline, category, tech_stack, folder,
                      entry_file, admin_path, accent_from, accent_to, sort_order)
VALUES ('HieuWeb07', 'du-an-moi', 'Tên dự án mới', 'Mô tả một dòng',
        'Lĩnh vực', 'PHP 8,MySQL,AJAX', 'HieuWeb07',
        'index.php', 'admin/', '#0EA5E9', '#6366F1', 7);
```

---

## 9. Bảo mật

| Nguy cơ | Biện pháp |
|---|---|
| SQL Injection | 100% truy vấn dùng PDO prepared statement, `ATTR_EMULATE_PREPARES = false` |
| XSS | Mọi dữ liệu in ra đều qua `e()` = `htmlspecialchars(ENT_QUOTES\|ENT_SUBSTITUTE)` |
| CSRF | Token 32 byte ngẫu nhiên trong mọi biểu mẫu POST, đối chiếu bằng `hash_equals()` |
| Truy cập trái phép trang admin | Mặc định 404; chỉ mở khi có `ADMIN_PASSWORD` ở tầng máy chủ |
| Dò mật khẩu | Khóa 10 phút sau 5 lần sai |
| Session Fixation | `session_regenerate_id(true)` khi đăng nhập và đăng xuất |
| Đánh cắp cookie | Cookie phiên đặt `httponly` và `samesite=Lax` |
| Lộ thông tin khi lỗi | `display_errors` tắt mặc định; chỉ bật khi đặt `APP_DEBUG=1` |
| Chỉ mục tìm kiếm quét trang admin | `X-Robots-Tag` và thẻ meta `noindex, nofollow` |

---

## 10. Xử lý sự cố thường gặp

<details>
<summary><b>Hiện trang "Chưa kết nối được cơ sở dữ liệu"</b></summary>

MySQL chưa chạy hoặc chưa nạp script. Bật MySQL trong XAMPP, sau đó import
`database/hieumini_portfolio.sql` qua phpMyAdmin. Muốn xem thông báo lỗi chi tiết,
chạy máy chủ với `APP_DEBUG=1`.
</details>

<details>
<summary><b>Vào <code>/admin/</code> chỉ thấy trang 404</b></summary>

Đây là **hành vi đúng theo thiết kế**. Máy chủ chưa có biến `ADMIN_PASSWORD`.
Xem lại mục 3 để bật.
</details>

<details>
<summary><b>Đã đặt <code>SetEnv</code> trong .htaccess nhưng admin vẫn 404</b></summary>

Kiểm tra ba điểm: (1) `mod_env` đã bật trong `httpd.conf`;
(2) `AllowOverride All` cho thư mục `htdocs`; (3) đã khởi động lại Apache sau khi sửa.
Nếu vẫn không được, dùng Cách A (`php -S`) để kiểm chứng nhanh.
</details>

<details>
<summary><b>Khung xem trực tiếp trống trơn hoặc chỉ hiện màu gradient</b></summary>

Ba nguyên nhân thường gặp:
1. Thư mục dự án con không đúng tên — đối chiếu cột `folder` trong bảng `projects`
   với tên thư mục thật trong `projects/`.
2. Dự án con chưa được nạp CSDL riêng của nó nên đang trả về trang lỗi.
   Mỗi dự án con dùng một CSDL riêng, xem cột `db_name`.
3. Dự án con gửi tiêu đề `X-Frame-Options: DENY` (thường do `.htaccess` của nó).
   Gỡ dòng đó, hoặc dùng nút **Mở toàn màn hình**.
</details>

<details>
<summary><b>Chữ hiển thị sai font</b></summary>

Máy đang ngoại tuyến nên không tải được Google Fonts. Giao diện tự động dùng
`Segoe UI` / `system-ui` và vẫn hiển thị đúng bố cục.
</details>

<details>
<summary><b>Tiếng Việt hiển thị thành dấu hỏi</b></summary>

CSDL chưa dùng `utf8mb4`. Nạp lại `database/hieumini_portfolio.sql` — script đã khai báo
`DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci` ở cả cấp CSDL lẫn từng bảng.
</details>

<details>
<summary><b>Trang chủ tải chậm</b></summary>

Sáu iframe được nạp trễ nên chỉ những thẻ trong tầm nhìn mới tải. Nếu vẫn chậm,
nguyên nhân thường là một dự án con đang truy vấn CSDL nặng — mở riêng dự án đó
bằng nút "Tab mới" để kiểm chứng.
</details>

---

## 11. Triển khai lên máy chủ thật

1. **Tải mã nguồn** lên thư mục web (`public_html`, `/var/www/html`…), giữ nguyên
   cấu trúc `projects/` bên cạnh.
2. **Tạo CSDL** trên hosting và import `database/hieumini_portfolio.sql`.
3. **Cấu hình kết nối** qua biến môi trường thay vì sửa mã:

   ```apache
   SetEnv DB_HOST "localhost"
   SetEnv DB_NAME "ten_csdl"
   SetEnv DB_USER "ten_dang_nhap"
   SetEnv DB_PASS "mat_khau"
   ```

4. **Chỉ đặt `ADMIN_PASSWORD` khi thực sự cần** vào quản trị, và dùng mật khẩu mạnh
   (tối thiểu 16 ký tự, có chữ hoa, số và ký tự đặc biệt). Gỡ biến này khi không dùng.
5. **Bật HTTPS**, sau đó bổ sung `'secure' => true` vào `session_set_cookie_params()`
   trong `config/config.php`.
6. **Không bật `APP_DEBUG`** trên môi trường chạy thật.

---

## 12. Bảng tra nhanh

| Việc cần làm | Lệnh / Đường dẫn |
|---|---|
| Mở website | <http://localhost/HieuWebsite/> |
| Xem một dự án | `project.php?slug=luxury-fitness` |
| Nạp CSDL | Chạy `import-local.bat` hoặc phpMyAdmin → Import → `database/tidb_all.sql` |
| Bật admin (cmd) | `set ADMIN_PASSWORD=xxx` rồi `php -S localhost:8080` |
| Bật admin (PowerShell) | `$env:ADMIN_PASSWORD="xxx"` rồi `php -S localhost:8080` |
| Bật admin (Apache) | Thêm `SetEnv ADMIN_PASSWORD "xxx"` vào `.htaccess` |
| Tắt admin | Xóa biến môi trường, khởi động lại máy chủ |
| Bật gỡ lỗi | Thêm biến `APP_DEBUG=1` |
| Đọc báo cáo đồ án | `BaoCao.docx` |

---

## Giấy phép

Dự án phục vụ mục đích học tập. © 2026 Trần Văn Minh Hiếu.
