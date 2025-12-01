<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/auth.php';

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name  = mysqli_real_escape_string($conn, trim($_POST['name'] ?? ''));
    $email = mysqli_real_escape_string($conn, trim($_POST['email'] ?? ''));
    $pass  = $_POST['password'] ?? '';

    if ($name === '' || $email === '' || $pass === '') {
        $err = 'All fields are required.';
    } else {
        // Check if email exists
        $exists = mysqli_query($conn, "SELECT id FROM users WHERE email='$email' LIMIT 1");
        if (mysqli_fetch_assoc($exists)) {
            $err = 'Email already registered.';
        } else {
            // For demo: store plain password (replace with password_hash in production)
            $ok = mysqli_query($conn, "INSERT INTO users (role_id,name,email,password_plain) VALUES (3,'$name','$email','$pass')");
            if ($ok) {
                // Auto-login
                $res = mysqli_query($conn, "SELECT u.*, r.name AS role FROM users u JOIN roles r ON r.id=u.role_id WHERE email='$email' LIMIT 1");
                $u = mysqli_fetch_assoc($res);
                login($u);
                header('Location: dashboard.php');
                exit;
            } else {
                $err = 'Registration failed.';
            }
        }
    }
}

include __DIR__ . '/../includes/header_supplier.php';

?>
<div class="container" style="max-width:460px; margin-top:60px;">
    <div class="glass p-4 shadow">
        <h3 class="mb-3">Create Supplier Account</h3>
        <?php if($err): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($err) ?></div>
        <?php endif; ?>
        <form method="post">
            <div class="mb-3">
                <label class="form-label">Full Name</label>
                <input name="name" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Email</label>
                <input name="email" type="email" class="form-control" required>
            </div>
            <div class="mb-3">
                <label class="form-label">Password</label>
                <input name="password" type="password" class="form-control" required>
            </div>
            <button class="btn btn-brand w-100">Register</button>
        </form>
        <div class="mt-3 text-center">
            <small>Already have an account? <a href="login.php">Login</a></small>
        </div>
    </div>
</div>
<?php include __DIR__ . '/../includes/footer.php'; ?>
