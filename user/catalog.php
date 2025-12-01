<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$u = user(); // check login user

// Load active products with stock
$items=[];
$q = "SELECT p.id, p.name, p.brand, p.price, p.thumbnail, c.name AS category,
             COALESCE(i.stock,0) AS stock
      FROM products p
      JOIN categories c ON c.id=p.category_id
      LEFT JOIN inventory i ON i.product_id=p.id
      WHERE p.is_active=1
      ORDER BY p.id DESC";
$res = mysqli_query($conn, $q);
while($r=mysqli_fetch_assoc($res)) $items[]=$r;

include '../includes/header_user.php';
include '../includes/nav_user.php';
?>
<div class="container my-4">
  <div class="glass p-3 mb-3">
    <h3 class="mb-0">Catalog</h3>
    <div class="text-muted">Browse products</div>
  </div>

  <div class="row g-3">
    <?php foreach($items as $p): ?>
      <div class="col-md-4 col-lg-3">
        <div class="glass p-2 h-100">
          <div class="ratio ratio-4x3 mb-2">
            <img src="<?=htmlspecialchars($p['thumbnail'] ?: '/cyclestore/uploads/placeholder.jpg')?>"
                 alt="<?=htmlspecialchars($p['name'])?>"
                 style="object-fit:cover;border-radius:10px;">
          </div>
          <div class="small text-muted"><?=htmlspecialchars($p['category'])?></div>
          <div class="fw-semibold"><?=htmlspecialchars($p['name'])?></div>
          <div class="text-muted"><?=htmlspecialchars($p['brand'])?></div>
          <div class="d-flex justify-content-between align-items-center mt-2">
            <div class="fw-bold">₹<?=number_format((float)$p['price'],2)?></div>
            <span class="badge <?=($p['stock']>0?'text-bg-success':'text-bg-danger')?>">
              <?= $p['stock']>0?'In stock':'Out' ?>
            </span>
          </div>
          <div class="mt-2 d-flex gap-2">
            <?php if($u): ?>
              <a class="btn btn-outline-secondary btn-sm" href="product.php?id=<?=$p['id']?>">Details</a>
              <a class="btn btn-brand btn-sm" href="cart.php?action=add&id=<?=$p['id']?>">Add to Cart</a>
      
            <?php else: ?>
              <a class="btn btn-outline-secondary btn-sm" href="login.php">Details</a>
              <a class="btn btn-brand btn-sm" href="login.php">Add to Cart</a>
            <?php endif; ?>
          </div>
        </div>
      </div>
    <?php endforeach; ?>
    <?php if(empty($items)): ?>
      <div class="col-12"><div class="alert alert-info">No products available.</div></div>
    <?php endif; ?>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
