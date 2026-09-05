-- =====================================================================
-- Thêm dự án thứ 7 (HieuMini Books) vào cổng trưng bày HieuMini
--
-- Chạy MỘT LẦN sau khi đã nạp hieumini_portfolio.sql:
--   mysql -u root hieumini_portfolio < database/them_hieuweb07.sql
--
-- Tệp này chạy lại được nhiều lần mà không sinh dữ liệu trùng: dòng DELETE
-- ở đầu dọn bản ghi cũ, và khoá ngoại ON DELETE CASCADE tự dọn luôn phần
-- điểm nổi bật đi kèm.
-- =====================================================================

USE `hieumini_portfolio`;
SET NAMES utf8mb4;

DELETE FROM `projects` WHERE `code` = 'HieuWeb07';

INSERT INTO `projects`
 (`code`,`slug`,`name`,`tagline`,`summary`,`description`,
  `category`,`tech_stack`,`folder`,`entry_file`,`admin_path`,`db_name`,
  `accent_from`,`accent_to`,`year`,`table_count`,`page_count`,
  `is_featured`,`status`,`sort_order`)
VALUES
('HieuWeb07','books-library','HieuMini Books',
 'Thư viện sách trực tuyến với giao diện editorial tối và hiệu ứng cuộn GSAP',

 'Thư viện tra cứu 30 đầu sách qua 6 thể loại, viết bằng PHP thuần và MySQL. Giao diện tối kiểu tạp chí theo lưới 12 cột, chữ Cormorant Garamond và Crimson Pro tự host. Có tìm kiếm gợi ý tức thời, đánh giá của bạn đọc phải qua duyệt, và toàn bộ 30 bìa sách do một script Python tự sinh.',

 'HieuMini Books là dự án duy nhất trong bộ sưu tập không phải thương mại điện tử: đây là một thư viện tra cứu thuần tuý, không giỏ hàng, không thanh toán. Phạm vi hẹp lại giúp dồn công sức vào chất lượng phần tra cứu và phần giao diện.\n\nVề giao diện, dự án đi theo phong cách Swiss Modernism biến thể tối — lưới 12 cột, khoảng thở rộng, gần như không hoạ tiết trang trí, mọi nhấn nhá dồn vào chữ và một sắc đồng thau duy nhất. Hai phông serif Cormorant Garamond và Crimson Pro được tự host kèm lát cắt tiếng Việt riêng, nên trang chạy được cả khi máy không có mạng và không để lộ IP người đọc sang máy chủ bên thứ ba. Bảng màu đạt tương phản WCAG AA ở cả hai chế độ sáng và tối, cặp thấp nhất đo được 4,77:1.\n\nHiệu ứng chuyển động dùng GSAP kết hợp Lenis cho cuộn mượt, ScrollTrigger cho hoạt ảnh theo vị trí cuộn và SplitText để tách tiêu đề. Điểm khác biệt nằm ở nguyên tắc: hiệu ứng chỉ là lớp phủ thêm, không phải điều kiện để đọc được nội dung. CSS chỉ ẩn nội dung khi JavaScript thực sự chạy, nên tắt JS hay robot tìm kiếm ghé qua thì chữ vẫn hiện đầy đủ — điều kiện tiên quyết để trang được lập chỉ mục đúng.\n\nPhần tối ưu tìm kiếm gồm thẻ mô tả riêng từng trang, canonical, Open Graph, dữ liệu có cấu trúc JSON-LD kiểu Book kèm điểm đánh giá tổng hợp, và sơ đồ trang sinh động thẳng từ cơ sở dữ liệu. Ba mươi ảnh bìa là thiết kế chữ do script Python của dự án tạo ra, tránh hoàn toàn vấn đề bản quyền ảnh bìa của nhà xuất bản.',

 'Sách',
 'PHP 8,MySQL,GSAP,Lenis,SplitText,WCAG AA,JSON-LD,Dark/Light',
 'HieuWeb07','index.php','admin/login.php','hieumini_books_db',
 '#D8A94A','#7FB3B3',
 2026, 6, 17, 1, 'published', 7);

