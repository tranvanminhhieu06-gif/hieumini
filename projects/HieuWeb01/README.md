# HieuMini Fashion Studio - Website Bán Quần Áo Thời Trang Cao Cấp

Hệ thống website thương mại điện tử chuyên ngành thời trang **HieuMini** được xây dựng toàn diện bằng ngôn ngữ lập trình **PHP** và hệ quản trị cơ sở dữ liệu **MySQL**, đáp ứng đầy đủ quy trình bán hàng trực tuyến hiện đại và phân hệ quản trị Admin chuyên nghiệp.

---

## 🌟 1. Tính Năng Nổi Bật

### A. Phân Hệ Khách Hàng (Client Storefront)
- **Trang chủ (`index.php`)**: Hero Slider trình chiếu bộ sưu tập thời trang, đồng hồ đếm ngược Flash Sale Giờ Vàng theo thời gian thực, Khối sản phẩm bán chạy, Lookbook cá tính thương hiệu.
- **Danh mục & Bộ lọc đa chiều (`products.php`)**:
  - Lọc theo 7 danh mục thời trang (Áo thun & Polo, Áo sơ mi, Áo khoác & Hoodie, Quần jeans, Quần kaki, Váy đầm nữ, Phụ kiện).
  - Lọc theo mức giá (Dưới 200K, 200-400K, 400-600K, Trên 600K).
  - Lọc theo kích cỡ size (S, M, L, XL, XXL, 29, 30, 31, 32).
  - Tìm kiếm theo từ khóa tên hoặc mã SKU sản phẩm.
  - Sắp xếp: Mới nhất, Xem nhiều nhất, Giá tăng/giảm dần.
- **Trang chi tiết sản phẩm (`product_detail.php`)**:
  - Trình xem ảnh thời trang sắc nét kèm thumbnail.
  - Bộ chọn Size và Màu sắc trực quan.
  - **Bảng quy đổi kích cỡ chuẩn (Size Guide Modal)** tính theo chiều cao / cân nặng.
  - Tùy chọn "Thêm vào giỏ hàng" và "Mua ngay" tức thì.
  - Hệ thống Tab: Chi tiết chất liệu, Hướng dẫn bảo quản/giặt ủi, Đánh giá nhận xét kèm số sao 1-5.
- **Giỏ hàng (`cart.php`)**: Cập nhật số lượng, xóa món, áp dụng mã giảm giá (VD: `HIEUMINI10`, `FREESHIP`), tự động tính toán miễn phí vận chuyển cho đơn từ 300.000đ.
- **Thanh toán & Đặt hàng (`checkout.php`)**:
  - Biểu mẫu thu thập địa chỉ nhận hàng và ghi chú.
  - Phương thức thanh toán: Tiền mặt khi nhận hàng (COD) hoặc Chuyển khoản VietQR hiển thị thông tin MBBank.
- **Hóa đơn điện tử (`order_success.php`)**: Hiển thị chi tiết đơn hàng, mã vận đơn, hỗ trợ In hóa đơn.
- **Tài khoản & Tra cứu (`login.php`, `register.php`, `profile.php`, `my_orders.php`)**: Đăng ký, đăng nhập an toàn (Bcrypt), đổi mật khẩu, theo dõi lịch sử đơn hàng.

### B. Phân Hệ Quản Trị (Admin Control Panel)
- **Dashboard (`admin/index.php`)**: Thống kê Tổng doanh thu, Tổng đơn hàng, Số khách hàng, Tồn kho, đơn hàng mới nhất và sản phẩm xem nhiều.
- **Quản lý sản phẩm (`admin/products.php`, `admin/product_add.php`, `admin/product_edit.php`)**: Thêm, sửa, xóa, tải lên ảnh sản phẩm, quản lý size/màu, cảnh báo hàng sắp hết kho.
- **Quản lý danh mục (`admin/categories.php`)**: Thêm mới danh mục, liên kết sản phẩm.
- **Quản lý đơn hàng (`admin/orders.php`, `admin/order_detail.php`)**: Lọc theo trạng thái (Chờ xử lý, Đang xử lý, Đang giao, Hoàn thành, Đã hủy), cập nhật trạng thái đơn & thanh toán, in hóa đơn xuất kho.
- **Quản lý mã giảm giá (`admin/coupons.php`)**: Tạo voucher giảm theo % hoặc số tiền cố định, thiết lập hạn sử dụng và đơn tối thiểu.
- **Quản lý người dùng (`admin/users.php`)**: Xem danh sách khách hàng và phân quyền Admin linh hoạt.

---

## 🔑 2. Tài Khoản Mặc Định

| Loại tài khoản | Email đăng nhập | Mật khẩu | Phân quyền |
| :--- | :--- | :--- | :--- |
| **Quản trị viên (Admin)** | `admin@hieumini.vn` | `admin123` | Toàn quyền quản trị |
| **Khách hàng mẫu (Customer)** | `khachhang@gmail.com` | `admin123` | Mua hàng, đánh giá, xem đơn |

---

## 🚀 3. Hướng Dẫn Cài Đặt & Vận Hành

1. **Khởi động XAMPP**:
   - Mở XAMPP Control Panel và nhấn **Start** tại **Apache** và **MySQL**.
2. **Khởi tạo cơ sở dữ liệu**:
   - Chạy lệnh khởi tạo dữ liệu mẫu (hoặc import file `database/hieumini_db.sql` vào phpMyAdmin):
   ```bash
   php database/init_database.php
   ```
3. **Khởi chạy máy chủ Web**:
   - Chạy lệnh:
   ```bash
   php -S 127.0.0.1:8088
   ```
   - Mở trình duyệt và truy cập: **`http://127.0.0.1:8088/index.php`**
   - Truy cập trang quản trị Admin: **`http://127.0.0.1:8088/admin/index.php`**

---

## 📄 4. Tài Liệu Báo Cáo Học Thuật (`BaoCao.docx`)

Tài liệu báo cáo hoàn chỉnh được soạn thảo đầy đủ, chi tiết, chuyên sâu theo đúng mục lục `mucluc.txt`:
- **Chương 1. Tổng quan lập trình web**: 1.1 Ngôn ngữ PHP, 1.2 Hệ quản trị MySQL, 1.3 Cài đặt máy chủ XAMPP.
- **Chương 2. Phân tích và thiết kế website**: 2.1 Chức năng (Usecase & bảng đặc tả chi tiết), 2.2 Cơ sở dữ liệu (7 bảng CSDL & ERD).
- **Chương 3. Chương trình thử nghiệm**: 3.1 Giao diện (Client & Admin), 3.2 Kết luận (Đánh giá, ưu điểm, định hướng tương lai).
- File báo cáo được lưu tại: **`c:\Users\tranv\Desktop\HieuWeb01\BaoCao.docx`**.
