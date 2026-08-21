-- ======================================================================
--  Cơ sở dữ liệu: hieumini_portfolio (cổng trưng bày HieuMini)
--  Dán CẢ tệp vào TiDB SQL Editor rồi bấm Run.
--  Không cần chọn database — mọi bảng đã gắn sẵn tên CSDL.
-- ======================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;

CREATE DATABASE IF NOT EXISTS `hieumini_portfolio` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;


-- ---------------------------------------------------------------------
-- Bảng 1: projects — Hồ sơ từng dự án website được trưng bày
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_portfolio`.`projects`;
CREATE TABLE `hieumini_portfolio`.`projects` (
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
DROP TABLE IF EXISTS `hieumini_portfolio`.`project_features`;
CREATE TABLE `hieumini_portfolio`.`project_features` (
  `id`          INT UNSIGNED NOT NULL AUTO_INCREMENT,
  `project_id`  INT UNSIGNED NOT NULL,
  `icon`        VARCHAR(40)  NOT NULL DEFAULT 'spark' COMMENT 'Khóa icon SVG nội bộ',
  `title`       VARCHAR(160) NOT NULL,
  `content`     VARCHAR(500) NOT NULL DEFAULT '',
  `sort_order`  SMALLINT     NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idx_features_project` (`project_id`, `sort_order`),
  CONSTRAINT `fk_features_project` FOREIGN KEY (`project_id`)
    REFERENCES `hieumini_portfolio`.`projects` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------------------------------------------------------------------
-- Bảng 3: messages — Tin nhắn liên hệ từ khách truy cập
-- ---------------------------------------------------------------------
DROP TABLE IF EXISTS `hieumini_portfolio`.`messages`;
CREATE TABLE `hieumini_portfolio`.`messages` (
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
DROP TABLE IF EXISTS `hieumini_portfolio`.`visit_logs`;
CREATE TABLE `hieumini_portfolio`.`visit_logs` (
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
    REFERENCES `hieumini_portfolio`.`projects` (`id`) ON DELETE SET NULL ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;


-- =====================================================================
--  DỮ LIỆU MẪU — 6 dự án website trong thư mục projects/
-- =====================================================================
INSERT INTO `hieumini_portfolio`.`projects`
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

INSERT INTO `hieumini_portfolio`.`project_features` (`project_id`,`icon`,`title`,`content`,`sort_order`) VALUES
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
INSERT INTO `hieumini_portfolio`.`visit_logs` (`project_id`,`path`,`ip`,`created_at`)
SELECT
  1 + FLOOR(RAND() * 6),
  '/project.php',
  '127.0.0.1',
  NOW() - INTERVAL FLOOR(RAND() * 30) DAY
FROM `hieumini_portfolio`.`project_features` f1, `hieumini_portfolio`.`project_features` f2
LIMIT 400;

UPDATE `hieumini_portfolio`.`projects` p
SET p.`views` = (SELECT COUNT(*) FROM `hieumini_portfolio`.`visit_logs` v WHERE v.`project_id` = p.`id`);

INSERT INTO `hieumini_portfolio`.`messages` (`name`,`email`,`subject`,`content`,`ip`,`is_read`) VALUES
('Nguyễn Thị Lan','lan.nguyen@example.com','Hỏi về dự án Fashion Studio','Chào bạn, mình muốn tham khảo mã nguồn phần bộ lọc size của dự án HieuWeb01. Bạn có thể chia sẻ thêm không?','127.0.0.1',0),
('Trần Quốc Bảo','bao.tran@example.com','Hợp tác phát triển','Bên mình đang cần một website bán hàng tương tự HieuWeb04. Rất mong được trao đổi thêm về chi phí và thời gian.','127.0.0.1',1);

-- =====================================================================
--  KẾT THÚC SCRIPT
--  Ghi chú: Hệ thống KHÔNG lưu tài khoản quản trị trong cơ sở dữ liệu.
--  Trang admin chỉ được kích hoạt khi máy chủ có biến môi trường
--  ADMIN_PASSWORD. Xem README.md, mục "Bật trang quản trị".
-- =====================================================================

SET FOREIGN_KEY_CHECKS = 1;
