<?php
/**
 * Members List API Endpoint
 * Method: GET
 * Params: department_id (optional), level (optional), is_active (optional)
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();

    $departmentId = filter_input(INPUT_GET, 'department_id', FILTER_VALIDATE_INT);
    $level = filter_input(INPUT_GET, 'level', FILTER_SANITIZE_SPECIAL_CHARS);
    $activeOnly = isset($_GET['all']) ? false : true;

    $sql = "
        SELECT 
            m.id, m.name, m.school_origin, m.photo_path, m.social_instagram, m.order_index, m.is_active,
            p.id as position_id, p.name as position_name, p.level as position_level,
            d.id as department_id, d.name as department_name, d.slug as department_slug
        FROM members m
        JOIN positions p ON m.position_id = p.id
        LEFT JOIN departments d ON m.department_id = d.id
        WHERE 1=1
    ";

    $params = [];

    if ($activeOnly) {
        $sql .= " AND m.is_active = 1";
    }

    if ($departmentId) {
        $sql .= " AND m.department_id = ?";
        $params[] = $departmentId;
    }

    if ($level) {
        $sql .= " AND p.level = ?";
        $params[] = $level;
    }

    $sql .= " ORDER BY p.order_index ASC, m.order_index ASC, m.id ASC";

    $stmt = $db->prepare($sql);
    $stmt->execute($params);
    $members = $stmt->fetchAll();

    jsonResponse(true, 'Data kepengurusan berhasil diambil.', $members);

} catch (\Exception $e) {
    error_log("[Members Index Error] " . $e->getMessage());
    jsonResponse(false, 'Gagal mengambil data pengurus.', null, 500);
}
