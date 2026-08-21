CREATE DATABASE IF NOT EXISTS `hieumini_furniture_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hieumini_furniture_db`;
SET FOREIGN_KEY_CHECKS = 0;

-- 1. Table: categories
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;
DROP TABLE IF EXISTS `coupons`;
DROP TABLE IF EXISTS `contacts`;

CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `description` TEXT,
  `icon` VARCHAR(50) DEFAULT 'bi-pencil-square',
  `badge` VARCHAR(50) DEFAULT 'Phổ biến',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Table: products
CREATE TABLE `products` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `category_id` INT NOT NULL,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `sku` VARCHAR(50) UNIQUE NOT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `sale_price` DECIMAL(12,2) DEFAULT NULL,
  `image` VARCHAR(255) NOT NULL,
  `description` TEXT,
  `specification` TEXT,
  `stock_quantity` INT DEFAULT 100,
  `is_featured` TINYINT(1) DEFAULT 0,
  `is_hot` TINYINT(1) DEFAULT 0,
  `is_new` TINYINT(1) DEFAULT 1,
  `rating` DECIMAL(3,2) DEFAULT 5.0,
  `review_count` INT DEFAULT 18,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Table: users
CREATE TABLE `users` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL UNIQUE,
  `password` VARCHAR(255) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `address` TEXT DEFAULT NULL,
  `role` ENUM('admin', 'customer') DEFAULT 'customer',
  `avatar` VARCHAR(255) DEFAULT 'default_avatar.png',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Table: orders
