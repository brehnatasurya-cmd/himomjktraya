<?php
$pageTitle = 'Dashboard';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../api/config/database.php';

$totalMembers = 0;
$totalNews = 0;
$publishedNews = 0;
$totalDepartments = 0;
$recentLogs = [];

try {
    $db = getDB();

    $totalMembers = (int)$db->query("SELECT COUNT(*) FROM members WHERE is_active = 1")->fetchColumn();
    $totalNews = (int)$db->query("SELECT COUNT(*) FROM news")->fetchColumn();
    $publishedNews = (int)$db->query("SELECT COUNT(*) FROM news WHERE status = 'published'")->fetchColumn();
    $totalDepartments = (int)$db->query("SELECT COUNT(*) FROM departments")->fetchColumn();

    $stmt = $db->query("SELECT action, description, ip_address, created_at FROM activity_logs ORDER BY id DESC LIMIT 6");
    $recentLogs = $stmt->fetchAll();

} catch (\Exception $e) {
    error_log("[Dashboard Stats Error] " . $e->getMessage());
}
?>

<div class="admin-header">
  <div>
    <h1 class="admin-page-title">Ringkasan Sistem</h1>
    <p class="admin-page-subtitle">Selamat datang kembali di panel administrasi HIMO Mojokerto Raya.</p>
  </div>
  <div>
    <a href="news.php" class="btn-admin btn-admin-accent">
      <i class="fas fa-plus"></i> Buat Berita Baru
    </a>
  </div>
</div>

<!-- STATS CARDS -->
<div class="admin-grid">
  <div class="admin-stat-card">
    <div>
      <div class="admin-stat-num"><?php echo $totalMembers; ?></div>
      <div class="admin-stat-label">Pengurus Aktif</div>
    </div>
    <div class="admin-stat-icon">
      <i class="fas fa-users"></i>
    </div>
  </div>

  <div class="admin-stat-card">
    <div>
      <div class="admin-stat-num"><?php echo $publishedNews; ?></div>
      <div class="admin-stat-label">Berita Diterbitkan</div>
    </div>
    <div class="admin-stat-icon">
      <i class="fas fa-newspaper"></i>
    </div>
  </div>

  <div class="admin-stat-card">
    <div>
      <div class="admin-stat-num"><?php echo $totalDepartments; ?></div>
      <div class="admin-stat-label">Departemen Organisasi</div>
    </div>
    <div class="admin-stat-icon">
      <i class="fas fa-layer-group"></i>
    </div>
  </div>

  <div class="admin-stat-card">
    <div>
      <div class="admin-stat-num">2026/2027</div>
      <div class="admin-stat-label">Periode Kepengurusan</div>
    </div>
    <div class="admin-stat-icon">
      <i class="fas fa-calendar-alt"></i>
    </div>
  </div>
</div>

<!-- QUICK ACTIONS & AUDIT TRAIL -->
<div style="display: grid; grid-template-columns: 1fr 1fr; gap: 24px;">
  
  <!-- QUICK ACTIONS -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title"><i class="fas fa-bolt" style="color:var(--admin-sky)"></i> Aksi Cepat</h2>
    </div>
    <div style="padding: 24px; display: flex; flex-direction: column; gap: 12px;">
      <a href="members.php" class="btn-admin btn-admin-outline" style="justify-content: space-between;">
        <span><i class="fas fa-user-plus"></i> Kelola Struktur Pengurus</span>
        <i class="fas fa-chevron-right"></i>
      </a>
      <a href="news.php" class="btn-admin btn-admin-outline" style="justify-content: space-between;">
        <span><i class="fas fa-edit"></i> Publikasi & Dokumentasi Kegiatan</span>
        <i class="fas fa-chevron-right"></i>
      </a>
      <a href="contacts.php" class="btn-admin btn-admin-outline" style="justify-content: space-between;">
        <span><i class="fas fa-phone-alt"></i> Perbarui Narahubung & Medsos</span>
        <i class="fas fa-chevron-right"></i>
      </a>
      <a href="settings.php" class="btn-admin btn-admin-outline" style="justify-content: space-between;">
        <span><i class="fas fa-cog"></i> Pengaturan Logo & Hero Landing</span>
        <i class="fas fa-chevron-right"></i>
      </a>
    </div>
  </div>

  <!-- RECENT AUDIT LOGS -->
  <div class="admin-card">
    <div class="admin-card-header">
      <h2 class="admin-card-title"><i class="fas fa-history" style="color:var(--admin-navy-mid)"></i> Aktivitas Sistem Terbaru</h2>
      <a href="activity-logs.php" style="font-size: 0.82rem; color: var(--admin-sky); text-decoration: none; font-weight: 700;">Lihat Semua</a>
    </div>
    <div class="admin-table-wrap">
      <table class="admin-table">
        <thead>
          <tr>
            <th>Waktu</th>
            <th>Tindakan</th>
            <th>Keterangan</th>
          </tr>
        </thead>
        <tbody>
          <?php if (empty($recentLogs)): ?>
            <tr>
              <td colspan="3" style="text-align: center; color: var(--admin-text-soft);">Belum ada catatan aktivitas tercatat.</td>
            </tr>
          <?php else: ?>
            <?php foreach ($recentLogs as $log): ?>
              <tr>
                <td style="font-size: 0.8rem; white-space: nowrap;"><?php echo date('d M H:i', strtotime($log['created_at'])); ?></td>
                <td><span class="badge badge-info"><?php echo htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8'); ?></span></td>
                <td style="font-size: 0.85rem;"><?php echo htmlspecialchars($log['description'], ENT_QUOTES, 'UTF-8'); ?></td>
              </tr>
            <?php endforeach; ?>
          <?php endif; ?>
        </tbody>
      </table>
    </div>
  </div>

</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
