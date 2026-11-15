<?php
/**
 * Single News Detail API Endpoint
 * Method: GET
 * Params: slug or id
 */

require_once __DIR__ . '/../helpers/response.php';
require_once __DIR__ . '/../config/database.php';

$slug = trim($_GET['slug'] ?? '');
$id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (empty($slug) && !$id) {
    jsonResponse(false, 'Parameter slug atau ID berita wajib disertakan.', null, 422);
}

try {
    $db = getDB();

    if ($id) {
        $stmt = $db->prepare("
            SELECT n.*, c.name as category_name, c.slug as category_slug, u.name as author_name
            FROM news n
            JOIN news_categories c ON n.category_id = c.id
            LEFT JOIN users u ON n.author_id = u.id
            WHERE n.id = ?
            LIMIT 1
        ");
        $stmt->execute([$id]);
    } else {
        $stmt = $db->prepare("
            SELECT n.*, c.name as category_name, c.slug as category_slug, u.name as author_name
            FROM news n
            JOIN news_categories c ON n.category_id = c.id
            LEFT JOIN users u ON n.author_id = u.id
            WHERE n.slug = ?
            LIMIT 1
        ");
        $stmt->execute([$slug]);
    }

    $article = $stmt->fetch();

    if (!$article) {
        jsonResponse(false, 'Artikel berita tidak ditemukan.', null, 404);
    }

    // Increment views count
    $db->prepare("UPDATE news SET views_count = views_count + 1 WHERE id = ?")->execute([$article['id']]);

    // Fetch documentation images gallery if any
    $galleryStmt = $db->prepare("SELECT id, image_path, caption FROM news_images WHERE news_id = ? ORDER BY order_index ASC");
    $galleryStmt->execute([$article['id']]);
    $article['gallery'] = $galleryStmt->fetchAll();

    // Fetch related articles (same category, excluding current)
    $relatedStmt = $db->prepare("
        SELECT id, title, slug, excerpt, featured_image, published_at
        FROM news
        WHERE category_id = ? AND id != ? AND status = 'published'
        ORDER BY published_at DESC
        LIMIT 3
    ");
    $relatedStmt->execute([$article['category_id'], $article['id']]);
    $article['related'] = $relatedStmt->fetchAll();

    jsonResponse(true, 'Detail berita berhasil diambil.', $article);

} catch (\Exception $e) {
    error_log("[News Show Error] " . $e->getMessage());
    jsonResponse(false, 'Gagal mengambil detail berita.', null, 500);
}