CREATE TABLE `orders` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_code` VARCHAR(30) UNIQUE NOT NULL,
  `user_id` INT DEFAULT NULL,
  `customer_name` VARCHAR(100) NOT NULL,
  `customer_email` VARCHAR(150) NOT NULL,
  `customer_phone` VARCHAR(20) NOT NULL,
  `shipping_address` TEXT NOT NULL,
  `order_notes` TEXT DEFAULT NULL,
  `subtotal` DECIMAL(12,2) NOT NULL,
  `discount_amount` DECIMAL(12,2) DEFAULT 0,
  `shipping_fee` DECIMAL(12,2) DEFAULT 0,
  `total_amount` DECIMAL(12,2) NOT NULL,
  `payment_method` ENUM('cod', 'bank_transfer', 'momo') DEFAULT 'cod',
  `payment_status` ENUM('unpaid', 'paid', 'refunded') DEFAULT 'unpaid',
  `status` ENUM('pending', 'processing', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Table: order_items
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_image` VARCHAR(255) NOT NULL,
  `price` DECIMAL(12,2) NOT NULL,
  `quantity` INT NOT NULL,
  `total_price` DECIMAL(12,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Table: reviews
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_name` VARCHAR(100) NOT NULL,
  `rating` INT NOT NULL DEFAULT 5,
  `comment` TEXT NOT NULL,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Table: coupons
CREATE TABLE `coupons` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `code` VARCHAR(50) UNIQUE NOT NULL,
  `discount_type` ENUM('percentage', 'fixed') DEFAULT 'percentage',
  `discount_value` DECIMAL(10,2) NOT NULL,
  `min_order_value` DECIMAL(12,2) DEFAULT 0,
  `max_discount` DECIMAL(12,2) DEFAULT NULL,
  `is_active` TINYINT(1) DEFAULT 1,
  `expiry_date` DATE DEFAULT '2028-12-31'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. Table: contacts
CREATE TABLE `contacts` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `fullname` VARCHAR(100) NOT NULL,
  `email` VARCHAR(150) NOT NULL,
  `phone` VARCHAR(20) DEFAULT NULL,
  `subject` VARCHAR(255) DEFAULT NULL,
  `message` TEXT NOT NULL,
  `status` ENUM('new', 'replied', 'closed') DEFAULT 'new',
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- SEED DATA

-- Categories
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `badge`) VALUES
(1, 'Bút & Dụng Cụ Viết', 'but-dung-cu-viet', 'Bút bi, bút gel, bút máy, bút chì kim và bút lông chuyên nghiệp', 'bi-pen', 'Bán chạy'),
(2, 'Sổ Tay & Vở Viết', 'so-tay-vo-viet', 'Sổ còng binder, bullet journal, vở học sinh ô ly cao cấp', 'bi-journal-bookmark', 'Mới về'),
(3, 'Dụng Cụ Vẽ & Mỹ Thuật', 'dung-cu-ve-my-thuat', 'Màu nước, chì màu nghệ thuật, cọ vẽ và bảng pha màu cao cấp', 'bi-palette', 'Yêu thích'),
(4, 'Bìa Hồ Sơ & Lưu Trữ', 'bia-ho-so-luu-tru', 'Cặp tài liệu nhiều ngăn, file còng, túi zip đựng đề thi chống nước', 'bi-folder2-open', 'Gọn gàng'),
(5, 'Phụ Kiện Bàn Học', 'phu-kien-ban-hoc', 'Hộp bút canvas, đèn LED học sinh chống cận, máy tính khoa học, thước kẻ eke', 'bi-lamp', 'Tiện ích'),
(6, 'Ba Lô & Cặp Học Sinh', 'ba-lo-cap-hoc-sinh', 'Ba lô chống gù lưng, túi tote canvas thời trang học đường', 'bi-backpack', 'Hot Trend');

-- 30 Products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `sku`, `price`, `sale_price`, `image`, `description`, `specification`, `stock_quantity`, `is_featured`, `is_hot`, `is_new`, `rating`, `review_count`) VALUES
(1, 1, 'Bút Gel Pastel Morandi (Set 6 Cây)', 'but-gel-pastel-morandi-set-6', 'PEN-MOR-01', 55000, 45000, 'p1.png', 'Bộ 6 cây bút gel màu pastel phong cách Morandi siêu ngọt ngào. Ngòi 0.5mm êm ái, mực đều và mau khô không lem trang giấy.', 'Ngòi bút: 0.5mm\nSố lượng: 6 cây/hộp\nMàu mực: Đen / Xanh tím than\nChất liệu thân: Nhựa ABS mờ nhung', 150, 1, 1, 1, 4.9, 42),
(2, 1, 'Bút Chì Kim Cao Cấp Pentel GraphGear 1000 0.5mm', 'but-chi-kim-pentel-graphgear-1000', 'PEN-PEN-02', 210000, 185000, 'p2.png', 'Bút chì kim cơ khí chuẩn kỹ thuật Pentel GraphGear 1000 với thân kim loại cao cấp, hệ thống thu ngòi an toàn chống gãy ngòi chì.', 'Thương hiệu: Pentel Nhật Bản\nCỡ ngòi: 0.5mm\nChất liệu: Hợp kim nhôm & kẹp kim loại', 80, 1, 1, 0, 5.0, 38),
(3, 1, 'Bút Dạ Quang 2 Đầu Pastel Macaron (Set 6 Màu)', 'but-da-quang-2-dau-pastel-macaron', 'PEN-HL-03', 68000, 55000, 'p3.png', 'Set bút highlight 2 đầu phong cách Macaron pastel dịu mắt, gồm 1 đầu vát dày và 1 đầu tròn nhỏ tiện ghi chú và gạch chân tài liệu.', 'Số lượng: 6 màu pastel\nThiết kế: 2 đầu tiện dụng\nMực: Gốc nước an toàn, không mùi', 200, 1, 0, 1, 4.8, 29),
(4, 1, 'Bút Máy Học Sinh Kim Tinh Ngòi Mài Luyện Chữ', 'but-may-kim-tinh-ngoi-mai', 'PEN-FP-04', 85000, 68000, 'p4.png', 'Bút máy ngòi mài thanh đậm giúp học sinh dễ dàng rèn nét chữ đẹp, thân bút hợp kim nhẹ tay cầm chắc chắn chống trơn trượt.', 'Chất liệu: Hợp kim cao cấp\nNgòi: Mài thanh đậm vát mép\nPiston: Hút mực xoay êm ái', 120, 0, 0, 1, 4.7, 19),
(5, 1, 'Bút Lông Calligraphy Chuyên Nghiệp Tombow Fudenosuke', 'but-long-calligraphy-tombow', 'PEN-TM-05', 110000, 92000, 'p5.png', 'Bút cọ brush Tombow Fudenosuke đầu đàn hồi cao cấp, chuyên dụng vẽ doodle, viết thư pháp hiện đại và tiêu đề trang trí sổ tay.', 'Xuất xứ: Nhật Bản\nĐầu cọ: Elastomer cứng đàn hồi\nMực: Pigment kháng nước tuyệt đối', 95, 1, 1, 0, 4.9, 51),

(6, 2, 'Sổ Còng Binder A5 Bìa Da PU Vintage Cao Cấp', 'so-cong-binder-a5-bia-da-pu', 'NB-BIN-06', 150000, 120000, 'p6.png', 'Sổ còng A5 da PU mềm cao cấp sang trọng, dễ dàng tháo mở thêm bớt ruột giấy, tích hợp khe cài thẻ ngân hàng và khe cắm bút tiện lợi.', 'Kích thước: Khổ A5 (18 x 23 cm)\nBìa: Da PU chống nước cao cấp\nKhóa còng: Kim loại mạ chống gỉ 6 lỗ', 110, 1, 1, 1, 4.9, 65),
(7, 2, 'Sổ Bullet Journal Dot Grid 160 Trang Giấy 100gsm', 'so-bullet-journal-dot-160-trang', 'NB-BJ-07', 95000, 75000, 'p7.png', 'Sổ chấm dot grid chuyên dụng lập kế hoạch Bullet Journal. Giấy định lượng 100gsm siêu dày, không lem khi viết bút highlight hay bút gel nước.', 'Khổ giấy: A5 tiêu chuẩn\nĐịnh lượng giấy: 100gsm (160 trang)\nĐặc điểm: Dập dây ruy băng đánh dấu trang', 180, 1, 0, 1, 4.8, 33),
(8, 2, 'Tập Vở Học Sinh Campus Landscape 200 Trang Ô Ly', 'tap-vo-campus-landscape-200-trang', 'NB-CP-08', 35000, 28000, 'p8.png', 'Vở học sinh Campus công nghệ gáy thông minh ép nhiệt chắc chắn, đường kẻ ô ly rõ nét, giấy viết không nhòe mực, độ trắng 82% ISO dịu mắt.', 'Số trang: 200 trang\nĐộ trắng: 82% ISO chống lóa\nThương hiệu: Kokuyo Campus', 500, 0, 1, 0, 5.0, 88),
(9, 2, 'Sổ Kế Hoạch Weekly & Monthly Planner Dễ Thương', 'so-ke-hoach-planner-minimalist', 'NB-PL-09', 80000, 65000, 'p9.png', 'Sổ tay lên kế hoạch học tập chi tiết theo tuần và theo tháng, bố cục thiết kế khoa học giúp quản lý mục tiêu và theo dõi thói quen hiệu quả.', 'Quy cách: Bìa cứng cán mờ\nRuột: Thiết kế sẵn layout kế hoạch\nSố trang: 120 trang in màu pastel', 140, 1, 0, 1, 4.8, 27),
(10, 2, 'Sổ Phác Thảo Sketchbook A4 Giấy Vẽ 160gsm Bìa Cứng', 'so-phac-thao-sketchbook-a4-160gsm', 'NB-SK-10', 135000, 110000, 'p10.png', 'Sổ vẽ phác thảo mỹ thuật Sketchbook A4 chuyên dụng, giấy định lượng 160gsm bề mặt hạt mịn bám chì than, màu sáp, marker và bút kỹ thuật.', 'Khổ giấy: A4 chuẩn (21 x 29.7 cm)\nĐịnh lượng giấy: 160gsm\nSố tờ: 60 tờ (120 trang) gáy xoắn', 90, 0, 1, 0, 4.9, 44),

(11, 3, 'Bộ Màu Nước Dạng Nén Solid Watercolor 36 Màu Kèm Cọ', 'bo-mau-nuoc-nen-36-mau-kem-co', 'ART-WC-11', 200000, 165000, 'p11.png', 'Bộ màu nước 36 thỏi nén màu sắc tươi tắn, độ hòa tan cao, đựng trong hộp thiếc cao cấp kèm cọ ngậm nước Water Brush và mút thấm chuyên dụng.', 'Quy cách: Hộp kim loại sang trọng\nSố màu: 36 sắc tố tươi sáng\nPhụ kiện đi kèm: 1 cọ nước + 1 cọ nét + mút xốp', 85, 1, 1, 1, 4.9, 57),
(12, 3, 'Hộp Bút Màu Chì Dầu Faber-Castell 48 Màu Chuyên Nghiệp', 'hop-chi-mau-dau-faber-castell-48', 'ART-FC-12', 290000, 245000, 'p12.png', 'Chì màu dầu Faber-Castell dòng Classic cao cấp, lõi chì dày 3.3mm công nghệ SV bonding chống gãy vụn, hạt màu đậm dễ phối loang tầng màu.', 'Thương hiệu: Faber-Castell Đức\nSố lượng: 48 màu chuẩn mỹ thuật\nĐặc điểm: Lõi chì dầu siêu mịn màng', 60, 1, 1, 0, 5.0, 72),
(13, 3, 'Bộ Cọ Vẽ Nghệ Thuật 10 Cây Đầu Lông Chuyên Dụng', 'bo-co-ve-nghe-thuat-10-cay', 'ART-BR-13', 110000, 85000, 'p13.png', 'Bộ 10 cây cọ đa dạng kích cỡ từ cọ nét, cọ dẹp đến cọ quạt, sợi lông nylon cao cấp giữ nước tốt, thích hợp màu nước, acrylic và sơn dầu.', 'Số lượng: 10 cây cọ các kích thước\nChất liệu lông: Sợi tổng hợp mềm mượt\nThân cọ: Gỗ sơn phủ bóng chống nứt', 130, 0, 0, 1, 4.8, 25),
(14, 3, 'Bút Vẽ Kỹ Thuật Artline Ergoline Đủ Size (Set 5 Cây)', 'but-ve-ky-thuat-artline-ergoline-5', 'ART-AL-14', 160000, 135000, 'p14.png', 'Set 5 bút kỹ thuật Artline Ergoline các cỡ ngòi 0.1, 0.2, 0.3, 0.5, 0.8mm. Mực pigment bền màu, kháng nước tuyệt đối khi vẽ đè màu nước.', 'Thương hiệu: Artline Nhật Bản\nCỡ ngòi: 0.1mm đến 0.8mm\nTính năng: Chống nước, chống phai màu UV', 110, 1, 0, 0, 4.9, 39),
(15, 3, 'Bảng Pha Màu & Khay Rửa Cọ Silicon Gấp Gọn Đa Năng', 'bang-pha-mau-khay-rua-co-gap-gon', 'ART-TR-15', 55000, 42000, 'p15.png', 'Khay rửa cọ vẽ kiêm bảng pha màu thông minh làm từ silicon dẻo, có thể gấp gọn phẳng tiện lợi mang đi vẽ ngoại cảnh hoặc lớp học vẽ.', 'Chất liệu: Silicon dẻo thực phẩm siêu bền\nTính năng: Gấp gọn xếp tầng\nKích thước mở: 12 x 12 x 10 cm', 170, 0, 0, 1, 4.7, 16),

(16, 4, 'Cặp Đựng Tài Liệu A4 Nhiều Ngăn Có Quai Xách Hiện Đại', 'cap-dung-tai-lieu-a4-nhieu-ngan', 'DOC-FL-16', 98000, 78000, 'p16.png', 'Cặp phân loại tài liệu A4 12 ngăn có nhãn chỉ mục màu sắc, quai xách chắc chắn, khóa cài bấm tiện lợi giúp giữ bài kiểm tra luôn phẳng phiu.', 'Kích thước: Khổ A4 (33 x 24 cm)\nSố ngăn: 12 ngăn phân chia môn học\nChất liệu: Nhựa PP dẻo dai chống rách', 160, 1, 1, 1, 4.9, 49),
(17, 4, 'Bìa Cây Trong Suốt Giữ Hồ Sơ Không Cần Đục Lỗ (Set 10)', 'bia-cay-trong-suot-set-10', 'DOC-BC-17', 45000, 35000, 'p17.png', 'Bìa kẹp gáy cây nhựa trong suốt cao cấp giữ chặt tài liệu dày đến 50 trang mà không cần dập lỗ bấm kim, giữ nguyên vẹn tiểu luận đề tài.', 'Quy cách: Set 10 chiếc kèm gáy kẹp màu\nKhổ: A4\nSức chứa: 1 - 50 tờ giấy', 250, 0, 1, 0, 4.8, 31),
(18, 4, 'Kệ Đựng Sách Vở & Tài Liệu 4 Ngăn Lắp Ghép Để Bàn', 'ke-sach-vo-4-ngan-de-ban-hoc', 'DOC-KE-18', 115000, 89000, 'p18.png', 'Kệ sách mini 4 ngăn đứng để bàn học sinh, chất liệu nhựa cứng chịu lực tốt, thiết kế thông thoáng dễ lau chùi, giúp góc học tập gọn gàng.', 'Kích thước: 32 x 26 x 27 cm\nSố ngăn: 4 ngăn tài liệu đứng\nChất liệu: Nhựa PS chịu tải 15kg', 95, 1, 0, 1, 4.8, 23),
(19, 4, 'File Còng Bật 7cm Đựng Tài Liệu Văn Phòng Bền Đẹp', 'file-cong-bat-7cm-van-phong', 'DOC-CB-19', 60000, 48000, 'p19.png', 'File còng bật khổ F4 dày 7cm làm từ bìa cứng bọc simili cao cấp, khóa còng inox mạ chống gỉ mở nhẹ nhàng, lưu trữ đến 500 tờ tài liệu.', 'Khổ bìa: F4 / A4\nĐộ dày gáy: 70mm (7cm)\nSức chứa: ~500 tờ giấy', 130, 0, 0, 0, 4.7, 14),
(20, 4, 'Túi Zip Lưới Đựng Giấy Tờ Đề Thi A4 Chống Nước (Set 5)', 'tui-zip-luoi-dung-giay-to-a4-set-5', 'DOC-ZP-20', 50000, 39000, 'p20.png', 'Túi lưới có khóa kéo zipper A4 tiện lợi đựng dụng cụ học tập, đề thi học kỳ, chất liệu lưới gia cường chống nước và chống rách cực bền.', 'Quy cách: Set 5 túi 5 màu phong cách pastel\nKhổ: A4 rộng rãi\nKhóa kéo: Kim loại trơn mượt', 220, 0, 1, 1, 4.9, 40),

(21, 5, 'Hộp Bút Vải Canvas Đa Năng Sức Chứa Khủng 50 Bút', 'hop-but-vai-canvas-da-nang-suc-chua-khung', 'ACC-PC-21', 89000, 69000, 'p21.png', 'Hộp bút vải canvas phong cách Hàn Quốc mở rộng 2 tầng, sức chứa khủng lên tới 50 cây bút kèm thước kẻ, máy tính bỏ túi và băng dính washi.', 'Kích thước: 22 x 10 x 8 cm\nChất liệu: Vải Canvas dày dặn chống bám bụi\nMàu sắc: Hồng pastel / Xanh mint / Ghi xám', 210, 1, 1, 1, 5.0, 77),
(22, 5, 'Đèn Bàn Học LED Cảm Ứng Chống Cận 3 Chế Độ Sáng', 'den-ban-hoc-led-cam-ung-chong-can', 'ACC-DL-22', 250000, 195000, 'p22.png', 'Đèn LED để bàn thông minh trang bị chip LED lọc ánh sáng xanh, 3 nhiệt độ màu (trắng, vàng, trung tính), tích hợp pin sạc 2000mAh dùng 6 tiếng.', 'Công suất: 5W tiết kiệm điện\nPin: Lithium 2000mAh sạc Type-C\nTính năng: Cảm ứng chạm dimming vô cấp', 75, 1, 1, 0, 4.9, 45),
(23, 5, 'Máy Tính Khoa Học Casio FX-580VN X Cho Học Sinh', 'may-tinh-khoa-hoc-casio-fx-580vn-x', 'ACC-CS-23', 750000, 650000, 'p23.png', 'Máy tính bỏ túi khoa học chính hãng Casio FX-580VN X với 521 tính năng, màn hình LCD phân giải cao, hỗ trợ ngôn ngữ Tiếng Việt cho học sinh thi THPT.', 'Thương hiệu: Casio chính hãng (tem chống giả)\nSố tính năng: 521 tính năng\nBảo hành: 7 năm chính hãng', 120, 1, 1, 0, 5.0, 120),
(24, 5, 'Bộ Dụng Cụ Thước Kẻ Eke Đo Độ Hợp Kim Nhôm Cao Cấp', 'bo-thuoc-ke-eke-do-do-hop-kim-nhom', 'ACC-RL-24', 55000, 45000, 'p24.png', 'Bộ dụng cụ hình học 4 món: Thước thẳng 20cm, Eke 45 độ, Eke 60 độ và Thước đo độ, làm bằng nhôm anode siêu nhẹ khắc số laser không phai.', 'Chất liệu: Hợp kim nhôm siêu nhẹ\nBộ sản phẩm: 4 chi tiết kèm hộp đựng\nMàu sắc: Xanh Navy / Hồng / Bạc kim loại', 180, 0, 0, 1, 4.8, 36),
(25, 5, 'Gọt Bút Chì Quay Tay Hình Ngôi Nhà Hoạt Hình Đáng Yêu', 'got-but-chi-quay-tay-ngoi-nha-cute', 'ACC-SH-25', 75000, 58000, 'p25.png', 'Máy gọt bút chì tay quay cơ học hình ngôi nhà xinh xắn, lưỡi dao thép xoắn ốc hợp kim vonfram siêu sắc bén, ngắt chì nhọn chuẩn xác.', 'Chất liệu: Nhựa ABS nguyên sinh & lưỡi thép vonfram\nTính năng: Tự động giữ và nhả bút chì\nKích thước: 10 x 9 x 8 cm', 140, 0, 1, 1, 4.8, 22),
(26, 5, 'Kéo Cắt Giấy An Toàn Có Nắp Đậy Thép Titan Siêu Bén', 'keo-cat-giay-an-toan-titan-co-nap', 'ACC-SC-26', 42000, 32000, 'p26.png', 'Kéo thủ công học sinh bọc nắp an toàn, lưỡi phủ hợp kim titan chống bám dính băng keo, mũi bo tròn an toàn tuyệt đối cho các bạn học sinh.', 'Chiều dài: 14cm\nLưỡi kéo: Thép không gỉ mạ Titan\nTay cầm: Nhựa mềm TPR êm ái', 200, 0, 0, 0, 4.7, 18),
(27, 5, 'Dập Ghim Bấm Nhỏ Kèm 1000 Ghim Bấm Pastel Xinh Xắn', 'dap-ghim-bam-nho-kem-1000-ghim', 'ACC-ST-27', 48000, 38000, 'p27.png', 'Dập ghim mini kèm hộp 1000 kim bấm No.10 phong cách kẹo ngọt, cơ chế lò xo lực đàn hồi nhẹ bấm được 15 tờ giấy êm ái không kẹt ghim.', 'Quy cách: 1 dập ghim + 1 hộp 1000 kim ghim\nCỡ kim: No.10 tiêu chuẩn\nKhả năng bấm: 2 - 15 tờ giấy A4', 210, 0, 0, 1, 4.8, 26),
(28, 5, 'Băng Xóa Kéo Mini Không Đứt Đoạn Dài 12m Tiện Lợi', 'bang-xoa-keo-mini-khong-dut-doan-12m', 'ACC-CT-28', 32000, 25000, 'p28.png', 'Băng xóa kéo cao cấp lõi dải phim PET siêu dai dài 12m, lực kéo trơn mượt không đứt gãy giữa chừng, mặt xóa mịn cho phép viết đè bút bi ngay.', 'Độ dài: 12 mét (bản rộng 5mm)\nChất liệu băng: Màng nhựa PET siêu dai\nThiết kế: Vỏ trong suốt nhìn thấy lượng băng', 300, 1, 0, 1, 4.9, 58),

(29, 6, 'Ba Lô Học Sinh Chống Gù & Chống Thấm Nước Phản Quang', 'ba-lo-hoc-sinh-chong-gu-chong-nuoc', 'BAG-BP-29', 390000, 320000, 'p29.png', 'Ba lô học đường cao cấp đệm lưng tổ ong 3D thoáng khí giảm áp lực cột sống chống gù, vải Oxford chống thấm nước kèm dải phản quang an toàn ban đêm.', 'Kích thước: 42 x 30 x 18 cm (vừa laptop 15.6 inch)\nChất liệu: Vải Oxford 900D chống thấm\nTính năng: Đệm lưng công thái học giảm tải', 65, 1, 1, 1, 5.0, 61),
(30, 6, 'Túi Tote Vải Canvas Đựng Vở & Laptop A4 Thời Trang', 'tui-tote-canvas-dung-vo-laptop-a4', 'BAG-TT-30', 125000, 95000, 'p30.png', 'Túi tote canvas phong cách Vintage trẻ trung có khóa kéo miệng an toàn và ngăn phụ bên trong, đựng vừa tập vở A4, laptop và đồ dùng học tập hàng ngày.', 'Kích thước: 38 x 36 cm (đáy rộng 8cm)\nChất liệu: Vải Canvas tự nhiên 12oz\nKhóa: Khóa kéo kim loại & túi con bên trong', 150, 1, 0, 1, 4.9, 43);

-- Default Users (Password is bcrypt of 'admin123' and 'user123')
-- admin123 hash: $2y$10$eO0V4hF2vjYyv40cghWqC.Vq0Z7y7Xvj6H6fGkW/wQ9d2Hj0A9Z32
-- user123 hash: $2y$10$7vM/4hF2vjYyv40cghWqC.Vq0Z7y7Xvj6H6fGkW/wQ9d2Hj0A9Z32
INSERT INTO `users` (`id`, `fullname`, `email`, `password`, `phone`, `address`, `role`, `avatar`) VALUES
(1, 'Quản Trị Viên HieuMini', 'admin@hieumini.vn', '$2y$10$wT2Hl95qA7vO3P6sJc9k4uM1Qe5/XzN9bS.5XqGkVc7P1/dI5hU0W', '0901234567', 'Tòa nhà HieuMini Tower, 123 Đường Cầu Giấy, Hà Nội', 'admin', 'admin_avatar.png'),
(2, 'Trần Văn Minh Hiếu', 'user@hieumini.vn', '$2y$10$wT2Hl95qA7vO3P6sJc9k4uM1Qe5/XzN9bS.5XqGkVc7P1/dI5hU0W', '0987654321', 'Số 45 Ngõ 88 Phố Trần Đại Nghĩa, Hai Bà Trưng, Hà Nội', 'customer', 'user_avatar.png');

-- Coupons
INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_order_value`, `max_discount`, `is_active`, `expiry_date`) VALUES
(1, 'HIEUMINI10', 'percentage', 10.00, 100000.00, 50000.00, 1, '2028-12-31'),
(2, 'FREESHIP', 'fixed', 30000.00, 250000.00, 30000.00, 1, '2028-12-31'),
(3, 'BACK2SCHOOL', 'percentage', 15.00, 200000.00, 100000.00, 1, '2028-12-31');

