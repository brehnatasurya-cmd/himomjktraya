# Arsitektur Keamanan Siber (Cybersecurity Architecture)
## Himpunan Musyawarah OSIS (HIMO) Mojokerto Raya

Sistem website dan backend HIMO Mojokerto Raya dibangun menggunakan prinsip **Security by Design** dan **Defense-in-Depth** untuk memitigasi kerentanan umum seperti yang tercantum dalam standar OWASP Top 10.

---

## 1. Autentikasi & Manajemen Sesi

### A. Algoritma Hashing Kata Sandi
- Menggunakan fungsi bawaan `password_hash()` dengan algoritma **Bcrypt** (cost parameter 12) atau **Argon2id**.
- Penyimpanan password hash di database menggunakan kolom `VARCHAR(255)`.
- Menggunakan `password_verify()` dengan penanganan waktu konstan untuk mencegah serangan timing attack.
- Menerapkan fungsi otomatis `password_needs_rehash()` pada setiap proses login untuk menjamin pembaruan konfigurasi hash di masa depan secara transparan.

### B. Keamanan Cookie Sesi
Setiap sesi web dimulai dengan konfigurasi `session_set_cookie_params()` yang ketat:
- `HttpOnly = true`: Mencegah pembacaan session ID oleh skrip JavaScript pihak ketiga (mitigasi pencurian cookie via XSS).
- `SameSite = Lax`: Melindungi pengguna dari serangan Cross-Site Request Forgery (CSRF).
- `Secure = true` (ketika mendeteksi koneksi HTTPS): Mencegah kebocoran cookie melalui jaringan tidak terenkripsi.
- Regenerasi ID Sesi: Dilakukan pemanggilan `session_regenerate_id(true)` setiap kali pengguna berhasil login untuk mencegah serangan Session Fixation.

### C. Mitigasi Brute Force & Rate Limiting
- Sistem membatasi percobaan login maksimal **5 kali per 15 menit per alamat IP**.
- Jika ambang batas terlampaui, alamat IP tersebut akan terkunci secara sementara dan menerima kode status HTTP `429 Too Many Requests`.

---

## 2. Proteksi Terhadap Serangan Web Umum

### A. SQL Injection (SQLi)
- Seluruh interaksi database dilakukan melalui **PDO (PHP Data Objects)** dengan **Prepared Statements** dan parameter binding yang ketat.
- Opsi `PDO::ATTR_EMULATE_PREPARES` disetel ke `false` untuk memastikan preparasi query dilakukan secara native di tingkat RDBMS MySQL, bukan simulasi emulator PHP.
- Tidak ada perangkaian string langsung (string concatenation) pada query SQL yang berasal dari parameter pengguna.

### B. Cross-Site Scripting (XSS)
- Seluruh data dinamis yang ditampilkan ke antarmuka pengguna difilter melalui `htmlspecialchars($str, ENT_QUOTES, 'UTF-8')`.
- Content-Security-Policy (CSP) diterapkan pada HTTP header untuk membatasi domain eksekusi skrip dan pemuatan font/gambar eksternal.

### C. Cross-Site Request Forgery (CSRF)
- Setiap formulir atau permintaan yang memodifikasi status server (POST) wajib menyertakan token kriptografis acak `csrf_token` sepanjang 32 byte (`bin2hex(random_bytes(32))`).
- Token divalidasi menggunakan fungsi waktu-konstan `hash_equals()`.

---

## 3. Keamanan Unggah Berkas (File Upload Security)

Unggahan foto profil pengurus, gambar berita, logo, dan hero landing page dilindungi oleh lapisan validasi ketat:
1. **Validasi Tipe MIME Nyata**: Membaca header biner berkas menggunakan ekstensi `FileInfo` (`finfo_file(FILEINFO_MIME_TYPE)`), bukan hanya memeriksa ekstensi nama berkas yang mudah dipalsukan.
2. **Whitelist Ekstensi**: Hanya mengizinkan berkas `image/jpeg` (.jpg), `image/png` (.png), dan `image/webp` (.webp).
3. **Penyimpanan Acak**: Nama berkas diubah secara otomatis menjadi string acak kriptografis (`bin2hex(random_bytes(16)) . '_' . time() . '.' . $ext`).
4. **Isolasi Eksekusi Skrip**: Direktori `uploads/` dilengkapi dengan berkas `.htaccess` yang secara eksplisit menolak eksekusi semua skrip server (`.php`, `.phtml`, `.cgi`, `.py`, dll).
5. **Batasan Ukuran**: Ukuran unggahan dibatasi maksimal 5 Megabyte untuk mencegah serangan Resource Exhaustion / Denial of Service.

---

## 4. Konfigurasi HTTP Security Headers

Dikonfigurasi melalui berkas `.htaccess` di akar proyek:

| Header | Nilai | Tujuan Keamanan |
|---|---|---|
| `X-Content-Type-Options` | `nosniff` | Mencegah browser menebak (sniffing) MIME type yang berbeda |
| `X-Frame-Options` | `SAMEORIGIN` | Mencegah pembingkaian iframe untuk mitigasi Clickjacking |
| `X-XSS-Protection` | `1; mode=block` | Mengaktifkan filter XSS bawaan peramban lawas |
| `Referrer-Policy` | `strict-origin-when-cross-origin` | Melindungi informasi privasi path URL saat menuju situs luar |
| `Permissions-Policy` | `geolocation=(), microphone=(), camera=()` | Menonaktifkan izin API sensitif browser |
| `Content-Security-Policy` | Sumber terdaftar | Membatasi skrip & sumber daya hanya ke origin tepercaya |

---

## 5. Audit Trail & Logging

Tabel `activity_logs` mencatat seluruh peristiwa penting di balik layar:
- Upaya login sukses dan logout.
- Modifikasi, penambahan, dan penghapusan data pengurus.
- Publikasi dan penghapusan berita.
- Pembaruan konfigurasi kontak dan identitas.
- Parameter yang dicatat: ID pengguna, jenis aksi, deskripsi, alamat IP pengunjung, dan user agent peramban.
