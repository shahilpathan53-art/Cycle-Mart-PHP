<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

// Quick counts
$users = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM users"))['c'];
$products = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM products"))['c'];
$orders = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM orders"))['c'];
$suppliers = (int)mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) c FROM suppliers"))['c'];

// Recent 10 orders
$recent=[];
$ro = mysqli_query($conn, "SELECT o.id, u.name customer, o.status, o.payment_status, o.total, o.created_at
                           FROM orders o JOIN users u ON u.id=o.user_id
                           ORDER BY o.id DESC LIMIT 10");
while($r=mysqli_fetch_assoc($ro)) $recent[]=$r;

include '../includes/header_admin.php';
include '../includes/nav_admin.php';
?>
<div class="container my-4">
  <div class="row g-3">
    <div class="col-12">
      <div class="glass p-4">
        <h3 class="mb-0">Admin Dashboard</h3>
        <div class="text-muted">Quick overview and recent activity</div>
      </div>
    </div>

    <div class="col-md-3">
      <div class="glass p-4 h-100">
        <div class="text-muted">Users</div>
        <div class="display-6"><?=$users?></div>
        <a class="btn btn-brand mt-2" href="users.php">Manage Users</a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="glass p-4 h-100">
        <div class="text-muted">Products</div>
        <div class="display-6"><?=$products?></div>
        <a class="btn btn-brand mt-2" href="products.php">Manage Products</a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="glass p-4 h-100">
        <div class="text-muted">Orders</div>
        <div class="display-6"><?=$orders?></div>
        <a class="btn btn-brand mt-2" href="orders.php">View Orders</a>
      </div>
    </div>
    <div class="col-md-3">
      <div class="glass p-4 h-100">
        <div class="text-muted">Suppliers</div>
        <div class="display-6"><?=$suppliers?></div>
        <a class="btn btn-brand mt-2" href="po_list.php">Supplier Orders</a>
      </div>
    </div>

    <div class="col-12">
      <div class="glass p-3">
        <h5 class="mb-3">Recent Orders</h5>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr><th style="width:10%">#</th><th>Customer</th><th style="width:15%">Status</th><th style="width:15%">Payment</th><th style="width:15%">Total</th><th style="width:20%">Created</th></tr>
            </thead>
            <tbody>
              <?php foreach($recent as $o): ?>
                <tr>
                  <td>#<?=$o['id']?></td>
                  <td><?=htmlspecialchars($o['customer'])?></td>
                  <td>
                    <?php
                      $badge='text-bg-secondary';
                      if($o['status']==='pending') $badge='text-bg-warning';
                      if($o['status']==='confirmed') $badge='text-bg-info';
                      if($o['status']==='shipped') $badge='text-bg-primary';
                      if($o['status']==='delivered') $badge='text-bg-success';
                      if($o['status']==='cancelled') $badge='text-bg-dark';
                    ?>
                    <span class="badge <?=$badge?>"><?=htmlspecialchars($o['status'])?></span>
                  </td>
                  <td><?=htmlspecialchars($o['payment_status'])?></td>
                  <td>₹<?=number_format((float)$o['total'],2)?></td>
                  <td><?=htmlspecialchars($o['created_at'])?></td>
                </tr>
              <?php endforeach; ?>
              <?php if(empty($recent)): ?>
                <tr><td colspan="6" class="text-center py-3">No recent orders.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="mt-3 d-flex gap-2">
          <a class="btn btn-outline-secondary" href="orders.php">All Orders</a>
          <a class="btn btn-brand" href="po_list.php">Supplier Orders</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
