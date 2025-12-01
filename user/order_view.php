<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('user');

if (session_status() === PHP_SESSION_NONE) session_start();
$user_id = (int)($_SESSION['user']['id'] ?? 0);
if ($user_id <= 0) { header('Location: /user/login.php'); exit; }

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); die('Invalid order id'); }

// During development: turn on MySQLi exceptions for clear errors.
// Comment out in production or wrap in try/catch for friendly UI.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT); // throws mysqli_sql_exception on SQL errors [web:884][web:886]

// Map these to your actual column names if they differ:
$ORDER_TOTAL_COL = 'total';      // e.g., 'amount' or 'grand_total'
$ORDER_USER_COL  = 'user_id';    // e.g., 'customer_id'

try {
  // Load order header for this user
  $sql1 = "SELECT o.id, o.status, o.payment_status, o.$ORDER_TOTAL_COL AS total, o.created_at
           FROM orders o
           WHERE o.id=? AND o.$ORDER_USER_COL=? LIMIT 1";
  $stmt = mysqli_prepare($conn, $sql1);
  if ($stmt === false) {
    // Fallback diagnostic if prepare fails silently in some setups
    die('Prepare failed for header. MySQL: '.mysqli_error($conn));
  }
  mysqli_stmt_bind_param($stmt, "ii", $id, $user_id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $order = mysqli_fetch_assoc($res);
  mysqli_stmt_close($stmt);

  if (!$order) { http_response_code(404); die('Order not found or not accessible.'); }

  // Load items with product thumbnails (adjust columns if needed)
$sql2 = "SELECT 
            oi.id,
            oi.product_id,
            COALESCE(p.name, '') AS name,
            oi.price,
            oi.quantity AS qty,           -- change this to your real column (e.g., quantity or qty_ordered)
            COALESCE(p.thumbnail, '') AS thumbnail
         FROM order_items oi
         LEFT JOIN products p ON p.id = oi.product_id
         WHERE oi.order_id=?
         ORDER BY oi.id ASC";


  $stmt = mysqli_prepare($conn, $sql2);
  if ($stmt === false) {
    die('Prepare failed for items. MySQL: '.mysqli_error($conn));
  }
  mysqli_stmt_bind_param($stmt, "i", $id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $items = [];
  while ($r = mysqli_fetch_assoc($res)) $items[] = $r;
  mysqli_stmt_close($stmt);

} catch (mysqli_sql_exception $e) {
  // Clear message that points to the exact SQL issue during dev
  http_response_code(500);
  die('SQL error: '.$e->getMessage()); // e.g., Unknown column 'user_id' in 'where clause' [web:805][web:824]
}

include '../includes/header_user.php';
include '../includes/nav_user.php';
?>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="glass p-3">
      <h3 class="mb-0">Order #<?= (int)$order['id'] ?></h3>
      <div class="text-muted">Placed: <?= htmlspecialchars($order['created_at']) ?></div>
    </div>
    <div class="glass p-3">
      <?php
        $badge='text-bg-secondary';
        if ($order['status']==='pending')   $badge='text-bg-warning';
        if ($order['status']==='confirmed') $badge='text-bg-info';
        if ($order['status']==='shipped')   $badge='text-bg-primary';
        if ($order['status']==='delivered') $badge='text-bg-success';
        if ($order['status']==='cancelled') $badge='text-bg-dark';

        $pBadge='text-bg-secondary';
        if ($order['payment_status']==='paid')    $pBadge='text-bg-success';
        if ($order['payment_status']==='partial') $pBadge='text-bg-warning';
        if ($order['payment_status']==='unpaid' || $order['payment_status']==='pending') $pBadge='text-bg-secondary';
      ?>
      <div>Status: <span class="badge <?= $badge ?>"><?= htmlspecialchars($order['status']) ?></span></div>
      <div>Payment: <span class="badge <?= $pBadge ?>"><?= htmlspecialchars($order['payment_status']) ?></span></div>
      <div>Total: <strong>₹<?= number_format((float)$order['total'],2) ?></strong></div>
    </div>
  </div>

  <div class="glass p-0">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th style="width:8%"></th>
          <th>Product</th>
          <th style="width:15%">Price</th>
          <th style="width:12%">Qty</th>
          <th style="width:18%">Line Total</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($items)): ?>
          <tr><td colspan="5" class="text-center py-4">No items found.</td></tr>
        <?php else: foreach ($items as $it): ?>
          <tr>
            <td>
              <img
                src="<?= htmlspecialchars($it['thumbnail'] ?: '/assets/img/placeholder.jpg') ?>"
                alt=""
                style="width:60px;height:60px;object-fit:cover;border-radius:8px;"
                onerror="this.onerror=null;this.src='/assets/img/placeholder.jpg';">
            </td>
            <td class="fw-semibold"><?= htmlspecialchars($it['name']) ?></td>
            <td>₹<?= number_format((float)$it['price'],2) ?></td>
            <td><?= (int)$it['qty'] ?></td>
            <td>₹<?= number_format((float)$it['price'] * (int)$it['qty'], 2) ?></td>
          </tr>
        <?php endforeach; endif; ?>
      </tbody>
      <?php if (!empty($items)): ?>
      <tfoot>
        <tr>
          <th colspan="4" class="text-end">Total</th>
          <th>₹<?= number_format((float)$order['total'],2) ?></th>
        </tr>
      </tfoot>
      <?php endif; ?>
    </table>
  </div>

  <div class="mt-3 d-flex gap-2">
    <a class="btn btn-outline-secondary" href="/user/order_history.php">Back to Orders</a>
    <?php if ($order['payment_status']!=='paid' && $order['status']!=='cancelled'): ?>
      <!-- Optional: integrate gateway later -->
      <a class="btn btn-brand" href="/user/checkout_payment.php?order_id=<?= (int)$order['id'] ?>">Pay Now</a>
    <?php endif; ?>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
