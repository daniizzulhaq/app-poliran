# PROGRES POLIRAN

## Status tahap

- [x] Tahap 1 — Dasar: setup, auth, 5 role + permission, master data, akun admin + kecamatan, layout, komponen, file private, akun demo, semua migration
- [x] Tahap 2 — Instansi & survei
- [x] Tahap 3 — Lowongan
- [x] Tahap 4 — Pencari kerja & Admin Lapangan
- [ ] Tahap 5 — Pencocokan & penyaluran
- [ ] Tahap 6 — Pelatihan
- [ ] Tahap 7 — Landing page & halaman publik
- [ ] Tahap 8 — Dashboard, laporan, keamanan, dokumentasi

## Keputusan (opsi paling sederhana bila spesifikasi tidak jelas)

1. **Data wilayah contoh**: `database/data/wilayah_tts.csv` berisi nama *placeholder* ("Kecamatan Contoh A", "Desa Contoh A1", dst.) agar tidak mengarang nama kecamatan/desa TTS. Ganti dengan data asli, format: `kode_kecamatan,nama_kecamatan,kode_desa,nama_desa`.
2. **Logo**: `public/images/logo-polres.svg` adalah logo sementara. Ganti dengan logo resmi (nama file sama, atau ubah di `resources/views/components/logo.blade.php`).
3. **Zona waktu**: Asia/Makassar (WITA).
4. **Database lokal & test**: MySQL/MariaDB (`poliran` dan `poliran_test`), karena driver SQLite tidak tersedia di PHP lokal.
5. **Composer platform** dikunci ke PHP 8.3 agar dependency cocok dengan hosting cPanel.
6. **Setelah daftar akun**, pengguna diarahkan ke dashboard (atau ke halaman yang tadi dituju, mis. form pendaftaran pelatihan). Dashboard menampilkan ajakan melengkapi profil / data verifikasi.
7. **Admin Lapangan + izin pelatihan**: Super Admin mencentang "Boleh membuat & mengelola pelatihan" pada akun Admin Lapangan (permission langsung `kelola-pelatihan`).
8. **Pendaftar yang diinput admin** tidak punya akun login (`pencari_kerja.user_id` = null).
