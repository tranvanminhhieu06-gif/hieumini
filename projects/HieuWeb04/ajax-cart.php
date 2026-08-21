<?php
/**
 * AJAX Cart Handler & Quick View Endpoint
 * DatCyber Home Appliances
 */

header('Content-Type: application/json; charset=utf-8');
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/functions.php';

if (!isset($_SESSION['cart'])) {
    $_SESSION['cart'] = [];
}

$action = $_REQUEST['action'] ?? '';

// 1. ADD ITEM TO CART
if ($action === 'add') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = max(1, (int)($_POST['quantity'] ?? 1));

    if ($productId <= 0) {
        echo json_encode(['success' => false, 'message' => 'Sản phẩm không hợp lệ!']);
        exit;
    }

    $stmt = $pdo->prepare("SELECT * FROM products WHERE id = ?");
    $stmt->execute([$productId]);
    $product = $stmt->fetch();

    if (!$product) {
        echo json_encode(['success' => false, 'message' => 'Không tìm thấy sản phẩm!']);
        exit;
    }

    $currentCartQty = isset($_SESSION['cart'][$productId]) ? (int)$_SESSION['cart'][$productId]['quantity'] : 0;
    $targetQty = $currentCartQty + $quantity;

    if ($targetQty > (int)$product['stock']) {
        echo json_encode([
            'success' => false,
            'message' => 'Rất tiếc! Số lượng yêu cầu (' . $targetQty . ') vượt quá số lượng còn lại trong kho (' . $product['stock'] . ' sản phẩm).'
        ]);
        exit;
    }

    if (isset($_SESSION['cart'][$productId])) {
        $_SESSION['cart'][$productId]['quantity'] = $targetQty;
    } else {
        $_SESSION['cart'][$productId] = [
            'id' => $product['id'],
            'name' => $product['name'],
            'slug' => $product['slug'],
            'price' => (float)$product['price'],
            'old_price' => (float)$product['old_price'],
            'image' => $product['image'],
            'quantity' => $quantity
        ];
    }

    echo json_encode([
        'success' => true,
        'message' => 'Đã thêm <strong>' . htmlspecialchars($product['name']) . '</strong> vào giỏ hàng!',
        'total_items' => get_cart_count(),
        'subtotal' => get_cart_subtotal()
    ]);
    exit;
}

// 2. UPDATE ITEM QUANTITY
if ($action === 'update') {
    $productId = (int)($_POST['product_id'] ?? 0);
    $quantity = (int)($_POST['quantity'] ?? 1);

    if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
        if ($quantity <= 0) {
            unset($_SESSION['cart'][$productId]);
        } else {
            // Check product stock limit
            $stStmt = $pdo->prepare("SELECT stock FROM products WHERE id = ?");
            $stStmt->execute([$productId]);
            $stock = (int)$stStmt->fetchColumn();

            if ($quantity > $stock) {
                echo json_encode([
                    'success' => false,
                    'message' => 'Số lượng trong kho chỉ còn tối đa ' . $stock . ' sản phẩm!',
                    'total_items' => get_cart_count(),
                    'subtotal' => get_cart_subtotal()
                ]);
                exit;
            }

            $_SESSION['cart'][$productId]['quantity'] = $quantity;
        }
    }

    echo json_encode([
        'success' => true,
        'total_items' => get_cart_count(),
        'subtotal' => get_cart_subtotal()
    ]);
    exit;
}

// 3. REMOVE ITEM FROM CART
if ($action === 'remove') {
    $productId = (int)($_POST['product_id'] ?? 0);
    if ($productId > 0 && isset($_SESSION['cart'][$productId])) {
        unset($_SESSION['cart'][$productId]);
    }

    echo json_encode([
        'success' => true,
        'total_items' => get_cart_count(),
        'subtotal' => get_cart_subtotal()
    ]);
    exit;
}

