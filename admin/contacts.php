<?php
$pageTitle = 'Kontak & Media Sosial';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../api/config/database.php';

$contacts = [];

try {
    $db = getDB();
    $stmt = $db->query("SELECT `key`, `value`, `label` FROM contacts");
    while ($row = $stmt->fetch()) {
        $contacts[$row['key']] = $row['value'];
    }
} catch (\Exception $e) {
    error_log("[Contacts Admin Error] " . $e->getMessage());
}
?>

<div class="admin-header">
  <div>
    <h1 class="admin-page-title">Manajemen Kontak & Media Sosial</h1>
    <p class="admin-page-subtitle">Perbarui narahubung resmi, tautan media sosial, dan alamat sekretariat organisasi.</p>
  </div>
</div>

<div class="admin-card" style="max-width: 900px;">
  <div class="admin-card-header">
    <h2 class="admin-card-title">Informasi Narahubung & Sekretariat</h2>
  </div>
  <div style="padding: 28px;">
    <form id="form-contacts">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>" />

      <!-- CONTACT PERSON 1 -->
      <h3 style="font-size: 1rem; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
        <i class="fas fa-user-circle" style="color: var(--admin-sky);"></i> Contact Person 1
      </h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
        <div class="form-group">
          <label class="form-label" for="cp1-name">Nama Narahubung</label>
          <input type="text" id="cp1-name" name="contact_person_1_name" class="form-control" value="<?php echo htmlspecialchars($contacts['contact_person_1_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div class="form-group">
          <label class="form-label" for="cp1-role">Jabatan</label>
          <input type="text" id="cp1-role" name="contact_person_1_role" class="form-control" value="<?php echo htmlspecialchars($contacts['contact_person_1_role'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div class="form-group">
          <label class="form-label" for="cp1-phone">Nomor WhatsApp (Contoh: 628123456789)</label>
          <input type="text" id="cp1-phone" name="contact_person_1_phone" class="form-control" value="<?php echo htmlspecialchars($contacts['contact_person_1_phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
      </div>

      <!-- CONTACT PERSON 2 -->
      <h3 style="font-size: 1rem; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
        <i class="fas fa-user-circle" style="color: var(--admin-sky);"></i> Contact Person 2
      </h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr 1fr; gap: 16px; margin-bottom: 24px;">
        <div class="form-group">
          <label class="form-label" for="cp2-name">Nama Narahubung</label>
          <input type="text" id="cp2-name" name="contact_person_2_name" class="form-control" value="<?php echo htmlspecialchars($contacts['contact_person_2_name'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div class="form-group">
          <label class="form-label" for="cp2-role">Jabatan</label>
          <input type="text" id="cp2-role" name="contact_person_2_role" class="form-control" value="<?php echo htmlspecialchars($contacts['contact_person_2_role'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div class="form-group">
          <label class="form-label" for="cp2-phone">Nomor WhatsApp</label>
          <input type="text" id="cp2-phone" name="contact_person_2_phone" class="form-control" value="<?php echo htmlspecialchars($contacts['contact_person_2_phone'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
      </div>

      <!-- OFFICIAL CHANNELS & SOCIAL MEDIA -->
      <h3 style="font-size: 1rem; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
        <i class="fas fa-share-alt" style="color: var(--admin-sky);"></i> Kanal Resmi & Media Sosial
      </h3>
      <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px; margin-bottom: 24px;">
        <div class="form-group">
          <label class="form-label" for="email">Email Resmi Organisasi</label>
          <input type="email" id="email" name="official_email" class="form-control" value="<?php echo htmlspecialchars($contacts['official_email'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div class="form-group">
          <label class="form-label" for="ig">Tautan Akun Instagram</label>
          <input type="url" id="ig" name="social_instagram_url" class="form-control" value="<?php echo htmlspecialchars($contacts['social_instagram_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div class="form-group">
          <label class="form-label" for="tiktok">Tautan Akun TikTok</label>
          <input type="url" id="tiktok" name="social_tiktok_url" class="form-control" value="<?php echo htmlspecialchars($contacts['social_tiktok_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
        <div class="form-group">
          <label class="form-label" for="yt">Tautan Channel YouTube</label>
          <input type="url" id="yt" name="social_youtube_url" class="form-control" value="<?php echo htmlspecialchars($contacts['social_youtube_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?>" />
        </div>
      </div>

      <!-- ADDRESS & MAPS -->
      <h3 style="font-size: 1rem; color: var(--admin-navy); margin-bottom: 16px; border-bottom: 2px solid var(--admin-border); padding-bottom: 8px;">
        <i class="fas fa-map-marker-alt" style="color: var(--admin-sky);"></i> Alamat Sekretariat & Peta
      </h3>
      <div class="form-group">
        <label class="form-label" for="address">Alamat Lengkap Kantor/Sekretariat</label>
        <textarea id="address" name="secretariat_address" class="form-control" style="min-height: 80px;"><?php echo htmlspecialchars($contacts['secretariat_address'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
      </div>
      <div class="form-group">
        <label class="form-label" for="maps">Google Maps Embed URL</label>
        <textarea id="maps" name="google_maps_embed_url" class="form-control" style="min-height: 80px;"><?php echo htmlspecialchars($contacts['google_maps_embed_url'] ?? '', ENT_QUOTES, 'UTF-8'); ?></textarea>
      </div>

      <div style="margin-top: 28px; text-align: right;">
        <button type="submit" class="btn-admin btn-admin-primary" id="btn-save-contacts" style="padding: 12px 28px;">
          <i class="fas fa-save"></i> Simpan Perubahan Kontak
        </button>
      </div>
    </form>
  </div>
</div>

<script>
  document.getElementById('form-contacts').addEventListener('submit', async function(e) {
    e.preventDefault();
    const btn = document.getElementById('btn-save-contacts');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    const formData = new FormData(this);

    try {
      const res = await fetch('../api/contacts/update.php', { method: 'POST', body: formData });
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
      btn.innerHTML = '<i class="fas fa-save"></i> Simpan Perubahan Kontak';
    }
  });
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
