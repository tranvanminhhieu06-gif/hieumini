-- ==========================================================================
--  HIEUMINI — TOÀN BỘ CƠ SỞ DỮ LIỆU CHO TIDB CLOUD
--  Gộp 7 cơ sở dữ liệu: 1 cổng trưng bày + 6 dự án con
--
--  Cách dùng:
--    TiDB Cloud Console → Cluster → SQL Editor / Import → chạy tệp này
--    Hoặc: mysql -h <HOST> -P 4000 -u <USER> -p --ssl-mode=VERIFY_IDENTITY \
--                --ssl-ca=<CA_PATH> < tidb_all.sql
--
--  Đã điều chỉnh cho TiDB: gỡ chỉ mục FULLTEXT (không được hỗ trợ).
--  Toàn bộ dùng utf8mb4_unicode_ci, engine InnoDB.
-- ==========================================================================

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


-- ==========================================================================
--  HieuMini (cổng trưng bày)   →   CSDL: hieumini_portfolio
-- ==========================================================================

-- =====================================================================
--  HieuMini — Portfolio Live Projects
--  Cơ sở dữ liệu: hieumini_portfolio
--  Bộ mã: utf8mb4_unicode_ci (hỗ trợ đầy đủ tiếng Việt có dấu)
--  Engine: InnoDB (hỗ trợ khóa ngoại, giao dịch)
--  Có thể chạy lại nhiều lần (mỗi bảng đều DROP TABLE IF EXISTS)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `hieumini_portfolio`
  DEFAULT CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE `hieumini_portfolio`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

