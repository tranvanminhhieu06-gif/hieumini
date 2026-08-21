-- ======================================================================
--  Cơ sở dữ liệu: hieumini_bookstore_db
--  Dán CẢ tệp vào TiDB SQL Editor rồi bấm Run.
--  Không cần chọn database — mọi bảng đã gắn sẵn tên CSDL.
-- ======================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


-- ==========================================================
-- CƠ SỞ DỮ LIỆU WEBSITE THƯƠNG MẠI ĐIỆN TỬ HIEUMINI (TECH STORE)
-- Hệ quản trị CSDL: MySQL / MariaDB (InnoDB Engine, UTF-8 MB4)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `hieumini_bookstore_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Bảng `categories` (Danh mục sản phẩm)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_bookstore_db`.`categories`;
CREATE TABLE `hieumini_bookstore_db`.`categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(120) NOT NULL UNIQUE,
    `icon` VARCHAR(50) DEFAULT 'fa-laptop',
    `description` TEXT NULL,
    `status` TINYINT(1) DEFAULT 1, -- 1: Hiện, 0: Ẩn
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 2. Bảng `users` (Người dùng & Quản trị viên)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_bookstore_db`.`users`;
CREATE TABLE `hieumini_bookstore_db`.`users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) NULL,
    `address` VARCHAR(255) NULL,
    `role` ENUM('admin', 'customer') DEFAULT 'customer',
    `avatar` VARCHAR(255) DEFAULT 'default_avatar.png',
    `status` TINYINT(1) DEFAULT 1, -- 1: Hoạt động, 0: Khóa
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 3. Bảng `products` (Sản phẩm công nghệ)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_bookstore_db`.`products`;
CREATE TABLE `hieumini_bookstore_db`.`products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(220) NOT NULL UNIQUE,
    `brand` VARCHAR(80) NOT NULL,
    `price` DECIMAL(12, 2) NOT NULL,
    `sale_price` DECIMAL(12, 2) NULL,
    `stock_quantity` INT DEFAULT 10,
    `thumbnail` VARCHAR(255) NOT NULL,
    `images` TEXT NULL, -- JSON array các ảnh chi tiết
    `short_desc` VARCHAR(300) NULL,
    `description` LONGTEXT NULL,
    `specifications` LONGTEXT NULL, -- JSON hoặc HTML thông số kỹ thuật (RAM, CPU, Pin...)
    `is_featured` TINYINT(1) DEFAULT 0,
    `is_flash_sale` TINYINT(1) DEFAULT 0,
    `views` INT DEFAULT 0,
    `rating` DECIMAL(2, 1) DEFAULT 5.0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_products_categories` FOREIGN KEY (`category_id`) REFERENCES `hieumini_bookstore_db`.`categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Bảng `coupons` (Mã giảm giá / Khuyến mãi)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_bookstore_db`.`coupons`;
CREATE TABLE `hieumini_bookstore_db`.`coupons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `discount_percent` INT DEFAULT 0,
    `discount_amount` DECIMAL(12, 2) DEFAULT 0.00,
    `min_order_amount` DECIMAL(12, 2) DEFAULT 0.00,
    `expires_at` DATE NULL,
    `status` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 5. Bảng `orders` (Đơn hàng)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_bookstore_db`.`orders`;
