<?php
/**
 * Delete Member API Endpoint
 * Method: POST
 * Auth: Required
 */

require_once __DIR__ . '/../helpers/auth.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Metode permintaan tidak diizinkan.', null, 405);
}

verifyCsrfToken();

$input = getJsonInput() ?: $_POST;
$id = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

if (!$id) {
    jsonResponse(false, 'ID pengurus tidak valid.', null, 422);
}

try {
    $db = getDB();

    $stmt = $db->prepare("SELECT id, name, photo_path FROM members WHERE id = ?");
    $stmt->execute([$id]);
    $member = $stmt->fetch();

    if (!$member) {
        jsonResponse(false, 'Data pengurus tidak ditemukan.', null, 404);
    }

    if (!empty($member['photo_path']) && strpos($member['photo_path'], 'uploads/') === 0) {
        $absPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $member['photo_path'];
        if (file_exists($absPath)) {
            @unlink($absPath);
        }
    }

    $del = $db->prepare("DELETE FROM members WHERE id = ?");
    $del->execute([$id]);

    logActivity('member_delete', "Menghapus pengurus: {$member['name']} (ID: {$id})");

    jsonResponse(true, 'Data pengurus berhasil dihapus.');

} catch (\Exception $e) {
    error_log("[Member Delete Error] " . $e->getMessage());
    jsonResponse(false, 'Gagal menghapus data pengurus.', null, 500);
}
