# RENCANA TEKNIS POLIRAN — Polisi Peduli Pengangguran (Polres TTS)

Stack: Laravel 12 · PHP 8.3 · MySQL/MariaDB · Blade + Tailwind 4 + Alpine.js · Spatie Permission · Leaflet/OSM · DomPDF · Laravel Excel · Pest.

## 1. Struktur Folder (yang ditambahkan)

```
app/
  Console/Commands/TutupLowonganKedaluwarsa.php
  Enums/            Role, StatusInstansi, JenisInstansi, JenisDokumen, StatusLowongan, TipeKerja,
                    Pendidikan, JenisKelamin, StatusPendaftar, StatusPenyaluran, StatusPelatihan, StatusPeserta
  Exports/          RekapPendaftarExport
  Http/
    Controllers/
      Auth/         LoginController, PasswordResetController, RegisterPencariKerjaController, RegisterInstansiController
      Admin/        Dashboard, AdminUser, Kecamatan, Desa, Kategori, Skill, SurveiInstansi, SurveiLowongan,
                    Lowongan, Pendaftar, Penyaluran, Pelatihan, PesertaPelatihan, Konten, Testimoni, Laporan
      Instansi/     Dashboard, Verifikasi, Lowongan
      PencariKerja/ Dashboard, Profil, DaftarPelatihan, Sertifikat
      Public/       Home, Pelatihan, Halaman
      BerkasController (unduh file private lewat Policy), WilayahController (JSON desa per kecamatan)
    Requests/       satu FormRequest per aksi tulis
  Models/           User, Kecamatan, Desa, Kategori, Skill, Instansi, JobListing, PencariKerja,
                    RiwayatStatus, Penyaluran, Pelatihan, PesertaPelatihan, Pengaturan, Testimoni
  Policies/         InstansiPolicy, JobListingPolicy, PencariKerjaPolicy, PenyaluranPolicy,
                    PelatihanPolicy, PesertaPelatihanPolicy
  Services/         InstansiService, JobListingService, PencariKerjaService, StatusPendaftarService,
                    JobMatcher, PenyaluranService, PelatihanService, SertifikatService,
                    StatistikService, PengaturanService, FileService
  View/Components/  (komponen anonim di resources/views/components)
database/
  data/wilayah_tts.csv      (kecamatan,desa — contoh, data asli diisi pemilik)
  seeders/                  RolePermission, Wilayah, Kategori, Skill, AkunDemo, Konten, DataDemo
resources/views/
  layouts/  app (admin/dashboard, sidebar per role), public, auth
  components/ button, input, select, textarea, card, badge, alert, stat, modal, map-picker, map-view, nik, empty, ...
  admin/ instansi/ pencari/ public/ auth/ pdf/ errors/
docs/ RENCANA.md, PROGRES.md
```

## 2. Tabel

Semua tabel punya `id` (bigint) dan `created_at/updated_at` kecuali disebut lain.

| Tabel | Kolom | Relasi |
|---|---|---|
| users | name, email (unik), phone, password, is_active (bool), persetujuan_data_at (null), email_verified_at, remember_token | hasOne instansi, hasOne pencariKerja, belongsToMany kecamatan (admin_kecamatan) |
| password_reset_tokens, sessions, cache, cache_locks, jobs, job_batches, failed_jobs | bawaan Laravel (`jobs` = antrian, BUKAN lowongan) | |
| roles, permissions, model_has_roles, model_has_permissions, role_has_permissions | Spatie | |
| kecamatan | kode (null, unik), nama (unik) | hasMany desa |
| desa | kecamatan_id, kode (null), nama; unik (kecamatan_id, nama) | belongsTo kecamatan |
| admin_kecamatan | user_id, kecamatan_id (PK gabungan) | pivot Admin Lapangan ↔ kecamatan |
| kategori | nama (unik), slug (unik) | |
| skill | nama (unik) | |
| instansi | user_id (unik), nama, jenis, penanggung_jawab, alamat, no_wa, jenis_dokumen (ktp/nib), nomor_dokumen, file_dokumen (private), file_selfie (private), latitude, longitude, status, catatan_survei, alasan_tolak, disurvei_oleh (users), disurvei_pada, diajukan_pada | belongsTo user, hasMany jobListings |
| job_listings | instansi_id, kategori_id, judul, jobdesk, pendidikan_min (null), umur_min, umur_max, jenis_kelamin (null), pengalaman, syarat_lain, gaji_min, gaji_max, gaji_nego (bool), fasilitas, ketentuan_lain, tipe_kerja, lokasi_kerja, kuota, jumlah_tersalur (default 0), batas_tanggal, status, catatan_survei, alasan_tolak, disurvei_oleh, disurvei_pada, ditutup_oleh, ditutup_pada | belongsTo instansi & kategori, belongsToMany skill, hasMany penyaluran |
| job_listing_skill | job_listing_id, skill_id (PK gabungan) | |
| pencari_kerja | user_id (null, unik — null bila diinput admin), nama, nik (unik, 16 digit), jenis_kelamin, tanggal_lahir, pendidikan, jurusan, no_wa, kecamatan_id, desa_id, alamat_detail, kemampuan_lain, pengalaman_kerja, cv_path (private), status, dibuat_oleh (users, null) | belongsTo user/kecamatan/desa, belongsToMany kategori & skill, hasMany riwayatStatus, penyaluran, pesertaPelatihan |
| kategori_pencari_kerja | pencari_kerja_id, kategori_id | |
| pencari_kerja_skill | pencari_kerja_id, skill_id | |
| riwayat_status | pencari_kerja_id, status_lama (null), status_baru, user_id (null = sistem/pendaftar), catatan, created_at | |
| penyaluran | job_listing_id, pencari_kerja_id (unik gabungan), status, catatan, disalurkan_oleh, diproses_oleh (null), hasil_pada (null) | |
| pelatihan | kategori_id, kecamatan_id, nama, slug (unik), deskripsi, penyelenggara (default "Polres Timor Tengah Selatan"), mitra (null), tanggal_mulai, tanggal_selesai (null), jam_mulai, jam_selesai (null), alamat, kuota, syarat, batas_pendaftaran, bersertifikat (bool), poster (public), status, dibuat_oleh | hasMany peserta |
| peserta_pelatihan | pelatihan_id, pencari_kerja_id (unik gabungan), alasan, pengalaman_bidang, status, catatan, nomor_sertifikat (null, unik), sertifikat_path (private, null), diproses_oleh | |
| pengaturan | kunci (unik), nilai (longtext) | konten landing: hero, sambutan, program, kontak |
| testimoni | nama, keterangan, isi, foto (public, null), urutan, aktif | cerita sukses |

