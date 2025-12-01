<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('user'); // only user login

$u = user();

// Ensure cart exists
$cartRes = mysqli_query($conn, "SELECT id FROM carts WHERE user_id=".(int)$u['id']." LIMIT 1");
$cart    = mysqli_fetch_assoc($cartRes);
if (!$cart) {
    // If no cart, go back safely
    header('Location: cart.php');
    exit;
}
$cartId = (int)$cart['id'];

// Load cart items
$q = "SELECT ci.id ciid, p.id pid, p.name, p.price, COALESCE(i.stock,0) stock, ci.quantity
      FROM cart_items ci
      JOIN products p ON p.id = ci.product_id
      LEFT JOIN inventory i ON i.product_id = p.id
      WHERE ci.cart_id = $cartId";
$res = mysqli_query($conn, $q);

$items = [];
$subtotal = 0;
$stockIssue = false;
while ($r = mysqli_fetch_assoc($res)) {
    if ($r['quantity'] > $r['stock']) {
        $stockIssue = true;
    }
    $items[] = $r;
    $subtotal += ((float)$r['price'] * (int)$r['quantity']);
}

// If empty cart
if (empty($items)) {
    header('Location: cart.php');
    exit;
}

$shipping = ($subtotal >= 20000) ? 0 : 299;
$total    = $subtotal + $shipping;

// Load default address
$addrRes = mysqli_query($conn, "SELECT id,line1,city,state,postal_code FROM addresses WHERE user_id=".(int)$u['id']." ORDER BY is_default DESC, id DESC LIMIT 1");
$addr    = mysqli_fetch_assoc($addrRes);

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Save/update address
    $line1 = mysqli_real_escape_string($conn, trim($_POST['line1'] ?? ''));
    $city  = mysqli_real_escape_string($conn, trim($_POST['city'] ?? ''));
    $state = mysqli_real_escape_string($conn, trim($_POST['state'] ?? ''));
    $pin   = mysqli_real_escape_string($conn, trim($_POST['postal_code'] ?? ''));

    if ($line1 === '' || $city === '' || $state === '' || $pin === '') {
        $err = 'Please fill full address.';
    } elseif ($stockIssue) {
        $err = 'Some items exceed stock. Update cart first.';
    } else {
        if ($addr) {
            // Update existing address
            mysqli_query($conn, "UPDATE addresses SET line1='$line1', city='$city', state='$state', postal_code='$pin', is_default=1 WHERE id=".(int)$addr['id']);
            $addrId = (int)$addr['id'];
        } else {
            // Insert new address
            mysqli_query($conn, "INSERT INTO addresses (user_id,line1,city,state,postal_code,is_default) VALUES (".(int)$u['id'].",'$line1','$city','$state','$pin',1)");
            $addrId = mysqli_insert_id($conn);
        }

        // Create order
        mysqli_query($conn, "INSERT INTO orders (user_id,address_id,status,subtotal,shipping,total,payment_status,created_at) 
                             VALUES (".(int)$u['id'].",$addrId,'pending',$subtotal,$shipping,$total,'unpaid',NOW())");
        $orderId = mysqli_insert_id($conn);

        // Insert order items
        $ins = mysqli_prepare($conn, "INSERT INTO order_items (order_id,product_id,price,quantity) VALUES (?,?,?,?)");
        foreach ($items as $it) {
            $pid = (int)$it['pid'];
            $price = (float)$it['price'];
            $qty = (int)$it['quantity'];
            mysqli_stmt_bind_param($ins, "iidi", $orderId, $pid, $price, $qty);
            mysqli_stmt_execute($ins);
        }
        mysqli_stmt_close($ins);

        // Reduce stock immediately (demo purpose)
        foreach ($items as $it) {
            mysqli_query($conn, "UPDATE inventory SET stock=GREATEST(stock-".(int)$it['quantity'].",0) WHERE product_id=".(int)$it['pid']);
        }

        // Empty cart
        mysqli_query($conn, "DELETE FROM cart_items WHERE cart_id=$cartId");

        // Redirect to payment
        header('Location: payment.php?id='.$orderId);
        exit;
    }
}

include '../includes/header_user.php';
include '../includes/nav_user.php';
?>
<div class="container my-4">
  <div class="glass p-3 mb-3">
    <h3 class="mb-0">Checkout</h3>
  </div>

  <?php if($err): ?>
    <div class="alert alert-danger"><?=$err?></div>
  <?php endif; ?>

  <div class="row g-3">
    <div class="col-lg-7">
      <div class="glass p-3">
        <h5 class="mb-3">Delivery Address</h5>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Address Line</label>
            <input name="line1" class="form-control" value="<?=htmlspecialchars($addr['line1'] ?? '')?>" placeholder="House no, street">
          </div>
          <div class="row g-2">
            <div class="col-md-4">
              <label class="form-label">City</label>
              <input name="city" class="form-control" value="<?=htmlspecialchars($addr['city'] ?? '')?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">State</label>
              <input name="state" class="form-control" value="<?=htmlspecialchars($addr['state'] ?? '')?>">
            </div>
            <div class="col-md-4">
              <label class="form-label">PIN</label>
              <input name="postal_code" class="form-control" value="<?=htmlspecialchars($addr['postal_code'] ?? '')?>">
            </div>
          </div>
          <button class="btn btn-brand mt-3" type="submit" <?= $stockIssue ? 'disabled' : '' ?>>Place Order</button>
          <?php if($stockIssue): ?>
            <div class="text-danger small mt-2">Fix stock issues in cart before placing order.</div>
          <?php endif; ?>
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
        <div class="d-flex justify-content-between"><span>Subtotal</span><strong>₹<?=number_format((float)$subtotal,2)?></strong></div>
        <div class="d-flex justify-content-between"><span>Shipping</span><strong>₹<?=number_format((float)$shipping,2)?></strong></div>
        <hr class="my-2">
        <div class="d-flex justify-content-between"><span>Total</span><strong>₹<?=number_format((float)$total,2)?></strong></div>
        <div class="text-muted small mt-2">Payment on next step.</div>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
