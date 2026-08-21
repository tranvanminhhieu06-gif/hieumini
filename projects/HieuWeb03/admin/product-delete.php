<?php
// admin/product-delete.php
require_once __DIR__ . '/../config/app.php';
require_admin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
if ($id > 0) {
    $del = $pdo->prepare("DELETE FROM products WHERE id = ?");
    $del->execute([$id]);
    set_flash('success', "Đã xóa sản phẩm #{$id} thành công.");
}
header('Location: products.php');
exit;
