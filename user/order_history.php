<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('user');

if (session_status() === PHP_SESSION_NONE) session_start();
$user_id = (int)($_SESSION['user']['id'] ?? 0);
if ($user_id<=0) { header('Location: /user/login.php'); exit; }

// Optional status filter
$status = $_GET['status'] ?? 'all';
$where = "o.user_id = ?";
$params = [$user_id];
$types = "i";
if (in_array($status, ['pending','confirmed','shipped','delivered','cancelled'])) {
  $where .= " AND o.status = ?";
  $params[] = $status;
  $types .= "s";
}

// Fetch orders with payment_status and total
$sql = "SELECT o.id, o.status, o.payment_status, o.total, o.created_at
        FROM orders o
        WHERE $where
        ORDER BY o.id DESC";
$stmt = mysqli_prepare($conn, $sql);
mysqli_stmt_bind_param($stmt, $types, ...$params);
mysqli_stmt_execute($stmt);
$res = mysqli_stmt_get_result($stmt);
$orders = [];
while($r = mysqli_fetch_assoc($res)) $orders[] = $r;
mysqli_stmt_close($stmt);

include '../includes/header_user.php';
include '../includes/nav_user.php';
?>
<div class="container my-4">
  <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
    <div class="glass p-2"><h3 class="mb-0">Order History</h3></div>
    <div class="ms-auto d-flex gap-2">
      <a class="btn btn-outline-secondary <?= $status==='all'?'active':'' ?>" href="order_history.php?status=all">All</a>
      <a class="btn btn-outline-secondary <?= $status==='pending'?'active':'' ?>" href="order_history.php?status=pending">Pending</a>
      <a class="btn btn-outline-secondary <?= $status==='confirmed'?'active':'' ?>" href="order_history.php?status=confirmed">Confirmed</a>
      <a class="btn btn-outline-secondary <?= $status==='shipped'?'active':'' ?>" href="order_history.php?status=shipped">Shipped</a>
      <a class="btn btn-outline-secondary <?= $status==='delivered'?'active':'' ?>" href="order_history.php?status=delivered">Delivered</a>
      <a class="btn btn-outline-secondary <?= $status==='cancelled'?'active':'' ?>" href="order_history.php?status=cancelled">Cancelled</a>
    </div>
  </div>

  <div class="glass p-0">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th style="width:10%">#</th>
          <th style="width:20%">Status</th>
          <th style="width:20%">Payment</th>
          <th style="width:20%">Total</th>
          <th style="width:30%">Placed</th>
          <th></th>
        </tr>
      </thead>
      <tbody>
        <?php if(empty($orders)): ?>
          <tr><td colspan="6" class="text-center py-4">No orders yet.</td></tr>
        <?php else: foreach($orders as $o): ?>
          <?php
            $badge='text-bg-secondary';
            if ($o['status']==='pending') $badge='text-bg-warning';
            if ($o['status']==='confirmed') $badge='text-bg-info';
            if ($o['status']==='shipped') $badge='text-bg-primary';
            if ($o['status']==='delivered') $badge='text-bg-success';
            if ($o['status']==='cancelled') $badge='text-bg-dark';

            $pBadge='text-bg-secondary';
            if ($o['payment_status']==='paid') $pBadge='text-bg-success';
            if ($o['payment_status']==='partial') $pBadge='text-bg-warning';
            if ($o['payment_status']==='unpaid' || $o['payment_status']==='pending') $pBadge='text-bg-secondary';
          ?>
          <tr>
            <td>#<?= (int)$o['id'] ?></td>
            <td><span class="badge <?= $badge ?>"><?= htmlspecialchars($o['status']) ?></span></td>
            <td><span class="badge <?= $pBadge ?>"><?= htmlspecialchars($o['payment_status']) ?></span></td>
            <td>₹<?= number_format((float)$o['total'],2) ?></td>
            <td><?= htmlspecialchars($o['created_at']) ?></td>
            <td class="text-end">
              <a class="btn btn-sm btn-outline-secondary" href="order_view.php?id=<?= (int)$o['id'] ?>">View</a>
            </td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