-- ---------------------------------------------------------------------
-- Bảng 1: projects — Hồ sơ từng dự án website được trưng bày
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
  `id`            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `code`          VARCHAR(20)  NOT NULL COMMENT 'Mã dự án, VD: HieuWeb01',
  `slug`          VARCHAR(120) NOT NULL COMMENT 'Định danh thân thiện URL',
  `name`          VARCHAR(180) NOT NULL COMMENT 'Tên hiển thị của dự án',
  `tagline`       VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Câu mô tả ngắn 1 dòng',
  `summary`       TEXT         NULL COMMENT 'Mô tả tổng quan',
  `description`   MEDIUMTEXT   NULL COMMENT 'Mô tả chi tiết (nhiều đoạn)',
  `category`      VARCHAR(80)  NOT NULL DEFAULT 'E-commerce',
  `tech_stack`    VARCHAR(255) NOT NULL DEFAULT '' COMMENT 'Danh sách công nghệ, phân tách bởi dấu phẩy',
  `folder`        VARCHAR(120) NOT NULL COMMENT 'Thư mục con trong projects/ dùng để nhúng live',
  `entry_file`    VARCHAR(80)  NOT NULL DEFAULT 'index.php',
  `admin_path`    VARCHAR(120) NOT NULL DEFAULT 'admin/' COMMENT 'Đường dẫn trang quản trị của dự án con',
  `db_name`       VARCHAR(80)  NOT NULL DEFAULT '' COMMENT 'Tên CSDL mà dự án con sử dụng',
  `accent_from`   VARCHAR(9)   NOT NULL DEFAULT '#4F46E5' COMMENT 'Màu gradient bắt đầu',
  `accent_to`     VARCHAR(9)   NOT NULL DEFAULT '#7C3AED' COMMENT 'Màu gradient kết thúc',
  `year`          SMALLINT UNSIGNED NOT NULL DEFAULT 2026,
  `table_count`   SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Số bảng CSDL của dự án con',
  `page_count`    SMALLINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Số trang PHP',
  `is_featured`   TINYINT(1)   NOT NULL DEFAULT 0,
  `status`        ENUM('draft','published','archived') NOT NULL DEFAULT 'published',
  `sort_order`    SMALLINT     NOT NULL DEFAULT 0,
  `views`         INT UNSIGNED NOT NULL DEFAULT 0,
  `sold`          INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'Số lượng đã bán / đăng ký',
  `created_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `updated_at`    DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_projects_code` (`code`),
  UNIQUE KEY `uq_projects_slug` (`slug`),
  KEY `idx_projects_status_sort` (`status`, `sort_order`),
  KEY `idx_projects_category` (`category`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bảng 2: project_features — Các điểm nổi bật của từng dự án (1-N)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `project_features`;
CREATE TABLE `project_features` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id`  INT UNSIGNED NOT NULL,
  `icon`        VARCHAR(40)  NOT NULL DEFAULT 'spark' COMMENT 'Khóa icon SVG nội bộ',
  `title`       VARCHAR(160) NOT NULL,
  `content`     VARCHAR(500) NOT NULL DEFAULT '',
  `sort_order`  SMALLINT     NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_features_project` (`project_id`, `sort_order`),
  CONSTRAINT `fk_features_project` FOREIGN KEY (`project_id`)
    REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bảng 3: messages — Tin nhắn liên hệ từ khách truy cập
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `messages`;
CREATE TABLE `messages` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(180) NOT NULL,
  `subject`    VARCHAR(200) NOT NULL DEFAULT '',
  `content`    TEXT         NOT NULL,
  `ip`         VARCHAR(45)  NOT NULL DEFAULT '',
  `is_read`    TINYINT(1)   NOT NULL DEFAULT 0,
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_messages_read_time` (`is_read`, `created_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bảng 4: visit_logs — Nhật ký lượt xem dùng cho biểu đồ Dashboard
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `visit_logs`;
CREATE TABLE `visit_logs` (
  `id`         BIGINT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT UNSIGNED NULL,
  `path`       VARCHAR(255) NOT NULL DEFAULT '/',
  `referer`    VARCHAR(255) NOT NULL DEFAULT '',
  `ip`         VARCHAR(45)  NOT NULL DEFAULT '',
  `created_at` DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `idx_visits_time` (`created_at`),
  KEY `idx_visits_project` (`project_id`),
  CONSTRAINT `fk_visits_project` FOREIGN KEY (`project_id`)
    REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

SET FOREIGN_KEY_CHECKS = 0;

-- =====================================================================
--  DỮ LIỆU MẪU — 6 dự án website trong thư mục projects/
-- =====================================================================
INSERT INTO `projects`
(`id`,`code`,`slug`,`name`,`tagline`,`summary`,`description`,`category`,`tech_stack`,`folder`,`entry_file`,`admin_path`,`db_name`,`accent_from`,`accent_to`,`year`,`table_count`,`page_count`,`is_featured`,`status`,`sort_order`)
VALUES
(1,'HieuWeb01','fashion-studio','HieuMini Fashion Studio',
 'Sàn thương mại điện tử thời trang cao cấp với Flash Sale thời gian thực',
 'Website bán quần áo thời trang đầy đủ quy trình mua sắm: hero slider bộ sưu tập, đồng hồ đếm ngược Flash Sale Giờ Vàng, bộ lọc 7 danh mục kèm size/giá, bảng quy đổi kích cỡ theo chiều cao – cân nặng, giỏ hàng mã giảm giá và thanh toán COD/VietQR.',
 'HieuMini Fashion Studio là dự án thương mại điện tử ngành thời trang được xây dựng bằng PHP thuần theo mô hình module hóa. Phân hệ khách hàng cung cấp trang chủ với hero slider, khối sản phẩm bán chạy và lookbook thương hiệu; trang danh mục hỗ trợ lọc đa chiều theo 7 nhóm hàng, 4 khoảng giá và 9 mức size, kèm tìm kiếm theo tên hoặc mã SKU. Trang chi tiết sản phẩm tích hợp trình xem ảnh có thumbnail, bộ chọn size – màu trực quan, modal bảng quy đổi kích cỡ và hệ thống tab chất liệu / bảo quản / đánh giá 5 sao.\n\nQuy trình mua hàng đi từ giỏ hàng (cập nhật số lượng, áp mã HIEUMINI10 và FREESHIP, tự động miễn phí vận chuyển từ 300.000đ) tới trang thanh toán hỗ trợ COD hoặc chuyển khoản VietQR, kết thúc bằng hóa đơn điện tử có mã vận đơn và chức năng in. Phân hệ quản trị gồm dashboard KPI, CRUD sản phẩm kèm cảnh báo tồn kho thấp, quản lý danh mục, đơn hàng theo 5 trạng thái, mã giảm giá và phân quyền người dùng.',
 'Thời trang','PHP 8,MySQL,PDO,JavaScript,CSS3,AJAX','HieuWeb01','index.php','admin/','hieumini_db','#EC4899','#8B5CF6',2026,9,14,1,'published',1),

(2,'HieuWeb02','tech-store','HieuMini Tech Store',
 'Cửa hàng thiết bị công nghệ cao cấp theo phong cách Glassmorphism tối',
 'Website bán điện thoại, laptop, máy tính bảng và phụ kiện công nghệ với giao diện Dark Glassmorphism, giỏ hàng AJAX không tải lại trang, biểu đồ doanh thu vẽ bằng HTML5 Canvas và thanh toán VietQR động.',
 'HieuMini Tech Store hướng tới nhóm khách hàng yêu công nghệ với danh mục iPhone, Samsung Galaxy, MacBook, laptop Gaming ROG, iPad, smartwatch, tai nghe Sony / Marshall và bàn phím cơ. Điểm nhấn kỹ thuật của dự án là lớp giao diện Glassmorphism tối màu kết hợp hiệu ứng glow, cùng toàn bộ thao tác giỏ hàng được xử lý bất đồng bộ bằng AJAX nên người dùng không bao giờ phải chờ tải lại trang.\n\nKết nối cơ sở dữ liệu áp dụng mẫu Singleton trên PDO với charset utf8mb4, mọi truy vấn đều dùng prepared statement để chống SQL Injection. Lớp includes tách bạch header, footer, functions và auth_check đóng vai trò middleware kiểm tra phiên đăng nhập cùng phân quyền admin. Dashboard quản trị tự vẽ biểu đồ doanh thu bằng HTML5 Canvas API thay vì phụ thuộc thư viện ngoài, giúp trang nhẹ và chạy tốt khi không có Internet.',
 'Công nghệ','PHP 8,MySQL,PDO Singleton,AJAX,Canvas API,Glassmorphism','HieuWeb02','index.php','admin/','hieumini_bookstore_db','#0EA5E9','#6366F1',2026,8,13,1,'published',2),

(3,'HieuWeb03','study-tools','HieuMini Study & Creative',
 'Cửa hàng đồ dùng học tập – văn phòng phẩm trên nền Bootstrap 5',
 'Nền tảng bán dụng cụ học tập, đồ vẽ mỹ thuật, sổ tay và phụ kiện bàn học. Hệ thống tự khởi tạo cơ sở dữ liệu ở lần truy cập đầu tiên, tìm kiếm thời gian thực bằng AJAX và bộ lọc đa tiêu chí.',
 'HieuMini Study & Creative phục vụ nhóm học sinh, sinh viên và nhân viên văn phòng. Giao diện dựng trên Bootstrap 5 kèm Bootstrap Icons nên đạt độ tương thích thiết bị di động cao mà không cần viết lại hệ thống lưới. Trang chủ tổ chức theo các khối banner tựu trường, danh mục ngành hàng, sản phẩm Hot – New – Sale, đánh giá khách hàng và chính sách bảo hành đổi trả.\n\nĐiểm khác biệt lớn nhất về kỹ thuật là cơ chế tự động khởi tạo cơ sở dữ liệu: khi truy cập lần đầu, config/db.php sẽ kiểm tra sự tồn tại của CSDL hieumini_db, nếu chưa có thì tự động tạo và nạp toàn bộ script database.sql. Nhờ đó người chấm đồ án chỉ cần copy mã nguồn vào htdocs và mở trình duyệt là chạy được ngay, không cần thao tác thủ công với phpMyAdmin. Mật khẩu người dùng được băm bằng password_hash() (thuật toán Bcrypt) và mọi dữ liệu đầu vào đi qua hàm clean_input() để chống XSS.',
 'Văn phòng phẩm','PHP 8,MySQL,PDO,Bootstrap 5,AJAX,Bcrypt','HieuWeb03','index.php','admin/','hieumini_furniture_db','#F59E0B','#EF4444',2026,10,16,0,'published',3),

(4,'HieuWeb04','smart-home','DatCyber Smart Home',
 'Thương mại điện tử đồ gia dụng thông minh chuẩn SEO',
 'Website bán robot hút bụi, nồi chiên không dầu, máy lọc không khí và thiết bị nhà thông minh. Có Flash Sale đếm ngược kèm thanh tiến độ số lượng bán, lọc theo 4 khoảng giá và bảng thông số kỹ thuật chi tiết.',
 'DatCyber Smart Home là dự án thương mại điện tử ngành hàng gia dụng thông minh, tập trung vào trải nghiệm mua sắm nhanh và chuẩn SEO. Trang chủ gồm hero slider ưu đãi, lưới danh mục có hiệu ứng hover, khối Flash Sale với countdown timer thời gian thực và thanh tiến độ thể hiện số lượng đã bán, tiếp đến là sản phẩm bán chạy, tin tức tiện ích và đánh giá khách hàng.\n\nBộ lọc catalog cho phép tìm kiếm tức thì theo từ khóa, lọc theo danh mục và 4 khoảng giá (dưới 1 triệu, 1–3 triệu, 3–7 triệu, trên 7 triệu), sắp xếp theo mới nhất / giá / điểm đánh giá. Trang chi tiết sản phẩm hiển thị huy hiệu giảm giá, tình trạng kho, bảng thông số kỹ thuật đầy đủ và chính sách bảo hành 24 tháng. Toàn bộ thao tác giỏ hàng đi qua endpoint ajax-cart.php giúp phản hồi tức thời. Giao diện dựng bằng Bootstrap 5.3 kết hợp Font Awesome 6.5.',
 'Gia dụng','PHP 8,MySQL,Bootstrap 5.3,Font Awesome,AJAX,SEO','HieuWeb04','index.php','admin/login.php','datcyber_appliances_db','#10B981','#0891B2',2026,9,12,0,'published',4),

(5,'HieuWeb05','luxury-fitness','HieuMini Luxury Fitness Club',
 'Câu lạc bộ thể hình 5 sao: đặt lịch PT, đo InBody và cửa hàng thiết bị',
 'Nền tảng phòng gym cao cấp phối màu Dark & Gold với 30 sản phẩm – dịch vụ chia 5 danh mục, công cụ tính BMI/TDEE tương tác, scroll reveal bằng IntersectionObserver và live counter số liệu.',
 'HieuMini Luxury Fitness Club được thiết kế theo định hướng thương hiệu cao cấp: nền tối #0A0C10 kết hợp điểm nhấn vàng #F59E0B, ánh sáng neon cyan và emerald. Hệ thống hoạt ảnh xây dựng thuần CSS với các keyframes glowPulse, shimmerGold, floatCard, kết hợp scroll reveal bằng IntersectionObserver và live counter đếm tăng dần các chỉ số thành tích của câu lạc bộ.\n\nDanh mục sản phẩm – dịch vụ gồm 30 mục chia đều 5 nhóm: gói hội viên và dịch vụ VIP, thiết bị máy tập, thực phẩm bổ sung, trang phục phụ kiện và dịch vụ huấn luyện cá nhân. Ngoài chức năng thương mại điện tử (giỏ hàng AJAX, mã ưu đãi CEOFIT20 và HIEUMINI10, thanh toán sinh mã VietQR), website còn có hai công cụ tương tác đặc thù ngành: máy tính BMI/TDEE và biểu mẫu đặt lịch trải nghiệm phòng tập VIP kèm đo InBody 770.',
 'Thể hình','PHP 8,MySQL,PDO,Vanilla CSS3,ES6+,IntersectionObserver','HieuWeb05','index.php','admin/login.php','hieumini_gym_db','#F59E0B','#DC2626',2026,10,15,1,'published',5),

(6,'HieuWeb06','source-market','HieuMini Source Market',
 'Chợ mã nguồn website chuẩn SEO với 19/19 hạng mục tối ưu',
 'Sàn giao dịch mã nguồn website viết bằng PHP 8 thuần, không dùng framework. Tích hợp sitemap động, RSS feed, robots.txt, bảo vệ CSRF, giao diện Dark Neon Glassmorphism có chế độ sáng/tối và trang tự kiểm tra 37 hạng mục hệ thống.',
 'HieuMini Source Market là dự án có phạm vi kỹ thuật rộng nhất trong bộ sưu tập: bên cạnh phần thương mại điện tử thông thường, hệ thống bổ sung một tầng SEO hoàn chỉnh gồm sitemap.php sinh sơ đồ trang động, rss.php phát hành nguồn tin, robots.txt, thẻ Open Graph và dữ liệu có cấu trúc, trang 404 tùy biến cùng đường dẫn thân thiện cấu hình qua .htaccess.\n\nVề bảo mật, toàn bộ truy vấn dùng PDO prepared statement, biểu mẫu được bảo vệ bằng token CSRF, mật khẩu băm Bcrypt và dữ liệu đầu ra escape chống XSS. Giao diện Dark Neon Glassmorphism hỗ trợ song song hai chế độ sáng và tối, ghi nhớ lựa chọn của người dùng. Dự án còn kèm blog, wishlist, tài khoản người dùng và đặc biệt là test_system.php — trang tự kiểm tra 37 hạng mục cấu hình, kết nối và toàn vẹn dữ liệu, rất hữu ích khi triển khai trên máy mới.',
 'Marketplace','PHP 8.2,MySQL 8,CSRF,SEO động,Glassmorphism,Dark/Light','HieuWeb06','index.php','admin/login.php','hieumini_market_db','#7C3AED','#22D3EE',2026,12,20,1,'published',6);

INSERT INTO `project_features` (`project_id`,`icon`,`title`,`content`,`sort_order`) VALUES
(1,'bolt','Flash Sale Giờ Vàng','Đồng hồ đếm ngược thời gian thực trên trang chủ, tự động ẩn khối khuyến mãi khi hết giờ.',1),
(1,'filter','Bộ lọc đa chiều','Lọc đồng thời theo 7 danh mục, 4 khoảng giá và 9 mức size, kèm tìm kiếm theo tên hoặc mã SKU.',2),
(1,'ruler','Bảng quy đổi size','Modal gợi ý kích cỡ theo chiều cao và cân nặng, giảm tỉ lệ đổi trả do chọn sai size.',3),
(1,'wallet','Thanh toán COD & VietQR','Sinh mã VietQR hiển thị thông tin MBBank hoặc chọn thanh toán khi nhận hàng.',4),
(2,'layers','Glassmorphism tối','Hệ thống giao diện kính mờ nhiều lớp kèm hiệu ứng glow, tối ưu cho ngành hàng công nghệ.',1),
(2,'refresh','Giỏ hàng AJAX','Thêm, sửa, xóa sản phẩm hoàn toàn bất đồng bộ, không tải lại trang, phản hồi bằng toast.',2),
(2,'chart','Biểu đồ Canvas thuần','Dashboard tự vẽ biểu đồ doanh thu bằng HTML5 Canvas API, không phụ thuộc thư viện ngoài.',3),
(2,'shield','PDO Singleton','Một kết nối duy nhất dùng chung toàn ứng dụng, prepared statement chống SQL Injection.',4),
(3,'database','Tự khởi tạo CSDL','Lần truy cập đầu tiên hệ thống tự tạo cơ sở dữ liệu và nạp dữ liệu mẫu từ database.sql.',1),
(3,'grid','Bootstrap 5 responsive','Dùng hệ lưới Bootstrap 5 và Bootstrap Icons, hiển thị tốt từ điện thoại tới màn hình lớn.',2),
(3,'search','Tìm kiếm thời gian thực','Gợi ý sản phẩm ngay khi gõ nhờ AJAX, kết hợp bộ lọc danh mục và khoảng giá.',3),
(3,'star','Đánh giá 5 sao','Khách hàng chấm điểm và bình luận sản phẩm, hệ thống tính điểm trung bình tự động.',4),
(4,'bolt','Flash Sale có tiến độ','Countdown timer kèm thanh tiến độ thể hiện số lượng đã bán trên tổng số suất khuyến mãi.',1),
(4,'search','Chuẩn SEO','Thẻ meta đầy đủ, đường dẫn thân thiện, cấu trúc heading hợp lý cho từng trang sản phẩm.',2),
(4,'list','Bảng thông số kỹ thuật','Mỗi sản phẩm gia dụng đều có bảng specification chi tiết và chính sách bảo hành 24 tháng.',3),
(4,'refresh','Endpoint ajax-cart','Toàn bộ thao tác giỏ hàng đi qua một endpoint duy nhất, dễ bảo trì và mở rộng.',4),
(5,'spark','Hoạt ảnh CSS cao cấp','glowPulse, shimmerGold, floatCard kết hợp scroll reveal bằng IntersectionObserver.',1),
(5,'calculator','Công cụ BMI & TDEE','Tính chỉ số khối cơ thể và nhu cầu calo, gợi ý gói tập phù hợp ngay trên trang.',2),
(5,'calendar','Đặt lịch phòng tập VIP','Biểu mẫu đặt lịch trải nghiệm và đăng ký đo InBody 770, lưu trực tiếp vào cơ sở dữ liệu.',3),
(5,'box','30 sản phẩm – dịch vụ','Năm danh mục: hội viên, thiết bị, dinh dưỡng, trang phục và huấn luyện cá nhân.',4),
(6,'search','SEO 19/19 hạng mục','sitemap.php động, rss.php, robots.txt, Open Graph và dữ liệu có cấu trúc.',1),
(6,'shield','CSRF & Bcrypt','Mọi biểu mẫu có token chống giả mạo yêu cầu, mật khẩu băm Bcrypt, dữ liệu ra được escape.',2),
(6,'moon','Chế độ sáng / tối','Người dùng chuyển đổi giao diện và lựa chọn được ghi nhớ giữa các phiên truy cập.',3),
(6,'check','Tự kiểm tra 37 hạng mục','Trang test_system.php kiểm tra cấu hình PHP, kết nối CSDL và tính toàn vẹn dữ liệu.',4);

-- Dữ liệu mẫu cho phần thống kê Dashboard (30 ngày gần nhất)
INSERT INTO `visit_logs` (`project_id`,`path`,`ip`,`created_at`)
SELECT
  1 + FLOOR(RAND() * 6),
  '/project.php',
  '127.0.0.1',
  NOW() - INTERVAL FLOOR(RAND() * 30) DAY
FROM `project_features` f1, `project_features` f2
LIMIT 400;

UPDATE `projects` p
SET p.`views` = (SELECT COUNT(*) FROM `visit_logs` v WHERE v.`project_id` = p.`id`);

UPDATE `projects` SET `sold` = 1284 WHERE `code` = 'HieuWeb01';
UPDATE `projects` SET `sold` = 947  WHERE `code` = 'HieuWeb02';
UPDATE `projects` SET `sold` = 2156 WHERE `code` = 'HieuWeb03';
UPDATE `projects` SET `sold` = 763  WHERE `code` = 'HieuWeb04';
UPDATE `projects` SET `sold` = 421  WHERE `code` = 'HieuWeb05';
UPDATE `projects` SET `sold` = 1539 WHERE `code` = 'HieuWeb06';

INSERT INTO `messages` (`name`,`email`,`subject`,`content`,`ip`,`is_read`) VALUES
('Nguyễn Thị Lan','lan.nguyen@example.com','Hỏi về dự án Fashion Studio','Chào bạn, mình muốn tham khảo mã nguồn phần bộ lọc size của dự án HieuWeb01. Bạn có thể chia sẻ thêm không?','127.0.0.1',0),
('Trần Quốc Bảo','bao.tran@example.com','Hợp tác phát triển','Bên mình đang cần một website bán hàng tương tự HieuWeb04. Rất mong được trao đổi thêm về chi phí và thời gian.','127.0.0.1',1);

-- =====================================================================
--  KẾT THÚC SCRIPT
--  Ghi chú: Hệ thống KHÔNG lưu tài khoản quản trị trong cơ sở dữ liệu.
--  Trang admin chỉ được kích hoạt khi máy chủ có biến môi trường
--  ADMIN_PASSWORD. Xem README.md, mục "Bật trang quản trị".
-- =====================================================================


-- ==========================================================================
--  HieuWeb01 — Fashion Studio   →   CSDL: hieumini_db
-- ==========================================================================

CREATE DATABASE IF NOT EXISTS `hieumini_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

USE `hieumini_db`;
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(100) NOT NULL,
  `slug` varchar(100) NOT NULL,
  `description` text DEFAULT NULL,
  `image` varchar(255) DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `categories` WRITE;
INSERT INTO `categories` VALUES
(1,'Áo Thun & Polo','ao-thun-polo','Bộ sưu tập áo thun Unisex, áo Polo phong cách năng động, cotton thoáng mát','cat_ao_thun.jpg',1,'2026-08-21 11:42:09'),
(2,'Áo Sơ Mi Cao Cấp','ao-so-mi','Áo sơ mi Oxford, sơ mi lụa công sở và dạo phố lịch lãm','cat_ao_somi.jpg',1,'2026-08-21 11:42:09'),
(3,'Áo Khoác & Hoodie','ao-khoac-hoodie','Áo khoác Bomber, Varsity jacket, Hoodie nỉ bông ấm áp thời thượng','cat_ao_khoac.jpg',1,'2026-08-21 11:42:09'),
(4,'Quần Jeans & Denim','quan-jeans','Quần Jean Slimfit, Jean ống rộng Baggy, Denim wash cao cấp','cat_quan_jeans.jpg',1,'2026-08-21 11:42:09'),
(5,'Quần Kaki & Trousers','quan-kaki','Quần Kaki Chino, quần tây âu dáng suông thanh lịch công sở','cat_quan_kaki.jpg',1,'2026-08-21 11:42:09'),
(6,'Váy & Đầm Nữ','vay-dam-nu','Váy hoa nhí Vintage, đầm suông, chân váy chữ A phong cách Hàn Quốc','cat_vay_dam.jpg',1,'2026-08-21 11:42:09'),
(7,'Phụ Kiện Thời Trang','phu-kien','Thắt lưng da, nón kết lưỡi trai, túi đeo chéo, vớ thời trang','cat_phu_kien.jpg',1,'2026-08-21 11:42:09');
UNLOCK TABLES;
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `code` varchar(50) NOT NULL,
  `discount_type` enum('percentage','fixed') DEFAULT 'percentage',
  `discount_value` decimal(10,2) NOT NULL,
  `min_order_amount` decimal(12,2) DEFAULT 0.00,
  `usage_limit` int(11) DEFAULT 100,
  `used_count` int(11) DEFAULT 0,
  `expiry_date` date DEFAULT NULL,
  `status` tinyint(1) DEFAULT 1,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `code` (`code`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `coupons` WRITE;
INSERT INTO `coupons` VALUES
(1,'HIEUMINI10','percentage',10.00,200000.00,200,0,'2026-12-31',1,'2026-08-21 11:42:09'),
(2,'FREESHIP','fixed',30000.00,300000.00,500,0,'2026-12-31',1,'2026-08-21 11:42:09'),
(3,'WELCOME50K','fixed',50000.00,400000.00,100,0,'2026-12-31',1,'2026-08-21 11:42:09');
UNLOCK TABLES;
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `order_id` int(11) NOT NULL,
  `product_id` int(11) NOT NULL,
  `product_name` varchar(200) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `quantity` int(11) NOT NULL,
  `size` varchar(20) DEFAULT 'M',
  `color` varchar(50) DEFAULT 'Đen',
  `subtotal` decimal(12,2) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `fk_order_items_order` (`order_id`),
  KEY `fk_order_items_product` (`product_id`),
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `order_items` WRITE;
INSERT INTO `order_items` VALUES
(1,1,1,'Áo Thun Nam Nữ Streetwear Basic Cotton 100%',199000.00,1,'L','Đen',199000.00),
(2,1,4,'Áo Sơ Mi Nam Oxford Dài Tay Chống Nhăn',350000.00,1,'XL','Xanh Nhạt',350000.00),
(3,2,7,'Áo Khoác Bomber Kaki 2 Lớp Form Rộng Unisex',480000.00,1,'L','Xanh Rêu',480000.00),
(4,2,14,'Đầm Hoa Nhí Dáng Xòe Vintage Cổ Vuông Tiểu Thư',390000.00,1,'M','Hoa Nhí Hồng',390000.00),
(5,3,10,'Quần Jean Nam Slimfit Co Giãn Rửa Màu Vintage',380000.00,1,'31','Xanh Đậm',380000.00),
(6,3,2,'Áo Polo Nam Phối Cổ Bo Dệt Sang Trọng',289000.00,1,'L','Xanh Navy',289000.00);
UNLOCK TABLES;
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `user_id` int(11) DEFAULT NULL,
  `order_code` varchar(30) NOT NULL,
  `customer_name` varchar(100) NOT NULL,
  `customer_phone` varchar(20) NOT NULL,
  `customer_email` varchar(100) NOT NULL,
  `shipping_address` text NOT NULL,
  `payment_method` enum('cod','banking','vnpay','momo') DEFAULT 'cod',
  `payment_status` enum('unpaid','paid') DEFAULT 'unpaid',
  `order_status` enum('pending','processing','shipping','completed','cancelled') DEFAULT 'pending',
  `total_amount` decimal(12,2) NOT NULL,
  `discount_amount` decimal(12,2) DEFAULT 0.00,
  `shipping_fee` decimal(12,2) DEFAULT 30000.00,
  `coupon_code` varchar(50) DEFAULT NULL,
  `notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `order_code` (`order_code`),
  KEY `fk_orders_user` (`user_id`),
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `orders` WRITE;
INSERT INTO `orders` VALUES
(1,2,'HM-ORD-1001','Nguyễn Văn Nam','0912345678','khachhang@gmail.com','Số 18 Duy Tân, Cầu Giấy, Hà Nội','cod','paid','completed',549000.00,30000.00,0.00,'FREESHIP',NULL,'2026-08-21 11:42:09'),
(2,NULL,'HM-ORD-1002','Trần Thị Mai','0987654321','maitran@gmail.com','120 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh','banking','paid','shipping',870000.00,0.00,0.00,NULL,NULL,'2026-08-21 11:42:09'),
(3,NULL,'HM-ORD-1003','Lê Hoàng Long','0905123987','longle@gmail.com','45 Lê Duẩn, Hải Châu, Đà Nẵng','cod','unpaid','processing',699000.00,0.00,30000.00,NULL,NULL,'2026-08-21 11:42:09');
UNLOCK TABLES;
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category_id` int(11) NOT NULL,
  `name` varchar(200) NOT NULL,
  `slug` varchar(200) NOT NULL,
  `sku` varchar(50) NOT NULL,
  `price` decimal(12,2) NOT NULL,
  `discount_price` decimal(12,2) DEFAULT NULL,
  `stock` int(11) DEFAULT 50,
  `sizes` varchar(100) DEFAULT 'S,M,L,XL',
  `colors` varchar(100) DEFAULT 'Trắng,Đen,Xanh,Be',
  `description` text DEFAULT NULL,
  `content` longtext DEFAULT NULL,
  `image` varchar(255) NOT NULL,
  `featured` tinyint(1) DEFAULT 0,
  `status` tinyint(1) DEFAULT 1,
  `view_count` int(11) DEFAULT 0,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `slug` (`slug`),
  UNIQUE KEY `sku` (`sku`),
  KEY `fk_products_category` (`category_id`),
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `products` WRITE;
INSERT INTO `products` VALUES
(1,1,'Áo Thun Nam Nữ Streetwear Basic Cotton 100%','ao-thun-streetwear-basic-cotton','HM-TS01',250000.00,199000.00,120,'S,M,L,XL,XXL','Đen,Trắng,Xám,Be','Áo thun phong cách Streetwear chất liệu 100% Cotton Compact 2 chiều 250gsm dày dặn, thấm hút mồ hôi cực tốt, co giãn thoải mái.','<p>Áo thun HieuMini Streetwear là sự kết hợp hoàn hảo giữa thiết kế tối giản hiện đại và chất liệu vải Cotton dệt chải kỹ cao cấp.</p><ul><li>Chất liệu: 100% Cotton định lượng 250gsm không xù lông.</li><li>Form dáng: Oversize Unisex thoải mái, trẻ trung.</li><li>Bo cổ: Dệt bo gân 2.5cm dày dặn, chống bai dão sau nhiều lần giặt.</li><li>Đường may: Trần 2 kim chắc chắn theo tiêu chuẩn xuất khẩu.</li></ul>','ao_thun_streetwear.jpg',1,1,1520,'2026-08-21 11:42:09'),
(2,1,'Áo Polo Nam Phối Cổ Bo Dệt Sang Trọng','ao-polo-nam-phoi-co-bo-det','HM-PL02',350000.00,289000.00,85,'M,L,XL,XXL','Xanh Navy,Trắng,Đen,Xanh Rêu','Áo Polo nam vải pique cá sấu mắt chim thoáng khí, cổ phối bo dệt sọc tinh tế, phong cách Smart Casual lịch lãm.','<p>Mẫu áo Polo cao cấp tôn lên vẻ nam tính, thanh lịch thích hợp mặc đi làm, đi chơi hay gặp gỡ đối tác.</p><ul><li>Vải Pique Cotton 95% + 5% Spandex co giãn 4 chiều.</li><li>Cúc áo dập chìm logo thương hiệu sang trọng.</li><li>Khử mùi kháng khuẩn tự nhiên, không nhăn nhúm.</li></ul>','ao_polo_dệt_bo.jpg',1,1,980,'2026-08-21 11:42:09'),
(3,1,'Áo Thun Graphic Oversize HieuMini Edition 2026','ao-thun-graphic-oversize-hieumini','HM-TS03',320000.00,260000.00,95,'S,M,L,XL','Trắng Kem,Đen Khói,Xanh Pastel','Áo thun Graphic in lụa thủ công cao cấp sắc nét, họa tiết độc quyền mang đậm tinh thần tự do phóng khoáng của giới trẻ.','<p>Thiết kế phiên bản giới hạn Limited Edition 2026 từ HieuMini Studio.</p><ul><li>Hình in kỹ thuật số công nghệ Nhật Bản sắc nét không bong tróc.</li><li>Chất vải dệt sợi chải kỹ Organic Cotton thân thiện môi trường.</li></ul>','ao_thun_graphic_hieu.jpg',0,1,640,'2026-08-21 11:42:09'),
(4,2,'Áo Sơ Mi Nam Oxford Dài Tay Chống Nhăn','ao-so-mi-nam-oxford-dai-tay','HM-SM01',420000.00,350000.00,70,'M,L,XL,XXL','Xanh Nhạt,Trắng,Hồng Pastel,Xám','Áo sơ mi chất liệu vải Oxford cao cấp dệt sợi kép bền bỉ, form dáng Regular Fit chuẩn mực tôn dáng phái mạnh.','<p>Sơ mi Oxford là item kinh điển không thể thiếu trong tủ đồ của quý ông hiện đại.</p><ul><li>Chất vải Oxford 100% Cotton dệt nổi hạt đặc trưng, thoáng mát 4 mùa.</li><li>Công nghệ ép nhiệt chống nhăn Easy-Iron giúp tiết kiệm thời gian ủi đồ.</li><li>Cổ Button-down giữ dáng cứng cáp suốt ngày dài làm việc.</li></ul>','ao_so_mi_oxford.jpg',1,1,1890,'2026-08-21 11:42:09'),
(5,2,'Áo Sơ Mi Lụa Cổ Cubano Phong Cách Hàn Quốc','ao-so-mi-lua-co-cubano-han-quoc','HM-SM02',390000.00,319000.00,60,'S,M,L,XL','Be Cát,Xanh Mint,Đen,Nâu Cafe','Áo sơ mi vải lụa tơ tằm nhân tạo mềm rủ, cổ Cubano phong cách nghỉ dưỡng phóng khoáng, dạo phố sành điệu.','<p>Cực kỳ mát mẻ và tôn dáng cho những chuyến du lịch dạo phố mùa hè.</p><ul><li>Chất liệu Silk Rayon mềm mại, nhẹ nhàng và mát lịm trên da.</li><li>Form áo Relaxed suông nhẹ bay bổng.</li></ul>','ao_so_mi_cubano.jpg',1,1,1240,'2026-08-21 11:42:09'),
(6,2,'Áo Sơ Mi Kẻ Flannel Vintage Classic','ao-so-mi-ke-flannel-vintage','HM-SM03',360000.00,299000.00,50,'M,L,XL','Đỏ Kẻ Đen,Xanh Kẻ Vàng,Nâu Kẻ Be','Áo sơ mi Flannel dạ kẻ ô vuông cổ điển, chất vải êm ái giữ ấm nhẹ, phong cách Retro bụi bặm.','<p>Phù hợp mặc khoác ngoài áo thun trơn tạo layer ấn tượng.</p><ul><li>Chất vải Flannel cào bông 2 mặt êm ái.</li><li>Phối túi hộp ngực tiện lợi cá tính.</li></ul>','ao_so_mi_caro.jpg',0,1,710,'2026-08-21 11:42:09'),
(7,3,'Áo Khoác Bomber Kaki 2 Lớp Form Rộng Unisex','ao-khoac-bomber-kaki-2-lop','HM-JK01',590000.00,480000.00,45,'M,L,XL,XXL','Xanh Rêu,Đen,Be Sáng','Áo khoác Bomber Kaki 2 lớp lót dù chống gió chống nước nhẹ, khóa kéo kim loại YKK trơn tru bền bỉ.','<p>Áo khoác Bomber biểu tượng thời trang đường phố trẻ trung và đa dụng.</p><ul><li>Lớp ngoài: Vải Kaki dệt mật độ cao chống bám bụi và cản gió.</li><li>Lớp lót: Vải dù lụa thoáng khí, có túi trong an toàn đựng điện thoại/ví.</li><li>Bo tay và gấu áo dệt thun co giãn dày dặn ôm trọn cơ thể.</li></ul>','ao_khoac_bomber.jpg',1,1,2100,'2026-08-21 11:42:09'),
(8,3,'Áo Hoodie Nỉ Bông Unisex Warm Comfy','ao-hoodie-ni-bong-unisex-warm','HM-HD02',490000.00,399000.00,65,'S,M,L,XL','Xám Tiêu,Đen,Xanh Rêu,Nâu Đất','Áo Hoodie nỉ bông dày 380gsm siêu ấm áp, mũ 2 lớp đứng form, túi kangaroo rộng rãi giữ ấm tay.','<p>Chiếc áo Hoodie hoàn hảo cho mùa thu đông se lạnh.</p><ul><li>Chất vải nỉ chân cua cào bông tuyết mềm mại không ngứa da.</li><li>Mũ trùm đầu may 2 lớp giữ form cứng cáp, dây rút có chốt kim loại sang trọng.</li></ul>','ao_hoodie_ni_bong.jpg',1,1,1650,'2026-08-21 11:42:09'),
(9,3,'Áo Blazer Nam Nữ Dáng Suông Hàn Quốc','ao-blazer-nam-nu-dang-suong-han-quoc','HM-BZ03',680000.00,550000.00,40,'S,M,L,XL','Nâu Mocha,Đen Tuyền,Ghi Xám','Áo khoác Blazer dáng Relaxed Fit 2 lớp chuẩn form Hàn Quốc, cầu vai đệm mút tự nhiên tạo form vai thẳng đẹp.','<p>Blazer thời thượng giúp nâng tầm phong cách ngay tức thì từ đi học, đi làm đến dự tiệc nhẹ.</p><ul><li>Chất liệu Tuyết mưa cao cấp co giãn nhẹ, giữ phom đứng dáng.</li><li>Túi mổ 2 bên may nắp giấu tinh tế.</li></ul>','ao_blazer_han_quoc.jpg',1,1,1430,'2026-08-21 11:42:09'),
(10,4,'Quần Jean Nam Slimfit Co Giãn Rửa Màu Vintage','quan-jean-nam-slimfit-co-gian','HM-JN01',450000.00,380000.00,80,'29,30,31,32,34','Xanh Đậm,Xanh Nhạt,Đen Khói','Quần Jean nam dáng Slimfit ôm vừa vặn, vải Denim 12oz wash màu thủ công bền màu, co giãn 2% Spandex thoải mái.','<p>Mẫu quần Jeans tôn dáng hoàn hảo, dễ dàng phối hợp với áo thun hay sơ mi.</p><ul><li>Đinh tán đồng và khóa kéo đồng nguyên khối dập logo HieuMini.</li><li>Đường chỉ may bò kép chịu lực cao, không đứt chỉ khi vận động mạnh.</li></ul>','quan_jean_slimfit.jpg',1,1,1780,'2026-08-21 11:42:09'),
(11,4,'Quần Jeans Ống Rộng Wide-Leg Unisex Baggy Fit','quan-jeans-ong-rong-wide-leg-unisex','HM-JN02',480000.00,399000.00,75,'S,M,L,XL','Xanh Retro,Xanh Khói,Trắng Kem','Quần Jean ống rộng suông dài hack dáng cực đỉnh, cạp cao tôn vòng eo và kéo dài đôi chân.','<p>Item không thể thiếu của các tín đồ phong cách thời trang Y2K và Streetwear.</p>','quan_jean_baggy.jpg',0,1,1120,'2026-08-21 11:42:09'),
(12,5,'Quần Kaki Chino Dáng Đứng Co Giãn Công Sở','quan-kaki-chino-dang-dung-co-gian','HM-KK01',390000.00,320000.00,90,'29,30,31,32,34','Vàng Be,Đen,Xanh Rêu,Ghi','Quần Kaki Chino cao cấp chất vải Cotton pha sợi thun mềm mại, chống nhăn, dáng đứng thanh lịch.','<p>Quần Kaki Chino mang lại vẻ ngoài trẻ trung nhưng không kém phần trang trọng.</p><ul><li>Túi xéo 2 bên sâu rộng và 2 túi sau cài khuy tiện lợi.</li><li>Lưng quần may đai lót chống tụt áo sơ mi khi sơ vin.</li></ul>','quan_kaki_chino.jpg',1,1,1340,'2026-08-21 11:42:09'),
(13,5,'Quần Tây Âu Xếp Ly Dáng Suông Trousers','quan-tay-au-xep-ly-dang-suong','HM-TR02',460000.00,379000.00,60,'S,M,L,XL','Đen,Xám Tro,Be Nâu','Quần âu xếp ly phía trước dáng suông rộng thời thượng, chất vải chống nhăn và giữ nếp li sắc sảo.','<p>Phong cách quý ông hiện đại pha lẫn nét lãng tử Hàn Quốc.</p>','quan_tay_au.jpg',0,1,890,'2026-08-21 11:42:09'),
(14,6,'Đầm Hoa Nhí Dáng Xòe Vintage Cổ Vuông Tiểu Thư','dam-hoa-nhi-dang-xoe-vintage-co-vuong','HM-DR01',480000.00,390000.00,55,'S,M,L','Hoa Nhí Hồng,Hoa Nhí Vàng,Hoa Xanh Pastel','Đầm hoa nhí dáng xòe bay bổng chất liệu voan Hàn 2 lớp cao cấp, cổ vuông tôn xương quai xanh quyến rũ.','<p>Thiết kế đầm tiểu thư ngọt ngào phù hợp chụp ảnh du lịch, dạo phố và hẹn hò.</p><ul><li>Lưng đầm bo chun nhún co giãn ôm sát vòng eo thon gọn.</li><li>Tay bồng nhẹ che khuyết điểm bắp tay hiệu quả.</li></ul>','dam_hoa_nhi.jpg',1,1,2450,'2026-08-21 11:42:09'),
(15,6,'Chân Váy Chữ A Lưng Cao Xếp Ly Cá Tính','chan-vay-chu-a-lung-cao-xep-ly','HM-SK02',290000.00,230000.00,65,'S,M,L','Đen,Kẻ Caro Xám,Nâu Be','Chân váy chữ A cạp cao tôn dáng có quần bảo hộ bên trong an toàn kín đáo, chất liệu tuyết mưa không xù lông.','<p>Dễ dàng phối cùng áo thun, sơ mi hoặc áo croptop năng động.</p>','chan_vay_chu_a.jpg',0,1,1190,'2026-08-21 11:42:09'),
(16,7,'Thắt Lưng Da Bò Khóa Tự Động Kim Loại Tinh Tế','that-lung-da-bo-khoa-tu-dong','HM-BT01',220000.00,169000.00,110,'Freesize (115-125cm)','Đen Bóng,Nâu Đậm','Dây lưng da bò thật nguyên tấm 100% mềm mại, mặt khóa hợp kim chống gỉ mạ nano bóng bẩy sang trọng.','<p>Phụ kiện định hình phong cách lịch lãm của phái mạnh trong mọi trang phục công sở.</p>','that_lung_da.jpg',0,1,670,'2026-08-21 11:42:09'),
(17,7,'Mũ Lưỡi Trai Nón Kết Thêu Logo HieuMini Signature','mu-luoi-trai-theu-logo-hieumini','HM-CP02',180000.00,139000.00,150,'Freesize có khóa gài sau','Đen,Trắng,Be,Xanh Rêu','Nón kết vải Kaki cotton 100% dày dặn giữ form, thêu logo nổi 3D tinh xảo chống bay màu.','<p>Mũ lưỡi trai phong cách unisex năng động cho cả nam và nữ.</p>','non_ket_hieumini.jpg',0,1,840,'2026-08-21 11:42:09');
UNLOCK TABLES;
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `product_id` int(11) NOT NULL,
  `user_id` int(11) NOT NULL,
  `user_name` varchar(100) NOT NULL,
  `rating` tinyint(4) NOT NULL DEFAULT 5,
  `comment` text NOT NULL,
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  KEY `fk_reviews_product` (`product_id`),
  KEY `fk_reviews_user` (`user_id`),
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `reviews` WRITE;
INSERT INTO `reviews` VALUES
(1,1,2,'Nguyễn Văn Nam',5,'Áo thun mặc rất thích, chất cotton dày dặn thấm hút tốt không hề bị xù lông. Sẽ tiếp tục ủng hộ shop HieuMini!','2026-08-21 11:42:09'),
(2,4,1,'Admin HieuMini',5,'Sơ mi Oxford đứng form, chống nhăn tốt, mặc đi làm rất lịch sự.','2026-08-21 11:42:09'),
(3,7,2,'Nguyễn Văn Nam',5,'Áo Bomber form đẹp mê ly, lớp lót dù êm ái, khóa mượt.','2026-08-21 11:42:09'),
(4,14,2,'Mai Phương',5,'Đầm hoa nhí xinh xỉu, chất voan 2 lớp bồng bềnh, eo co giãn tôn dáng cực kỳ!','2026-08-21 11:42:09');
UNLOCK TABLES;
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `full_name` varchar(100) NOT NULL,
  `email` varchar(100) NOT NULL,
  `password` varchar(255) NOT NULL,
  `phone` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `role` enum('admin','customer') DEFAULT 'customer',
  `avatar` varchar(255) DEFAULT 'default_avatar.png',
  `created_at` timestamp NULL DEFAULT current_timestamp(),
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

LOCK TABLES `users` WRITE;
INSERT INTO `users` VALUES
(1,'Admin HieuMini','admin@hieumini.vn','$2y$10$eEskGf1Z3z15i1ZzU/kLw.5x8X/0R.hBvM6h76Yq/YJz31X7PZ.Gy','0988889999','Hà Nội, Việt Nam','admin','default_avatar.png','2026-08-21 11:42:09'),
(2,'Nguyễn Văn Nam','khachhang@gmail.com','$2y$10$eEskGf1Z3z15i1ZzU/kLw.5x8X/0R.hBvM6h76Yq/YJz31X7PZ.Gy','0912345678','Cầu Giấy, Hà Nội','customer','default_avatar.png','2026-08-21 11:42:09');
UNLOCK TABLES;


-- ==========================================================================
--  HieuWeb02 — Tech Store   →   CSDL: hieumini_bookstore_db
-- ==========================================================================

-- ==========================================================
-- CƠ SỞ DỮ LIỆU WEBSITE THƯƠNG MẠI ĐIỆN TỬ HIEUMINI (TECH STORE)
-- Hệ quản trị CSDL: MySQL / MariaDB (InnoDB Engine, UTF-8 MB4)
-- ==========================================================

CREATE DATABASE IF NOT EXISTS `hieumini_bookstore_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hieumini_bookstore_db`;
SET FOREIGN_KEY_CHECKS = 0;

-- --------------------------------------------------------
-- 1. Bảng `categories` (Danh mục sản phẩm)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
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
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
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
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
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
    CONSTRAINT `fk_products_categories` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 4. Bảng `coupons` (Mã giảm giá / Khuyến mãi)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
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
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
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
    CONSTRAINT `fk_orders_users` FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 6. Bảng `order_items` (Chi tiết các mặt hàng trong đơn)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `order_id` INT NOT NULL,
    `product_id` INT NULL,
    `product_name` VARCHAR(200) NOT NULL,
    `price` DECIMAL(12, 2) NOT NULL,
    `quantity` INT NOT NULL DEFAULT 1,
    `total` DECIMAL(12, 2) NOT NULL,
    CONSTRAINT `fk_order_items_orders` FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
    CONSTRAINT `fk_order_items_products` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 7. Bảng `reviews` (Đánh giá & Bình luận sản phẩm)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `product_id` INT NOT NULL,
    `user_id` INT NULL,
    `user_name` VARCHAR(100) NOT NULL,
    `rating` INT NOT NULL DEFAULT 5,
    `comment` TEXT NOT NULL,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT `fk_reviews_products` FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- --------------------------------------------------------
-- 8. Bảng `banners` (Banner Slider trang chủ)
-- --------------------------------------------------------
DROP TABLE IF EXISTS `banners`;
CREATE TABLE `banners` (
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
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`, `status`) VALUES
(1, 'Điện thoại Smartphone', 'dien-thoai', 'fa-mobile-screen-button', 'Các dòng điện thoại iPhone, Samsung, Xiaomi, ROG Phone đỉnh cao công nghệ', 1),
(2, 'Laptop & Macbook', 'laptop-macbook', 'fa-laptop', 'Laptop gaming, ultrabook đồ họa, văn phòng mỏng nhẹ hiệu năng mạnh', 1),
(3, 'Máy tính bảng (Tablet)', 'tablet', 'fa-tablet-screen-button', 'iPad Pro, Samsung Galaxy Tab phục vụ học tập, làm việc sáng tạo', 1),
(4, 'Đồng hồ thông minh', 'smartwatch', 'fa-clock', 'Apple Watch, Garmin, Galaxy Watch theo dõi sức khỏe và phong cách', 1),
(5, 'Tai nghe & Âm thanh', 'tai-nghe-am-thanh', 'fa-headphones', 'Tai nghe chống ồn AirPods Pro, Sony WH-1000XM5, loa bluetooth Marshall', 1),
(6, 'Phụ kiện công nghệ', 'phu-kien', 'fa-keyboard', 'Bàn phím cơ, chuột gaming, củ sạc nhanh GaN, pin dự phòng dung lượng cao', 1);

-- Thêm Tài khoản người dùng (Mật khẩu mặc định: admin123 và user123 mã hóa BCRYPT)
-- Password 'admin123': $2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi (hoặc sinh hash chuẩn)
INSERT INTO `users` (`id`, `full_name`, `email`, `password`, `phone`, `address`, `role`, `status`) VALUES
(1, 'Trần Văn Minh Hiếu (Admin)', 'admin@hieumini.vn', '$2y$10$p0bHk9h6K8XGgPms4pYpNuA4h7h5j6H1I3qU1lR4k0W3q5J9L7rWy', '0988889999', 'Tòa nhà HieuMini Tech Tower, Cầu Giấy, Hà Nội', 'admin', 1),
(2, 'Nguyễn Hoàng Nam', 'nam.nguyen@gmail.com', '$2y$10$p0bHk9h6K8XGgPms4pYpNuA4h7h5j6H1I3qU1lR4k0W3q5J9L7rWy', '0912345678', '120 Phố Huế, Hai Bà Trưng, Hà Nội', 'customer', 1),
(3, 'Lê Thị Thu Thảo', 'thao.le@gmail.com', '$2y$10$p0bHk9h6K8XGgPms4pYpNuA4h7h5j6H1I3qU1lR4k0W3q5J9L7rWy', '0934567890', '45 Lê Duẩn, Quận 1, TP. Hồ Chí Minh', 'customer', 1);

-- Thêm Mã giảm giá
INSERT INTO `coupons` (`code`, `discount_percent`, `discount_amount`, `min_order_amount`, `expires_at`, `status`) VALUES
('HIEUMINI2026', 10, 0.00, 5000000.00, '2026-12-31', 1),
('TECHNEW', 5, 0.00, 1000000.00, '2026-12-31', 1),
('GIAM500K', 0, 500000.00, 10000000.00, '2026-12-31', 1);

-- Thêm Sản phẩm công nghệ tiêu biểu
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `brand`, `price`, `sale_price`, `stock_quantity`, `thumbnail`, `images`, `short_desc`, `description`, `specifications`, `is_featured`, `is_flash_sale`, `views`, `rating`) VALUES
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
INSERT INTO `orders` (`id`, `order_code`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `shipping_city`, `payment_method`, `payment_status`, `shipping_status`, `subtotal`, `discount`, `shipping_fee`, `total_amount`, `note`) VALUES
(1, 'ORD-2026-001', 2, 'Nguyễn Hoàng Nam', 'nam.nguyen@gmail.com', '0912345678', '120 Phố Huế, Hai Bà Trưng', 'Hà Nội', 'bank_transfer', 'paid', 'completed', 31990000.00, 0.00, 0.00, 31990000.00, 'Giao giờ hành chính, gọi trước khi giao.'),
(2, 'ORD-2026-002', 3, 'Lê Thị Thu Thảo', 'thao.le@gmail.com', '0934567890', '45 Lê Duẩn, Quận 1', 'TP. Hồ Chí Minh', 'cod', 'unpaid', 'shipping', 6990000.00, 349500.00, 30000.00, 6670500.00, 'Đóng gói cẩn thận có xốp chống sốc.');

