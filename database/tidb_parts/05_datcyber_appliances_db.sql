-- ======================================================================
--  Cơ sở dữ liệu: datcyber_appliances_db
--  Dán CẢ tệp vào TiDB SQL Editor rồi bấm Run.
--  Không cần chọn database — mọi bảng đã gắn sẵn tên CSDL.
-- ======================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


CREATE DATABASE IF NOT EXISTS `datcyber_appliances_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if exists
DROP TABLE IF EXISTS `datcyber_appliances_db`.`order_items`;
DROP TABLE IF EXISTS `datcyber_appliances_db`.`orders`;
DROP TABLE IF EXISTS `datcyber_appliances_db`.`reviews`;
DROP TABLE IF EXISTS `datcyber_appliances_db`.`coupons`;
DROP TABLE IF EXISTS `datcyber_appliances_db`.`products`;
DROP TABLE IF EXISTS `datcyber_appliances_db`.`categories`;
DROP TABLE IF EXISTS `datcyber_appliances_db`.`users`;

-- 1. Categories
CREATE TABLE `datcyber_appliances_db`.`categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `icon` VARCHAR(50) DEFAULT 'bi-grid',
  `description` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Products
CREATE TABLE `datcyber_appliances_db`.`products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `price` DECIMAL(12,0) NOT NULL,
  `old_price` DECIMAL(12,0) DEFAULT NULL,
  `image` VARCHAR(255) NOT NULL,
  `short_description` TEXT,
  `description` LONGTEXT,
  `specs` TEXT,
  `stock` INT NOT NULL DEFAULT 50,
  `rating` DECIMAL(2,1) DEFAULT 5.0,
  `review_count` INT DEFAULT 0,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_best_seller` TINYINT(1) DEFAULT 0,
  `is_flash_sale` TINYINT(1) DEFAULT 0,
  `discount_percent` INT DEFAULT 0,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `datcyber_appliances_db`.`categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Users
CREATE TABLE `datcyber_appliances_db`.`users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `role` ENUM('admin', 'customer') DEFAULT 'customer',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Orders
CREATE TABLE `datcyber_appliances_db`.`orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_code` VARCHAR(50) NOT NULL UNIQUE,
  `user_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(150) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(30) NOT NULL,
  `customer_address` TEXT NOT NULL,
  `customer_note` TEXT DEFAULT NULL,
  `payment_method` VARCHAR(50) DEFAULT 'cod',
  `total_amount` DECIMAL(12,0) NOT NULL,
  `discount_amount` DECIMAL(12,0) DEFAULT 0,
  `shipping_fee` DECIMAL(12,0) DEFAULT 0,
  `final_amount` DECIMAL(12,0) NOT NULL,
  `status` ENUM('pending', 'processing', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `datcyber_appliances_db`.`users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Order Items
CREATE TABLE `datcyber_appliances_db`.`order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT DEFAULT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_image` VARCHAR(255) DEFAULT NULL,
  `price` DECIMAL(12,0) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(12,0) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `datcyber_appliances_db`.`orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `datcyber_appliances_db`.`products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Reviews
CREATE TABLE `datcyber_appliances_db`.`reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_name` VARCHAR(100) NOT NULL,
  `rating` INT NOT NULL DEFAULT 5,
  `comment` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `datcyber_appliances_db`.`products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Coupons
CREATE TABLE `datcyber_appliances_db`.`coupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) NOT NULL UNIQUE,
  `discount_type` ENUM('percent', 'fixed') DEFAULT 'percent',
  `discount_value` DECIMAL(10,0) NOT NULL,
  `min_order` DECIMAL(12,0) DEFAULT 0,
  `expiry_date` DATE DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Customer Contacts / Feedback