CREATE TABLE `hieumini_bookstore_db`.`orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_code` VARCHAR(30) NOT NULL UNIQUE,
    `user_id` INT NULL,
    `customer_name` VARCHAR(100) NOT NULL,
    `customer_email` VARCHAR(100) NOT NULL,
    `customer_phone` VARCHAR(20) NOT NULL,
    `customer_address` VARCHAR(255) NOT NULL,
    `shipping_city` VARCHAR(100) DEFAULT 'Hà Nội',
    `payment_method` ENUM('cod', 'bank_transfer', 'momo') DEFAULT 'cod',
    `payment_status` ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    `shipping_status` ENUM('pending', 'processing', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
    `subtotal` DECIMAL(12, 2) NOT NULL,
    `discount` DECIMAL(12, 2) DEFAULT 0.00,
    `shipping_fee` DECIMAL(12, 2) DEFAULT 30000.00,
    `total_amount` DECIMAL(12, 2) NOT NULL,
    `note` TEXT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `hieumini_bookstore_db`.`users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Bảng `order_items` (Chi tiết các mặt hàng trong đơn)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_bookstore_db`.`order_items`;
CREATE TABLE `hieumini_bookstore_db`.`order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NULL,
    `product_name` VARCHAR(200) NOT NULL,
    `price` DECIMAL(12, 2) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `total` DECIMAL(12, 2) NOT NULL,
    CONSTRAINT `fk_order_items_orders` FOREIGN KEY (`order_id`) REFERENCES `hieumini_bookstore_db`.`orders`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_order_items_products` FOREIGN KEY (`product_id`) REFERENCES `hieumini_bookstore_db`.`products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. Bảng `reviews` (Đánh giá & Bình luận sản phẩm)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_bookstore_db`.`reviews`;
CREATE TABLE `hieumini_bookstore_db`.`reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `user_id` INT NULL,
    `user_name` VARCHAR(100) NOT NULL,
    `rating` INT NOT NULL DEFAULT 5,
    `comment` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reviews_products` FOREIGN KEY (`product_id`) REFERENCES `hieumini_bookstore_db`.`products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. Bảng `banners` (Banner Slider trang chủ)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_bookstore_db`.`banners`;
CREATE TABLE `hieumini_bookstore_db`.`banners` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `title` VARCHAR(150) NOT NULL,
    `subtitle` VARCHAR(255) NULL,
    `image` VARCHAR(255) NOT NULL,
    `link` VARCHAR(255) DEFAULT 'products.php',
    `button_text` VARCHAR(50) DEFAULT 'Khám phá ngay',
    `sort_order` INT DEFAULT 0,
    `status` TINYINT(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- ==========================================================
-- DỮ LIỆU MẪU (SEED DATA CHO HỆ THỐNG HIEUMINI)
-- ==========================================================

-- Thêm Danh mục sản phẩm
INSERT INTO `hieumini_bookstore_db`.`categories` (`id`, `name`, `slug`, `icon`, `description`, `status`) VALUES
(1, 'Điện thoại Smartphone', 'dien-thoai', 'fa-mobile-screen-button', 'Các dòng điện thoại iPhone, Samsung, Xiaomi, ROG Phone đỉnh cao công nghệ', 1),
(2, 'Laptop & Macbook', 'laptop-macbook', 'fa-laptop', 'Laptop gaming, ultrabook đồ họa, văn phòng mỏng nhẹ hiệu năng mạnh', 1),
(3, 'Máy tính bảng (Tablet)', 'tablet', 'fa-tablet-screen-button', 'iPad Pro, Samsung Galaxy Tab phục vụ học tập, làm việc sáng tạo', 1),
(4, 'Đồng hồ thông minh', 'smartwatch', 'fa-clock', 'Apple Watch, Garmin, Galaxy Watch theo dõi sức khỏe và phong cách', 1),
(5, 'Tai nghe & Âm thanh', 'tai-nghe-am-thanh', 'fa-headphones', 'Tai nghe chống ồn AirPods Pro, Sony WH-1000XM5, loa bluetooth Marshall', 1),
(6, 'Phụ kiện công nghệ', 'phu-kien', 'fa-keyboard', 'Bàn phím cơ, chuột gaming, củ sạc nhanh GaN, pin dự phòng dung lượng cao', 1);

-- Thêm Tài khoản người dùng (Mật khẩu mặc định: admin123 và user123 mã hóa BCRYPT)
-- Password 'admin123': $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (hoặc sinh hash chuẩn)
INSERT INTO `hieumini_bookstore_db`.`users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `role`, `status`) VALUES
(1, 'Trần Văn Minh Hiếu (Admin)', 'admin@hieumini.vn', '$2y$10$p0bHk9h6K8XGgPms4pYpNuA4h7h5j6H1I3qU1lR4k0W3q5J9L7rWy', '0988889999', 'Tòa nhà HieuMini Tech Tower, Cầu Giấy, Hà Nội', 'admin', 1),
(2, 'Nguyễn Hoàng Nam', 'nam.nguyen@gmail.com', '$2y$10$p0bHk9h6K8XGgPms4pYpNuA4h7h5j6H1I3qU1lR4k0W3q5J9L7rWy', '0912345678', '120 Phố Huế, Hai Bà Trưng, Hà Nội', 'customer', 1),
(3, 'Lê Thị Thu Thảo', 'thao.le@gmail.com', '$2y$10$p0bHk9h6K8XGgPms4pYpNuA4h7h5j6H1I3qU1lR4k0W3q5J9L7rWy', '0934567890', '45 Lê Duẩn, Quận 1, TP. Hồ Chí Minh', 'customer', 1);

-- Thêm Mã giảm giá
INSERT INTO `hieumini_bookstore_db`.`coupons` (`code`, `discount_percent`, `discount_amount`, `min_order_amount`, `expires_at`, `status`) VALUES
('HIEUMINI2026', 10, 0.00, 5000000.00, '2026-12-31', 1),
('TECHNEW', 5, 0.00, 1000000.00, '2026-12-31', 1),
('GIAM500K', 0, 500000.00, 10000000.00, '2026-12-31', 1);

-- Thêm Sản phẩm công nghệ tiêu biểu
INSERT INTO `hieumini_bookstore_db`.`products` (`id`, `category_id`, `name`, `slug`, `brand`, `price`, `sale_price`, `stock_quantity`, `thumbnail`, `images`, `short_desc`, `description`, `specifications`, `is_featured`, `is_flash_sale`, `views`, `rating`) VALUES
(1, 1, 'iPhone 16 Pro Max 256GB Titan Sa Mạc', 'iphone-16-pro-max-256gb', 'Apple', 34990000.00, 31990000.00, 25, 'iphone16promax.png', '["iphone16_1.png", "iphone16_2.png"]', 'Chip A18 Pro 3nm cực mạnh, Camera nút điều khiển Camera Control mới, viền titan siêu mỏng nhẹ.', 'iPhone 16 Pro Max mang đến bước đột phá công nghệ với chip A18 Pro, màn hình Super Retina XDR 6.9 inch cùng hệ thống camera Pro 48MP zoom quang học 5x. Thời lượng pin kỷ lục cùng thiết kế viền titan cấp 5 đẳng cấp hàng đầu thế giới.', '{"Màn hình": "6.9 inch Super Retina XDR OLED 120Hz ProMotion", "Chip CPU": "Apple A18 Pro 6 nhân", "RAM": "8 GB", "Bộ nhớ trong": "256 GB", "Camera sau": "Chính 48MP + Góc siêu rộng 48MP + Tele 12MP 5x", "Pin & Sạc": "4.685 mAh, Sạc nhanh 30W, MagSafe 25W"}', 1, 1, 1540, 5.0),

(2, 1, 'Samsung Galaxy S24 Ultra 5G 12GB/256GB', 'samsung-galaxy-s24-ultra', 'Samsung', 31990000.00, 26990000.00, 18, 's24ultra.png', '["s24_1.png", "s24_2.png"]', 'Quyền năng Galaxy AI, Khung viền Titan phẳng, Bút S-Pen tiện ích, Camera mắt thần bóng đêm 200MP.', 'Galaxy S24 Ultra mở ra kỷ nguyên mới của trí tuệ nhân tạo Galaxy AI: Khoanh tròn để tìm kiếm, Phiên dịch trực tiếp cuộc gọi, Trợ lý ghi chú thông minh. Màn hình Dynamic AMOLED 2X sáng 2600 nits chống chói độc quyền.', '{"Màn hình": "6.8 inch Dynamic AMOLED 2X QHD+ 120Hz", "Chip CPU": "Snapdragon 8 Gen 3 for Galaxy", "RAM": "12 GB", "Bộ nhớ trong": "256 GB", "Camera sau": "200MP + 50MP 5x + 12MP + 10MP 3x", "Pin & Sạc": "5000 mAh, Sạc nhanh 45W"}', 1, 1, 1280, 4.9),

(3, 2, 'MacBook Pro 14 M3 Pro (18GB/512GB SSD)', 'macbook-pro-14-m3-pro', 'Apple', 49990000.00, 45490000.00, 12, 'macbookpro14.png', '["mbp14_1.png"]', 'Chip Apple M3 Pro kiến trúc GPU thế hệ mới, màn hình Liquid Retina XDR 120Hz siêu sắc nét.', 'MacBook Pro 14 M3 Pro là cỗ máy hoàn hảo cho lập trình viên, designer và nhà sáng tạo nội dung chuyên nghiệp. Màu Space Black chống bám vân tay, thời lượng pin lên đến 18 giờ làm việc liên tục.', '{"Màn hình": "14.2 inch Liquid Retina XDR (3024 x 1964) 120Hz", "Chip CPU": "Apple M3 Pro 11-core CPU", "GPU": "14-core GPU", "RAM": "18 GB Unified Memory", "Ổ cứng": "512 GB SSD siêu tốc", "Trọng lượng": "1.61 kg"}', 1, 0, 950, 5.0),

(4, 2, 'Laptop Gaming ASUS ROG Zephyrus G16 OLED (2024)', 'asus-rog-zephyrus-g16-oled', 'ASUS', 54990000.00, 49990000.00, 8, 'rog_g16.png', '["rog1.png"]', 'Intel Core Ultra 9 185H, RTX 4070 8GB, Màn hình 2.5K OLED 240Hz 0.2ms, Vỏ nhôm CNC cao cấp.', 'ROG Zephyrus G16 định nghĩa lại chuẩn mực laptop gaming mỏng nhẹ cao cấp. Thiết kế dải đèn Slash Lighting độc bản trên nắp máy, hệ thống tản nhiệt buồng hơi 3 quạt êm ái mát mẻ tuyệt đối.', '{"Màn hình": "16 inch ROG Nebula OLED 2.5K 240Hz 0.2ms G-Sync", "Chip CPU": "Intel Core Ultra 9 185H (16 nhân 22 luồng)", "Card đồ họa": "NVIDIA GeForce RTX 4070 8GB GDDR6", "RAM": "32 GB LPDDR5X 7467MHz", "Ổ cứng": "1 TB SSD M.2 PCIe 4.0"}', 1, 1, 870, 4.8),

(5, 3, 'iPad Pro 11 inch M4 Wi-Fi 256GB Ultra Thin', 'ipad-pro-11-m4-256gb', 'Apple', 28990000.00, 26990000.00, 15, 'ipadpro_m4.png', '["ipad1.png"]', 'Mỏng chỉ 5.3mm kỷ lục, Màn hình Ultra Retina XDR Tandem OLED, chip Apple M4 sức mạnh AI vượt trội.', 'iPad Pro M4 mang đến trải nghiệm thị giác đỉnh cao với tấm nền OLED hai lớp Tandem OLED rực rỡ. Hỗ trợ Apple Pencil Pro mới với cảm ứng bóp xoay và phản hồi rung ma thuật.', '{"Màn hình": "11 inch Ultra Retina XDR Tandem OLED 120Hz", "Chip CPU": "Apple M4 9-core CPU, 10-core GPU", "RAM": "8 GB", "Bộ nhớ": "256 GB", "Độ mỏng": "5.3 mm, siêu nhẹ 444g"}', 1, 0, 720, 4.9),

(6, 4, 'Apple Watch Ultra 2 GPS + Cellular 49mm Titanium', 'apple-watch-ultra-2-49mm', 'Apple', 21990000.00, 19490000.00, 10, 'applewatch_ultra2.png', '["aw_1.png"]', 'Vỏ Titan cấp hàng không, màn hình sáng 3000 nits, định vị GPS kép L1/L5 chuyên nghiệp, pin 72 giờ.', 'Chiếc đồng hồ thể thao chuyên nghiệp và bền bỉ nhất của Apple dành cho những chuyến phiêu lưu khám phá, lặn biển và chạy bộ đường dài.', '{"Kích thước": "49 mm Titan siêu bền", "Màn hình": "Sapphire Crystal OLED 3000 nits", "Chống nước": "WR100, Chứng nhận lặn EN13319 40m", "Pin": "36 giờ sử dụng bình thường, 72 giờ chế độ tiết kiệm pin"}', 1, 1, 640, 5.0),

(7, 5, 'Tai nghe Sony WH-1000XM5 Chống Ồn Đỉnh Cao', 'tai-nghe-sony-wh-1000xm5', 'Sony', 8490000.00, 6990000.00, 30, 'sony_wh1000xm5.png', '["sony_1.png"]', 'Chống ồn chủ động kép với 8 micro và 2 bộ xử lý, Âm thanh Hi-Res Audio Wireless LDAC, Pin 30h.', 'Sony WH-1000XM5 tiếp tục khẳng định vị thế dẫn đầu thế giới về công nghệ khử tiếng ồn chủ động. Thiết kế chụp tai êm ái bọc da mềm mại, hỗ trợ đàm thoại AI chống ồn gió trong trẻo tuyệt đối.', '{"Driver": "30 mm màng loa sợi carbon", "Thời lượng pin": "30 giờ bật ANC (Sạc 3 phút dùng 3 giờ)", "Kết nối": "Bluetooth 5.2, LDAC, Multipoint nối 2 thiết bị", "Trọng lượng": "250g"}', 1, 1, 1100, 4.9),

(8, 5, 'Loa Bluetooth Marshall Stanmore III Chính Hãng', 'loa-marshall-stanmore-iii', 'Marshall', 10490000.00, 8990000.00, 14, 'marshall_stanmore3.png', '["marshall_1.png"]', 'Âm thanh không gian stereo rộng hơn, Thiết kế da cổ điển Vintage sang trọng, Công suất 80W RMS.', 'Marshall Stanmore III là biểu tượng của âm thanh Rock and Roll với âm trường trải rộng, âm bass sâu lắng uy lực và dải treble tách bạch tuyệt hảo.', '{"Công suất": "80W (1x 50W Woofer + 2x 15W Tweeter)", "Dải tần": "45 - 20,000 Hz", "Kết nối": "Bluetooth 5.2 LE Audio, AUX 3.5mm, RCA", "Nguồn điện": "100-240V trực tiếp"}', 0, 0, 430, 4.7),

(9, 6, 'Bàn phím cơ không dây NuPhy Air75 V2 Low-Profile', 'ban-phim-co-nuphy-air75-v2', 'NuPhy', 3200000.00, 2750000.00, 20, 'nuphy_air75.png', '["nuphy_1.png"]', 'Switch Gateron Low-Profile 2.0 hotswap, Keycap PBT nắp gõ cực êm, Kết nối 3 chế độ 2.4G/BT/Dây.', 'Bàn phím cơ mỏng nhẹ đỉnh nhất cho dân văn phòng và lập trình viên Mac/Windows. Polling rate 1000Hz ở chế độ không dây.', '{"Layout": "75% (84 phím)", "Switch": "Gateron Low-profile Cowberry/Wisteria Hotswap", "Pin": "4000 mAh (dùng đến 220 giờ)", "LED": "RGB 16.8 triệu màu + 2 dải LED cạnh hông"}', 0, 0, 510, 4.8),

(10, 6, 'Củ sạc nhanh GaN Anker Prime 100W 3 cổng', 'cu-sac-anker-prime-100w', 'Anker', 1900000.00, 1490000.00, 40, 'anker_prime_100w.png', '["anker_1.png"]', 'Công nghệ GaNPrime độc quyền, 2 cổng USB-C + 1 USB-A, sạc cùng lúc MacBook, iPhone, iPad.', 'Củ sạc siêu nhỏ gọn với công suất khủng 100W giúp bạn tối giản hóa không gian làm việc và hành lý du lịch.', '{"Tổng công suất": "100W Max", "Cổng kết nối": "2x USB-C Power Delivery, 1x USB-A PowerIQ", "Công nghệ an toàn": "ActiveShield 2.0 kiểm soát nhiệt độ 3 triệu lần/ngày"}', 0, 1, 890, 4.9);

-- Thêm Đơn hàng mẫu
INSERT INTO `hieumini_bookstore_db`.`orders` (`id`, `order_code`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `shipping_city`, `payment_method`, `payment_status`, `shipping_status`, `subtotal`, `discount`, `shipping_fee`, `total_amount`, `note`) VALUES
(1, 'ORD-2026-001', 2, 'Nguyễn Hoàng Nam', 'nam.nguyen@gmail.com', '0912345678', '120 Phố Huế, Hai Bà Trưng', 'Hà Nội', 'bank_transfer', 'paid', 'completed', 31990000.00, 0.00, 0.00, 31990000.00, 'Giao giờ hành chính, gọi trước khi giao.'),
(2, 'ORD-2026-002', 3, 'Lê Thị Thu Thảo', 'thao.le@gmail.com', '0934567890', '45 Lê Duẩn, Quận 1', 'TP. Hồ Chí Minh', 'cod', 'unpaid', 'shipping', 6990000.00, 349500.00, 30000.00, 6670500.00, 'Đóng gói cẩn thận có xốp chống sốc.');

-- Thêm Chi tiết đơn hàng mẫu
INSERT INTO `hieumini_bookstore_db`.`order_items` (`order_id`, `product_id`, `product_name`, `price`, `quantity`, `total`) VALUES
(1, 1, 'iPhone 16 Pro Max 256GB Titan Sa Mạc', 31990000.00, 1, 31990000.00),
(2, 7, 'Tai nghe Sony WH-1000XM5 Chống Ồn Đỉnh Cao', 6990000.00, 1, 6990000.00);

-- Thêm Đánh giá mẫu
INSERT INTO `hieumini_bookstore_db`.`reviews` (`product_id`, `user_id`, `user_name`, `rating`, `comment`) VALUES
(1, 2, 'Nguyễn Hoàng Nam', 5, 'Máy cầm rất đầm tay, viền titan sa mạc cực kỳ sang trọng. Giao hàng HieuMini siêu nhanh trong 2h!'),
(7, 3, 'Lê Thị Thu Thảo', 5, 'Khả năng chống ồn của Sony XM5 thực sự kinh ngạc, ngồi quán cafe bật nhạc là tĩnh lặng như trong phòng thu.');

-- Tài khoản quản trị demo dùng chung
UPDATE `hieumini_bookstore_db`.`users` SET `email`='admin@hieumini.vn', `password`='$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role`='admin';

SET FOREIGN_KEY_CHECKS = 1;
