<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

$id = (int)($_GET['id'] ?? 0);
if ($id<=0){ http_response_code(400); die('Invalid PO id'); }

$err=''; $msg='';

// Handle mark received (force close)
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['mark_received'])) {
  // compute if fully received
  $sum = mysqli_query($conn, "SELECT SUM(qty) rq, SUM(received_qty) rr FROM purchase_order_items WHERE po_id=$id");
  $s = mysqli_fetch_assoc($sum);
  $fully = ((int)$s['rq']>0 && (int)$s['rq']===(int)$s['rr']);
  // Allow admin to mark received regardless (could add guard if needed)
  mysqli_query($conn, "UPDATE purchase_orders SET status='received', received_at=NOW() WHERE id=$id");
  $msg='PO marked as received.';
}

// Load header
$h = mysqli_query($conn, "SELECT po.id, po.status, po.total, po.created_at, po.received_at, s.company_name
                          FROM purchase_orders po JOIN suppliers s ON s.id=po.supplier_id
                          WHERE po.id=$id LIMIT 1");
$po = mysqli_fetch_assoc($h);
if (!$po){ http_response_code(404); die('PO not found'); }

// Load items
$items=[]; $iq = mysqli_query($conn, "SELECT i.id, p.name, i.qty, i.received_qty, i.unit_cost
                                      FROM purchase_order_items i JOIN products p ON p.id=i.product_id
                                      WHERE i.po_id=$id");
while($r=mysqli_fetch_assoc($iq)) $items[]=$r;

// Determine completion
$req=0; $rec=0; foreach($items as $it){ $req+=(int)$it['qty']; $rec+=(int)$it['received_qty']; }
$complete = ($req>0 && $req===$rec);

// Sum payments against this PO (Paid / Balance)
$pay_q = mysqli_query($conn, "SELECT COALESCE(SUM(amount),0) AS paid FROM supplier_payments WHERE po_id=$id");
$pay = mysqli_fetch_assoc($pay_q);
$paid = (float)($pay['paid'] ?? 0.0);
$po_total = (float)$po['total'];
$balance = max(0.0, $po_total - $paid);

include '../includes/header_admin.php';
include '../includes/nav_admin.php';
?>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="glass p-3">
      <h3 class="mb-0">PO #<?=$po['id']?> — <?=htmlspecialchars($po['company_name'])?></h3>
      <div class="text-muted">Created: <?=htmlspecialchars($po['created_at'])?></div>
    </div>
    <div class="glass p-3">
      <?php
        $badge='text-bg-secondary';
        if($po['status']==='ordered') $badge='text-bg-warning';
        if($po['status']==='part_received') $badge='text-bg-info';
        if($po['status']==='received') $badge='text-bg-success';
        if($po['status']==='cancelled') $badge='text-bg-dark';
      ?>
      <div>Status: <span class="badge <?=$badge?>"><?=htmlspecialchars($po['status'])?></span></div>
      <div>Total: <strong>₹<?=number_format($po_total,2)?></strong></div>
      <div>Paid: <span class="badge text-bg-primary">₹<?=number_format($paid,2)?></span></div>
      <div>Balance: <span class="badge <?= $balance>0 ? 'text-bg-warning' : 'text-bg-success' ?>">₹<?=number_format($balance,2)?></span></div>
      <?php if($po['received_at']): ?><div>Received: <?=htmlspecialchars($po['received_at'])?></div><?php endif; ?>
      <?php if ($paid > $po_total): ?>
        <div class="alert alert-warning mt-2 mb-0 py-1 px-2">Warning: Paid exceeds PO total.</div>
      <?php endif; ?>
    </div>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
  <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

  <div class="glass p-0">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th>Product</th>
          <th style="width:12%">Qty</th>
          <th style="width:12%">Received</th>
          <th style="width:15%">Unit Cost</th>
          <th style="width:15%">Line Total</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $it):
          $done = (int)$it['qty'] === (int)$it['received_qty'];
          $b = $done ? 'text-bg-success' : 'text-bg-warning';
        ?>
          <tr>
            <td><?=htmlspecialchars($it['name'])?></td>
            <td><?=$it['qty']?></td>
            <td>
              <?=$it['received_qty']?>
              <span class="badge <?=$b?> ms-2"><?= $done ? 'Done' : 'Pending' ?></span>
            </td>
            <td>₹<?=number_format((float)$it['unit_cost'],2)?></td>
            <td>₹<?=number_format((float)$it['unit_cost']*$it['qty'],2)?></td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($items)): ?>
          <tr><td colspan="5" class="text-center py-4">No items.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="d-flex gap-2 mt-3">
    <a class="btn btn-outline-secondary" href="po_list.php">Back to PO List</a>
    <form method="post">
      <button class="btn btn-brand" name="mark_received" value="1" <?= $po['status']==='received' ? 'disabled' : '' ?>>
        Mark Received
      </button>
    </form>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
