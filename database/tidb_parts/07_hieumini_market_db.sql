-- ======================================================================
--  Cơ sở dữ liệu: hieumini_market_db
--  Dán CẢ tệp vào TiDB SQL Editor rồi bấm Run.
--  Không cần chọn database — mọi bảng đã gắn sẵn tên CSDL.
-- ======================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


-- =====================================================================
--  HIEUMINI - CHO DU AN WEBSITE (Website Project Marketplace)
--  Cơ sở dữ liệu MySQL 8.0 / MariaDB 10.4+
--  Tác giả: Trần Văn Minh Hiếu
--  Charset: utf8mb4_unicode_ci (hỗ trợ đầy đủ tiếng Việt & emoji)
-- =====================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
SET time_zone = '+07:00';

CREATE DATABASE IF NOT EXISTS `hieumini_market_db`
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- 1. BẢNG users - Người dùng & Quản trị viên
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`users`;
CREATE TABLE `hieumini_market_db`.`users` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `full_name`   VARCHAR(120)  NOT NULL,
  `email`       VARCHAR(160)  NOT NULL,
  `password`    VARCHAR(255)  NOT NULL COMMENT 'Băm bằng password_hash() BCRYPT',
  `phone`       VARCHAR(20)   DEFAULT NULL,
  `avatar`      VARCHAR(255)  DEFAULT NULL,
  `role`        ENUM('user','admin') NOT NULL DEFAULT 'user',
  `status`      TINYINT(1)    NOT NULL DEFAULT 1 COMMENT '1=hoạt động, 0=khoá',
  `last_login`  DATETIME      DEFAULT NULL,
  `created_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`  DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_users_email` (`email`),
  KEY `idx_users_role` (`role`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 2. BẢNG categories - Danh mục dự án
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`categories`;
CREATE TABLE `hieumini_market_db`.`categories` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`        VARCHAR(120) NOT NULL,
  `slug`        VARCHAR(150) NOT NULL,
  `description` VARCHAR(255) DEFAULT NULL,
  `icon`        VARCHAR(60)  DEFAULT NULL COMMENT 'Tên icon SVG nội bộ',
  `sort_order`  INT          NOT NULL DEFAULT 0,
  `status`      TINYINT(1)   NOT NULL DEFAULT 1,
  `created_at`  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_categories_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 3. BẢNG projects - Sản phẩm (mã nguồn website được bán)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`projects`;
