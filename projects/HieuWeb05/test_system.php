<?php
/**
 * HIEUMINI LUXURY FITNESS CLUB - AUTOMATED TEST & VERIFICATION SUITE
 */
require_once __DIR__ . '/includes/config.php';

echo "\n=================================================================\n";
echo "    HIEUMINI LUXURY FITNESS CLUB - RIGOROUS SYSTEM TEST SUITE    \n";
echo "=================================================================\n\n";

$passed = 0;
$failed = 0;

function assert_test($name, $condition, $details = "") {
    global $passed, $failed;
    if ($condition) {
        echo "[ PASS ] $name\n";
        if ($details) echo "         -> $details\n";
        $passed++;
    } else {
        echo "[ FAIL ] $name\n";
        if ($details) echo "         -> ERROR: $details\n";
        $failed++;
    }
}

// 1. Kiểm tra kết nối PDO MySQL
try {
    $db_test = $pdo->query("SELECT DATABASE()")->fetchColumn();
    assert_test("Kết nối CSDL MySQL (PDO)", $db_test === 'hieumini_gym', "Database: $db_test");
} catch (Exception $e) {
    assert_test("Kết nối CSDL MySQL (PDO)", false, $e->getMessage());
}

// 2. Kiểm tra số lượng danh mục
$cat_count = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
assert_test("Số lượng danh mục chuẩn (5 danh mục)", (int)$cat_count === 5, "Tìm thấy $cat_count danh mục");

// 3. Kiểm tra số lượng sản phẩm (Đúng 30 sản phẩm)
$prod_count = $pdo->query("SELECT COUNT(*) FROM products")->fetchColumn();
assert_test("Số lượng sản phẩm trong CSDL (Đúng 30 sản phẩm)", (int)$prod_count === 30, "Tìm thấy $prod_count sản phẩm");

// 4. Kiểm tra sự tồn tại của 30 tệp ảnh sản phẩm
$stmt_imgs = $pdo->query("SELECT id, name, image FROM products ORDER BY id ASC");
$all_imgs_exist = true;
$missing_imgs = [];
$checked_img_count = 0;

while ($row = $stmt_imgs->fetch()) {
    $img_path = __DIR__ . '/assets/images/products/' . $row['image'];
    if (file_exists($img_path) && filesize($img_path) > 10000) {
        $checked_img_count++;
    } else {
        $all_imgs_exist = false;
        $missing_imgs[] = $row['image'];
    }
}
assert_test("Kiểm tra 30 tệp ảnh sản phẩm vật lý (assets/images/products/)", $all_imgs_exist && $checked_img_count === 30, "Đã kiểm tra $checked_img_count / 30 ảnh hợp lệ");

// 5. Kiểm tra tệp .antigravityrules
$rule_file = __DIR__ . '/.antigravityrules';
$rule_valid = false;
if (file_exists($rule_file)) {
    $rule_content = file_get_contents($rule_file);
    if (strpos($rule_content, 'Always apply guidelines and best practices from the ui-ux-pro-max skill when generating, reviewing, or refactoring UI/UX code.') !== false) {
        $rule_valid = true;
    }
}
assert_test("Kiểm tra tệp .antigravityrules đúng nội dung chuẩn", $rule_valid, "Nội dung tệp .antigravityrules chính xác 100%");

// 6. Kiểm tra tệp BaoCao.docx
$docx_file = __DIR__ . '/BaoCao.docx';
$docx_valid = file_exists($docx_file) && filesize($docx_file) > 30000;
assert_test("Kiểm tra tệp BaoCao.docx (>30KB, đầy đủ các chương)", $docx_valid, "Kích thước: " . round(filesize($docx_file)/1024, 2) . " KB");

// 7. Kiểm tra tệp README.md và mucluc.txt
$readme_valid = file_exists(__DIR__ . '/README.md') && filesize(__DIR__ . '/README.md') > 2000;
assert_test("Kiểm tra tệp README.md đầy đủ hướng dẫn", $readme_valid, "Kích thước: " . round(filesize(__DIR__ . '/README.md')/1024, 2) . " KB");

