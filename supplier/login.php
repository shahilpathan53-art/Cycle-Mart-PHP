<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    if ($email === '' || $pass === '') {
        $err = 'Email and password are required.';
    } else {
        $sql = "SELECT u.*, r.name AS role
                FROM users u
                JOIN roles r ON r.id=u.role_id
                WHERE u.email='$email' LIMIT 1";
        $res = mysqli_query($conn, $sql);
        $u   = mysqli_fetch_assoc($res);

        if ($u && $pass === ($u['password_plain'] ?? '') && $u['role'] === 'supplier') {
            login($u);
            header('Location: dashboard.php'); 
            exit;
        } else {
            $err = 'Invalid credentials or not a supplier account.';
        }
    }
}

include __DIR__ . '/../includes/header_supplier.php';

?>
<div class="container" style="max-width:420px; margin-top:60px;">
    <div class="glass p-4 shadow">
        <h3 class="mb-3">Supplier Login</h3>
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
        <div class="mt-3 text-center">
            <small>New supplier? <a href="register.php">Create account</a></small>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
