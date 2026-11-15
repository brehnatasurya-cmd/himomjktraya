<?php
/**
 * Logout API Endpoint
 * Method: POST
 */

require_once __DIR__ . '/../helpers/auth.php';

initSecureSession();

if (!empty($_SESSION['user_id'])) {
    logActivity('logout', "Pengguna {$_SESSION['user_email']} keluar dari sistem.");
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

jsonResponse(true, 'Sesi berhasil diakhiri.', [
    'redirect' => '../admin/login.php'
]);
