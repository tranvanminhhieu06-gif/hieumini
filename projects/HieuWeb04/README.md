# 🏠 DatCyber - Website Thương Mại Điện Tử Đồ Gia Dụng Thông Minh

[![PHP Version](https://img.shields.io/badge/PHP-8.0%2B-777BB4?style=for-the-badge&logo=php&logoColor=white)](https://www.php.net/)
[![MySQL](https://img.shields.io/badge/MySQL-8.0%2B-4479A1?style=for-the-badge&logo=mysql&logoColor=white)](https://www.mysql.com/)
[![Bootstrap 5](https://img.shields.io/badge/Bootstrap-5.3.3-7952B3?style=for-the-badge&logo=bootstrap&logoColor=white)](https://getbootstrap.com/)
[![FontAwesome](https://img.shields.io/badge/Font_Awesome-6.5.1-339AF0?style=for-the-badge&logo=font-awesome&logoColor=white)](https://fontawesome.com/)
[![XAMPP](https://img.shields.io/badge/Server-XAMPP%20%2F%20Apache-FB7A24?style=for-the-badge&logo=xampp&logoColor=white)](https://www.apachefriends.org/)

> **DatCyber** là nền tảng thương mại điện tử chuyên cung cấp các thiết bị gia dụng thông minh chính hãng (robot hút bụi, nồi chiên không dầu, máy lọc không khí, máy ép chậm, máy pha cà phê,...). Website được xây dựng hoàn chỉnh với giao diện hiện đại, chuẩn SEO, trải nghiệm giỏ hàng AJAX mượt mà và hệ thống quản trị (Admin Dashboard) toàn diện.

---

## 📑 Mục Lục
- [✨ Tính Năng Nổi Bật](#-tính-năng-nổi-bật)
- [🛠 Công Nghệ Sử Dụng](#-công-nghệ-sử-dụng)
- [📂 Cấu Trúc Thư Mục](#-cấu-trúc-thư-mục)
- [🚀 Hướng Dẫn Cài Đặt & Chạy Dự Án](#-hướng-dẫn-cài-đặt--chạy-dự-án)
- [👤 Tài Khoản Đăng Nhập Mẫu](#-tài-khoản-đăng-nhập-mẫu)
- [💳 Mã Giảm Giá Dùng Thử](#-mã-giảm-giá-dùng-thử)
- [📸 Các Phân Hệ Chính](#-các-phân-hệ-chính)
- [🔒 Bảo Mật & Tối Ưu](#-bảo-mật--tối-ưu)
- [👨‍💻 Tác Giả & Bản Quyền](#-tác-giả--bản-quyền)

---

## ✨ Tính Năng Nổi Bật

### 🌐 Dành Cho Khách Hàng (Customer Frontend)
* **Trang Chủ Hiện Đại (Homepage):**
  * Banner Hero Slider nổi bật các chương trình ưu đãi, cam kết bán hàng.
  * Danh mục sản phẩm trực quan với icon và hiệu ứng hover.
  * Flash Sale theo thời gian thực (Countdown Timer) với thanh tiến độ số lượng bán.
  * Khối sản phẩm bán chạy, sản phẩm nổi bật, tin tức tiện ích & đánh giá khách hàng.
* **Bộ Lọc & Tìm Kiếm Sản Phẩm (Catalog & Filter):**
  * Tìm kiếm theo từ khóa tức thì.
  * Lọc linh hoạt theo danh mục, khoảng giá (dưới 1 triệu, 1-3 triệu, 3-7 triệu, trên 7 triệu).
  * Sắp xếp theo: Mới nhất, Giá tăng dần, Giá giảm dần, Đánh giá cao.
* **Chi Tiết Sản Phẩm (Product Details):**
  * Hình ảnh sản phẩm sắc nét, huy hiệu giảm giá, thông tin kho hàng.
  * Bảng thông số kỹ thuật (Specifications) chi tiết, chính sách bảo hành 24 tháng.
  * Tính năng đánh giá sao (Rating & Review) thực tế từ người mua.
  * Gợi ý các sản phẩm cùng danh mục liên quan.
* **Giỏ Hàng AJAX Thông Minh (Smart Shopping Cart):**
  * Thêm vào giỏ hàng ngay lập tức không cần tải lại trang (Toast notification trực quan).
  * Tăng/giảm số lượng, xóa sản phẩm và cập nhật tổng tiền bằng AJAX.
  * Lưu trữ phiên giỏ hàng qua PHP Session an toàn.
* **Thanh Toán & Áp Dụng Mã Giảm Giá (Checkout & Coupons):**
  * Áp dụng mã khuyến mãi (Coupon/Voucher) giảm theo `%` hoặc số tiền cố định, kiểm tra điều kiện đơn hàng tối thiểu.
  * Đa dạng phương thức thanh toán: COD (Tiền mặt khi nhận hàng) và Chuyển khoản ngân hàng (Hiển thị thông tin QR/Số tài khoản).
  * Tự động tạo mã đơn hàng chuẩn format (`DC-YYYYMMDD-XX`).
* **Trang Xác Nhận Đơn Hàng (Order Confirmation):**
  * Hiển thị biên lai tóm tắt đơn hàng, danh sách sản phẩm, địa chỉ giao hàng và trạng thái đơn hàng.

---

### ⚙️ Dành Cho Quản Trị Viên (Admin Dashboard)
* **Báo Cáo & Thống Kê Tổng Quan (Dashboard Statistics):**
  * Tổng doanh thu thực tế (đã loại trừ đơn hủy).
  * Tổng số lượng đơn hàng, tổng sản phẩm đang kinh doanh, số lượng danh mục.
  * Bảng danh sách đơn hàng mới nhất và Top sản phẩm được quan tâm.
* **Quản Lý Sản Phẩm (Product Management - CRUD):**
  * Thêm mới sản phẩm: Tên, slug, danh mục, giá bán, giá cũ, tải lên/chọn hình ảnh, mô tả ngắn, mô tả chi tiết, thông số kỹ thuật, số lượng tồn kho.
  * Đánh dấu nhãn: Flash Sale (kèm % giảm), Nổi bật (Featured), Bán chạy (Best Seller).
  * Chỉnh sửa thông tin, cập nhật kho và xóa sản phẩm.
* **Quản Lý Danh Mục (Category Management - CRUD):**
  * Thêm, sửa, xóa danh mục thiết bị gia dụng, quản lý icon FontAwesome và mô tả danh mục.
* **Quản Lý Đơn Hàng (Order Management):**
  * Danh sách đơn hàng chi tiết: Mã đơn hàng, tên khách hàng, số điện thoại, ngày đặt, tổng tiền, phương thức thanh toán.
  * Cập nhật trạng thái đơn hàng: `Chờ xử lý` ➔ `Đang xử lý` ➔ `Đang giao hàng` ➔ `Đã hoàn thành` / `Đã hủy`.

---

## 🛠 Công Nghệ Sử Dụng

| Tầng (Layer) | Công Nghệ / Thư Viện | Mục Đích |
| :--- | :--- | :--- |
| **Backend** | **PHP 8.x (Native PDO)** | Xử lý logic nghiệp vụ, bảo mật Prepared Statements chống SQL Injection |
| **Database** | **MySQL / MariaDB** | Lưu trữ dữ liệu quan hệ (InnoDB, utf8mb4) |
| **Frontend** | **HTML5, CSS3, JavaScript (ES6)** | Xây dựng giao diện Responsive, xử lý tương tác DOM và AJAX |
| **UI Framework** | **Bootstrap 5.3.3** | Hệ thống Grid responsive, Modal, Alerts, Cards, Forms |
| **Icons** | **FontAwesome 6.5.1** | Icon hệ thống, biểu tượng thiết bị và trạng thái |
| **Môi Trường** | **XAMPP / Apache** | Web Server cục bộ phục vụ phát triển |

---

## 📂 Cấu Trúc Thư Mục

```plaintext
HieuWeb04/
│
├── admin/                      # Phân hệ Quản trị viên
│   ├── categories.php          # Quản lý danh mục (CRUD)
│   ├── footer.php              # Chân trang giao diện Admin
│   ├── header.php              # Sidebar & Header điều hướng Admin
│   ├── index.php               # Bảng điều khiển thống kê (Dashboard)
│   ├── orders.php              # Quản lý đơn hàng & Cập nhật trạng thái
│   └── products.php            # Quản lý sản phẩm (Thêm/Sửa/Xóa/Upload)
│
├── assets/                     # Tài nguyên tĩnh
│   ├── css/
│   │   └── style.css           # CSS tùy biến, màu sắc thương hiệu & animations
│   ├── js/
│   │   └── main.js             # Xử lý AJAX giỏ hàng, Toast, Modal, Countdown
│   └── images/                 # Hình ảnh sản phẩm, banner, logo
│
├── includes/                   # Các module dùng chung
│   ├── db.php                  # Kết nối Cơ sở dữ liệu PDO (datcyber_appliances_db)
│   ├── functions.php           # Hàm tiện ích (format tiền tệ, xử lý giỏ hàng, slug...)
│   ├── header.php              # Header người dùng, thanh tìm kiếm & điều hướng
│   └── footer.php              # Footer thông tin liên hệ, cam kết & chính sách
│
├── about.php                   # Trang Giới thiệu thương hiệu DatCyber
├── ajax-cart.php               # API nội bộ xử lý AJAX cho Giỏ hàng & Áp Coupon
├── cart.php                    # Trang hiển thị và chỉnh sửa Giỏ hàng
├── checkout.php                # Trang Đặt hàng & Nhập thông tin thanh toán
├── contact.php                 # Trang Liên hệ & Bản đồ/Form phản hồi
├── database.sql                # CSDL mẫu (Bảng, Ràng buộc khóa & Dữ liệu Seed)
├── index.php                   # Trang chủ Website DatCyber
├── order-success.php           # Trang xác nhận & Biên lai đơn hàng thành công
├── product-detail.php          # Trang Chi tiết sản phẩm & Đánh giá
├── products.php                # Trang Danh mục sản phẩm (Lọc, Tìm kiếm, Phân trang)
└── README.md                   # Tài liệu hướng dẫn dự án
```

---

## 🚀 Hướng Dẫn Cài Đặt & Chạy Dự Án

### 1. Chuẩn Bị Môi Trường
- Đã cài đặt **XAMPP** (hoặc WampServer / Laragon) hỗ trợ **PHP 7.4+** hoặc **PHP 8.x**.
- Đã bật dịch vụ **Apache** và **MySQL** trong bảng điều khiển XAMPP Control Panel.

### 2. Tải Mã Nguồn Vào Thư Mục Web
- Đặt toàn bộ thư mục dự án vào thư mục gốc của Web Server:
  ```plaintext
  C:\xampp\htdocs\HieuWeb04
  ```

### 3. Nhập Cơ Sở Dữ Liệu (Import Database)
1. Mở trình duyệt và truy cập vào **phpMyAdmin**: [http://localhost/phpmyadmin](http://localhost/phpmyadmin)
2. Tạo một Database mới có tên: `datcyber_appliances_db` với bảng mã `utf8mb4_unicode_ci`.
3. Chọn tab **Nhập (Import)** ➔ Chọn tệp `database.sql` nằm trong thư mục dự án ➔ Bấm **Thực hiện (Go)**.
4. Kiểm tra xem 8 bảng đã được tạo thành công:
   - `categories`: Danh mục sản phẩm
   - `products`: Thông tin sản phẩm
   - `users`: Tài khoản quản trị & khách hàng
   - `orders`: Thông tin đơn đặt hàng
   - `order_items`: Chi tiết các món hàng trong đơn
   - `reviews`: Đánh giá sản phẩm
   - `coupons`: Mã giảm giá / Voucher
   - `contacts`: Hộp thư liên hệ & phản hồi của khách hàng

### 4. Cấu Hình Kết Nối CSDL (Nếu cần)
Mặc định cấu hình trong [includes/db.php](file:///c:/xampp/htdocs/HieuWeb04/includes/db.php) là:
```php
$host = '127.0.0.1';
$dbname = 'datcyber_appliances_db';
$username = 'root';
$password = ''; // Mặc định XAMPP để trống
```
*Nếu bạn đổi cổng MySQL hoặc mật khẩu root, vui lòng cập nhật lại file này.*

### 5. Truy Cập Ứng Dụng
- **Trang Khách Hàng:** [http://localhost/HieuWeb04/](http://localhost/HieuWeb04/)
- **Cổng Đăng Nhập Quản Trị:** [http://localhost/HieuWeb04/admin/login.php](http://localhost/HieuWeb04/admin/login.php)
- **Bảng Điều Khiển Admin:** [http://localhost/HieuWeb04/admin/](http://localhost/HieuWeb04/admin/)

---

## 👤 Tài Khoản Đăng Nhập Mẫu

| Phân Quyền | Email | Mật Khẩu | Mục Đích |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@datcyber.vn` | `123456` | Truy cập toàn quyền Admin Dashboard |
| **Khách hàng (Customer)** | `khachhang@gmail.com` | `123456` | Tài khoản thành viên mẫu |

---

## 💳 Mã Giảm Giá Dùng Thử

Khi tiến hành đặt hàng tại trang [checkout.php](file:///c:/xampp/htdocs/HieuWeb04/checkout.php), bạn có thể nhập thử các mã khuyến mãi sau:

| Mã Coupon | Loại Giảm | Giá Trị Giảm | Điều Kiện Đơn Hàng |
| :--- | :--- | :--- | :--- |
| `DATCYBER10` | Phần trăm | Giảm **10%** | Đơn hàng từ 1.000.000đ trở lên |
| `FREESHIP` | Cố định | Giảm **50.000đ** | Đơn hàng từ 500.000đ trở lên |
| `GIADUNGVIP` | Phần trăm | Giảm **15%** | Đơn hàng từ 3.000.000đ trở lên |

---

## 📸 Các Phân Hệ Chính

### 1. Trang Chủ & Mua Sắm
- Hiển thị danh sách thiết bị gia dụng theo dạng lưới chuẩn UI/UX.
- Nút "Thêm vào giỏ" nhanh với phản hồi Toast thông minh.
- Xem nhanh thông số, nhãn Flash Sale, đánh giá số sao trung bình.

### 2. Trang Quản Trị Hệ Thống (Admin Panel)
- **Thống kê doanh thu & đơn hàng:** Biểu đồ thẻ trực quan, cảnh báo đơn hàng mới.
- **Quản lý sản phẩm:** Đầy đủ chức năng Thêm / Sửa / Xóa, hỗ trợ cấu hình giảm giá, chọn ảnh bìa đại diện.
- **Xử lý đơn hàng:** Xem chi tiết từng món đồ khách đặt, số điện thoại, địa chỉ nhận hàng và đổi trạng thái giao hàng từ `Chờ xử lý` sang `Đã hoàn thành`.

---

## 🔒 Bảo Mật & Tối Ưu

1. **Prepared Statements (PDO):** 100% các câu truy vấn cơ sở dữ liệu đều được tham số hóa (Parameterized Queries) ngăn ngừa triệt để lỗ hổng **SQL Injection**.
2. **XSS Protection:** Sử dụng `htmlspecialchars()` khi hiển thị dữ liệu do người dùng hoặc cơ sở dữ liệu cung cấp ra HTML.
3. **Session Sanitization:** Kiểm tra và chuẩn hóa dữ liệu giỏ hàng, số lượng và tổng tiền trong PHP Session phía máy chủ, ngăn chặn gian lận giá từ Client.
4. **Clean Code & Responsive UI:** Mã nguồn phân tách rõ ràng, cấu trúc dễ mở rộng, tối ưu hiển thị hoàn hảo trên Mobile, Tablet và Desktop.

---

## 👨‍💻 Tác Giả & Bản Quyền
- **Dự án:** Website Thương Mại Điện Tử Thiết Bị Gia Dụng DatCyber
- **Nhà phát triển:** Nhóm phát triển DatCyber
- **Bản quyền:** © 2026 DatCyber Smart Home Appliances. All rights reserved.
