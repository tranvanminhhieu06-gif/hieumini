<?php
/**
 * Script Khởi tạo CSDL MySQL cho HieuMini
 */
$host = '127.0.0.1';
$user = 'root';
$pass = '';
$charset = 'utf8mb4';

try {
    $pdo = new PDO("mysql:host=$host;charset=$charset", $user, $pass, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
    ]);
    
    echo "1. Kết nối MySQL thành công!\n";
    
    // Khởi tạo Database sạch
    $pdo->exec("DROP DATABASE IF EXISTS `hieumini_db`; CREATE DATABASE `hieumini_db` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci; USE `hieumini_db`;");
    echo "2. Đã tạo mới CSDL hieumini_db!\n";

    // Đọc và chạy file SQL schema
    $sqlFile = __DIR__ . '/hieumini_db.sql';
    if (file_exists($sqlFile)) {
        $sql = file_get_contents($sqlFile);
        $pdo->exec($sql);
        echo "3. Khởi tạo cấu trúc các bảng thành công!\n";
    }

    
    // Nạp dữ liệu sản phẩm chi tiết
    $products = [
        [
            'category_id' => 1,
            'name' => 'Áo Thun Nam Nữ Streetwear Basic Cotton 100%',
            'slug' => 'ao-thun-streetwear-basic-cotton',
            'sku' => 'HM-TS01',
            'price' => 250000,
            'discount_price' => 199000,
            'stock' => 120,
            'sizes' => 'S,M,L,XL,XXL',
            'colors' => 'Đen,Trắng,Xám,Be',
            'description' => 'Áo thun phong cách Streetwear chất liệu 100% Cotton Compact 2 chiều 250gsm dày dặn, thấm hút mồ hôi cực tốt, co giãn thoải mái.',
            'content' => '<p>Áo thun HieuMini Streetwear là sự kết hợp hoàn hảo giữa thiết kế tối giản hiện đại và chất liệu vải Cotton dệt chải kỹ cao cấp.</p><ul><li>Chất liệu: 100% Cotton định lượng 250gsm không xù lông.</li><li>Form dáng: Oversize Unisex thoải mái, trẻ trung.</li><li>Bo cổ: Dệt bo gân 2.5cm dày dặn, chống bai dão sau nhiều lần giặt.</li><li>Đường may: Trần 2 kim chắc chắn theo tiêu chuẩn xuất khẩu.</li></ul>',
            'image' => 'ao_thun_streetwear.jpg',
            'featured' => 1,
            'view_count' => 1520
        ],
        [
            'category_id' => 1,
            'name' => 'Áo Polo Nam Phối Cổ Bo Dệt Sang Trọng',
            'slug' => 'ao-polo-nam-phoi-co-bo-det',
            'sku' => 'HM-PL02',
            'price' => 350000,
            'discount_price' => 289000,
            'stock' => 85,
            'sizes' => 'M,L,XL,XXL',
            'colors' => 'Xanh Navy,Trắng,Đen,Xanh Rêu',
            'description' => 'Áo Polo nam vải pique cá sấu mắt chim thoáng khí, cổ phối bo dệt sọc tinh tế, phong cách Smart Casual lịch lãm.',
            'content' => '<p>Mẫu áo Polo cao cấp tôn lên vẻ nam tính, thanh lịch thích hợp mặc đi làm, đi chơi hay gặp gỡ đối tác.</p><ul><li>Vải Pique Cotton 95% + 5% Spandex co giãn 4 chiều.</li><li>Cúc áo dập chìm logo thương hiệu sang trọng.</li><li>Khử mùi kháng khuẩn tự nhiên, không nhăn nhúm.</li></ul>',
            'image' => 'ao_polo_dệt_bo.jpg',
            'featured' => 1,
            'view_count' => 980
        ],
        [
            'category_id' => 1,
            'name' => 'Áo Thun Graphic Oversize HieuMini Edition 2026',
            'slug' => 'ao-thun-graphic-oversize-hieumini',
            'sku' => 'HM-TS03',
            'price' => 320000,
            'discount_price' => 260000,
            'stock' => 95,
            'sizes' => 'S,M,L,XL',
            'colors' => 'Trắng Kem,Đen Khói,Xanh Pastel',
            'description' => 'Áo thun Graphic in lụa thủ công cao cấp sắc nét, họa tiết độc quyền mang đậm tinh thần tự do phóng khoáng của giới trẻ.',
            'content' => '<p>Thiết kế phiên bản giới hạn Limited Edition 2026 từ HieuMini Studio.</p><ul><li>Hình in kỹ thuật số công nghệ Nhật Bản sắc nét không bong tróc.</li><li>Chất vải dệt sợi chải kỹ Organic Cotton thân thiện môi trường.</li></ul>',
            'image' => 'ao_thun_graphic_hieu.jpg',
            'featured' => 0,
            'view_count' => 640
        ],
        [
            'category_id' => 2,
            'name' => 'Áo Sơ Mi Nam Oxford Dài Tay Chống Nhăn',
            'slug' => 'ao-so-mi-nam-oxford-dai-tay',
            'sku' => 'HM-SM01',
            'price' => 420000,
            'discount_price' => 350000,
            'stock' => 70,
            'sizes' => 'M,L,XL,XXL',
            'colors' => 'Xanh Nhạt,Trắng,Hồng Pastel,Xám',
            'description' => 'Áo sơ mi chất liệu vải Oxford cao cấp dệt sợi kép bền bỉ, form dáng Regular Fit chuẩn mực tôn dáng phái mạnh.',
            'content' => '<p>Sơ mi Oxford là item kinh điển không thể thiếu trong tủ đồ của quý ông hiện đại.</p><ul><li>Chất vải Oxford 100% Cotton dệt nổi hạt đặc trưng, thoáng mát 4 mùa.</li><li>Công nghệ ép nhiệt chống nhăn Easy-Iron giúp tiết kiệm thời gian ủi đồ.</li><li>Cổ Button-down giữ dáng cứng cáp suốt ngày dài làm việc.</li></ul>',
            'image' => 'ao_so_mi_oxford.jpg',
            'featured' => 1,
            'view_count' => 1890
        ],
        [
            'category_id' => 2,
            'name' => 'Áo Sơ Mi Lụa Cổ Cubano Phong Cách Hàn Quốc',
            'slug' => 'ao-so-mi-lua-co-cubano-han-quoc',
            'sku' => 'HM-SM02',
            'price' => 390000,
            'discount_price' => 319000,
            'stock' => 60,
            'sizes' => 'S,M,L,XL',
            'colors' => 'Be Cát,Xanh Mint,Đen,Nâu Cafe',
            'description' => 'Áo sơ mi vải lụa tơ tằm nhân tạo mềm rủ, cổ Cubano phong cách nghỉ dưỡng phóng khoáng, dạo phố sành điệu.',
            'content' => '<p>Cực kỳ mát mẻ và tôn dáng cho những chuyến du lịch dạo phố mùa hè.</p><ul><li>Chất liệu Silk Rayon mềm mại, nhẹ nhàng và mát lịm trên da.</li><li>Form áo Relaxed suông nhẹ bay bổng.</li></ul>',
            'image' => 'ao_so_mi_cubano.jpg',
            'featured' => 1,
            'view_count' => 1240
        ],
        [
            'category_id' => 2,
            'name' => 'Áo Sơ Mi Kẻ Flannel Vintage Classic',
            'slug' => 'ao-so-mi-ke-flannel-vintage',
            'sku' => 'HM-SM03',
            'price' => 360000,
            'discount_price' => 299000,
            'stock' => 50,
            'sizes' => 'M,L,XL',
            'colors' => 'Đỏ Kẻ Đen,Xanh Kẻ Vàng,Nâu Kẻ Be',
            'description' => 'Áo sơ mi Flannel dạ kẻ ô vuông cổ điển, chất vải êm ái giữ ấm nhẹ, phong cách Retro bụi bặm.',
            'content' => '<p>Phù hợp mặc khoác ngoài áo thun trơn tạo layer ấn tượng.</p><ul><li>Chất vải Flannel cào bông 2 mặt êm ái.</li><li>Phối túi hộp ngực tiện lợi cá tính.</li></ul>',
            'image' => 'ao_so_mi_caro.jpg',
            'featured' => 0,
            'view_count' => 710
        ],
        [
            'category_id' => 3,
            'name' => 'Áo Khoác Bomber Kaki 2 Lớp Form Rộng Unisex',
            'slug' => 'ao-khoac-bomber-kaki-2-lop',
            'sku' => 'HM-JK01',
            'price' => 590000,
            'discount_price' => 480000,
            'stock' => 45,
            'sizes' => 'M,L,XL,XXL',
            'colors' => 'Xanh Rêu,Đen,Be Sáng',
            'description' => 'Áo khoác Bomber Kaki 2 lớp lót dù chống gió chống nước nhẹ, khóa kéo kim loại YKK trơn tru bền bỉ.',
            'content' => '<p>Áo khoác Bomber biểu tượng thời trang đường phố trẻ trung và đa dụng.</p><ul><li>Lớp ngoài: Vải Kaki dệt mật độ cao chống bám bụi và cản gió.</li><li>Lớp lót: Vải dù lụa thoáng khí, có túi trong an toàn đựng điện thoại/ví.</li><li>Bo tay và gấu áo dệt thun co giãn dày dặn ôm trọn cơ thể.</li></ul>',
            'image' => 'ao_khoac_bomber.jpg',
            'featured' => 1,
            'view_count' => 2100
        ],
        [
            'category_id' => 3,
            'name' => 'Áo Hoodie Nỉ Bông Unisex Warm Comfy',
            'slug' => 'ao-hoodie-ni-bong-unisex-warm',
            'sku' => 'HM-HD02',
            'price' => 490000,
            'discount_price' => 399000,
            'stock' => 65,
            'sizes' => 'S,M,L,XL',
            'colors' => 'Xám Tiêu,Đen,Xanh Rêu,Nâu Đất',
            'description' => 'Áo Hoodie nỉ bông dày 380gsm siêu ấm áp, mũ 2 lớp đứng form, túi kangaroo rộng rãi giữ ấm tay.',
            'content' => '<p>Chiếc áo Hoodie hoàn hảo cho mùa thu đông se lạnh.</p><ul><li>Chất vải nỉ chân cua cào bông tuyết mềm mại không ngứa da.</li><li>Mũ trùm đầu may 2 lớp giữ form cứng cáp, dây rút có chốt kim loại sang trọng.</li></ul>',
            'image' => 'ao_hoodie_ni_bong.jpg',
            'featured' => 1,
            'view_count' => 1650
        ],
        [
            'category_id' => 3,
            'name' => 'Áo Blazer Nam Nữ Dáng Suông Hàn Quốc',
            'slug' => 'ao-blazer-nam-nu-dang-suong-han-quoc',
            'sku' => 'HM-BZ03',
            'price' => 680000,
            'discount_price' => 550000,
            'stock' => 40,
            'sizes' => 'S,M,L,XL',
            'colors' => 'Nâu Mocha,Đen Tuyền,Ghi Xám',
            'description' => 'Áo khoác Blazer dáng Relaxed Fit 2 lớp chuẩn form Hàn Quốc, cầu vai đệm mút tự nhiên tạo form vai thẳng đẹp.',
            'content' => '<p>Blazer thời thượng giúp nâng tầm phong cách ngay tức thì từ đi học, đi làm đến dự tiệc nhẹ.</p><ul><li>Chất liệu Tuyết mưa cao cấp co giãn nhẹ, giữ phom đứng dáng.</li><li>Túi mổ 2 bên may nắp giấu tinh tế.</li></ul>',
            'image' => 'ao_blazer_han_quoc.jpg',
            'featured' => 1,
            'view_count' => 1430
        ],
        [
            'category_id' => 4,
            'name' => 'Quần Jean Nam Slimfit Co Giãn Rửa Màu Vintage',
            'slug' => 'quan-jean-nam-slimfit-co-gian',
            'sku' => 'HM-JN01',
            'price' => 450000,
            'discount_price' => 380000,
            'stock' => 80,
            'sizes' => '29,30,31,32,34',
            'colors' => 'Xanh Đậm,Xanh Nhạt,Đen Khói',
            'description' => 'Quần Jean nam dáng Slimfit ôm vừa vặn, vải Denim 12oz wash màu thủ công bền màu, co giãn 2% Spandex thoải mái.',
            'content' => '<p>Mẫu quần Jeans tôn dáng hoàn hảo, dễ dàng phối hợp với áo thun hay sơ mi.</p><ul><li>Đinh tán đồng và khóa kéo đồng nguyên khối dập logo HieuMini.</li><li>Đường chỉ may bò kép chịu lực cao, không đứt chỉ khi vận động mạnh.</li></ul>',
            'image' => 'quan_jean_slimfit.jpg',
            'featured' => 1,
            'view_count' => 1780
        ],
        [
            'category_id' => 4,
            'name' => 'Quần Jeans Ống Rộng Wide-Leg Unisex Baggy Fit',
            'slug' => 'quan-jeans-ong-rong-wide-leg-unisex',
            'sku' => 'HM-JN02',
            'price' => 480000,
            'discount_price' => 399000,
            'stock' => 75,
            'sizes' => 'S,M,L,XL',
            'colors' => 'Xanh Retro,Xanh Khói,Trắng Kem',
            'description' => 'Quần Jean ống rộng suông dài hack dáng cực đỉnh, cạp cao tôn vòng eo và kéo dài đôi chân.',
            'content' => '<p>Item không thể thiếu của các tín đồ phong cách thời trang Y2K và Streetwear.</p>',
            'image' => 'quan_jean_baggy.jpg',
            'featured' => 0,
            'view_count' => 1120
        ],
        [
            'category_id' => 5,
            'name' => 'Quần Kaki Chino Dáng Đứng Co Giãn Công Sở',
            'slug' => 'quan-kaki-chino-dang-dung-co-gian',
            'sku' => 'HM-KK01',
            'price' => 390000,
            'discount_price' => 320000,
            'stock' => 90,
            'sizes' => '29,30,31,32,34',
            'colors' => 'Vàng Be,Đen,Xanh Rêu,Ghi',
            'description' => 'Quần Kaki Chino cao cấp chất vải Cotton pha sợi thun mềm mại, chống nhăn, dáng đứng thanh lịch.',
            'content' => '<p>Quần Kaki Chino mang lại vẻ ngoài trẻ trung nhưng không kém phần trang trọng.</p><ul><li>Túi xéo 2 bên sâu rộng và 2 túi sau cài khuy tiện lợi.</li><li>Lưng quần may đai lót chống tụt áo sơ mi khi sơ vin.</li></ul>',
            'image' => 'quan_kaki_chino.jpg',
            'featured' => 1,
            'view_count' => 1340
        ],
        [
            'category_id' => 5,
            'name' => 'Quần Tây Âu Xếp Ly Dáng Suông Trousers',
            'slug' => 'quan-tay-au-xep-ly-dang-suong',
            'sku' => 'HM-TR02',
            'price' => 460000,
            'discount_price' => 379000,
            'stock' => 60,
            'sizes' => 'S,M,L,XL',
            'colors' => 'Đen,Xám Tro,Be Nâu',
            'description' => 'Quần âu xếp ly phía trước dáng suông rộng thời thượng, chất vải chống nhăn và giữ nếp li sắc sảo.',
            'content' => '<p>Phong cách quý ông hiện đại pha lẫn nét lãng tử Hàn Quốc.</p>',
            'image' => 'quan_tay_au.jpg',
            'featured' => 0,
            'view_count' => 890
        ],
        [
            'category_id' => 6,
            'name' => 'Đầm Hoa Nhí Dáng Xòe Vintage Cổ Vuông Tiểu Thư',
            'slug' => 'dam-hoa-nhi-dang-xoe-vintage-co-vuong',
            'sku' => 'HM-DR01',
            'price' => 480000,
            'discount_price' => 390000,
            'stock' => 55,
            'sizes' => 'S,M,L',
            'colors' => 'Hoa Nhí Hồng,Hoa Nhí Vàng,Hoa Xanh Pastel',
            'description' => 'Đầm hoa nhí dáng xòe bay bổng chất liệu voan Hàn 2 lớp cao cấp, cổ vuông tôn xương quai xanh quyến rũ.',
            'content' => '<p>Thiết kế đầm tiểu thư ngọt ngào phù hợp chụp ảnh du lịch, dạo phố và hẹn hò.</p><ul><li>Lưng đầm bo chun nhún co giãn ôm sát vòng eo thon gọn.</li><li>Tay bồng nhẹ che khuyết điểm bắp tay hiệu quả.</li></ul>',
            'image' => 'dam_hoa_nhi.jpg',
            'featured' => 1,
            'view_count' => 2450
        ],
        [
            'category_id' => 6,
            'name' => 'Chân Váy Chữ A Lưng Cao Xếp Ly Cá Tính',
            'slug' => 'chan-vay-chu-a-lung-cao-xep-ly',
            'sku' => 'HM-SK02',
            'price' => 290000,
            'discount_price' => 230000,
            'stock' => 65,
            'sizes' => 'S,M,L',
            'colors' => 'Đen,Kẻ Caro Xám,Nâu Be',
            'description' => 'Chân váy chữ A cạp cao tôn dáng có quần bảo hộ bên trong an toàn kín đáo, chất liệu tuyết mưa không xù lông.',
            'content' => '<p>Dễ dàng phối cùng áo thun, sơ mi hoặc áo croptop năng động.</p>',
            'image' => 'chan_vay_chu_a.jpg',
            'featured' => 0,
            'view_count' => 1190
        ],
        [
            'category_id' => 7,
            'name' => 'Thắt Lưng Da Bò Khóa Tự Động Kim Loại Tinh Tế',
            'slug' => 'that-lung-da-bo-khoa-tu-dong',
            'sku' => 'HM-BT01',
            'price' => 220000,
            'discount_price' => 169000,
            'stock' => 110,
            'sizes' => 'Freesize (115-125cm)',
            'colors' => 'Đen Bóng,Nâu Đậm',
            'description' => 'Dây lưng da bò thật nguyên tấm 100% mềm mại, mặt khóa hợp kim chống gỉ mạ nano bóng bẩy sang trọng.',
            'content' => '<p>Phụ kiện định hình phong cách lịch lãm của phái mạnh trong mọi trang phục công sở.</p>',
            'image' => 'that_lung_da.jpg',
            'featured' => 0,
            'view_count' => 670
        ],
        [
            'category_id' => 7,
            'name' => 'Mũ Lưỡi Trai Nón Kết Thêu Logo HieuMini Signature',
            'slug' => 'mu-luoi-trai-theu-logo-hieumini',
            'sku' => 'HM-CP02',
            'price' => 180000,
            'discount_price' => 139000,
            'stock' => 150,
            'sizes' => 'Freesize có khóa gài sau',
            'colors' => 'Đen,Trắng,Be,Xanh Rêu',
            'description' => 'Nón kết vải Kaki cotton 100% dày dặn giữ form, thêu logo nổi 3D tinh xảo chống bay màu.',
            'content' => '<p>Mũ lưỡi trai phong cách unisex năng động cho cả nam và nữ.</p>',
            'image' => 'non_ket_hieumini.jpg',
            'featured' => 0,
            'view_count' => 840
        ]
    ];
    
    // Xóa sản phẩm cũ nếu có để nạp mới
    $pdo->exec("DELETE FROM `products`;");
    $stmt = $pdo->prepare("INSERT INTO `products` (`category_id`, `name`, `slug`, `sku`, `price`, `discount_price`, `stock`, `sizes`, `colors`, `description`, `content`, `image`, `featured`, `status`, `view_count`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1, ?)");
    
    foreach ($products as $p) {
        $stmt->execute([
            $p['category_id'],
            $p['name'],
            $p['slug'],
            $p['sku'],
            $p['price'],
            $p['discount_price'],
            $p['stock'],
            $p['sizes'],
            $p['colors'],
            $p['description'],
            $p['content'],
            $p['image'],
            $p['featured'],
            $p['view_count']
        ]);
    }
    echo "3. Đã nạp thành công " . count($products) . " sản phẩm thời trang mẫu vào CSDL!\n";
    
    // Nạp một số đơn hàng mẫu để Dashboard Admin hiển thị số liệu đẹp mắt
    $pdo->exec("DELETE FROM `order_items`; DELETE FROM `orders`;");
    
    $sampleOrders = [
        [
            'order_code' => 'HM-ORD-1001',
            'user_id' => 2,
            'customer_name' => 'Nguyễn Văn Nam',
            'customer_phone' => '0912345678',
            'customer_email' => 'khachhang@gmail.com',
            'shipping_address' => 'Số 18 Duy Tân, Cầu Giấy, Hà Nội',
            'payment_method' => 'cod',
            'payment_status' => 'paid',
            'order_status' => 'completed',
            'total_amount' => 549000,
            'shipping_fee' => 0,
            'discount_amount' => 30000,
            'coupon_code' => 'FREESHIP',
            'items' => [
                ['product_id' => 1, 'name' => 'Áo Thun Nam Nữ Streetwear Basic Cotton 100%', 'price' => 199000, 'qty' => 1, 'size' => 'L', 'color' => 'Đen'],
                ['product_id' => 4, 'name' => 'Áo Sơ Mi Nam Oxford Dài Tay Chống Nhăn', 'price' => 350000, 'qty' => 1, 'size' => 'XL', 'color' => 'Xanh Nhạt']
            ]
        ],
        [
            'order_code' => 'HM-ORD-1002',
            'user_id' => NULL,
            'customer_name' => 'Trần Thị Mai',
            'customer_phone' => '0987654321',
            'customer_email' => 'maitran@gmail.com',
            'shipping_address' => '120 Nguyễn Huệ, Quận 1, TP. Hồ Chí Minh',
            'payment_method' => 'banking',
            'payment_status' => 'paid',
            'order_status' => 'shipping',
            'total_amount' => 870000,
            'shipping_fee' => 0,
            'discount_amount' => 0,
            'coupon_code' => NULL,
            'items' => [
                ['product_id' => 7, 'name' => 'Áo Khoác Bomber Kaki 2 Lớp Form Rộng Unisex', 'price' => 480000, 'qty' => 1, 'size' => 'L', 'color' => 'Xanh Rêu'],
                ['product_id' => 14, 'name' => 'Đầm Hoa Nhí Dáng Xòe Vintage Cổ Vuông Tiểu Thư', 'price' => 390000, 'qty' => 1, 'size' => 'M', 'color' => 'Hoa Nhí Hồng']
            ]
        ],
        [
            'order_code' => 'HM-ORD-1003',
            'user_id' => NULL,
            'customer_name' => 'Lê Hoàng Long',
            'customer_phone' => '0905123987',
            'customer_email' => 'longle@gmail.com',
            'shipping_address' => '45 Lê Duẩn, Hải Châu, Đà Nẵng',
            'payment_method' => 'cod',
            'payment_status' => 'unpaid',
            'order_status' => 'processing',
            'total_amount' => 699000,
            'shipping_fee' => 30000,
            'discount_amount' => 0,
            'coupon_code' => NULL,
            'items' => [
                ['product_id' => 10, 'name' => 'Quần Jean Nam Slimfit Co Giãn Rửa Màu Vintage', 'price' => 380000, 'qty' => 1, 'size' => '31', 'color' => 'Xanh Đậm'],
                ['product_id' => 2, 'name' => 'Áo Polo Nam Phối Cổ Bo Dệt Sang Trọng', 'price' => 289000, 'qty' => 1, 'size' => 'L', 'color' => 'Xanh Navy']
            ]
        ]
    ];
    
    $stmtOrd = $pdo->prepare("INSERT INTO `orders` (`order_code`, `user_id`, `customer_name`, `customer_phone`, `customer_email`, `shipping_address`, `payment_method`, `payment_status`, `order_status`, `total_amount`, `shipping_fee`, `discount_amount`, `coupon_code`) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
    $stmtItem = $pdo->prepare("INSERT INTO `order_items` (`order_id`, `product_id`, `product_name`, `price`, `quantity`, `size`, `color`, `subtotal`) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    
    foreach ($sampleOrders as $so) {
        $stmtOrd->execute([
            $so['order_code'],
            $so['user_id'],
            $so['customer_name'],
            $so['customer_phone'],
            $so['customer_email'],
            $so['shipping_address'],
            $so['payment_method'],
            $so['payment_status'],
            $so['order_status'],
            $so['total_amount'],
            $so['shipping_fee'],
            $so['discount_amount'],
            $so['coupon_code']
        ]);
        $orderId = $pdo->lastInsertId();
        
        foreach ($so['items'] as $item) {
            $subtotal = $item['price'] * $item['qty'];
            $stmtItem->execute([
                $orderId,
                $item['product_id'],
                $item['name'],
                $item['price'],
                $item['qty'],
                $item['size'],
                $item['color'],
                $subtotal
            ]);
        }
    }
    echo "4. Đã nạp thành công các đơn hàng mẫu và chi tiết đơn hàng!\n";
    
    // Nạp một số đánh giá review mẫu
    $pdo->exec("DELETE FROM `reviews`;");
    $reviews = [
        [1, 2, 'Nguyễn Văn Nam', 5, 'Áo thun mặc rất thích, chất cotton dày dặn thấm hút tốt không hề bị xù lông. Sẽ tiếp tục ủng hộ shop HieuMini!'],
        [4, 1, 'Admin HieuMini', 5, 'Sơ mi Oxford đứng form, chống nhăn tốt, mặc đi làm rất lịch sự.'],
        [7, 2, 'Nguyễn Văn Nam', 5, 'Áo Bomber form đẹp mê ly, lớp lót dù êm ái, khóa mượt.'],
        [14, 2, 'Mai Phương', 5, 'Đầm hoa nhí xinh xỉu, chất voan 2 lớp bồng bềnh, eo co giãn tôn dáng cực kỳ!']
    ];
    $stmtRev = $pdo->prepare("INSERT INTO `reviews` (`product_id`, `user_id`, `user_name`, `rating`, `comment`) VALUES (?, ?, ?, ?, ?)");
    foreach ($reviews as $r) {
        $stmtRev->execute($r);
    }
    echo "5. Đã nạp thành công các đánh giá sản phẩm mẫu!\n";
    echo "\n=== KHỞI TẠO CƠ SỞ DỮ LIỆU HOÀN TẤT 100% ===\n";

} catch (PDOException $e) {
    echo "LỖI CSDL: " . $e->getMessage() . "\n";
}
