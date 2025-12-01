<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

$err=''; $msg='';

// Load categories and suppliers for form selects
$cats=[]; $suppliers=[];
$rc = mysqli_query($conn, "SELECT id,name FROM categories ORDER BY name");
while($r=mysqli_fetch_assoc($rc)) $cats[]=$r;
$rs = mysqli_query($conn, "SELECT id,company_name FROM suppliers ORDER BY company_name");
while($r=mysqli_fetch_assoc($rs)) $suppliers[]=$r;

// Add product
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add'])) {
  $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
  $category_id = (int)($_POST['category_id'] ?? 0);
  $supplier_id = (int)($_POST['supplier_id'] ?? 0);
  $brand = mysqli_real_escape_string($conn, trim($_POST['brand'] ?? ''));
  $price = (float)($_POST['price'] ?? 0);
  $thumbnail = mysqli_real_escape_string($conn, trim($_POST['thumbnail'] ?? ''));
  $desc = mysqli_real_escape_string($conn, trim($_POST['description'] ?? ''));
  $active = isset($_POST['is_active']) ? 1 : 0;

  if ($name==='' || $category_id<=0 || $supplier_id<=0 || $price<=0) {
    $err = 'Fill required fields (Name, Category, Supplier, Price).';
  } else {
    $slug = strtolower(preg_replace('/[^a-z0-9]+/','-', $name)).'-'.substr(md5(uniqid()),0,5);
    $thumb = $thumbnail !== '' ? $thumbnail : '/assets/img/placeholder.jpg';
    $ok = mysqli_query($conn, "INSERT INTO products (supplier_id,category_id,name,slug,description,brand,price,thumbnail,is_active)
                               VALUES ($supplier_id,$category_id,'$name','$slug','$desc','$brand',$price,'$thumb',$active)");
    if ($ok) {
      $pid = mysqli_insert_id($conn);
      mysqli_query($conn, "INSERT IGNORE INTO inventory (product_id,stock) VALUES ($pid,0)");
      $msg='Product added.';
    } else { $err='Insert failed.'; }
  }
}

// Toggle active
if (isset($_GET['toggle'])) {
  $pid=(int)$_GET['toggle'];
  mysqli_query($conn, "UPDATE products SET is_active=1-is_active WHERE id=$pid");
  header('Location: products.php'); exit;
}

// Delete
if (isset($_GET['del'])) {
  $pid=(int)$_GET['del'];
  mysqli_query($conn, "DELETE FROM products WHERE id=$pid");
  header('Location: products.php'); exit;
}

// List products
$prods=[];
$q = "SELECT p.id,p.name,p.brand,p.price,p.is_active,COALESCE(i.stock,0) stock,c.name cat,s.company_name sup
      FROM products p
      LEFT JOIN inventory i ON i.product_id=p.id
      LEFT JOIN categories c ON c.id=p.category_id
      LEFT JOIN suppliers s ON s.id=p.supplier_id
      ORDER BY p.id DESC";
$res = mysqli_query($conn, $q);
while($r=mysqli_fetch_assoc($res)) $prods[]=$r;

include '../includes/header_admin.php';
include '../includes/nav_admin.php';
?>
<div class="container my-4">
  <div class="glass p-3 mb-3 d-flex justify-content-between align-items-center">
    <h3 class="mb-0">Products</h3>
    <a class="btn btn-outline-secondary" href="categories.php">Manage Categories</a>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
  <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

  <div class="glass p-3 mb-3">
    <h5 class="mb-3">Add Product</h5>
    <form method="post" class="row g-2">
      <input type="hidden" name="add" value="1">
      <div class="col-md-4">
        <label class="form-label">Name</label>
        <input name="name" class="form-control" required>
      </div>
      <div class="col-md-2">
        <label class="form-label">Category</label>
        <select name="category_id" class="form-select" required>
          <option value="">Select</option>
          <?php foreach($cats as $c): ?>
            <option value="<?=$c['id']?>"><?=htmlspecialchars($c['name'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Supplier</label>
        <select name="supplier_id" class="form-select" required>
          <option value="">Select</option>
          <?php foreach($suppliers as $s): ?>
            <option value="<?=$s['id']?>"><?=htmlspecialchars($s['company_name'])?></option>
          <?php endforeach; ?>
        </select>
      </div>
      <div class="col-md-3">
        <label class="form-label">Brand</label>
        <input name="brand" class="form-control">
      </div>
      <div class="col-md-2">
        <label class="form-label">Price (₹)</label>
        <input name="price" type="number" step="0.01" class="form-control" required>
      </div>
      <div class="col-md-5">
        <label class="form-label">Thumbnail URL</label>
        <input name="thumbnail" class="form-control" placeholder="uploads/products/placeholder.jpg">
      </div>
      <div class="col-md-12">
        <label class="form-label">Description</label>
        <textarea name="description" rows="2" class="form-control"></textarea>
      </div>
      <div class="col-md-2 form-check ms-2">
        <input class="form-check-input" type="checkbox" name="is_active" id="pactive" checked>
        <label for="pactive" class="form-check-label">Active</label>
      </div>
      <div class="col-12">
        <button class="btn btn-brand">Save</button>
      </div>
    </form>
  </div>

  <div class="glass p-0">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th style="width:8%">ID</th>
          <th>Name</th>
          <th style="width:12%">Brand</th>
          <th style="width:10%">Category</th>
          <th style="width:14%">Supplier</th>
          <th style="width:10%">Price</th>
          <th style="width:10%">Stock</th>
          <th style="width:12%">Active</th>
          <th style="width:18%">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($prods as $p): ?>
          <tr>
            <td><?=$p['id']?></td>
            <td><?=htmlspecialchars($p['name'])?></td>
            <td><?=htmlspecialchars($p['brand'])?></td>
            <td><?=htmlspecialchars($p['cat'])?></td>
            <td><?=htmlspecialchars($p['sup'])?></td>
            <td>₹<?=number_format((float)$p['price'],2)?></td>
            <td><span class="badge <?=($p['stock']>0?'text-bg-success':'text-bg-danger')?>"><?=$p['stock']?></span></td>
            <td><span class="badge <?=($p['is_active']?'text-bg-success':'text-bg-secondary')?>"><?=$p['is_active']?'Yes':'No'?></span></td>
            <td class="d-flex gap-2">
              <a class="btn btn-sm btn-outline-secondary" href="products.php?toggle=<?=$p['id']?>">Toggle</a>
              <a class="btn btn-sm btn-outline-danger" href="products.php?del=<?=$p['id']?>" onclick="return confirm('Delete this product?');">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($prods)): ?>
          <tr><td colspan="9" class="text-center py-4">No products found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