// 4. GET DRAWER CONTENT (HTML)
if ($action === 'get_drawer') {
    if (empty($_SESSION['cart'])) {
        $items_html = '<div class="text-center py-5">
            <i class="fas fa-cart-shopping fa-3x text-muted mb-3"></i>
            <h6 class="text-muted fw-bold">Giỏ hàng của bạn đang trống</h6>
            <p class="text-secondary small">Hãy chọn những món đồ gia dụng ưng ý nhé!</p>
            <a href="products.php" class="btn btn-primary-custom btn-sm mt-2">Khám phá sản phẩm</a>
        </div>';
        $footer_html = '';
    } else {
        $items_html = '<div class="cart-items-list">';
        foreach ($_SESSION['cart'] as $item) {
            $items_html .= '
            <div class="cart-drawer-item">
                <img src="assets/images/products/' . htmlspecialchars($item['image']) . '" alt="' . htmlspecialchars($item['name']) . '">
                <div style="flex: 1; min-width: 0;">
                    <a href="product-detail.php?id=' . $item['id'] . '" class="text-truncate d-block fw-bold text-dark text-decoration-none mb-1" style="font-size:0.9rem;" title="' . htmlspecialchars($item['name']) . '">' . htmlspecialchars($item['name']) . '</a>
                    <div class="text-danger fw-bold mb-2">' . format_price($item['price']) . '</div>
                    <div class="d-flex align-items-center justify-content-between">
                        <div class="qty-control" style="transform: scale(0.85); transform-origin: left;">
                            <button type="button" class="qty-btn" onclick="updateCartItemQty(' . $item['id'] . ', ' . ($item['quantity'] - 1) . ')">-</button>
                            <input type="text" class="qty-input" value="' . $item['quantity'] . '" readonly>
                            <button type="button" class="qty-btn" onclick="updateCartItemQty(' . $item['id'] . ', ' . ($item['quantity'] + 1) . ')">+</button>
                        </div>
                        <button type="button" class="btn btn-link text-danger p-0" onclick="removeCartItem(' . $item['id'] . ')" title="Xóa"><i class="fas fa-trash-alt"></i></button>
                    </div>
                </div>
            </div>';
        }
        $items_html .= '</div>';

        $footer_html = '
        <div class="d-flex justify-content-between align-items-center mb-3">
            <span class="text-muted fw-semibold">Tổng cộng:</span>
            <span class="fs-5 fw-bold text-danger">' . format_price(get_cart_subtotal()) . '</span>
        </div>
        <div class="d-grid gap-2">
            <a href="checkout.php" class="btn btn-primary-custom justify-content-center">Tiến hành thanh toán <i class="fas fa-arrow-right ms-1"></i></a>
            <a href="cart.php" class="btn btn-light btn-sm text-secondary fw-semibold">Xem chi tiết giỏ hàng</a>
        </div>';
    }

    echo json_encode([
        'success' => true,
        'items_html' => $items_html,
        'footer_html' => $footer_html
    ]);
    exit;
}

// 5. QUICK VIEW MODAL
if ($action === 'quick_view') {
    $id = (int)($_GET['id'] ?? 0);
    $stmt = $pdo->prepare("SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE p.id = ?");
    $stmt->execute([$id]);
    $prod = $stmt->fetch();

    if (!$prod) {
        echo json_encode(['success' => false]);
        exit;
    }

    $discountBadge = ($prod['discount_percent'] > 0) ? '<span class="badge bg-danger fs-6 px-2 py-1">-' . $prod['discount_percent'] . '%</span>' : '';
    $oldPriceHtml = ($prod['old_price'] > $prod['price']) ? '<span class="text-muted text-decoration-line-through fs-6 ms-2">' . format_price($prod['old_price']) . '</span>' : '';

    $html = '
    <div class="row g-4 align-items-center">
        <div class="col-md-6">
            <div class="position-relative rounded-4 overflow-hidden bg-light border p-2 text-center" style="height: 320px; display: flex; align-items: center; justify-content: center;">
                <img src="assets/images/products/' . htmlspecialchars($prod['image']) . '" alt="' . htmlspecialchars($prod['name']) . '" class="img-fluid rounded-3" style="max-height: 100%; object-fit: contain;">
                <div class="position-absolute top-0 start-0 m-3">' . $discountBadge . '</div>
            </div>
        </div>
        <div class="col-md-6 d-flex flex-column justify-content-between">
            <div>
                <span class="badge bg-primary bg-opacity-10 text-primary fw-bold mb-2">' . htmlspecialchars($prod['category_name']) . '</span>
                <h4 class="fw-bold text-dark mb-2" style="font-size: 1.25rem; line-height: 1.4;">' . htmlspecialchars($prod['name']) . '</h4>
                ' . render_stars($prod['rating']) . '
                
                <div class="my-3 p-3 bg-light rounded-3 border">
                    <span class="fs-4 fw-bold text-danger">' . format_price($prod['price']) . '</span>
                    ' . $oldPriceHtml . '
                </div>

                <p class="text-secondary small mb-3" style="line-height: 1.6;">' . htmlspecialchars($prod['short_description']) . '</p>

                <div class="d-flex align-items-center gap-3 mb-3">
                    <span class="fw-semibold small">Số lượng:</span>
                    <div class="qty-control">
                        <button type="button" class="qty-btn" onclick="let inp=document.getElementById(\'quickQty\'); if(parseInt(inp.value)>1) inp.value=parseInt(inp.value)-1;">-</button>
                        <input type="text" id="quickQty" class="qty-input" value="1" readonly>
                        <button type="button" class="qty-btn" onclick="let inp=document.getElementById(\'quickQty\'); if(parseInt(inp.value) < ' . (int)$prod['stock'] . ') inp.value=parseInt(inp.value)+1;">+</button>
                    </div>
                    <small class="text-muted">(Còn ' . $prod['stock'] . ' sản phẩm)</small>
                </div>
            </div>

            <div class="d-flex gap-2 pt-2 border-top">
                <button type="button" class="btn btn-primary-custom flex-grow-1 py-2 justify-content-center" onclick="addToCart(' . $prod['id'] . ', parseInt(document.getElementById(\'quickQty\').value)); bootstrap.Modal.getInstance(document.getElementById(\'quickViewModal\')).hide();">
                    <i class="fas fa-cart-plus me-1"></i> Thêm vào giỏ
                </button>
                <a href="product-detail.php?id=' . $prod['id'] . '" class="btn btn-outline-secondary py-2 px-3">Chi tiết</a>
            </div>
        </div>
    </div>';

    echo json_encode(['success' => true, 'html' => $html]);
    exit;
}

echo json_encode(['success' => false, 'message' => 'Yêu cầu không hợp lệ!']);
