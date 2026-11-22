# PROGRES POLIRAN

## Status tahap

- [x] Tahap 1 — Dasar: setup, auth, 5 role + permission, master data, akun admin + kecamatan, layout, komponen, file private, akun demo, semua migration
- [x] Tahap 2 — Instansi & survei
- [x] Tahap 3 — Lowongan
- [x] Tahap 4 — Pencari kerja & Admin Lapangan
- [x] Tahap 5 — Pencocokan & penyaluran
- [x] Tahap 6 — Pelatihan
- [x] Tahap 7 — Landing page & halaman publik
- [x] Tahap 8 — Dashboard, laporan & export, review keamanan, halaman error, DEPLOY.md, PANDUAN-ADMIN.md

Test: 135 test Pest hijau di MySQL (termasuk smoke test semua halaman untuk setiap akun demo dan 12 test chat/rekrutmen lowongan). Uji browser mencakup pesan dua arah tanpa reload, NIK lengkap, penerusan rekrutmen ke Admin Lapangan sesuai domisili, pembatasan wilayah, dan tampilan desktop/ponsel.

## Keputusan (opsi paling sederhana bila spesifikasi tidak jelas)

1. **Data wilayah**: 32 kecamatan & 278 desa/kelurahan TTS dari API wilayah.id (kode Kemendagri 53.02; API emsifa tidak dipakai karena memakai kode BPS, di sana 5302 = Sumba Timur). Disalin ke `database/data/wilayah_tts.csv` agar seeding tidak butuh internet. `wilayah:impor` / tombol Sinkronkan hanya menambah data baru, cocokkan berdasarkan kode lalu nama, dan tidak menimpa nama yang diubah admin. Nama huruf kapital dirapikan ("KI'E" → "Ki'e").
2. **Logo**: `public/images/logo-polres.svg` adalah logo sementara. Ganti dengan logo resmi (nama file sama, atau ubah di `resources/views/components/logo.blade.php`).
3. **Zona waktu**: Asia/Makassar (WITA).
4. **Database lokal & test**: MySQL/MariaDB (`poliran` dan `poliran_test`), karena driver SQLite tidak tersedia di PHP lokal.
5. **Composer platform** dikunci ke PHP 8.3 agar dependency cocok dengan hosting cPanel. `public/build` ikut di-commit agar hosting tidak perlu Node.js.
6. **Setelah daftar akun**, pengguna diarahkan ke dashboard (atau ke halaman yang tadi dituju, mis. form pendaftaran pelatihan). Dashboard menampilkan ajakan melengkapi profil / data verifikasi.
7. **Admin Lapangan + izin pelatihan**: Super Admin mencentang "Boleh membuat & mengelola pelatihan" pada akun Admin Lapangan (permission langsung `kelola-pelatihan`). Admin tersebut bisa mengelola semua pelatihan; seleksi peserta tetap dibatasi kecamatannya.
8. **Pendaftar yang diinput admin** tidak punya akun login (`pencari_kerja.user_id` = null).
9. **Data instansi** baru dibuat saat formulir verifikasi dikirim. Selama *menunggu survei* data tidak bisa diubah; setelah *ditolak* bisa diperbaiki & dikirim ulang; *terverifikasi* terkunci.
10. **Ubah lowongan** hanya saat *menunggu survei* atau *ditolak*; setiap perubahan dikirim ulang ke antrian survei. Tutup manual: instansi pemilik, Super Admin, atau Admin Survei.
11. **Admin Lapangan melihat semua lowongan aktif** (lowongan tidak terikat kecamatan), tetapi pendaftar cocok/penyaluran hanya dari kecamatannya.
12. **Pencocokan**: lowongan tanpa syarat skill memberi 40 poin skill penuh; tanpa syarat pendidikan/umur memberi poin penuh. Kandidat yang ditampilkan hanya yang kategori (minat atau pelatihan yang diikuti) atau skill-nya beririsan dengan lowongan; maksimal 50 teratas. Pendaftar yang sudah pernah disalurkan ke lowongan yang sama tidak ditampilkan lagi di daftar cocok lowongan itu.
13. **Status pendaftar "disalurkan"** tidak diturunkan oleh hasil pelatihan berikutnya.
14. **Ubah status manual** oleh admin boleh ke status mana pun, catatan wajib.
15. **Satu penyaluran per pasangan lowongan–pendaftar**.
16. **Statistik publik** di-cache 10 menit. "Dilatih" = pendaftar yang pernah diterima/selesai pelatihan; "bersertifikat" = pendaftar yang punya sertifikat.
17. **Data demo** (`DataDemoSeeder`: instansi, lowongan, pelatihan, pendaftar fiktif) hanya dijalankan bila `APP_ENV=local`.
18. **Header keamanan** (X-Frame-Options, nosniff, Referrer-Policy) lewat middleware `SecurityHeaders`; disk `local` tidak disajikan langsung (`serve => false`).
19. **Tahapan penyaluran**: Merekrut → Melatih (Bersertifikat / Non Bersertifikat) → Menyalurkan; pelatihan boleh dilewati; Tidak Diterima bisa dipilih sebelum Menyalurkan. Status pendaftar ikut naik tetapi tidak pernah diturunkan (`StatusPendaftar::tingkat()`).
20. **Tindak lanjut kerja** pada bulan ke-1, 3, 6 setelah tanggal mulai kerja, berurutan, hanya setelah jatuh tempo. Berhenti bekerja (alasan wajib) → pendaftar kembali ke status sebelum disalurkan (yang dulu hanya "direkrut" kembali "terdaftar") sehingga ikut pencocokan lagi; jumlah tersalur lowongan tidak dikurangi. % bertahan = masih bekerja ÷ (masih bekerja + berhenti).
21. **Persetujuan data**: pendaftar mandiri tercatat "daring" (centang saat daftar akun); input petugas wajib centang + cara (lisan/tertulis). Data lama tanpa persetujuan ditandai "Belum tercatat" dan bisa dicatat saat ubah data.
22. **Pendaftar menunggu lama**: status terdaftar, tanpa perubahan status dan tanpa penyaluran berjalan > 30 hari (`PencariKerja::BATAS_HARI_MENUNGGU`).
23. **Lowongan luar TTS / luar negeri**: ACC wajib centang pemeriksaan legalitas (`legalitas_diperiksa_pada`); luar negeri wajib nomor izin P3MI. Menolak tidak perlu centang.
24. **Jemput bola**: Admin Survei/Super Admin bisa menginput instansi tanpa akun (`instansi.user_id` null, langsung terverifikasi) dan menginput lowongan untuk instansi terverifikasi (langsung aktif, petugas = surveyor).
25. **Pesan WA siap kirim**: tautan `wa.me/...?text=` berisi template (`App\Support\PesanWa`); aplikasi tetap tidak mengirim pesan otomatis.
26. **Lowongan publik & chat Admin Utama (22 November 2026)**: lowongan aktif, belum kedaluwarsa, dan belum penuh tampil di beranda, daftar/detail publik, serta dashboard pencari kerja. Data internal survei dan kontak penyedia kerja tidak ditampilkan di halaman lowongan publik. Pencari kerja dengan profil lengkap memulai satu chat per lowongan; pesan minat awal tersimpan otomatis. Chat hanya untuk pemilik akun pencari kerja dan Super Admin (kotak masuk bersama semua Super Admin). Profil, CV, kemampuan, dan rincian pencocokan tersedia di halaman chat admin. Pesan diperiksa tiap 3 detik, kotak masuk 5 detik, badge 10 detik melalui polling JSON tanpa reload; riwayat dimuat bertahap, pesan belum dibaca ditandai saat diambil penerima, kirim ulang memakai UUID agar tidak menggandakan pesan. Chat lama tetap dapat dipakai setelah lowongan ditutup. Jalankan migration baru dan gunakan aset `public/build` terbaru saat deploy.
27. **NIK admin & penerusan rekrutmen**: NIK tampil lengkap di daftar/detail pendaftar dan panel profil chat Super Admin. Tombol rekrut dari chat memakai alur penyaluran yang sama; rekrutmen Super Admin masuk antrean Admin Lapangan sesuai kecamatan domisili pekerja, ditampilkan di dashboard dan filter Penyaluran. Semua petugas aktif di kecamatan itu dapat menindaklanjuti sesuai kebijakan wilayah yang sudah ada. Pesan keterangan rekrutmen ditambahkan ke chat secara transaksional. Bila belum ada petugas aktif, rekrutmen tetap tercatat dan Super Admin diminta menetapkan petugas; penugasan baru langsung mengikuti antrean wilayah. Chat tetap khusus Super Admin dan pemilik akun pencari kerja.
