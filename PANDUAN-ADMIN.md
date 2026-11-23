# Panduan Admin POLIRAN

Masuk di `/masuk`. Menu di sisi kiri menyesuaikan peran Anda. Semua informasi status cukup dilihat di dashboard; aplikasi **tidak** mengirim WhatsApp/email otomatis — petugas menghubungi sendiri lewat tombol WA/telepon.

---

## Super Admin

**Chat Pencari Kerja** — kotak masuk khusus Admin Utama (Super Admin). Pencari kerja memilih lowongan tersedia dan menekan **Chat Admin**; satu percakapan dibuat untuk tiap pasangan akun–lowongan, dengan pesan minat awal. Semua Super Admin dapat melayani percakapan ini. Admin Survei, Admin Lapangan, dan instansi tidak memiliki akses chat.

Buka percakapan untuk membalas pesan, memeriksa kemampuan, pengalaman, pendidikan, CV, dan skor kecocokan dengan lowongan tersebut. **Lihat Detail Lengkap** membuka profil pendaftar; **Kelola Lowongan & Penyaluran** melanjutkan proses kerja yang sudah ada. Skor merupakan panduan awal, bukan keputusan penerimaan otomatis.

**Rekrut & Teruskan ke Admin Lapangan** di halaman chat memasukkan pencari kerja ke tahap *Merekrut*. Tujuan otomatis mengikuti kecamatan domisili pencari kerja, bukan lokasi lowongan. Nama petugas aktif ditampilkan; bila satu kecamatan ditangani beberapa Admin Lapangan, semuanya melihat antrean wilayah yang sama. Pencari kerja menerima keterangan tindak lanjut di chat. Admin Lapangan melihatnya pada panel **Rekrutmen dari Admin Utama** di dashboard dan menu **Penyaluran**. Bila belum ada petugas aktif di wilayah itu, rekrutmen tetap tercatat dan Super Admin mendapat pemberitahuan untuk menetapkan petugas melalui **Akun Admin**.

Pesan baru diperiksa setiap 3 detik tanpa memuat ulang halaman; kotak masuk setiap 5 detik dan jumlah pesan belum dibaca setiap 10 detik. Chat lama tetap dapat dilanjutkan setelah lowongan ditutup. Pemeriksaan otomatis berhenti sementara ketika tab tidak terlihat dan mencoba kembali saat koneksi pulih.

**Akun Admin** — tambah admin, pilih peran:
- *Admin Survei*: menyurvei instansi & lowongan.
- *Admin Lapangan*: centang kecamatan tugasnya (boleh lebih dari satu). Centang "Boleh membuat & mengelola pelatihan" bila perlu.
- Nonaktifkan akun yang tidak dipakai (hilangkan centang "Akun aktif").

**Kecamatan & Desa / Kategori / Skill** — data master. Kecamatan & desa TTS sudah terisi sesuai kode Kemendagri (32 kecamatan); bila ada pemekaran, klik **Sinkronkan Data Wilayah**. Klik nama kecamatan untuk mengelola desanya. Data yang sudah dipakai tidak bisa dihapus.

**Pelatihan** — Buat Pelatihan (tersimpan sebagai *Draft*) → buka detail → ubah status ke *Dibuka* agar tampil di halaman publik. *Ditutup* = pendaftaran berhenti, *Selesai* = pelatihan selesai.

**Konten Halaman Awal & Cerita Sukses** — ubah judul hero, sambutan & foto Kapolres, penjelasan program, kontak (termasuk titik peta), dan testimoni.

**Laporan & Export** — rekap per kecamatan & status, serta tabel **Bertahan Bekerja** (berapa persen pekerja tersalur yang masih bekerja di bulan ke-1, 3, 6). Unduh Excel (rekap + daftar pendaftar, NIK disensor) atau PDF.

Super Admin juga bisa melakukan semua tugas admin lain untuk semua kecamatan.

---

## Admin Survei

