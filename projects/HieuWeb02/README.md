# 🚀 HIEUMINI - HỆ THỐNG WEBSITE BÁN HÀNG CÔNG NGHỆ (PHP & MYSQL)

> **Đồ án môn học:** Lập trình phát triển ứng dụng Web  
> **Sinh viên thực hiện:** Trần Văn Minh Hiếu  
> **Công nghệ sử dụng:** PHP (Server-Side, PDO, Session), MySQL / MariaDB (InnoDB, UTF-8 MB4), JavaScript (AJAX, Canvas API), CSS3 (Modern Glassmorphism, Responsive).

---

## 🌟 1. Giới Thiệu Dự Án

**HieuMini** là website thương mại điện tử chuyên cung cấp các thiết bị công nghệ cao cấp (Điện thoại iPhone, Samsung Galaxy, Laptop MacBook, Gaming ROG, iPad, Smartwatch, Tai nghe Sony, Marshall, Bàn phím cơ...).

Website được phát triển với tiêu chuẩn thẩm mỹ cao cấp (**Glassmorphism & Dark Modern Tech**), hỗ trợ đầy đủ các tính năng thương mại điện tử thời gian thực và phân hệ Quản trị Dashboard toàn diện.

Dự án đi kèm tài liệu báo cáo học thuật chuyên sâu **`BaoCao.docx`** được biên soạn chi tiết theo đúng cấu trúc mục lục yêu cầu.

---

## 📁 2. Cấu Trúc Mã Nguồn

```
HieuWeb02/
├── config/
│   └── database.php         # Kết nối CSDL MySQL bằng PDO (Singleton Pattern, UTF-8 MB4)
├── includes/
│   ├── header.php           # Thanh điều hướng, tìm kiếm, giỏ hàng động, menu danh mục
│   ├── footer.php           # Chân trang, cam kết bảo hành, chính sách, mạng xã hội
│   ├── functions.php        # Bộ hàm tiện ích (format tiền tệ, slug, sanitize, flash alert)
│   └── auth_check.php       # Middleware kiểm tra phiên đăng nhập và phân quyền Admin
├── assets/
│   ├── css/
│   │   ├── style.css        # Giao diện Glassmorphism, Dark Mode, Glow effect, Animation
│   │   └── admin.css        # Giao diện Dashboard quản trị chuyên nghiệp
│   ├── js/
│   │   ├── main.js          # Xử lý AJAX giỏ hàng, đếm ngược Flash Sale, VietQR, Toast
│   │   └── admin.js         # Biểu đồ doanh thu HTML5 Canvas, xác nhận thao tác CRUD
│   └── images/              # Thư mục hình ảnh và icon sản phẩm
├── admin/
│   ├── index.php            # Bảng điều khiển KPI doanh thu, đơn hàng, biểu đồ trực quan
│   ├── products.php         # Quản lý danh sách sản phẩm (Tìm kiếm, Phân trang, Xóa)
│   ├── product_add.php      # Form thêm sản phẩm mới kèm cấu hình thông số JSON
│   ├── product_edit.php     # Form chỉnh sửa thông tin và giá bán sản phẩm
│   ├── categories.php       # Quản lý danh mục ngành hàng (Thêm / Xóa danh mục)
│   ├── orders.php           # Quản lý đơn hàng và cập nhật tiến trình giao hàng
│   ├── users.php            # Quản lý tài khoản và phân quyền Quản trị viên
│   ├── login.php            # Cổng đăng nhập Quản trị viên
│   └── sidebar.php          # Thanh menu điều hướng phân hệ Admin
├── database/
│   └── hieumini_db.sql      # Kịch bản tạo 8 bảng CSDL và dữ liệu mẫu phong phú
├── index.php                # Trang chủ: Hero Banner, Flash Sale đếm ngược, Bán chạy
├── products.php             # Danh sách sản phẩm: Lọc đa tiêu chí (Hãng, Giá, Danh mục)
├── product_detail.php       # Chi tiết sản phẩm: Cấu hình kỹ thuật, đánh giá sao, đặt mua
├── cart.php                 # Giỏ hàng: Cập nhật số lượng (+/-), mã giảm giá (HIEUMINI2026)
├── checkout.php             # Đặt hàng: Thông tin người nhận, quét mã VietQR tự động, COD
├── order_success.php        # Trang xác nhận đặt hàng thành công và mã tra cứu
├── my_orders.php            # Lịch sử và tiến trình đơn hàng của khách hàng
├── login.php                # Đăng nhập khách hàng
├── register.php             # Đăng ký tài khoản mới (Mã hóa mật khẩu BCRYPT)
├── logout.php               # Đăng xuất tài khoản
├── profile.php              # Quản lý thông tin cá nhân và đổi mật khẩu
├── mucluc.txt               # File mục lục gốc của báo cáo
├── generate_report.py       # Script Python tạo file BaoCao.docx
├── BaoCao.docx              # File Báo cáo đồ án đầy đủ, chi tiết, chuyên sâu
└── README.md                # Hướng dẫn sử dụng và triển khai dự án
```

---

## ⚙️ 3. Hướng Dẫn Cài Đặt & Chạy Môi Trường

### Bước 1: Khởi động Web Server & Cơ sở dữ liệu
1. Mở **XAMPP Control Panel** (hoặc Laragon / WampServer).
2. Nhấn nút **Start** tại **Apache** và **MySQL**.

### Bước 2: Import Cơ sở dữ liệu
1. Mở trình duyệt và truy cập: `http://localhost/phpmyadmin`
2. Nhấn nút **New** (Tạo mới CSDL), nhập tên: `hieumini_db`, chọn collation `utf8mb4_unicode_ci` và nhấn **Create**.
3. Nhấp vào CSDL `hieumini_db` vừa tạo, chọn tab **Import** (Nhập).
4. Chọn tệp `database/hieumini_db.sql` từ thư mục dự án và nhấn **Go** (Thực hiện).

### Bước 3: Truy cập Website
- **Trang chủ Khách hàng:** `http://localhost/DoAnWebsite/projects/HieuWeb02/` (hoặc `http://localhost:8000` nếu chạy `php -S localhost:8000`)
- **Trang Quản trị Admin:** `http://localhost/DoAnWebsite/projects/HieuWeb02/admin/`

---

## 🔑 4. Tài Khoản Đăng Nhập Mẫu

| Phân hệ | Email | Mật khẩu | Quyền hạn |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@hieumini.vn` | `admin123` | Toàn quyền Dashboard & CRUD |
| **Khách hàng thân thiết** | `user@gmail.com` | `user123` | Mua hàng, Giỏ hàng, Đánh giá |

---

## 📄 5. Về File Báo Cáo `BaoCao.docx`

File **`BaoCao.docx`** được tạo tự động với nội dung học thuật đầy đủ, bám sát từng mục trong `mucluc.txt`:
- **Chương 1. Tổng quan lập trình web** (PHP 8.x, PDO, Session, Security OWASP, MySQL InnoDB, ACID, Cài đặt máy chủ).
- **Chương 2. Phân tích và thiết kế website** (Actors, Bảng ma trận chức năng, 4 Bảng đặc tả Use Case, DFD, ERD, Từ điển dữ liệu Data Dictionary 8 bảng).
- **Chương 3. Chương trình thử nghiệm** (Triết lý UI/UX Glassmorphism, Mô tả chi tiết 7 màn hình Frontend & Backend, Đánh giá kết quả, Ưu/nhược điểm và Hướng phát triển AI/Mobile).

*Nếu muốn cập nhật hoặc xuất lại file báo cáo, bạn chỉ cần chạy lệnh:*
```bash
python generate_report.py
```
