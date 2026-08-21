<?php
// admin/products.php - Manage Stationery Products
$page_title = "Quản Lý Danh Sách Sản Phẩm";
require_once __DIR__ . '/includes/header.php';

$cat_filter = isset($_GET['category']) ? (int)$_GET['category'] : 0;
$search = clean_input($_GET['q'] ?? '');

$sql = "SELECT p.*, c.name as category_name FROM products p JOIN categories c ON p.category_id = c.id WHERE 1=1";
$params = [];

if ($cat_filter > 0) {
    $sql .= " AND p.category_id = ?";
    $params[] = $cat_filter;
}

if (!empty($search)) {
    $sql .= " AND (p.name LIKE ? OR p.sku LIKE ?)";
    $params[] = "%{$search}%";
    $params[] = "%{$search}%";
}

$sql .= " ORDER BY p.id ASC";
$stmt = $pdo->prepare($sql);
$stmt->execute($params);
$products = $stmt->fetchAll();

$categories = $pdo->query("SELECT * FROM categories ORDER BY id ASC")->fetchAll();
?>

<!-- Header Actions -->
<div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 24px; flex-wrap: wrap; gap: 16px;">
  <!-- Filters -->
  <form action="products.php" method="GET" style="display: flex; gap: 12px; flex-wrap: wrap; flex: 1; max-width: 600px;">
    <input type="text" name="q" class="form-control" placeholder="Tìm theo tên sản phẩm, mã SKU..." value="<?= htmlspecialchars($search) ?>" style="flex: 1; min-width: 220px;">
    <select name="category" class="form-control" style="width: auto;" onchange="this.form.submit();">
      <option value="0">Tất cả danh mục</option>
      <?php foreach ($categories as $c): ?>
        <option value="<?= $c['id'] ?>" <?= $cat_filter == $c['id'] ? 'selected' : '' ?>><?= htmlspecialchars($c['name']) ?></option>
      <?php endforeach; ?>
    </select>
    <button type="submit" class="btn btn-outline btn-sm"><i class="bi bi-search"></i> Lọc</button>
  </form>

  <a href="product-add.php" class="btn btn-primary">
    <i class="bi bi-plus-circle-fill"></i> Thêm Sản Phẩm Mới
  </a>
</div>

<!-- Products Table -->
<div class="data-table-card">
  <div style="margin-bottom: 16px; font-weight: 700; color: var(--muted); font-size: 0.9rem;">
    Tổng số: <strong><?= count($products) ?></strong> sản phẩm
  </div>

  <div style="overflow-x: auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Hình Ảnh</th>
          <th>Tên Sản Phẩm / SKU</th>
          <th>Danh Mục</th>
          <th>Giá Bán</th>
          <th>Tồn Kho</th>
          <th>Đặc Tính</th>
          <th style="text-align: right;">Thao Tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($products as $p): ?>
        <tr>
          <td style="font-weight: 700; color: var(--muted);">#<?= $p['id'] ?></td>
          <td>
            <img src="../assets/images/products/<?= htmlspecialchars($p['image']) ?>" alt="" style="width: 50px; height: 50px; border-radius: 8px; object-fit: contain; background: #f8fafc; border: 1px solid var(--border);">
          </td>
          <td>
            <a href="../product-detail.php?id=<?= $p['id'] ?>" target="_blank" style="font-weight: 700; color: var(--dark);">
              <?= htmlspecialchars($p['name']) ?>
            </a>
            <div style="font-size: 0.78rem; color: var(--muted);">SKU: <code><?= htmlspecialchars($p['sku']) ?></code></div>
          </td>
          <td>
            <span style="font-size: 0.85rem; color: var(--dark); font-weight: 600;"><?= htmlspecialchars($p['category_name']) ?></span>
          </td>
          <td>
            <?php if ($p['sale_price']): ?>
              <div style="font-weight: 800; color: var(--primary);"><?= format_price($p['sale_price']) ?></div>
              <div style="font-size: 0.78rem; color: var(--muted); text-decoration: line-through;"><?= format_price($p['price']) ?></div>
            <?php else: ?>
              <div style="font-weight: 800; color: var(--dark);"><?= format_price($p['price']) ?></div>
            <?php endif; ?>
          </td>
          <td>
            <span style="font-weight: 700; color: <?= $p['stock_quantity'] < 20 ? '#ef4444' : 'var(--accent-emerald)' ?>;">
              <?= $p['stock_quantity'] ?> cái
            </span>
          </td>
          <td>
            <?php if ($p['is_hot']): ?><span class="badge-tag badge-hot" style="font-size: 0.65rem;">HOT</span><?php endif; ?>
            <?php if ($p['is_featured']): ?><span class="badge-tag badge-new" style="font-size: 0.65rem;">NỔI BẬT</span><?php endif; ?>
          </td>
          <td style="text-align: right; white-space: nowrap;">
            <a href="product-edit.php?id=<?= $p['id'] ?>" class="btn btn-outline btn-sm" style="padding: 6px 12px; margin-right: 6px;" title="Chỉnh sửa">
              <i class="bi bi-pencil-square"></i>
            </a>
            <a href="product-delete.php?id=<?= $p['id'] ?>" class="btn btn-sm" style="padding: 6px 12px; background: #fee2e2; color: #dc2626;" onclick="return confirm('Bạn có chắc chắn muốn xóa sản phẩm này?');" title="Xóa">
              <i class="bi bi-trash3"></i>
            </a>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
