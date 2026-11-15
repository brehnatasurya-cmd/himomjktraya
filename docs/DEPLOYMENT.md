# Panduan Deployment Produksi — HIMO Mojokerto Raya

Panduan ini ditujukan bagi pengurus dan administrator teknis HIMO Mojokerto Raya untuk menerbitkan website ke lingkungan hosting produksi (Shared Hosting cPanel, Niagahoster, DomaiNesia, IDCloudHost, atau VPS Linux).

---

## 1. Kebutuhan Server (System Requirements)

- **Web Server**: Apache 2.4+ (dengan modul `mod_rewrite`, `mod_headers`, `mod_security`) atau Nginx
- **PHP Version**: PHP 8.1 atau lebih tinggi (disarankan PHP 8.2 / 8.3)
- **Ekstensi PHP Wajib**:
  - `pdo` & `pdo_mysql`
  - `fileinfo` (untuk validasi MIME type berkas unggahan)
  - `json`, `mbstring`, `openssl`
  - `gd` atau `imagick`
- **Database**: MySQL 8.0+ atau MariaDB 10.4+
- **Kapasitas Penyimpanan**: Minimal 500 MB (disesuaikan dengan kebutuhan unggahan foto kegiatan)

---

## 2. Langkah Deployment di Shared Hosting (cPanel)

### Langkah 1: Siapkan Database MySQL
1. Buka cPanel akun hosting Anda.
2. Masuk ke menu **MySQL Databases**.
3. Buat database baru, misalnya: `himomjkt_db`.
4. Buat pengguna database baru, misalnya: `himomjkt_user` dengan kata sandi yang kuat dan aman.
5. Tambahkan pengguna tersebut ke database baru dengan hak akses penuh (**ALL PRIVILEGES**).

### Langkah 2: Impor Skema & Data Awal
1. Masuk ke menu **phpMyAdmin** di cPanel.
2. Pilih database `himomjkt_db` di panel sebelah kiri.
3. Klik tab **Import**.
4. Pilih file `database/schema.sql` dari repositori proyek ini, lalu klik **Go** / **Kirim**.
5. Setelah berhasil, klik tab **Import** kembali dan pilih file `database/seed.sql` untuk memasukkan data awal.

### Langkah 3: Unggah Berkas Web ke `public_html`
1. Buka **File Manager** di cPanel, masuk ke folder `public_html`.
2. Unggah seluruh isi proyek ini (atau arsipkan menjadi `.zip` lalu ekstrak di dalam `public_html`).
3. Pastikan struktur berkas berada tepat di dalam `public_html`, sehingga `index.html` dapat diakses langsung melalui domain utama (contoh: `https://himomojokerto.org/`).

### Langkah 4: Konfigurasi Environment (`.env`)
1. Di dalam File Manager `public_html`, salin berkas `.env.example` dan ubah namanya menjadi `.env`.
2. Buka dan edit file `.env` tersebut:
   ```env
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://himomojokerto.org

   DB_HOST=localhost
   DB_PORT=3306
   DB_DATABASE=himomjkt_db
   DB_USERNAME=himomjkt_user
   DB_PASSWORD=PasswordDatabaseAndaYangAman!
   ```
3. Simpan perubahan. Berkas `.htaccess` yang terpasang akan otomatis memblokir akses publik ke berkas `.env` ini.

### Langkah 5: Pengaturan Izin Direktori (Permissions)
Pastikan izin folder penyimpanan berkas unggahan disetel agar dapat ditulis oleh server web:
- Folder `uploads/` dan `uploads/images/`: `755` (atau `775`)
- Berkas PHP: `644`
- Berkas `.htaccess`: `644`

### Langkah 6: Login Perdana Administrator
1. Buka URL: `https://domainanda.com/admin/login.php`
2. Masuk menggunakan akun bawaan:
   - **Email**: `admin@himo.id`
   - **Password**: `AdminHimo2026!`
3. **PENTING**: Segera perbarui kata sandi dan email administrator setelah login pertama kali melalui database atau dashboard demi keamanan.

---

## 3. Langkah Deployment di VPS Linux (Ubuntu 22.04 / 24.04 LTS)

### 1. Install Paket
```bash
sudo apt update && sudo apt install -y apache2 mysql-server php8.2 php8.2-mysql php8.2-mbstring php8.2-fileinfo php8.2-gd php8.2-curl git
sudo a2enmod rewrite headers
sudo systemctl restart apache2
```

### 2. Kloning Repositori
```bash
cd /var/www/
sudo git clone https://github.com/himomjktraya/himomjktraya.git himo
sudo chown -R www-data:www-data /var/www/himo/uploads
sudo chmod -R 755 /var/www/himo/uploads
```

### 3. Setup VirtualHost Apache
Buat file `/etc/apache2/sites-available/himo.conf`:
```apache
<VirtualHost *:80>
    ServerName himomojokerto.org
    ServerAlias www.himomojokerto.org
    DocumentRoot /var/www/himo

    <Directory /var/www/himo>
        Options -Indexes +FollowSymLinks
        AllowOverride All
        Require all granted
    </Directory>

    ErrorLog ${APACHE_LOG_DIR}/himo_error.log
    CustomLog ${APACHE_LOG_DIR}/himo_access.log combined
</VirtualHost>
```
Aktifkan situs:
```bash
sudo a2ensite himo.conf
sudo systemctl reload apache2
```

### 4. Pasang Sertifikat SSL Gratis (Let's Encrypt Certbot)
```bash
sudo apt install -y certbot python3-certbot-apache
sudo certbot --apache -d himomojokerto.org -d www.himomojokerto.org
```

Selesai. Website resmi HIMO Mojokerto Raya siap beroperasi secara publik dan aman!
