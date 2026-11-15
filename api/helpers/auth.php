<?php
/**
 * Authentication & Security Helper
 * Handles session protection, CSRF validation, rate limiting, and RBAC
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/response.php';

function initSecureSession() {
    if (session_status() === PHP_SESSION_NONE) {
        $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') || (isset($_SERVER['SERVER_PORT']) && $_SERVER['SERVER_PORT'] == 443);

        session_set_cookie_params([
            'lifetime' => 0,
            'path'     => '/',
            'domain'   => '',
            'secure'   => $isSecure,
            'httponly' => true,
            'samesite' => 'Lax'
        ]);

        session_start();
    }
}

function generateCsrfToken() {
    initSecureSession();
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrfToken($token = null) {
    initSecureSession();
    if ($token === null) {
        $token = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? $_POST['csrf_token'] ?? '';
    }

    if (empty($_SESSION['csrf_token']) || empty($token) || !hash_equals($_SESSION['csrf_token'], $token)) {
        jsonResponse(false, 'Validasi keamanan CSRF token tidak valid atau telah kedaluwarsa.', null, 403);
    }
    return true;
}

function getClientIp() {
    if (!empty($_SERVER['HTTP_CLIENT_IP'])) {
        return $_SERVER['HTTP_CLIENT_IP'];
    } elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
        $ips = explode(',', $_SERVER['HTTP_X_FORWARDED_FOR']);
        return trim($ips[0]);
    }
    return $_SERVER['REMOTE_ADDR'] ?? '127.0.0.1';
}

function checkRateLimit($action = 'login', $maxAttempts = 5, $decayMinutes = 15) {
    initSecureSession();
    $ip = getClientIp();
    $key = "rate_limit_{$action}_{$ip}";

    $now = time();
    $attempts = $_SESSION[$key] ?? ['count' => 0, 'first_attempt' => $now, 'locked_until' => 0];

    if ($attempts['locked_until'] > $now) {
        $remaining = ceil(($attempts['locked_until'] - $now) / 60);
        jsonResponse(false, "Terlalu banyak percobaan. Akses dibatasi selama {$remaining} menit ke depan demi keamanan.", null, 429);
    }

    if (($now - $attempts['first_attempt']) > ($decayMinutes * 60)) {
        // Reset window
        $attempts = ['count' => 1, 'first_attempt' => $now, 'locked_until' => 0];
    } else {
        $attempts['count']++;
        if ($attempts['count'] > $maxAttempts) {
            $attempts['locked_until'] = $now + ($decayMinutes * 60);
            $_SESSION[$key] = $attempts;
            jsonResponse(false, "Batas percobaan login terlampaui. Sistem terkunci selama {$decayMinutes} menit.", null, 429);
        }
    }

    $_SESSION[$key] = $attempts;
}

function resetRateLimit($action = 'login') {
    initSecureSession();
    $ip = getClientIp();
    $key = "rate_limit_{$action}_{$ip}";
    unset($_SESSION[$key]);
}

function requireAuth() {
    initSecureSession();
    if (empty($_SESSION['user_id'])) {
        jsonResponse(false, 'Sesi Anda telah berakhir atau belum terautentikasi. Silakan masuk kembali.', null, 401);
    }
    return $_SESSION['user_id'];
}

function requireRole($allowedRoles = ['super_admin']) {
    $userId = requireAuth();
    $userRole = $_SESSION['user_role'] ?? '';

    if (!in_array($userRole, (array)$allowedRoles)) {
        jsonResponse(false, 'Anda tidak memiliki hak akses (permission) untuk menjalankan tindakan ini.', null, 403);
    }
    return $userId;
}

function logActivity($action, $description, $details = null) {
    try {
        $db = getDB();
        $userId = $_SESSION['user_id'] ?? null;
        $ip = getClientIp();
        $userAgent = substr($_SERVER['HTTP_USER_AGENT'] ?? 'Unknown', 0, 255);
        $detailsJson = $details ? json_encode($details, JSON_UNESCAPED_UNICODE) : null;

        $stmt = $db->prepare("INSERT INTO activity_logs (user_id, action, description, details, ip_address, user_agent) VALUES (?, ?, ?, ?, ?, ?)");
        $stmt->execute([$userId, $action, $description, $detailsJson, $ip, $userAgent]);
    } catch (\Exception $e) {
        error_log("[LogActivity Failed] " . $e->getMessage());
    }
}
