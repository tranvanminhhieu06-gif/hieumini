-- ======================================================================
--  Cơ sở dữ liệu: hieumini_gym_db
--  Dán CẢ tệp vào TiDB SQL Editor rồi bấm Run.
--  Không cần chọn database — mọi bảng đã gắn sẵn tên CSDL.
-- ======================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


-- CƠ SỞ DỮ LIỆU WEBSITE THỂ HÌNH CAO CẤP HIEUMINI LUXURY FITNESS CLUB (CHUẨN CEO)
-- Database: hieumini_gym
-- Tương thích: MySQL 5.7+ / MySQL 8.0+ / MariaDB

CREATE DATABASE IF NOT EXISTS `hieumini_gym_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

-- 1. Bảng danh mục sản phẩm & dịch vụ (categories)
DROP TABLE IF EXISTS `hieumini_gym_db`.`categories`;
CREATE TABLE `hieumini_gym_db`.`categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT,
  `icon` VARCHAR(100) DEFAULT 'fa-dumbbell',
  `image` VARCHAR(255) DEFAULT 'cat_default.jpg',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng sản phẩm & gói dịch vụ thể hình (products)
DROP TABLE IF EXISTS `hieumini_gym_db`.`products`;
CREATE TABLE `hieumini_gym_db`.`products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `sku` VARCHAR(50) NOT NULL UNIQUE,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `price` DECIMAL(15,2) NOT NULL,
  `original_price` DECIMAL(15,2) DEFAULT NULL,
  `stock` INT NOT NULL DEFAULT 100,
  `rating` DECIMAL(3,2) DEFAULT 5.00,
  `review_count` INT DEFAULT 0,
  `badge` VARCHAR(50) DEFAULT NULL,
  `image` VARCHAR(255) NOT NULL,
  `short_description` VARCHAR(500) DEFAULT NULL,
  `description` LONGTEXT,
  `specs_json` LONGTEXT,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_bestseller` TINYINT(1) DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `hieumini_gym_db`.`categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng người dùng / Admin (users)
DROP TABLE IF EXISTS `hieumini_gym_db`.`users`;
CREATE TABLE `hieumini_gym_db`.`users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `phone` VARCHAR(20) DEFAULT NULL,
  `password` VARCHAR(255) NOT NULL,
  `role` ENUM('admin', 'member') DEFAULT 'member',
  `avatar` VARCHAR(255) DEFAULT 'default_avatar.jpg',
  `address` VARCHAR(255) DEFAULT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng đơn hàng (orders)
