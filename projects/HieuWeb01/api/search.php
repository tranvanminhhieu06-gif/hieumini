<?php
/**
 * API Tìm Kiếm Sản Phẩm Thời Gian Thực (Live Ajax Search) - HieuMini
 */
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';

$keyword = isset($_GET['keyword']) ? trim($_GET['keyword']) : '';

if (mb_strlen($keyword, 'UTF-8') < 1) {
    echo json_encode([
        'status' => 'success',
        'data' => [],
        'total' => 0,
        'message' => 'Từ khóa quá ngắn'
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

try {
    $sql = "SELECT p.id, p.name, p.slug, p.price, p.discount_price, p.image, c.name as category_name 
            FROM products p 
            JOIN categories c ON p.category_id = c.id 
            WHERE p.status = 1 
              AND (p.name LIKE ? OR p.description LIKE ? OR p.sku LIKE ? OR c.name LIKE ?) 
            ORDER BY 
              CASE WHEN p.name LIKE ? THEN 1 ELSE 2 END,
              p.view_count DESC, 
              p.id DESC 
            LIMIT 6";

    $searchPattern = '%' . $keyword . '%';
    $exactStart = $keyword . '%';
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$searchPattern, $searchPattern, $searchPattern, $searchPattern, $exactStart]);
    $products = $stmt->fetchAll();

    $results = [];
    foreach ($products as $prod) {
        $price = (float)$prod['price'];
        $discountPrice = $prod['discount_price'] ? (float)$prod['discount_price'] : null;
        $currentPrice = $discountPrice ?: $price;
        $discountPercent = ($discountPrice && $price > 0) ? round((($price - $discountPrice) / $price) * 100) : 0;

        $results[] = [
            'id' => (int)$prod['id'],
            'name' => $prod['name'],
            'slug' => $prod['slug'],
            'category_name' => $prod['category_name'],
            'image' => $prod['image'],
            'price_formatted' => format_price($price),
            'current_price_formatted' => format_price($currentPrice),
            'has_discount' => $discountPrice !== null,
            'discount_percent' => $discountPercent,
            'url' => 'product_detail.php?id=' . $prod['id']
        ];
    }

    echo json_encode([
        'status' => 'success',
        'data' => $results,
        'total' => count($results),
        'keyword' => $keyword
    ], JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
    echo json_encode([
        'status' => 'error',
        'message' => 'Lỗi truy vấn CSDL: ' . $e->getMessage(),
        'data' => []
    ], JSON_UNESCAPED_UNICODE);
}