CREATE TABLE `hieumini_market_db`.`projects` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `category_id`      INT UNSIGNED NOT NULL,
  `title`            VARCHAR(200)  NOT NULL,
  `slug`             VARCHAR(220)  NOT NULL,
  `short_desc`       VARCHAR(300)  DEFAULT NULL,
  `description`      MEDIUMTEXT    DEFAULT NULL,
  `features`         TEXT          DEFAULT NULL COMMENT 'Mỗi tính năng 1 dòng',
  `tech_stack`       VARCHAR(255)  DEFAULT NULL COMMENT 'Ngăn cách bởi dấu phẩy',
  `price`            DECIMAL(12,0) NOT NULL DEFAULT 0,
  `sale_price`       DECIMAL(12,0) DEFAULT NULL COMMENT 'Giá khuyến mãi, NULL = không giảm',
  `thumbnail`        VARCHAR(255)  DEFAULT NULL,
  `demo_url`         VARCHAR(255)  DEFAULT NULL,
  `badge`            VARCHAR(40)   DEFAULT NULL COMMENT 'HOT / NEW / BEST SELLER',
  `is_featured`      TINYINT(1)    NOT NULL DEFAULT 0,
  `views`            INT UNSIGNED  NOT NULL DEFAULT 0,
  `sales`            INT UNSIGNED  NOT NULL DEFAULT 0,
  `rating_avg`       DECIMAL(3,2)  NOT NULL DEFAULT 0.00,
  `rating_count`     INT UNSIGNED  NOT NULL DEFAULT 0,
  `status`           TINYINT(1)    NOT NULL DEFAULT 1,
  `meta_title`       VARCHAR(180)  DEFAULT NULL,
  `meta_description` VARCHAR(300)  DEFAULT NULL,
  `meta_keywords`    VARCHAR(255)  DEFAULT NULL,
  `created_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`       DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_projects_slug` (`slug`),
  KEY `idx_projects_category` (`category_id`),
  KEY `idx_projects_status_featured` (`status`,`is_featured`),
  CONSTRAINT `fk_projects_category` FOREIGN KEY (`category_id`)
      REFERENCES `hieumini_market_db`.`categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. BẢNG project_images - Thư viện ảnh của từng dự án
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`project_images`;
CREATE TABLE `hieumini_market_db`.`project_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `alt_text`   VARCHAR(180) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_pimages_project` (`project_id`),
  CONSTRAINT `fk_pimages_project` FOREIGN KEY (`project_id`)
      REFERENCES `hieumini_market_db`.`projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. BẢNG coupons - Mã giảm giá
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`coupons`;
CREATE TABLE `hieumini_market_db`.`coupons` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`          VARCHAR(40)  NOT NULL,
  `type`          ENUM('percent','fixed') NOT NULL DEFAULT 'percent',
  `value`         DECIMAL(12,0) NOT NULL,
  `min_total`     DECIMAL(12,0) NOT NULL DEFAULT 0,
  `usage_limit`   INT UNSIGNED NOT NULL DEFAULT 100,
  `used_count`    INT UNSIGNED NOT NULL DEFAULT 0,
  `expires_at`    DATE DEFAULT NULL,
  `status`        TINYINT(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_coupons_code` (`code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 6. BẢNG orders - Đơn hàng
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`orders`;
CREATE TABLE `hieumini_market_db`.`orders` (
  `id`             INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_code`     VARCHAR(30)  NOT NULL,
  `user_id`        INT UNSIGNED DEFAULT NULL COMMENT 'NULL = khách vãng lai',
  `customer_name`  VARCHAR(120) NOT NULL,
  `email`          VARCHAR(160) NOT NULL,
  `phone`          VARCHAR(20)  NOT NULL,
  `note`           VARCHAR(500) DEFAULT NULL,
  `payment_method` ENUM('bank','momo','cod') NOT NULL DEFAULT 'bank',
  `coupon_code`    VARCHAR(40)  DEFAULT NULL,
  `subtotal`       DECIMAL(12,0) NOT NULL DEFAULT 0,
  `discount`       DECIMAL(12,0) NOT NULL DEFAULT 0,
  `total`          DECIMAL(12,0) NOT NULL DEFAULT 0,
  `status`         ENUM('pending','paid','delivered','cancelled') NOT NULL DEFAULT 'pending',
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_orders_code` (`order_code`),
  KEY `idx_orders_user` (`user_id`),
  KEY `idx_orders_status` (`status`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`)
      REFERENCES `hieumini_market_db`.`users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. BẢNG order_items - Chi tiết đơn hàng
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`order_items`;
CREATE TABLE `hieumini_market_db`.`order_items` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `order_id`   INT UNSIGNED NOT NULL,
  `project_id` INT UNSIGNED DEFAULT NULL,
  `title`      VARCHAR(200) NOT NULL COMMENT 'Lưu lại tên tại thời điểm mua',
  `license`    ENUM('personal','commercial','extended') NOT NULL DEFAULT 'personal',
  `price`      DECIMAL(12,0) NOT NULL,
  `quantity`   INT UNSIGNED NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `idx_oitems_order` (`order_id`),
  CONSTRAINT `fk_oitems_order` FOREIGN KEY (`order_id`)
      REFERENCES `hieumini_market_db`.`orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_oitems_project` FOREIGN KEY (`project_id`)
      REFERENCES `hieumini_market_db`.`projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. BẢNG reviews - Đánh giá của khách hàng
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`reviews`;
CREATE TABLE `hieumini_market_db`.`reviews` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT UNSIGNED NOT NULL,
  `user_id`    INT UNSIGNED NOT NULL,
  `rating`     TINYINT UNSIGNED NOT NULL DEFAULT 5,
  `content`    VARCHAR(1000) DEFAULT NULL,
  `status`     TINYINT(1) NOT NULL DEFAULT 1 COMMENT '1=hiển thị, 0=chờ duyệt',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_review_user_project` (`project_id`,`user_id`),
  KEY `idx_reviews_project` (`project_id`),
  CONSTRAINT `fk_reviews_project` FOREIGN KEY (`project_id`)
      REFERENCES `hieumini_market_db`.`projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`)
      REFERENCES `hieumini_market_db`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. BẢNG wishlists - Danh sách yêu thích
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`wishlists`;
CREATE TABLE `hieumini_market_db`.`wishlists` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `project_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wishlist` (`user_id`,`project_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`)
      REFERENCES `hieumini_market_db`.`users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_wishlist_project` FOREIGN KEY (`project_id`)
      REFERENCES `hieumini_market_db`.`projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. BẢNG posts - Bài viết blog (phục vụ SEO Content Marketing)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`posts`;
CREATE TABLE `hieumini_market_db`.`posts` (
  `id`               INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `title`            VARCHAR(220) NOT NULL,
  `slug`             VARCHAR(240) NOT NULL,
  `excerpt`          VARCHAR(400) DEFAULT NULL,
  `content`          MEDIUMTEXT   DEFAULT NULL,
  `thumbnail`        VARCHAR(255) DEFAULT NULL,
  `author`           VARCHAR(120) NOT NULL DEFAULT 'HieuMini Team',
  `tags`             VARCHAR(255) DEFAULT NULL,
  `views`            INT UNSIGNED NOT NULL DEFAULT 0,
  `status`           TINYINT(1) NOT NULL DEFAULT 1,
  `meta_title`       VARCHAR(180) DEFAULT NULL,
  `meta_description` VARCHAR(300) DEFAULT NULL,
  `published_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_posts_slug` (`slug`),
  KEY `idx_posts_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 11. BẢNG contacts - Liên hệ / Yêu cầu báo giá
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`contacts`;
CREATE TABLE `hieumini_market_db`.`contacts` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(160) NOT NULL,
  `phone`      VARCHAR(20)  DEFAULT NULL,
  `subject`    VARCHAR(200) DEFAULT NULL,
  `message`    TEXT NOT NULL,
  `status`     ENUM('new','processing','done') NOT NULL DEFAULT 'new',
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_contacts_status` (`status`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 12. BẢNG settings - Cấu hình hệ thống & SEO
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_market_db`.`settings`;
CREATE TABLE `hieumini_market_db`.`settings` (
  `setting_key`   VARCHAR(80) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `group_name`    VARCHAR(40) NOT NULL DEFAULT 'general',
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  DỮ LIỆU MẪU
-- =====================================================================

-- Tài khoản: admin@hieumini.vn / admin123  |  user@hieumini.vn / user123
INSERT INTO `hieumini_market_db`.`users` (`full_name`,`email`,`password`,`phone`,`role`) VALUES
('Trần Văn Minh Hiếu','admin@hieumini.vn','$2y$12$o6aJxBjBSABkZOZ02k.FoO8ifhh9vtoqXxkPLgntbcJkXLVhBNiDi','0987654321','admin'),
('Nguyễn Khách Hàng','user@hieumini.vn','$2y$12$6V4uXWgKwqef5jt7R9si/u7SnTr5C8pytvAOA9lCS/UcvbudGz.E6','0912345678','user'),
('Lê Thu Trang','trang.le@example.com','$2y$12$6V4uXWgKwqef5jt7R9si/u7SnTr5C8pytvAOA9lCS/UcvbudGz.E6','0933444555','user'),
('Phạm Quốc Đạt','dat.pham@example.com','$2y$12$6V4uXWgKwqef5jt7R9si/u7SnTr5C8pytvAOA9lCS/UcvbudGz.E6','0977888999','user');

INSERT INTO `hieumini_market_db`.`categories` (`name`,`slug`,`description`,`icon`,`sort_order`) VALUES
('Thương mại điện tử','thuong-mai-dien-tu','Website bán hàng, giỏ hàng, thanh toán, quản trị đơn hàng','cart',1),
('Doanh nghiệp & Dịch vụ','doanh-nghiep-dich-vu','Website giới thiệu công ty, dịch vụ, landing page chuyển đổi cao','building',2),
('Portfolio & Cá nhân','portfolio-ca-nhan','Hồ sơ năng lực, CV trực tuyến, blog cá nhân','user',3),
('Quản lý & Dashboard','quan-ly-dashboard','Hệ thống quản lý nội bộ, báo cáo, thống kê','chart',4),
('Giáo dục & Khoá học','giao-duc-khoa-hoc','Website trung tâm, LMS, thi trắc nghiệm trực tuyến','book',5),
('Du lịch & Nhà hàng','du-lich-nha-hang','Đặt phòng, đặt bàn, thực đơn, tour du lịch','globe',6);

INSERT INTO `hieumini_market_db`.`projects`
(`category_id`,`title`,`slug`,`short_desc`,`description`,`features`,`tech_stack`,`price`,`sale_price`,`thumbnail`,`demo_url`,`badge`,`is_featured`,`views`,`sales`,`rating_avg`,`rating_count`,`meta_title`,`meta_description`,`meta_keywords`) VALUES
(1,'HieuShop Pro - Website Bán Hàng Đa Ngành','hieushop-pro-website-ban-hang-da-nganh','Mã nguồn website thương mại điện tử PHP thuần, đầy đủ giỏ hàng, thanh toán và trang quản trị.','Bộ mã nguồn thương mại điện tử hoàn chỉnh được viết bằng PHP 8 thuần và MySQL, không phụ thuộc framework nên dễ đọc, dễ chỉnh sửa và phù hợp cho đồ án tốt nghiệp lẫn dự án thực tế. Toàn bộ truy vấn sử dụng PDO Prepared Statement, mật khẩu băm BCRYPT, biểu mẫu có CSRF token. Giao diện responsive chuẩn mobile-first, tối ưu Core Web Vitals và schema Product cho Google Rich Results.','Giỏ hàng AJAX không tải lại trang\nThanh toán mô phỏng: chuyển khoản, MoMo, COD\nQuản lý sản phẩm, danh mục, đơn hàng, khách hàng\nBộ lọc đa tiêu chí + tìm kiếm Fulltext\nMã giảm giá và tính phí vận chuyển\nBáo cáo doanh thu theo ngày/tháng','PHP 8.2, MySQL 8, PDO, JavaScript ES6, CSS Grid',2490000,1890000,'assets/images/projects/hieushop-pro.svg','#','BEST SELLER',1,4820,213,4.90,42,'HieuShop Pro - Mã nguồn website bán hàng PHP MySQL đầy đủ','Tải mã nguồn website bán hàng PHP MySQL đầy đủ giỏ hàng, thanh toán, quản trị. Code sạch, bảo mật PDO, chuẩn SEO, có tài liệu hướng dẫn.','website bán hàng php, mã nguồn ecommerce, source code php mysql'),
(1,'MiniMart - Siêu Thị Mini Online','minimart-sieu-thi-mini-online','Website siêu thị mini với quản lý tồn kho theo thời gian thực và in hoá đơn.','MiniMart hướng tới các cửa hàng tạp hoá, siêu thị mini muốn bán hàng online song song với bán tại quầy. Hệ thống đồng bộ tồn kho giữa hai kênh, hỗ trợ quét mã vạch, in hoá đơn khổ K80 và quản lý ca bán hàng của nhân viên.','Đồng bộ tồn kho online - tại quầy\nQuét mã vạch bằng camera điện thoại\nIn hoá đơn nhiệt K80\nQuản lý ca làm việc nhân viên\nCảnh báo hàng sắp hết và hàng cận date','PHP 8.2, MySQL 8, Chart.js, Bootstrap 5',1990000,NULL,'assets/images/projects/minimart.svg','#','HOT',1,3140,158,4.70,31,'MiniMart - Mã nguồn website siêu thị mini PHP MySQL','Mã nguồn website siêu thị mini quản lý tồn kho thời gian thực, quét mã vạch, in hoá đơn K80. Viết bằng PHP 8 và MySQL.','website siêu thị mini, quản lý tồn kho php, source code bán hàng'),
(1,'FashionHub - Thời Trang Cao Cấp','fashionhub-thoi-trang-cao-cap','Website thời trang với bộ lọc theo size, màu sắc và gợi ý phối đồ thông minh.','FashionHub được thiết kế cho thương hiệu thời trang muốn có trải nghiệm mua sắm cao cấp. Trang chi tiết sản phẩm hỗ trợ zoom ảnh, chọn nhanh biến thể size - màu, bảng quy đổi size và gợi ý phối đồ dựa trên lịch sử xem.','Biến thể sản phẩm size - màu\nZoom ảnh độ phân giải cao\nBảng quy đổi size quốc tế\nGợi ý phối đồ theo hành vi\nTích hợp lookbook theo mùa','PHP 8.2, MySQL 8, GSAP, Swiper.js',2290000,1790000,'assets/images/projects/fashionhub.svg','#',NULL,0,2260,96,4.60,22,'FashionHub - Mã nguồn website thời trang PHP chuẩn SEO','Source code website thời trang PHP MySQL có biến thể size màu, zoom ảnh, lookbook và tối ưu SEO cho ngành thời trang.','website thời trang php, source code shop quần áo'),
(2,'CorpVision - Website Doanh Nghiệp Chuẩn SEO','corpvision-website-doanh-nghiep-chuan-seo','Website giới thiệu công ty đa ngôn ngữ, tối ưu điểm PageSpeed trên 95.','CorpVision là bộ khung website doanh nghiệp chuyên nghiệp: trang chủ kể chuyện thương hiệu, trang dịch vụ, dự án tiêu biểu, tin tức và tuyển dụng. Toàn bộ nội dung quản trị được qua trang admin, hỗ trợ hai ngôn ngữ Việt - Anh, sinh sitemap.xml tự động và khai báo schema Organization.','Đa ngôn ngữ Việt - Anh\nSitemap.xml và robots.txt tự động\nSchema Organization và BreadcrumbList\nTrang tuyển dụng và nộp CV trực tuyến\nĐiểm PageSpeed Desktop trên 95','PHP 8.2, MySQL 8, Vanilla JS, WebP',1690000,1290000,'assets/images/projects/corpvision.svg','#','NEW',1,3980,177,4.80,38,'CorpVision - Mã nguồn website doanh nghiệp chuẩn SEO PHP','Mã nguồn website giới thiệu công ty đa ngôn ngữ, chuẩn SEO, PageSpeed trên 95, quản trị nội dung đầy đủ bằng PHP MySQL.','website doanh nghiệp php, website công ty chuẩn seo'),
(2,'LandingX - Landing Page Chuyển Đổi Cao','landingx-landing-page-chuyen-doi-cao','Bộ 8 mẫu landing page kèm form thu lead và A/B testing đơn giản.','LandingX cung cấp 8 mẫu landing page cho các ngành khác nhau, mỗi mẫu đều có form thu lead lưu vào cơ sở dữ liệu, gửi email xác nhận, tích hợp Google Analytics 4 và Facebook Pixel. Có sẵn cơ chế A/B testing để so sánh hai phiên bản tiêu đề.','8 mẫu landing page sẵn sàng dùng\nForm thu lead lưu CSDL và gửi email\nA/B testing tiêu đề và nút CTA\nĐếm ngược khuyến mãi thời gian thực\nTích hợp GA4 và Facebook Pixel','PHP 8.2, MySQL 8, PHPMailer, GA4',990000,NULL,'assets/images/projects/landingx.svg','#',NULL,0,2740,204,4.50,27,'LandingX - Bộ mẫu landing page PHP thu lead chuyển đổi cao','8 mẫu landing page PHP MySQL kèm form thu lead, A/B testing, đếm ngược khuyến mãi và tích hợp GA4.','landing page php, mẫu landing page chuyển đổi'),
(2,'ClinicCare - Website Phòng Khám Đặt Lịch','cliniccare-website-phong-kham-dat-lich','Website phòng khám với đặt lịch trực tuyến và nhắc hẹn tự động.','ClinicCare giúp phòng khám nhận đặt lịch trực tuyến 24/7. Bệnh nhân chọn chuyên khoa, bác sĩ và khung giờ còn trống, hệ thống tự khoá slot và gửi email nhắc hẹn trước 24 giờ. Trang quản trị có lịch tuần dạng kéo thả.','Đặt lịch theo khung giờ còn trống\nNhắc hẹn tự động qua email\nHồ sơ bác sĩ và chuyên khoa\nLịch tuần kéo thả cho quản trị\nBáo cáo lượt khám theo chuyên khoa','PHP 8.2, MySQL 8, FullCalendar, PHPMailer',1890000,1490000,'assets/images/projects/cliniccare.svg','#',NULL,0,1620,71,4.60,18,'ClinicCare - Mã nguồn website phòng khám đặt lịch PHP MySQL','Source code website phòng khám PHP MySQL: đặt lịch trực tuyến, nhắc hẹn email, quản lý bác sĩ và chuyên khoa.','website phòng khám php, đặt lịch khám online'),
(3,'DevFolio - Portfolio Lập Trình Viên','devfolio-portfolio-lap-trinh-vien','Portfolio hiệu ứng cao cấp cho lập trình viên, tích hợp GitHub API.','DevFolio là mẫu hồ sơ năng lực dành cho lập trình viên với hiệu ứng cuộn mượt, dòng thời gian kinh nghiệm, biểu đồ kỹ năng và khối dự án tự động lấy từ GitHub API. Có chế độ sáng - tối và trang blog kỹ thuật hỗ trợ Markdown.','Đồng bộ dự án từ GitHub API\nHiệu ứng cuộn và con trỏ tuỳ biến\nChế độ sáng - tối lưu localStorage\nBlog kỹ thuật viết bằng Markdown\nXuất CV dạng PDF một chạm','PHP 8.2, MySQL 8, GSAP, Parsedown',790000,590000,'assets/images/projects/devfolio.svg','#','HOT',1,5210,286,4.90,55,'DevFolio - Mẫu portfolio lập trình viên PHP tích hợp GitHub','Mẫu website portfolio lập trình viên hiệu ứng cao cấp, tích hợp GitHub API, blog Markdown, xuất CV PDF.','portfolio lập trình viên, mẫu cv online, website cá nhân php'),
(3,'PhotoLens - Portfolio Nhiếp Ảnh','photolens-portfolio-nhiep-anh','Thư viện ảnh masonry, lightbox mượt và bảo vệ ảnh gốc bằng watermark.','PhotoLens dành cho nhiếp ảnh gia muốn trình bày bộ sưu tập theo album. Ảnh được tải chậm theo cuộn, hiển thị dạng masonry, xem lớn bằng lightbox có phím tắt, đồng thời tự động đóng dấu chìm để bảo vệ bản quyền.','Bố cục masonry tải chậm\nLightbox điều khiển bằng phím tắt\nWatermark tự động khi tải lên\nAlbum theo sự kiện và khách hàng\nBiểu mẫu báo giá chụp ảnh','PHP 8.2, MySQL 8, GD Library, Intersection Observer',690000,NULL,'assets/images/projects/photolens.svg','#',NULL,0,1480,63,4.40,14,'PhotoLens - Mã nguồn portfolio nhiếp ảnh PHP có watermark','Website portfolio nhiếp ảnh PHP MySQL với thư viện masonry, lightbox, watermark tự động bảo vệ bản quyền ảnh.','portfolio nhiếp ảnh, website ảnh php'),
(4,'AdminForge - Bộ Khung Quản Trị','adminforge-bo-khung-quan-tri','Khung quản trị PHP với phân quyền RBAC, nhật ký thao tác và 20 thành phần giao diện.','AdminForge là bộ khung quản trị sẵn sàng dùng lại cho mọi dự án PHP. Hệ thống phân quyền theo vai trò và quyền chi tiết, ghi nhật ký mọi thao tác quan trọng, có sẵn 20 thành phần giao diện như bảng dữ liệu, biểu đồ, modal, toast, bộ chọn ngày.','Phân quyền RBAC theo vai trò và quyền\nNhật ký thao tác kèm địa chỉ IP\n20 thành phần giao diện dùng lại\nBảng dữ liệu lọc - sắp xếp - xuất Excel\nXác thực hai lớp qua email','PHP 8.2, MySQL 8, Chart.js, DataTables',2890000,2290000,'assets/images/projects/adminforge.svg','#','BEST SELLER',1,3660,142,4.80,33,'AdminForge - Bộ khung quản trị PHP RBAC đầy đủ thành phần','Khung quản trị PHP MySQL với phân quyền RBAC, nhật ký thao tác, bảng dữ liệu, biểu đồ và 20 thành phần giao diện.','admin template php, phân quyền rbac php, dashboard php'),
(4,'StockFlow - Quản Lý Kho Hàng','stockflow-quan-ly-kho-hang','Phần mềm quản lý kho nhiều chi nhánh với phiếu nhập, xuất và kiểm kê.','StockFlow quản lý hàng hoá cho doanh nghiệp có nhiều kho. Hệ thống theo dõi phiếu nhập, phiếu xuất, phiếu chuyển kho, kiểm kê định kỳ và tính giá vốn bình quân gia quyền. Báo cáo tồn kho có thể xuất Excel.','Quản lý nhiều kho và chuyển kho\nPhiếu nhập - xuất - kiểm kê\nTính giá vốn bình quân gia quyền\nCảnh báo tồn tối thiểu\nXuất báo cáo Excel và PDF','PHP 8.2, MySQL 8, PhpSpreadsheet',2590000,NULL,'assets/images/projects/stockflow.svg','#',NULL,0,1930,74,4.50,19,'StockFlow - Mã nguồn quản lý kho hàng PHP MySQL nhiều chi nhánh','Phần mềm quản lý kho PHP MySQL: phiếu nhập xuất, kiểm kê, giá vốn bình quân, báo cáo tồn kho xuất Excel.','quản lý kho php, phần mềm kho mysql'),
(4,'HR Insight - Quản Lý Nhân Sự','hr-insight-quan-ly-nhan-su','Hệ thống nhân sự với chấm công, tính lương và đánh giá KPI.','HR Insight số hoá quy trình nhân sự: hồ sơ nhân viên, hợp đồng, chấm công theo ca, tính lương tự động kèm bảo hiểm và thuế thu nhập cá nhân, đánh giá KPI theo chu kỳ. Nhân viên có cổng tự phục vụ để xem phiếu lương và xin nghỉ phép.','Chấm công theo ca và vân tay\nTính lương, bảo hiểm, thuế TNCN\nĐánh giá KPI theo chu kỳ\nCổng tự phục vụ cho nhân viên\nQuản lý hợp đồng và nghỉ phép','PHP 8.2, MySQL 8, Chart.js, TCPDF',3290000,2690000,'assets/images/projects/hrinsight.svg','#',NULL,0,1410,52,4.70,15,'HR Insight - Mã nguồn quản lý nhân sự PHP chấm công tính lương','Hệ thống quản lý nhân sự PHP MySQL: chấm công, tính lương, bảo hiểm, thuế TNCN và đánh giá KPI.','quản lý nhân sự php, phần mềm chấm công tính lương'),
(5,'EduLearn - Hệ Thống Học Trực Tuyến','edulearn-he-thong-hoc-truc-tuyen','Nền tảng LMS với bài giảng video, bài kiểm tra và chứng chỉ tự động.','EduLearn là nền tảng học trực tuyến hoàn chỉnh: giảng viên tạo khoá học nhiều chương, tải video bài giảng, tạo ngân hàng câu hỏi và bài kiểm tra tự động chấm điểm. Học viên theo dõi tiến độ, làm bài tập, nhận chứng chỉ PDF khi hoàn thành.','Khoá học nhiều chương và bài học\nBài kiểm tra trắc nghiệm tự chấm\nTheo dõi tiến độ và điểm danh\nChứng chỉ PDF tự động\nThảo luận hỏi đáp theo bài học','PHP 8.2, MySQL 8, Video.js, TCPDF',3490000,2890000,'assets/images/projects/edulearn.svg','#','HOT',1,4310,163,4.80,41,'EduLearn - Mã nguồn website học trực tuyến LMS PHP MySQL','Nền tảng học trực tuyến PHP MySQL với video bài giảng, thi trắc nghiệm tự chấm, chứng chỉ PDF và theo dõi tiến độ.','website học trực tuyến, lms php, thi trắc nghiệm online'),
(5,'QuizMaster - Thi Trắc Nghiệm Trực Tuyến','quizmaster-thi-trac-nghiem-truc-tuyen','Hệ thống thi trắc nghiệm có đếm giờ, trộn đề và chống gian lận.','QuizMaster phù hợp cho trung tâm và nhà trường tổ chức thi trên máy. Đề thi được trộn ngẫu nhiên từ ngân hàng câu hỏi, có đếm giờ, tự nộp bài khi hết giờ, ghi nhận hành vi chuyển tab để hạn chế gian lận và thống kê phổ điểm sau kỳ thi.','Trộn đề từ ngân hàng câu hỏi\nĐếm giờ và tự nộp bài\nPhát hiện chuyển tab khi thi\nThống kê phổ điểm và độ khó câu hỏi\nNhập câu hỏi hàng loạt từ Excel','PHP 8.2, MySQL 8, PhpSpreadsheet, Chart.js',1590000,1190000,'assets/images/projects/quizmaster.svg','#',NULL,0,2870,131,4.60,29,'QuizMaster - Mã nguồn thi trắc nghiệm trực tuyến PHP MySQL','Website thi trắc nghiệm online PHP MySQL: trộn đề, đếm giờ, chống gian lận, thống kê phổ điểm, nhập đề từ Excel.','thi trắc nghiệm online, phần mềm thi php'),
(6,'StayBooking - Đặt Phòng Khách Sạn','staybooking-dat-phong-khach-san','Website khách sạn với sơ đồ phòng trống theo ngày và đặt phòng trực tuyến.','StayBooking cho phép khách chọn ngày nhận - trả phòng, xem sơ đồ phòng trống theo lịch, so sánh hạng phòng và đặt cọc trực tuyến. Quản trị viên theo dõi công suất phòng, giá theo mùa và danh sách khách đến trong ngày.','Lịch phòng trống theo ngày\nGiá linh hoạt theo mùa và cuối tuần\nĐặt cọc trực tuyến và xác nhận email\nBáo cáo công suất phòng\nQuản lý dịch vụ đi kèm','PHP 8.2, MySQL 8, FullCalendar, PHPMailer',2790000,2190000,'assets/images/projects/staybooking.svg','#','NEW',1,2050,88,4.70,21,'StayBooking - Mã nguồn website đặt phòng khách sạn PHP MySQL','Source code website khách sạn PHP MySQL: lịch phòng trống, giá theo mùa, đặt cọc online, báo cáo công suất phòng.','website khách sạn php, đặt phòng online'),
(6,'FoodieGo - Nhà Hàng & Đặt Bàn','foodiego-nha-hang-dat-ban','Website nhà hàng với thực đơn ảnh, đặt bàn và đặt món mang về.','FoodieGo trình bày thực đơn theo nhóm món kèm ảnh chất lượng cao, cho phép khách đặt bàn theo khung giờ và đặt món mang về. Bếp nhận đơn theo thời gian thực qua màn hình KDS đơn giản.','Thực đơn theo nhóm món có ảnh\nĐặt bàn theo khung giờ\nĐặt món mang về và giao hàng\nMàn hình bếp KDS thời gian thực\nĐánh giá món ăn của khách','PHP 8.2, MySQL 8, AJAX Polling',1890000,1390000,'assets/images/projects/foodiego.svg','#',NULL,0,1760,79,4.50,17,'FoodieGo - Mã nguồn website nhà hàng đặt bàn PHP MySQL','Website nhà hàng PHP MySQL: thực đơn có ảnh, đặt bàn theo khung giờ, đặt món mang về, màn hình bếp thời gian thực.','website nhà hàng php, đặt bàn online'),
(6,'TravelNest - Tour Du Lịch Trực Tuyến','travelnest-tour-du-lich-truc-tuyen','Website bán tour với lịch trình chi tiết, đặt chỗ và quản lý đoàn khách.','TravelNest giúp công ty lữ hành bán tour trực tuyến. Mỗi tour có lịch trình theo ngày, ảnh điểm đến, chính sách hoàn huỷ và số chỗ còn lại theo từng ngày khởi hành. Quản trị viên quản lý đoàn khách và xuất danh sách.','Lịch trình tour theo từng ngày\nQuản lý ngày khởi hành và số chỗ\nĐặt chỗ và thanh toán đặt cọc\nQuản lý danh sách đoàn khách\nĐánh giá tour sau chuyến đi','PHP 8.2, MySQL 8, Swiper.js, Leaflet',2390000,NULL,'assets/images/projects/travelnest.svg','#',NULL,0,1320,47,4.40,12,'TravelNest - Mã nguồn website bán tour du lịch PHP MySQL','Website bán tour du lịch PHP MySQL với lịch trình chi tiết, quản lý ngày khởi hành, đặt cọc và quản lý đoàn khách.','website du lịch php, bán tour online'),
(1,'GadgetZone - Điện Máy & Công Nghệ','gadgetzone-dien-may-cong-nghe','Website điện máy có so sánh thông số, trả góp và bảo hành điện tử.','GadgetZone tập trung vào ngành điện máy - công nghệ với bảng thông số kỹ thuật chi tiết, công cụ so sánh nhiều sản phẩm cùng lúc, tính trả góp theo kỳ hạn và tra cứu bảo hành bằng số IMEI hoặc số serial.','So sánh thông số nhiều sản phẩm\nTính trả góp theo kỳ hạn\nTra cứu bảo hành điện tử\nBộ lọc theo thông số kỹ thuật\nHỏi đáp sản phẩm từ người mua','PHP 8.2, MySQL 8, Alpine.js',2690000,2090000,'assets/images/projects/gadgetzone.svg','#',NULL,0,2410,101,4.60,24,'GadgetZone - Mã nguồn website điện máy công nghệ PHP MySQL','Website bán điện máy PHP MySQL: so sánh thông số, tính trả góp, tra cứu bảo hành điện tử, bộ lọc kỹ thuật.','website điện máy php, bán điện thoại online'),
(3,'BlogVerse - Blog Cá Nhân Chuẩn SEO','blogverse-blog-ca-nhan-chuan-seo','Blog cá nhân tối ưu tốc độ, hỗ trợ Markdown và bảng mục lục tự động.','BlogVerse là mã nguồn blog nhẹ, điểm PageSpeed gần tuyệt đối. Bài viết soạn bằng Markdown, tự sinh bảng mục lục, tự tạo mô tả meta, hỗ trợ RSS, sitemap và schema Article. Có chế độ đọc tập trung và ước tính thời gian đọc.','Soạn bài bằng Markdown\nBảng mục lục tự động\nRSS, sitemap và schema Article\nChế độ đọc tập trung\nTìm kiếm toàn văn tức thời','PHP 8.2, MySQL 8, Parsedown, Fuse.js',590000,390000,'assets/images/projects/blogverse.svg','#','NEW',0,3120,192,4.70,36,'BlogVerse - Mã nguồn blog cá nhân PHP chuẩn SEO tốc độ cao','Mã nguồn blog cá nhân PHP MySQL hỗ trợ Markdown, mục lục tự động, RSS, sitemap, schema Article và tốc độ tải cực nhanh.','blog cá nhân php, mã nguồn blog chuẩn seo');

INSERT INTO `hieumini_market_db`.`coupons` (`code`,`type`,`value`,`min_total`,`usage_limit`,`expires_at`) VALUES
('HIEUMINI10','percent',10,1000000,200,'2027-12-31'),
('SINHVIEN20','percent',20,500000,500,'2027-12-31'),
('GIAM300K','fixed',300000,2000000,100,'2027-06-30');

INSERT INTO `hieumini_market_db`.`orders` (`order_code`,`user_id`,`customer_name`,`email`,`phone`,`payment_method`,`subtotal`,`discount`,`total`,`status`,`created_at`) VALUES
('HM20260801001',2,'Nguyễn Khách Hàng','user@hieumini.vn','0912345678','bank',1890000,189000,1701000,'delivered','2026-08-01 09:15:00'),
('HM20260805002',3,'Lê Thu Trang','trang.le@example.com','0933444555','momo',590000,0,590000,'paid','2026-08-05 14:40:00'),
('HM20260812003',4,'Phạm Quốc Đạt','dat.pham@example.com','0977888999','bank',2890000,300000,2590000,'pending','2026-08-12 20:05:00');

INSERT INTO `hieumini_market_db`.`order_items` (`order_id`,`project_id`,`title`,`license`,`price`,`quantity`) VALUES
(1,1,'HieuShop Pro - Website Bán Hàng Đa Ngành','commercial',1890000,1),
(2,7,'DevFolio - Portfolio Lập Trình Viên','personal',590000,1),
(3,9,'AdminForge - Bộ Khung Quản Trị','extended',2890000,1);

INSERT INTO `hieumini_market_db`.`reviews` (`project_id`,`user_id`,`rating`,`content`,`created_at`) VALUES
(1,2,5,'Code rất sạch, chú thích tiếng Việt đầy đủ nên mình bàn giao lại cho khách rất nhanh. Phần giỏ hàng AJAX chạy mượt.','2026-08-02 10:20:00'),
(1,3,5,'Mua về làm đồ án tốt nghiệp, thầy đánh giá cao phần bảo mật PDO và CSRF. Rất đáng tiền.','2026-08-06 08:30:00'),
(7,3,5,'Portfolio đẹp, hiệu ứng mượt, tích hợp GitHub API chỉ mất 5 phút cấu hình.','2026-08-07 19:45:00'),
(9,4,5,'Bộ khung quản trị tiết kiệm cho mình cả tuần làm việc. Phân quyền RBAC rõ ràng, dễ mở rộng.','2026-08-13 09:10:00'),
(12,2,4,'LMS đầy đủ tính năng, video chạy ổn. Mong tác giả bổ sung thêm thanh toán quốc tế.','2026-08-15 16:00:00');

INSERT INTO `hieumini_market_db`.`wishlists` (`user_id`,`project_id`) VALUES
(2,4),(2,9),(2,12),(3,1),(3,7),(4,15);

INSERT INTO `hieumini_market_db`.`posts` (`title`,`slug`,`excerpt`,`content`,`thumbnail`,`tags`,`views`,`meta_title`,`meta_description`,`published_at`) VALUES
('7 tiêu chí chọn mua mã nguồn website chất lượng','7-tieu-chi-chon-mua-ma-nguon-website-chat-luong','Mua mã nguồn rẻ nhưng phải sửa lại toàn bộ thì không hề rẻ. Bảy tiêu chí dưới đây giúp bạn nhận diện một bộ mã nguồn thực sự đáng tiền.','Thị trường mã nguồn website hiện nay rất sôi động nhưng chất lượng thì chênh lệch rất lớn. Một bộ mã nguồn tốt không chỉ chạy được mà còn phải dễ đọc, dễ mở rộng và an toàn.\n\nTiêu chí đầu tiên là cấu trúc thư mục rõ ràng. Mã nguồn nên tách bạch phần cấu hình, phần thư viện dùng chung, phần giao diện và phần quản trị. Khi cần sửa một tính năng, bạn phải biết ngay nên mở tệp nào.\n\nTiêu chí thứ hai là bảo mật. Hãy kiểm tra xem truy vấn có dùng Prepared Statement hay không, mật khẩu có được băm bằng thuật toán hiện đại hay không, biểu mẫu có mã chống giả mạo CSRF hay không. Ba điểm này quyết định website của bạn có bị tấn công hay không.\n\nTiêu chí thứ ba là hiệu năng. Hãy chạy thử công cụ PageSpeed Insights, kiểm tra ảnh đã được nén và tải chậm chưa, số truy vấn cơ sở dữ liệu trên mỗi trang có hợp lý không.\n\nTiêu chí thứ tư là khả năng tối ưu công cụ tìm kiếm. Mã nguồn phải cho phép chỉnh tiêu đề, mô tả, đường dẫn thân thiện, sinh sitemap và khai báo dữ liệu có cấu trúc.\n\nTiêu chí thứ năm là tài liệu hướng dẫn. Một bộ mã nguồn nghiêm túc luôn kèm tài liệu cài đặt, sơ đồ cơ sở dữ liệu và hướng dẫn tuỳ biến.\n\nTiêu chí thứ sáu là chính sách bản quyền rõ ràng: dùng cá nhân, dùng thương mại hay bàn giao cho khách hàng.\n\nTiêu chí cuối cùng là hỗ trợ sau bán. Hãy ưu tiên nơi cam kết hỗ trợ cài đặt và sửa lỗi trong ít nhất sáu tháng.','assets/images/blog/post-1.svg','mã nguồn, kinh nghiệm, mua bán',1840,'7 tiêu chí chọn mua mã nguồn website chất lượng năm 2026','Hướng dẫn chọn mua mã nguồn website chất lượng: cấu trúc, bảo mật, hiệu năng, SEO, tài liệu, bản quyền và hỗ trợ sau bán.','2026-07-18 09:00:00'),
('Checklist SEO On-page cho website PHP thuần','checklist-seo-onpage-cho-website-php-thuan','Không cần plugin, một website PHP thuần vẫn có thể đạt điểm SEO gần tuyệt đối nếu làm đúng những việc dưới đây.','Nhiều người tin rằng phải dùng mã nguồn mở có sẵn plugin thì mới làm SEO được. Thực tế, website PHP thuần còn có lợi thế vì bạn kiểm soát được từng dòng HTML xuất ra.\n\nTrước hết là thẻ tiêu đề. Mỗi trang phải có một thẻ title duy nhất, dài khoảng 50 đến 60 ký tự và chứa từ khoá chính ở đầu. Thẻ mô tả nên dài 150 đến 160 ký tự, viết như một lời mời nhấp chuột chứ không phải liệt kê từ khoá.\n\nTiếp theo là cấu trúc thẻ tiêu đề nội dung. Mỗi trang chỉ nên có một thẻ H1, các mục lớn dùng H2, mục con dùng H3. Đừng dùng thẻ tiêu đề chỉ để cho chữ to hơn.\n\nĐường dẫn thân thiện là yếu tố thứ ba. Hãy chuyển tiêu đề tiếng Việt có dấu thành chuỗi không dấu, viết thường, nối bằng dấu gạch ngang và không chứa tham số khó hiểu.\n\nDữ liệu có cấu trúc giúp Google hiểu trang của bạn. Với trang sản phẩm hãy khai báo schema Product kèm giá và đánh giá, với bài viết hãy dùng schema Article, với đường dẫn phân cấp hãy dùng BreadcrumbList.\n\nCuối cùng là hiệu năng và trải nghiệm. Nén ảnh sang định dạng WebP, đặt thuộc tính chiều rộng và chiều cao cho ảnh để tránh giật bố cục, tải chậm ảnh ngoài màn hình đầu tiên, và luôn kiểm tra trên thiết bị di động trước khi phát hành.','assets/images/blog/post-2.svg','seo, php, hướng dẫn',2260,'Checklist SEO On-page đầy đủ cho website PHP thuần','Danh sách kiểm tra SEO on-page cho website PHP: thẻ title, meta description, heading, slug, schema, Core Web Vitals.','2026-07-26 10:30:00'),
('Bảo mật website PHP: 10 lỗ hổng thường gặp và cách phòng','bao-mat-website-php-10-lo-hong-thuong-gap','SQL Injection, XSS, CSRF và bảy lỗ hổng khác vẫn đang khiến hàng nghìn website PHP bị tấn công mỗi ngày.','Bảo mật không phải là tính năng thêm vào cuối dự án mà là cách bạn viết từng dòng mã ngay từ đầu.\n\nLỗ hổng phổ biến nhất là SQL Injection, xảy ra khi dữ liệu người dùng được ghép trực tiếp vào câu truy vấn. Giải pháp triệt để là dùng PDO với Prepared Statement và tham số ràng buộc.\n\nThứ hai là Cross-site Scripting, khi nội dung người dùng nhập được in ra HTML mà không lọc. Hãy luôn đi qua hàm htmlspecialchars với cờ ENT_QUOTES trước khi hiển thị.\n\nThứ ba là Cross-site Request Forgery. Mọi biểu mẫu thay đổi dữ liệu phải kèm một mã ngẫu nhiên lưu trong phiên và được kiểm tra ở phía máy chủ.\n\nThứ tư là tải tệp không kiểm soát. Hãy kiểm tra phần mở rộng, kiểm tra kiểu MIME thật, đổi tên tệp và cấm thực thi PHP trong thư mục tải lên.\n\nThứ năm là lưu mật khẩu sai cách. Không bao giờ lưu mật khẩu dạng thô hay băm MD5, hãy dùng password_hash với BCRYPT hoặc Argon2.\n\nCác lỗ hổng còn lại gồm lộ thông tin lỗi ra người dùng, phiên làm việc không được làm mới sau khi đăng nhập, thiếu giới hạn số lần đăng nhập sai, phân quyền kiểm tra ở giao diện mà không kiểm tra ở máy chủ, và thư viện bên thứ ba không được cập nhật.\n\nMột website an toàn là kết quả của thói quen viết mã cẩn thận, không phải của một lần rà soát duy nhất.','assets/images/blog/post-3.svg','bảo mật, php, pdo',1590,'Bảo mật website PHP: 10 lỗ hổng thường gặp và cách phòng chống','Tổng hợp 10 lỗ hổng bảo mật phổ biến của website PHP như SQL Injection, XSS, CSRF và cách phòng chống bằng mã nguồn.','2026-08-03 08:00:00'),
('Tối ưu Core Web Vitals cho website bán hàng','toi-uu-core-web-vitals-cho-website-ban-hang','LCP, INP và CLS ảnh hưởng trực tiếp tới thứ hạng và tỉ lệ chuyển đổi. Đây là cách cải thiện cả ba chỉ số.','Core Web Vitals là bộ ba chỉ số Google dùng để đo trải nghiệm thực tế của người dùng. Với website bán hàng, mỗi giây chậm trễ có thể làm giảm đáng kể tỉ lệ chuyển đổi.\n\nChỉ số đầu tiên là LCP, thời gian hiển thị khối nội dung lớn nhất. Với trang chủ, khối này thường là ảnh nền khu vực hero. Hãy nén ảnh sang WebP, khai báo kích thước, tải trước ảnh hero và tránh chèn phông chữ chặn hiển thị.\n\nChỉ số thứ hai là INP, đo độ phản hồi khi người dùng tương tác. Hãy chia nhỏ các tác vụ JavaScript dài, dùng sự kiện uỷ quyền thay vì gắn hàng trăm trình lắng nghe, và hoãn các đoạn mã không cần thiết cho lần hiển thị đầu.\n\nChỉ số thứ ba là CLS, đo mức độ giật bố cục. Nguyên nhân thường gặp là ảnh không khai báo kích thước, quảng cáo chèn động và phông chữ đổi kích thước khi tải xong. Hãy đặt thuộc tính width và height cho mọi thẻ ảnh, dành sẵn khoảng trống cho nội dung tải chậm và dùng font-display swap kèm phông dự phòng có kích thước tương đương.\n\nSau khi tối ưu, hãy đo lại bằng dữ liệu thực tế từ Search Console chứ không chỉ dựa vào điểm số trong phòng thí nghiệm.','assets/images/blog/post-4.svg','hiệu năng, core web vitals, seo',1230,'Tối ưu Core Web Vitals cho website bán hàng: LCP, INP, CLS','Hướng dẫn cải thiện LCP, INP và CLS cho website thương mại điện tử để tăng thứ hạng tìm kiếm và tỉ lệ chuyển đổi.','2026-08-10 11:15:00'),
('Từ đồ án môn học đến sản phẩm bán được tiền','tu-do-an-mon-hoc-den-san-pham-ban-duoc-tien','Khoảng cách giữa một đồ án được điểm A và một sản phẩm khách hàng chịu trả tiền nằm ở năm điều rất cụ thể.','Rất nhiều sinh viên có đồ án chạy tốt nhưng không thể bán được. Lý do không nằm ở kỹ thuật mà nằm ở cách đóng gói sản phẩm.\n\nĐiều đầu tiên là tài liệu. Khách hàng cần biết cách cài đặt trong mười phút. Hãy viết một tệp README rõ ràng gồm yêu cầu hệ thống, các bước nhập cơ sở dữ liệu, thông tin tài khoản mẫu và câu trả lời cho các lỗi thường gặp.\n\nĐiều thứ hai là dữ liệu mẫu. Một website trống rỗng không thuyết phục được ai. Hãy chuẩn bị dữ liệu mẫu thật, ảnh thật, nội dung thật.\n\nĐiều thứ ba là bản trình diễn trực tuyến. Người mua muốn bấm thử trước khi trả tiền. Hãy triển khai một bản demo và ghi lại một video ngắn khoảng hai phút.\n\nĐiều thứ tư là khả năng tuỳ biến. Hãy tách màu sắc, phông chữ, thông tin liên hệ ra thành cấu hình để người mua đổi thương hiệu mà không cần sửa mã.\n\nĐiều thứ năm là cam kết hỗ trợ. Một chính sách hỗ trợ sáu tháng rõ ràng làm tăng đáng kể tỉ lệ chốt đơn, đồng thời buộc bạn phải viết mã cẩn thận hơn.\n\nKhi làm đủ năm điều này, đồ án của bạn không còn là bài tập mà đã trở thành một sản phẩm.','assets/images/blog/post-5.svg','khởi nghiệp, freelance, kinh nghiệm',2740,'Từ đồ án môn học đến sản phẩm bán được tiền cho sinh viên IT','Năm bước biến đồ án môn học thành sản phẩm thương mại: tài liệu, dữ liệu mẫu, demo, khả năng tuỳ biến và hỗ trợ.','2026-08-16 09:45:00'),
('Thiết kế giao diện tối chuẩn khả năng tiếp cận','thiet-ke-giao-dien-toi-chuan-kha-nang-tiep-can','Giao diện tối đẹp mắt nhưng rất dễ vi phạm độ tương phản. Đây là cách làm đúng ngay từ đầu.','Giao diện tối đang là xu hướng nhưng cũng là nơi lỗi tương phản xuất hiện nhiều nhất.\n\nSai lầm phổ biến nhất là dùng màu đen tuyệt đối làm nền và trắng tuyệt đối làm chữ. Độ tương phản quá cao gây mỏi mắt và tạo hiện tượng nhoè chữ. Hãy chọn nền xám rất tối và chữ xám rất sáng thay vì hai cực trắng đen.\n\nSai lầm thứ hai là dùng màu bão hoà cao trên nền tối. Màu neon rực rỡ nhìn rất bắt mắt trong ảnh chụp nhưng khi đọc lâu sẽ gây khó chịu. Hãy giữ màu neon cho điểm nhấn, đường viền và trạng thái, còn phần chữ chính nên dùng màu trung tính.\n\nSai lầm thứ ba là giảm độ mờ của chữ phụ xuống quá thấp. Chữ phụ vẫn phải đạt tỉ lệ tương phản tối thiểu 4,5 trên 1 so với nền.\n\nSai lầm thứ tư là bỏ vòng viền khi lấy tiêu điểm bàn phím. Người dùng bàn phím và trình đọc màn hình phụ thuộc hoàn toàn vào vòng viền này.\n\nCuối cùng, hãy luôn tôn trọng tuỳ chọn giảm chuyển động của hệ điều hành. Một truy vấn media đơn giản có thể tắt toàn bộ hiệu ứng cho người dùng nhạy cảm với chuyển động.','assets/images/blog/post-6.svg','ui ux, dark mode, accessibility',1470,'Thiết kế giao diện tối chuẩn khả năng tiếp cận WCAG','Hướng dẫn thiết kế dark mode đúng chuẩn WCAG: chọn nền, độ tương phản, màu neon, vòng focus và giảm chuyển động.','2026-08-19 15:20:00');

INSERT INTO `hieumini_market_db`.`contacts` (`name`,`email`,`phone`,`subject`,`message`,`status`,`created_at`) VALUES
('Vũ Minh Anh','minhanh@example.com','0901234567','Báo giá website bán hàng','Mình cần một website bán mỹ phẩm tương tự HieuShop Pro nhưng muốn thêm chức năng tích điểm thành viên. Vui lòng báo giá giúp mình.','new','2026-08-18 10:12:00'),
('Đặng Hoàng Nam','nam.dang@example.com','0918273645','Hỗ trợ cài đặt','Mình đã mua AdminForge nhưng khi nhập database gặp lỗi collation. Nhờ bên bạn hỗ trợ.','processing','2026-08-19 14:35:00');

INSERT INTO `hieumini_market_db`.`settings` (`setting_key`,`setting_value`,`group_name`) VALUES
('site_name','HieuMini','general'),
('site_tagline','Chợ mã nguồn website chuẩn SEO cho người Việt','general'),
('site_description','HieuMini là nền tảng mua bán mã nguồn website PHP MySQL chất lượng cao: thương mại điện tử, doanh nghiệp, portfolio, quản trị, giáo dục và du lịch. Code sạch, bảo mật, chuẩn SEO, có tài liệu và hỗ trợ trọn đời.','seo'),
('site_keywords','mã nguồn website, source code php, website bán hàng php, đồ án website, mua bán website','seo'),
('site_url','http://localhost/DoAnWebsite/projects/HieuMini','general'),
('contact_email','lienhe@hieumini.vn','contact'),
('contact_phone','0987 654 321','contact'),
('contact_address','Số 1 Đại Cồ Việt, Hai Bà Trưng, Hà Nội','contact'),
('bank_info','Vietcombank - 0123456789 - TRAN VAN MINH HIEU','contact'),
('facebook','https://facebook.com/hieumini','social'),
('youtube','https://youtube.com/@hieumini','social'),
('github','https://github.com/hieumini','social'),
('og_image','assets/images/og-cover.svg','seo'),
('ga_id','','seo');

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
--  MỘT SỐ TRUY VẤN THỐNG KÊ THAM KHẢO
-- =====================================================================
-- Doanh thu theo tháng:
--   SELECT DATE_FORMAT(created_at,'%Y-%m') AS thang, SUM(total) AS doanh_thu
--   FROM orders WHERE status IN ('paid','delivered') GROUP BY thang ORDER BY thang;
--
-- Top 5 dự án bán chạy:
--   SELECT p.title, SUM(oi.quantity) AS so_luong
--   FROM order_items oi JOIN projects p ON p.id = oi.project_id
--   GROUP BY p.id ORDER BY so_luong DESC LIMIT 5;
--
-- Cập nhật lại điểm đánh giá trung bình:
--   UPDATE projects p SET
--     p.rating_avg = (SELECT IFNULL(AVG(r.rating),0) FROM reviews r WHERE r.project_id=p.id AND r.status=1),
--     p.rating_count = (SELECT COUNT(*) FROM reviews r WHERE r.project_id=p.id AND r.status=1);


SET FOREIGN_KEY_CHECKS = 1;

-- Tài khoản quản trị demo dùng chung
UPDATE `hieumini_market_db`.`users` SET `email`='admin@hieumini.vn', `password`='$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role`='admin';

SET FOREIGN_KEY_CHECKS = 1;
