<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

// Load POs list with supplier name
$pos = [];
$q = "SELECT po.id, s.company_name, po.status, po.total, po.created_at
      FROM purchase_orders po
      JOIN suppliers s ON s.id=po.supplier_id
      ORDER BY po.id DESC";
$res = mysqli_query($conn, $q);
while($row=mysqli_fetch_assoc($res)) $pos[]=$row;

include '../includes/header_admin.php';
include '../includes/nav_admin.php';
?>
<div class="container my-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <div class="glass p-3"><h3 class="mb-0">Supplier Purchase Orders</h3></div>
    <div><a class="btn btn-brand" href="po_new.php">Create New PO</a></div>
  </div>

  <div class="glass p-0">
    <table class="table table-hover align-middle mb-0">
      <thead>
        <tr>
          <th style="width:10%">#</th>
          <th>Supplier</th>
          <th style="width:15%">Status</th>
          <th style="width:15%">Total</th>
          <th style="width:20%">Created</th>
          <th style="width:20%">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($pos as $po): ?>
          <tr>
            <td>#<?=$po['id']?></td>
            <td><?=htmlspecialchars($po['company_name'])?></td>
            <td>
              <?php
                $badge='text-bg-secondary';
                if($po['status']==='ordered') $badge='text-bg-warning';
                if($po['status']==='part_received') $badge='text-bg-info';
                if($po['status']==='received') $badge='text-bg-success';
                if($po['status']==='cancelled') $badge='text-bg-dark';
              ?>
              <span class="badge <?=$badge?>"><?=htmlspecialchars($po['status'])?></span>
            </td>
            <td>₹<?=number_format((float)$po['total'],2)?></td>
            <td><?=htmlspecialchars($po['created_at'])?></td>
            <td class="d-flex gap-2">
              <a class="btn btn-sm btn-outline-secondary" href="po_view.php?id=<?=$po['id']?>">View</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($pos)): ?>
          <tr><td colspan="6" class="text-center py-4">No purchase orders yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
