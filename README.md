# POLIRAN — Polisi Peduli Pengangguran (Polres Timor Tengah Selatan)

Aplikasi web Laravel 12 untuk pendataan pencari kerja, pelatihan, survei penyedia kerja, pencocokan, dan penyaluran kerja warga TTS.

- Rencana teknis: `docs/RENCANA.md`
- Progres & keputusan: `docs/PROGRES.md`
- Deploy cPanel: `DEPLOY.md`
- Panduan admin: `PANDUAN-ADMIN.md`

## Menjalankan lokal

```bash
composer install
npm install && npm run build
cp .env.example .env && php artisan key:generate   # atur DB_* (MySQL)
php artisan migrate:fresh --seed
php artisan storage:link
php artisan serve
```

Test (butuh database `poliran_test`, lihat `phpunit.xml`): `php artisan test`

## Akun demo (password: `password`)

| Peran | Email |
|---|---|
| Super Admin | superadmin@poliran.test |
| Admin Survei | survei@poliran.test |
| Admin Lapangan (Kecamatan Contoh A) | lapangan@poliran.test |
| Admin Lapangan (Kecamatan Contoh B) | lapangan2@poliran.test |
| Instansi (terverifikasi) | instansi@poliran.test |
| Pencari Kerja | pencari@poliran.test |
