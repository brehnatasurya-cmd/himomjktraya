<?php
$pageTitle = 'Struktur Pengurus';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../api/config/database.php';

$members = [];
$departments = [];
$positions = [];

try {
    $db = getDB();

    $stmt = $db->query("
        SELECT m.*, p.name as position_name, p.level as position_level, d.name as department_name
        FROM members m
        JOIN positions p ON m.position_id = p.id
        LEFT JOIN departments d ON m.department_id = d.id
        ORDER BY p.order_index ASC, m.order_index ASC, m.id ASC
    ");
    $members = $stmt->fetchAll();

    $departments = $db->query("SELECT id, name FROM departments ORDER BY order_index ASC")->fetchAll();
    $positions = $db->query("SELECT id, name, level FROM positions ORDER BY order_index ASC")->fetchAll();

} catch (\Exception $e) {
    error_log("[Members Admin Error] " . $e->getMessage());
}
?>

<div class="admin-header">
  <div>
    <h1 class="admin-page-title">Manajemen Struktur Kepengurusan</h1>
    <p class="admin-page-subtitle">Kelola anggota, hierarki jabatan, dan departemen HIMO Mojokerto Raya.</p>
  </div>
  <div>
    <button class="btn-admin btn-admin-primary" onclick="openAddMemberModal()">
      <i class="fas fa-user-plus"></i> Tambah Pengurus
    </button>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title">Daftar Pengurus Periode 2026/2027 (<?php echo count($members); ?> Orang)</h2>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Foto</th>
          <th>Nama Lengkap</th>
          <th>Jabatan</th>
          <th>Departemen</th>
          <th>Asal Sekolah</th>
          <th>Urutan</th>
          <th>Status</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($members)): ?>
          <tr>
            <td colspan="8" style="text-align: center; padding: 32px; color: var(--admin-text-soft);">
              Belum ada data pengurus yang dimasukkan.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($members as $m): ?>
            <tr>
              <td>
                <?php if (!empty($m['photo_path'])): ?>
                  <img src="../<?php echo htmlspecialchars($m['photo_path'], ENT_QUOTES, 'UTF-8'); ?>" alt="" style="width: 36px; height: 36px; border-radius: 50%; object-fit: cover;" />
                <?php else: ?>
                  <div style="width: 36px; height: 36px; border-radius: 50%; background: #E2E8F0; display: grid; place-items: center; color: #64748B; font-size: 0.8rem;">
                    <i class="fas fa-user"></i>
                  </div>
                <?php endif; ?>
              </td>
              <td><strong><?php echo htmlspecialchars($m['name'], ENT_QUOTES, 'UTF-8'); ?></strong></td>
              <td><span class="badge badge-info"><?php echo htmlspecialchars($m['position_name'], ENT_QUOTES, 'UTF-8'); ?></span></td>
              <td><?php echo htmlspecialchars($m['department_name'] ?? 'Pimpinan Inti / BPH', ENT_QUOTES, 'UTF-8'); ?></td>
              <td style="color: var(--admin-text-soft); font-size: 0.85rem;"><?php echo htmlspecialchars($m['school_origin'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo (int)$m['order_index']; ?></td>
              <td>
                <?php if ($m['is_active']): ?>
                  <span class="badge badge-success">Aktif</span>
                <?php else: ?>
                  <span class="badge badge-warning">Nonaktif</span>
                <?php endif; ?>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <button class="btn-admin btn-admin-outline btn-admin-sm" onclick='editMember(<?php echo json_encode($m, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                  <i class="fas fa-pencil-alt"></i> Edit
                </button>
                <button class="btn-admin btn-admin-danger btn-admin-sm" onclick="deleteMember(<?php echo $m['id']; ?>, '<?php echo addslashes($m['name']); ?>')">
                  <i class="fas fa-trash"></i>
                </button>
              </td>
            </tr>
          <?php endforeach; ?>
        <?php endif; ?>
      </tbody>
    </table>
  </div>
</div>

<!-- MODAL ADD/EDIT MEMBER -->
<div class="modal-backdrop" id="modal-member">
  <div class="modal-card">
    <div class="modal-header">
      <h3 class="modal-title" id="modal-member-title">Tambah Pengurus</h3>
      <button class="modal-close" onclick="closeModal('modal-member')">&times;</button>
    </div>
    <form id="form-member" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>" />
      <input type="hidden" name="id" id="member-id" value="" />

      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="member-name">Nama Lengkap *</label>
          <input type="text" id="member-name" name="name" class="form-control" required placeholder="Contoh: Muhammad Fikri" />
        </div>

        <div class="form-group">
          <label class="form-label" for="member-position">Jabatan Organisasi *</label>
          <select id="member-position" name="position_id" class="form-control" required>
            <option value="">-- Pilih Jabatan --</option>
            <?php foreach ($positions as $p): ?>
              <option value="<?php echo $p['id']; ?>"><?php echo htmlspecialchars($p['name'], ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="member-dept">Departemen (Jika relevan)</label>
          <select id="member-dept" name="department_id" class="form-control">
            <option value="">-- Bukan Anggota Departemen / Inti --</option>
            <?php foreach ($departments as $d): ?>
              <option value="<?php echo $d['id']; ?>"><?php echo htmlspecialchars($d['name'], ENT_QUOTES, 'UTF-8'); ?></option>
            <?php endforeach; ?>
          </select>
        </div>

        <div class="form-group">
          <label class="form-label" for="member-school">Asal Sekolah</label>
          <input type="text" id="member-school" name="school_origin" class="form-control" placeholder="Contoh: SMAN 1 Puri Mojokerto" />
        </div>

        <div class="form-group">
          <label class="form-label" for="member-order">Urutan Tampilan</label>
          <input type="number" id="member-order" name="order_index" class="form-control" value="0" />
        </div>

        <div class="form-group">
          <label class="form-label" for="member-photo">Foto Profil (JPG, PNG, WebP maks 5MB)</label>
          <input type="file" id="member-photo" name="photo" class="form-control" accept="image/png, image/jpeg, image/webp" />
        </div>

        <div class="form-group" id="group-active" style="display: none;">
          <label class="form-label">Status Keaktifan</label>
          <select name="is_active" id="member-active" class="form-control">
            <option value="1">Aktif</option>
            <option value="0">Nonaktif</option>
          </select>
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-admin btn-admin-outline" onclick="closeModal('modal-member')">Batal</button>
        <button type="submit" class="btn-admin btn-admin-primary" id="btn-save-member">Simpan Data</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openAddMemberModal() {
    document.getElementById('modal-member-title').textContent = 'Tambah Pengurus Baru';
    document.getElementById('form-member').reset();
    document.getElementById('member-id').value = '';
    document.getElementById('group-active').style.display = 'none';
    openModal('modal-member');
  }

  function editMember(m) {
    document.getElementById('modal-member-title').textContent = 'Edit Data Pengurus';
    document.getElementById('member-id').value = m.id;
    document.getElementById('member-name').value = m.name;
    document.getElementById('member-position').value = m.position_id;
    document.getElementById('member-dept').value = m.department_id || '';
    document.getElementById('member-school').value = m.school_origin || '';
    document.getElementById('member-order').value = m.order_index;
    document.getElementById('member-active').value = m.is_active;
    document.getElementById('group-active').style.display = 'block';
    openModal('modal-member');
  }

  document.getElementById('form-member').addEventListener('submit', async function(e) {
    e.preventDefault();
    const isEdit = Boolean(document.getElementById('member-id').value);
    const endpoint = isEdit ? '../api/members/update.php' : '../api/members/store.php';

    const formData = new FormData(this);
    const btn = document.getElementById('btn-save-member');
    btn.disabled = true;
    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';

    try {
      const res = await fetch(endpoint, { method: 'POST', body: formData });
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
      btn.innerHTML = 'Simpan Data';
    }
  });

  async function deleteMember(id, name) {
    if (!confirm('Apakah Anda yakin ingin menghapus data pengurus "' + name + '"?')) {
      return;
    }

    try {
      const res = await fetch('../api/members/delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, csrf_token: '<?php echo $csrfToken; ?>' })
      });
      const data = await res.json();
      if (data.success) {
        alert('Data pengurus berhasil dihapus.');
        window.location.reload();
      } else {
        alert('Gagal: ' + data.message);
      }
    } catch (err) {
      alert('Terjadi kesalahan jaringan.');
    }
  }
</script>

<?php require_once __DIR__ . '/includes/footer.php'; ?>
