<?php
require_once '../config/db.php';
require_once '../includes/auth.php';

$err = '';
if ($_SERVER['REQUEST_METHOD']==='POST') {
  $email = mysqli_real_escape_string($conn, $_POST['email'] ?? '');
  $pass  = $_POST['password'] ?? '';
  $sql   = "SELECT u.*, r.name AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE u.email='$email' LIMIT 1";
  $res   = mysqli_query($conn, $sql);
  $u     = mysqli_fetch_assoc($res);

  if ($u && $pass === $u['password_plain'] && $u['role']==='admin') {
    login($u);
    header('Location: dashboard.php'); exit;
  } else {
    $err = 'Invalid admin credentials.';
  }
}

include '../includes/header_admin.php';

?>
<div class="container" style="max-width:420px; margin-top:60px;">
  <div class="glass p-4 shadow">
    <h3 class="mb-3">Admin Login</h3>
    <?php if($err): ?><div class="alert alert-danger"><?=htmlspecialchars($err)?></div><?php endif; ?>
    <form method="post">
      <div class="mb-3">
        <label class="form-label">Email</label>
        <input name="email" type="email" class="form-control" required autofocus>
      </div>
      <div class="mb-3">
        <label class="form-label">Password</label>
        <input name="password" type="password" class="form-control" required>
      </div>
      <button class="btn btn-brand w-100">Login</button>
    </form>
  </div>
</div>
<?php include '../includes/footer.php'; ?>
