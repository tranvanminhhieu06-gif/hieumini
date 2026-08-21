# 📚 HieuMini - Website Bán Đồ Dùng Học Tập & Sáng Tạo

> **Dự án Website Thương mại Điện tử Bán Đồ Dùng Học Tập & Văn Phòng Phẩm trực tuyến**  
> Xây dựng bằng **PHP (PDO)**, **MySQL**, **Bootstrap 5**, và **JavaScript (AJAX)**.

---

## 🌟 Giới Thiệu Dự Án

**HieuMini** là nền tảng thương mại điện tử chuyên cung cấp các sản phẩm đồ dùng học tập, dụng cụ vẽ & mỹ thuật, sổ tay, bút viết và phụ kiện bàn học. Hệ thống được thiết kế theo phong cách giao diện hiện đại, thân thiện với học sinh, sinh viên và nhân viên văn phòng, tối ưu trải nghiệm mua sắm nhanh chóng và tiện lợi.

---

## 🚀 Công Nghệ Sử Dụng

- **Backend**: PHP 7.4+ / PHP 8.x (Thuần - Native PHP, mô hình hướng thủ tục & module hóa, kết nối CSDL qua **PDO** an toàn chống SQL Injection).
- **Database**: MySQL / MariaDB (Bộ mã `utf8mb4_unicode_ci` hỗ trợ đầy đủ tiếng Việt).
- **Frontend**: HTML5, CSS3, JavaScript (ES6+), **Bootstrap 5**, **Bootstrap Icons**.
- **Tương tác**: AJAX (Thêm/cập nhật giỏ hàng không tải lại trang, tìm kiếm thời gian thực, áp dụng mã giảm giá).
- **Bảo mật**: Mã hóa mật khẩu với thuật toán `password_hash()` (Bcrypt), xử lý dữ liệu đầu vào chống XSS (`clean_input`).

---

## ✨ Các Tính Năng Nổi Bật

### 🛒 1. Dành Cho Khách Hàng (Client)
- **Trang chủ (`index.php`)**:
  - Banner khuyến mãi tựu trường bắt mắt.
  - Danh mục ngành hàng nổi bật với icon sinh động.
  - Khối sản phẩm bán chạy (Hot), sản phẩm mới về (New), sản phẩm giảm giá đặc biệt.
  - Đánh giá từ khách hàng & chính sách bảo hành, đổi trả.
- **Trang Sản phẩm (`products.php`)**:
  - Bộ lọc sản phẩm đa tiêu chí: Lọc theo danh mục, khoảng giá.
  - Sắp xếp sản phẩm: Giá tăng dần/giảm dần, mới nhất, tên A-Z.
  - Phân trang tự động theo số lượng sản phẩm.
- **Chi tiết sản phẩm (`product-detail.php`)**:
  - Trình xem ảnh sản phẩm & gallery.
  - Thông số kỹ thuật chi tiết, chính sách cam kết chất lượng.
  - Đánh giá & bình luận sản phẩm với hệ thống sao (Rating 1-5 sao).
  - Gợi ý danh sách sản phẩm cùng danh mục liên quan.
- **Giỏ hàng & Khuyến mãi (`cart.php`)**:
  - Thêm / Cập nhật số lượng / Xóa sản phẩm qua AJAX.
  - Hệ thống áp dụng mã giảm giá (**Coupon Code**) tự động tính lại tổng tiền.
- **Đặt hàng & Thanh toán (`checkout.php` & `order-success.php`)**:
  - Hỗ trợ mua hàng có tài khoản hoặc khách vãng lai.
  - Nhiều phương thức thanh toán: COD (Thanh toán khi nhận hàng), Chuyển khoản ngân hàng (Hiển thị QR Code), Ví MoMo.
  - Tạo mã đơn hàng duy nhất và xuất hóa đơn xác nhận đơn hàng thành công.
- **Tài khoản người dùng (`login.php`, `register.php`, `profile.php`)**:
  - Đăng ký tài khoản mới & Đăng nhập phân quyền (Khách hàng / Admin).
  - Xem & cập nhật thông tin cá nhân.
  - Xem lịch sử đơn hàng đã đặt cùng trạng thái chi tiết.
