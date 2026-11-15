<?php
/**
 * News List API Endpoint
 * Method: GET
 * Params: category_id, search, status, page, limit
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../config/database.php';

try {
    $db = getDB();

    $page = max(1, (int)($_GET['page'] ?? 1));
    $limit = min(50, max(1, (int)($_GET['limit'] ?? 9)));
    $offset = ($page - 1) * $limit;

    $categoryId = filter_input(INPUT_GET, 'category_id', FILTER_VALIDATE_INT);
    $categorySlug = filter_input(INPUT_GET, 'category', FILTER_SANITIZE_SPECIAL_CHARS);
    $search = trim($_GET['search'] ?? '');
    $status = $_GET['status'] ?? 'published'; // default published for public

    $where = [];
    $params = [];

    if ($status !== 'all') {
        $where[] = "n.status = ?";
        $params[] = $status;
    }

    if ($categoryId) {
        $where[] = "n.category_id = ?";
        $params[] = $categoryId;
    } elseif ($categorySlug && $categorySlug !== 'semua') {
        $where[] = "c.slug = ?";
        $params[] = $categorySlug;
    }

    if (!empty($search)) {
        $where[] = "(n.title LIKE ? OR n.excerpt LIKE ?)";
        $searchTerm = "%{$search}%";
        $params[] = $searchTerm;
        $params[] = $searchTerm;
    }

    $whereClause = !empty($where) ? "WHERE " . implode(" AND ", $where) : "";

    // Count total records
    $countSql = "SELECT COUNT(*) FROM news n LEFT JOIN news_categories c ON n.category_id = c.id {$whereClause}";
    $countStmt = $db->prepare($countSql);
    $countStmt->execute($params);
    $total = (int)$countStmt->fetchColumn();

    // Fetch news list
    $sql = "
        SELECT 
            n.id, n.title, n.slug, n.excerpt, n.featured_image, n.views_count, n.status, n.published_at, n.created_at,
            c.id as category_id, c.name as category_name, c.slug as category_slug,
            u.name as author_name
        FROM news n
        JOIN news_categories c ON n.category_id = c.id
        LEFT JOIN users u ON n.author_id = u.id
        {$whereClause}
        ORDER BY n.published_at DESC, n.id DESC
        LIMIT ? OFFSET ?
    ";

    $stmt = $db->prepare($sql);
    $bindIndex = 1;
    foreach ($params as $param) {
        $stmt->bindValue($bindIndex++, $param);
    }
    $stmt->bindValue($bindIndex++, $limit, PDO::PARAM_INT);
    $stmt->bindValue($bindIndex++, $offset, PDO::PARAM_INT);
    $stmt->execute();
    $newsList = $stmt->fetchAll();

    jsonResponse(true, 'Data berita berhasil diambil.', [
        'items'       => $newsList,
        'pagination'  => [
            'total'        => $total,
            'page'         => $page,
            'limit'        => $limit,
            'total_pages'  => ceil($total / $limit)
        ]
    ]);

} catch (\Exception $e) {
    error_log("[News Index Error] " . $e->getMessage());
    jsonResponse(false, 'Gagal mengambil data berita.', null, 500);
}
