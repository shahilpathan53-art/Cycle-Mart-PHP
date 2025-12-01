<?php
require_once __DIR__ . '/auth.php';
$u = user(); // ab $u hamesha define rahega
?>
<nav class="navbar navbar-expand-lg sticky-top navbar-light bg-light shadow-sm">
  <div class="container">
    <a class="navbar-brand fw-bold text-primary" href="dashboard.php">CycleStore</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navU">
      <span class="navbar-toggler-icon"></span>
    </button>
    <div id="navU" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="catalog.php">Catalog</a></li>
        <?php if($u): ?>
          <li class="nav-item"><a class="nav-link" href="cart.php">Cart</a></li>
          <li class="nav-item"><a class="nav-link" href="order_history.php">Orders</a></li>
        <?php endif; ?>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if($u): ?>
          <li class="nav-item">
            <span class="nav-link">Hi, <?=htmlspecialchars($u['name'])?></span>
          </li>
          <li class="nav-item">
            <a class="nav-link text-danger" href="logout.php">Logout</a>
          </li>
        <?php else: ?>
          <li class="nav-item"><a class="nav-link text-primary" href="login.php">Login</a></li>
          <li class="nav-item"><a class="nav-link text-success" href="register.php">Register</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
