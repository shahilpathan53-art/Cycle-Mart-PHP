<?php require_once __DIR__.'/auth.php'; $u=user(); ?>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">Supplier Panel</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navS"><span class="navbar-toggler-icon"></span></button>
    <div id="navS" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="my_products.php">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="inventory.php">Inventory</a></li>
        <li class="nav-item"><a class="nav-link" href="po_fulfill.php">Purchase Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="bill.php">Bill</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if($u): ?>
          <li class="nav-item"><span class="nav-link">Hi, <?=htmlspecialchars($u['name'])?></span></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
