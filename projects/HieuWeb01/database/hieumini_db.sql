-- CSDL HieuMini - Website Bán Quần Áo Thời Trang
-- Khởi tạo Database và các Bảng chức năng

CREATE DATABASE IF NOT EXISTS `hieumini_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hieumini_db`;

-- 1. Bảng Người dùng (Users)
CREATE TABLE IF NOT EXISTS `users` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `full_name` VARCHAR(100) NOT NULL,
    `email` VARCHAR(100) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `phone` VARCHAR(20) DEFAULT NULL,
    `address` TEXT DEFAULT NULL,
    `role` ENUM('admin', 'customer') DEFAULT 'customer',
    `avatar` VARCHAR(255) DEFAULT 'default_avatar.png',
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng Danh mục sản phẩm (Categories)
CREATE TABLE IF NOT EXISTS `categories` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `name` VARCHAR(100) NOT NULL,
    `slug` VARCHAR(100) NOT NULL UNIQUE,
    `description` TEXT DEFAULT NULL,
    `image` VARCHAR(255) DEFAULT NULL,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng Sản phẩm (Products)
CREATE TABLE IF NOT EXISTS `products` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `category_id` INT NOT NULL,
    `name` VARCHAR(200) NOT NULL,
    `slug` VARCHAR(200) NOT NULL UNIQUE,
    `sku` VARCHAR(50) NOT NULL UNIQUE,
    `price` DECIMAL(12,2) NOT NULL,
    `discount_price` DECIMAL(12,2) DEFAULT NULL,
    `stock` INT DEFAULT 50,
    `sizes` VARCHAR(100) DEFAULT 'S,M,L,XL',
    `colors` VARCHAR(100) DEFAULT 'Trắng,Đen,Xanh,Be',
    `description` TEXT DEFAULT NULL,
    `content` LONGTEXT DEFAULT NULL,
    `image` VARCHAR(255) NOT NULL,
    `featured` TINYINT(1) DEFAULT 0,
    `status` TINYINT(1) DEFAULT 1,
    `view_count` INT DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. Bảng Mã giảm giá (Coupons)
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `code` VARCHAR(50) NOT NULL UNIQUE,
    `discount_type` ENUM('percentage', 'fixed') DEFAULT 'percentage',
    `discount_value` DECIMAL(10,2) NOT NULL,
    `min_order_amount` DECIMAL(12,2) DEFAULT 0,
    `usage_limit` INT DEFAULT 100,
    `used_count` INT DEFAULT 0,
    `expiry_date` DATE DEFAULT NULL,
    `status` TINYINT(1) DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng Đơn hàng (Orders)
CREATE TABLE IF NOT EXISTS `orders` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `user_id` INT DEFAULT NULL,
    `order_code` VARCHAR(30) NOT NULL UNIQUE,
    `customer_name` VARCHAR(100) NOT NULL,
    `customer_phone` VARCHAR(20) NOT NULL,
    `customer_email` VARCHAR(100) NOT NULL,
    `shipping_address` TEXT NOT NULL,
    `payment_method` ENUM('cod', 'banking', 'vnpay', 'momo') DEFAULT 'cod',
    `payment_status` ENUM('unpaid', 'paid') DEFAULT 'unpaid',
    `order_status` ENUM('pending', 'processing', 'shipping', 'completed', 'cancelled') DEFAULT 'pending',
    `total_amount` DECIMAL(12,2) NOT NULL,
    `discount_amount` DECIMAL(12,2) DEFAULT 0,
    `shipping_fee` DECIMAL(12,2) DEFAULT 30000,
    `coupon_code` VARCHAR(50) DEFAULT NULL,
    `notes` TEXT DEFAULT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng Chi tiết Đơn hàng (Order Items)
CREATE TABLE IF NOT EXISTS `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NOT NULL,
    `product_name` VARCHAR(200) NOT NULL,
    `price` DECIMAL(12,2) NOT NULL,
    `quantity` INT NOT NULL,
    `size` VARCHAR(20) DEFAULT 'M',
    `color` VARCHAR(50) DEFAULT 'Đen',
    `subtotal` DECIMAL(12,2) NOT NULL,
    CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Bảng Đánh giá sản phẩm (Reviews)
CREATE TABLE IF NOT EXISTS `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `user_id` INT NOT NULL,
    `user_name` VARCHAR(100) NOT NULL,
    `rating` TINYINT NOT NULL DEFAULT 5,
    `comment` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Thêm tài khoản quản trị mặc định (Mật khẩu: admin123)
-- Hash tạo bởi password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `role`) VALUES
(1, 'Admin HieuMini', 'admin@hieumini.vn', '$2y$10$eEskGf1Z3z15i1ZzU/kLw.5x8X/0R.hBvM6h76Yq/YJz31X7PZ.Gy', '0988889999', 'Hà Nội, Việt Nam', 'admin'),
(2, 'Nguyễn Văn Nam', 'khachhang@gmail.com', '$2y$10$eEskGf1Z3z15i1ZzU/kLw.5x8X/0R.hBvM6h76Yq/YJz31X7PZ.Gy', '0912345678', 'Cầu Giấy, Hà Nội', 'customer')
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`);

-- Thêm Danh mục mẫu
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `image`, `status`) VALUES
(1, 'Áo Thun & Polo', 'ao-thun-polo', 'Bộ sưu tập áo thun Unisex, áo Polo phong cách năng động, cotton thoáng mát', 'cat_ao_thun.jpg', 1),
(2, 'Áo Sơ Mi Cao Cấp', 'ao-so-mi', 'Áo sơ mi Oxford, sơ mi lụa công sở và dạo phố lịch lãm', 'cat_ao_somi.jpg', 1),
(3, 'Áo Khoác & Hoodie', 'ao-khoac-hoodie', 'Áo khoác Bomber, Varsity jacket, Hoodie nỉ bông ấm áp thời thượng', 'cat_ao_khoac.jpg', 1),
(4, 'Quần Jeans & Denim', 'quan-jeans', 'Quần Jean Slimfit, Jean ống rộng Baggy, Denim wash cao cấp', 'cat_quan_jeans.jpg', 1),
(5, 'Quần Kaki & Trousers', 'quan-kaki', 'Quần Kaki Chino, quần tây âu dáng suông thanh lịch công sở', 'cat_quan_kaki.jpg', 1),
(6, 'Váy & Đầm Nữ', 'vay-dam-nu', 'Váy hoa nhí Vintage, đầm suông, chân váy chữ A phong cách Hàn Quốc', 'cat_vay_dam.jpg', 1),
(7, 'Phụ Kiện Thời Trang', 'phu-kien', 'Thắt lưng da, nón kết lưỡi trai, túi đeo chéo, vớ thời trang', 'cat_phu_kien.jpg', 1)
ON DUPLICATE KEY UPDATE `name` = VALUES(`name`);

-- Thêm Mã giảm giá
INSERT INTO `coupons` (`id`, `code`, `discount_type`, `discount_value`, `min_order_amount`, `usage_limit`, `expiry_date`, `status`) VALUES
(1, 'HIEUMINI10', 'percentage', 10.00, 200000, 200, '2026-12-31', 1),
(2, 'FREESHIP', 'fixed', 30000, 300000, 500, '2026-12-31', 1),
(3, 'WELCOME50K', 'fixed', 50000, 400000, 100, '2026-12-31', 1)
ON DUPLICATE KEY UPDATE `code` = VALUES(`code`);