- **Tìm kiếm thông minh**: Thanh tìm kiếm Live Search gợi ý sản phẩm ngay khi nhập từ khóa.
- **Liên hệ & Giới thiệu (`contact.php`, `about.php`)**: Gửi phản hồi/thắc mắc về ban quản trị, bản đồ cửa hàng.

---

### 🛡️ 2. Dành Cho Quản Trị Viên (Admin Dashboard)
- **Bảng điều khiển tổng quan (`admin/index.php`)**:
  - Thống kê doanh thu thực tế, tổng đơn hàng, số lượng khách hàng, số lượng sản phẩm.
  - Bảng danh sách đơn hàng mới nhất cần xử lý.
- **Quản lý Sản phẩm (`admin/products.php`, `admin/product-add.php`, `admin/product-edit.php`)**:
  - Xem danh sách sản phẩm, tìm kiếm và phân loại.
  - Thêm mới / Cập nhật / Xóa sản phẩm.
  - Tải lên hình ảnh sản phẩm, gắn nhãn Nổi bật / Hot / Mới.
  - Quản lý số lượng tồn kho, giá bán và giá khuyến mãi.
- **Quản lý Danh mục (`admin/categories.php`)**:
  - Thêm mới, chỉnh sửa và xóa danh mục hàng hóa.
- **Quản lý Đơn hàng (`admin/orders.php`, `admin/order-detail.php`)**:
  - Xem danh sách toàn bộ đơn hàng của cửa hàng.
  - Xem chi tiết từng đơn hàng (khách hàng, sản phẩm, số lượng, phương thức thanh toán, ghi chú).
  - Cập nhật trạng thái đơn hàng: `Chờ xử lý (Pending)` ➔ `Đang xử lý (Processing)` ➔ `Đang giao hàng (Shipping)` ➔ `Đã hoàn thành (Completed)` ➔ `Đã hủy (Cancelled)`.
- **Quản lý Người dùng (`admin/users.php`)**:
  - Xem danh sách tài khoản khách hàng và quản trị viên.
- **Quản lý Liên hệ (`admin/contacts.php`)**:
  - Xem danh sách thắc mắc/tin nhắn gửi từ form liên hệ của khách hàng.

---

## 📁 Cấu Trúc Thư Mục Dự Án

```text
HieuWeb03/
├── admin/                     # Phân hệ Quản trị (Admin Panel)
│   ├── includes/              # Header, sidebar, footer của trang quản trị
│   ├── categories.php         # Quản lý danh mục
│   ├── contacts.php           # Quản lý thư liên hệ
│   ├── index.php              # Bảng điều khiển (Dashboard)
│   ├── order-detail.php       # Xem chi tiết đơn hàng
│   ├── orders.php             # Danh sách đơn hàng
│   ├── product-add.php        # Thêm sản phẩm mới
│   ├── product-edit.php       # Chỉnh sửa sản phẩm
│   ├── product-delete.php     # Xóa sản phẩm
│   ├── products.php           # Danh sách sản phẩm
│   └── users.php              # Quản lý tài khoản
├── api/                       # Các API xử lý AJAX
│   ├── cart.php               # API xử lý thêm/sửa/xóa giỏ hàng
│   ├── coupon.php             # API kiểm tra và áp dụng mã giảm giá
│   └── search.php             # API tìm kiếm nhanh gợi ý sản phẩm
├── assets/                    # Tài nguyên tĩnh
│   ├── css/                   # Stylesheet tùy chỉnh
│   ├── images/                # Hình ảnh sản phẩm, banner, logo
│   └── js/                    # Scripts tương tác và xử lý AJAX
├── config/                    # Cấu hình hệ thống
│   ├── app.php                # Hằng số toàn cục & hàm tiện ích (Helpers)
│   └── db.php                 # Kết nối MySQL PDO & cơ chế Auto-Setup
├── includes/                  # Thành phần giao diện dùng chung
│   ├── header.php             # Thẻ meta, link CSS, typography
│   ├── navbar.php             # Thanh menu điều hướng & giỏ hàng
│   └── footer.php             # Chân trang & liên kết hỗ trợ
├── about.php                  # Trang giới thiệu cửa hàng
├── cart.php                   # Trang giỏ hàng
├── checkout.php               # Trang đặt hàng & thanh toán
├── contact.php                # Trang liên hệ & gửi góp ý
├── database.sql               # Script CSDL (Bảng & Dữ liệu mẫu)
├── index.php                  # Trang chủ
├── login.php                  # Trang đăng nhập
├── logout.php                 # Xử lý đăng xuất
├── order-success.php          # Trang thông báo đặt hàng thành công
├── product-detail.php         # Trang chi tiết sản phẩm
├── products.php               # Trang danh sách tất cả sản phẩm
├── profile.php                # Trang cá nhân & lịch sử đơn hàng
├── register.php               # Trang đăng ký tài khoản
└── README.md                  # Tài liệu hướng dẫn dự án
```

