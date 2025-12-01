<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

$err=''; $msg='';

// Create
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['add'])) {
  $name = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
  if ($name===''){ $err='Category name required.'; }
  else {
    $ok = mysqli_query($conn, "INSERT INTO categories (name) VALUES ('$name')");
    if ($ok){ $msg='Category added.'; } else { $err='Insert failed.'; }
  }
}

// Delete
if (isset($_GET['del'])) {
  $id=(int)$_GET['del'];
  mysqli_query($conn, "DELETE FROM categories WHERE id=$id");
  header('Location: categories.php'); exit;
}

// List
$cats=[];
$res = mysqli_query($conn, "SELECT id,name FROM categories ORDER BY name");
while($r=mysqli_fetch_assoc($res)) $cats[]=$r;

include '../includes/header_admin.php';
include '../includes/nav_admin.php';
?>
<div class="container my-4">
  <div class="glass p-3 mb-3 d-flex align-items-center justify-content-between">
    <h3 class="mb-0">Categories</h3>
    <a class="btn btn-outline-secondary" href="products.php">Manage Products</a>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
  <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

  <div class="glass p-3 mb-3">
    <form method="post" class="row g-2">
      <input type="hidden" name="add" value="1">
      <div class="col-md-6">
        <label class="form-label">New Category</label>
        <input name="name" class="form-control" placeholder="e.g. Mountain Bikes" required>
      </div>
      <div class="col-md-2 align-self-end">
        <button class="btn btn-brand w-100">Add</button>
      </div>
    </form>
  </div>

  <div class="glass p-0">
    <table class="table align-middle mb-0">
      <thead>
        <tr><th style="width:12%">ID</th><th>Name</th><th style="width:15%">Actions</th></tr>
      </thead>
      <tbody>
        <?php foreach($cats as $c): ?>
          <tr>
            <td><?=$c['id']?></td>
            <td><?=htmlspecialchars($c['name'])?></td>
            <td>
              <a class="btn btn-sm btn-outline-danger" href="/admin/categories.php?del=<?=$c['id']?>" onclick="return confirm('Delete this category?');">Delete</a>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($cats)): ?>
          <tr><td colspan="3" class="text-center py-4">No categories yet.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
