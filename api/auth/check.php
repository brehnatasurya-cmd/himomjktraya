<?php
/**
 * Auth Status Check Endpoint
 * Returns current user data and a fresh CSRF token
 */

require_once __DIR__ . '/../helpers/auth.php';

initSecureSession();

$isAuthenticated = !empty($_SESSION['user_id']);
$csrfToken = generateCsrfToken();

if ($isAuthenticated) {
    jsonResponse(true, 'Pengguna terautentikasi.', [
        'is_logged_in' => true,
        'user' => [
            'id'    => $_SESSION['user_id'],
            'name'  => $_SESSION['user_name'] ?? '',
            'email' => $_SESSION['user_email'] ?? '',
            'role'  => $_SESSION['user_role'] ?? ''
        ],
        'csrf_token' => $csrfToken
    ]);
} else {
    jsonResponse(false, 'Pengguna belum terautentikasi.', [
        'is_logged_in' => false,
        'csrf_token'   => $csrfToken
    ], 200);
}
