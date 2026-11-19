# Panduan Deploy POLIRAN ke cPanel

Kebutuhan hosting: PHP 8.3 (ekstensi: pdo_mysql, mbstring, fileinfo, gd, zip, intl, xml), MySQL/MariaDB, akses Terminal/SSH (disarankan) dan Cron Jobs.

## 1. Siapkan di komputer lokal

```bash
composer install --no-dev --optimize-autoloader
npm ci && npm run build        # hasil di public/build (ikut di-commit/di-upload)
```

Kompres seluruh folder proyek **tanpa** `node_modules`, `.env`, dan `storage/logs/*`.

## 2. Upload & struktur folder

Disarankan kode berada di luar `public_html`:

```
/home/USER/poliran/          <- seluruh isi proyek
/home/USER/public_html/      <- isi folder poliran/public
```

1. Upload zip ke `/home/USER/poliran` lalu extract.
2. Pindahkan **isi** `poliran/public/` ke `public_html/`.
3. Ubah `public_html/index.php`, sesuaikan dua path:
   ```php
   require __DIR__.'/../poliran/vendor/autoload.php';
   $app = require_once __DIR__.'/../poliran/bootstrap/app.php';
   ```
   dan tambahkan sebelum `$app->handleRequest(...)`:
   ```php
   $app->usePublicPath(__DIR__);
   ```

(Alternatif: arahkan Document Root domain/subdomain langsung ke `poliran/public` lewat menu Domains bila tersedia — lebih sederhana.)

## 3. Database

cPanel → MySQL Databases: buat database + user, beri ALL PRIVILEGES.

## 4. File `.env`

Salin `.env.example` menjadi `.env` di folder `poliran/`, lalu ubah:

```
APP_NAME=POLIRAN
APP_ENV=production
APP_DEBUG=false
APP_URL=https://domain-anda.go.id
APP_LOCALE=id

DB_CONNECTION=mysql
DB_HOST=localhost
DB_DATABASE=nama_database
DB_USERNAME=user_database
DB_PASSWORD=password_database

SESSION_SECURE_COOKIE=true       # bila sudah HTTPS
QUEUE_CONNECTION=sync

# Email hanya untuk reset password (pakai akun email cPanel)
MAIL_MAILER=smtp
MAIL_HOST=mail.domain-anda.go.id
MAIL_PORT=465
MAIL_SCHEME=smtps
MAIL_USERNAME=noreply@domain-anda.go.id
MAIL_PASSWORD=********
MAIL_FROM_ADDRESS=noreply@domain-anda.go.id
MAIL_FROM_NAME=POLIRAN
```

## 5. Perintah awal (Terminal cPanel, di folder `poliran/`)

```bash
php artisan key:generate
php artisan migrate --force
php artisan db:seed --force          # role, permission, wilayah, kategori, skill, akun awal
php artisan storage:link             # atau lihat catatan di bawah
php artisan config:cache
php artisan route:cache
php artisan view:cache
```

Data contoh (instansi/lowongan/pelatihan fiktif) **tidak** ikut di produksi (hanya `APP_ENV=local`).

**storage:link** bila `public_html` terpisah: buat symlink manual
`ln -s /home/USER/poliran/storage/app/public /home/USER/public_html/storage`.

Hak akses: `chmod -R 775 storage bootstrap/cache`.

## 6. Cron (wajib, untuk lowongan kedaluwarsa otomatis)

cPanel → Cron Jobs → setiap menit:

```
* * * * * cd /home/USER/poliran && php artisan schedule:run >> /dev/null 2>&1
```

(Sesuaikan `php` dengan path PHP 8.3 hosting, mis. `/usr/local/bin/ea-php83`.)

## 7. Setelah online — WAJIB

1. Masuk sebagai `superadmin@poliran.test` / `password`, **ganti email & password** (menu Akun Admin) atau buat Super Admin baru lalu nonaktifkan akun demo.
2. Nonaktifkan/ubah semua akun demo lain (`survei@`, `lapangan@`, `lapangan2@`, `instansi@`, `pencari@` — akun instansi & pencari demo bisa dihapus lewat database).
3. Periksa data wilayah (lihat bawah), isi konten halaman awal, dan ganti logo.

## Data wilayah (kecamatan & desa TTS)

`database/data/wilayah_tts.csv` sudah berisi 32 kecamatan dan 278 desa/kelurahan Kabupaten Timor Tengah Selatan
(kode Kemendagri 53.02, sumber API [wilayah.id](https://wilayah.id)). `db:seed` memuatnya otomatis, tanpa perlu internet.

- Menambah kecamatan/desa baru (mis. pemekaran): menu **Kecamatan & Desa** → **Sinkronkan Data Wilayah** (server perlu akses internet),
  atau di terminal `php artisan wilayah:impor`. Hanya menambah yang belum ada; nama yang sudah diubah admin tidak ditimpa.
- Memperbarui salinan CSV di repo: `php artisan wilayah:impor --csv`.
- Sumber API bisa diganti lewat `.env`: `WILAYAH_API_URL` (default `https://wilayah.id/api`) dan `WILAYAH_KABUPATEN` (default `53.02`).

## Update aplikasi

```bash
php artisan down
# upload file baru (vendor & public/build hasil build lokal)
php artisan migrate --force
php artisan optimize:clear && php artisan config:cache && php artisan route:cache && php artisan view:cache
php artisan up
```

## Backup

Backup rutin database (cPanel Backup / phpMyAdmin export) dan folder `storage/app` (berisi KTP/NIB, selfie, CV, sertifikat — **data sensitif**, jangan dibuka publik).
