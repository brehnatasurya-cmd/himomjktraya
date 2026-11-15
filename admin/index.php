<?php
/**
 * Admin Entrypoint Router
 */
require_once __DIR__ . '/../api/helpers/auth.php';

initSecureSession();

if (!empty($_SESSION['user_id'])) {
    header('Location: dashboard.php');
} else {
    header('Location: login.php');
}
exit;
