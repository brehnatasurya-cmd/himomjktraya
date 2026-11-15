<?php
/**
 * Site Settings List API Endpoint
 * Method: GET
 * Params: group (optional)
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();

    $group = filter_input(INPUT_GET, 'group', FILTER_SANITIZE_SPECIAL_CHARS);

    if ($group) {
        $stmt = $db->prepare("SELECT `key`, `value`, `label`, `group` FROM site_settings WHERE `group` = ?");
        $stmt->execute([$group]);
    } else {
        $stmt = $db->query("SELECT `key`, `value`, `label`, `group` FROM site_settings ORDER BY `group`, id ASC");
    }

    $settings = $stmt->fetchAll();

    $keyed = [];
    foreach ($settings as $s) {
        $keyed[$s['key']] = $s['value'];
    }

    jsonResponse(true, 'Pengaturan website berhasil diambil.', [
        'items' => $settings,
        'keyed' => $keyed
    ]);

} catch (\Exception $e) {
    error_log("[Settings Index Error] " . $e->getMessage());
    jsonResponse(false, 'Gagal mengambil pengaturan website.', null, 500);
}
