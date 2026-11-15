<?php
$pageTitle = 'Log Aktivitas Keamanan';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../api/config/database.php';

$logs = [];

try {
    $db = getDB();
    $stmt = $db->query("
        SELECT l.*, u.name as user_name, u.email as user_email
        FROM activity_logs l
        LEFT JOIN users u ON l.user_id = u.id
        ORDER BY l.id DESC
        LIMIT 100
    ");
    $logs = $stmt->fetchAll();
} catch (\Exception $e) {
    error_log("[Logs Admin Error] " . $e->getMessage());
}
?>

<div class="admin-header">
  <div>
    <h1 class="admin-page-title">Log Audit & Keamanan Sistem</h1>
    <p class="admin-page-subtitle">Pencatatan riwayat autentikasi, modifikasi data, dan pengawasan akses sistem.</p>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title">Riwayat 100 Aktivitas Terakhir</h2>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>ID</th>
          <th>Waktu (WIB)</th>
          <th>Tindakan</th>
          <th>Pengguna</th>
          <th>Alamat IP</th>
          <th>Keterangan</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($logs)): ?>
          <tr>
            <td colspan="6" style="text-align: center; padding: 32px; color: var(--admin-text-soft);">
              Belum ada riwayat aktivitas yang tercatat.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($logs as $log): ?>
            <tr>
              <td style="color: var(--admin-text-soft); font-size: 0.8rem;">#<?php echo $log['id']; ?></td>
              <td style="font-size: 0.84rem; white-space: nowrap;"><?php echo date('d M Y H:i:s', strtotime($log['created_at'])); ?></td>
              <td><span class="badge badge-info"><?php echo htmlspecialchars($log['action'], ENT_QUOTES, 'UTF-8'); ?></span></td>
              <td style="font-size: 0.85rem;">
                <?php if (!empty($log['user_email'])): ?>
                  <strong><?php echo htmlspecialchars($log['user_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></strong>
                  <div style="font-size: 0.75rem; color: var(--admin-text-soft);"><?php echo htmlspecialchars($log['user_email'], ENT_QUOTES, 'UTF-8'); ?></div>
                <?php else: ?>
                  <span style="color: var(--admin-text-soft);">Sistem Publik</span>
                <?php endif; ?>
              </td>
              <td><code><?php echo htmlspecialchars($log['ip_address'], ENT_QUOTES, 'UTF-8'); ?></code></td>
              <td style="font-size: 0.86rem;"><?php echo htmlspecialchars($log['description'], ENT_QUOTES, 'UTF-8'); ?></td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
