<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('user');

$id = (int)($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(400); die('Invalid product'); }

// product + stock
$sql = "SELECT p.id,p.name,p.slug,p.description,p.brand,p.price,p.thumbnail,COALESCE(i.stock,0) stock
        FROM products p
        LEFT JOIN inventory i ON i.product_id=p.id
        WHERE p.id=$id AND p.is_active=1";
$res = mysqli_query($conn, $sql);
$p = mysqli_fetch_assoc($res);
if (!$p) { http_response_code(404); die('Product not found'); }

include '../includes/header_user.php';
include '../includes/nav_user.php';
?>
<div class="container my-4">
  <div class="row g-3">
    <div class="col-md-6">
      <div class="glass p-2">
        <img src="<?=htmlspecialchars($p['thumbnail'] ?: '../uploads\products\placeholder.jpg')?>" 
             class="w-100" style="height:360px;object-fit:cover;border-radius:14px;">
      </div>
    </div>
    <div class="col-md-6">
      <div class="glass p-4 h-100">
        <h3 class="fw-bold mb-1"><?=htmlspecialchars($p['name'])?></h3>
        <div class="text-muted mb-2"><?=htmlspecialchars($p['brand'] ?? '')?></div>
        <div class="mb-3">₹<?=number_format((float)$p['price'],2)?></div>
        <div class="mb-3">
          <span class="badge <?=($p['stock']>0?'text-bg-success':'text-bg-danger')?>"><?= $p['stock']>0 ? 'In stock' : 'Out of stock' ?></span>
        </div>
        <p class="text-muted"><?=nl2br(htmlspecialchars($p['description'] ?? ''))?></p>
        <div class="mt-3">
          <?php if($p['stock']>0): ?>
            <a class="btn btn-brand" href="cart.php?action=add&id=<?=$p['id']?>">Add to Cart</a>
          <?php else: ?>
            <button class="btn btn-secondary" disabled>Unavailable</button>
          <?php endif; ?>
          <a class="btn btn-outline-secondary ms-2" href="catalog.php">Back to Catalog</a>
        </div>
      </div>
    </div>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
