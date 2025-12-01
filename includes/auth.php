<?php
// hamesha session start karo
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// check agar user login hai
function isLoggedIn() {
    return isset($_SESSION['user']);
}

// current user return karo (ya null)
function user() {
    return $_SESSION['user'] ?? null;
}

// login karne ke liye
function login($u) {
    $_SESSION['user'] = $u;
}

// logout karne ke liye
function logout() {
    $_SESSION = [];
    if (ini_get('session.use_cookies')) {
        $p = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $p['path'], $p['domain'], $p['secure'], $p['httponly']
        );
    }
    session_destroy();
}
