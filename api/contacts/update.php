<?php
/**
 * Update Contacts API Endpoint
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

if (empty($input) || !is_array($input)) {
    jsonResponse(false, 'Data pembaruan kontak tidak boleh kosong.', null, 422);
}

try {
    $db = getDB();
    $db->beginTransaction();

    $stmt = $db->prepare("UPDATE contacts SET `value` = ? WHERE `key` = ?");

    $allowedKeys = [
        'contact_person_1_name', 'contact_person_1_role', 'contact_person_1_phone',
        'contact_person_2_name', 'contact_person_2_role', 'contact_person_2_phone',
        'official_email', 'social_instagram_url', 'social_tiktok_url', 'social_youtube_url',
        'secretariat_address', 'google_maps_embed_url'
    ];

    $updatedCount = 0;
    foreach ($input as $key => $val) {
        if (in_array($key, $allowedKeys)) {
            $stmt->execute([trim((string)$val), $key]);
            $updatedCount++;
        }
    }

    $db->commit();
    logActivity('contacts_update', "Memperbarui konfigurasi kontak resmi ({$updatedCount} entri).");

    jsonResponse(true, 'Data kontak berhasil diperbarui.');

} catch (\Exception $e) {
    if (isset($db) && $db->inTransaction()) {
        $db->rollBack();
    }
    error_log("[Contacts Update Error] " . $e->getMessage());
    jsonResponse(false, 'Gagal memperbarui data kontak.', null, 500);
}
