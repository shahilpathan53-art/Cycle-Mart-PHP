<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

$err=''; $msg='';

// Load suppliers and recent POs for selection
$suppliers=[]; $sres=mysqli_query($conn,"SELECT id,company_name FROM suppliers ORDER BY company_name");
while($r=mysqli_fetch_assoc($sres)) $suppliers[]=$r;

$pos=[]; $pres=mysqli_query($conn,"SELECT id, supplier_id, total, status FROM purchase_orders ORDER BY id DESC LIMIT 100");
while($r=mysqli_fetch_assoc($pres)) $pos[]=$r;

// Handle payment record
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add'])) {
  $supplier_id = (int)($_POST['supplier_id'] ?? 0);
  $po_id = (int)($_POST['po_id'] ?? 0);
  $method = mysqli_real_escape_string($conn, trim($_POST['method'] ?? ''));
  $amount = (float)($_POST['amount'] ?? 0);
  $ref    = mysqli_real_escape_string($conn, trim($_POST['reference'] ?? ''));
  $pdate  = mysqli_real_escape_string($conn, trim($_POST['paid_at'] ?? ''));

  if ($supplier_id<=0 || $amount<=0 || $method==='') {
    $err='Supplier, amount and method are required.';
  } else {
    $dateSql = $pdate!=='' ? "'$pdate'" : "NOW()";
    $poCol = $po_id>0 ? (string)$po_id : "NULL";
    $ok = mysqli_query($conn, "INSERT INTO supplier_payments (supplier_id, po_id, method, amount, reference, paid_at, created_at)
                               VALUES ($supplier_id, $poCol, '$method', $amount, '$ref', $dateSql, NOW())");
    if ($ok) { $msg='Payment recorded.'; } else { $err='Insert failed.'; }
  }
}

// Recent supplier payments list
$rows=[];
$q = "SELECT sp.id, s.company_name, sp.po_id, sp.method, sp.amount, sp.reference, sp.paid_at
      FROM supplier_payments sp
      JOIN suppliers s ON s.id=sp.supplier_id
      ORDER BY sp.id DESC LIMIT 50";
$res = mysqli_query($conn, $q);
while($r=mysqli_fetch_assoc($res)) $rows[]=$r;

include '../includes/header_admin.php';
include '../includes/nav_admin.php';
?>
<div class="container my-4">
  <div class="glass p-3 mb-3">
    <h3 class="mb-0">Supplier Payments</h3>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
  <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

  <div class="glass p-3 mb-3">
    <h5 class="mb-3">Record Payment</h5>
    <form method="post" class="row g-2">
      <input type="hidden" name="add" value="1">
      <div class="col-md-4">
        <label class="form-label">Supplier</label>
        <select name="supplier_id" class="form-select" required>
          <option value="">Select supplier</option>
          <?php foreach($suppliers as $s): ?>
            <option value="<?=$s['id']?>"><?=htmlspecialchars($s['company_name'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">PO (optional)</label>
        <select name="po_id" class="form-select">
          <option value="0">No PO reference</option>
          <?php foreach($pos as $po): ?>
            <option value="<?=$po['id']?>">#<?=$po['id']?> · <?=$po['status']?> · ₹<?=number_format((float)$po['total'],2)?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Amount (₹)</label>
        <input name="amount" type="number" step="0.01" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Method</label>
        <select name="method" class="form-select" required>
          <option value="">Choose</option>
          <option value="Bank">Bank Transfer</option>
          <option value="UPI">UPI</option>
          <option value="Cheque">Cheque</option>
          <option value="Cash">Cash</option>
          <option value="Other">Other</option>
        </select>
      </div>
      <div class="col-md-4">
        <label class="form-label">Reference</label>
        <input name="reference" class="form-control" placeholder="Txn ID / Cheque no / Note">
      </div>
      <div class="col-md-3">
        <label class="form-label">Paid At (optional)</label>
        <input name="paid_at" type="datetime-local" class="form-control">
      </div>
      <div class="col-md-2 align-self-end">
        <button class="btn btn-brand w-100">Save</button>
      </div>
    </form>
  </div>

  <div class="glass p-0">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th style="width:8%">#</th>
          <th>Supplier</th>
          <th style="width:12%">PO</th>
          <th style="width:12%">Method</th>
          <th style="width:14%">Amount</th>
          <th style="width:20%">Reference</th>
          <th style="width:20%">Paid At</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td>#<?=$r['id']?></td>
            <td><?=htmlspecialchars($r['company_name'])?></td>
            <td><?= $r['po_id'] ? '#'.$r['po_id'] : '-' ?></td>
            <td><?=htmlspecialchars($r['method'])?></td>
            <td>₹<?=number_format((float)$r['amount'],2)?></td>
            <td><?=htmlspecialchars($r['reference'] ?? '')?></td>
            <td><?=htmlspecialchars($r['paid_at'])?></td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($rows)): ?>
          <tr><td colspan="7" class="text-center py-4">No payments recorded.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