DROP TABLE IF EXISTS `hieumini_gym_db`.`orders`;
CREATE TABLE `hieumini_gym_db`.`orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_code` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `customer_address` VARCHAR(255) NOT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'cod',
  `payment_status` ENUM('pending', 'paid', 'failed') DEFAULT 'pending',
  `order_status` ENUM('pending', 'processing', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
  `subtotal` DECIMAL(15,2) NOT NULL,
  `discount_amount` DECIMAL(15,2) DEFAULT 0,
  `shipping_fee` DECIMAL(15,2) DEFAULT 0,
  `total_amount` DECIMAL(15,2) NOT NULL,
  `coupon_code` VARCHAR(50) DEFAULT NULL,
  `notes` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `hieumini_gym_db`.`users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng chi tiết đơn hàng (order_items)
DROP TABLE IF EXISTS `hieumini_gym_db`.`order_items`;
CREATE TABLE `hieumini_gym_db`.`order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_image` VARCHAR(255) NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `subtotal` DECIMAL(15,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `hieumini_gym_db`.`orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `hieumini_gym_db`.`products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng đặt lịch tập thử VIP & Đăng ký tư vấn (bookings)
DROP TABLE IF EXISTS `hieumini_gym_db`.`bookings`;
CREATE TABLE `hieumini_gym_db`.`bookings` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `full_name` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `service_type` VARCHAR(150) NOT NULL,
  `branch` VARCHAR(150) DEFAULT 'HieuMini Luxury Diamond - Quận 1, TP.HCM',
  `booking_date` DATE NOT NULL,
  `booking_time` VARCHAR(50) DEFAULT '09:00 - 11:00',
  `fitness_goal` VARCHAR(255) DEFAULT 'Tăng cơ, giảm mỡ, rèn luyện thể lực CEO',
  `notes` TEXT,
  `status` ENUM('pending', 'confirmed', 'completed', 'cancelled') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bảng liên hệ phản hồi (contacts)
DROP TABLE IF EXISTS `hieumini_gym_db`.`contacts`;
CREATE TABLE `hieumini_gym_db`.`contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(150) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `subject` VARCHAR(255) NOT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('unread', 'read', 'replied') DEFAULT 'unread',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Bảng đánh giá sản phẩm (reviews)
DROP TABLE IF EXISTS `hieumini_gym_db`.`reviews`;
CREATE TABLE `hieumini_gym_db`.`reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_name` VARCHAR(150) NOT NULL,
  `user_role` VARCHAR(100) DEFAULT 'CEO / Hội Viên VIP',
  `rating` INT NOT NULL DEFAULT 5,
  `comment` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `hieumini_gym_db`.`products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NẠP DỮ LIỆU MẪU CHUẨN CEO

-- Danh mục
INSERT INTO `hieumini_gym_db`.`categories` (`id`, `name`, `slug`, `description`, `icon`, `image`) VALUES
(1, 'Gói Hội Viên & Dịch Vụ VIP', 'goi-hoi-vien-vip', 'Đặc quyền trải nghiệm không gian tập luyện chuẩn 5 sao đẳng cấp CEO', 'fa-crown', 'cat_membership.jpg'),
(2, 'Thiết Bị & Máy Tập Thể Hình', 'thiet-bi-may-tap', 'Hệ thống máy tập cơ khí công nghệ cao nhập khẩu tiêu chuẩn Olympic', 'fa-dumbbell', 'cat_equipment.jpg'),
(3, 'Dinh Dưỡng & Thực Phẩm Bổ Sung', 'dinh-duong-the-hinh', 'Thực phẩm dinh dưỡng tinh khiết nhập khẩu chính ngạch từ Hoa Kỳ & Châu Âu', 'fa-capsules', 'cat_supplements.jpg'),
(4, 'Phụ Kiện & Trang Phục Tập', 'phu-kien-trang-phuc', 'Trang bị tập luyện cao cấp da bò thật, sợi carbon và vải co giãn cao cấp', 'fa-tshirt', 'cat_apparel.jpg'),
(5, 'Huấn Luyện Viên & Trị Liệu', 'huan-luyen-vien-tri-lieu', 'Đội ngũ Master Trainer quốc tế và trị liệu thể thao phục hồi cơ chuyên sâu', 'fa-user-ninja', 'cat_pt.jpg');

-- Tài khoản Admin & Member mặc định (Mật khẩu: Admin@123 -> $2y$10$eW4P16P/0vQo0hWbMskg..9z/PqU3kGk5e/O56yTq20oD7/ZfJgxe hoặc hash trực tiếp)
INSERT INTO `hieumini_gym_db`.`users` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `avatar`, `address`) VALUES
(1, 'CEO HieuMini', 'admin@hieumini.com', '0988889999', '$2y$10$k1M43n1.XlQk8R91E7lG2.8ZgVqA1p14Xw0nU4zD5qR8K2u5F9P2e', 'admin', 'ceo_avatar.jpg', 'Tòa nhà HieuMini Tower, Quận 1, TP. Hồ Chí Minh'),
(2, 'Doanh Nhân Nguyễn Hoàng Long', 'member@hieumini.com', '0912345678', '$2y$10$k1M43n1.XlQk8R91E7lG2.8ZgVqA1p14Xw0nU4zD5qR8K2u5F9P2e', 'member', 'member_avatar.jpg', 'Khu Đô Thị Sala, TP. Thủ Đức, TP. Hồ Chí Minh');

-- 30 SẢN PHẨM & DỊCH VỤ FITNESS CHUẨN CEO
INSERT INTO `hieumini_gym_db`.`products` (`id`, `category_id`, `sku`, `name`, `slug`, `price`, `original_price`, `stock`, `rating`, `review_count`, `badge`, `image`, `short_description`, `description`, `specs_json`, `is_featured`, `is_bestseller`) VALUES
-- 1. Gói Hội Viên
(1, 1, 'MEM-01', 'Gói Hội Viên CEO Diamond Elite 1 Năm', 'goi-hoi-vien-ceo-diamond-elite-1-nam', 24500000, 28000000, 50, 5.0, 38, 'CEO VIP', '01_membership_diamond.jpg', 'Thẻ hội viên cao cấp nhất toàn quyền sử dụng tất cả tiện ích 5 sao, hồ bơi vô cực, xông hơi đá muối Himalaya và tủ đồ riêng.', 'Gói hội viên CEO Diamond Elite là biểu tượng của đẳng cấp thể hình thượng lưu. Hội viên sở hữu thẻ được toàn quyền tiếp cận không gian tập luyện VIP Lounge, sử dụng hệ thống phục hồi Hydrotherapy, InBody 770 không giới hạn, kèm 12 buổi Master Trainer 1:1 và dịch vụ giặt sấy đồ tập cao cấp.', '{"Thời hạn": "12 Tháng", "Phạm vi": "Toàn bộ chi nhánh HieuMini", "Dịch vụ kèm theo": "Sauna, Bể sục Jacuzzi, VIP Lounge, Tủ đồ Smartlock", "Ưu đãi": "Tặng 12 buổi PT 1:1 & Bộ dinh dưỡng CEO"}', 1, 1),
(2, 1, 'MEM-02', 'Gói Hội Viên Executive Gold 6 Tháng', 'goi-hoi-vien-executive-gold-6-thang', 13900000, 15500000, 80, 4.9, 26, 'HOT SALE', '02_membership_gold.jpg', 'Gói tập 6 tháng đẳng cấp dành cho doanh nhân và nhà quản lý bận rộn với thời gian linh hoạt không giới hạn khung giờ.', 'Trải nghiệm không gian thể thao đỉnh cao với thẻ Executive Gold. Bạn sẽ được rèn luyện thể lực với hệ thống máy tập hàng đầu thế giới, phòng tắm sauna chuẩn Phần Lan, đo chỉ số cơ mỡ định kỳ và tham gia các lớp Yoga, Kickboxing đỉnh cao.', '{"Thời hạn": "06 Tháng", "Thời gian tập": "05:30 - 22:30 hàng ngày", "Tiện ích": "Sauna, Khăn tập cao cấp, Tủ đồ thông minh", "Tặng kèm": "04 buổi hướng dẫn thể lực cá nhân"}', 1, 0),
(3, 1, 'MEM-03', 'Thẻ Tập VIP Platinum All-Access 3 Tháng', 'the-tap-vip-platinum-all-access-3-thang', 7900000, 8500000, 100, 4.8, 19, 'PHỔ BIẾN', '03_membership_platinum.jpg', 'Gói trải nghiệm toàn diện 90 ngày bứt phá phong độ và thể lực chuẩn CEO tại HieuMini Luxury Club.', 'Gói thẻ Platinum All-Access mang đến giải pháp rèn luyện tối ưu trong 3 tháng. Đầy đủ tiện ích đẳng cấp, hỗ trợ lập lộ trình tập luyện bài bản từ huấn luyện viên trưởng.', '{"Thời hạn": "03 Tháng", "Quyền lợi": "Tập luyện không giới hạn, tham gia tất cả lớp Studio", "Đo InBody": "Miễn phí 2 tuần/lần", "Phục hồi": "Khu vực Sauna & Steam bath"}', 0, 1),
(4, 1, 'MEM-04', 'Vé Trải Nghiệm VIP Day Pass & Sauna', 've-trai-nghiem-vip-day-pass-sauna', 350000, 500000, 200, 4.9, 52, 'TRẢI NGHIỆM', '04_day_pass.jpg', 'Vé tập luyện 01 ngày trọn gói trải nghiệm toàn bộ trang thiết bị và dịch vụ xông hơi phục hồi 5 sao.', 'Thử thách và cảm nhận sự khác biệt tại HieuMini Luxury Fitness trong 1 ngày với quyền tiếp cận toàn bộ khu máy tập cardio, tạ tự do, khu phục hồi sauna và dịch vụ khăn tập cao cấp.', '{"Thời hạn": "01 Ngày", "Bao gồm": "Full phòng tập, Bể sục Jacuzzi, Khăn tắm, Nước ion kiềm", "Yêu cầu": "Đăng ký trước 2 tiếng"}', 0, 0),
(5, 1, 'MEM-05', 'Gói Hội Viên Doanh Nghiệp Corporate Club', 'goi-hoi-vien-doanh-nghiep-corporate-club', 39900000, 45000000, 30, 5.0, 14, 'DOANH NGHIỆP', '05_corporate_club.jpg', 'Gói giải pháp chăm sóc sức khỏe và thể lực toàn diện dành riêng cho ban lãnh đạo và đối tác doanh nghiệp (5 thành viên).', 'Chương trình sức khỏe doanh nghiệp Corporate Club thiết kế đặc biệt nhằm nâng cao thể chất, giải tỏa căng thẳng cho đội ngũ quản lý cấp cao với quyền lợi chia sẻ linh hoạt cho 5 thành viên.', '{"Quy mô": "Dành cho nhóm 05 thành viên", "Thời hạn": "06 Tháng", "Đặc quyền": "Phòng họp thể thao riêng, PT nhóm, Đánh giá sức khỏe tổng quát"}', 1, 0),

-- 2. Thiết Bị & Máy Tập Thể Hình
(6, 2, 'EQP-01', 'Máy Chạy Bộ Thương Mại Commercial X9 Pro', 'may-chay-bo-thuong-mai-commercial-x9-pro', 59900000, 68000000, 15, 5.0, 22, 'SIÊU PHẨM', '06_commercial_treadmill.jpg', 'Động cơ AC 6.0 HP siêu bền bỉ, màn hình cảm ứng 21.5 inch 4K kết nối thực tế ảo, độ dốc tự động 18%.', 'Dòng máy chạy bộ thương mại cao cấp chuyên dụng cho các câu lạc bộ thể hình hàng đầu. Động cơ công suất cực đại 6.0 HP vận hành êm ái 24/7, bề mặt thảm chạy 7 lớp giảm chấn siêu êm bảo vệ khớp gối tuyệt đối.', '{"Động cơ": "AC 6.0 HP Heavy Duty", "Tốc độ": "0.8 - 25.0 km/h", "Độ dốc": "0 - 18%", "Màn hình": "21.5 Inch Full HD Touchscreen", "Tải trọng": "220 kg"}', 1, 1),
(7, 2, 'EQP-02', 'Khung Gánh Đa Năng Monster Power Rack Pro', 'khung-ganh-da-nang-monster-power-rack-pro', 36500000, 42000000, 20, 4.9, 17, 'CHUYÊN NGHIỆP', '07_power_rack.jpg', 'Khung thép chịu lực hộp 75x75mm dày 3.5mm, tích hợp xà đơn đa hướng, móc tạ J-Cups và thanh an toàn cao cấp.', 'Bộ khung Monster Power Rack Pro là trung tâm sức mạnh cho mọi bài tập Squat, Bench Press, Pull-up, Deadlift. Khung thép sơn tĩnh điện chống trầy xước, chịu lực tải lên đến 1000kg chuẩn vận động viên Olympic.', '{"Vật liệu": "Thép kết cấu Q235 dày 3.5mm", "Kích thước": "140 x 135 x 235 cm", "Tải trọng chịu lực": "1000 kg", "Phụ kiện đi kèm": "J-Cups bọc Polyurethane, Dây đai an toàn Safety Straps, Thanh xà Multi-Grip"}', 1, 0),
(8, 2, 'EQP-03', 'Máy Smith Machine 3D Chuẩn Phòng Gym', 'may-smith-machine-3d-chuan-phong-gym', 32900000, 38000000, 18, 4.8, 15, 'CAO CẤP', '08_smith_machine.jpg', 'Hệ thống ray trượt bi tuyến tính 3D chuyển động tự do theo cả trục đứng và ngang, khóa an toàn tự động thông minh.', 'Máy tập Smith Machine 3D thế hệ mới mô phỏng chính xác chuyển động cơ học tự nhiên của cơ thể nhưng vẫn đảm bảo an toàn tuyệt đối khi tập nặng mà không cần người đỡ tạ (Spotter).', '{"Hệ chuyển động": "Ray trượt bi hợp kim tuyến tính 3D", "Trục đòn": "Thép mạ Chrome phi 50mm chuẩn Olympic", "Kích thước": "210 x 160 x 225 cm", "Trọng lượng máy": "210 kg"}', 0, 1),
(9, 2, 'EQP-04', 'Giàn Kéo Cáp Đôi Dual Cable Crossover', 'gian-keo-cap-doi-dual-cable-crossover', 46000000, 52000000, 12, 4.9, 21, 'ĐẲNG CẤP', '09_cable_crossover.jpg', 'Hai cụm tạ tạ lá độc lập 100kg mỗi bên, bánh xe ròng rọc nhôm CNC xoay 180 độ điều chỉnh 36 nấc chiều cao.', 'Cung cấp hàng trăm bài tập cô lập toàn diện cho cơ ngực, vai, lưng, tay và cơ lõi. Dây cáp hàng không chịu tải 1500kg kết hợp ròng rọc bạc đạn trơn tru chuẩn CEO Club.', '{"Trọng lượng tạ lá": "200 kg (100kg x 2 cụm)", "Cáp kéo": "Thép lõi bọc Nylon chống mài mòn phi 6mm", "Ròng rọc": "Nhôm CNC nguyên khối bạc đạn đôi", "Kích thước": "180 x 115 x 220 cm"}', 1, 0),
(10, 2, 'EQP-05', 'Bộ Đòn Tạ Olympic & Bánh Tạ Bumper 150kg', 'bo-don-ta-olympic-banh-ta-bumper-150kg', 16200000, 18500000, 35, 5.0, 31, 'BÁN CHẠY', '10_olympic_barbell_set.jpg', 'Đòn tạ Olympic 20kg thép đàn hồi 215k PSI 8 vòng bi, kèm bộ bánh tạ cao su nguyên sinh Bumper Plates màu thi đấu.', 'Bộ tạ tiêu chuẩn thi đấu IWF mang lại cảm giác nâng tạ hoàn hảo. Bánh tạ cao su siêu bền độ nảy thấp bảo vệ mặt sàn tuyệt đối trong các bài tập cử tạ Olympic và rèn luyện thể lực mạnh mẽ.', '{"Đòn tạ": "Olympic Barbell 2.2m, 20kg, tải trọng 900kg, mạ Hard Chrome", "Bánh tạ gồm": "2x25kg, 2x20kg, 2x15kg, 2x10kg, 2x5kg", "Khóa tạ": "Bộ Aluminum Collars Pro"}', 0, 1),
(11, 2, 'EQP-06', 'Bộ Tạ Đơn Thông Minh Điều Chỉnh 40kg QuickLock', 'bo-ta-don-thong-minh-dieu-chinh-40kg-quicklock', 10800000, 12500000, 45, 4.9, 44, 'TIỆN DỤNG', '11_smart_dumbbells.jpg', 'Cơ chế xoay khóa số chỉ mất 1 giây để chuyển đổi mức tạ từ 5kg đến 40kg, thay thế trọn bộ 16 cặp tạ truyền thống.', 'Giải pháp hoàn hảo cho không gian tập luyện CEO tại gia đình và văn phòng. Cơ cấu bánh răng hợp kim siêu chính xác, tay cầm bọc thép vân kim cương chống trơn trượt.', '{"Mức tạ điều chỉnh": "5kg, 7.5kg, 10kg, 12.5kg ... lên đến 40kg", "Quy cách": "Bộ 2 chiếc kèm đế đỡ sang trọng", "Chất liệu": "Thép đặc sơn tĩnh điện cao cấp"}', 1, 1),
(12, 2, 'EQP-07', 'Máy Chèo Thuyền Kháng Lực Nước WaterRower Pro', 'may-cheo-thuyen-khang-luc-nuoc-waterrower-pro', 22900000, 26000000, 25, 4.8, 16, 'THỦ CÔNG', '12_water_rower.jpg', 'Khung gỗ sồi tự nhiên nguyên khối cao cấp, bình chứa nước kháng lực vô cấp tạo âm thanh lướt sóng chân thực.', 'Thiết bị cardio đẳng cấp được các CEO yêu thích nhờ tác động đến 86% nhóm cơ trên cơ thể, đốt cháy đến 1000 calo/giờ mà không gây áp lực lên khớp gối.', '{"Chất liệu": "Gỗ sồi Bắc Mỹ xử lý chống ẩm cao cấp", "Bình kháng lực": "Polycarbonate chống va đập", "Màn hình": "Hiển thị công suất Watt, nhịp chèo, calo, cự ly", "Gập gọn": "Có bánh xe di chuyển đứng gọn gàng"}', 0, 0),
(13, 2, 'EQP-08', 'Bộ Tạ Ấm Cast Iron Competition Kettlebell 24kg', 'bo-ta-am-cast-iron-competition-kettlebell-24kg', 2650000, 3200000, 60, 4.9, 27, 'BỀN BỈ', '13_kettlebell_set.jpg', 'Đúc từ gang nguyên khối không hàn nối, tay cầm rộng gia công CNC nhẵn mịn phủ sơn tĩnh điện nhám cao cấp.', 'Dụng cụ rèn luyện sức bền, sức mạnh bùng nổ và sự dẻo dai toàn thân. Phù hợp cho các bài tập Kettlebell Swing, Snatch, Turkish Get-up chuẩn vận động viên.', '{"Trọng lượng": "24 kg", "Chất liệu": "Gang cầu đặc đúc nguyên khối", "Đường kính tay cầm": "35mm chuẩn thi đấu", "Màu sắc": "Đen mờ viền sơn nhận diện xanh lá"}', 0, 0),

-- 3. Dinh Dưỡng & Thực Phẩm Bổ Sung
(14, 3, 'SUP-01', 'Sữa Tăng Cơ HieuMini Hydrolyzed Whey Isolate 5lbs', 'sua-tang-co-hieumini-hydrolyzed-whey-isolate-5lbs', 2150000, 2450000, 120, 5.0, 89, 'BEST SELLER', '14_whey_isolate.jpg', '100% Đạm Whey Thủy Phân Hydrolyzed siêu tinh khiết, 28g Protein, 6.5g BCAA, 0 đường, 0 chất béo, hấp thu tức thì.', 'Nguồn protein chất lượng cao nhất cho cơ bắp phát triển thần tốc. Công nghệ lọc vi sinh lạnh Cross-Flow Microfiltration giữ trọn các phân đoạn sinh học quý giá hỗ trợ phục hồi và phát triển cơ nạc tối đa.', '{"Trọng lượng": "5 Lbs (2.27 kg) ~ 75 khẩu phần", "Hàm lượng": "28g Protein Hydrolyzed / muỗng", "Hương vị": "Chocolate Bỉ thượng hạng / Vani Madagascar", "Xuất xứ": "Made in USA"}', 1, 1),
(15, 3, 'SUP-02', 'Bột Tăng Sức Mạnh Creatine Creapure 500g', 'bot-tang-suc-manh-creatine-creapure-500g', 820000, 950000, 150, 4.9, 65, 'TINH KHIẾT', '15_creatine_creapure.jpg', '100% Creatine Monohydrate nguồn nguyên liệu Creapure® độc quyền từ Đức đạt độ tinh khiết 99.99%.', 'Gia tăng sức mạnh bùng nổ, tăng thể tích tế bào cơ bắp và đẩy lùi mệt mỏi trong các hiệp tập nặng. Không gây tích nước dưới da, hòa tan cực nhanh.', '{"Trọng lượng": "500g ~ 100 lần dùng", "Thành phần": "5g Pure Creapure® Monohydrate / liều", "Đặc tính": "Không mùi, không vị, dễ pha chung với Whey/BCAA", "Tiêu chuẩn": "IFS Food & GMP Germany"}', 1, 1),
(16, 3, 'SUP-03', 'Năng Lượng Trước Tập Pre-Workout Explosive Energy', 'nang-luong-truoc-tap-pre-workout-explosive-energy', 990000, 1150000, 90, 4.9, 48, 'NĂNG LƯỢNG', '16_pre_workout.jpg', 'Công thức tăng lực bùng nổ với 300mg Caffeine tự nhiên, 6g L-Citrulline Malate, 3.2g Beta-Alanine và Alpha-GPC.', 'Tăng cường sự tỉnh táo tập trung cao độ, bơm máu căng phồng cơ bắp (Muscle Pump) và đẩy cao ngưỡng chịu đựng giúp bạn tập luyện như một chiến binh.', '{"Quy cách": "60 Servings (Hũ 450g)", "Hoạt chất chính": "L-Citrulline, Beta-Alanine, Caffeine Anhydrous, L-Theanine", "Hương vị": "Việt quất dâu rừng / Táo xanh đá tuyết"}', 1, 0),
(17, 3, 'SUP-04', 'Phục Hồi Cơ Bắp BCAA & EAA Intra-Workout Matrix', 'phuc-hoi-co-bap-bcaa-eaa-intra-workout-matrix', 1080000, 1250000, 110, 4.8, 37, 'PHỤC HỒI', '17_bcaa_eaa.jpg', 'Bộ 9 Axit Amin thiết yếu EAA kết hợp BCAA tỷ lệ vàng 2:1:1 và điện giải khoáng chất dừa tự nhiên bù nước tức thì.', 'Chống dị hóa cơ bắp trong lúc tập, giảm đau nhức cơ sau tập (DOMS) và duy trì sự bền bỉ suốt buổi tập cường độ cao của các CEO.', '{"Khối lượng": "450g (30 lần dùng)", "Thành phần": "8g EAA + 5g BCAA + 500mg Coconut Water Powder", "Hương vị": "Cam nhiệt đới / Chanh leo bạc hà", "Đặc điểm": "Không đường, không phẩm màu nhân tạo"}', 0, 1),
(18, 3, 'SUP-05', 'Sữa Tăng Cân Nhanh Mass Gainer Complex 12lbs', 'sua-tang-can-nhanh-mass-gainer-complex-12lbs', 1950000, 2200000, 70, 4.8, 42, 'TĂNG CÂN', '18_mass_gainer.jpg', 'Cung cấp 1250 Calo, 55g Protein chất lượng cao và tinh bột phức hợp từ yến mạch giúp người gầy tăng cân nạc bền vững.', 'Giải pháp hoàn hảo cho người gầy khó tăng cân. Bổ sung đầy đủ vitamin khoáng chất và enzyme tiêu hóa DigeZyme giúp hấp thu dinh dưỡng tối đa mà không gây đầy bụng.', '{"Trọng lượng": "12 Lbs (5.44 kg)", "Calo mỗi liều": "1250 kcal (pha nước) / 1600 kcal (pha sữa tươi)", "Protein": "55g đa tầng (Whey, Casein, Egg)", "Hương vị": "Socola chuối / Bánh quy kem"}', 0, 0),
(19, 3, 'SUP-06', 'Dầu Cá Tinh Khiết Triple Strength Omega-3 Gold', 'dau-ca-tinh-khiet-triple-strength-omega-3-gold', 650000, 780000, 140, 5.0, 56, 'SỨC KHỎE', '19_omega3_fishoil.jpg', 'Hàm lượng cực cao 1000mg EPA + 500mg DHA mỗi viên nang, chiết xuất từ cá biển sâu Na Uy được khử mùi tanh.', 'Hỗ trợ sức khỏe tim mạch, cải thiện chức năng não bộ, bôi trơn khớp xương và tăng cường phục hồi cơ bắp cho các nhà lãnh đạo làm việc áp lực cao.', '{"Quy cách": "Hộp 120 viên nang mềm", "Hàm lượng": "1500mg Omega-3 (1000mg EPA / 500mg DHA)", "Độ tinh khiết": "Chứng nhận IFOS 5 Sao không chứa kim loại nặng", "Xuất xứ": "Norway"}', 1, 0),
(20, 3, 'SUP-07', 'Vitamin Tổng Hợp & Khoáng Chất CEO Elite Multi', 'vitamin-tong-hop-khoang-chat-ceo-elite-multi', 720000, 850000, 130, 4.9, 39, 'ĐỀ KHÁNG', '20_multivitamin.jpg', 'Tổ hợp hơn 30 vitamin, khoáng chất thiết yếu, chiết xuất sâm Maca, CoQ10 và chất chống oxy hóa tăng cường sinh lực.', 'Bảo vệ hệ miễn dịch, tăng cường năng lượng chuyển hóa tế bào và duy trì phong độ đỉnh cao cho cả ngày dài làm việc và tập luyện.', '{"Số lượng": "90 viên (Dùng trong 3 tháng)", "Thành phần nổi bật": "Vitamin A, B-Complex, C, D3, K2, Kẽm, Magie, CoQ10", "Dành cho": "Nam & Nữ vận động viên, doanh nhân"}', 0, 0),
(21, 3, 'SUP-08', 'Thùng Bánh Protein Bar Siêu Tiện Lợi (Hộp 12 Thanh)', 'thung-banh-protein-bar-sieu-tien-loi-hop-12-thanh', 620000, 720000, 160, 4.9, 73, 'TIỆN LỢI', '21_protein_bar.jpg', 'Mỗi thanh chứa 20g Protein tinh khiết, 3g chất xơ, phủ socola giòn rụm giòn ngon như bánh kẹo cao cấp.', 'Bữa ăn phụ hoàn hảo trước hoặc sau giờ tập, hoặc nạp năng lượng nhanh giữa các cuộc họp quan trọng của CEO.', '{"Quy cách": "Hộp 12 thanh x 60g", "Dinh dưỡng": "20g Protein, 2g Đường, 210 Calo", "Hương vị": "Caramel muối socola / Bơ đậu phộng giòn"}', 0, 1),

-- 4. Phụ Kiện & Trang Phục Tập
(22, 4, 'APP-01', 'Đai Lưng Cứng Nâng Tạ Da Bò Thật Leather Lever Belt', 'dai-lung-cung-nang-ta-da-bo-that-leather-lever-belt', 1390000, 1650000, 85, 5.0, 61, 'SIÊU BỀN', '22_leather_lever_belt.jpg', 'Chất liệu da bò tự nhiên 4 lớp dày 10mm hoặc 13mm chuẩn IPF, khóa gạt thép không gỉ Stainless Steel mạ crom mờ.', 'Bảo vệ cột sống và vùng thắt lưng tuyệt đối trong các bài nâng nặng Squat và Deadlift. Khóa gạt thông minh đóng mở siêu tốc trong 0.5 giây.', '{"Độ dày": "10mm / 13mm chuẩn thi đấu quốc tế", "Chất liệu": "100% Da bò thuộc tự nhiên lớp Top-grain", "Khóa": "Thép hợp kim nguyên khối siêu bền bảo hành trọn đời", "Size": "S, M, L, XL"}', 1, 1),
(23, 4, 'APP-02', 'Băng Gối Trợ Lực Nâng Tạ Neoprene Knee Sleeves 7mm', 'bang-goi-tro-luc-nang-ta-neoprene-knee-sleeves-7mm', 990000, 1200000, 95, 4.9, 45, 'TRỢ LỰC', '23_knee_sleeves.jpg', 'Cao su Neoprene mật độ cao 7mm chuẩn Powerlifting mang lại sự nén ép và giữ ấm khớp gối tối ưu.', 'Tăng độ ổn định cho đầu gối, hỗ trợ lực đẩy đáy khi Squat và hạn chế tối đa nguy cơ chấn thương dây chằng khớp gối.', '{"Độ dày": "7mm High-Density Neoprene", "Đường may": "Chỉ may gia cường 4 kim 6 chỉ chịu lực xé", "Màu sắc": "Đen chỉ vàng kim HieuMini", "Kích cỡ": "M, L, XL, XXL"}', 0, 1),
(24, 4, 'APP-03', 'Dây Kéo Lưng Figure-8 Deadlift Straps Chuyên Dụng', 'day-keo-lung-figure-8-deadlift-straps-chuyen-dung', 350000, 450000, 150, 4.9, 58, 'CHỐNG TUỘT', '24_figure8_straps.jpg', 'Sợi vải bố dệt cotton pha nylon dày 5mm siêu dai, chịu lực kéo tĩnh lên đến 600kg, khóa chặt đòn tạ vào lòng bàn tay.', 'Loại bỏ hoàn toàn giới hạn sức nắm của cẳng tay giúp bạn tập trung 100% lực kéo vào nhóm cơ lưng xô và mông đùi.', '{"Kiểu dáng": "Hình số 8 khóa kép chống tuột tay", "Chịu tải": "600 kg", "Đệm cổ tay": "Neoprene êm ái chống hằn đau", "Quy cách": "1 Cặp (2 chiếc)"}', 0, 0),
(25, 4, 'APP-04', 'Bình Lắc Thép Giữ Nhiệt Cao Cấp Steel Shaker 800ml', 'binh-lac-thep-giu-nhiet-cao-cap-steel-shaker-800ml', 420000, 550000, 180, 5.0, 77, 'GIỮ NHIỆT', '25_steel_shaker.jpg', 'Thép không gỉ thực phẩm SUS 304 hai lớp chân không, giữ lạnh 24 giờ, lưới đánh tan bột chuyên dụng và nắp chống rò rỉ.', 'Chiếc bình lắc đẳng cấp không bao giờ ám mùi hôi protein. Logo HieuMini khắc laser tinh xảo phong cách doanh nhân.', '{"Dung tích": "800 ml (Có vạch chia mililit dập nổi)", "Chất liệu": "Inox 304 cao cấp an toàn BPA Free", "Khả năng": "Giữ lạnh 24h / Giữ nóng 12h", "Màu sắc": "Đen nhám viền vàng Matte Gold"}', 1, 0),
(26, 4, 'APP-05', 'Áo Tập Nam Co Giãn Thoáng Khí HieuMini Dry-Fit Tee', 'ao-tap-nam-co-gian-thoang-khi-hieumini-dry-fit-tee', 390000, 490000, 120, 4.8, 34, 'THỜI TRANG', '26_dryfit_tee.jpg', 'Sợi vải Poly-Spandex công nghệ dệt 4 chiều siêu nhẹ, thấm hút mồ hôi và kháng khuẩn khử mùi vượt trội.', 'Form áo tôn dáng cơ bắp thể thao, co giãn tối đa trong mọi chuyển động nâng tạ hay cardio cường độ cao.', '{"Chất liệu": "88% Polyester Quick-Dry + 12% Spandex", "Công nghệ": "Dry-Fit tản nhiệt lỗ thở vi điểm", "Size": "M, L, XL, XXL (Chuẩn dáng người Việt)", "Màu sắc": "Đen Obsidian / Xám Titan"}', 0, 0),

-- 5. Huấn Luyện Viên & Trị Liệu
(27, 5, 'SRV-01', 'Gói Huấn Luyện Viên 1:1 VIP Master Trainer (30 Buổi)', 'goi-huan-luyen-vien-1-1-vip-master-trainer-30-buoi', 18500000, 21000000, 20, 5.0, 41, 'ĐẲNG CẤP', '27_master_trainer.jpg', 'Chương trình huấn luyện cá nhân 1 kèm 1 cùng các Master Trainer chứng chỉ quốc tế NASM, ISSA và vận động viên thể hình.', 'Thiết kế giáo án tập luyện và chế độ dinh dưỡng cá nhân hóa 100% dựa trên thể trạng và lịch làm việc của CEO, cam kết đạt mục tiêu hình thể trong 90 ngày.', '{"Số buổi": "30 Buổi (60 phút/buổi)", "HLV": "Master Trainer chứng chỉ NASM/ISSA quốc tế", "Đo InBody": "Theo dõi hàng tuần", "Hỗ trợ": "Menu dinh dưỡng riêng từng bữa ăn"}', 1, 1),
(28, 5, 'SRV-02', 'Liệu Trình Trị Liệu Thể Thao & Giãn Cơ Phục Hồi (10 Buổi)', 'lieu-trinh-tri-lieu-the-thao-gian-co-phuc-hoi-10-buoi', 6900000, 8000000, 30, 4.9, 29, 'PHỤC HỒI', '28_sports_therapy.jpg', 'Phương pháp giải phóng màng cơ Myofascial Release, nắn chỉnh cột sống và kéo giãn chuyên sâu giúp loại bỏ đau mỏi cổ vai gáy.', 'Giải pháp hoàn hảo cho các nhà lãnh đạo thường xuyên ngồi họp và làm việc trước máy tính. Tái tạo năng lượng và tăng cường tuần hoàn máu.', '{"Liệu trình": "10 Buổi (45 phút/buổi)", "Chuyên viên": "Cử nhân Vật lý trị liệu thể thao", "Thiết bị kèm theo": "Súng massage Theragun PRO & Bốt nén khí phục hồi"}', 0, 1),
(29, 5, 'SRV-03', 'Đo Chỉ Số InBody 770 & Tư Vấn Thực Đơn CEO Dinh Dưỡng', 'do-chi-so-inbody-770-tu-van-thuc-don-ceo-dinh-duong', 990000, 1500000, 80, 5.0, 53, 'CHUYÊN SÂU', '29_inbody_analysis.jpg', 'Phân tích thành phần cơ thể 6 tần số với máy InBody 770 y khoa: tỷ lệ cơ, mỡ nội tạng, nước nội bào và chuyển hóa BMR.', 'Được tư vấn trực tiếp cùng chuyên gia dinh dưỡng thể thao để xây dựng thói quen ăn uống khoa học, tối ưu năng suất làm việc và vóc dáng.', '{"Thiết bị": "InBody 770 Medical Grade", "Báo cáo": "Bản in chi tiết 10 trang kèm đồ thị", "Tư vấn": "45 phút cùng Bác sĩ / Chuyên gia dinh dưỡng"}', 1, 0),
(30, 5, 'SRV-04', 'Gói Yoga & Thiền Định Riêng Dành Cho Doanh Nhân (20 Buổi)', 'goi-yoga-thien-dinh-rieng-danh-cho-doanh-nhan-20-buoi', 13900000, 16000000, 25, 4.9, 23, 'TÂM TRÍ', '30_executive_yoga.jpg', 'Lớp Yoga & Thiền 1:1 trong không gian VIP yên tĩnh, giúp tái cân bằng cảm xúc, nâng cao khả năng tập trung và giảm stress đỉnh cao.', 'Huấn luyện viên Master Yoga Ấn Độ trực tiếp hướng dẫn kỹ thuật thở Pranayama, các tư thế Asana trị liệu và thiền tĩnh tâm sâu.', '{"Quy mô": "1 Kèm 1 trong phòng thiền riêng biệt", "Thời lượng": "20 Buổi (75 phút/buổi)", "Trang bị": "Thảm Manduka PRO, Tinh dầu thảo mộc hữu cơ"}', 0, 0);

-- Đánh giá mẫu tiêu biểu
INSERT INTO `hieumini_gym_db`.`reviews` (`product_id`, `user_name`, `user_role`, `rating`, `comment`) VALUES
(1, 'Doanh nhân Trần Đình Tuấn', 'Chủ tịch Tập đoàn Tuấn Phát', 5, 'Dịch vụ tại HieuMini thực sự xứng tầm CEO. Không gian tập riêng tư, sạch sẽ và máy móc cực kỳ hiện đại. Rất hài lòng với gói Diamond Elite!'),
(6, 'Anh Hoàng Mạnh Thắng', 'CEO TechVenture', 5, 'Máy chạy bộ Commercial X9 Pro chạy siêu êm, màn hình lớn kết nối chạy thực tế ảo như đang chạy ngoài phố Paris. Xứng đáng 5 sao!'),
(14, 'Nguyễn Tiến Dũng', 'Hội viên VIP HieuMini', 5, 'Whey Hydrolyzed vị socola rất thơm ngon, uống không bị ngọt gắt hay nổi mụn. Cơ bắp phục hồi nhanh rõ rệt.'),
(27, 'Lê Thị Thu Hương', 'Giám đốc Điều hành FinCorp', 5, 'HLV Master Trainer rất chuyên nghiệp và thấu hiểu lịch trình bận rộn của tôi. Đã giảm 6kg mỡ thừa sau 2 tháng tập luyện!');

-- Đặt lịch mẫu
INSERT INTO `hieumini_gym_db`.`bookings` (`full_name`, `phone`, `email`, `service_type`, `branch`, `booking_date`, `booking_time`, `fitness_goal`, `notes`, `status`) VALUES
('Phạm Quang Minh', '0987654321', 'minh.pham@enterprise.vn', 'Gói Hội Viên CEO Diamond Elite', 'HieuMini Luxury Diamond - Quận 1, TP.HCM', CURDATE() + INTERVAL 2 DAY, '08:30 - 10:30', 'Rèn luyện thể lực lãnh đạo & Tăng cơ giảm mỡ', 'Cần tư vấn riêng với HLV trưởng', 'confirmed'),
('Vũ Bích Ngọc', '0909112233', 'ngoc.vu@designstudio.com', 'Gói Yoga & Thiền Định Doanh Nhân', 'HieuMini Luxury Landmark - Bình Thạnh, TP.HCM', CURDATE() + INTERVAL 3 DAY, '17:30 - 19:00', 'Giảm căng thẳng sau giờ làm việc', 'Chuẩn bị thảm tập riêng', 'pending');

-- Tài khoản quản trị demo dùng chung
UPDATE `hieumini_gym_db`.`users` SET `email`='admin@hieumini.vn', `password`='$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role`='admin';

SET FOREIGN_KEY_CHECKS = 1;
