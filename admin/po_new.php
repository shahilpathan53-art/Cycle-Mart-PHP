<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

$err=''; $msg='';

// suppliers list
$suppliers=[]; $sres=mysqli_query($conn,"SELECT id,company_name FROM suppliers ORDER BY company_name");
while($r=mysqli_fetch_assoc($sres)) $suppliers[]=$r;

// Handle submit
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['supplier_id'])) {
  $supplier_id=(int)($_POST['supplier_id'] ?? 0);

  // Ensure arrays exist and are arrays
  $prod = (isset($_POST['product_id']) && is_array($_POST['product_id'])) ? $_POST['product_id'] : [];
  $qty  = (isset($_POST['qty'])        && is_array($_POST['qty']))        ? $_POST['qty']        : [];
  $cost = (isset($_POST['unit_cost'])  && is_array($_POST['unit_cost']))  ? $_POST['unit_cost']  : [];

  if ($supplier_id<=0) {
    $err='Select a supplier.';
  } else {
    // Build clean lines: each is [pid(int), q(int), uc(float)] and skip bad inputs
    $lines=[];
    $n = max(count($prod), count($qty), count($cost));
    for ($i=0; $i<$n; $i++) {
      $rawPid = $prod[$i]  ?? null;
      $rawQty = $qty[$i]   ?? null;
      $rawCost= $cost[$i]  ?? null;

      // Skip if any element is an array (malformed POST) or empty string
      if (is_array($rawPid) || is_array($rawQty) || is_array($rawCost)) continue;
      if ($rawPid === '' || $rawQty === '' || $rawCost === '') continue;

      // Cast to numeric, enforce bounds
      $pid = (int)$rawPid;
      $q   = (int)$rawQty;
      $uc  = (float)$rawCost;

      if ($pid>0 && $q>0 && $uc>0) {
        $lines[] = [$pid, $q, $uc];
      }
    }

    if (empty($lines)) {
      $err='Add at least one valid line (product, qty, unit cost).';
    } else {
      // Compute total safely
      $total = 0.0;
      foreach ($lines as $ln) {
        // $ln[1] = qty (int), $ln[2] = unit cost (float)
        $total += ((int)$ln[1]) * ((float)$ln[2]);
      }

      // Create PO header
      mysqli_query($conn, "INSERT INTO purchase_orders (supplier_id,status,total,created_at) VALUES ($supplier_id,'ordered',$total,NOW())");
      $po_id = mysqli_insert_id($conn);

      // Insert lines with prepared statement
      $stmt = mysqli_prepare($conn, "INSERT INTO purchase_order_items (po_id,product_id,qty,received_qty,unit_cost) VALUES (?,?,?,?,?)");
      foreach($lines as $ln){
        $zero=0;
        mysqli_stmt_bind_param($stmt, "iiiid", $po_id, $ln[0], $ln[1], $zero, $ln[2]);
        mysqli_stmt_execute($stmt);
      }
      mysqli_stmt_close($stmt);

      // Redirect to PO view
      header('Location: po_view.php?id='.$po_id); exit;
    }
  }
}

// products list (all active)
$products=[]; $pres=mysqli_query($conn,"SELECT id,name FROM products WHERE is_active=1 ORDER BY name");
while($r=mysqli_fetch_assoc($pres)) $products[]=$r;

include '../includes/header_admin.php';
include '../includes/nav_admin.php';
?>
<div class="container my-4">
  <div class="glass p-3 mb-3">
    <h3 class="mb-0">Create Purchase Order</h3>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>

  <form method="post" id="poForm" class="glass p-3">
    <div class="row g-2 mb-3">
      <div class="col-md-6">
        <label class="form-label">Supplier</label>
        <select name="supplier_id" class="form-select" required>
          <option value="">Select supplier</option>
          <?php foreach($suppliers as $s): ?>
            <option value="<?=$s['id']?>"><?=htmlspecialchars($s['company_name'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
    </div>

    <h5 class="mt-2 mb-2">Items</h5>
    <div id="lines">
      <div class="row g-2 align-items-end mb-2 line">
        <div class="col-md-5">
          <label class="form-label">Product</label>
          <select name="product_id[]" class="form-select" required>
            <option value="">Select product</option>
            <?php foreach($products as $p): ?>
              <option value="<?=$p['id']?>"><?=htmlspecialchars($p['name'])?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="col-md-2">
          <label class="form-label">Qty</label>
          <input name="qty[]" type="number" min="1" class="form-control" required>
        </div>
        <div class="col-md-3">
          <label class="form-label">Unit Cost (₹)</label>
          <input name="unit_cost[]" type="number" step="0.01" min="0" class="form-control" required>
        </div>
        <div class="col-md-2">
          <button class="btn btn-outline-secondary w-100 remove-line" type="button">Remove</button>
        </div>
      </div>
    </div>

    <div class="d-flex gap-2 mt-2">
      <button class="btn btn-outline-secondary" type="button" id="addLine">Add Line</button>
      <button class="btn btn-brand" type="submit">Create PO</button>
    </div>
  </form>
</div>

<script>
document.getElementById('addLine').addEventListener('click', function(){
  const lines = document.getElementById('lines');
  const tpl = lines.querySelector('.line').cloneNode(true);
  // reset values
  tpl.querySelectorAll('input').forEach(i=>{ i.value=''; });
  tpl.querySelectorAll('select').forEach(s=>{ s.selectedIndex=0; });
  lines.appendChild(tpl);
});
document.addEventListener('click', function(e){
  if(e.target && e.target.classList.contains('remove-line')){
    const lines = document.getElementById('lines');
    const line = e.target.closest('.line');
    if(lines.querySelectorAll('.line').length>1){ line.remove(); }
  }
});
</script>
<?php include '../includes/footer.php'; ?>
