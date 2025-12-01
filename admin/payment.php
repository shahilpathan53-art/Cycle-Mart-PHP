<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

// Enable MySQLi exceptions for clearer errors during development
// Comment these two lines in production if preferred.
mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

$err=''; $msg='';

$order_id = (int)($_GET['id'] ?? 0);
if ($order_id<=0) { http_response_code(400); die('Invalid order id'); }

try {
  // Load order header (align columns with your schema)
  $sql = "SELECT o.id, o.user_id, u.name AS customer, o.status, o.payment_status, o.total, o.created_at
          FROM orders o 
          JOIN users u ON u.id=o.user_id
          WHERE o.id=? LIMIT 1";
  $stmt = mysqli_prepare($conn, $sql);
  mysqli_stmt_bind_param($stmt, "i", $order_id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $order = mysqli_fetch_assoc($res);
  mysqli_stmt_close($stmt);

  if (!$order) { http_response_code(404); die('Order not found'); }

  // If your users table uses full_name or email, change u.name accordingly.
  // If your orders table uses grand_total or amount, change o.total accordingly.

  // Sum payments for this order
  $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) AS paid FROM order_payments WHERE order_id=?");
  mysqli_stmt_bind_param($stmt, "i", $order_id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $sum = mysqli_fetch_assoc($res);
  mysqli_stmt_close($stmt);

  $paid = (float)($sum['paid'] ?? 0.0);
  $balance = max(0.0, (float)$order['total'] - $paid);

  // Handle new payment submit
  if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_payment'])) {
    $amount    = (float)($_POST['amount'] ?? 0);
    $method    = trim($_POST['method'] ?? '');
    $reference = trim($_POST['reference'] ?? '');
    $paid_at   = trim($_POST['paid_at'] ?? '');

    if ($amount<=0) {
      $err='Enter a positive amount.';
    } elseif ($method==='') {
      $err='Select a method.';
    } else {
      $already = $paid;
      $total   = (float)$order['total'];
      if ($already + $amount > $total + 0.01) {
        $err='Payment exceeds order total. Adjust the amount.';
      }
    }

    if (!$err) {
      $ins = mysqli_prepare($conn, "INSERT INTO order_payments (order_id, amount, method, reference, paid_at, created_at) VALUES (?,?,?,?,?, NOW())");
      $paidAt = $paid_at !== '' ? $paid_at : date('Y-m-d H:i:s');
      mysqli_stmt_bind_param($ins, "idsss", $order_id, $amount, $method, $reference, $paidAt);
      mysqli_stmt_execute($ins);
      mysqli_stmt_close($ins);

      // Recompute paid/balance
      $stmt = mysqli_prepare($conn, "SELECT COALESCE(SUM(amount),0) AS paid FROM order_payments WHERE order_id=?");
      mysqli_stmt_bind_param($stmt, "i", $order_id);
      mysqli_stmt_execute($stmt);
      $res = mysqli_stmt_get_result($stmt);
      $sum = mysqli_fetch_assoc($res);
      mysqli_stmt_close($stmt);

      $paid = (float)($sum['paid'] ?? 0.0);
      $balance = max(0.0, (float)$order['total'] - $paid);

      // Auto-update payment_status
      $newPayStatus = $paid >= $order['total'] - 0.01 ? 'paid' : 'partial';
      $ups = mysqli_prepare($conn, "UPDATE orders SET payment_status=? WHERE id=?");
      mysqli_stmt_bind_param($ups, "si", $newPayStatus, $order_id);
      mysqli_stmt_execute($ups);
      mysqli_stmt_close($ups);

      $msg='Payment recorded.';
      // Refresh header value
      $order['payment_status'] = $newPayStatus;
    }
  }

  // Load payments list
  $stmt = mysqli_prepare($conn, "SELECT id, amount, method, reference, paid_at, created_at FROM order_payments WHERE order_id=? ORDER BY id DESC");
  mysqli_stmt_bind_param($stmt, "i", $order_id);
  mysqli_stmt_execute($stmt);
  $res = mysqli_stmt_get_result($stmt);
  $rows = [];
  while ($r = mysqli_fetch_assoc($res)) { $rows[] = $r; }
  mysqli_stmt_close($stmt);

} catch (mysqli_sql_exception $e) {
  // Show concise error for debugging; adjust messaging for production
  http_response_code(500);
  die('SQL error: '.$e->getMessage());
}

