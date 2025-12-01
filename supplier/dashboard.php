<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('supplier');

$u = user();

// Supplier ID resolve from users->suppliers
$sidRes = mysqli_query($conn, "SELECT id, company_name FROM suppliers WHERE user_id=".$u['id']." LIMIT 1");
$supplier = mysqli_fetch_assoc($sidRes);
$supplierId = $supplier['id'] ?? 0;

// Counts
$totalProducts = 0; $totalStock = 0;
if ($supplierId) {
  $cp = mysqli_query($conn, "SELECT COUNT(*) c FROM products WHERE supplier_id=".$supplierId);
  $totalProducts = (int)mysqli_fetch_assoc($cp)['c'];

  $cs = mysqli_query($conn, "SELECT COALESCE(SUM(i.stock),0) s FROM products p LEFT JOIN inventory i ON i.product_id=p.id WHERE p.supplier_id=".$supplierId);
  $totalStock = (int)mysqli_fetch_assoc($cs)['s'];
}

// Recent products
$recent = [];
if ($supplierId) {
  $rp = mysqli_query($conn, "SELECT p.id,p.name,COALESCE(i.stock,0) stock, p.price FROM products p LEFT JOIN inventory i ON i.product_id=p.id WHERE p.supplier_id=".$supplierId." ORDER BY p.id DESC LIMIT 8");
  while($row = mysqli_fetch_assoc($rp)) $recent[] = $row;
}

include '../includes/header_supplier.php';
include '../includes/nav_supplier.php';
?>
<div class="container my-4">
  <div class="row g-3">
    <div class="col-12">
      <div class="glass p-4">
        <h3 class="mb-1">Welcome, <?=htmlspecialchars($u['name'])?> 👋</h3>
        <p class="text-muted mb-0"><?=htmlspecialchars($supplier['company_name'] ?? 'Set company in profile')?> — Supplier Dashboard</p>
      </div>
    </div>

    <div class="col-md-4">
      <div class="glass p-4 h-100">
        <div class="text-muted">Total Products</div>
        <div class="display-6"><?=$totalProducts?></div>
        <a class="btn btn-brand mt-2" href="my_products.php">Manage Products</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="glass p-4 h-100">
        <div class="text-muted">Total Stock</div>
        <div class="display-6"><?=$totalStock?></div>
        <a class="btn btn-brand mt-2" href="inventory.php">Update Inventory</a>
      </div>
    </div>

    <div class="col-md-4">
      <div class="glass p-4 h-100">
        <div class="text-muted">Purchase Orders</div>
        <p class="mb-2">Fulfill admin purchase orders assigned to you.</p>
        <a class="btn btn-brand" href="po_fulfill.php">Open PO Center</a>
      </div>
    </div>

    <div class="col-12">
      <div class="glass p-3">
        <h5 class="mb-3">Recent Products</h5>
        <div class="table-responsive">
          <table class="table align-middle mb-0">
            <thead>
              <tr><th style="width:10%">ID</th><th>Name</th><th style="width:15%">Stock</th><th style="width:15%">Price</th></tr>
            </thead>
            <tbody>
              <?php foreach($recent as $p): ?>
                <tr>
                  <td><?=$p['id']?></td>
                  <td><?=htmlspecialchars($p['name'])?></td>
                  <td><?=$p['stock']?></td>
                  <td>₹<?=number_format((float)$p['price'],2)?></td>
                </tr>
              <?php endforeach; ?>
              <?php if(empty($recent)): ?>
                <tr><td colspan="4" class="text-center py-3">No products yet. Add your first product.</td></tr>
              <?php endif; ?>
            </tbody>
          </table>
        </div>
        <div class="mt-3 d-flex gap-2">
          <a class="btn btn-outline-secondary" href="my_products.php">View All</a>
          <a class="btn btn-brand" href="my_products.php#add">Add Product</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
