<?php
// admin/contacts.php - Manage Customer Inquiries
$page_title = "Tin Nhắn & Liên Hệ Khách Hàng";
require_once __DIR__ . '/includes/header.php';

// Update status
if (isset($_GET['reply'])) {
    $c_id = (int)$_GET['reply'];
    $pdo->prepare("UPDATE contacts SET status = 'replied' WHERE id = ?")->execute([$c_id]);
    set_flash('success', "Đã đánh dấu đã phản hồi tin nhắn #{$c_id}");
    header('Location: contacts.php');
    exit;
}

$contacts = $pdo->query("SELECT * FROM contacts ORDER BY id DESC")->fetchAll();
?>

<div class="data-table-card">
  <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
    <h3 style="font-size: 1.15rem; font-weight: 800; color: var(--dark);">Hộp Thư Khách Hàng (<?= count($contacts) ?>)</h3>
  </div>

  <div style="overflow-x: auto;">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Họ Tên</th>
          <th>Email / SĐT</th>
          <th>Tiêu Đề</th>
          <th>Nội Dung</th>
          <th>Trạng Thái</th>
          <th>Ngày Gửi</th>
          <th style="text-align: right;">Thao Tác</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach ($contacts as $c): ?>
        <tr>
          <td style="font-weight: 700; color: var(--muted);">#<?= $c['id'] ?></td>
          <td style="font-weight: 700; color: var(--dark);"><?= htmlspecialchars($c['fullname']) ?></td>
          <td>
            <div><?= htmlspecialchars($c['email']) ?></div>
            <div style="font-size: 0.8rem; color: var(--muted);"><?= htmlspecialchars($c['phone'] ?? '') ?></div>
          </td>
          <td style="font-weight: 600;"><?= htmlspecialchars($c['subject'] ?? 'Không có') ?></td>
          <td style="max-width: 320px; font-size: 0.88rem; color: #475569;"><?= nl2br(htmlspecialchars($c['message'])) ?></td>
          <td>
            <span class="badge-tag <?= $c['status'] === 'new' ? 'badge-hot' : 'badge-new' ?>">
              <?= $c['status'] === 'new' ? 'Tin mới' : 'Đã phản hồi' ?>
            </span>
          </td>
          <td style="color: var(--muted); font-size: 0.82rem;"><?= date('d/m/Y H:i', strtotime($c['created_at'])) ?></td>
          <td style="text-align: right;">
            <?php if ($c['status'] === 'new'): ?>
            <a href="contacts.php?reply=<?= $c['id'] ?>" class="btn btn-outline btn-sm" style="padding: 4px 10px; font-size: 0.8rem;">
              <i class="bi bi-check2"></i> Đã trả lời
            </a>
            <?php endif; ?>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
