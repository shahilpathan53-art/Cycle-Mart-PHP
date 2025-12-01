<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('supplier');

$u = user();

// Resolve supplier id
$sres = mysqli_query($conn, "SELECT id FROM suppliers WHERE user_id=".$u['id']." LIMIT 1");
$supplier = mysqli_fetch_assoc($sres);
$supplierId = (int)($supplier['id'] ?? 0);
if ($supplierId<=0) { die('Supplier profile missing.'); }

// Handle stock updates
$err=''; $msg='';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['update_stock'])) {
  $pid   = (int)($_POST['product_id'] ?? 0);
  $delta = (int)($_POST['delta'] ?? 0);

  // Ensure product belongs to this supplier
  $own = mysqli_query($conn, "SELECT id FROM products WHERE id=$pid AND supplier_id=$supplierId LIMIT 1");
  if (!mysqli_fetch_assoc($own)) {
    $err = 'Product not found or access denied.';
  } else {
    // Ensure inventory row exists
    mysqli_query($conn, "INSERT IGNORE INTO inventory (product_id, stock) VALUES ($pid, 0)");

    if ($delta !== 0) {
      // Apply delta with floor at 0
      $q = "UPDATE inventory SET stock = GREATEST(stock + ($delta), 0) WHERE product_id=$pid";
      if (mysqli_query($conn, $q)) { $msg='Stock updated.'; } else { $err='Update failed.'; }
    } else {
      $err='No change specified.';
    }
  }
}

// Load product stocks
$rows=[];
$q = "SELECT p.id, p.name, p.price, COALESCE(i.stock,0) stock
      FROM products p LEFT JOIN inventory i ON i.product_id=p.id
      WHERE p.supplier_id=$supplierId
      ORDER BY p.id DESC";
$res = mysqli_query($conn, $q);
while($r = mysqli_fetch_assoc($res)) $rows[]=$r;

include '../includes/header_supplier.php';
include '../includes/nav_supplier.php';
?>
<div class="container my-4">
  <div class="glass p-3 mb-3">
    <h3 class="mb-0">Manage Inventory</h3>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
  <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

  <div class="glass p-0">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th style="width:10%">ID</th>
          <th>Product</th>
          <th style="width:15%">Price</th>
          <th style="width:12%">Stock</th>
          <th style="width:28%">Adjust</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($rows as $r): ?>
          <tr>
            <td><?=$r['id']?></td>
            <td><?=htmlspecialchars($r['name'])?></td>
            <td>₹<?=number_format((float)$r['price'],2)?></td>
            <td><span class="badge <?=($r['stock']>0?'text-bg-success':'text-bg-danger')?>"><?=$r['stock']?></span></td>
            <td>
              <form method="post" class="d-flex gap-2 align-items-center">
                <input type="hidden" name="update_stock" value="1">
                <input type="hidden" name="product_id" value="<?=$r['id']?>">
                <div class="input-group" style="max-width:260px;">
                  <span class="input-group-text">±</span>
                  <input name="delta" type="number" class="form-control" placeholder="e.g. 5 or -3">
                  <button class="btn btn-brand" type="submit">Apply</button>
                </div>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($rows)): ?>
          <tr><td colspan="5" class="text-center py-4">No products yet. Add from Products page.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>

  <div class="d-flex gap-2 mt-3">
    <a class="btn btn-outline-secondary" href="my_products.php">Back to Products</a>
    <a class="btn btn-brand" href="dashboard.php">Supplier Dashboard</a>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
