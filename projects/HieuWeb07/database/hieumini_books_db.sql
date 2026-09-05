-- =====================================================================
-- HieuMini Books — Cơ sở dữ liệu thư viện sách trực tuyến
-- Sinh tự động bởi tools/generate_sql.py — không sửa tay tệp này.
-- Bộ mã utf8mb4 để lưu đầy đủ dấu tiếng Việt và ký tự mở rộng.
-- =====================================================================

CREATE DATABASE IF NOT EXISTS `hieumini_books_db`
  CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `hieumini_books_db`;

SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;
DROP TABLE IF EXISTS `reviews`, `books`, `authors`, `categories`, `admins`, `messages`;
SET FOREIGN_KEY_CHECKS = 1;

-- ---------- Danh mục thể loại ----------
CREATE TABLE `categories` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `slug`        VARCHAR(80)  NOT NULL UNIQUE,
  `name`        VARCHAR(120) NOT NULL,
  `description` TEXT,
  `bg_color`    CHAR(7)      NOT NULL DEFAULT '#1C1917',
  `accent`      CHAR(7)      NOT NULL DEFAULT '#C89B3C',
  `sort_order`  SMALLINT     NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Tác giả ----------
CREATE TABLE `authors` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `slug`       VARCHAR(120) NOT NULL UNIQUE,
  `name`       VARCHAR(160) NOT NULL,
  `country`    VARCHAR(80),
  `birth_year` SMALLINT,
  `bio`        TEXT,
  INDEX `idx_authors_name` (`name`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Sách ----------
CREATE TABLE `books` (
  `id`             INT AUTO_INCREMENT PRIMARY KEY,
  `slug`           VARCHAR(180) NOT NULL UNIQUE,
  `title`          VARCHAR(220) NOT NULL,
  `author_id`      INT NOT NULL,
  `category_id`    INT NOT NULL,
  `publisher`      VARCHAR(160),
  `published_year` SMALLINT,
  `pages`          SMALLINT,
  `language`       VARCHAR(40) DEFAULT 'Tiếng Việt',
  `isbn`           VARCHAR(24),
  `cover`          VARCHAR(180),
  `summary`        TEXT,
  `is_featured`    TINYINT(1) NOT NULL DEFAULT 0,
  `views`          INT NOT NULL DEFAULT 0,
  `created_at`     DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_books_author`   FOREIGN KEY (`author_id`)   REFERENCES `authors`(`id`)    ON DELETE CASCADE,
  CONSTRAINT `fk_books_category` FOREIGN KEY (`category_id`) REFERENCES `categories`(`id`) ON DELETE CASCADE,
  INDEX `idx_books_category` (`category_id`),
  INDEX `idx_books_author`   (`author_id`),
  INDEX `idx_books_title`    (`title`),
  INDEX `idx_books_featured` (`is_featured`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Đánh giá của bạn đọc ----------
CREATE TABLE `reviews` (
  `id`          INT AUTO_INCREMENT PRIMARY KEY,
  `book_id`     INT NOT NULL,
  `reader_name` VARCHAR(120) NOT NULL,
  `rating`      TINYINT NOT NULL,
  `content`     TEXT,
  `is_approved` TINYINT(1) NOT NULL DEFAULT 0,
  `created_at`  DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT `fk_reviews_book` FOREIGN KEY (`book_id`) REFERENCES `books`(`id`) ON DELETE CASCADE,
  CONSTRAINT `chk_reviews_rating` CHECK (`rating` BETWEEN 1 AND 5),
  INDEX `idx_reviews_book` (`book_id`, `is_approved`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Tài khoản quản trị ----------
CREATE TABLE `admins` (
  `id`            INT AUTO_INCREMENT PRIMARY KEY,
  `username`      VARCHAR(60)  NOT NULL UNIQUE,
  `password_hash` VARCHAR(255) NOT NULL,
  `full_name`     VARCHAR(120),
  `created_at`    DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Tin nhắn liên hệ ----------
CREATE TABLE `messages` (
  `id`         INT AUTO_INCREMENT PRIMARY KEY,
  `name`       VARCHAR(120) NOT NULL,
  `email`      VARCHAR(160) NOT NULL,
  `subject`    VARCHAR(200),
  `content`    TEXT NOT NULL,
  `is_read`    TINYINT(1) NOT NULL DEFAULT 0,
  `created_at` DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ---------- Dữ liệu mẫu ----------
INSERT INTO `categories` (`slug`,`name`,`description`,`bg_color`,`accent`,`sort_order`) VALUES
('van-hoc-viet-nam','Văn học Việt Nam','Truyện, tiểu thuyết và ký của các tác giả trong nước qua nhiều thời kỳ.','#1C1917','#C89B3C',0),
('van-hoc-the-gioi','Văn học thế giới','Tác phẩm kinh điển và đương đại được dịch sang tiếng Việt.','#16181D','#B08D57',1),
('khoa-hoc-cong-nghe','Khoa học & Công nghệ','Sách phổ biến khoa học, lập trình và tư duy kỹ thuật.','#101820','#7FB3B3',2),
('lich-su-dia-chi','Lịch sử & Địa chí','Biên khảo lịch sử, địa chí và hồi ký tư liệu.','#1A1614','#C0703A',3),
('triet-hoc-tu-tuong','Triết học & Tư tưởng','Tác phẩm triết học, luân lý và tư tưởng phương Đông lẫn phương Tây.','#141419','#9B8EC4',4),
('thieu-nhi','Thiếu nhi','Truyện tranh, truyện kể và sách kỹ năng dành cho bạn đọc nhỏ tuổi.','#131A17','#7FBF8E',5);

INSERT INTO `authors` (`slug`,`name`,`country`,`birth_year`,`bio`) VALUES
('nam-cao','Nam Cao','Việt Nam',1915,'Nhà văn hiện thực phê phán, nổi tiếng với các truyện ngắn viết về người nông dân và trí thức nghèo trước năm 1945.'),
('to-hoai','Tô Hoài','Việt Nam',1920,'Cây bút văn xuôi bền bỉ bậc nhất của văn học Việt Nam hiện đại, để lại khối lượng tác phẩm đồ sộ cho cả người lớn lẫn thiếu nhi.'),
('nguyen-nhat-anh','Nguyễn Nhật Ánh','Việt Nam',1955,'Nhà văn được bạn đọc trẻ yêu thích, chuyên viết về tuổi mới lớn với giọng văn trong trẻo và hóm hỉnh.'),
('vu-trong-phung','Vũ Trọng Phụng','Việt Nam',1912,'Nhà văn, nhà báo nổi bật của trào lưu hiện thực, được mệnh danh là ông vua phóng sự đất Bắc.'),
('thach-lam','Thạch Lam','Việt Nam',1910,'Thành viên Tự Lực văn đoàn, viết truyện ngắn giàu chất thơ và tinh tế trong quan sát đời thường.'),
('antoine-de-saint-exupery','Antoine de Saint-Exupéry','Pháp',1900,'Phi công kiêm nhà văn, tác giả của những trang viết về bầu trời, tình bạn và trách nhiệm.'),
('ernest-hemingway','Ernest Hemingway','Hoa Kỳ',1899,'Nhà văn đoạt giải Nobel Văn học 1954, nổi tiếng với lối viết tiết chế và câu văn ngắn gọn.'),
('haruki-murakami','Haruki Murakami','Nhật Bản',1949,'Tiểu thuyết gia đương đại có ảnh hưởng rộng, hòa trộn đời thường với yếu tố siêu thực.'),
('george-orwell','George Orwell','Anh',1903,'Nhà văn, nhà báo, tác giả của những tác phẩm chính trị có sức ảnh hưởng lâu dài.'),
('fyodor-dostoevsky','Fyodor Dostoevsky','Nga',1821,'Tiểu thuyết gia bậc thầy về tâm lý và các câu hỏi luân lý của con người.'),
('carl-sagan','Carl Sagan','Hoa Kỳ',1934,'Nhà thiên văn học và người phổ biến khoa học, nổi tiếng với khả năng diễn đạt trong sáng.'),
('yuval-noah-harari','Yuval Noah Harari','Israel',1976,'Sử gia nghiên cứu lịch sử dài hạn của loài người và tác động của công nghệ.'),
('donald-knuth','Donald Knuth','Hoa Kỳ',1938,'Nhà khoa học máy tính, tác giả bộ sách nền tảng về thuật toán và là cha đẻ của TeX.'),
('tran-trong-kim','Trần Trọng Kim','Việt Nam',1883,'Học giả, nhà giáo dục, tác giả bộ sử phổ thông có ảnh hưởng lớn đầu thế kỷ 20.'),
('phan-boi-chau','Phan Bội Châu','Việt Nam',1867,'Chí sĩ yêu nước, đồng thời là tác giả của nhiều trước tác chính luận và tự truyện.'),
('nguyen-hien-le','Nguyễn Hiến Lê','Việt Nam',1912,'Học giả, dịch giả và nhà văn hóa với hơn một trăm đầu sách biên khảo, dịch thuật.'),
('lao-tu','Lão Tử','Trung Quốc',NULL,'Nhân vật được xem là tác giả Đạo Đức Kinh, nền tảng của tư tưởng Đạo gia.'),
('marcus-aurelius','Marcus Aurelius','La Mã',121,'Hoàng đế La Mã, để lại tập ghi chép cá nhân trở thành tác phẩm trụ cột của phái Khắc Kỷ.'),
('to-huu','Tố Hữu','Việt Nam',1920,'Nhà thơ cách mạng, giọng thơ trữ tình chính trị tiêu biểu của văn học Việt Nam thế kỷ 20.'),
('astrid-lindgren','Astrid Lindgren','Thụy Điển',1907,'Nhà văn thiếu nhi Thụy Điển, người tạo ra những nhân vật trẻ em độc lập và giàu tưởng tượng.');

INSERT INTO `books` (`slug`,`title`,`author_id`,`category_id`,`publisher`,`published_year`,
  `pages`,`language`,`isbn`,`cover`,`summary`,`is_featured`,`views`) VALUES
('chi-pheo','Chí Phèo',(SELECT id FROM authors WHERE slug='nam-cao'),(SELECT id FROM categories WHERE slug='van-hoc-viet-nam'),'NXB Văn học',1941,168,'Tiếng Việt','978-604-1-00001-1','chi-pheo.webp','Tập truyện ngắn xoay quanh bi kịch bị tha hóa của người nông dân trong xã hội thuộc địa nửa phong kiến. Nhân vật Chí Phèo trở thành một điển hình văn học về khát vọng được làm người lương thiện.',1,1146),
('lao-hac','Lão Hạc',(SELECT id FROM authors WHERE slug='nam-cao'),(SELECT id FROM categories WHERE slug='van-hoc-viet-nam'),'NXB Văn học',1943,124,'Tiếng Việt','978-604-1-00002-8','lao-hac.webp','Câu chuyện về một người cha già nghèo khó chọn cái chết để giữ lại mảnh vườn cho con, khắc họa lòng tự trọng của người nông dân Việt Nam.',0,382),
('de-men-phieu-luu-ky','Dế Mèn phiêu lưu ký',(SELECT id FROM authors WHERE slug='to-hoai'),(SELECT id FROM categories WHERE slug='van-hoc-viet-nam'),'NXB Kim Đồng',1941,156,'Tiếng Việt','978-604-2-00003-5','de-men-phieu-luu-ky.webp','Hành trình trưởng thành của chú Dế Mèn qua những chuyến đi, những lần vấp ngã và tình bạn — tác phẩm thiếu nhi kinh điển của văn học Việt Nam.',1,2141),
('vo-chong-a-phu','Vợ chồng A Phủ',(SELECT id FROM authors WHERE slug='to-hoai'),(SELECT id FROM categories WHERE slug='van-hoc-viet-nam'),'NXB Văn học',1953,96,'Tiếng Việt','978-604-1-00004-2','vo-chong-a-phu.webp','Truyện ngắn viết về số phận người dân miền núi Tây Bắc và hành trình tự giải phóng khỏi áp bức.',0,1328),
('mat-biec','Mắt biếc',(SELECT id FROM authors WHERE slug='nguyen-nhat-anh'),(SELECT id FROM categories WHERE slug='van-hoc-viet-nam'),'NXB Trẻ',1990,268,'Tiếng Việt','978-604-3-00005-9','mat-biec.webp','Chuyện tình đơn phương kéo dài từ tuổi thơ làng Đo Đo tới khi trưởng thành, giọng văn trong trẻo mà day dứt.',1,1989),
('so-do','Số đỏ',(SELECT id FROM authors WHERE slug='vu-trong-phung'),(SELECT id FROM categories WHERE slug='van-hoc-viet-nam'),'NXB Văn học',1936,232,'Tiếng Việt','978-604-1-00006-6','so-do.webp','Tiểu thuyết trào phúng châm biếm xã hội thành thị Việt Nam thời Âu hóa, với nhân vật Xuân Tóc Đỏ nổi tiếng.',0,471),
('ha-noi-bam-sau-pho-phuong','Hà Nội băm sáu phố phường',(SELECT id FROM authors WHERE slug='thach-lam'),(SELECT id FROM categories WHERE slug='van-hoc-viet-nam'),'NXB Văn học',1943,112,'Tiếng Việt','978-604-1-00007-3','ha-noi-bam-sau-pho-phuong.webp','Tập tùy bút ghi lại nếp sống, món ăn và không khí phố cổ Hà Nội bằng lối viết nhẹ nhàng, tinh tế.',0,974),
('hoang-tu-be','Hoàng tử bé',(SELECT id FROM authors WHERE slug='antoine-de-saint-exupery'),(SELECT id FROM categories WHERE slug='van-hoc-the-gioi'),'NXB Hội Nhà văn',1943,128,'Tiếng Việt','978-604-4-00008-0','hoang-tu-be.webp','Câu chuyện ngụ ngôn về một cậu bé đến từ tiểu tinh cầu B612, nói về tình bạn, sự mất mát và cách nhìn thế giới bằng trái tim.',1,208),
('ong-gia-va-bien-ca','Ông già và biển cả',(SELECT id FROM authors WHERE slug='ernest-hemingway'),(SELECT id FROM categories WHERE slug='van-hoc-the-gioi'),'NXB Văn học',1952,144,'Tiếng Việt','978-604-1-00009-7','ong-gia-va-bien-ca.webp','Cuộc chiến đơn độc giữa ông lão đánh cá Santiago với con cá kiếm khổng lồ, biểu tượng cho phẩm giá con người trước thất bại.',0,1261),
('rung-na-uy','Rừng Na Uy',(SELECT id FROM authors WHERE slug='haruki-murakami'),(SELECT id FROM categories WHERE slug='van-hoc-the-gioi'),'NXB Hội Nhà văn',1987,396,'Tiếng Việt','978-604-4-00010-3','rung-na-uy.webp','Tiểu thuyết về ký ức, mất mát và tuổi trẻ Nhật Bản cuối thập niên 1960.',1,888),
('kafka-ben-bo-bien','Kafka bên bờ biển',(SELECT id FROM authors WHERE slug='haruki-murakami'),(SELECT id FROM categories WHERE slug='van-hoc-the-gioi'),'NXB Văn học',2002,508,'Tiếng Việt','978-604-1-00011-0','kafka-ben-bo-bien.webp','Hai tuyến truyện đan xen giữa hiện thực và siêu thực, đi tìm lời giải cho một lời nguyền và một ký ức bị đánh mất.',0,560),
('1984','1984',(SELECT id FROM authors WHERE slug='george-orwell'),(SELECT id FROM categories WHERE slug='van-hoc-the-gioi'),'NXB Dân trí',1949,384,'Tiếng Việt','978-604-5-00012-7','1984.webp','Bức tranh về một xã hội toàn trị nơi ngôn ngữ, ký ức và sự thật đều bị kiểm soát.',1,273),
('trai-suc-vat','Trại súc vật',(SELECT id FROM authors WHERE slug='george-orwell'),(SELECT id FROM categories WHERE slug='van-hoc-the-gioi'),'NXB Hội Nhà văn',1945,152,'Tiếng Việt','978-604-4-00013-4','trai-suc-vat.webp','Truyện ngụ ngôn chính trị mượn chuyện đàn gia súc nổi dậy để nói về sự tha hóa của quyền lực.',0,2349),
('toi-ac-va-trung-phat','Tội ác và trừng phạt',(SELECT id FROM authors WHERE slug='fyodor-dostoevsky'),(SELECT id FROM categories WHERE slug='van-hoc-the-gioi'),'NXB Văn học',1866,672,'Tiếng Việt','978-604-1-00014-1','toi-ac-va-trung-phat.webp','Tiểu thuyết tâm lý theo chân một sinh viên nghèo sau tội ác của anh ta, và hành trình dằn vặt đi tới sám hối.',0,959),
('anh-em-nha-karamazov','Anh em nhà Karamazov',(SELECT id FROM authors WHERE slug='fyodor-dostoevsky'),(SELECT id FROM categories WHERE slug='van-hoc-the-gioi'),'NXB Văn học',1880,912,'Tiếng Việt','978-604-1-00015-8','anh-em-nha-karamazov.webp','Tác phẩm cuối cùng của Dostoevsky, đặt ra những câu hỏi lớn về đức tin, tự do và trách nhiệm.',0,199),
('vu-tru','Vũ trụ',(SELECT id FROM authors WHERE slug='carl-sagan'),(SELECT id FROM categories WHERE slug='khoa-hoc-cong-nghe'),'NXB Thế giới',1980,432,'Tiếng Việt','978-604-6-00016-5','vu-tru.webp','Hành trình phổ biến khoa học đưa người đọc đi từ nguồn gốc vũ trụ tới vị trí nhỏ bé của Trái Đất trong không gian.',1,64),
('cham-xanh-mo-nhat','Chấm xanh mờ nhạt',(SELECT id FROM authors WHERE slug='carl-sagan'),(SELECT id FROM categories WHERE slug='khoa-hoc-cong-nghe'),'NXB Thế giới',1994,288,'Tiếng Việt','978-604-6-00017-2','cham-xanh-mo-nhat.webp','Suy tưởng về vị trí của Trái Đất nhìn từ rìa Thái Dương hệ, và lời kêu gọi con người giữ gìn hành tinh duy nhất mình có.',0,933),
('sapiens-luoc-su-loai-nguoi','Sapiens: Lược sử loài người',(SELECT id FROM authors WHERE slug='yuval-noah-harari'),(SELECT id FROM categories WHERE slug='khoa-hoc-cong-nghe'),'NXB Tri thức',2011,554,'Tiếng Việt','978-604-7-00018-9','sapiens-luoc-su-loai-nguoi.webp','Nhìn lại bảy mươi nghìn năm lịch sử loài người qua ba cuộc cách mạng: nhận thức, nông nghiệp và khoa học.',1,714),
('homo-deus-luoc-su-tuong-lai','Homo Deus: Lược sử tương lai',(SELECT id FROM authors WHERE slug='yuval-noah-harari'),(SELECT id FROM categories WHERE slug='khoa-hoc-cong-nghe'),'NXB Tri thức',2015,496,'Tiếng Việt','978-604-7-00019-6','homo-deus-luoc-su-tuong-lai.webp','Phần tiếp nối của Sapiens, bàn về việc con người sẽ đi về đâu khi nắm trong tay công nghệ sinh học và trí tuệ nhân tạo.',0,907),
('nghe-thuat-lap-trinh-may-tinh','Nghệ thuật lập trình máy tính',(SELECT id FROM authors WHERE slug='donald-knuth'),(SELECT id FROM categories WHERE slug='khoa-hoc-cong-nghe'),'NXB Khoa học và Kỹ thuật',1968,650,'Tiếng Việt','978-604-8-00020-2','nghe-thuat-lap-trinh-may-tinh.webp','Bộ sách nền tảng về thuật toán và phân tích độ phức tạp, được xem là tài liệu kinh điển của ngành khoa học máy tính.',0,1396),
('viet-nam-su-luoc','Việt Nam sử lược',(SELECT id FROM authors WHERE slug='tran-trong-kim'),(SELECT id FROM categories WHERE slug='lich-su-dia-chi'),'NXB Văn học',1920,596,'Tiếng Việt','978-604-1-00021-9','viet-nam-su-luoc.webp','Bộ thông sử bằng chữ quốc ngữ đầu tiên trình bày lịch sử Việt Nam một cách hệ thống từ thời dựng nước tới đầu thế kỷ 20.',1,214),
('nguc-trung-thu','Ngục trung thư',(SELECT id FROM authors WHERE slug='phan-boi-chau'),(SELECT id FROM categories WHERE slug='lich-su-dia-chi'),'NXB Văn học',1914,184,'Tiếng Việt','978-604-1-00022-6','nguc-trung-thu.webp','Tự truyện viết trong ngục, ghi lại chặng đường hoạt động và tâm sự của một chí sĩ đầu thế kỷ 20.',0,438),
('dong-kinh-nghia-thuc','Đông Kinh nghĩa thục',(SELECT id FROM authors WHERE slug='nguyen-hien-le'),(SELECT id FROM categories WHERE slug='lich-su-dia-chi'),'NXB Văn hóa Thông tin',1968,216,'Tiếng Việt','978-604-9-00023-3','dong-kinh-nghia-thuc.webp','Biên khảo về phong trào giáo dục và canh tân đầu thế kỷ 20 tại Hà Nội.',0,1661),
('dao-duc-kinh','Đạo Đức Kinh',(SELECT id FROM authors WHERE slug='lao-tu'),(SELECT id FROM categories WHERE slug='triet-hoc-tu-tuong'),'NXB Hồng Đức',-500,176,'Tiếng Việt','978-604-A-00025-7','dao-duc-kinh.webp','Tác phẩm nền tảng của Đạo gia gồm 81 chương ngắn, bàn về Đạo, Đức và lối sống thuận tự nhiên.',1,239),
('suy-tuong','Suy tưởng',(SELECT id FROM authors WHERE slug='marcus-aurelius'),(SELECT id FROM categories WHERE slug='triet-hoc-tu-tuong'),'NXB Trẻ',180,264,'Tiếng Việt','978-604-3-00026-4','suy-tuong.webp','Tập ghi chép riêng tư của một hoàng đế La Mã, trở thành cẩm nang thực hành của chủ nghĩa Khắc Kỷ.',1,1236),
('tu-hoc-mot-nhu-cau-thoi-dai','Tự học — một nhu cầu thời đại',(SELECT id FROM authors WHERE slug='nguyen-hien-le'),(SELECT id FROM categories WHERE slug='triet-hoc-tu-tuong'),'NXB Văn hóa Thông tin',1964,248,'Tiếng Việt','978-604-9-00028-8','tu-hoc-mot-nhu-cau-thoi-dai.webp','Bàn về phương pháp tự học và thái độ đọc sách, viết cho người trẻ muốn tự bồi đắp tri thức.',0,545),
('tu-ay','Từ ấy',(SELECT id FROM authors WHERE slug='to-huu'),(SELECT id FROM categories WHERE slug='van-hoc-viet-nam'),'NXB Văn học',1946,132,'Tiếng Việt','978-604-2-00029-5','tu-ay.webp','Tập thơ đầu tay đánh dấu bước ngoặt trong đời thơ của tác giả, nhiều bài đã đi vào sách giáo khoa.',0,1769),
('pippi-tat-dai','Pippi Tất Dài',(SELECT id FROM authors WHERE slug='astrid-lindgren'),(SELECT id FROM categories WHERE slug='thieu-nhi'),'NXB Kim Đồng',1945,208,'Tiếng Việt','978-604-2-00030-1','pippi-tat-dai.webp','Cô bé khỏe nhất thế giới sống một mình cùng con khỉ và con ngựa, mang tới cho trẻ em tinh thần tự do và tưởng tượng.',1,166),
('cho-toi-xin-mot-ve-di-tuoi-tho','Cho tôi xin một vé đi tuổi thơ',(SELECT id FROM authors WHERE slug='nguyen-nhat-anh'),(SELECT id FROM categories WHERE slug='thieu-nhi'),'NXB Trẻ',2008,208,'Tiếng Việt','978-604-3-00031-8','cho-toi-xin-mot-ve-di-tuoi-tho.webp','Người lớn kể lại tuổi thơ của chính mình bằng giọng hài hước, gợi nhớ những trò nghịch ngợm ai cũng từng trải qua.',1,2356),
('kinh-van-hoa','Kính vạn hoa',(SELECT id FROM authors WHERE slug='nguyen-nhat-anh'),(SELECT id FROM categories WHERE slug='thieu-nhi'),'NXB Kim Đồng',1995,224,'Tiếng Việt','978-604-2-00032-5','kinh-van-hoa.webp','Bộ truyện dài nhiều tập về nhóm bạn học trò và những tình huống dở khóc dở cười của tuổi mới lớn.',0,194);

INSERT INTO `reviews` (`book_id`,`reader_name`,`rating`,`content`,`is_approved`) VALUES
((SELECT id FROM books WHERE slug='chi-pheo'),'Bảo Ngọc',4,'Mình đọc một mạch hết trong hai buổi tối. Rất đáng để đọc lại lần nữa.',1),
((SELECT id FROM books WHERE slug='lao-hac'),'Bảo Ngọc',3,'Tái bản lần này chỉnh sửa kỹ hơn bản cũ, đáng tiền.',1),
((SELECT id FROM books WHERE slug='de-men-phieu-luu-ky'),'Phương Nhi',5,'Bản dịch mượt, giữ được giọng văn của nguyên tác.',1),
((SELECT id FROM books WHERE slug='de-men-phieu-luu-ky'),'Minh Thư',3,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',1),
((SELECT id FROM books WHERE slug='vo-chong-a-phu'),'Đức Thắng',5,'Văn phong giản dị mà thấm. Đọc chậm mới thấy hay.',0),
((SELECT id FROM books WHERE slug='vo-chong-a-phu'),'Minh Thư',3,'Mình đọc một mạch hết trong hai buổi tối. Rất đáng để đọc lại lần nữa.',1),
((SELECT id FROM books WHERE slug='mat-biec'),'Đức Thắng',5,'Bản dịch mượt, giữ được giọng văn của nguyên tác.',1),
((SELECT id FROM books WHERE slug='mat-biec'),'Tuấn Kiệt',3,'Mua làm quà tặng, người nhận rất ưng.',0),
((SELECT id FROM books WHERE slug='mat-biec'),'Hoàng Long',4,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',0),
((SELECT id FROM books WHERE slug='so-do'),'Quốc Bảo',3,'Văn phong giản dị mà thấm. Đọc chậm mới thấy hay.',0),
((SELECT id FROM books WHERE slug='ha-noi-bam-sau-pho-phuong'),'Quốc Bảo',5,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',0),
((SELECT id FROM books WHERE slug='ha-noi-bam-sau-pho-phuong'),'Hoàng Long',5,'Một cuốn nên có trong tủ sách gia đình. Con mình cũng thích.',0),
((SELECT id FROM books WHERE slug='hoang-tu-be'),'Thu Trang',5,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',1),
((SELECT id FROM books WHERE slug='ong-gia-va-bien-ca'),'Hà Linh',3,'Bản dịch mượt, giữ được giọng văn của nguyên tác.',1),
((SELECT id FROM books WHERE slug='ong-gia-va-bien-ca'),'Quốc Bảo',3,'Sách in đẹp, giấy dày, đọc rất đã mắt. Nội dung thì khỏi bàn.',1),
((SELECT id FROM books WHERE slug='ong-gia-va-bien-ca'),'Minh Thư',4,'Mình đọc một mạch hết trong hai buổi tối. Rất đáng để đọc lại lần nữa.',1),
((SELECT id FROM books WHERE slug='rung-na-uy'),'Bảo Ngọc',4,'Đọc xong thấy suy nghĩ khác đi khá nhiều về chuyện cũ.',1),
((SELECT id FROM books WHERE slug='rung-na-uy'),'Thu Trang',5,'Tái bản lần này chỉnh sửa kỹ hơn bản cũ, đáng tiền.',1),
((SELECT id FROM books WHERE slug='rung-na-uy'),'Thu Trang',4,'Tái bản lần này chỉnh sửa kỹ hơn bản cũ, đáng tiền.',0),
((SELECT id FROM books WHERE slug='kafka-ben-bo-bien'),'Mai Anh',5,'Một cuốn nên có trong tủ sách gia đình. Con mình cũng thích.',1),
((SELECT id FROM books WHERE slug='1984'),'Gia Huy',3,'Mình đọc một mạch hết trong hai buổi tối. Rất đáng để đọc lại lần nữa.',1),
((SELECT id FROM books WHERE slug='1984'),'Hà Linh',5,'Sách in đẹp, giấy dày, đọc rất đã mắt. Nội dung thì khỏi bàn.',0),
((SELECT id FROM books WHERE slug='trai-suc-vat'),'Quốc Bảo',3,'Phần đầu hơi chậm nhưng càng về sau càng cuốn. Kiên nhẫn sẽ được đền đáp.',1),
((SELECT id FROM books WHERE slug='trai-suc-vat'),'Đức Thắng',3,'Tái bản lần này chỉnh sửa kỹ hơn bản cũ, đáng tiền.',1),
((SELECT id FROM books WHERE slug='toi-ac-va-trung-phat'),'Hoàng Long',4,'Mua làm quà tặng, người nhận rất ưng.',1),
((SELECT id FROM books WHERE slug='toi-ac-va-trung-phat'),'Minh Thư',5,'Mình đọc một mạch hết trong hai buổi tối. Rất đáng để đọc lại lần nữa.',1),
((SELECT id FROM books WHERE slug='toi-ac-va-trung-phat'),'Hoàng Long',3,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',0),
((SELECT id FROM books WHERE slug='anh-em-nha-karamazov'),'Quốc Bảo',4,'Đọc xong thấy suy nghĩ khác đi khá nhiều về chuyện cũ.',1),
((SELECT id FROM books WHERE slug='anh-em-nha-karamazov'),'Hoàng Long',3,'Bản dịch mượt, giữ được giọng văn của nguyên tác.',0),
((SELECT id FROM books WHERE slug='vu-tru'),'Hoàng Long',4,'Phần đầu hơi chậm nhưng càng về sau càng cuốn. Kiên nhẫn sẽ được đền đáp.',0),
((SELECT id FROM books WHERE slug='cham-xanh-mo-nhat'),'Đức Thắng',5,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',0),
((SELECT id FROM books WHERE slug='cham-xanh-mo-nhat'),'Minh Thư',5,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',1),
((SELECT id FROM books WHERE slug='cham-xanh-mo-nhat'),'Minh Thư',5,'Phần đầu hơi chậm nhưng càng về sau càng cuốn. Kiên nhẫn sẽ được đền đáp.',1),
((SELECT id FROM books WHERE slug='sapiens-luoc-su-loai-nguoi'),'Hà Linh',5,'Phần đầu hơi chậm nhưng càng về sau càng cuốn. Kiên nhẫn sẽ được đền đáp.',1),
((SELECT id FROM books WHERE slug='sapiens-luoc-su-loai-nguoi'),'Hoàng Long',5,'Tái bản lần này chỉnh sửa kỹ hơn bản cũ, đáng tiền.',0),
((SELECT id FROM books WHERE slug='homo-deus-luoc-su-tuong-lai'),'Mai Anh',5,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',1),
((SELECT id FROM books WHERE slug='nghe-thuat-lap-trinh-may-tinh'),'Gia Huy',5,'Mua làm quà tặng, người nhận rất ưng.',0),
((SELECT id FROM books WHERE slug='nghe-thuat-lap-trinh-may-tinh'),'Tuấn Kiệt',3,'Mình đọc một mạch hết trong hai buổi tối. Rất đáng để đọc lại lần nữa.',1),
((SELECT id FROM books WHERE slug='nghe-thuat-lap-trinh-may-tinh'),'Mai Anh',3,'Một cuốn nên có trong tủ sách gia đình. Con mình cũng thích.',1),
((SELECT id FROM books WHERE slug='viet-nam-su-luoc'),'Bảo Ngọc',5,'Sách in đẹp, giấy dày, đọc rất đã mắt. Nội dung thì khỏi bàn.',0),
((SELECT id FROM books WHERE slug='viet-nam-su-luoc'),'Hoàng Long',5,'Phần đầu hơi chậm nhưng càng về sau càng cuốn. Kiên nhẫn sẽ được đền đáp.',1),
((SELECT id FROM books WHERE slug='viet-nam-su-luoc'),'Bảo Ngọc',3,'Tái bản lần này chỉnh sửa kỹ hơn bản cũ, đáng tiền.',1),
((SELECT id FROM books WHERE slug='nguc-trung-thu'),'Thu Trang',5,'Sách in đẹp, giấy dày, đọc rất đã mắt. Nội dung thì khỏi bàn.',1),
((SELECT id FROM books WHERE slug='nguc-trung-thu'),'Gia Huy',4,'Văn phong giản dị mà thấm. Đọc chậm mới thấy hay.',0),
((SELECT id FROM books WHERE slug='dong-kinh-nghia-thuc'),'Tuấn Kiệt',4,'Sách in đẹp, giấy dày, đọc rất đã mắt. Nội dung thì khỏi bàn.',1),
((SELECT id FROM books WHERE slug='dong-kinh-nghia-thuc'),'Tuấn Kiệt',5,'Tái bản lần này chỉnh sửa kỹ hơn bản cũ, đáng tiền.',1),
((SELECT id FROM books WHERE slug='dong-kinh-nghia-thuc'),'Gia Huy',4,'Phần đầu hơi chậm nhưng càng về sau càng cuốn. Kiên nhẫn sẽ được đền đáp.',1),
((SELECT id FROM books WHERE slug='dao-duc-kinh'),'Hoàng Long',3,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',1),
((SELECT id FROM books WHERE slug='dao-duc-kinh'),'Thu Trang',5,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',1),
((SELECT id FROM books WHERE slug='suy-tuong'),'Hà Linh',4,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',1),
((SELECT id FROM books WHERE slug='suy-tuong'),'Đức Thắng',3,'Một cuốn nên có trong tủ sách gia đình. Con mình cũng thích.',1),
((SELECT id FROM books WHERE slug='suy-tuong'),'Thu Trang',5,'Mua làm quà tặng, người nhận rất ưng.',1),
((SELECT id FROM books WHERE slug='tu-hoc-mot-nhu-cau-thoi-dai'),'Đăng Khoa',5,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',0),
((SELECT id FROM books WHERE slug='tu-hoc-mot-nhu-cau-thoi-dai'),'Phương Nhi',3,'Bản dịch mượt, giữ được giọng văn của nguyên tác.',0),
((SELECT id FROM books WHERE slug='tu-hoc-mot-nhu-cau-thoi-dai'),'Tuấn Kiệt',3,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',1),
((SELECT id FROM books WHERE slug='tu-ay'),'Phương Nhi',4,'Nội dung tốt nhưng phần chú thích còn hơi sơ sài.',1),
((SELECT id FROM books WHERE slug='tu-ay'),'Minh Thư',4,'Mình đọc một mạch hết trong hai buổi tối. Rất đáng để đọc lại lần nữa.',1),
((SELECT id FROM books WHERE slug='pippi-tat-dai'),'Hà Linh',3,'Phần đầu hơi chậm nhưng càng về sau càng cuốn. Kiên nhẫn sẽ được đền đáp.',0),
((SELECT id FROM books WHERE slug='pippi-tat-dai'),'Quốc Bảo',3,'Phần đầu hơi chậm nhưng càng về sau càng cuốn. Kiên nhẫn sẽ được đền đáp.',1),
((SELECT id FROM books WHERE slug='pippi-tat-dai'),'Minh Thư',3,'Mình đọc một mạch hết trong hai buổi tối. Rất đáng để đọc lại lần nữa.',1),
((SELECT id FROM books WHERE slug='cho-toi-xin-mot-ve-di-tuoi-tho'),'Mai Anh',3,'Phần đầu hơi chậm nhưng càng về sau càng cuốn. Kiên nhẫn sẽ được đền đáp.',0),
((SELECT id FROM books WHERE slug='cho-toi-xin-mot-ve-di-tuoi-tho'),'Tuấn Kiệt',5,'Bản dịch mượt, giữ được giọng văn của nguyên tác.',1),
((SELECT id FROM books WHERE slug='cho-toi-xin-mot-ve-di-tuoi-tho'),'Mai Anh',3,'Văn phong giản dị mà thấm. Đọc chậm mới thấy hay.',1),
((SELECT id FROM books WHERE slug='kinh-van-hoa'),'Gia Huy',5,'Mua làm quà tặng, người nhận rất ưng.',1),
((SELECT id FROM books WHERE slug='kinh-van-hoa'),'Hoàng Long',4,'Mua làm quà tặng, người nhận rất ưng.',1),
((SELECT id FROM books WHERE slug='kinh-van-hoa'),'Gia Huy',4,'Đọc xong thấy suy nghĩ khác đi khá nhiều về chuyện cũ.',1);

INSERT INTO `admins` (`username`,`password_hash`,`full_name`) VALUES
('admin','$2y$10$fUFFXf/TqekJNEghad3NDunVMawk47OF93fIgiUoemGylwXCGJaiu','Trần Văn Minh Hiếu');
-- Mật khẩu mặc định: hieumini2026 — ĐỔI NGAY sau khi cài đặt xong.

INSERT INTO `messages` (`name`,`email`,`subject`,`content`,`is_read`) VALUES
('Ngọc Anh','ngocanh@example.com','Hỏi về bản in mới','Cho mình hỏi cuốn Chí Phèo có bản bìa cứng không ạ?',0),
('Trung Hiếu','trunghieu@example.com','Góp ý giao diện','Trang web đọc rất dễ chịu, mong có thêm chức năng lưu sách yêu thích.',1);

