<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('user');

$u = user();

// Ensure user cart exists
$cartRes = mysqli_query($conn, "SELECT id FROM carts WHERE user_id=".(int)$u['id']." LIMIT 1");
$cart = mysqli_fetch_assoc($cartRes);
if (!$cart) {
  mysqli_query($conn, "INSERT INTO carts (user_id) VALUES (".(int)$u['id'].")");
  $cartId = mysqli_insert_id($conn);
} else {
  $cartId = $cart['id'];
}

// Handle actions
$action = $_GET['action'] ?? '';

if ($action === 'add') {
  $pid = (int)($_GET['id'] ?? 0);
  if ($pid > 0) {
    $it = mysqli_query($conn, "SELECT id,quantity FROM cart_items WHERE cart_id=$cartId AND product_id=$pid");
    $row = mysqli_fetch_assoc($it);
    if ($row) {
      $newQ = $row['quantity'] + 1;
      mysqli_query($conn, "UPDATE cart_items SET quantity=$newQ WHERE id=".(int)$row['id']);
    } else {
      mysqli_query($conn, "INSERT INTO cart_items (cart_id,product_id,quantity) VALUES ($cartId,$pid,1)");
    }
  }
  header("Location: cart.php"); exit;
}

if ($action === 'remove') {
  $iid = (int)($_GET['id'] ?? 0);
  if ($iid > 0) {
    mysqli_query($conn, "DELETE FROM cart_items WHERE id=$iid AND cart_id=$cartId");
  }
  header("Location: cart.php"); exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!empty($_POST['qty']) && is_array($_POST['qty'])) {
    foreach ($_POST['qty'] as $itemId => $qty) {
      $iid = (int)$itemId;
      $q   = max(0, (int)$qty);
      if ($q === 0) {
        mysqli_query($conn, "DELETE FROM cart_items WHERE id=$iid AND cart_id=$cartId");
      } else {
        mysqli_query($conn, "UPDATE cart_items SET quantity=$q WHERE id=$iid AND cart_id=$cartId");
      }
    }
  }
  // ⚡️ NO redirect here → items reload immediately
}

// Load cart items again after update
$q = "SELECT ci.id ciid, p.id pid, p.name, p.price, COALESCE(i.stock,0) stock, ci.quantity
      FROM cart_items ci
      JOIN products p ON p.id=ci.product_id
      LEFT JOIN inventory i ON i.product_id=p.id
      WHERE ci.cart_id=$cartId";
$res = mysqli_query($conn, $q);

$items = [];
$subtotal = 0;
while ($r = mysqli_fetch_assoc($res)) {
  $items[] = $r;
  $subtotal += ((float)$r['price'] * (int)$r['quantity']);
}
$shipping = ($subtotal >= 20000) ? 0 : 299;
$total    = $subtotal + $shipping;

include '../includes/header_user.php';
include '../includes/nav_user.php';
?>
<div class="container my-4">
  <div class="glass p-4 mb-3">
    <h3 class="mb-0">Your Cart</h3>
  </div>

  <form method="post">
    <div class="glass p-0">
      <table class="table align-middle mb-0">
        <thead>
          <tr>
            <th style="width:45%">Product</th>
            <th style="width:15%">Price</th>
            <th style="width:15%">Qty</th>
            <th style="width:15%">Line</th>
            <th style="width:10%"></th>
          </tr>
        </thead>
        <tbody>
          <?php foreach($items as $r): ?>
            <tr>
              <td>
                <?=htmlspecialchars($r['name'])?>
                <?=($r['stock'] < $r['quantity'] ? '<span class="badge text-bg-danger ms-2">Low stock</span>' : '')?>
              </td>
              <td>₹<?=number_format((float)$r['price'],2)?></td>
              <td style="max-width:120px;">
                <input type="number" name="qty[<?=$r['ciid']?>]" class="form-control" min="0" value="<?=$r['quantity']?>">
              </td>
              <td>₹<?=number_format((float)$r['price'] * (int)$r['quantity'],2)?></td>
              <td>
                <a href="cart.php?action=remove&id=<?=$r['ciid']?>" class="btn btn-sm btn-outline-danger">Remove</a>
              </td>
            </tr>
          <?php endforeach; ?>

          <?php if (empty($items)): ?>
            <tr>
              <td colspan="5" class="text-center py-4">Your cart is empty.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>

    <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mt-3 gap-2">
      <div class="d-flex gap-2">
        <a class="btn btn-outline-secondary" href="catalog.php">Continue Shopping</a>
        <button class="btn btn-outline-secondary" type="submit">Update Cart</button>
      </div>
      <div class="glass p-3" style="min-width:280px">
        <div class="d-flex justify-content-between">
          <span>Subtotal</span>
          <strong>₹<?=number_format((float)$subtotal,2)?></strong>
        </div>
        <div class="d-flex justify-content-between">
          <span>Shipping</span>
          <strong>₹<?=number_format((float)$shipping,2)?></strong>
        </div>
        <hr class="my-2">
        <div class="d-flex justify-content-between">
          <span>Total</span>
          <strong>₹<?=number_format((float)$total,2)?></strong>
        </div>
        <a class="btn btn-brand w-100 mt-3 <?=empty($items)?'disabled':''?>" href="<?=empty($items)?'#':'checkout.php'?>">
          Proceed to Checkout
        </a>
      </div>
    </div>
  </form>
</div>
<?php include '../includes/footer.php'; ?>
