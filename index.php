<?php
require_once __DIR__.'/config/db.php';
require_once __DIR__.'/includes/auth.php';

// For a public landing, reuse user header theme for consistency
include __DIR__.'/includes/header_user.php';
?>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="index.php">CycleStore</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navHome">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navHome" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="user/catalog.php">Catalog</a></li>
        <li class="nav-item"><a class="nav-link" href="user/login.php">User Login</a></li>
        <li class="nav-item"><a class="nav-link" href="supplier/login.php">Supplier</a></li>
        <li class="nav-item"><a class="nav-link" href="admin/login.php">Admin</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if(isset($_SESSION['user'])): ?>
          <li class="nav-item"><span class="nav-link">Hi, <?=htmlspecialchars($_SESSION['user']['name'])?></span></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>

<style>
.hero {
  position: relative;
  padding: 90px 0 70px;
  color: #0e1726;
}
.hero .glass-hero {
  background: rgba(255,255,255,.78);
  backdrop-filter: blur(14px);
  border: 1px solid rgba(255,255,255,.35);
  border-radius: 18px;
  box-shadow: 0 16px 40px rgba(24,90,157,.12);
}
.feature .glass {
  background: rgba(255,255,255,.78);
  border: 1px solid rgba(255,255,255,.35);
  border-radius: 16px;
  box-shadow: 0 12px 30px rgba(24,90,157,.12);
}
</style>

<div class="container hero">
  <div class="row align-items-center g-4">
    <div class="col-lg-7">
      <div class="glass-hero p-4 p-md-5">
        <h1 class="fw-bold mb-2">Find the perfect cycle</h1>
        <p class="text-muted mb-4">Mountain, Road, Hybrid and Kids bikes — discover great rides with fast delivery.</p>
        <div class="d-flex gap-2 flex-wrap">
          <a class="btn btn-brand btn-lg" href="user/catalog.php">Browse Catalog</a>
          <a class="btn btn-outline-secondary btn-lg" href="user/login.php">Sign in</a>
        </div>
        <div class="d-flex gap-2 flex-wrap mt-3">
          <a class="btn btn-outline-secondary" href="supplier/login.php">Supplier Portal</a>
          <a class="btn btn-outline-secondary" href="admin/login.php">Admin Panel</a>
        </div>
      </div>
    </div>
    <div class="col-lg-5">
      <div class="glass-hero p-3">
        <img src="https://www.bikes4sale.in/pictures/default/being-human-bh12/being-human-bh12-pic-11.jpg" class="w-100" style="height:420px;object-fit:cover;border-radius:18px;">
      </div>
    </div>
  </div>
</div>

<div class="container my-4 feature">
  <div class="row g-3">
    <div class="col-md-4">
      <div class="glass p-4 h-100">
        <h5 class="mb-2">Curated Selection</h5>
        <p class="text-muted mb-0">Popular brands and categories, kept in stock and ready to ship.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="glass p-4 h-100">
        <h5 class="mb-2">Secure Checkout</h5>
        <p class="text-muted mb-0">Simple checkout with UPI/Card and Cash on Delivery support.</p>
      </div>
    </div>
    <div class="col-md-4">
      <div class="glass p-4 h-100">
        <h5 class="mb-2">Supplier Network</h5>
        <p class="text-muted mb-0">Suppliers manage inventory and fulfill purchase orders seamlessly.</p>
      </div>
    </div>
  </div>
</div>

<?php include __DIR__.'/includes/footer.php'; ?>