**Survei Instansi**
1. Buka tab *Menunggu Survei*, klik **Survei**.
2. Periksa data, nama dan tanggal lahir penanggung jawab sesuai KTP (usia minimal 18 tahun dan sudah memiliki KTP), serta lokasi di peta (tombol **Buka di Google Maps** untuk navigasi ke lokasi). Pendaftaran baru tidak meminta unggahan KTP/NIB maupun selfie.
3. Isi catatan survei, pilih **ACC** atau **Tolak** (alasan wajib — akan dibaca instansi agar bisa memperbaiki dan mengirim ulang).

**Jemput bola** — usaha yang Anda datangi langsung dan belum punya akun:
1. **Survei Instansi** → **+ Tambah Instansi (Jemput Bola)**: isi data, nama dan tanggal lahir penanggung jawab sesuai KTP (usia minimal 18 tahun), pin lokasi, dan catatan survei. Tidak perlu unggahan KTP/NIB atau selfie. Instansi langsung *Terverifikasi*.
2. Di detail instansi (atau menu **Lowongan** → **+ Input Lowongan**) isi lowongannya. Lowongan langsung *Aktif* atas nama Anda.

**Survei Lowongan**
1. Klik **Survei** pada lowongan.
2. Periksa: gaji wajar? tidak meminta uang dari pelamar? bukan penipuan?
3. Lowongan **luar TTS** atau **luar negeri** diberi tanda kuning/merah dan daftar periksa (kontrak tertulis, biaya perjalanan tidak dipotong gaji, KTP/ijazah asli tidak ditahan; luar negeri: izin P3MI resmi dan visa kerja). ACC hanya bisa setelah Anda mencentang bahwa legalitas sudah diperiksa.
4. ACC (lowongan jadi *Aktif*) atau Tolak dengan alasan.

Lowongan yang sudah aktif bisa ditutup manual dengan tombol **Tutup Lowongan**.

---

## Admin Lapangan

Anda hanya melihat pendaftar dari kecamatan tugas Anda.

**Pendaftar**
- Pendaftar baru yang mendaftar sendiri otomatis muncul di sini (status *Terdaftar*).
- **Tambah Pendaftar Manual** untuk warga yang tidak bisa mendaftar sendiri (tanpa akun login). Jelaskan dulu bahwa datanya disimpan untuk program POLIRAN, lalu centang **persetujuan warga** dan pilih caranya (lisan / tertulis) — wajib.
- Filter status **Belum diproses > 30 hari** (juga di dashboard) menampilkan warga yang lama tidak ada perkembangan, supaya tidak terlupakan.
- Di detail pendaftar: lihat data, CV, riwayat status; **Ubah Status** (catatan wajib — semua perubahan tercatat).
- Alur status: Terdaftar → Direkrut → Dilatih → Bersertifikat / Non Bersertifikat → Disalurkan.

**Pencocokan & Penyaluran**
Rekrutmen dari Super Admin masuk otomatis ke wilayah kecamatan domisili pencari kerja. Buka panel **Rekrutmen dari Admin Utama** di dashboard atau filter yang sama pada menu **Penyaluran**, periksa detail pekerja, hubungi pekerja/instansi, lalu lanjutkan tahapnya. Pekerja dari kecamatan lain tetap tidak dapat dibuka atau diproses.

1. Menu **Lowongan** → buka lowongan aktif → tab **Pendaftar Cocok** (urut skor 0–100, dengan rincian kategori/skill/pendidikan/umur). Atau dari detail pendaftar, panel **Lowongan Cocok**.
2. Klik **Salurkan ke lowongan ini** → penyaluran masuk tahap *Merekrut* (status pendaftar *Direkrut*).
3. Hubungi instansi & pendaftar lewat tombol **WA (pesan siap kirim)** / Telepon di menu **Penyaluran**. Pesan WhatsApp sudah terisi sesuai tahap; periksa lalu tekan kirim.
4. Pilih **tahap berikutnya** di menu Penyaluran:
   - **Melatih – bersertifikat** bila dilatih lembaga/instansi formal (status pendaftar *Bersertifikat*), atau **Melatih – non bersertifikat** bila tidak (status *Non Bersertifikat*). Tahap ini boleh dilewati.
   - **Menyalurkan (diterima kerja)** → isi tanggal mulai kerja (dan gaji bila tahu). Status pendaftar *Disalurkan*, jumlah tersalur lowongan +1, lowongan otomatis *Terpenuhi* bila kuota penuh.
   - **Tidak diterima (batal)** → bisa dipilih di tahap mana pun sebelum Menyalurkan; pendaftar bisa disalurkan ke lowongan lain.
   - Status pendaftar tidak pernah diturunkan: pendaftar yang sudah *Bersertifikat* dari pelatihan tetap *Bersertifikat* saat direkrut.