---

## ⚙️ Hướng Dẫn Cài Đặt & Chạy Website

### Cách 1: Chạy bằng XAMPP (Khuyến nghị)

1. **Sao chép thư mục dự án**:
   - Đặt thư mục `HieuWeb03` vào thư mục web root của XAMPP:
     ```text
     C:\xampp\htdocs\HieuWeb03
     ```

2. **Khởi động dịch vụ trong XAMPP Control Panel**:
   - Bật module **Apache**.
   - Bật module **MySQL**.

3. **Cơ sở dữ liệu (Database)**:
   - Hệ thống đã tích hợp cơ chế **tự động khởi tạo database** trong `config/db.php`. Khi bạn truy cập website lần đầu, hệ thống sẽ tự động tạo CSDL `hieumini_db` và nhập dữ liệu từ `database.sql`.
   - *(Tùy chọn)* Nếu muốn nhập thủ công:
     1. Mở trình duyệt và truy cập: `http://localhost/phpmyadmin`
     2. Tạo CSDL mới tên `hieumini_db` với collation `utf8mb4_unicode_ci`.
     3. Nhấn tab **Import** và chọn tệp `database.sql` trong thư mục dự án để nạp dữ liệu.

4. **Truy cập website**:
   - Mở trình duyệt và truy cập: [http://localhost/HieuWeb03](http://localhost/HieuWeb03)
   - Trang quản trị Admin: [http://localhost/HieuWeb03/admin](http://localhost/HieuWeb03/admin)

---

### Cách 2: Chạy bằng PHP Built-in Server

Nếu máy tính đã cài đặt PHP và MySQL:

1. Mở Terminal / PowerShell tại thư mục dự án:
   ```bash
   cd c:\xampp\htdocs\HieuWeb03
   ```
2. Khởi chạy máy chủ PHP cục bộ:
   ```bash
   php -S localhost:8000
   ```
3. Mở trình duyệt và truy cập: [http://localhost:8000](http://localhost:8000)

---

## 🔑 Tài Khoản Mặc Định

Hệ thống đã có sẵn 2 tài khoản mẫu để kiểm thử:

| Loại Tài Khoản | Email | Mật khẩu | Quyền hạn |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@hieumini.vn` | `admin123` | Toàn quyền truy cập Dashboard `/admin` |
| **Khách hàng (Customer)** | `user@hieumini.vn` | `user123` | Đặt hàng, xem lịch sử đơn hàng cá nhân |

---

## 🎟️ Mã Giảm Giá Mẫu (Coupons)

| Mã Giảm Giá | Mức Giảm | Điều Kiện Đơn Hàng |
| :--- | :--- | :--- |
| **`HIEUMINI10`** | Giảm **10%** tổng đơn hàng | Áp dụng cho đơn từ 100.000 đ (Tối đa 50.000 đ) |
| **`FREESHIP`** | Giảm **30.000 đ** phí vận chuyển | Áp dụng cho đơn từ 250.000 đ |
| **`BACK2SCHOOL`** | Giảm **15%** tổng đơn hàng | Áp dụng cho đơn từ 200.000 đ (Tối đa 100.000 đ) |

---

## 📞 Hỗ Trợ & Bản Quyền

- **Đơn vị phát triển**: HieuMini Stationery Team
- **Liên hệ**: `admin@hieumini.vn` / Hotline: `0901 234 567`
- **Địa chỉ**: Tòa nhà HieuMini Tower, 123 Đường Cầu Giấy, Hà Nội
