<?php
/**
 * Delete News Article API Endpoint
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
    jsonResponse(false, 'ID artikel tidak valid.', null, 422);
}

try {
    $db = getDB();

    $stmt = $db->prepare("SELECT id, title, featured_image FROM news WHERE id = ?");
    $stmt->execute([$id]);
    $news = $stmt->fetch();

    if (!$news) {
        jsonResponse(false, 'Artikel berita tidak ditemukan.', null, 404);
    }

    if (!empty($news['featured_image']) && strpos($news['featured_image'], 'uploads/') === 0) {
        $absPath = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . $news['featured_image'];
        if (file_exists($absPath)) @unlink($absPath);
    }

    $del = $db->prepare("DELETE FROM news WHERE id = ?");
    $del->execute([$id]);

    logActivity('news_delete', "Menghapus artikel: {$news['title']} (ID: {$id})");

    jsonResponse(true, 'Artikel berita berhasil dihapus.');

} catch (\Exception $e) {
    error_log("[News Delete Error] " . $e->getMessage());
    jsonResponse(false, 'Gagal menghapus artikel berita.', null, 500);
}
