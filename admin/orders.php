<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

$err=''; $msg='';

// Status actions
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $oid = (int)($_POST['order_id'] ?? 0);
  $act = $_POST['action'] ?? '';
  if ($oid>0 && in_array($act,['ship','deliver','cancel'])) {
    if ($act==='ship') {
      mysqli_query($conn, "UPDATE orders SET status='shipped' WHERE id=$oid AND status IN ('confirmed','pending')");
      $msg='Order marked as shipped.';
    } elseif ($act==='deliver') {
      mysqli_query($conn, "UPDATE orders SET status='delivered' WHERE id=$oid AND status='shipped'");
      $msg='Order marked as delivered.';
    } elseif ($act==='cancel') {
      // if already paid, flag for refund
      mysqli_query($conn, "UPDATE orders SET status='cancelled', payment_status=IF(payment_status='paid','refund',payment_status) WHERE id=$oid AND status IN ('pending','confirmed')");
      $msg='Order cancelled.';
    }
  } else {
    $err='Invalid request.';
  }
}

// Filter
$status = $_GET['status'] ?? 'all';
$where = "1=1";
if (in_array($status, ['pending','confirmed','shipped'])) {
  $where = "o.status='$status'";
} elseif ($status==='active') {
  $where = "o.status IN ('pending','confirmed','shipped')";
}

// Load orders
$rows=[];
$q = "SELECT o.id, u.name customer, o.status, o.payment_status, o.total, o.created_at
      FROM orders o JOIN users u ON u.id=o.user_id
      WHERE $where
      ORDER BY o.id DESC";
$res = mysqli_query($conn, $q);
while($r=mysqli_fetch_assoc($res)) $rows[]=$r;

include '../includes/header_admin.php';
include '../includes/nav_admin.php';
?>
<div class="container my-4">
  <div class="d-flex flex-wrap gap-2 align-items-center mb-3">
    <div class="glass p-2"><h3 class="mb-0">Orders</h3></div>
    <div class="ms-auto d-flex gap-2">
      <a class="btn btn-outline-secondary <?= $status==='all'?'active':'' ?>" href="orders.php?status=all">All</a>
      <a class="btn btn-outline-secondary <?= $status==='active'?'active':'' ?>" href="orders.php?status=active">Active</a>
      <a class="btn btn-outline-secondary <?= $status==='pending'?'active':'' ?>" href="orders.php?status=pending">Pending</a>
      <a class="btn btn-outline-secondary <?= $status==='confirmed'?'active':'' ?>" href="orders.php?status=confirmed">Confirmed</a>
      <a class="btn btn-outline-secondary <?= $status==='shipped'?'active':'' ?>" href="orders.php?status=shipped">Shipped</a>
    </div>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
  <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

  <div class="glass p-0">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th style="width:8%">#</th>
          <th>Customer</th>
          <th style="width:12%">Status</th>
          <th style="width:14%">Payment</th>
          <th style="width:14%">Total</th>
          <th style="width:22%">Placed</th>
          <th style="width:30%">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $o): ?>
          <tr>
            <td>#<?=$o['id']?></td>
            <td><?=htmlspecialchars($o['customer'])?></td>
            <td>
              <?php
                $badge='text-bg-secondary';
                if ($o['status']==='pending') $badge='text-bg-warning';
                if ($o['status']==='confirmed') $badge='text-bg-info';
                if ($o['status']==='shipped') $badge='text-bg-primary';
                if ($o['status']==='delivered') $badge='text-bg-success';
                if ($o['status']==='cancelled') $badge='text-bg-dark';
              ?>
              <span class="badge <?=$badge?>"><?=htmlspecialchars($o['status'])?></span>
            </td>
            <td><?=htmlspecialchars($o['payment_status'])?></td>
            <td>₹<?=number_format((float)$o['total'],2)?></td>
            <td><?=htmlspecialchars($o['created_at'])?></td>
            <td>
              <form method="post" class="d-flex gap-2 flex-wrap">
                <input type="hidden" name="order_id" value="<?=$o['id']?>">
                <button class="btn btn-sm btn-outline-secondary" name="action" value="ship" <?= !in_array($o['status'],['pending','confirmed'])?'disabled':''; ?>>Mark Shipped</button>
                <button class="btn btn-sm btn-outline-secondary" name="action" value="deliver" <?= $o['status']!=='shipped'?'disabled':''; ?>>Mark Delivered</button>
                <button class="btn btn-sm btn-outline-danger" name="action" value="cancel" <?= !in_array($o['status'],['pending','confirmed'])?'disabled':''; ?> onclick="return confirm('Cancel this order?')">Cancel</button>
                <a class="btn btn-sm btn-outline-secondary" href="payment.php?id=<?=$o['id']?>" target="_blank">View</a>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($rows)): ?>
          <tr><td colspan="7" class="text-center py-4">No orders found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
