<?php
require_once '../config/db.php';
require_once '../includes/auth.php';
require_once '../includes/guards.php';
requireRole('admin');

// Handle role change
$err=''; $msg='';
if ($_SERVER['REQUEST_METHOD']==='POST' && isset($_POST['set_role'])) {
  $uid = (int)($_POST['user_id'] ?? 0);
  $role_id = (int)($_POST['role_id'] ?? 0);
  // prevent self-demotion optionally
  if ($uid>0 && in_array($role_id, [1,2,3])) {
    $ok = mysqli_query($conn, "UPDATE users SET role_id=$role_id WHERE id=$uid");
    if ($ok) { $msg='Role updated.'; } else { $err='Update failed.'; }
  } else { $err='Invalid input.'; }
}

// Load roles map
$roles=[]; $rr = mysqli_query($conn, "SELECT id,name FROM roles ORDER BY id");
while($r=mysqli_fetch_assoc($rr)) $roles[$r['id']]=$r['name'];

// Load users
$users=[];
$q = "SELECT u.id, u.name, u.email, u.role_id, r.name AS role
      FROM users u JOIN roles r ON r.id=u.role_id
      ORDER BY u.id DESC";
$res = mysqli_query($conn,$q);
while($r=mysqli_fetch_assoc($res)) $users[]=$r;

include '../includes/header_admin.php';
include '../includes/nav_admin.php';
?>
<div class="container my-4">
  <div class="glass p-3 mb-3">
    <h3 class="mb-0">Users</h3>
  </div>

  <?php if($err): ?><div class="alert alert-danger"><?=$err?></div><?php endif; ?>
  <?php if($msg): ?><div class="alert alert-success"><?=$msg?></div><?php endif; ?>

  <div class="glass p-0">
    <table class="table align-middle mb-0">
      <thead>
        <tr>
          <th style="width:8%">ID</th>
          <th style="width:22%">Name</th>
          <th style="width:25%">Email</th>
          <th style="width:15%">Role</th>
          <th style="width:30%">Actions</th>
        </tr>
      </thead>
      <tbody>
        <?php foreach($users as $u): ?>
          <tr>
            <td><?=$u['id']?></td>
            <td><?=htmlspecialchars($u['name'])?></td>
            <td><?=htmlspecialchars($u['email'])?></td>
            <td>
              <?php
                $badge='text-bg-secondary';
                if ($u['role']==='admin') $badge='text-bg-danger';
                if ($u['role']==='supplier') $badge='text-bg-info';
                if ($u['role']==='user') $badge='text-bg-success';
              ?>
              <span class="badge <?=$badge?>"><?=htmlspecialchars($u['role'])?></span>
            </td>
            <td>
              <form method="post" class="d-flex align-items-center gap-2">
                <input type="hidden" name="set_role" value="1">
                <input type="hidden" name="user_id" value="<?=$u['id']?>">
                <select name="role_id" class="form-select form-select-sm" style="max-width:180px;">
                  <?php foreach($roles as $id=>$name): ?>
                    <option value="<?=$id?>" <?=$u['role_id']==$id?'selected':''?>><?=htmlspecialchars($name)?></option>
                  <?php endforeach; ?>
                </select>
                <button class="btn btn-sm btn-brand">Update</button>
              </form>
            </td>
          </tr>
        <?php endforeach; ?>
        <?php if(empty($users)): ?>
          <tr><td colspan="5" class="text-center py-4">No users found.</td></tr>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
