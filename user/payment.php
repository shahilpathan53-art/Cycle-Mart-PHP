<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('user');

$u = user();
$orderId = (int)($_GET['id'] ?? 0);
if ($orderId <= 0) { http_response_code(400); die('Invalid order id'); }

// Load order (only this user)
$oq = mysqli_query($conn, "SELECT id, user_id, status, subtotal, shipping, total, payment_status, created_at FROM orders WHERE id=$orderId AND user_id=".$u['id']." LIMIT 1");
$order = mysqli_fetch_assoc($oq);
if (!$order) { http_response_code(404); die('Order not found'); }

// If already paid, redirect to history
if ($order['payment_status'] === 'paid') {
  header('Location: order_history.php'); // ✅ relative path
  exit;
}

// Load items
$iq = mysqli_query($conn, "SELECT oi.product_id, p.name, oi.price, oi.quantity 
                           FROM order_items oi 
                           JOIN products p ON p.id=oi.product_id 
                           WHERE oi.order_id=".$order['id']);
$items = [];
while($r = mysqli_fetch_assoc($iq)) $items[] = $r;

$err=''; $msg='';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $method = mysqli_real_escape_string($conn, trim($_POST['method'] ?? ''));
  $ref    = mysqli_real_escape_string($conn, trim($_POST['provider_ref'] ?? ''));

  if (!in_array($method, ['COD','UPI','Card'])) {
    $err = 'Choose a valid payment method.';
  } else {
    // Create payment record (demo)
    $amount = (float)$order['total'];
    $status = ($method==='COD') ? 'pending' : 'success'; 
    mysqli_query($conn, "INSERT INTO payments (order_id, provider, provider_ref, amount, status, paid_at) 
                         VALUES (".$order['id'].",'$method','$ref',$amount,'$status', NOW())");

    // Insert bill record (admin can see)
    if ($method !== 'COD') {
      mysqli_query($conn, "INSERT INTO bills (order_id, amount, source, created_at) VALUES (".$order['id'].", $amount, 'user', NOW())");
    }

    // Update order payment + status
    if ($method==='COD') {
      mysqli_query($conn, "UPDATE orders SET payment_status='cod', status='confirmed' WHERE id=".$order['id']);
      $msg='Order placed with Cash on Delivery.';
    } else {
      mysqli_query($conn, "UPDATE orders SET payment_status='paid', status='confirmed' WHERE id=".$order['id']);
      $msg='Payment successful. Order confirmed.';
    }

    // Redirect to order history
    header('Location: order_history.php'); // ✅ relative path
    exit;
  }
}

include '../includes/header_user.php';
include '../includes/nav_user.php';
?>
<div class="container my-4">
  <div class="glass p-3 mb-3">
    <h3 class="mb-0">Payment</h3>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="glass p-3">
        <h5 class="mb-3">Select Payment Method</h5>
        <form method="post">
          <div class="mb-3">
            <select name="method" class="form-select" required>
              <option value="">Choose...</option>
              <option value="COD">Cash on Delivery (COD)</option>
              <option value="UPI">UPI (Demo)</option>
              <option value="Card">Card (Demo)</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Reference (optional)</label>
            <input name="provider_ref" class="form-control" placeholder="UPI Txn ID / Last 4 Card / Note">
          </div>
          <button class="btn btn-brand">Pay Now</button>
        </form>
      </div>
    </div>

    <div class="col-lg-5">
      <div class="glass p-3">
        <h5 class="mb-3">Order Summary</h5>
        <ul class="list-group mb-3">
          <?php foreach($items as $it): ?>
            <li class="list-group-item d-flex justify-content-between align-items-center">
              <div>
                <div class="fw-semibold"><?=htmlspecialchars($it['name'])?></div>
                <small class="text-muted">Qty: <?=$it['quantity']?></small>
              </div>
              <div>₹<?=number_format((float)$it['price']*$it['quantity'],2)?></div>
            </li>
          <?php endforeach; ?>
        </ul>
        <div class="d-flex justify-content-between"><span>Subtotal</span><strong>₹<?=number_format((float)$order['subtotal'],2)?></strong></div>
        <div class="d-flex justify-content-between"><span>Shipping</span><strong>₹<?=number_format((float)$order['shipping'],2)?></strong></div>
        <hr class="my-2">
        <div class="d-flex justify-content-between"><span>Total to Pay</span><strong>₹<?=number_format((float)$order['total'],2)?></strong></div>
        <div class="text-muted small mt-2">Secure demo payment. For real gateway, integrate later.</div>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