INSERT INTO `project_features` (`project_id`,`icon`,`title`,`content`,`sort_order`) VALUES
((SELECT id FROM projects WHERE code='HieuWeb07'),'search','Gợi ý tìm kiếm tức thời',
 'Gõ tới đâu gợi ý hiện tới đó kèm ảnh bìa, chờ 220ms sau phím cuối mới gọi máy chủ và tự huỷ yêu cầu cũ. Điều hướng được bằng phím mũi tên.',1),

((SELECT id FROM projects WHERE code='HieuWeb07'),'spark','Hiệu ứng không chặn nội dung',
 'GSAP, Lenis và SplitText chỉ là lớp phủ thêm. Thiếu thư viện hoặc tắt JavaScript thì trang tự lui về cách hiển thị thường, chữ vẫn đọc được đầy đủ.',2),

((SELECT id FROM projects WHERE code='HieuWeb07'),'eye','Tương phản đạt WCAG AA',
 'Tám cặp màu ở cả hai giao diện sáng và tối đều vượt ngưỡng 4,5:1, thấp nhất đo được 4,77:1. Vùng bấm tối thiểu 44×44 pixel.',3),

((SELECT id FROM projects WHERE code='HieuWeb07'),'shield','Đánh giá phải qua duyệt',
 'Nhận xét của bạn đọc lưu ở trạng thái chờ, quản trị viên duyệt mới hiển thị công khai và mới được tính vào điểm trung bình.',4),

((SELECT id FROM projects WHERE code='HieuWeb07'),'code','Chuẩn SEO đầy đủ',
 'JSON-LD kiểu Book kèm điểm đánh giá, canonical, Open Graph, robots.txt và sơ đồ trang sinh động từ cơ sở dữ liệu.',5),

((SELECT id FROM projects WHERE code='HieuWeb07'),'layers','Bìa sách tự sinh',
 'Ba mươi bìa là thiết kế chữ do script Python của dự án tạo ra từ cùng một nguồn dữ liệu với cơ sở dữ liệu, nên tên tệp không bao giờ lệch.',6);

UPDATE `projects` SET `views` = 318, `sold` = 0 WHERE `code` = 'HieuWeb07';

SELECT CONCAT('Đã thêm ', COUNT(*), ' dự án HieuWeb07') AS ket_qua
  FROM `projects` WHERE `code` = 'HieuWeb07';

-- =====================================================================
-- Tài khoản demo dùng chung
--
-- Trang chi tiết dự án trên cổng HieuMini quảng cáo một tài khoản demo
-- dùng chung cho cả bảy dự án: admin@hieumini.vn / demo123. Phần dưới bổ
-- sung tài khoản đó vào cơ sở dữ liệu của HieuWeb07 để lời quảng cáo ấy
-- đúng với thực tế — nếu thiếu, người xem bấm "Trang quản trị dự án" từ
-- trang chính sẽ không đăng nhập được.
--
-- Bản hieumini_books_db.sql mới đã có sẵn tài khoản này; đoạn dưới chỉ
-- cần thiết nếu bạn đã nạp bản cũ trước đó.
-- =====================================================================

USE `hieumini_books_db`;

INSERT INTO `admins` (`username`, `password_hash`, `full_name`) VALUES
('admin@hieumini.vn', '$2y$10$aPz8E7Db655Q.Knl94pkbu4MPbMhUfv4wP0JPmA8hSn0XQtBFHyB.', 'Tài khoản demo')
ON DUPLICATE KEY UPDATE
  `password_hash` = VALUES(`password_hash`),
  `full_name`     = VALUES(`full_name`);

SELECT GROUP_CONCAT(username SEPARATOR ' · ') AS tai_khoan_quan_tri FROM `admins`;
