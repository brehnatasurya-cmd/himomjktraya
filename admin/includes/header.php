<?php
require_once __DIR__ . '/../../api/helpers/auth.php';

initSecureSession();

if (empty($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

$csrfToken = generateCsrfToken();
$userName = $_SESSION['user_name'] ?? 'Admin';
$userRole = $_SESSION['user_role'] ?? 'super_admin';
$currentPage = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?php echo htmlspecialchars($pageTitle ?? 'Admin Dashboard', ENT_QUOTES, 'UTF-8'); ?> | HIMO Mojokerto Raya</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.0/css/all.min.css" />
  <link rel="stylesheet" href="assets/admin.css" />
</head>
<body>

<div class="admin-layout">
  <!-- SIDEBAR NAVIGATION -->
  <aside class="admin-sidebar" id="admin-sidebar">
    <a href="dashboard.php" class="admin-brand">
      <img src="../Logo Himo Mojokerto.PNG" alt="Logo HIMO" />
      <div>
        <div class="admin-brand-title">HIMO ADMIN</div>
        <div class="admin-brand-sub">Mojokerto Raya</div>
      </div>
    </a>

    <ul class="admin-menu">
      <li class="admin-menu-label">Utama</li>
      <li>
        <a href="dashboard.php" class="admin-menu-link <?php echo $currentPage === 'dashboard.php' ? 'active' : ''; ?>">
          <i class="fas fa-chart-pie"></i> Dashboard
        </a>
      </li>

      <li class="admin-menu-label">Pengelolaan Konten</li>
      <li>
        <a href="members.php" class="admin-menu-link <?php echo $currentPage === 'members.php' ? 'active' : ''; ?>">
          <i class="fas fa-sitemap"></i> Struktur Pengurus
        </a>
      </li>
      <li>
        <a href="news.php" class="admin-menu-link <?php echo $currentPage === 'news.php' ? 'active' : ''; ?>">
          <i class="fas fa-newspaper"></i> Informasi & Berita
        </a>
      </li>
      <li>
        <a href="contacts.php" class="admin-menu-link <?php echo $currentPage === 'contacts.php' ? 'active' : ''; ?>">
          <i class="fas fa-address-book"></i> Kontak & Medsos
        </a>
      </li>

      <li class="admin-menu-label">Sistem & Pengaturan</li>
      <li>
        <a href="settings.php" class="admin-menu-link <?php echo $currentPage === 'settings.php' ? 'active' : ''; ?>">
          <i class="fas fa-sliders-h"></i> Profil & Identitas
        </a>
      </li>
      <li>
        <a href="activity-logs.php" class="admin-menu-link <?php echo $currentPage === 'activity-logs.php' ? 'active' : ''; ?>">
          <i class="fas fa-shield-alt"></i> Log Keamanan
        </a>
      </li>
      <li>
        <a href="../index.html" target="_blank" class="admin-menu-link">
          <i class="fas fa-external-link-alt"></i> Lihat Website
        </a>
      </li>
    </ul>

    <div class="admin-user-bar">
      <div class="admin-user-info">
        <span class="admin-user-name"><?php echo htmlspecialchars($userName, ENT_QUOTES, 'UTF-8'); ?></span>
        <span class="admin-user-role"><?php echo htmlspecialchars($userRole, ENT_QUOTES, 'UTF-8'); ?></span>
      </div>
      <a href="logout.php" class="admin-logout-btn" title="Keluar">
        <i class="fas fa-sign-out-alt"></i>
      </a>
    </div>
  </aside>

  <!-- MAIN VIEW -->
  <main class="admin-main">
