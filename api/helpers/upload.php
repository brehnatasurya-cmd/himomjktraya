<?php
/**
 * Secure File Upload Helper
 * Validates real MIME type, file size, extension whitelist, and stores safely
 */

function handleImageUpload($fileKey, $targetDir = null, $maxSizeMb = 5) {
    if (!isset($_FILES[$fileKey]) || $_FILES[$fileKey]['error'] === UPLOAD_ERR_NO_FILE) {
        return null;
    }

    $file = $_FILES[$fileKey];

    if ($file['error'] !== UPLOAD_ERR_OK) {
        throw new \Exception("Gagal mengunggah file (Kode Error: {$file['error']}).");
    }

    // 1. Max Size check
    $maxBytes = $maxSizeMb * 1024 * 1024;
    if ($file['size'] > $maxBytes) {
        throw new \Exception("Ukuran file melebihi batas maksimal {$maxSizeMb}MB.");
    }

    // 2. MIME type verification
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);

    $allowedMimes = [
        'image/jpeg' => 'jpg',
        'image/png'  => 'png',
        'image/webp' => 'webp'
    ];

    if (!array_key_exists($mime, $allowedMimes)) {
        throw new \Exception("Format file tidak didukung ({$mime}). Hanya format JPG, PNG, dan WebP yang diizinkan.");
    }

    // 3. Extension from verified MIME (prevent double extension spoofing)
    $ext = $allowedMimes[$mime];

    // Target directory
    if ($targetDir === null) {
        $targetDir = dirname(__DIR__, 2) . DIRECTORY_SEPARATOR . 'uploads' . DIRECTORY_SEPARATOR . 'images';
    }

    if (!is_dir($targetDir)) {
        mkdir($targetDir, 0755, true);
    }

    // 4. Cryptographically secure random filename
    $filename = bin2hex(random_bytes(16)) . '_' . time() . '.' . $ext;
    $destination = $targetDir . DIRECTORY_SEPARATOR . $filename;

    if (!move_uploaded_file($file['tmp_name'], $destination)) {
        throw new \Exception("Gagal memindahkan file ke direktori penyimpanan server.");
    }

    // Return relative public path
    return 'uploads/images/' . $filename;
}
