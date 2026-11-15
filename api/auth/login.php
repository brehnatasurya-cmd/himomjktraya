<?php
/**
 * Login API Endpoint
 * Method: POST
 * Requires: email, password, csrf_token
 */

require_once __DIR__ . '/../helpers/auth.php';

header('Content-Type: application/json; charset=UTF-8');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Metode permintaan tidak diizinkan. Gunakan POST.', null, 405);
}

// 1. Rate limiting check (max 5 attempts per 15 minutes)
checkRateLimit('login', 5, 15);

// 2. Fetch input data (supports JSON or Form POST)
$input = getJsonInput();
if (empty($input)) {
    $input = $_POST;
}

$email = filter_var($input['email'] ?? '', FILTER_VALIDATE_EMAIL);
$password = (string)($input['password'] ?? '');

if (!$email || empty($password)) {
    jsonResponse(false, 'Alamat email dan password wajib diisi dengan format yang benar.', null, 422);
}

// 3. Database lookup with prepared statement
try {
    $db = getDB();
    $stmt = $db->prepare("
        SELECT u.id, u.name, u.email, u.password_hash, u.is_active, u.lockout_until, r.name as role_name
        FROM users u
        JOIN roles r ON u.role_id = r.id
        WHERE u.email = ?
        LIMIT 1
    ");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user) {
        jsonResponse(false, 'Kredensial email atau password tidak sesuai.', null, 401);
    }

    if (!$user['is_active']) {
        jsonResponse(false, 'Akun ini dinonaktifkan oleh administrator. Hubungi tim teknis.', null, 403);
    }

    // 4. Verify password with bcrypt
    if (!password_verify($password, $user['password_hash'])) {
        jsonResponse(false, 'Kredensial email atau password tidak sesuai.', null, 401);
    }

    // Check if password needs rehash (e.g. upgraded cost)
    if (password_needs_rehash($user['password_hash'], PASSWORD_BCRYPT, ['cost' => 12])) {
        $newHash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);
        $updateStmt = $db->prepare("UPDATE users SET password_hash = ? WHERE id = ?");
        $updateStmt->execute([$newHash, $user['id']]);
    }

    // 5. Successful login: Reset rate limiter and initialize session
    resetRateLimit('login');
    initSecureSession();
    session_regenerate_id(true);

    $_SESSION['user_id'] = (int)$user['id'];
    $_SESSION['user_name'] = $user['name'];
    $_SESSION['user_email'] = $user['email'];
    $_SESSION['user_role'] = $user['role_name'];
    $_SESSION['logged_at'] = time();

    // Update last login in database
    $ip = getClientIp();
    $db->prepare("UPDATE users SET last_login_at = NOW(), last_login_ip = ?, failed_login_attempts = 0 WHERE id = ?")
       ->execute([$ip, $user['id']]);

    logActivity('login', "Pengguna {$user['email']} berhasil masuk ke sistem.");

    jsonResponse(true, 'Login berhasil.', [
        'user' => [
            'id'    => $user['id'],
            'name'  => $user['name'],
            'email' => $user['email'],
            'role'  => $user['role_name']
        ],
        'redirect' => '../admin/dashboard.php'
    ]);

} catch (\Exception $e) {
    error_log("[Login Error] " . $e->getMessage());
    jsonResponse(false, 'Terjadi gangguan internal saat memproses autentikasi.', null, 500);
}
