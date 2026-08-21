-- ======================================================================
--  Cơ sở dữ liệu: hieumini_db
--  Dán CẢ tệp vào TiDB SQL Editor rồi bấm Run.
--  Không cần chọn database — mọi bảng đã gắn sẵn tên CSDL.
-- ======================================================================
SET NAMES utf8mb4;
SET FOREIGN_KEY_CHECKS = 0;


CREATE DATABASE IF NOT EXISTS `hieumini_db` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;

DROP TABLE IF EXISTS `hieumini_db`.`categories`;
CREATE TABLE `hieumini_db`.`categories` (
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

INSERT INTO `hieumini_db`.`categories` VALUES
(1,'Áo Thun & Polo','ao-thun-polo','Bộ sưu tập áo thun Unisex, áo Polo phong cách năng động, cotton thoáng mát','cat_ao_thun.jpg',1,'2026-08-21 11:42:09'),
(2,'Áo Sơ Mi Cao Cấp','ao-so-mi','Áo sơ mi Oxford, sơ mi lụa công sở và dạo phố lịch lãm','cat_ao_somi.jpg',1,'2026-08-21 11:42:09'),
(3,'Áo Khoác & Hoodie','ao-khoac-hoodie','Áo khoác Bomber, Varsity jacket, Hoodie nỉ bông ấm áp thời thượng','cat_ao_khoac.jpg',1,'2026-08-21 11:42:09'),
(4,'Quần Jeans & Denim','quan-jeans','Quần Jean Slimfit, Jean ống rộng Baggy, Denim wash cao cấp','cat_quan_jeans.jpg',1,'2026-08-21 11:42:09'),
(5,'Quần Kaki & Trousers','quan-kaki','Quần Kaki Chino, quần tây âu dáng suông thanh lịch công sở','cat_quan_kaki.jpg',1,'2026-08-21 11:42:09'),
(6,'Váy & Đầm Nữ','vay-dam-nu','Váy hoa nhí Vintage, đầm suông, chân váy chữ A phong cách Hàn Quốc','cat_vay_dam.jpg',1,'2026-08-21 11:42:09'),
(7,'Phụ Kiện Thời Trang','phu-kien','Thắt lưng da, nón kết lưỡi trai, túi đeo chéo, vớ thời trang','cat_phu_kien.jpg',1,'2026-08-21 11:42:09');
DROP TABLE IF EXISTS `hieumini_db`.`coupons`;
CREATE TABLE `hieumini_db`.`coupons` (
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

INSERT INTO `hieumini_db`.`coupons` VALUES
(1,'HIEUMINI10','percentage',10.00,200000.00,200,0,'2026-12-31',1,'2026-08-21 11:42:09'),
(2,'FREESHIP','fixed',30000.00,300000.00,500,0,'2026-12-31',1,'2026-08-21 11:42:09'),
(3,'WELCOME50K','fixed',50000.00,400000.00,100,0,'2026-12-31',1,'2026-08-21 11:42:09');
DROP TABLE IF EXISTS `hieumini_db`.`order_items`;
CREATE TABLE `hieumini_db`.`order_items` (
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
  CONSTRAINT `fk_order_items_order` FOREIGN KEY (`order_id`) REFERENCES `hieumini_db`.`orders` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_order_items_product` FOREIGN KEY (`product_id`) REFERENCES `hieumini_db`.`products` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hieumini_db`.`order_items` VALUES
(1,1,1,'Áo Thun Nam Nữ Streetwear Basic Cotton 100%',199000.00,1,'L','Đen',199000.00),
(2,1,4,'Áo Sơ Mi Nam Oxford Dài Tay Chống Nhăn',350000.00,1,'XL','Xanh Nhạt',350000.00),
(3,2,7,'Áo Khoác Bomber Kaki 2 Lớp Form Rộng Unisex',480000.00,1,'L','Xanh Rêu',480000.00),
(4,2,14,'Đầm Hoa Nhí Dáng Xòe Vintage Cổ Vuông Tiểu Thư',390000.00,1,'M','Hoa Nhí Hồng',390000.00),
(5,3,10,'Quần Jean Nam Slimfit Co Giãn Rửa Màu Vintage',380000.00,1,'31','Xanh Đậm',380000.00),
(6,3,2,'Áo Polo Nam Phối Cổ Bo Dệt Sang Trọng',289000.00,1,'L','Xanh Navy',289000.00);
DROP TABLE IF EXISTS `hieumini_db`.`orders`;
CREATE TABLE `hieumini_db`.`orders` (
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
  CONSTRAINT `fk_orders_user` FOREIGN KEY (`user_id`) REFERENCES `hieumini_db`.`users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hieumini_db`.`orders` VALUES
(1,2,'HM-ORD-1001','Nguyễn Văn Nam','0912345678','khachhang@gmail.com','Số 18 Duy Tân, Cầu Giấy, Hà Nội','cod','paid','completed',549000.00,30000.00,0.00,'FREESHIP',NULL,'2026-08-21 11:42:09'),
(2,NULL,'HM-ORD-1002','Trần Thị Mai','0987654321','maitran@gmail.com','120 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh','banking','paid','shipping',870000.00,0.00,0.00,NULL,NULL,'2026-08-21 11:42:09'),
(3,NULL,'HM-ORD-1003','Lê Hoàng Long','0905123987','longle@gmail.com','45 Lê Duẩn, Hải Châu, Đà Nẵng','cod','unpaid','processing',699000.00,0.00,30000.00,NULL,NULL,'2026-08-21 11:42:09');
DROP TABLE IF EXISTS `hieumini_db`.`products`;
CREATE TABLE `hieumini_db`.`products` (
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
  CONSTRAINT `fk_products_category` FOREIGN KEY (`category_id`) REFERENCES `hieumini_db`.`categories` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hieumini_db`.`products` VALUES
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
DROP TABLE IF EXISTS `hieumini_db`.`reviews`;
CREATE TABLE `hieumini_db`.`reviews` (
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
  CONSTRAINT `fk_reviews_product` FOREIGN KEY (`product_id`) REFERENCES `hieumini_db`.`products` (`id`) ON DELETE CASCADE,
  CONSTRAINT `fk_reviews_user` FOREIGN KEY (`user_id`) REFERENCES `hieumini_db`.`users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `hieumini_db`.`reviews` VALUES
(1,1,2,'Nguyễn Văn Nam',5,'Áo thun mặc rất thích, chất cotton dày dặn thấm hút tốt không hề bị xù lông. Sẽ tiếp tục ủng hộ shop HieuMini!','2026-08-21 11:42:09'),
(2,4,1,'Admin HieuMini',5,'Sơ mi Oxford đứng form, chống nhăn tốt, mặc đi làm rất lịch sự.','2026-08-21 11:42:09'),
(3,7,2,'Nguyễn Văn Nam',5,'Áo Bomber form đẹp mê ly, lớp lót dù êm ái, khóa mượt.','2026-08-21 11:42:09'),
(4,14,2,'Mai Phương',5,'Đầm hoa nhí xinh xỉu, chất voan 2 lớp bồng bềnh, eo co giãn tôn dáng cực kỳ!','2026-08-21 11:42:09');
DROP TABLE IF EXISTS `hieumini_db`.`users`;
CREATE TABLE `hieumini_db`.`users` (
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

INSERT INTO `hieumini_db`.`users` VALUES
(1,'Admin HieuMini','admin@hieumini.vn','$2y$10$eEskGf1Z3z15i1ZzU/kLw.5x8X/0R.hBvM6h76Yq/YJz31X7PZ.Gy','0988889999','Hà Nội, Việt Nam','admin','default_avatar.png','2026-08-21 11:42:09'),
(2,'Nguyễn Văn Nam','khachhang@gmail.com','$2y$10$eEskGf1Z3z15i1ZzU/kLw.5x8X/0R.hBvM6h76Yq/YJz31X7PZ.Gy','0912345678','Cầu Giấy, Hà Nội','customer','default_avatar.png','2026-08-21 11:42:09');

-- Tài khoản quản trị demo dùng chung
UPDATE `hieumini_db`.`users` SET `email`='admin@hieumini.vn', `password`='$2y$12$VNWuZfLGEhoGn5l3eGTx2unsvMwipSFRc..lz0bUmXQwT0i1jR6yS' WHERE `role`='admin';

SET FOREIGN_KEY_CHECKS = 1;
