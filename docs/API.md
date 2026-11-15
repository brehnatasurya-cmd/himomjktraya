# Dokumentasi API — HIMO Mojokerto Raya Backend

Sistem backend HIMO Mojokerto Raya menyediakan RESTful API berbasis PHP PDO dengan format pertukaran data JSON. Semua endpoint dilindungi oleh standar keamanan seperti proteksi CSRF, sanitasi input, validasi tipe MIME untuk berkas unggahan, dan rate limiting.

---

## 1. Konvensi Format Respon

Setiap endpoint API mengembalikan format JSON yang konsisten:

```json
{
  "success": true,
  "message": "Pesan deskriptif hasil operasi",
  "timestamp": "2026-05-23T10:00:00+07:00",
  "data": { ... }
}
```

Jika terjadi kesalahan:
```json
{
  "success": false,
  "message": "Deskripsi kesalahan yang aman (tanpa stack trace)",
  "timestamp": "2026-05-23T10:00:00+07:00"
}
```

---

## 2. Autentikasi (`/api/auth/`)

### A. Login Admin
- **Endpoint**: `POST /api/auth/login.php`
- **Rate Limit**: Maksimal 5 percobaan per 15 menit per alamat IP.
- **Header**: `Content-Type: application/json` atau `multipart/form-data`
- **Parameter**:
  - `email` (string, required): Alamat email admin
  - `password` (string, required): Kata sandi
  - `csrf_token` (string, required): Token CSRF yang valid
- **Respon Sukses (200)**:
  ```json
  {
    "success": true,
    "message": "Login berhasil.",
    "data": {
      "user": { "id": 1, "name": "Administrator HIMO", "email": "admin@himo.id", "role": "super_admin" },
      "redirect": "../admin/dashboard.php"
    }
  }
  ```

### B. Logout
- **Endpoint**: `POST /api/auth/logout.php`
- **Keterangan**: Menghapus sesi server, meregenerasi session cookie kedaluwarsa, dan mencatat log aktivitas.

### C. Status Sesi & CSRF Token
- **Endpoint**: `GET /api/auth/check.php`
- **Respon**: Mengembalikan status keaktifan sesi saat ini serta token CSRF terbaru.

---

## 3. Manajemen Struktur Pengurus (`/api/members/`)

### A. Daftar Pengurus
- **Endpoint**: `GET /api/members/index.php`
- **Parameter Query (Opsional)**:
  - `department_id` (int): Filter berdasarkan ID departemen
  - `level` (string): Filter berdasarkan tingkat hierarki (`delegasi`, `pimpinan`, `bph`, `departemen_koor`, `departemen_anggota`)
  - `all` (boolean): Jika disertakan, menampilkan pengurus nonaktif juga (khusus admin).

### B. Tambah Pengurus
- **Endpoint**: `POST /api/members/store.php`
- **Autentikasi**: Wajib (Admin Sesi)
- **Parameter**:
  - `name` (string, required): Nama lengkap pengurus
  - `position_id` (int, required): ID jabatan
  - `department_id` (int, optional): ID departemen
  - `school_origin` (string): Nama sekolah asal
  - `social_instagram` (string): Akun Instagram
  - `order_index` (int): Urutan tampilan
  - `photo` (file, optional): Berkas gambar (JPG, PNG, WebP maks 5MB)
  - `csrf_token` (string, required)

### C. Ubah Pengurus
- **Endpoint**: `POST /api/members/update.php`
- **Autentikasi**: Wajib
- **Parameter**: Mirip dengan create, disertai `id` (int, required) dan `is_active` (0 atau 1).

### D. Hapus Pengurus
- **Endpoint**: `POST /api/members/delete.php`
- **Autentikasi**: Wajib
- **Parameter**: `id` (int, required), `csrf_token` (string, required)

---

## 4. Publikasi Informasi & Berita (`/api/news/`)

### A. Daftar Berita
- **Endpoint**: `GET /api/news/index.php`
- **Parameter Query**:
  - `page` (int, default 1)
  - `limit` (int, default 9)
  - `category` (string, slug kategori)
  - `search` (string, kata kunci pencarian)
  - `status` (string, default `published`)

### B. Detail Berita
- **Endpoint**: `GET /api/news/show.php`
- **Parameter Query**: `slug` (string) atau `id` (int)
- **Fitur Otomatis**: Menginkrementasi jumlah pembaca (`views_count`), memuat galeri foto kegiatan, dan 3 berita terkait.

### C. Tambah Berita
- **Endpoint**: `POST /api/news/store.php`
- **Autentikasi**: Wajib
- **Parameter**: `title`, `category_id`, `excerpt`, `body`, `status` (`draft` atau `published`), `featured_image` (file).

### D. Ubah Berita
- **Endpoint**: `POST /api/news/update.php`
- **Autentikasi**: Wajib
- **Parameter**: `id`, `title`, `category_id`, `excerpt`, `body`, `status`, `featured_image` (file opsional).

### E. Hapus Berita
- **Endpoint**: `POST /api/news/delete.php`
- **Autentikasi**: Wajib
- **Parameter**: `id`, `csrf_token`.

---

## 5. Kontak & Pengaturan (`/api/contacts/` & `/api/settings/`)

- `GET /api/contacts/index.php`: Mengambil seluruh nomor WhatsApp narahubung, alamat, media sosial, dan Google Maps embed link.
- `POST /api/contacts/update.php`: Memperbarui data kontak (Admin wajib).
- `GET /api/settings/index.php`: Mengambil deskripsi organisasi, visi misi, statistik, path logo dan hero landing page.
- `POST /api/settings/update.php`: Memperbarui data profil, logo, dan foto hero latar depan.
