<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$role = $_SESSION['user']['role'] ?? '';
if ($role !== 'admin') {
  http_response_code(403);
  die('Access denied');
}

// Handle filter
$filter = $_GET['source'] ?? 'all';

$whereClause = '';
if ($filter === 'user') {
  $whereClause = "WHERE b.source = 'user'";
} elseif ($filter === 'supplier') {
  $whereClause = "WHERE b.source = 'supplier'";
}

$sql = "
  SELECT 
    b.id AS bill_id,
    b.order_id,
    b.amount,
    b.created_at,
    b.source,
    u.name AS user_name,
    s.company_name AS supplier_name,
    s.gstin AS supplier_gst
  FROM bills b
  LEFT JOIN orders o ON o.id = b.order_id
  LEFT JOIN users u ON u.id = o.user_id
  LEFT JOIN suppliers s ON s.id = b.supplier_id
  $whereClause
  ORDER BY b.id ASC
";

$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html>
<head>
  <title>Admin - All Bills (Users + Suppliers)</title>
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css">
  <style>
   /* ========== Root Variables ========== */
:root {
  --bg: #f2f5f9;
  --text: #222;
  --card-bg: #fff;
  --primary: #6c63ff;
  --primary-light: #7c7bff;
  --primary-dark: #574b90;
  --border: #dfe3e8;
  --badge-bg: #e0f7fa;
  --badge-text: #00796b;
  --hover: #f0f0f0;
}

@media (prefers-color-scheme: dark) {
  :root {
    --bg: #121212;
    --text: #eaeaea;
    --card-bg: #1e1e1e;
    --primary: #9f88ff;
    --primary-light: #b2a4ff;
    --primary-dark: #8573e8;
    --border: #333;
    --badge-bg: #004d40;
    --badge-text: #a7ffeb;
    --hover: #2a2a2a;
  }
}

/* ========== Body ========== */
body {
  background: var(--bg);
  color: var(--text);
  font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  margin: 0;
  padding: 0;
}

/* ========== Container ========== */
.container {
  background-color: var(--card-bg);
  padding: 2rem;
  margin-top: 2rem;
  border-radius: 16px;
  box-shadow: 0 12px 30px rgba(0, 0, 0, 0.05);
  transition: background 0.3s;
}

/* ========== Headings ========== */
h2 {
  font-size: 2rem;
  font-weight: bold;
  text-align: center;
  margin-bottom: 2rem;
  color: var(--primary-dark);
}

/* ========== Form ========== */
form .form-label {
  font-weight: 600;
  color: var(--text);
}

.form-select {
  border-radius: 8px;
  border: 1px solid var(--border);
  padding: 0.6rem;
  background-color: var(--card-bg);
  color: var(--text);
  transition: border 0.2s;
}

.form-select:focus {
  border-color: var(--primary);
  box-shadow: 0 0 0 0.2rem rgba(108, 99, 255, 0.25);
}

/* ========== Table ========== */
.table {
  border-collapse: separate;
  border-spacing: 0 8px;
  width: 100%;
  margin-top: 1rem;
}

.table th {
  background: linear-gradient(135deg, var(--primary), var(--primary-dark));
  color: white;
  padding: 1rem;
  text-align: center;
  border: none;
}

.table td {
  background-color: var(--card-bg);
  border: 1px solid var(--border);
  padding: 0.8rem;
  text-align: center;
  vertical-align: middle;
  color: var(--text);
}

.table tbody tr {
  transition: background-color 0.3s ease;
}

.table tbody tr:hover {
  background-color: var(--hover);
}

/* ========== Badge ========== */
.badge.bg-info {
  background-color: var(--badge-bg) !important;
  color: var(--badge-text) !important;
  font-weight: 500;
  border-radius: 12px;
  padding: 6px 10px;
  font-size: 0.85rem;
}

/* ========== Button ========== */
.btn-secondary {
  background-color: var(--primary);
  border: none;
  color: white;
  font-weight: 500;
  padding: 0.6rem 1.4rem;
  border-radius: 8px;
  transition: all 0.3s ease;
}

.btn-secondary:hover {
  background-color: var(--primary-dark);
  transform: translateY(-1px);
}

/* ========== Responsive ========== */
@media (max-width: 768px) {
  .table th,
  .table td {
    font-size: 0.85rem;
    padding: 0.6rem;
  }

  h2 {
    font-size: 1.6rem;
  }

  .container {
    padding: 1rem;
  }
}
  </style>
</head>
<body>
  <div class="container my-4">
    <h2 class="mb-4">All Bills</h2>

    <!-- Filter Form -->
    <form method="get" class="row g-3 mb-4">
      <div class="col-auto">
        <label for="sourceFilter" class="form-label">Filter by Source:</label>
        <select name="source" id="sourceFilter" class="form-select" onchange="this.form.submit()">
          <option value="all" <?= $filter === 'all' ? 'selected' : '' ?>>All</option>
          <option value="user" <?= $filter === 'user' ? 'selected' : '' ?>>Users</option>
          <option value="supplier" <?= $filter === 'supplier' ? 'selected' : '' ?>>Suppliers</option>
        </select>
      </div>
    </form>

    <table class="table table-bordered table-hover">
      <thead class="table-dark">
        <tr>
          <th>Bill ID</th>
          <th>Order ID</th>
          <th>Source</th>
          <th>Name</th>
          <th>GST No.</th>
          <th>Total (₹)</th>
          <th>GST (18%)</th>
          <th>Total Cost (₹)</th>
          <th>Date</th>
        </tr>
      </thead>
      <tbody>
        <?php if ($res->num_rows === 0): ?>
          <tr><td colspan="9" class="text-center">No bills found.</td></tr>
        <?php else: ?>
          <?php while ($row = $res->fetch_assoc()): ?>
            <?php
              $subtotal = (float)$row['amount'];
              $gst = round($subtotal * 0.18, 2);
              $total = $subtotal + $gst;

              $isSupplier = $row['source'] === 'supplier';
              $name = $isSupplier ? $row['supplier_name'] : $row['user_name'];
              $gstNo = $isSupplier ? $row['supplier_gst'] : 'N/A';
              $sourceLabel = ucfirst($row['source']);
              $formattedDateTime = date('d/m/Y H:i', strtotime($row['created_at']));
            ?>
            <tr>
              <td><?= $row['bill_id'] ?></td>
              <td><?= $row['order_id'] ?: '-' ?></td>
              <td><span class="badge bg-info text-dark"><?= htmlspecialchars($sourceLabel) ?></span></td>
              <td><?= htmlspecialchars($name) ?></td>
              <td><?= htmlspecialchars($gstNo) ?></td>
              <td>₹<?= number_format($subtotal, 2) ?></td>
              <td>₹<?= number_format($gst, 2) ?></td>
              <td><strong>₹<?= number_format($total, 2) ?></strong></td>
              <td><?= $formattedDateTime ?></td>
            </tr>
          <?php endwhile; ?>
        <?php endif; ?>
      </tbody>
    </table>

    <a href="dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
  </div>
</body>
</html>