-- Thêm Chi tiết đơn hàng mẫu
INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `price`, `quantity`, `total`) VALUES
(1, 1, 'iPhone 16 Pro Max 256GB Titan Sa Mạc', 31990000.00, 1, 31990000.00),
(2, 7, 'Tai nghe Sony WH-1000XM5 Chống Ồn Đỉnh Cao', 6990000.00, 1, 6990000.00);

-- Thêm Đánh giá mẫu
INSERT INTO `reviews` (`product_id`, `user_id`, `user_name`, `rating`, `comment`) VALUES
(1, 2, 'Nguyễn Hoàng Nam', 5, 'Máy cầm rất đầm tay, viền titan sa mạc cực kỳ sang trọng. Giao hàng HieuMini siêu nhanh trong 2h!'),
(7, 3, 'Lê Thị Thu Thảo', 5, 'Khả năng chống ồn của Sony XM5 thực sự kinh ngạc, ngồi quán cafe bật nhạc là tĩnh lặng như trong phòng thu.');


-- ==========================================================================
--  HieuWeb03 — Study & Creative   →   CSDL: hieumini_furniture_db
-- ==========================================================================

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


-- ==========================================================================
--  HieuWeb04 — DatCyber Smart Home   →   CSDL: datcyber_appliances_db
-- ==========================================================================

