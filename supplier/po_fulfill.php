<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('supplier');

$u = user();
// Resolve supplier id
$sres = mysqli_query($conn, "SELECT id, company_name FROM suppliers WHERE user_id=".$u['id']." LIMIT 1");
$supplier = mysqli_fetch_assoc($sres);
if(!$supplier){ die('Supplier profile missing.'); }
$supplierId = (int)$supplier['id'];

$po_id = (int)($_GET['id'] ?? 0);
$err=''; $msg='';

// If a submit happens, process fulfill quantities
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['fulfill'])) {
  $po_id = (int)($_POST['po_id'] ?? 0);
  // Validate PO belongs to this supplier
  $poq = mysqli_query($conn, "SELECT id,status FROM purchase_orders WHERE id=$po_id AND supplier_id=$supplierId LIMIT 1");
  $po = mysqli_fetch_assoc($poq);
  if (!$po) { $err='Purchase Order not found.'; }
  else {
    $fulfill = $_POST['fulfill_qty'] ?? [];
    foreach($fulfill as $itemId=>$qty){
      $iid = (int)$itemId; $q = max(0, (int)$qty);
      if ($q>0) {
        // Get product and remaining
        $rq = mysqli_query($conn, "SELECT product_id, qty, received_qty FROM purchase_order_items WHERE id=$iid AND po_id=$po_id");
        $r = mysqli_fetch_assoc($rq);
        if ($r) {
          $remain = (int)$r['qty'] - (int)$r['received_qty'];
          if ($remain>0) {
            $apply = min($q, $remain);
            // Update received_qty
            mysqli_query($conn, "UPDATE purchase_order_items SET received_qty = received_qty + $apply WHERE id=$iid");
            // Increase inventory
            mysqli_query($conn, "INSERT IGNORE INTO inventory (product_id, stock) VALUES (".$r['product_id'].", 0)");
            mysqli_query($conn, "UPDATE inventory SET stock = stock + $apply WHERE product_id=".$r['product_id']);
          }
        }
      }
    }
    // Recompute PO status
    $sum = mysqli_query($conn, "SELECT SUM(qty) rq, SUM(received_qty) rr FROM purchase_order_items WHERE po_id=$po_id");
    $t = mysqli_fetch_assoc($sum);
    $new = 'ordered';
    if ((int)$t['rr']===0) $new='ordered';
    elseif ((int)$t['rr'] < (int)$t['rq']) $new='part_received';
    else $new='received';
    mysqli_query($conn, "UPDATE purchase_orders SET status='$new', received_at = CASE WHEN '$new'='received' THEN NOW() ELSE received_at END WHERE id=$po_id");
    $msg='Fulfillment updated.';
  }
}

// Load list of this supplier POs for quick select
$pos = [];
$list = mysqli_query($conn, "SELECT id,status,total,created_at FROM purchase_orders WHERE supplier_id=$supplierId ORDER BY id DESC");
while($row=mysqli_fetch_assoc($list)) $pos[]=$row;

// If a PO selected, load its items
$po=null; $items=[];
if ($po_id>0) {
  $pq = mysqli_query($conn, "SELECT id,status,total,created_at FROM purchase_orders WHERE id=$po_id AND supplier_id=$supplierId");
  $po = mysqli_fetch_assoc($pq);
  if ($po) {
    $iq = mysqli_query($conn, "SELECT i.id, i.product_id, p.name, i.qty, i.received_qty, i.unit_cost
                               FROM purchase_order_items i JOIN products p ON p.id=i.product_id
                               WHERE i.po_id=".$po['id']);
    while($r=mysqli_fetch_assoc($iq)) $items[]=$r;
  } else {
    $err = $err ?: 'Purchase Order not found or access denied.';
  }
}

include '../includes/header_supplier.php';
include '../includes/nav_supplier.php';
?>
<div class="container my-4">
  <div class="glass p-3 mb-3">
    <h3 class="mb-0">Purchase Order Fulfillment</h3>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
  <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

  <div class="glass p-3 mb-3">
    <form class="row g-2" method="get">
      <div class="col-md-6">
        <label class="form-label">Choose PO</label>
        <select name="id" class="form-select" onchange="this.form.submit()">
          <option value="0">Select Purchase Order</option>
          <?php foreach($pos as $p): ?>
            <option value="<?=$p['id']?>" <?=$po_id==$p['id']?'selected':''?>>#<?=$p['id']?> · <?=$p['status']?> · ₹<?=number_format((float)$p['total'],2)?> · <?=$p['created_at']?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2 align-self-end">
        <button class="btn btn-brand w-100">Open</button>
      </div>
    </form>
  </div>

  <?php if($po): ?>
  <form method="post" class="glass p-0">
    <input type="hidden" name="po_id" value="<?=$po['id']?>">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th>Product</th><th>Ordered</th><th>Received</th><th>Unit Cost</th><th>Total cost</th><th style="width:22%">Fulfill Now</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($items as $it):
          $remain = (int)$it['qty'] - (int)$it['received_qty'];
        ?>
          <tr>
            <td><?=htmlspecialchars($it['name'])?></td>
            <td><?=$it['qty']?></td>
            <td><?=$it['received_qty']?></td>
            <td>₹<?=number_format((float)$it['unit_cost'],2)?></td>
            <td>₹<?=number_format((float)$it['unit_cost']*$it['qty'],2)?></td>
            <td>
              <?php if($remain>0): ?>
                <div class="input-group">
                  <input type="number" name="fulfill_qty[<?=$it['id']?>]" class="form-control" min="0" max="<?=$remain?>" placeholder="0">
                  <button class="btn btn-brand" name="fulfill" value="1">Apply</button>
                </div>
              <?php else: ?>
                <span class="badge text-bg-success">Completed</span>
              <?php endif; ?>
            </td>
          </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </form>

  <div class="d-flex gap-2 mt-3">
    <a class="btn btn-outline-secondary" href="dashboard.php">Back to Dashboard</a>
  </div>
  <?php endif; ?>
</div>
<?php include '../includes/footer.php'; ?>