CREATE TABLE IF NOT EXISTS `datcyber_appliances_db`.`contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) DEFAULT NULL,
  `phone` VARCHAR(30) NOT NULL,
  `subject` VARCHAR(150) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEED DATA

-- Categories
INSERT INTO `datcyber_appliances_db`.`categories` (`id`, `name`, `slug`, `icon`, `description`) VALUES
(1, 'Thiết Bị Nhà Bếp', 'thiet-bi-nha-bep', 'fa-utensils', 'Nồi chiên, máy xay, nồi cơm điện cao tần, bếp từ, lò vi sóng cao cấp'),
(2, 'Robot & Dọn Dẹp', 'robot-don-dep', 'fa-robot', 'Robot hút bụi lau nhà, máy hút bụi cầm tay, máy rửa chén thông minh'),
(3, 'Lọc Khí & Môi Trường', 'loc-khi-moi-truong', 'fa-wind', 'Máy lọc không khí HEPA, máy tạo ẩm, quạt tháp không cánh'),
(4, 'Chăm Sóc & Tiện Ích', 'cham-soc-tien-ich', 'fa-tshirt', 'Bàn là hơi nước đứng, máy sấy quần áo, ấm siêu tốc thông minh'),
(5, 'Máy Pha Chế & Cà Phê', 'may-pha-che-ca-phe', 'fa-coffee', 'Máy pha cà phê Espresso, máy ép chậm trái cây, máy làm sữa hạt');

-- Products
INSERT INTO `datcyber_appliances_db`.`products` (`id`, `category_id`, `name`, `slug`, `price`, `old_price`, `image`, `short_description`, `description`, `specs`, `stock`, `rating`, `review_count`, `is_featured`, `is_best_seller`, `is_flash_sale`, `discount_percent`) VALUES
(1, 1, 'Nồi Chiên Không Dầu Điện Tử DatCyber CrispyPro 6.5L', 'noi-chien-khong-dau-datcyber-crispypro', 2490000, 3290000, 'air_fryer.jpg', 'Công nghệ Rapid Air đối lưu 360 độ, màn hình cảm ứng OLED sang trọng, giảm 90% dầu mỡ.', '<p>Nồi chiên không dầu DatCyber CrispyPro thế hệ mới 2026 sở hữu dung tích lớn 6.5L, phù hợp cho gia đình 4-6 người. Mặt kính cường lực hiển thị thông số nấu trực quan với 12 chế độ cài đặt sẵn từ nướng gà nguyên con, khoai tây chiên, làm bánh đến sấy hoa quả.</p><p>Lớp chống dính Ceramic cao cấp không chứa PFOA, dễ dàng vệ sinh bằng máy rửa chén. Tích hợp cảm biến nhiệt NTC chính xác giúp thực phẩm chín đều vàng giòn bên ngoài, mọng nước bên trong.</p>', 'Dung tích: 6.5 Lít\nCông suất: 1800W\nNhiệt độ: 40°C - 200°C\nHẹn giờ: Lên đến 60 phút\nChất liệu lòng nồi: Hợp kim nhôm phủ Ceramic cao cấp\nTrọng lượng: 5.2 kg', 45, 4.9, 128, 1, 1, 1, 24),

(2, 2, 'Robot Hút Bụi Lau Nhà Tự Động DatCyber OmniClean X9', 'robot-hut-bui-datcyber-omniclean-x9', 8990000, 11990000, 'robot_vacuum.jpg', 'Hệ thống tự giặt giẻ sấy khô khí nóng, tự gom rác 60 ngày, định vị LiDAR 3D chính xác.', '<p>Robot hút bụi DatCyber OmniClean X9 là đỉnh cao công nghệ dọn dẹp nhà cửa thông minh. Trạm sạc đa năng tự động giặt giẻ lau bằng nước nóng và sấy khô bằng khí nóng 45°C diệt khuẩn 99.9%.</p><p>Lực hút siêu mạnh 6000Pa hút sạch mọi bụi mịn và lông thú cưng. Công nghệ cảm biến laser LiDAR thế hệ 4 quét bản đồ 3D ngôi nhà trong vài giây và thiết lập tường ảo tránh va chạm tối đa.</p>', 'Lực hút: 6000 Pa\nDung lượng pin: 5200 mAh (hoạt động 180 phút)\nTrạm sạc: Tự động gom rác + Giặt giẻ sấy nóng\nĐộ ồn: < 65dB\nKết nối: Wi-Fi App DatCyber Smart (iOS/Android)\nTrọng lượng trạm + robot: 11.5 kg', 28, 5.0, 96, 1, 1, 1, 25),

(3, 5, 'Máy Ép Chậm Trục Vít Đảo Chiều DatCyber PureJuice Pro', 'may-ep-cham-datcyber-purejuice-pro', 1850000, 2450000, 'slow_juicer.jpg', 'Tốc độ ép chậm 45 vòng/phút giữ trọn 98% vitamin, miệng ép cực lớn 82mm ép nguyên quả.', '<p>Máy ép chậm DatCyber PureJuice Pro sử dụng công nghệ ép trục vít xoắn ốc cải tiến, giảm thiểu tối đa sự oxy hóa và phân tầng nước ép. Ống tiếp nguyên liệu đường kính rộng 82mm giúp bạn bỏ nguyên quả táo hoặc cam mà không cần cắt nhỏ.</p><p>Động cơ DC lõi đồng nguyên chất vận hành siêu êm ái, bã khô kiệt kiệt nước, cho lượng nước ép nhiều gấp 1.5 lần so với máy ép ly tâm thông thường.</p>', 'Công suất: 250W\nTốc độ quay: 45 RPM\nĐường kính ống ép: 82 mm\nDung tích cối: 500 ml\nChất liệu: Nhựa Tritan an toàn không chứa BPA\nBảo hành: 24 tháng chính hãng', 60, 4.8, 84, 1, 0, 0, 24),

(4, 3, 'Máy Lọc Không Khí Thông Minh DatCyber AirShield Ultra', 'may-loc-khong-khi-datcyber-airshield-ultra', 3490000, 4500000, 'air_purifier.jpg', 'Màng lọc True HEPA H13 khử 99.97% bụi PM2.5, khử mùi than hoạt tính, màn hình OLED sắc nét.', '<p>DatCyber AirShield Ultra là giải pháp bảo vệ sức khỏe hệ hô hấp cho không gian sống hiện đại lên tới 60m². Màng lọc đa tầng kết hợp lớp tiền lọc, màng lọc True HEPA H13 chuẩn y tế và lớp than hoạt tính gáo dừa hấp thụ formaldehyde và mùi khó chịu.</p><p>Cảm biến laser đo chất lượng không khí PM2.5 theo thời gian thực và tự động điều chỉnh tốc độ gió phù hợp. Chế độ ngủ siêu tĩnh lặng chỉ 24dB cho giấc ngủ trọn vẹn.</p>', 'Diện tích sử dụng: 35 - 60 m²\nTốc độ phân phối khí sạch (CADR): 480 m³/h\nBộ lọc: Màng 4 lớp True HEPA H13 + Than hoạt tính\nĐộ ồn: 24 - 58 dB\nTính năng: Đèn báo AQI, Khóa trẻ em, Hẹn giờ\nKích thước: 260 x 260 x 590 mm', 35, 4.9, 112, 1, 1, 1, 22),

(5, 4, 'Ấm Siêu Tốc Giữ Nhiệt Thông Minh DatCyber ThermoSense 1.7L', 'am-sieu-toc-datcyber-thermosense', 890000, 1190000, 'electric_kettle.jpg', 'Thân bình thủy tinh Borosilicate chịu sốc nhiệt, điều chỉnh nhiệt độ chính xác 40°C - 100°C.', '<p>Ấm siêu tốc DatCyber ThermoSense mang phong cách hiện đại với thân thủy tinh cao cấp và đèn LED xanh dương khi đun nước. Màn hình LED kỹ thuật số trên tay cầm hiển thị chính xác nhiệt độ nước hiện tại.</p><p>Tính năng giữ nhiệt thông minh lên đến 12 giờ ở các mức nhiệt lý tưởng để pha sữa em bé (45°C), pha trà xanh (80°C), pha cà phê (90°C) và đun sôi (100°C).</p>', 'Dung tích: 1.7 Lít\nCông suất: 2200W (Đun sôi trong 4 phút)\nChất liệu: Thủy tinh Borosilicate + Inox 304 không gỉ\nGiữ nhiệt: 12 giờ liên tục\nBộ điều nhiệt: Strix cao cấp từ Anh Quốc', 75, 4.7, 65, 0, 0, 1, 25),

(6, 1, 'Máy Xay Sinh Tố & Nấu Sữa Hạt DatCyber Blender Master', 'may-xay-sinh-to-datcyber-blender-master', 1690000, 2200000, 'smart_blender.jpg', 'Động cơ siêu tốc 1200W, cối thủy tinh 6 lưỡi dao thép Nhật Bản, xay nhuyễn mịn đá và thực phẩm.', '<p>Máy xay sinh tố đa năng DatCyber Blender Master tích hợp 8 chương trình tự động xay sinh tố, nghiền đá, làm kem tuyết, xay thịt và nấu sữa đậu nành nóng. Cối thủy tinh chịu nhiệt dày 8mm an toàn tuyệt đối cho sức khỏe gia đình.</p><p>Hệ thống 6 lưỡi dao sắc bén răng cưa 3 tầng bằng thép không gỉ SUS 301 công nghệ Nhật Bản cắt ngọt mọi loại hạt cứng mà không cần lọc bã.</p>', 'Công suất: 1200W\nDung tích cối lớn: 1.75 Lít\nTốc độ: 28.000 vòng/phút (10 mức tùy chỉnh)\nLưỡi dao: Thép không gỉ 6 cánh 3D\nTính năng: Tự làm sạch thông minh 1 chạm', 40, 4.8, 53, 1, 0, 0, 23),

(7, 5, 'Máy Pha Cà Phê Bán Tự Động DatCyber Barista Touch 20Bar', 'may-pha-ca-phe-datcyber-barista-touch', 3890000, 4990000, 'coffee_machine.jpg', 'Bơm áp suất 20 Bar chuẩn Ý, vòi đánh bọt sữa Microfoam mịn mượt, đồng hồ đo áp lực cổ điển.', '<p>Thưởng thức ly cà phê Espresso, Cappuccino hay Latte chuẩn hương vị quán ngay tại nhà với máy pha cà phê DatCyber Barista Touch. Hệ thống làm nóng ThermoBlock gia nhiệt nhanh chỉ trong 30 giây.</p><p>Thân máy hoàn thiện từ inox bóng sang trọng, vòi tạo bọt sữa chuyên nghiệp cho bạn tự do tạo hình nghệ thuật Latte Art tuyệt đẹp.</p>', 'Áp suất bơm: 20 Bar (Bơm Ý Ulka)\nCông suất: 1350W\nDung tích bình nước: 1.5 Lít tháo rời\nChức năng: Pha Single/Double Espresso, Đánh sữa Cappuccino\nPhụ kiện kèm: Tay cầm 51mm, Tamper nén kim loại', 20, 5.0, 78, 1, 1, 1, 22),

(8, 1, 'Lò Vi Sóng Nướng Đối Lưu Điện Tử DatCyber ChefWave 25L', 'lo-vi-song-datcyber-chefwave-25l', 2990000, 3790000, 'microwave_oven.jpg', 'Tích hợp nướng đối lưu kết hợp vi sóng, khoang lò tráng men kháng khuẩn, 10 thực đơn nấu tự động.', '<p>DatCyber ChefWave là sự kết hợp hoàn hảo giữa lò vi sóng rã đông nhanh và lò nướng nhiệt đối lưu vàng đều. Cửa kính gương đen bóng bẩy cùng tay cầm kim loại tinh tế làm nổi bật không gian bếp hiện đại.</p><p>Công nghệ Inverter tiết kiệm điện năng và phân bổ nhiệt đồng đều, giữ trọn độ tươi ngon và dưỡng chất của thực phẩm.</p>', 'Dung tích: 25 Lít\nCông suất vi sóng: 900W / Công suất nướng: 1200W\nCông nghệ: Inverter tiết kiệm 30% điện năng\nChế độ nấu: 10 chế độ nấu tự động\nKích thước: 485 x 293 x 380 mm', 25, 4.8, 42, 0, 0, 0, 21),

(9, 1, 'Nồi Cơm Điện Cao Tần IH DatCyber RiceChef Master 1.5L', 'noi-com-dien-cao-tan-datcyber-ricechef', 2150000, 2750000, 'smart_rice_cooker.jpg', 'Công nghệ cảm ứng từ IH nhiệt bao quanh lòng nồi, ruột gang dày 3mm phủ men chống dính gốm.', '<p>Nồi cơm điện cao tần IH DatCyber RiceChef sử dụng sóng từ trường đun nóng trực tiếp không qua mâm nhiệt, giúp hạt gạo chín đều từ trong lõi, giữ trọn vị ngọt tự nhiên và không bị nát hay khô.</p><p>Bảng điều khiển cảm ứng chìm sang trọng với các chế độ nấu cơm niêu, nấu gạo lứt dẻo, nấu cháo dinh dưỡng và hầm canh.</p>', 'Dung tích: 1.5 Lít (4 - 6 người ăn)\nCông nghệ: Cảm ứng từ IH 360° đa chiều\nCông suất: 1300W\nLòng nồi: Gang cầu 8 lớp dày 3mm tráng gốm Binchotan Nhật Bản\nHẹn giờ nấu: 24 tiếng', 50, 4.9, 91, 1, 1, 0, 22),

(10, 4, 'Bàn Là Hơi Nước Đứng Cầm Tay DatCyber SteamGlide Ultra', 'ban-la-hoi-nuoc-datcyber-steamglide', 750000, 990000, 'garment_steamer.jpg', 'Hơi nước cực mạnh 30g/phút khử phẳng nếp nhăn trong 15 giây, mặt đế Ceramic chống cháy xước.', '<p>Bàn là hơi nước cầm tay DatCyber SteamGlide Ultra nhỏ gọn, tiện lợi mang theo khi đi công tác hoặc du lịch. Đầu phun gốm Ceramic dẫn nhiệt nhanh kết hợp luồng hơi nước áp suất cao tiêu diệt 99% vi khuẩn và mùi ẩm mốc trên quần áo.</p><p>Bình chứa nước 280ml có thể tháo rời, tự ngắt điện an toàn khi quá nhiệt hoặc cạn nước.</p>', 'Công suất: 1500W\nLưu lượng hơi: 30g / phút\nDung tích bình nước: 280 ml\nThời gian khởi động: 15 giây\nTrọng lượng máy: 850g', 80, 4.7, 69, 0, 0, 1, 24),

(11, 2, 'Máy Rửa Bát Để Bàn Thông Minh DatCyber TableClean Pro', 'may-rua-bat-de-ban-datcyber-tableclean-pro', 6490000, 8200000, 'countertop_dishwasher.jpg', 'Rửa nước nóng 75°C diệt khuẩn, sấy khô khí tươi PTC, sức chứa 6 bộ bát đĩa châu Á tiện dụng.', '<p>Máy rửa bát để bàn DatCyber TableClean Pro thiết kế cửa kính trong suốt sang trọng, dễ dàng quan sát quá trình rửa. Hệ thống tay phun kép 360 độ áp lực nước xoáy cực mạnh làm sạch bóng dầu mỡ cứng đầu.</p><p>Tiết kiệm nước vượt trội chỉ 5.5 lít nước cho một chu trình rửa, ít hơn 70% so với rửa thủ công bằng tay.</p>', 'Sức chứa: 6-8 bộ đồ ăn chuẩn\nLượng nước tiêu thụ: 5.5 Lít / chu trình\nNhiệt độ rửa tối đa: 75°C\nChương trình rửa: 6 chế độ (Rửa nhanh, Rửa sâu, Rửa tiết kiệm, Rửa hoa quả...)\nKích thước: 550 x 500 x 450 mm', 18, 4.9, 37, 1, 0, 0, 21);

-- Admin & Demo Users
-- Passwords are encrypted with bcrypt or demo md5/hash. Let's use standard password_hash '123456'
-- '$2y$10$eAmsF.4qU9o7k5F.45B7f.cGyL8tS9M24e4jLpY2K0PzGgTkmrP1W' is password_hash('123456', PASSWORD_BCRYPT)
INSERT INTO `datcyber_appliances_db`.`users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`) VALUES
(1, 'DatCyber Admin', 'admin@datcyber.vn', '$2y$10$wTfkD72cQyU62zrqo2hQgepG187r7p/zH2tLd5vW9kMhJbC8sB3lK', '0988889999', 'Tòa nhà DatCyber Tower, Cầu Giấy, Hà Nội', 'admin'),
(2, 'Nguyễn Văn An', 'khachhang@gmail.com', '$2y$10$wTfkD72cQyU62zrqo2hQgepG187r7p/zH2tLd5vW9kMhJbC8sB3lK', '0912345678', 'Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội', 'customer');

-- Demo Coupons
INSERT INTO `datcyber_appliances_db`.`coupons` (`code`, `discount_type`, `discount_value`, `min_order`, `expiry_date`, `is_active`) VALUES
('DATCYBER10', 'percent', 10, 1000000, '2027-12-31', 1),
('FREESHIP', 'fixed', 50000, 500000, '2027-12-31', 1),
('GIADUNGVIP', 'percent', 15, 3000000, '2027-12-31', 1);

-- Demo Reviews
INSERT INTO `datcyber_appliances_db`.`reviews` (`product_id`, `user_name`, `rating`, `comment`, `created_at`) VALUES
(1, 'Trần Minh Quang', 5, 'Nồi chiên DatCyber dùng rất thích, nướng đùi gà da giòn rụm bên trong vẫn ngọt thịt. Mặt kính nhìn được đồ ăn rất tiện!', '2026-08-15 10:30:00'),
(1, 'Lê Thị Thu Thủy', 5, 'Giao hàng nhanh 2 ngày là nhận được, nồi đẹp xịn xò sang góc bếp hẳn.', '2026-08-16 14:20:00'),
(2, 'Hoàng Nhật Minh', 5, 'Robot hút bụi OmniClean X9 lau siêu sạch, tự động giặt giẻ sấy khô nên không bị hôi chút nào. 10/10 điểm đáng tiền.', '2026-08-18 09:15:00'),
(4, 'Phạm Phương Linh', 5, 'Phòng mình 40m2 bật 15 phút là không khí trong lành hẳn, máy chạy êm ru ngủ ngon giấc.', '2026-08-19 21:00:00');

-- Demo Sample Orders
INSERT INTO `datcyber_appliances_db`.`orders` (`id`, `order_code`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `customer_note`, `payment_method`, `total_amount`, `discount_amount`, `shipping_fee`, `final_amount`, `status`, `created_at`) VALUES
(1, 'DC-20260819-01', 2, 'Nguyễn Văn An', 'khachhang@gmail.com', '0912345678', 'Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội', 'Giao hàng giờ hành chính giúp tôi', 'cod', 2490000, 0, 0, 2490000, 'completed', '2026-08-19 08:30:00'),
(2, 'DC-20260820-02', 2, 'Nguyễn Văn An', 'khachhang@gmail.com', '0912345678', 'Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội', 'Gọi trước khi giao 15p', 'banking', 8990000, 899000, 0, 8091000, 'shipping', '2026-08-20 10:15:00');

INSERT INTO `datcyber_appliances_db`.`order_items` (`order_id`, `product_id`, `product_name`, `product_image`, `price`, `quantity`, `subtotal`) VALUES
(1, 1, 'Nồi Chiên Không Dầu Điện Tử DatCyber CrispyPro 6.5L', 'air_fryer.jpg', 2490000, 1, 2490000),
(2, 2, 'Robot Hút Bụi Lau Nhà Tự Động DatCyber OmniClean X9', 'robot_vacuum.jpg', 8990000, 1, 8990000);

-- Tài khoản quản trị demo dùng chung
UPDATE `datcyber_appliances_db`.`users` SET `email`='admin@hieumini.vn', `password`='$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role`='admin';

SET FOREIGN_KEY_CHECKS = 1;