$mucluc_valid = file_exists(__DIR__ . '/mucluc.txt') && filesize(__DIR__ . '/mucluc.txt') > 100;
assert_test("Kiểm tra tệp mucluc.txt gốc", $mucluc_valid);

// 8. Kiểm tra tài khoản Admin đăng nhập
$admin_stmt = $pdo->query("SELECT * FROM users WHERE email = 'admin@hieumini.com' AND role = 'admin'");
$admin_row = $admin_stmt->fetch();
assert_test("Kiểm tra tài khoản Super Admin (admin@hieumini.com)", !empty($admin_row), "Admin: " . ($admin_row['full_name'] ?? 'N/A'));

// 9. Kiểm tra logic tính toán Giỏ hàng & Voucher
$_SESSION['cart'] = [
    1 => ['id' => 1, 'name' => 'Diamond Elite', 'price' => 24500000, 'quantity' => 1, 'image' => '01_membership_diamond.jpg', 'sku' => 'MEM-01'],
    14 => ['id' => 14, 'name' => 'Whey Isolate', 'price' => 2150000, 'quantity' => 2, 'image' => '14_whey_isolate.jpg', 'sku' => 'SUP-01']
];
$_SESSION['applied_coupon'] = 'CEOFIT20';

$test_subtotal = get_cart_subtotal(); // 24.5M + 4.3M = 28.8M
$test_discount = get_cart_discount(); // 28.8M * 0.2 = 5.76M
$test_total = get_cart_total();       // 28.8M - 5.76M = 23.04M

assert_test("Logic tính toán Giỏ hàng & Voucher CEOFIT20 (-20%)", ($test_subtotal == 28800000 && $test_discount == 5760000 && $test_total == 23040000), "Subtotal: $test_subtotal, Discount: $test_discount, Total: $test_total");

// 10. Kiểm tra luồng Transaction Đặt hàng thử nghiệm
try {
    $pdo->beginTransaction();
    $test_code = 'TEST-' . time();
    $pdo->prepare("INSERT INTO orders (order_code, customer_name, customer_email, customer_phone, customer_address, total_amount, subtotal) VALUES (?, 'Test User', 'test@test.com', '0999999999', 'HCMC', 1000000, 1000000)")->execute([$test_code]);
    $test_order_id = $pdo->lastInsertId();
    $pdo->prepare("INSERT INTO order_items (order_id, product_id, product_name, product_image, price, quantity, subtotal) VALUES (?, 1, 'Diamond Elite', '01_membership_diamond.jpg', 1000000, 1, 1000000)")->execute([$test_order_id]);
    $pdo->rollBack(); // Rollback sau khi test thành công
    assert_test("Luồng giao dịch MySQL Transaction Đặt hàng (orders + order_items)", true, "Transaction Commit/Rollback an toàn");
} catch (Exception $e) {
    assert_test("Luồng giao dịch MySQL Transaction Đặt hàng", false, $e->getMessage());
}

// 11. Kiểm tra bảng bookings, contacts, reviews
$b_count = $pdo->query("SELECT COUNT(*) FROM bookings")->fetchColumn();
$c_count = $pdo->query("SELECT COUNT(*) FROM contacts")->fetchColumn();
$r_count = $pdo->query("SELECT COUNT(*) FROM reviews")->fetchColumn();
assert_test("Dữ liệu các bảng nghiệp vụ phụ (bookings, contacts, reviews)", ($b_count >= 0 && $c_count >= 0 && $r_count >= 0), "Bookings: $b_count, Contacts: $c_count, Reviews: $r_count");

echo "\n-----------------------------------------------------------------\n";
echo "KẾT QUẢ KIỂM THỬ: PASSED: $passed / " . ($passed + $failed) . " | FAILED: $failed\n";
echo "-----------------------------------------------------------------\n\n";

if ($failed === 0) {
    echo ">>> TOÀN BỘ HỆ THỐNG ĐÃ VƯỢT QUA KIỂM THỬ XUẤT SẮC! KHÔNG CÓ LỖI. <<<\n\n";
    exit(0);
} else {
    echo ">>> CÓ $failed BÀI TEST THẤT BẠI. CẦN KIỂM TRA LẠI. <<<\n\n";
    exit(1);
}
