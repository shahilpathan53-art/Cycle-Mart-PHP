<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "cycle_store";

$conn = new mysqli($host, $user, $pass, $dbname);
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$gst_percent = 18;

$sql = "
SELECT 
  o.id AS order_id,
  u.name AS customer_name,
  p.name AS product_name,
  oi.price,
  oi.quantity,
  (oi.price * oi.quantity) AS total_price,
  o.status
FROM order_items oi
JOIN orders o ON oi.order_id = o.id
JOIN products p ON oi.product_id = p.id
JOIN users u ON o.user_id = u.id
ORDER BY o.id DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin Report - Cycle Booking</title>
  <style>
 /* RESET & BASE */
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

body {
  font-family: 'Poppins', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
  background: #f9faff;
  color: #222;
  padding: 30px 20px;
  font-size: 15px;
  line-height: 1.6;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
}

/* CONTAINER */
.container {
  max-width: 1200px;
  margin: 0 auto;
  background: #fff;
  padding: 30px 40px;
  border-radius: 12px;
  box-shadow: 0 8px 24px rgba(0, 0, 0, 0.1);
}

/* HEADER */
h1 {
  text-align: center;
  font-size: 28px;
  font-weight: 700;
  margin-bottom: 40px;
  text-transform: uppercase;
  letter-spacing: 2px;
  color: #333;
  border-bottom: 3px solid #4a90e2;
  padding-bottom: 12px;
  font-family: 'Poppins', sans-serif;
}

/* TABLE WRAPPER */
.table-wrapper {
  overflow-x: auto;
  border-radius: 12px;
  box-shadow: 0 6px 18px rgba(74, 144, 226, 0.1);
}

/* TABLE STYLES */
table {
  width: 100%;
  border-collapse: separate;
  border-spacing: 0 8px;
  font-family: 'Roboto Mono', monospace;
  font-weight: 500;
  color: #444;
  min-width: 800px;
}

thead th {
  background-color: #4a90e2;
  color: #fff;
  padding: 14px 20px;
  font-weight: 600;
  font-size: 15px;
  text-align: left;
  letter-spacing: 0.03em;
  border-top-left-radius: 12px;
  border-top-right-radius: 12px;
  user-select: none;
}

tbody tr {
  background: #fff;
  transition: background-color 0.3s ease;
  border-radius: 8px;
  box-shadow: 0 1px 4px rgba(0,0,0,0.05);
}

tbody tr:hover {
  background-color: #eaf2ff;
}

tbody td {
  padding: 14px 20px;
  vertical-align: middle;
  font-family: 'Roboto Mono', monospace;
  white-space: nowrap;
  border-bottom: 1px solid #f0f0f5;
  font-size: 14px;
  color: #555;
}

/* Numeric alignment */
tbody td:nth-child(4),
tbody td:nth-child(5),
tbody td:nth-child(6),
tbody td:nth-child(7),
tbody td:nth-child(8),
tbody td:nth-child(9) {
  text-align: right;
  font-variant-numeric: tabular-nums;
}

/* STATUS STYLES */
.status {
  display: inline-block;
  font-weight: 600;
  padding: 6px 14px;
  border-radius: 20px;
  font-size: 13px;
  border: 1.5px solid transparent;
  min-width: 90px;
  text-align: center;
  text-transform: capitalize;
  letter-spacing: 0.05em;
  user-select: none;
  transition: all 0.3s ease;
}

.status.Pending {
  background-color: #fff8e1;
  color: #c47f00;
  border-color: #ffecb3;
}

.status.Completed {
  background-color: #d9f7be;
  color: #237804;
  border-color: #b7eb8f;
}

.status.Cancelled {
  background-color: #ffd6d9;
  color: #a8071a;
  border-color: #ffa39e;
}

.status.Shipped {
  background-color: #bae7ff;
  color: #0050b3;
  border-color: #91d5ff;
}

.status.Confirmed {
  background-color: #f0f0f0;
  color: #595959;
  border-color: #d9d9d9;
}

.status.Delivered {
  background-color: #91d5ff;
  color: #003a8c;
  border-color: #69c0ff;
}

/* RESPONSIVE TABLE */
@media (max-width: 768px) {
  table, thead, tbody, th, td, tr {
    display: block;
    width: 100%;
  }

  thead {
    display: none;
  }

  tbody tr {
    margin-bottom: 25px;
    background: #fff;
    border-radius: 12px;
    box-shadow: 0 4px 16px rgba(74, 144, 226, 0.1);
    padding: 15px 20px;
  }

  tbody td {
    position: relative;
    padding-left: 50%;
    text-align: left;
    border-bottom: none;
    white-space: normal;
    font-size: 14px;
    color: #333;
  }

  tbody td::before {
    content: attr(data-label);
    position: absolute;
    left: 20px;
    top: 15px;
    font-weight: 600;
    color: #4a90e2;
    text-transform: uppercase;
    font-size: 13px;
    letter-spacing: 0.05em;
    white-space: nowrap;
  }

  tbody td:nth-child(4),
  tbody td:nth-child(5),
  tbody td:nth-child(6),
  tbody td:nth-child(7),
  tbody td:nth-child(8),
  tbody td:nth-child(9) {
    text-align: left;
  }
}

  </style>
</head>
<body>
  <div class="container">
    <h1>REPORTS</h1>
    <div class="table-wrapper">
      <table>
        <thead>
          <tr>
            <th>Order ID</th>
            <th>Customer</th>
            <th>Product</th>
            <th>Price (₹)</th>
            <th>Qty</th>
            <th>Total (₹)</th>
            <th>GST %</th>
            <th>GST Amount (₹)</th>
            <th>Total Cost (₹)</th>
            <th>Status</th>
          </tr>
        </thead>
        <tbody>
          <?php 
          if ($result && $result->num_rows > 0) {
              while($row = $result->fetch_assoc()) {
                  $total_price = $row['total_price'];
                  $gst_amount  = ($total_price * $gst_percent) / 100;
                  $grand_total = $total_price + $gst_amount;
                  ?>
                  <tr>
                    <td data-label="Order ID"><?php echo $row['order_id']; ?></td>
                    <td data-label="Customer"><?php echo $row['customer_name']; ?></td>
                    <td data-label="Product"><?php echo $row['product_name']; ?></td>
                    <td data-label="Price (₹)"><?php echo number_format($row['price'],2); ?></td>
                    <td data-label="Qty"><?php echo $row['quantity']; ?></td>
                    <td data-label="Total (₹)"><?php echo number_format($total_price,2); ?></td>
                    <td data-label="GST %"><?php echo $gst_percent; ?>%</td>
                    <td data-label="GST Amt (₹)"><?php echo number_format($gst_amount,2); ?></td>
                    <td data-label="Total Cost (₹)"><strong><?php echo number_format($grand_total,2); ?></strong></td>
                    <td data-label="Status">
                      <span class="status <?php echo ucfirst($row['status']); ?>">
                        <?php echo ucfirst($row['status']); ?>
                      </span>
                    </td>
                  </tr>
              <?php }
          } else {
              echo "<tr><td colspan='10'>No records found</td></tr>";
          } ?>
        </tbody>
      </table>
    </div>
  </div>
</body>
</html>
<?php
$conn->close();
?>
