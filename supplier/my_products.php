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

// Add product
$err=''; $msg='';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add_product'])) {
  $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
  $category_id = (int)($_POST['category_id'] ?? 0);
  $brand = mysqli_real_escape_string($conn, trim($_POST['brand'] ?? ''));
  $price = (float)($_POST['price'] ?? 0);
  $stock = (int)($_POST['stock'] ?? 0);
  $thumbnail = mysqli_real_escape_string($conn, trim($_POST['thumbnail'] ?? ''));
  $desc = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));
  if ($name==='' || $price<=0 || $category_id<=0) {
    $err='Please fill mandatory fields (Name, Category, Price).';
  } else {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/','-', $name)).'-'.substr(md5(uniqid()),0,5);
    $thumb = $thumbnail !== '' ? $thumbnail : '/assets/img/placeholder.jpg';
    $ok = mysqli_query($conn, "INSERT INTO products (supplier_id,category_id,name,slug,description,brand,price,thumbnail,is_active)
                               VALUES ($supplierId,$category_id,'$name','$slug','$desc','$brand',$price,'$thumb',1)");
    if ($ok) {
      $pid = mysqli_insert_id($conn);
      // Ensure inventory record
      $iv = mysqli_query($conn, "INSERT INTO inventory (product_id,stock) VALUES ($pid,$stock)");
      $msg='Product added successfully.';
    } else {
      $err='Failed to add product.';
    }
  }
}

// Load categories
$cats=[]; $cq = mysqli_query($conn,"SELECT id,name FROM categories ORDER BY name");
while($row=mysqli_fetch_assoc($cq)) $cats[]=$row;

// Load products list
$prods=[];
$q = "SELECT p.id,p.name,p.price,COALESCE(i.stock,0) stock
      FROM products p LEFT JOIN inventory i ON i.product_id=p.id
      WHERE p.supplier_id=$supplierId ORDER BY p.id DESC";
$res = mysqli_query($conn, $q);
while($row=mysqli_fetch_assoc($res)) $prods[]=$row;

include '../includes/header_supplier.php';
include '../includes/nav_supplier.php';
?>
<div class="container my-4">
  <div class="glass p-4 mb-3" id="add">
    <h4 class="mb-3">Add Product</h4>
    <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
    <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>
    <form method="post" class="row g-2">
      <input type="hidden" name="add_product" value="1">
      <div class="col-md-5">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
          <option value="">Select</option>
          <?php foreach($cats as $c): ?>
            <option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-2">
        <label class="form-label">Brand</label>
        <input name="brand" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">Price (₹)</label>
        <input name="price" type="number" step="0.01" class="form-control" required>
      </div>
      <div class="col-md-3">
        <label class="form-label">Initial Stock</label>
        <input name="stock" type="number" class="form-control" value="0">
      </div>
      <div class="col-md-5">
        <label class="form-label">Thumbnail URL</label>
        <input name="thumbnail" class="form-control" placeholder="/assets/img/placeholder.jpg">
      </div>
      <div class="col-md-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="3" class="form-control" placeholder="Short description"></textarea>
      </div>
      <div class="col-12">
        <button class="btn btn-brand">Save Product</button>
      </div>
    </form>
  </div>

  <div class="glass p-3">
    <h5 class="mb-3">Your Products</h5>
    <div class="table-responsive">
      <table class="table align-middle mb-0">
        <thead><tr><th style="width:10%">ID</th><th>Name</th><th style="width:15%">Price</th><th style="width:15%">Stock</th></tr></thead>
        <tbody>
          <?php foreach($prods as $p): ?>
            <tr>
              <td><?=$p['id']?></td>
              <td><?=htmlspecialchars($p['name'])?></td>
              <td>₹<?=number_format((float)$p['price'],2)?></td>
              <td><?=$p['stock']?></td>
            </tr>
          <?php endforeach; ?>
          <?php if(empty($prods)): ?>
            <tr><td colspan="4" class="text-center py-3">No products yet.</td></tr>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