## 3. Enum (PHP backed string, method `label()` & `color()`)

- **Role**: super_admin, admin_survey, admin_lapangan, instansi, pencari_kerja
- **StatusInstansi**: menunggu_survey, terverifikasi, ditolak
- **JenisInstansi**: perusahaan, umkm, perorangan, pemerintah
- **JenisDokumen**: ktp, nib
- **StatusLowongan**: menunggu_survey, aktif, ditolak, terpenuhi, ditutup, kedaluwarsa
- **TipeKerja**: tetap, kontrak, harian, part_time
- **Pendidikan**: tidak_sekolah, sd, smp, sma, d3, s1, s2 (+ `tingkat()` untuk perbandingan)
- **JenisKelamin**: L, P
- **StatusPendaftar**: terdaftar, direkrut, dilatih, bersertifikat, non_bersertifikat, disalurkan
- **StatusPenyaluran**: diproses, diterima, tidak_diterima
- **StatusPelatihan**: draft, dibuka, ditutup, selesai
- **StatusPeserta**: mendaftar, diterima, ditolak, lulus, tidak_lulus

## 4. Permission (Spatie)

| Permission | super_admin | admin_survey | admin_lapangan | instansi | pencari_kerja |
|---|---|---|---|---|---|
| kelola-admin | ✓ | | | | |
| kelola-master-data | ✓ | | | | |
| kelola-konten | ✓ | | | | |
| lihat-laporan | ✓ | | | | |
| survei-instansi | ✓ | ✓ | | | |
| survei-lowongan | ✓ | ✓ | | | |
| lihat-lowongan | ✓ | ✓ | ✓ | | |
| kelola-pendaftar | ✓ | | ✓ (kecamatannya) | | |
| kelola-penyaluran | ✓ | | ✓ (kecamatannya) | | |
| kelola-pelatihan | ✓ | | opsional (diberi Super Admin per akun) | | |
| kelola-peserta-pelatihan | ✓ | | ✓ (kecamatannya) | | |
| kelola-lowongan-sendiri | | | | ✓ | |
| daftar-pelatihan | | | | | ✓ |

Pembatasan per kecamatan: `PencariKerja::scopeVisibleTo(User)` + `PencariKerjaPolicy` (dan Policy turunan untuk penyaluran, peserta, CV).

## 5. Aturan bisnis kunci

- Lowongan tampil hanya untuk admin & instansi pemilik. Kedaluwarsa: command harian `lowongan:tutup-kedaluwarsa` + scope `aktif()` mengecek `batas_tanggal >= hari ini` + pembaruan saat daftar lowongan dibuka.
- JobMatcher: kategori 40, skill 40 × (sama/diminta; 40 jika lowongan tanpa skill), pendidikan 10 (10 jika tanpa syarat), umur 10. Filter jenis kelamin; pendaftar `disalurkan` dikeluarkan. Kategori dari minat + kategori pelatihan yang diikuti (diterima/lulus).
- Penyaluran diterima → pendaftar `disalurkan`, `jumlah_tersalur` +1, lowongan `terpenuhi` jika `jumlah_tersalur >= kuota` (dalam transaksi + lockForUpdate).
- Pelatihan: peserta diterima → pendaftar `dilatih`; lulus & bersertifikat → `bersertifikat` (+PDF); tidak lulus / tanpa sertifikat → `non_bersertifikat`.
