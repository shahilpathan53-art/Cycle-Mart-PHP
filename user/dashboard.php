<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('user'); // ensure only user role can access

$u = user();

// Optional: quick counts for a friendly dashboard
// total items in cart
$cartCount = 0;
$res = mysqli_query($conn, "SELECT ci.quantity FROM carts c JOIN cart_items ci ON ci.cart_id=c.id WHERE c.user_id=".$u['id']);
if ($res) { 
  while($row = mysqli_fetch_assoc($res)) { 
    $cartCount += (int)$row['quantity']; 
  } 
}

// recent order
$lastOrder = null;
$ro = mysqli_query($conn, "SELECT id, status, total, created_at FROM orders WHERE user_id=".$u['id']." ORDER BY id DESC LIMIT 1");
if ($ro) { 
  $lastOrder = mysqli_fetch_assoc($ro); 
}

include '../includes/header_user.php';
include '../includes/nav_user.php';
?>
<div class="container my-4">
  <div class="row g-3">
    <div class="col-12">
      <div class="glass p-4">
        <h3 class="mb-1">Welcome, <?=htmlspecialchars($u['name'])?> 👋</h3>
        <p class="text-muted mb-0">Explore cycles, manage cart, and track orders from here.</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="glass p-4 h-100">
        <h5 class="mb-2">Browse Catalog</h5>
        <p class="text-muted">Find Mountain, Road, Hybrid and Kids cycles.</p>
        <a class="btn btn-brand" href="catalog.php">Go to Catalog</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="glass p-4 h-100">
        <h5 class="mb-2">Your Cart</h5>
        <p class="text-muted mb-1">Items in cart: <strong><?=$cartCount?></strong></p>
        <a class="btn btn-brand" href="cart.php">Open Cart</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="glass p-4 h-100">
        <h5 class="mb-2">Orders</h5>
        <?php if($lastOrder): ?>
          <p class="text-muted mb-1">Last order: #<?=$lastOrder['id']?> · <?=htmlspecialchars($lastOrder['status'])?></p>
          <p class="mb-2">Total: ₹<?=number_format((float)$lastOrder['total'],2)?> · <?=htmlspecialchars($lastOrder['created_at'])?></p>
        <?php else: ?>
          <p class="text-muted">No orders yet. Start shopping!</p>
        <?php endif; ?>
        <a class="btn btn-brand" href="order_history.php">View Order History</a>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