CREATE DATABASE IF NOT EXISTS `datcyber_appliances_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `datcyber_appliances_db`;
SET FOREIGN_KEY_CHECKS = 0;

-- Drop tables if exists
DROP TABLE IF EXISTS `order_items`;
DROP TABLE IF EXISTS `orders`;
DROP TABLE IF EXISTS `reviews`;
DROP TABLE IF EXISTS `coupons`;
DROP TABLE IF EXISTS `products`;
DROP TABLE IF EXISTS `categories`;
DROP TABLE IF EXISTS `users`;

-- 1. Categories
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(100) NOT NULL,
  `slug` VARCHAR(100) NOT NULL UNIQUE,
  `icon` VARCHAR(50) DEFAULT 'bi-grid',
  `description` TEXT,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Products
CREATE TABLE `products` (
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
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Users
CREATE TABLE `users` (
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
CREATE TABLE `orders` (
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
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Order Items
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT DEFAULT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_image` VARCHAR(255) DEFAULT NULL,
  `price` DECIMAL(12,0) NOT NULL,
  `quantity` INT NOT NULL,
  `subtotal` DECIMAL(12,0) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Reviews
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_name` VARCHAR(100) NOT NULL,
  `rating` INT NOT NULL DEFAULT 5,
  `comment` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. Coupons
CREATE TABLE `coupons` (
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
CREATE TABLE IF NOT EXISTS `contacts` (
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
INSERT INTO `categories` (`id`, `name`, `slug`, `icon`, `description`) VALUES
(1, 'Thiết Bị Nhà Bếp', 'thiet-bi-nha-bep', 'fa-utensils', 'Nồi chiên, máy xay, nồi cơm điện cao tần, bếp từ, lò vi sóng cao cấp'),
(2, 'Robot & Dọn Dẹp', 'robot-don-dep', 'fa-robot', 'Robot hút bụi lau nhà, máy hút bụi cầm tay, máy rửa chén thông minh'),
(3, 'Lọc Khí & Môi Trường', 'loc-khi-moi-truong', 'fa-wind', 'Máy lọc không khí HEPA, máy tạo ẩm, quạt tháp không cánh'),
(4, 'Chăm Sóc & Tiện Ích', 'cham-soc-tien-ich', 'fa-tshirt', 'Bàn là hơi nước đứng, máy sấy quần áo, ấm siêu tốc thông minh'),
(5, 'Máy Pha Chế & Cà Phê', 'may-pha-che-ca-phe', 'fa-coffee', 'Máy pha cà phê Espresso, máy ép chậm trái cây, máy làm sữa hạt');

-- Products
INSERT INTO `products` (`id`, `category_id`, `name`, `slug`, `price`, `old_price`, `image`, `short_description`, `description`, `specs`, `stock`, `rating`, `review_count`, `is_featured`, `is_best_seller`, `is_flash_sale`, `discount_percent`) VALUES
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
INSERT INTO `users` (`id`, `name`, `email`, `password`, `phone`, `address`, `role`) VALUES
(1, 'DatCyber Admin', 'admin@datcyber.vn', '$2y$10$wTfkD72cQyU62zrqo2hQgepG187r7p/zH2tLd5vW9kMhJbC8sB3lK', '0988889999', 'Tòa nhà DatCyber Tower, Cầu Giấy, Hà Nội', 'admin'),
(2, 'Nguyễn Văn An', 'khachhang@gmail.com', '$2y$10$wTfkD72cQyU62zrqo2hQgepG187r7p/zH2tLd5vW9kMhJbC8sB3lK', '0912345678', 'Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội', 'customer');

-- Demo Coupons
INSERT INTO `coupons` (`code`, `discount_type`, `discount_value`, `min_order`, `expiry_date`, `is_active`) VALUES
('DATCYBER10', 'percent', 10, 1000000, '2027-12-31', 1),
('FREESHIP', 'fixed', 50000, 500000, '2027-12-31', 1),
('GIADUNGVIP', 'percent', 15, 3000000, '2027-12-31', 1);

-- Demo Reviews
INSERT INTO `reviews` (`product_id`, `user_name`, `rating`, `comment`, `created_at`) VALUES
(1, 'Trần Minh Quang', 5, 'Nồi chiên DatCyber dùng rất thích, nướng đùi gà da giòn rụm bên trong vẫn ngọt thịt. Mặt kính nhìn được đồ ăn rất tiện!', '2026-08-15 10:30:00'),
(1, 'Lê Thị Thu Thủy', 5, 'Giao hàng nhanh 2 ngày là nhận được, nồi đẹp xịn xò sang góc bếp hẳn.', '2026-08-16 14:20:00'),
(2, 'Hoàng Nhật Minh', 5, 'Robot hút bụi OmniClean X9 lau siêu sạch, tự động giặt giẻ sấy khô nên không bị hôi chút nào. 10/10 điểm đáng tiền.', '2026-08-18 09:15:00'),
(4, 'Phạm Phương Linh', 5, 'Phòng mình 40m2 bật 15 phút là không khí trong lành hẳn, máy chạy êm ru ngủ ngon giấc.', '2026-08-19 21:00:00');

-- Demo Sample Orders
INSERT INTO `orders` (`id`, `order_code`, `user_id`, `customer_name`, `customer_email`, `customer_phone`, `customer_address`, `customer_note`, `payment_method`, `total_amount`, `discount_amount`, `shipping_fee`, `final_amount`, `status`, `created_at`) VALUES
(1, 'DC-20260819-01', 2, 'Nguyễn Văn An', 'khachhang@gmail.com', '0912345678', 'Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội', 'Giao hàng giờ hành chính giúp tôi', 'cod', 2490000, 0, 0, 2490000, 'completed', '2026-08-19 08:30:00'),
(2, 'DC-20260820-02', 2, 'Nguyễn Văn An', 'khachhang@gmail.com', '0912345678', 'Số 123 Đường Cầu Giấy, Quận Cầu Giấy, Hà Nội', 'Gọi trước khi giao 15p', 'banking', 8990000, 899000, 0, 8091000, 'shipping', '2026-08-20 10:15:00');

INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `product_image`, `price`, `quantity`, `subtotal`) VALUES
(1, 1, 'Nồi Chiên Không Dầu Điện Tử DatCyber CrispyPro 6.5L', 'air_fryer.jpg', 2490000, 1, 2490000),
(2, 2, 'Robot Hút Bụi Lau Nhà Tự Động DatCyber OmniClean X9', 'robot_vacuum.jpg', 8990000, 1, 8990000);


-- ==========================================================================
--  HieuWeb05 — Luxury Fitness Club   →   CSDL: hieumini_gym_db
-- ==========================================================================

-- CƠ SỞ DỮ LIỆU WEBSITE THỂ HÌNH CAO CẤP HIEUMINI LUXURY FITNESS CLUB (CHUẨN CEO)
-- Database: hieumini_gym
-- Tương thích: MySQL 5.7+ / MySQL 8.0+ / MariaDB

CREATE DATABASE IF NOT EXISTS `hieumini_gym_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hieumini_gym_db`;

-- 1. Bảng danh mục sản phẩm & dịch vụ (categories)
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `name` VARCHAR(255) NOT NULL,
  `slug` VARCHAR(255) NOT NULL UNIQUE,
  `description` TEXT,
  `icon` VARCHAR(100) DEFAULT 'fa-dumbbell',
  `image` VARCHAR(255) DEFAULT 'cat_default.jpg',
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. Bảng sản phẩm & gói dịch vụ thể hình (products)
DROP TABLE IF EXISTS `products`;
CREATE TABLE `products` (
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
  FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. Bảng người dùng / Admin (users)
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
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
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
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
  FOREIGN KEY (`user_id`) REFERENCES `users`(`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. Bảng chi tiết đơn hàng (order_items)
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `order_id` INT NOT NULL,
  `product_id` INT NOT NULL,
  `product_name` VARCHAR(255) NOT NULL,
  `product_image` VARCHAR(255) NOT NULL,
  `price` DECIMAL(15,2) NOT NULL,
  `quantity` INT NOT NULL DEFAULT 1,
  `subtotal` DECIMAL(15,2) NOT NULL,
  FOREIGN KEY (`order_id`) REFERENCES `orders`(`id`) ON DELETE CASCADE,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. Bảng đặt lịch tập thử VIP & Đăng ký tư vấn (bookings)
