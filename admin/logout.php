<?php
require_once __DIR__ . '/../api/helpers/auth.php';

initSecureSession();

if (!empty($_SESSION['user_id'])) {
    logActivity('logout', "Pengguna {$_SESSION['user_email']} keluar dari admin dashboard.");
}

$_SESSION = [];

if (ini_get("session.use_cookies")) {
    $params = session_get_cookie_params();
    setcookie(
        session_name(),
        '',
        time() - 42000,
        $params["path"],
        $params["domain"],
        $params["secure"],
        $params["httponly"]
    );
}

session_destroy();
header('Location: login.php');
exit;
