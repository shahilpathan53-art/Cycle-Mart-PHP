<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

$err=''; $msg='';

// Filter by status in history
$flt = $_GET['status'] ?? 'all';
$where = "o.status IN ('delivered','cancelled')";
if ($flt==='delivered') $where = "o.status='delivered'";
if ($flt==='cancelled') $where = "o.status='cancelled'";

// Load history
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
    <div class="glass p-2"><h3 class="mb-0">Order History</h3></div>
    <div class="ms-auto d-flex gap-2">
      <a class="btn btn-outline-secondary <?= $flt==='all'?'active':'' ?>" href="order_history.php?status=all">All</a>
      <a class="btn btn-outline-secondary <?= $flt==='delivered'?'active':'' ?>" href="order_history.php?status=delivered">Delivered</a>
      <a class="btn btn-outline-secondary <?= $flt==='cancelled'?'active':'' ?>" href="order_history.php?status=cancelled">Cancelled</a>
    </div>
  </div>

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
          <th style="width:20%">Actions</th>
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
                if ($o['status']==='delivered') $badge='text-bg-success';
                if ($o['status']==='cancelled') $badge='text-bg-dark';
              ?>
              <span class="badge <?=$badge?>"><?=htmlspecialchars($o['status'])?></span>
            </td>
            <td><?=htmlspecialchars($o['payment_status'])?></td>
            <td>₹<?=number_format((float)$o['total'],2)?></td>
            <td><?=htmlspecialchars($o['created_at'])?></td>
            <td>
              <a class="btn btn-sm btn-outline-secondary" href="cyclestore/user/payment.php?id=<?=$o['id']?>" target="_blank">View</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($rows)): ?>
          <tr><td colspan="7" class="text-center py-4">No records.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="mt-3">
    <a class="btn btn-outline-secondary" href="orders.php">Back to Active Orders</a>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
