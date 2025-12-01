<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

if (session_status() === PHP_SESSION_NONE) session_start();

$role = $_SESSION['user']['role'] ?? '';
if ($role !== 'supplier') {
    http_response_code(403);
    die('Access denied');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?: null;
    $amount = $_POST['amount'] ?? null;

    if (!$amount || !is_numeric($amount)) {
        $error = "Please enter a valid amount.";
    } else {
        $supplier_id = $_SESSION['user']['id'];
        $created_at = date('Y-m-d H:i:s');

        if ($order_id === null || $order_id === '') {
            $order_id = null;
            $stmt = $conn->prepare("INSERT INTO bills (order_id, amount, source, supplier_id, created_at) VALUES (?, ?, 'supplier', ?, ?)");
            $stmt->bind_param("diss", $order_id, $amount, $supplier_id, $created_at);
        } else {
            $order_id = (int)$order_id;
            $stmt = $conn->prepare("INSERT INTO bills (order_id, amount, source, supplier_id, created_at) VALUES (?, ?, 'supplier', ?, ?)");
            $stmt->bind_param("idis", $order_id, $amount, $supplier_id, $created_at);
        }

        if ($stmt->execute()) {
            $success = "Bill inserted successfully!";
        } else {
            $error = "Error inserting bill: " . $stmt->error;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Supplier - Add Bill</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    
    <!-- Bootstrap 5 -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Custom CSS with dark mode support -->
    <style>
        :root {
            --bg-color: #f8f9fa;
            --text-color: #333;
            --card-bg: #fff;
            --input-border: #ccc;
            --btn-primary: #0d6efd;
            --btn-primary-hover: #0b5ed7;
            --btn-secondary: #6c757d;
            --alert-success-bg: #d1e7dd;
            --alert-success-text: #0f5132;
            --alert-success-border: #badbcc;
            --alert-danger-bg: #f8d7da;
            --alert-danger-text: #842029;
            --alert-danger-border: #f5c2c7;
        }

        @media (prefers-color-scheme: dark) {
            :root {
                --bg-color: #1e1e1e;
                --text-color: #f0f0f0;
                --card-bg: #2c2c2c;
                --input-border: #555;
                --btn-primary: #0d6efd;
                --btn-primary-hover: #0a58ca;
                --btn-secondary: #adb5bd;
                --alert-success-bg: #234d36;
                --alert-success-text: #c9f7dc;
                --alert-success-border: #2d6a4f;
                --alert-danger-bg: #5a1a1a;
                --alert-danger-text: #f8d7da;
                --alert-danger-border: #842029;
            }
        }

        body {
            background-color: var(--bg-color);
            color: var(--text-color);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .container {
            background: var(--card-bg);
            padding: 2rem 2.5rem;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
            max-width: 600px;
            margin: 40px auto;
        }

        h2 {
            font-weight: 600;
            margin-bottom: 1.5rem;
            text-align: center;
            color: var(--btn-primary);
        }

        label.form-label {
            font-weight: 500;
        }

        input.form-control {
            border-radius: 6px;
            border: 1px solid var(--input-border);
            background-color: var(--card-bg);
            color: var(--text-color);
        }

        input.form-control:focus {
            border-color: var(--btn-primary);
            box-shadow: none;
        }

        .btn-primary {
            background-color: var(--btn-primary);
            border-color: var(--btn-primary);
            font-weight: 500;
        }

        .btn-primary:hover {
            background-color: var(--btn-primary-hover);
            border-color: var(--btn-primary-hover);
        }

        .btn-secondary {
            background-color: var(--btn-secondary);
            border-color: var(--btn-secondary);
        }

        .alert {
            border-radius: 6px;
            padding: 0.75rem 1rem;
        }

        .alert-success {
            background-color: var(--alert-success-bg);
            color: var(--alert-success-text);
            border: 1px solid var(--alert-success-border);
        }

        .alert-danger {
            background-color: var(--alert-danger-bg);
            color: var(--alert-danger-text);
            border: 1px solid var(--alert-danger-border);
        }

        form .mb-3 {
            margin-bottom: 1.25rem;
        }

        a.btn {
            font-size: 0.95rem;
        }
    </style>
</head>
<body>
<div class="container">
    <h2>Add Bill</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php endif; ?>

    <form method="post">
        <div class="mb-3">
            <label for="order_id" class="form-label">Order ID</label>
            <input type="number" class="form-control" id="order_id" name="order_id" placeholder="Order ID">
        </div>
        <div class="mb-3">
            <label for="name" class="form-label">Name</label>
            <input type="text" class="form-control" id="name" name="name" placeholder="Name">
        </div>
        <div class="mb-3">
            <label for="amount" class="form-label">Amount (₹)</label>
            <input type="number" step="0.01" class="form-control" id="amount" name="amount" required>
        </div>
        <button type="submit" class="btn btn-primary">Add Bill</button>
    </form>

    <a href="dashboard.php" class="btn btn-secondary mt-3">← Back to Dashboard</a>
</div>
</body>
</html>
