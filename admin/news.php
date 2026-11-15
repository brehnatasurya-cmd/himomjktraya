<?php
$pageTitle = 'Informasi & Berita';
require_once __DIR__ . '/includes/header.php';
require_once __DIR__ . '/../api/config/database.php';

$newsList = [];
$categories = [];

try {
    $db = getDB();

    $stmt = $db->query("
        SELECT n.*, c.name as category_name, u.name as author_name
        FROM news n
        JOIN news_categories c ON n.category_id = c.id
        LEFT JOIN users u ON n.author_id = u.id
        ORDER BY n.id DESC
    ");
    $newsList = $stmt->fetchAll();

    $categories = $db->query("SELECT id, name FROM news_categories ORDER BY id ASC")->fetchAll();

} catch (\Exception $e) {
    error_log("[News Admin Error] " . $e->getMessage());
}
?>

<div class="admin-header">
  <div>
    <h1 class="admin-page-title">Manajemen Informasi & Dokumentasi</h1>
    <p class="admin-page-subtitle">Publikasi siaran pers, dokumentasi kegiatan, dan artikel edukasi HIMO.</p>
  </div>
  <div>
    <button class="btn-admin btn-admin-primary" onclick="openAddNewsModal()">
      <i class="fas fa-plus"></i> Tulis Berita Baru
    </button>
  </div>
</div>

<div class="admin-card">
  <div class="admin-card-header">
    <h2 class="admin-card-title">Daftar Publikasi (<?php echo count($newsList); ?> Artikel)</h2>
  </div>
  <div class="admin-table-wrap">
    <table class="admin-table">
      <thead>
        <tr>
          <th>Gambar</th>
          <th>Judul Berita</th>
          <th>Kategori</th>
          <th>Status</th>
          <th>Penulis</th>
          <th>Tanggal</th>
          <th style="text-align: right;">Aksi</th>
        </tr>
      </thead>
      <tbody>
        <?php if (empty($newsList)): ?>
          <tr>
            <td colspan="7" style="text-align: center; padding: 32px; color: var(--admin-text-soft);">
              Belum ada artikel berita yang dipublikasikan.
            </td>
          </tr>
        <?php else: ?>
          <?php foreach ($newsList as $item): ?>
            <tr>
              <td>
                <?php if (!empty($item['featured_image'])): ?>
                  <img src="../<?php echo htmlspecialchars($item['featured_image'], ENT_QUOTES, 'UTF-8'); ?>" alt="" style="width: 48px; height: 32px; border-radius: 4px; object-fit: cover;" />
                <?php else: ?>
                  <div style="width: 48px; height: 32px; border-radius: 4px; background: #E2E8F0; display: grid; place-items: center; color: #64748B; font-size: 0.75rem;">
                    <i class="fas fa-image"></i>
                  </div>
                <?php endif; ?>
              </td>
              <td>
                <strong><?php echo htmlspecialchars($item['title'], ENT_QUOTES, 'UTF-8'); ?></strong>
                <div style="font-size: 0.78rem; color: var(--admin-text-soft); margin-top: 2px;">
                  Slug: <code><?php echo htmlspecialchars($item['slug'], ENT_QUOTES, 'UTF-8'); ?></code>
                </div>
              </td>
              <td><span class="badge badge-info"><?php echo htmlspecialchars($item['category_name'], ENT_QUOTES, 'UTF-8'); ?></span></td>
              <td>
                <?php if ($item['status'] === 'published'): ?>
                  <span class="badge badge-success">Diterbitkan</span>
                <?php else: ?>
                  <span class="badge badge-warning">Draft</span>
                <?php endif; ?>
              </td>
              <td style="font-size: 0.85rem;"><?php echo htmlspecialchars($item['author_name'] ?? 'Admin', ENT_QUOTES, 'UTF-8'); ?></td>
              <td style="font-size: 0.82rem; white-space: nowrap;">
                <?php echo $item['published_at'] ? date('d M Y', strtotime($item['published_at'])) : date('d M Y', strtotime($item['created_at'])); ?>
              </td>
              <td style="text-align: right; white-space: nowrap;">
                <button class="btn-admin btn-admin-outline btn-admin-sm" onclick='editNews(<?php echo json_encode($item, JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP); ?>)'>
                  <i class="fas fa-pencil-alt"></i> Edit
                </button>
                <button class="btn-admin btn-admin-danger btn-admin-sm" onclick="deleteNews(<?php echo $item['id']; ?>, '<?php echo addslashes($item['title']); ?>')">
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

<!-- MODAL ADD/EDIT NEWS -->
<div class="modal-backdrop" id="modal-news">
  <div class="modal-card" style="max-width: 740px;">
    <div class="modal-header">
      <h3 class="modal-title" id="modal-news-title">Tulis Berita Baru</h3>
      <button class="modal-close" onclick="closeModal('modal-news')">&times;</button>
    </div>
    <form id="form-news" enctype="multipart/form-data">
      <input type="hidden" name="csrf_token" value="<?php echo htmlspecialchars($csrfToken, ENT_QUOTES, 'UTF-8'); ?>" />
      <input type="hidden" name="id" id="news-id" value="" />

      <div class="modal-body">
        <div class="form-group">
          <label class="form-label" for="news-title">Judul Berita *</label>
          <input type="text" id="news-title" name="title" class="form-control" required placeholder="Contoh: Rapat Kerja Daerah HIMO 2026" />
        </div>

        <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 16px;">
          <div class="form-group">
            <label class="form-label" for="news-category">Kategori *</label>
            <select id="news-category" name="category_id" class="form-control" required>
              <option value="">-- Pilih Kategori --</option>
              <?php foreach ($categories as $cat): ?>
                <option value="<?php echo $cat['id']; ?>"><?php echo htmlspecialchars($cat['name'], ENT_QUOTES, 'UTF-8'); ?></option>
              <?php endforeach; ?>
            </select>
          </div>

          <div class="form-group">
            <label class="form-label" for="news-status">Status Publikasi *</label>
            <select id="news-status" name="status" class="form-control" required>
              <option value="published">Diterbitkan (Published)</option>
              <option value="draft">Simpan Sebagai Draft</option>
            </select>
          </div>
        </div>

        <div class="form-group">
          <label class="form-label" for="news-excerpt">Ringkasan Singkat (Excerpt)</label>
          <textarea id="news-excerpt" name="excerpt" class="form-control" style="min-height: 60px;" placeholder="Ringkasan 1-2 kalimat untuk preview card..."></textarea>
        </div>

        <div class="form-group">
          <label class="form-label" for="news-body">Isi Lengkap Berita *</label>
          <textarea id="news-body" name="body" class="form-control" style="min-height: 180px;" required placeholder="Tuliskan isi berita atau dokumentasi kegiatan di sini..."></textarea>
        </div>

        <div class="form-group">
          <label class="form-label" for="news-image">Foto Utama (Featured Image)</label>
          <input type="file" id="news-image" name="featured_image" class="form-control" accept="image/png, image/jpeg, image/webp" />
        </div>
      </div>

      <div class="modal-footer">
        <button type="button" class="btn-admin btn-admin-outline" onclick="closeModal('modal-news')">Batal</button>
        <button type="submit" class="btn-admin btn-admin-primary" id="btn-save-news">Simpan Artikel</button>
      </div>
    </form>
  </div>
</div>

<script>
  function openAddNewsModal() {
    document.getElementById('modal-news-title').textContent = 'Tulis Berita Baru';
    document.getElementById('form-news').reset();
    document.getElementById('news-id').value = '';
    openModal('modal-news');
  }

  function editNews(item) {
    document.getElementById('modal-news-title').textContent = 'Edit Artikel Berita';
    document.getElementById('news-id').value = item.id;
    document.getElementById('news-title').value = item.title;
    document.getElementById('news-category').value = item.category_id;
    document.getElementById('news-status').value = item.status;
    document.getElementById('news-excerpt').value = item.excerpt;
    document.getElementById('news-body').value = item.body;
    openModal('modal-news');
  }

  document.getElementById('form-news').addEventListener('submit', async function(e) {
    e.preventDefault();
    const isEdit = Boolean(document.getElementById('news-id').value);
    const endpoint = isEdit ? '../api/news/update.php' : '../api/news/store.php';

    const formData = new FormData(this);
    const btn = document.getElementById('btn-save-news');
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
      btn.innerHTML = 'Simpan Artikel';
    }
  });

  async function deleteNews(id, title) {
    if (!confirm('Apakah Anda yakin ingin menghapus artikel "' + title + '"?')) {
      return;
    }

    try {
      const res = await fetch('../api/news/delete.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/json' },
        body: JSON.stringify({ id: id, csrf_token: '<?php echo $csrfToken; ?>' })
      });
      const data = await res.json();
      if (data.success) {
        alert('Artikel berhasil dihapus.');
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