DROP TABLE IF EXISTS `bookings`;
CREATE TABLE `bookings` (
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
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
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
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
  `id` INT AUTO_INCREMENT PRIMARY KEY,
  `product_id` INT NOT NULL,
  `user_name` VARCHAR(150) NOT NULL,
  `user_role` VARCHAR(100) DEFAULT 'CEO / Hội Viên VIP',
  `rating` INT NOT NULL DEFAULT 5,
  `comment` TEXT NOT NULL,
  `created_at` DATETIME DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (`product_id`) REFERENCES `products`(`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- NẠP DỮ LIỆU MẪU CHUẨN CEO

-- Danh mục
INSERT INTO `categories` (`id`, `name`, `slug`, `description`, `icon`, `image`) VALUES
(1, 'Gói Hội Viên & Dịch Vụ VIP', 'goi-hoi-vien-vip', 'Đặc quyền trải nghiệm không gian tập luyện chuẩn 5 sao đẳng cấp CEO', 'fa-crown', 'cat_membership.jpg'),
(2, 'Thiết Bị & Máy Tập Thể Hình', 'thiet-bi-may-tap', 'Hệ thống máy tập cơ khí công nghệ cao nhập khẩu tiêu chuẩn Olympic', 'fa-dumbbell', 'cat_equipment.jpg'),
(3, 'Dinh Dưỡng & Thực Phẩm Bổ Sung', 'dinh-duong-the-hinh', 'Thực phẩm dinh dưỡng tinh khiết nhập khẩu chính ngạch từ Hoa Kỳ & Châu Âu', 'fa-capsules', 'cat_supplements.jpg'),
(4, 'Phụ Kiện & Trang Phục Tập', 'phu-kien-trang-phuc', 'Trang bị tập luyện cao cấp da bò thật, sợi carbon và vải co giãn cao cấp', 'fa-tshirt', 'cat_apparel.jpg'),
(5, 'Huấn Luyện Viên & Trị Liệu', 'huan-luyen-vien-tri-lieu', 'Đội ngũ Master Trainer quốc tế và trị liệu thể thao phục hồi cơ chuyên sâu', 'fa-user-ninja', 'cat_pt.jpg');

-- Tài khoản Admin & Member mặc định (Mật khẩu: Admin@123 -> $2y$10$eW4P16P/0vQo0hWbMskg..9z/PqU3kGk5e/O56yTq20oD7/ZfJgxe hoặc hash trực tiếp)
INSERT INTO `users` (`id`, `full_name`, `email`, `phone`, `password`, `role`, `avatar`, `address`) VALUES
(1, 'CEO HieuMini', 'admin@hieumini.com', '0988889999', '$2y$10$k1M43n1.XlQk8R91E7lG2.8ZgVqA1p14Xw0nU4zD5qR8K2u5F9P2e', 'admin', 'ceo_avatar.jpg', 'Tòa nhà HieuMini Tower, Quận 1, TP. Hồ Chí Minh'),
(2, 'Doanh Nhân Nguyễn Hoàng Long', 'member@hieumini.com', '0912345678', '$2y$10$k1M43n1.XlQk8R91E7lG2.8ZgVqA1p14Xw0nU4zD5qR8K2u5F9P2e', 'member', 'member_avatar.jpg', 'Khu Đô Thị Sala, TP. Thủ Đức, TP. Hồ Chí Minh');

-- 30 SẢN PHẨM & DỊCH VỤ FITNESS CHUẨN CEO
INSERT INTO `products` (`id`, `category_id`, `sku`, `name`, `slug`, `price`, `original_price`, `stock`, `rating`, `review_count`, `badge`, `image`, `short_description`, `description`, `specs_json`, `is_featured`, `is_bestseller`) VALUES
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
INSERT INTO `reviews` (`product_id`, `user_name`, `user_role`, `rating`, `comment`) VALUES
(1, 'Doanh nhân Trần Đình Tuấn', 'Chủ tịch Tập đoàn Tuấn Phát', 5, 'Dịch vụ tại HieuMini thực sự xứng tầm CEO. Không gian tập riêng tư, sạch sẽ và máy móc cực kỳ hiện đại. Rất hài lòng với gói Diamond Elite!'),
(6, 'Anh Hoàng Mạnh Thắng', 'CEO TechVenture', 5, 'Máy chạy bộ Commercial X9 Pro chạy siêu êm, màn hình lớn kết nối chạy thực tế ảo như đang chạy ngoài phố Paris. Xứng đáng 5 sao!'),
(14, 'Nguyễn Tiến Dũng', 'Hội viên VIP HieuMini', 5, 'Whey Hydrolyzed vị socola rất thơm ngon, uống không bị ngọt gắt hay nổi mụn. Cơ bắp phục hồi nhanh rõ rệt.'),
(27, 'Lê Thị Thu Hương', 'Giám đốc Điều hành FinCorp', 5, 'HLV Master Trainer rất chuyên nghiệp và thấu hiểu lịch trình bận rộn của tôi. Đã giảm 6kg mỡ thừa sau 2 tháng tập luyện!');

-- Đặt lịch mẫu
INSERT INTO `bookings` (`full_name`, `phone`, `email`, `service_type`, `branch`, `booking_date`, `booking_time`, `fitness_goal`, `notes`, `status`) VALUES
('Phạm Quang Minh', '0987654321', 'minh.pham@enterprise.vn', 'Gói Hội Viên CEO Diamond Elite', 'HieuMini Luxury Diamond - Quận 1, TP.HCM', CURDATE() + INTERVAL 2 DAY, '08:30 - 10:30', 'Rèn luyện thể lực lãnh đạo & Tăng cơ giảm mỡ', 'Cần tư vấn riêng với HLV trưởng', 'confirmed'),
('Vũ Bích Ngọc', '0909112233', 'ngoc.vu@designstudio.com', 'Gói Yoga & Thiền Định Doanh Nhân', 'HieuMini Luxury Landmark - Bình Thạnh, TP.HCM', CURDATE() + INTERVAL 3 DAY, '17:30 - 19:00', 'Giảm căng thẳng sau giờ làm việc', 'Chuẩn bị thảm tập riêng', 'pending');


-- ==========================================================================
--  HieuWeb06 — Source Market   →   CSDL: hieumini_market_db
-- ==========================================================================

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

USE `hieumini_market_db`;

-- ---------------------------------------------------------------------
-- 1. BẢNG users - Người dùng & Quản trị viên
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
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
DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
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
DROP TABLE IF EXISTS `projects`;
CREATE TABLE `projects` (
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
      REFERENCES `categories` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 4. BẢNG project_images - Thư viện ảnh của từng dự án
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `project_images`;
CREATE TABLE `project_images` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id` INT UNSIGNED NOT NULL,
  `image_path` VARCHAR(255) NOT NULL,
  `alt_text`   VARCHAR(180) DEFAULT NULL,
  `sort_order` INT NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_pimages_project` (`project_id`),
  CONSTRAINT `fk_pimages_project` FOREIGN KEY (`project_id`)
      REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 5. BẢNG coupons - Mã giảm giá
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `coupons`;
CREATE TABLE `coupons` (
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
DROP TABLE IF EXISTS `orders`;
CREATE TABLE `orders` (
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
      REFERENCES `users` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 7. BẢNG order_items - Chi tiết đơn hàng
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `order_items`;
CREATE TABLE `order_items` (
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
      REFERENCES `orders` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_oitems_project` FOREIGN KEY (`project_id`)
      REFERENCES `projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 8. BẢNG reviews - Đánh giá của khách hàng
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `reviews`;
CREATE TABLE `reviews` (
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
      REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`)
      REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 9. BẢNG wishlists - Danh sách yêu thích
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `wishlists`;
CREATE TABLE `wishlists` (
  `id`         INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `user_id`    INT UNSIGNED NOT NULL,
  `project_id` INT UNSIGNED NOT NULL,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `uq_wishlist` (`user_id`,`project_id`),
  CONSTRAINT `fk_wishlist_user` FOREIGN KEY (`user_id`)
      REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `fk_wishlist_project` FOREIGN KEY (`project_id`)
      REFERENCES `projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- 10. BẢNG posts - Bài viết blog (phục vụ SEO Content Marketing)
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `posts`;
CREATE TABLE `posts` (
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
DROP TABLE IF EXISTS `contacts`;
CREATE TABLE `contacts` (
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
DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `setting_key`   VARCHAR(80) NOT NULL,
  `setting_value` TEXT DEFAULT NULL,
  `group_name`    VARCHAR(40) NOT NULL DEFAULT 'general',
  PRIMARY KEY (`setting_key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- =====================================================================
--  DỮ LIỆU MẪU
-- =====================================================================

-- Tài khoản: admin@hieumini.vn / admin123  |  user@hieumini.vn / user123
INSERT INTO `users` (`full_name`,`email`,`password`,`phone`,`role`) VALUES
('Trần Văn Minh Hiếu','admin@hieumini.vn','$2y$12$o6aJxBjBSABkZOZ02k.FoO8ifhh9vtoqXxkPLgntbcJkXLVhBNiDi','0987654321','admin'),
('Nguyễn Khách Hàng','user@hieumini.vn','$2y$12$6V4uXWgKwqef5jt7R9si/u7SnTr5C8pytvAOA9lCS/UcvbudGz.E6','0912345678','user'),
('Lê Thu Trang','trang.le@example.com','$2y$12$6V4uXWgKwqef5jt7R9si/u7SnTr5C8pytvAOA9lCS/UcvbudGz.E6','0933444555','user'),
('Phạm Quốc Đạt','dat.pham@example.com','$2y$12$6V4uXWgKwqef5jt7R9si/u7SnTr5C8pytvAOA9lCS/UcvbudGz.E6','0977888999','user');

INSERT INTO `categories` (`name`,`slug`,`description`,`icon`,`sort_order`) VALUES
('Thương mại điện tử','thuong-mai-dien-tu','Website bán hàng, giỏ hàng, thanh toán, quản trị đơn hàng','cart',1),
('Doanh nghiệp & Dịch vụ','doanh-nghiep-dich-vu','Website giới thiệu công ty, dịch vụ, landing page chuyển đổi cao','building',2),
('Portfolio & Cá nhân','portfolio-ca-nhan','Hồ sơ năng lực, CV trực tuyến, blog cá nhân','user',3),
('Quản lý & Dashboard','quan-ly-dashboard','Hệ thống quản lý nội bộ, báo cáo, thống kê','chart',4),
('Giáo dục & Khoá học','giao-duc-khoa-hoc','Website trung tâm, LMS, thi trắc nghiệm trực tuyến','book',5),
('Du lịch & Nhà hàng','du-lich-nha-hang','Đặt phòng, đặt bàn, thực đơn, tour du lịch','globe',6);

INSERT INTO `projects`
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

INSERT INTO `coupons` (`code`,`type`,`value`,`min_total`,`usage_limit`,`expires_at`) VALUES
('HIEUMINI10','percent',10,1000000,200,'2027-12-31'),
('SINHVIEN20','percent',20,500000,500,'2027-12-31'),
('GIAM300K','fixed',300000,2000000,100,'2027-06-30');

INSERT INTO `orders` (`order_code`,`user_id`,`customer_name`,`email`,`phone`,`payment_method`,`subtotal`,`discount`,`total`,`status`,`created_at`) VALUES
('HM20260801001',2,'Nguyễn Khách Hàng','user@hieumini.vn','0912345678','bank',1890000,189000,1701000,'delivered','2026-08-01 09:15:00'),
('HM20260805002',3,'Lê Thu Trang','trang.le@example.com','0933444555','momo',590000,0,590000,'paid','2026-08-05 14:40:00'),
('HM20260812003',4,'Phạm Quốc Đạt','dat.pham@example.com','0977888999','bank',2890000,300000,2590000,'pending','2026-08-12 20:05:00');

INSERT INTO `order_items` (`order_id`,`project_id`,`title`,`license`,`price`,`quantity`) VALUES
(1,1,'HieuShop Pro - Website Bán Hàng Đa Ngành','commercial',1890000,1),
(2,7,'DevFolio - Portfolio Lập Trình Viên','personal',590000,1),
(3,9,'AdminForge - Bộ Khung Quản Trị','extended',2890000,1);

INSERT INTO `reviews` (`project_id`,`user_id`,`rating`,`content`,`created_at`) VALUES
(1,2,5,'Code rất sạch, chú thích tiếng Việt đầy đủ nên mình bàn giao lại cho khách rất nhanh. Phần giỏ hàng AJAX chạy mượt.','2026-08-02 10:20:00'),
(1,3,5,'Mua về làm đồ án tốt nghiệp, thầy đánh giá cao phần bảo mật PDO và CSRF. Rất đáng tiền.','2026-08-06 08:30:00'),
(7,3,5,'Portfolio đẹp, hiệu ứng mượt, tích hợp GitHub API chỉ mất 5 phút cấu hình.','2026-08-07 19:45:00'),
(9,4,5,'Bộ khung quản trị tiết kiệm cho mình cả tuần làm việc. Phân quyền RBAC rõ ràng, dễ mở rộng.','2026-08-13 09:10:00'),
(12,2,4,'LMS đầy đủ tính năng, video chạy ổn. Mong tác giả bổ sung thêm thanh toán quốc tế.','2026-08-15 16:00:00');

INSERT INTO `wishlists` (`user_id`,`project_id`) VALUES
(2,4),(2,9),(2,12),(3,1),(3,7),(4,15);

INSERT INTO `posts` (`title`,`slug`,`excerpt`,`content`,`thumbnail`,`tags`,`views`,`meta_title`,`meta_description`,`published_at`) VALUES
('7 tiêu chí chọn mua mã nguồn website chất lượng','7-tieu-chi-chon-mua-ma-nguon-website-chat-luong','Mua mã nguồn rẻ nhưng phải sửa lại toàn bộ thì không hề rẻ. Bảy tiêu chí dưới đây giúp bạn nhận diện một bộ mã nguồn thực sự đáng tiền.','Thị trường mã nguồn website hiện nay rất sôi động nhưng chất lượng thì chênh lệch rất lớn. Một bộ mã nguồn tốt không chỉ chạy được mà còn phải dễ đọc, dễ mở rộng và an toàn.\n\nTiêu chí đầu tiên là cấu trúc thư mục rõ ràng. Mã nguồn nên tách bạch phần cấu hình, phần thư viện dùng chung, phần giao diện và phần quản trị. Khi cần sửa một tính năng, bạn phải biết ngay nên mở tệp nào.\n\nTiêu chí thứ hai là bảo mật. Hãy kiểm tra xem truy vấn có dùng Prepared Statement hay không, mật khẩu có được băm bằng thuật toán hiện đại hay không, biểu mẫu có mã chống giả mạo CSRF hay không. Ba điểm này quyết định website của bạn có bị tấn công hay không.\n\nTiêu chí thứ ba là hiệu năng. Hãy chạy thử công cụ PageSpeed Insights, kiểm tra ảnh đã được nén và tải chậm chưa, số truy vấn cơ sở dữ liệu trên mỗi trang có hợp lý không.\n\nTiêu chí thứ tư là khả năng tối ưu công cụ tìm kiếm. Mã nguồn phải cho phép chỉnh tiêu đề, mô tả, đường dẫn thân thiện, sinh sitemap và khai báo dữ liệu có cấu trúc.\n\nTiêu chí thứ năm là tài liệu hướng dẫn. Một bộ mã nguồn nghiêm túc luôn kèm tài liệu cài đặt, sơ đồ cơ sở dữ liệu và hướng dẫn tuỳ biến.\n\nTiêu chí thứ sáu là chính sách bản quyền rõ ràng: dùng cá nhân, dùng thương mại hay bàn giao cho khách hàng.\n\nTiêu chí cuối cùng là hỗ trợ sau bán. Hãy ưu tiên nơi cam kết hỗ trợ cài đặt và sửa lỗi trong ít nhất sáu tháng.','assets/images/blog/post-1.svg','mã nguồn, kinh nghiệm, mua bán',1840,'7 tiêu chí chọn mua mã nguồn website chất lượng năm 2026','Hướng dẫn chọn mua mã nguồn website chất lượng: cấu trúc, bảo mật, hiệu năng, SEO, tài liệu, bản quyền và hỗ trợ sau bán.','2026-07-18 09:00:00'),
('Checklist SEO On-page cho website PHP thuần','checklist-seo-onpage-cho-website-php-thuan','Không cần plugin, một website PHP thuần vẫn có thể đạt điểm SEO gần tuyệt đối nếu làm đúng những việc dưới đây.','Nhiều người tin rằng phải dùng mã nguồn mở có sẵn plugin thì mới làm SEO được. Thực tế, website PHP thuần còn có lợi thế vì bạn kiểm soát được từng dòng HTML xuất ra.\n\nTrước hết là thẻ tiêu đề. Mỗi trang phải có một thẻ title duy nhất, dài khoảng 50 đến 60 ký tự và chứa từ khoá chính ở đầu. Thẻ mô tả nên dài 150 đến 160 ký tự, viết như một lời mời nhấp chuột chứ không phải liệt kê từ khoá.\n\nTiếp theo là cấu trúc thẻ tiêu đề nội dung. Mỗi trang chỉ nên có một thẻ H1, các mục lớn dùng H2, mục con dùng H3. Đừng dùng thẻ tiêu đề chỉ để cho chữ to hơn.\n\nĐường dẫn thân thiện là yếu tố thứ ba. Hãy chuyển tiêu đề tiếng Việt có dấu thành chuỗi không dấu, viết thường, nối bằng dấu gạch ngang và không chứa tham số khó hiểu.\n\nDữ liệu có cấu trúc giúp Google hiểu trang của bạn. Với trang sản phẩm hãy khai báo schema Product kèm giá và đánh giá, với bài viết hãy dùng schema Article, với đường dẫn phân cấp hãy dùng BreadcrumbList.\n\nCuối cùng là hiệu năng và trải nghiệm. Nén ảnh sang định dạng WebP, đặt thuộc tính chiều rộng và chiều cao cho ảnh để tránh giật bố cục, tải chậm ảnh ngoài màn hình đầu tiên, và luôn kiểm tra trên thiết bị di động trước khi phát hành.','assets/images/blog/post-2.svg','seo, php, hướng dẫn',2260,'Checklist SEO On-page đầy đủ cho website PHP thuần','Danh sách kiểm tra SEO on-page cho website PHP: thẻ title, meta description, heading, slug, schema, Core Web Vitals.','2026-07-26 10:30:00'),
('Bảo mật website PHP: 10 lỗ hổng thường gặp và cách phòng','bao-mat-website-php-10-lo-hong-thuong-gap','SQL Injection, XSS, CSRF và bảy lỗ hổng khác vẫn đang khiến hàng nghìn website PHP bị tấn công mỗi ngày.','Bảo mật không phải là tính năng thêm vào cuối dự án mà là cách bạn viết từng dòng mã ngay từ đầu.\n\nLỗ hổng phổ biến nhất là SQL Injection, xảy ra khi dữ liệu người dùng được ghép trực tiếp vào câu truy vấn. Giải pháp triệt để là dùng PDO với Prepared Statement và tham số ràng buộc.\n\nThứ hai là Cross-site Scripting, khi nội dung người dùng nhập được in ra HTML mà không lọc. Hãy luôn đi qua hàm htmlspecialchars với cờ ENT_QUOTES trước khi hiển thị.\n\nThứ ba là Cross-site Request Forgery. Mọi biểu mẫu thay đổi dữ liệu phải kèm một mã ngẫu nhiên lưu trong phiên và được kiểm tra ở phía máy chủ.\n\nThứ tư là tải tệp không kiểm soát. Hãy kiểm tra phần mở rộng, kiểm tra kiểu MIME thật, đổi tên tệp và cấm thực thi PHP trong thư mục tải lên.\n\nThứ năm là lưu mật khẩu sai cách. Không bao giờ lưu mật khẩu dạng thô hay băm MD5, hãy dùng password_hash với BCRYPT hoặc Argon2.\n\nCác lỗ hổng còn lại gồm lộ thông tin lỗi ra người dùng, phiên làm việc không được làm mới sau khi đăng nhập, thiếu giới hạn số lần đăng nhập sai, phân quyền kiểm tra ở giao diện mà không kiểm tra ở máy chủ, và thư viện bên thứ ba không được cập nhật.\n\nMột website an toàn là kết quả của thói quen viết mã cẩn thận, không phải của một lần rà soát duy nhất.','assets/images/blog/post-3.svg','bảo mật, php, pdo',1590,'Bảo mật website PHP: 10 lỗ hổng thường gặp và cách phòng chống','Tổng hợp 10 lỗ hổng bảo mật phổ biến của website PHP như SQL Injection, XSS, CSRF và cách phòng chống bằng mã nguồn.','2026-08-03 08:00:00'),
('Tối ưu Core Web Vitals cho website bán hàng','toi-uu-core-web-vitals-cho-website-ban-hang','LCP, INP và CLS ảnh hưởng trực tiếp tới thứ hạng và tỉ lệ chuyển đổi. Đây là cách cải thiện cả ba chỉ số.','Core Web Vitals là bộ ba chỉ số Google dùng để đo trải nghiệm thực tế của người dùng. Với website bán hàng, mỗi giây chậm trễ có thể làm giảm đáng kể tỉ lệ chuyển đổi.\n\nChỉ số đầu tiên là LCP, thời gian hiển thị khối nội dung lớn nhất. Với trang chủ, khối này thường là ảnh nền khu vực hero. Hãy nén ảnh sang WebP, khai báo kích thước, tải trước ảnh hero và tránh chèn phông chữ chặn hiển thị.\n\nChỉ số thứ hai là INP, đo độ phản hồi khi người dùng tương tác. Hãy chia nhỏ các tác vụ JavaScript dài, dùng sự kiện uỷ quyền thay vì gắn hàng trăm trình lắng nghe, và hoãn các đoạn mã không cần thiết cho lần hiển thị đầu.\n\nChỉ số thứ ba là CLS, đo mức độ giật bố cục. Nguyên nhân thường gặp là ảnh không khai báo kích thước, quảng cáo chèn động và phông chữ đổi kích thước khi tải xong. Hãy đặt thuộc tính width và height cho mọi thẻ ảnh, dành sẵn khoảng trống cho nội dung tải chậm và dùng font-display swap kèm phông dự phòng có kích thước tương đương.\n\nSau khi tối ưu, hãy đo lại bằng dữ liệu thực tế từ Search Console chứ không chỉ dựa vào điểm số trong phòng thí nghiệm.','assets/images/blog/post-4.svg','hiệu năng, core web vitals, seo',1230,'Tối ưu Core Web Vitals cho website bán hàng: LCP, INP, CLS','Hướng dẫn cải thiện LCP, INP và CLS cho website thương mại điện tử để tăng thứ hạng tìm kiếm và tỉ lệ chuyển đổi.','2026-08-10 11:15:00'),
('Từ đồ án môn học đến sản phẩm bán được tiền','tu-do-an-mon-hoc-den-san-pham-ban-duoc-tien','Khoảng cách giữa một đồ án được điểm A và một sản phẩm khách hàng chịu trả tiền nằm ở năm điều rất cụ thể.','Rất nhiều sinh viên có đồ án chạy tốt nhưng không thể bán được. Lý do không nằm ở kỹ thuật mà nằm ở cách đóng gói sản phẩm.\n\nĐiều đầu tiên là tài liệu. Khách hàng cần biết cách cài đặt trong mười phút. Hãy viết một tệp README rõ ràng gồm yêu cầu hệ thống, các bước nhập cơ sở dữ liệu, thông tin tài khoản mẫu và câu trả lời cho các lỗi thường gặp.\n\nĐiều thứ hai là dữ liệu mẫu. Một website trống rỗng không thuyết phục được ai. Hãy chuẩn bị dữ liệu mẫu thật, ảnh thật, nội dung thật.\n\nĐiều thứ ba là bản trình diễn trực tuyến. Người mua muốn bấm thử trước khi trả tiền. Hãy triển khai một bản demo và ghi lại một video ngắn khoảng hai phút.\n\nĐiều thứ tư là khả năng tuỳ biến. Hãy tách màu sắc, phông chữ, thông tin liên hệ ra thành cấu hình để người mua đổi thương hiệu mà không cần sửa mã.\n\nĐiều thứ năm là cam kết hỗ trợ. Một chính sách hỗ trợ sáu tháng rõ ràng làm tăng đáng kể tỉ lệ chốt đơn, đồng thời buộc bạn phải viết mã cẩn thận hơn.\n\nKhi làm đủ năm điều này, đồ án của bạn không còn là bài tập mà đã trở thành một sản phẩm.','assets/images/blog/post-5.svg','khởi nghiệp, freelance, kinh nghiệm',2740,'Từ đồ án môn học đến sản phẩm bán được tiền cho sinh viên IT','Năm bước biến đồ án môn học thành sản phẩm thương mại: tài liệu, dữ liệu mẫu, demo, khả năng tuỳ biến và hỗ trợ.','2026-08-16 09:45:00'),
('Thiết kế giao diện tối chuẩn khả năng tiếp cận','thiet-ke-giao-dien-toi-chuan-kha-nang-tiep-can','Giao diện tối đẹp mắt nhưng rất dễ vi phạm độ tương phản. Đây là cách làm đúng ngay từ đầu.','Giao diện tối đang là xu hướng nhưng cũng là nơi lỗi tương phản xuất hiện nhiều nhất.\n\nSai lầm phổ biến nhất là dùng màu đen tuyệt đối làm nền và trắng tuyệt đối làm chữ. Độ tương phản quá cao gây mỏi mắt và tạo hiện tượng nhoè chữ. Hãy chọn nền xám rất tối và chữ xám rất sáng thay vì hai cực trắng đen.\n\nSai lầm thứ hai là dùng màu bão hoà cao trên nền tối. Màu neon rực rỡ nhìn rất bắt mắt trong ảnh chụp nhưng khi đọc lâu sẽ gây khó chịu. Hãy giữ màu neon cho điểm nhấn, đường viền và trạng thái, còn phần chữ chính nên dùng màu trung tính.\n\nSai lầm thứ ba là giảm độ mờ của chữ phụ xuống quá thấp. Chữ phụ vẫn phải đạt tỉ lệ tương phản tối thiểu 4,5 trên 1 so với nền.\n\nSai lầm thứ tư là bỏ vòng viền khi lấy tiêu điểm bàn phím. Người dùng bàn phím và trình đọc màn hình phụ thuộc hoàn toàn vào vòng viền này.\n\nCuối cùng, hãy luôn tôn trọng tuỳ chọn giảm chuyển động của hệ điều hành. Một truy vấn media đơn giản có thể tắt toàn bộ hiệu ứng cho người dùng nhạy cảm với chuyển động.','assets/images/blog/post-6.svg','ui ux, dark mode, accessibility',1470,'Thiết kế giao diện tối chuẩn khả năng tiếp cận WCAG','Hướng dẫn thiết kế dark mode đúng chuẩn WCAG: chọn nền, độ tương phản, màu neon, vòng focus và giảm chuyển động.','2026-08-19 15:20:00');

INSERT INTO `contacts` (`name`,`email`,`phone`,`subject`,`message`,`status`,`created_at`) VALUES
('Vũ Minh Anh','minhanh@example.com','0901234567','Báo giá website bán hàng','Mình cần một website bán mỹ phẩm tương tự HieuShop Pro nhưng muốn thêm chức năng tích điểm thành viên. Vui lòng báo giá giúp mình.','new','2026-08-18 10:12:00'),
('Đặng Hoàng Nam','nam.dang@example.com','0918273645','Hỗ trợ cài đặt','Mình đã mua AdminForge nhưng khi nhập database gặp lỗi collation. Nhờ bên bạn hỗ trợ.','processing','2026-08-19 14:35:00');

INSERT INTO `settings` (`setting_key`,`setting_value`,`group_name`) VALUES
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

-- ==========================================================================
--  TÀI KHOẢN QUẢN TRỊ DEMO DÙNG CHUNG CHO CẢ 6 DỰ ÁN CON
--    Email    : admin@hieumini.vn
--    Mật khẩu : demo123
--  Đặt cùng một email và mật khẩu cho tài khoản quản trị của cả sáu
--  dự án, để một tài khoản duy nhất đăng nhập được vào mọi trang quản trị.
--  Muốn dùng tài khoản gốc của từng dự án: bỏ khối UPDATE bên dưới.
-- ==========================================================================
UPDATE `hieumini_db`.`users` SET `email` = 'admin@hieumini.vn', `password` = '$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role` = 'admin';
UPDATE `hieumini_bookstore_db`.`users` SET `email` = 'admin@hieumini.vn', `password` = '$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role` = 'admin';
UPDATE `hieumini_furniture_db`.`users` SET `email` = 'admin@hieumini.vn', `password` = '$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role` = 'admin';
UPDATE `datcyber_appliances_db`.`users` SET `email` = 'admin@hieumini.vn', `password` = '$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role` = 'admin';
UPDATE `hieumini_gym_db`.`users` SET `email` = 'admin@hieumini.vn', `password` = '$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role` = 'admin';
UPDATE `hieumini_market_db`.`users` SET `email` = 'admin@hieumini.vn', `password` = '$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role` = 'admin';

-- ==========================================================================
--  KIỂM TRA SAU KHI IMPORT — chạy lệnh dưới đây, phải thấy đủ 7 dòng:
--    SHOW DATABASES;
--  hieumini_portfolio  hieumini_db  hieumini_bookstore_db  hieumini_furniture_db  datcyber_appliances_db  hieumini_gym_db  hieumini_market_db
-- ==========================================================================