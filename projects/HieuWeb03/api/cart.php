<?php
// api/cart.php - Shopping Cart AJAX Handler
header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/../config/app.php';

$action = $_POST['action'] ?? $_GET['action'] ?? '';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

if ($action === 'add') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if ($product_id <= 0) {
        echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không hợp lệ']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT id, name, price, sale_price, image, stock_quantity FROM products WHERE id = ?");
    $stmt->execute([$product_id]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['status' => 'error', 'message' => 'Không tìm thấy sản phẩm']);
        exit;
    }

    $actual_price = $product['sale_price'] ? (float)$product['sale_price'] : (float)$product['price'];

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] += $quantity;
    } else {
        $_SESSION['cart'][$product_id] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'price' => $actual_price,
            'image' => $product['image'],
            'quantity' => $quantity
        ];
    }

    echo json_encode([
        'status' => 'success',
        'message' => 'Đã thêm ' . $product['name'] . ' vào giỏ hàng!',
        'cart_count' => get_cart_count(),
        'subtotal' => get_cart_subtotal(),
        'formatted_subtotal' => format_price(get_cart_subtotal())
    ]);
    exit;
}

if ($action === 'update') {
    $product_id = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if (isset($_SESSION['cart'][$product_id])) {
        $_SESSION['cart'][$product_id]['quantity'] = $quantity;
        echo json_encode([
            'status' => 'success',
            'cart_count' => get_cart_count(),
            'subtotal' => get_cart_subtotal(),
            'formatted_subtotal' => format_price(get_cart_subtotal())
        ]);
        exit;
    }

    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không có trong giỏ hàng']);
    exit;
}

if ($action === 'remove') {
    $product_id = (int)($_POST['product_id'] ?? $_GET['product_id'] ?? 0);
    if (isset($_SESSION['cart'][$product_id])) {
        unset($_SESSION['cart'][$product_id]);
        echo json_encode([
            'status' => 'success',
            'message' => 'Đã xóa sản phẩm khỏi giỏ hàng',
            'cart_count' => get_cart_count(),
            'subtotal' => get_cart_subtotal(),
            'formatted_subtotal' => format_price(get_cart_subtotal())
        ]);
        exit;
    }
    echo json_encode(['status' => 'error', 'message' => 'Sản phẩm không tồn tại']);
    exit;
}

// Default response: get current cart status
echo json_encode([
    'status' => 'success',
    'cart_count' => get_cart_count(),
    'subtotal' => get_cart_subtotal(),
    'formatted_subtotal' => format_price(get_cart_subtotal())
]);