-- Sample Orders
INSERT INTO `orders` (`id`, `order_code`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `shipping_address`, `order_notes`, `subtotal`, `discount_amount`, `shipping_fee`, `total_amount`, `payment_method`, `payment_status`, `status`, `created_at`) VALUES
(1, 'HM-20260801-01', 2, 'Trần Văn Minh Hiếu', 'user@hieumini.vn', '0987654321', 'Số 45 Ngõ 88 Phố Trần Đại Nghĩa, Hai Bà Trưng, Hà Nội', 'Giao giờ hành chính giúp mình nhé!', 270000, 27000, 0, 243000, 'cod', 'unpaid', 'completed', '2026-08-18 10:15:00'),
(2, 'HM-20260802-02', 2, 'Trần Văn Minh Hiếu', 'user@hieumini.vn', '0987654321', 'Số 45 Ngõ 88 Phố Trần Đại Nghĩa, Hai Bà Trưng, Hà Nội', 'Đóng gói bọc xốp cẩn thận giúp shop', 650000, 50000, 0, 600000, 'bank_transfer', 'paid', 'processing', '2026-08-19 14:30:00'),
(3, 'HM-20260803-03', NULL, 'Nguyễn Thu Trang', 'thutrang99@gmail.com', '0912345678', 'Tầng 5 Tòa Keangnam, Mễ Trì, Nam Từ Liêm, Hà Nội', 'Giao trước 17h', 165000, 0, 25000, 190000, 'cod', 'unpaid', 'pending', '2026-08-20 09:10:00');

