<?php
/**
 * Update News Article API Endpoint
 * Method: POST (multipart/form-data)
 * Auth: Required
 */

require_once __DIR__ . '/../helpers/auth.php';
require_once __DIR__ . '/../helpers/upload.php';

requireAuth();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Metode permintaan tidak diizinkan.', null, 405);
}

verifyCsrfToken();

$id = filter_input(INPUT_POST, 'id', FILTER_VALIDATE_INT);
$title = trim($_POST['title'] ?? '');
$categoryId = filter_input(INPUT_POST, 'category_id', FILTER_VALIDATE_INT);
$excerpt = trim($_POST['excerpt'] ?? '');
$body = trim($_POST['body'] ?? '');
$status = in_array($_POST['status'] ?? '', ['draft', 'published', 'archived']) ? $_POST['status'] : 'draft';

if (!$id || empty($title) || !$categoryId || empty($body)) {
    jsonResponse(false, 'ID, judul berita, kategori, dan isi berita wajib diisi.', null, 422);
}

try {
    $db = getDB();

    $stmt = $db->prepare("SELECT id, featured_image, published_at FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $current = $stmt->fetch();

    if (!$current) {
        jsonResponse(false, 'Artikel berita tidak ditemukan.', null, 404);
    }

    $featuredImage = $current['featured_image'];
    if (isset($_FILES['featured_image']) && $_FILES['featured_image']['error'] !== UPLOAD_ERR_NO_FILE) {
        $newImage = handleImageUpload('featured_image');
        if ($newImage) {
            if (!empty($featuredImage) && strpos($featuredImage, 'uploads/') === 0) {
                $absPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $featuredImage;
                if (file_exists($absPath)) @unlink($absPath);
            }
            $featuredImage = $newImage;
        }
    }

    $publishedAt = $current['published_at'];
    if ($status === 'published' && empty($publishedAt)) {
        $publishedAt = date('Y-m-d H:i:s');
    }

    $update = $db->prepare("
        UPDATE news
        SET category_id = ?, title = ?, excerpt = ?, body = ?, featured_image = ?, status = ?, published_at = ?
        WHERE id = ?
    ");
    $update->execute([$categoryId, $title, $excerpt, $body, $featuredImage, $status, $publishedAt, $id]);

    logActivity('news_update', "Memperbarui artikel berita: {$title} (ID: {$id})");

    jsonResponse(true, 'Artikel berita berhasil diperbarui.');

} catch (\Exception $e) {
    error_log("[News Update Error] " . $e->getMessage());
    jsonResponse(false, $e->getMessage() ?: 'Gagal memperbarui artikel berita.', null, 500);
}
