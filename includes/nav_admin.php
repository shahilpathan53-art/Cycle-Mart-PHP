<?php require_once __DIR__.'/auth.php'; $u=user(); ?>
<nav class="navbar navbar-expand-lg sticky-top">
  <div class="container">
    <a class="navbar-brand fw-bold" href="dashboard.php">Admin Panel</a>
    <button class="navbar-toggler" data-bs-toggle="collapse" data-bs-target="#navA"><span class="navbar-toggler-icon"></span></button>
    <div id="navA" class="collapse navbar-collapse">
      <ul class="navbar-nav me-auto">
        <li class="nav-item"><a class="nav-link" href="dashboard.php">Dashboard</a></li>
        <li class="nav-item"><a class="nav-link" href="users.php">Users</a></li>
        <li class="nav-item"><a class="nav-link" href="products.php">Products</a></li>
        <li class="nav-item"><a class="nav-link" href="categories.php">Categories</a></li>
        <li class="nav-item"><a class="nav-link" href="orders.php">Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="order_history.php">Order History</a></li>
        <li class="nav-item"><a class="nav-link" href="po_list.php">Supplier Orders</a></li>
        <li class="nav-item"><a class="nav-link" href="po_new.php">Create PO</a></li>
        <li class="nav-item"><a class="nav-link" href="supplier_payments.php">Supplier Payments</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_bills.php">Bills</a></li>
        <li class="nav-item"><a class="nav-link" href="admin_report.php">Report</a></li>
      </ul>
      <ul class="navbar-nav ms-auto">
        <?php if($u): ?>
          <li class="nav-item"><span class="nav-link">Hi, <?=htmlspecialchars($u['name'])?></span></li>
          <li class="nav-item"><a class="nav-link" href="logout.php">Logout</a></li>
        <?php endif; ?>
      </ul>
    </div>
  </div>
</nav>