-- Order Items
INSERT INTO `order_items` (`id`, `order_id`, `product_id`, `product_name`, `product_image`, `price`, `quantity`, `total_price`) VALUES
(1, 1, 1, 'Bút Gel Pastel Morandi (Set 6 Cây)', 'p1.png', 45000, 2, 90000),
(2, 1, 6, 'Sổ Còng Binder A5 Bìa Da PU Vintage Cao Cấp', 'p6.png', 120000, 1, 120000),
(3, 1, 21, 'Hộp Bút Vải Canvas Đa Năng Sức Chứa Khủng 50 Bút', 'p21.png', 69000, 1, 60000),
(4, 2, 23, 'Máy Tính Khoa Học Casio FX-580VN X Cho Học Sinh', 'p23.png', 650000, 1, 650000),
(5, 3, 11, 'Bộ Màu Nước Dạng Nén Solid Watercolor 36 Màu Kèm Cọ', 'p11.png', 165000, 1, 165000);

-- Reviews
INSERT INTO `reviews` (`id`, `product_id`, `user_name`, `rating`, `comment`, `created_at`) VALUES
(1, 1, 'Linh Nguyễn', 5, 'Mực bút ra rất đều và màu pastel siêu xinh xắn! Rất đáng tiền.', '2026-08-15 11:20:00'),
(2, 1, 'Hoàng Long', 5, 'Bút viết êm tay, mau khô không bị lem tay khi viết bài nhiều.', '2026-08-16 16:40:00'),
(3, 6, 'Mai Phương', 5, 'Bìa da mềm mịn, còng mở êm và chắc chắn. Shop gói hàng rất cẩn thận!', '2026-08-17 09:15:00'),
(4, 23, 'Văn Nam', 5, 'Máy tính Casio chính hãng, bấm nảy, nhiều chức năng giải toán nhanh.', '2026-08-18 14:00:00'),
(5, 29, 'Thu Thảo', 5, 'Ba lô màu đẹp, vải xịn chống nước tốt, đeo rất êm vai không bị mỏi lưng.', '2026-08-19 18:30:00');

-- Contacts
INSERT INTO `contacts` (`id`, `fullname`, `email`, `phone`, `subject`, `message`, `status`, `created_at`) VALUES
(1, 'Đặng Minh Châu', 'minhchau@gmail.com', '0978111222', 'Tư vấn mua sỉ đồ dùng học tập', 'Chào shop, mình muốn đặt mua 50 bộ quà tặng tựu trường cho lớp học, shop có chiết khấu không ạ?', 'new', '2026-08-20 08:00:00');
