<?php
/**
 * Contacts List API Endpoint
 * Method: GET
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();

    $stmt = $db->query("SELECT `key`, `value`, `label`, `type` FROM contacts ORDER BY id ASC");
    $contacts = $stmt->fetchAll();

    // Map into keyed object for easy frontend/form usage
    $keyed = [];
    foreach ($contacts as $c) {
        $keyed[$c['key']] = [
            'value' => $c['value'],
            'label' => $c['label'],
            'type'  => $c['type']
        ];
    }

    jsonResponse(true, 'Data kontak berhasil diambil.', [
        'items' => $contacts,
        'keyed' => $keyed
    ]);

} catch (\Exception $e) {
    error_log("[Contacts Index Error] " . $e->getMessage());
    jsonResponse(false, 'Gagal mengambil data kontak.', null, 500);
}
