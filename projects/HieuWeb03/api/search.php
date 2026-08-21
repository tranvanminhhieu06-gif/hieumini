<?php
// api/search.php - Live Search Autocomplete Endpoint
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/app.php';

$q = clean_input($_GET['q'] ?? '');

if (empty($q) || mb_strlen($q) < 2) {
    echo json_encode(['status' => 'success', 'products' => []]);
    exit;
}

try {
    $stmt = $pdo->prepare("
        SELECT p.id, p.name, p.slug, p.price, p.sale_price, p.image, c.name as category_name
        FROM products p
        JOIN categories c ON p.category_id = c.id
        WHERE p.name LIKE ? OR p.description LIKE ? OR c.name LIKE ?
        ORDER BY p.is_hot DESC, p.id ASC
        LIMIT 6
    ");
    $searchTerm = "%{$q}%";
    $stmt->execute([$searchTerm, $searchTerm, $searchTerm]);
    $results = $stmt->fetchAll();

    $products = [];
    foreach ($results as $row) {
        $price = $row['sale_price'] ? $row['sale_price'] : $row['price'];
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'slug' => $row['slug'],
            'image' => $row['image'],
            'price' => $price,
            'formatted_price' => format_price($price),
            'category' => $row['category_name']
        ];
    }

    echo json_encode(['status' => 'success', 'products' => $products]);
} catch (Exception $e) {
    echo json_encode(['status' => 'error', 'message' => $e->getMessage()]);
}