include '../includes/header_admin.php';
include '../includes/nav_admin.php';
?>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="glass p-3">
      <h3 class="mb-0">Payment — Order #<?= (int)$order['id'] ?></h3>
      <div class="text-muted">Customer: <?= htmlspecialchars($order['customer']) ?></div>
      <div class="small">Placed: <?= htmlspecialchars($order['created_at']) ?></div>
    </div>
    <div class="glass p-3">
      <?php
        $badge='text-bg-secondary';
        if ($order['status']==='pending') $badge='text-bg-warning';
        if ($order['status']==='confirmed') $badge='text-bg-info';
        if ($order['status']==='shipped') $badge='text-bg-primary';
        if ($order['status']==='delivered') $badge='text-bg-success';
        if ($order['status']==='cancelled') $badge='text-bg-dark';
      ?>
      <div>Status: <span class="badge <?= $badge ?>"><?= htmlspecialchars($order['status']) ?></span></div>
      <div>Total: <strong>₹<?= number_format((float)$order['total'],2) ?></strong></div>
      <div>Paid: <span class="badge text-bg-primary">₹<?= number_format($paid,2) ?></span></div>
      <div>Balance: <span class="badge <?= $balance>0 ? 'text-bg-warning' : 'text-bg-success' ?>">₹<?= number_format($balance,2) ?></span></div>
      <div>Payment Status: <span class="badge text-bg-secondary"><?= htmlspecialchars($order['payment_status']) ?></span></div>
      <?php if ($paid > (float)$order['total']): ?>
        <div class="alert alert-warning mt-2 mb-0 py-1 px-2">Warning: Paid exceeds order total.</div>
      <?php endif; ?>
    </div>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?= htmlspecialchars($err) ?></div><?php endif; ?>
  <?php if($msg): ?><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div><?php endif; ?>

  <div class="row g-3">
    <div class="col-lg-6">
      <div class="glass p-3 h-100">
        <h5 class="mb-3">Add Payment</h5>
        <form method="post">
          <div class="mb-3">
            <label class="form-label">Amount (₹)</label>
            <input name="amount" type="number" step="0.01" min="0.01" class="form-control" required>
          </div>
          <div class="mb-3">
            <label class="form-label">Method</label>
            <select name="method" class="form-select" required>
              <option value="">Select</option>
              <option>Cash</option>
              <option>Card</option>
              <option>UPI</option>
              <option>Bank</option>
              <option>Wallet</option>
              <option>Other</option>
            </select>
          </div>
          <div class="mb-3">
            <label class="form-label">Reference (Txn ID / Notes)</label>
            <input name="reference" class="form-control" placeholder="Optional">
          </div>
          <div class="mb-3">
            <label class="form-label">Paid At</label>
            <input name="paid_at" type="datetime-local" class="form-control" value="<?= date('Y-m-d\TH:i') ?>">
          </div>
          <button class="btn btn-brand" name="add_payment" value="1">Save Payment</button>
          <a class="btn btn-outline-secondary ms-2" href="orders.php">Back to Orders</a>
        </form>
      </div>
    </div>

    <div class="col-lg-6">
      <div class="glass p-3 h-100">
        <h5 class="mb-3">Payments for Order #<?= (int)$order['id'] ?></h5>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr>
                <th style="width:10%">ID</th>
                <th style="width:20%">Amount</th>
                <th style="width:20%">Method</th>
                <th>Reference</th>
                <th style="width:25%">Paid At</th>
              </tr>
            </thead>
            <tbody>
              <?php if(empty($rows)): ?>
                <tr><td colspan="5" class="text-center py-4">No payments yet.</td></tr>
              <?php else: foreach($rows as $row): ?>
                <tr>
                  <td><?= (int)$row['id'] ?></td>
                  <td>₹<?= number_format((float)$row['amount'],2) ?></td>
                  <td><?= htmlspecialchars($row['method']) ?></td>
                  <td><?= htmlspecialchars($row['reference'] ?: '—') ?></td>
                  <td><?= htmlspecialchars($row['paid_at']) ?></td>
                </tr>
              <?php endforeach; endif; ?>
            </tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