**Tindak Lanjut Kerja**
1. Menu **Tindak Lanjut Kerja** → tab *Jatuh tempo* berisi pekerja yang sudah 1, 3, atau 6 bulan bekerja dan belum ditanya kabarnya.
2. Hubungi lewat **WA pekerja (pesan siap kirim)** atau telepon.
3. Catat hasil: **Masih bekerja**, **Berhenti bekerja** (alasan wajib), atau **Tidak bisa dihubungi**.
4. Pekerja yang berhenti otomatis kembali ke daftar pencari kerja (status pelatihan/sertifikatnya tetap) dan bisa dicocokkan dengan lowongan lain.

**Peserta Pelatihan**
1. Pilih pelatihan → **Terima** / **Tolak** peserta (diterima → status pendaftar *Dilatih*).
2. Setelah pelatihan, isi hasil **Lulus** / **Tidak Lulus**.
   - Lulus + pelatihan bersertifikat → sertifikat PDF dibuat otomatis, status *Bersertifikat*.
   - Tidak lulus / pelatihan tanpa sertifikat → status *Non Bersertifikat*.
3. Pendaftar yang sudah ikut pelatihan otomatis ikut dicocokkan dengan lowongan sesuai kategori pelatihannya.

---

## Untuk dijelaskan ke masyarakat

- **Pencari kerja / pelatihan**: Daftar dengan nama dan tanggal lahir sesuai KTP (usia minimal 18 tahun dan sudah memiliki KTP) → lengkapi profil (NIK, alamat di TTS, minat, kemampuan, CV) → pantau status, pelatihan, dan unduh sertifikat di dashboard.
- **Instansi**: Daftar dengan nama dan tanggal lahir sesuai KTP (usia minimal 18 tahun dan sudah memiliki KTP) → isi data verifikasi + pin lokasi, tanpa unggahan dokumen atau selfie → tunggu survei → setelah terverifikasi upload lowongan → lihat status lowongan, jumlah tersalur, dan nama pekerja yang disalurkan.
- Lowongan aktif yang sudah disetujui, belum melewati batas tanggal, dan masih memiliki kuota tampil di beranda, menu **Lowongan**, dan dashboard pencari kerja. Lowongan yang masih disurvei atau ditolak belum tampil. Pencari kerja melengkapi profil lalu memilih **Chat Admin** untuk berkonsultasi dengan Admin Utama; penyaluran tetap dilakukan petugas.
- Program **gratis**. Petugas tidak pernah meminta uang.
- **Waspada calo & perdagangan orang**: jangan serahkan KTP/ijazah asli, kerja luar negeri hanya lewat perusahaan penempatan (P3MI) resmi, laporkan perekrut mencurigakan ke Polres atau 110. Materi ini juga tampil di halaman awal dan halaman Tentang.

## Keamanan data

- NIK ditampilkan lengkap pada daftar dan detail pendaftar bagi admin berwenang, termasuk panel profil di chat Super Admin. Ekspor Excel tetap memakai NIK tersensor.
- Berkas KTP/NIB, selfie, CV, dan sertifikat tidak bisa dibuka publik; hanya pemilik dan petugas berwenang.
- Jangan membagikan password. Ganti password lewat menu **Ganti Password** di bagian atas.
