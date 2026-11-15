# Website Resmi Himpunan Musyawarah OSIS (HIMO) Mojokerto Raya

[![Status](https://img.shields.io/badge/Status-Production%20Ready-success)]()
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue)]()
[![Database](https://img.shields.io/badge/Database-MySQL%20%2F%20MariaDB-orange)]()
[![Cybersecurity](https://img.shields.io/badge/Security-Defense%20in%20Depth-navy)]()
[![Design](https://img.shields.io/badge/Design-Navy%20System%20v2.0-0B1F3A)]()

Website resmi dan Content Management System (CMS) **Himpunan Musyawarah OSIS (HIMO) Mojokerto Raya** periode kepengurusan 2026/2027. Dirancang dengan tampilan modern, profesional, elegan, responsif, dan mencerminkan identitas kepemimpinan pelajar tingkat Kabupaten dan Kota Mojokerto.

---

## 🏛️ Identitas Visual & Sistem Desain

Website ini menerapkan pedoman visual resmi organisasi:
- **Primary Color**: Navy Blue (`#0B1F3A`)
- **Secondary Color**: Royal Blue (`#174A7E`)
- **Accent Color**: Sky Blue (`#55B6E8`)
- **Background**: Pure White (`#FFFFFF`) / Light Blue Gray (`#F3F6FA`)
- **Text Color**: Dark Navy (`#142338`)
- **Tipografi**: Plus Jakarta Sans
- **Interaktivitas**: Navbar adaptif (transparan di hero, 50% saat discroll, solid dengan backdrop blur), AOS (Animate On Scroll) halus, dan count-up counter statistik.

---

## 📁 Struktur Halaman Frontend

1. **Beranda (`index.html`)**:
   - Hero section berlatar foto asli pengurus dengan overlay navy transparan.
   - Headline resmi: *"Building Connections, Creating Leaders, Inspiring Generations."*
   - Sekilas profil, counter statistik dinamis (48 Pengurus, 7 Departemen, 21 Program).
   - Preview struktur hierarkis organisasi.
   - Highlight kegiatan terbaru dan peta interaktif sekretariat HIMO.
2. **Tentang Kami (`about.html`)**:
   - Profil komprehensif, visi organisasi berornamen elegan, 5 misi strategis, 4 tujuan pokok, dan 5 nilai dasar (Kolaboratif, Progresif, Inklusif, Aspiratif, Inspiratif).
3. **Struktur Organisasi (`struktur.html`)**:
   - Organizational tree hierarkis dari atas ke bawah:
     1. Delegasi Kabupaten & Kota Mojokerto
     2. Ketua Kabupaten & Kota Mojokerto
     3. Wakil Ketua Kabupaten & Kota Mojokerto
     4. Sekretaris 1 & 2
     5. Bendahara 1 & 2
     6. 7 Departemen dengan kartu pengurus rapi
4. **Informasi Terbaru (`informasi.html`)**:
   - Grid berita modern dengan fitur pencarian real-time dan penyaringan kategori (Semua, Koordinasi, Sosial, Lingkungan, Publikasi, Edukasi).
   - Halaman detail artikel dinamis:
     - `informasi/rakoor-2026.html` (Rapat Koordinasi HIMO 2026)
     - `informasi/himo-bersinar-2026.html` (Apresiasi Pelajar Berprestasi)
     - `informasi/himo-clean-up-day-2026.html` (Aksi Nyata Lingkungan)
5. **Kontak Resmi (`kontak.html`)**:
   - Kartu Contact Person narahubung lengkap dengan tombol WhatsApp resmi.
   - Tautan akun resmi Instagram, TikTok, dan YouTube HIMO.
   - Alamat kantor sekretariat dan integrasi Google Maps responsif.

---

## ⚙️ Backend, Database & Admin CMS

Sistem backend dibangun menggunakan **PHP 8.1+ PDO** dan **MySQL** yang sepenuhnya kompatibel dengan shared hosting Indonesia (cPanel):

### Panel Administrator (`/admin/`):
- **Autentikasi Aman**: Login berbasis proteksi brute force (rate limit 5x per 15 menit), password hash Bcrypt cost 12, dan token CSRF.
- **Manajemen Pengurus (`members.php`)**: Tambah, edit, hapus, atur urutan, dan unggah foto 48 pengurus.
- **Manajemen Berita (`news.php`)**: Pembuatan berita, upload gambar unggulan, manajemen status (draft/published), dan slug SEO otomatis.
- **Manajemen Kontak (`contacts.php`)**: Konfigurasi nomor WhatsApp narahubung, medsos, dan alamat sekretariat.
- **Manajemen Profil (`settings.php`)**: Penyesuaian teks visi-misi, statistik dinamis, serta pergantian logo dan hero landing page.
- **Log Keamanan (`activity-logs.php`)**: Audit trail otomatis mencatat IP, waktu, pengguna, dan aktivitas data.

### Basis Data (`/database/`):
- `database/schema.sql`: 11 tabel berelasi dengan Foreign Keys, indexing, dan timestamp (`users`, `roles`, `members`, `departments`, `positions`, `news`, `news_categories`, `news_images`, `contacts`, `site_settings`, `activity_logs`).
- `database/seed.sql`: Data awal struktur 7 departemen, level jabatan, 3 artikel berita, dan akun default super admin.

---

## 🔒 Standar Keamanan Siber (Security by Design)

- **SQL Injection Prevention**: 100% Prepared Statements melalui PDO, tanpa concatenasi query SQL.
- **Cross-Site Scripting (XSS) Prevention**: Output encoding dengan `htmlspecialchars()` dan Content-Security-Policy (CSP).
- **CSRF Protection**: Token kriptografis 32-byte pada seluruh form POST.
- **Secure Sessions**: Cookie disetel dengan `HttpOnly`, `SameSite=Lax`, regenerasi session ID pasca-login.
- **File Upload Hardening**: Validasi MIME type berbasis biner (`finfo`), ekstensi whitelist (JPG, PNG, WebP), nama berkas terenkripsi acak, dan blokade eksekusi skrip di folder `uploads/.htaccess`.
- **HTTP Security Headers**: `X-Content-Type-Options: nosniff`, `X-Frame-Options: SAMEORIGIN`, `X-XSS-Protection`, `Referrer-Policy`, dan `Permissions-Policy`.

---

## 🚀 Panduan Memulai Cepat (Quick Start)

### Persyaratan:
- Web server Apache / Nginx
- PHP 8.1 atau lebih baru
- MySQL 8.0+ atau MariaDB 10.4+

### Instalasi:
1. Impor database ke MySQL:
   ```bash
   mysql -u root -p himo_database < database/schema.sql
   mysql -u root -p himo_database < database/seed.sql
   ```
2. Salin konfigurasi environment:
   ```bash
   cp .env.example .env
   ```
   Sesuaikan kredensial database di dalam `.env`.
3. Jalankan server lokal:
   ```bash
   php -S localhost:8000
   ```
4. Buka di peramban:
   - Website Publik: `http://localhost:8000`
   - Panel Admin: `http://localhost:8000/admin/login.php`
   - **Kredensial Default**: Email: `admin@himo.id` | Sandi: `AdminHimo2026!`

---

## 📚 Dokumentasi Terperinci

- [Panduan Penggunaan Admin CMS (docs/ADMIN-GUIDE.md)](docs/ADMIN-GUIDE.md)
- [Dokumentasi RESTful API (docs/API.md)](docs/API.md)
- [Arsitektur Keamanan Siber (docs/SECURITY.md)](docs/SECURITY.md)
- [Panduan Deployment cPanel & VPS (docs/DEPLOYMENT.md)](docs/DEPLOYMENT.md)

---

&copy; 2026 Himpunan Musyawarah OSIS (HIMO) Mojokerto Raya. *The Spirit Of Majapahit Greatness.*
