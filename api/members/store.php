<?php
/**
 * Create Member API Endpoint
 * Method: POST (multipart/form-data or application/json)
 * Auth: Required
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/upload.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Metode permintaan tidak diizinkan. Gunakan POST.', null, 405);
}

verifyCsrfToken();

$name = trim($_POST['name'] ?? '');
$positionId = filter_input(INPUT_POST, 'position_id', FILTER_VALIDATE_INT);
$departmentId = filter_input(INPUT_POST, 'department_id', FILTER_VALIDATE_INT) ?: null;
$schoolOrigin = trim($_POST['school_origin'] ?? '');
$instagram = trim($_POST['social_instagram'] ?? '');
$orderIndex = filter_input(INPUT_POST, 'order_index', FILTER_VALIDATE_INT) ?: 0;

if (empty($name) || !$positionId) {
    jsonResponse(false, 'Nama lengkap dan posisi/jabatan wajib diisi.', null, 422);
}

try {
    $photoPath = null;
    if (isset($_FILES['photo']) && $_FILES['photo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $photoPath = handleImageUpload('photo');
    }

    $db = getDB();
    $stmt = $db->prepare("
        INSERT INTO members (name, position_id, department_id, school_origin, photo_path, social_instagram, order_index)
        VALUES (?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$name, $positionId, $departmentId, $schoolOrigin, $photoPath, $instagram, $orderIndex]);
    $newId = $db->lastInsertId();

    logActivity('member_create', "Menambahkan pengurus baru: {$name} (ID: {$newId})");

    jsonResponse(true, 'Data pengurus berhasil ditambahkan.', ['id' => $newId], 201);

} catch (\Exception $e) {
    error_log("[Member Store Error] " . $e->getMessage());
    jsonResponse(false, $e->getMessage() ?: 'Terjadi kesalahan saat menyimpan data pengurus.', null, 500);
}
