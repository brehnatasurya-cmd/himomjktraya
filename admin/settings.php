<?php
$pageTitle = 'Profil & Identitas';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../api/config/database.php';

$settings = [];

try {
    $db = getDB();
    $stmt = $db->query("SELECT `key`, `value` FROM site_settings");
    while ($row = $stmt->fetch()) {
        $settings[$row['key']] = $row['value'];
    }
} catch (\Exception $e) {
    error_log("[Settings Admin Error] " . $e->getMessage());
}
?>

<div class="admin-header">
  <div>
    <h1 class="admin-page-title">Manajemen Profil & Identitas Website</h1>
    <p class="admin-page-subtitle">Ubah identitas visual, teks visi misi, statistik organisasi, dan foto hero landing page.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 900px;">
  <div class="admin-card-header">
    <h2 class="admin-card-title">Identitas & Profil Organisasi</h2>
  </div>
  <div style="padding: 28px;">
    <form id="form-settings" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>" />

      <!-- BRAND IDENTITY -->
      <h3 style="font-size: 1rem; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
        <i class="fas fa-id-card" style="color: var(--admin-sky);"></i> Identitas Organisasi
      </h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
        <div class="form-group">
          <label class="form-label" for="site-name">Nama Organisasi</label>
          <input type="text" id="site-name" name="site_name" class="form-control" value="<?php echo htmlspecialchars($settings['site_name'] ?? 'HIMO Mojokerto Raya', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div class="form-group">
          <label class="form-label" for="site-tagline">Slogan Organisasi (Tagline)</label>
          <input type="text" id="site-tagline" name="site_tagline" class="form-control" value="<?php echo htmlspecialchars($settings['site_tagline'] ?? 'The Spirit Of Majapahit Greatness', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
      </div>

      <div class="form-group" style="max-width: 300px; margin-bottom: 24px;">
        <label class="form-label" for="period">Periode Kepengurusan</label>
        <input type="text" id="period" name="period_active" class="form-control" value="<?php echo htmlspecialchars($settings['period_active'] ?? '2026/2027', ENT_QUOTES, 'UTF-8'); ?>" />
      </div>

      <!-- PROFILE & VISION -->
      <h3 style="font-size: 1rem; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
        <i class="fas fa-bullseye" style="color: var(--admin-sky);"></i> Deskripsi & Visi
      </h3>
      <div class="form-group">
        <label class="form-label" for="org-desc">Deskripsi Organisasi (About)</label>
        <textarea id="org-desc" name="org_description" class="form-control" style="min-height: 100px;"><?php echo htmlspecialchars($settings['org_description'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
      </div>
      <div class="form-group" style="margin-bottom: 24px;">
        <label class="form-label" for="org-vision">Pernyataan Visi</label>
        <textarea id="org-vision" name="vision_statement" class="form-control" style="min-height: 80px;"><?php echo htmlspecialchars($settings['vision_statement'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
      </div>

      <!-- STATISTICS -->
      <h3 style="font-size: 1rem; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
        <i class="fas fa-chart-bar" style="color: var(--admin-sky);"></i> Statistik Organisasi
      </h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
        <div class="form-group">
          <label class="form-label" for="stat-members">Jumlah Pengurus Aktif</label>
          <input type="number" id="stat-members" name="stat_members" class="form-control" value="<?php echo htmlspecialchars($settings['stat_members'] ?? '48', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div class="form-group">
          <label class="form-label" for="stat-dept">Jumlah Departemen</label>
          <input type="number" id="stat-dept" name="stat_departments" class="form-control" value="<?php echo htmlspecialchars($settings['stat_departments'] ?? '7', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div class="form-group">
          <label class="form-label" for="stat-prog">Jumlah Program Tahunan</label>
          <input type="number" id="stat-prog" name="stat_programs" class="form-control" value="<?php echo htmlspecialchars($settings['stat_programs'] ?? '21', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
      </div>

      <!-- ASSET UPLOADS -->
      <h3 style="font-size: 1rem; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
        <i class="fas fa-image" style="color: var(--admin-sky);"></i> Aset Visual (Logo & Hero)
      </h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
        <div class="form-group">
          <label class="form-label" for="logo-file">Unggah Logo Baru</label>
          <input type="file" id="logo-file" name="site_logo" class="form-control" accept="image/png, image/jpeg, image/webp" />
          <p style="font-size: 0.78rem; color: var(--admin-text-soft); margin-top: 4px;">Logo saat ini: <code><?php echo htmlspecialchars($settings['site_logo_path'] ?? 'Logo Himo Mojokerto.PNG', ENT_QUOTES, 'UTF-8'); ?></code></p>
        </div>
        <div class="form-group">
          <label class="form-label" for="hero-file">Unggah Foto Hero Landing Page</label>
          <input type="file" id="hero-file" name="hero_background" class="form-control" accept="image/png, image/jpeg, image/webp" />
          <p style="font-size: 0.78rem; color: var(--admin-text-soft); margin-top: 4px;">Foto hero saat ini: <code><?php echo htmlspecialchars($settings['hero_background_path'] ?? 'WhatsApp Image...', ENT_QUOTES, 'UTF-8'); ?></code></p>
        </div>
      </div>

      <div style="margin-top: 28px; text-align: right;">
        <button type="submit" class="btn-admin btn-admin-primary" id="btn-save-settings" style="padding: 12px 28px;">
          <i class="fas fa-save"></i> Simpan Perubahan Profil
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  document.getElementById('form-settings').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save-settings');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const formData = new FormData(this);

    try {
      const res = await fetch('../api/settings/update.php', { method: 'POST', body: formData });
      const data = await res.json();
      if (data.success) {
        alert(data.message);
        window.location.reload();
      } else {
        alert('Gagal: ' + data.message);
      }
    } catch (err) {
      alert('Terjadi kesalahan jaringan.');
    } finally {
      btn.disabled = false;
      btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan Profil';
    }
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
