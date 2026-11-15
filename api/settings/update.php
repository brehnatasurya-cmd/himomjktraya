<?php
/**
 * Update Site Settings API Endpoint
 * Method: POST (multipart/form-data or application/json)
 * Auth: Required
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/upload.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Metode permintaan tidak diizinkan.', null, 405);
}

verifyCsrfToken();

try {
    $db = getDB();
    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE site_settings SET `value` = ? WHERE `key` = ?");

    // 1. Text settings update
    $allowedKeys = [
        'site_name', 'site_tagline', 'period_active', 'org_description',
        'vision_statement', 'stat_members', 'stat_departments', 'stat_programs'
    ];

    foreach ($allowedKeys as $key) {
        if (isset($_POST[$key])) {
            $stmt->execute([trim($_POST[$key]), $key]);
        }
    }

    // 2. Handle Logo Upload
    if (isset($_FILES['site_logo']) && $_FILES['site_logo']['error'] !== UPLOAD_ERR_NO_FILE) {
        $logoPath = handleImageUpload('site_logo');
        if ($logoPath) {
            $stmt->execute([$logoPath, 'site_logo_path']);
        }
    }

    // 3. Handle Hero Photo Upload
    if (isset($_FILES['hero_background']) && $_FILES['hero_background']['error'] !== UPLOAD_ERR_NO_FILE) {
        $heroPath = handleImageUpload('hero_background');
        if ($heroPath) {
            $stmt->execute([$heroPath, 'hero_background_path']);
        }
    }

    $db->commit();
    logActivity('settings_update', 'Memperbarui pengaturan identitas dan profil organisasi.');

    jsonResponse(true, 'Pengaturan website berhasil diperbarui.');

} catch (\Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("[Settings Update Error] " . $e->getMessage());
    jsonResponse(false, $e->getMessage() ?: 'Gagal memperbarui pengaturan.', null, 500);
}
