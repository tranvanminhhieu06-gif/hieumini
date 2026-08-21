-- =====================================================================
--  MIGRATION: Thêm cột "sold" (Đã bán) vào bảng projects
--  Dùng cho cơ sở dữ liệu hieumini_portfolio ĐÃ nạp dữ liệu từ trước.
--  Cách chạy: mở phpMyAdmin > chọn CSDL hieumini_portfolio > tab SQL >
--             dán toàn bộ nội dung này > bấm Go/Thực hiện.
-- =====================================================================

USE `hieumini_portfolio`;

-- 1) Thêm cột sold (đặt ngay sau cột views). Bỏ qua nếu đã tồn tại.
ALTER TABLE `projects`
  ADD COLUMN IF NOT EXISTS `sold` INT UNSIGNED NOT NULL DEFAULT 0
  COMMENT 'Số lượng đã bán / đăng ký' AFTER `views`;

-- 2) Nạp số liệu mẫu (quản trị viên có thể sửa lại trong trang Quản lý dự án).
UPDATE `projects` SET `sold` = 1284 WHERE `code` = 'HieuWeb01';
UPDATE `projects` SET `sold` = 947  WHERE `code` = 'HieuWeb02';
UPDATE `projects` SET `sold` = 2156 WHERE `code` = 'HieuWeb03';
UPDATE `projects` SET `sold` = 763  WHERE `code` = 'HieuWeb04';
UPDATE `projects` SET `sold` = 421  WHERE `code` = 'HieuWeb05';
UPDATE `projects` SET `sold` = 1539 WHERE `code` = 'HieuWeb06';

-- Xong. Tải lại trang Quản lý dự án để thấy cột "Đã bán".
