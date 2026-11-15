<?php
/**
 * Update Member API Endpoint
 * Method: POST
 * Auth: Required
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/upload.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Metode permintaan tidak diizinkan.', null, 405);
}

verifyCsrfToken();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$name = trim($_POST['name'] ?? '');
$positionId = filter_input(INPUT_POST, 'position_id', FILTER_VALIDATE_INT);
$departmentId = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT) ?: null;
$schoolOrigin = trim($_POST['school_origin'] ?? '');
$instagram = trim($_POST['social_instagram'] ?? '');
$orderIndex = filter_input(INPUT_POST, 'order_index', FILTER_VALIDATE_INT) ?: 0;
$isActive = isset($_POST['is_active']) ? (int)$_POST['is_active'] : 1;

if (!$id || empty($name) || !$positionId) {
    jsonResponse(false, 'ID, nama lengkap, dan jabatan wajib diisi.', null, 422);
}

try {
    $db = getDB();

    // Check existing
    $check = $db->prepare("SELECT id, photo_path FROM members WHERE id = ?");
    $check->execute([$id]);
    $current = $check->fetch();

    if (!$current) {
        jsonResponse(false, 'Data pengurus tidak ditemukan.', null, 404);
    }

    $photoPath = $current['photo_path'];
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $newPhoto = handleImageUpload('photo');
        if ($newPhoto) {
            // Remove old uploaded file if exists and starts with uploads/
            if (!empty($photoPath) && strpos($photoPath, 'uploads/') === 0 && file_exists(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $photoPath)) {
                @unlink(dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $photoPath);
            }
            $photoPath = $newPhoto;
        }
    }

    $stmt = $db->prepare("
        UPDATE members 
        SET name = ?, position_id = ?, department_id = ?, school_origin = ?, photo_path = ?, social_instagram = ?, order_index = ?, is_active = ?
        WHERE id = ?
    ");
    $stmt->execute([$name, $positionId, $departmentId, $schoolOrigin, $photoPath, $instagram, $orderIndex, $isActive, $id]);

    logActivity('member_update', "Memperbarui data pengurus: {$name} (ID: {$id})");

    jsonResponse(true, 'Data pengurus berhasil diperbarui.');

} catch (\Exception $e) {
    error_log("[Member Update Error] " . $e->getMessage());
    jsonResponse(false, $e->getMessage() ?: 'Gagal memperbarui data pengurus.', null, 500);
}
