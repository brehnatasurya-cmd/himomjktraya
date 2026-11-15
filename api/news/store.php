<?php
/**
 * Create News Article API Endpoint
 * Method: POST (multipart/form-data)
 * Auth: Required
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/upload.php';

$userId = requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Metode permintaan tidak diizinkan.', null, 405);
}

verifyCsrfToken();

$title = trim($_POST['title'] ?? '');
$categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
$excerpt = trim($_POST['excerpt'] ?? '');
$body = trim($_POST['body'] ?? '');
$status = in_array($_POST['status'] ?? '', ['draft', 'published']) ? $_POST['status'] : 'draft';

if (empty($title) || !$categoryId || empty($body)) {
    jsonResponse(false, 'Judul berita, kategori, dan isi berita wajib diisi.', null, 422);
}

// Generate Excerpt if empty
if (empty($excerpt)) {
    $cleanText = strip_tags($body);
    $excerpt = mb_substr($cleanText, 0, 160) . '...';
}

// Generate Unique Slug
function createSlug($string, $db) {
    $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $string), '-'));
    $original = $slug;
    $count = 1;

    while (true) {
        $stmt = $db->prepare("SELECT id FROM news WHERE slug = ? LIMIT 1");
        $stmt->execute([$slug]);
        if (!$stmt->fetch()) {
            break;
        }
        $slug = $original . '-' . $count;
        $count++;
    }
    return $slug;
}

try {
    $db = getDB();
    $slug = createSlug($title, $db);

    $featuredImage = null;
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $featuredImage = handleImageUpload('featured_image');
    }

    $publishedAt = ($status === 'published') ? date('Y-m-d H:i:s') : null;

    $stmt = $db->prepare("
        INSERT INTO news (category_id, author_id, title, slug, excerpt, body, featured_image, status, published_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$categoryId, $userId, $title, $slug, $excerpt, $body, $featuredImage, $status, $publishedAt]);
    $newId = $db->lastInsertId();

    logActivity('news_create', "Membuat artikel berita: {$title} (ID: {$newId})");

    jsonResponse(true, 'Berita berhasil diterbitkan/disimpan.', [
        'id'   => $newId,
        'slug' => $slug
    ], 201);

} catch (\Exception $e) {
    error_log("[News Store Error] " . $e->getMessage());
    jsonResponse(false, $e->getMessage() ?: 'Terjadi kesalahan saat menyimpan artikel berita.', null, 500);
}
