# Panduan Admin POLIRAN

Masuk di `/masuk`. Menu di sisi kiri menyesuaikan peran Anda. Semua informasi status cukup dilihat di dashboard; aplikasi **tidak** mengirim WhatsApp/email otomatis — petugas menghubungi sendiri lewat tombol WA/telepon.

---

## Super Admin

**Akun Admin** — tambah admin, pilih peran:
- *Admin Survei*: menyurvei instansi & lowongan.
- *Admin Lapangan*: centang kecamatan tugasnya (boleh lebih dari satu). Centang "Boleh membuat & mengelola pelatihan" bila perlu.
- Nonaktifkan akun yang tidak dipakai (hilangkan centang "Akun aktif").

**Kecamatan & Desa / Kategori / Skill** — data master. Klik nama kecamatan untuk mengelola desanya. Data yang sudah dipakai tidak bisa dihapus.

**Pelatihan** — Buat Pelatihan (tersimpan sebagai *Draft*) → buka detail → ubah status ke *Dibuka* agar tampil di halaman publik. *Ditutup* = pendaftaran berhenti, *Selesai* = pelatihan selesai.

**Konten Halaman Awal & Cerita Sukses** — ubah judul hero, sambutan & foto Kapolres, penjelasan program, kontak (termasuk titik peta), dan testimoni.

**Laporan & Export** — rekap per kecamatan & status. Unduh Excel (rekap + daftar pendaftar, NIK disensor) atau PDF.

Super Admin juga bisa melakukan semua tugas admin lain untuk semua kecamatan.

---

## Admin Survei

**Survei Instansi**
1. Buka tab *Menunggu Survei*, klik **Survei**.
2. Periksa data, buka berkas KTP/NIB dan selfie, lihat lokasi di peta (tombol **Buka di Google Maps** untuk navigasi ke lokasi).
3. Isi catatan survei, pilih **ACC** atau **Tolak** (alasan wajib — akan dibaca instansi agar bisa memperbaiki dan mengirim ulang).

**Survei Lowongan**
1. Klik **Survei** pada lowongan.
2. Periksa: gaji wajar? tidak meminta uang dari pelamar? bukan penipuan?
3. ACC (lowongan jadi *Aktif*) atau Tolak dengan alasan.

Lowongan yang sudah aktif bisa ditutup manual dengan tombol **Tutup Lowongan**.

---

## Admin Lapangan

Anda hanya melihat pendaftar dari kecamatan tugas Anda.

**Pendaftar**
- Pendaftar baru yang mendaftar sendiri otomatis muncul di sini (status *Terdaftar*).
- **Tambah Pendaftar Manual** untuk warga yang tidak bisa mendaftar sendiri (tanpa akun login).
- Di detail pendaftar: lihat data, CV, riwayat status; **Ubah Status** (catatan wajib — semua perubahan tercatat).
- Alur status: Terdaftar → Direkrut → Dilatih → Bersertifikat / Non Bersertifikat → Disalurkan.

**Pencocokan & Penyaluran**
1. Menu **Lowongan** → buka lowongan aktif → tab **Pendaftar Cocok** (urut skor 0–100, dengan rincian kategori/skill/pendidikan/umur). Atau dari detail pendaftar, panel **Lowongan Cocok**.
2. Klik **Salurkan ke lowongan ini** → status penyaluran *Diproses*.
3. Hubungi instansi & pendaftar lewat tombol **WA / Telepon** di menu **Penyaluran**.
4. Catat hasil: **Diterima kerja** → status pendaftar otomatis *Disalurkan*, jumlah tersalur lowongan +1, lowongan otomatis *Terpenuhi* bila kuota penuh. **Tidak diterima** → pendaftar bisa disalurkan ke lowongan lain.

**Peserta Pelatihan**
1. Pilih pelatihan → **Terima** / **Tolak** peserta (diterima → status pendaftar *Dilatih*).
2. Setelah pelatihan, isi hasil **Lulus** / **Tidak Lulus**.
   - Lulus + pelatihan bersertifikat → sertifikat PDF dibuat otomatis, status *Bersertifikat*.
   - Tidak lulus / pelatihan tanpa sertifikat → status *Non Bersertifikat*.
3. Pendaftar yang sudah ikut pelatihan otomatis ikut dicocokkan dengan lowongan sesuai kategori pelatihannya.

---

## Untuk dijelaskan ke masyarakat

- **Pencari kerja**: Daftar → lengkapi profil (NIK, alamat di TTS, minat, kemampuan, CV) → pantau status, pelatihan, dan unduh sertifikat di dashboard.
- **Instansi**: Daftar → isi data verifikasi + selfie KTP + pin lokasi → tunggu survei → setelah terverifikasi upload lowongan → lihat status lowongan, jumlah tersalur, dan nama pekerja yang disalurkan.
- Lowongan tidak diumumkan ke publik; pencari kerja tidak melamar langsung — penyaluran dilakukan petugas.
- Program **gratis**. Petugas tidak pernah meminta uang.

## Keamanan data

- NIK selalu ditampilkan tersensor (contoh 5302********0001).
- Berkas KTP/NIB, selfie, CV, dan sertifikat tidak bisa dibuka publik; hanya pemilik dan petugas berwenang.
- Jangan membagikan password. Ganti password lewat menu **Ganti Password** di bagian atas.
