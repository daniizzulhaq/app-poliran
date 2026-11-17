<?php

namespace Database\Seeders;

use App\Enums\StatusInstansi;
use App\Enums\StatusLowongan;
use App\Enums\StatusPelatihan;
use App\Enums\StatusPendaftar;
use App\Models\Instansi;
use App\Models\JobListing;
use App\Models\Kategori;
use App\Models\Kecamatan;
use App\Models\Pelatihan;
use App\Models\PencariKerja;
use App\Models\Skill;
use App\Models\User;
use App\Services\StatusPendaftarService;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Storage;

/**
 * Data contoh untuk mencoba aplikasi (HANYA lingkungan lokal).
 * Nama orang/usaha di sini fiktif. Jangan dijalankan di server produksi.
 */
class DataDemoSeeder extends Seeder
{
    public function run(): void
    {
        $kecamatan = Kecamatan::with('desa')->orderBy('id')->get();
        if ($kecamatan->isEmpty()) {
            return;
        }

        $bangunan = Kategori::where('nama', 'Bangunan')->first();
        $otomotif = Kategori::where('nama', 'Otomotif')->first();
        $skill = Skill::pluck('id', 'nama');
        $superAdmin = User::where('email', 'superadmin@poliran.test')->first();

        // Instansi demo terverifikasi (berkas contoh)
        Storage::disk('local')->put('instansi/dokumen/demo.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
        Storage::disk('local')->put('instansi/selfie/demo.png', base64_decode('iVBORw0KGgoAAAANSUhEUgAAAAEAAAABCAYAAAAfFcSJAAAADUlEQVR42mP8z8BQDwAEhQGAhKmMIQAAAABJRU5ErkJggg=='));
        $akunInstansi = User::where('email', 'instansi@poliran.test')->first();
        $instansi = Instansi::firstOrCreate(['user_id' => $akunInstansi->id], [
            'nama' => 'CV Contoh Maju Bersama', 'jenis' => 'umkm', 'penanggung_jawab' => 'Instansi Demo',
            'alamat' => 'Alamat contoh', 'no_wa' => '081200000000', 'jenis_dokumen' => 'nib', 'nomor_dokumen' => '1234567890123',
            'file_dokumen' => 'instansi/dokumen/demo.png', 'file_selfie' => 'instansi/selfie/demo.png',
            'latitude' => -9.8606, 'longitude' => 124.2836, 'status' => StatusInstansi::Terverifikasi,
            'catatan_survei' => 'Data demo', 'disurvei_oleh' => $superAdmin?->id, 'disurvei_pada' => now(), 'diajukan_pada' => now(),
        ]);

        $lowongan = JobListing::firstOrCreate(['instansi_id' => $instansi->id, 'judul' => 'Tukang Bangunan'], [
            'kategori_id' => $bangunan->id, 'jobdesk' => 'Membantu pembangunan rumah dan gedung.', 'pendidikan_min' => 'smp',
            'umur_min' => 18, 'umur_max' => 45, 'gaji_min' => 2500000, 'gaji_max' => 3500000, 'tipe_kerja' => 'harian',
            'lokasi_kerja' => 'SoE', 'kuota' => 3, 'batas_tanggal' => now()->addMonth(), 'status' => StatusLowongan::Aktif,
            'disurvei_oleh' => $superAdmin?->id, 'disurvei_pada' => now(),
        ]);
        $lowongan->skills()->sync($skill->only(['Tukang Batu', 'Tukang Kayu'])->values());

        JobListing::firstOrCreate(['instansi_id' => $instansi->id, 'judul' => 'Mekanik Motor'], [
            'kategori_id' => $otomotif->id, 'jobdesk' => 'Servis rutin sepeda motor pelanggan.', 'gaji_nego' => true,
            'tipe_kerja' => 'kontrak', 'lokasi_kerja' => 'SoE', 'kuota' => 1, 'batas_tanggal' => now()->addWeeks(3),
            'status' => StatusLowongan::MenungguSurvey,
        ])->skills()->sync($skill->only(['Servis Motor'])->values());

        // Pelatihan dibuka
        Pelatihan::firstOrCreate(['slug' => 'servis-motor-dasar'], [
            'kategori_id' => $otomotif->id, 'kecamatan_id' => $kecamatan->first()->id, 'nama' => 'Servis Motor Dasar',
            'deskripsi' => "Pelatihan dasar perawatan dan perbaikan sepeda motor.\nPeserta belajar langsung dengan praktik.",
            'tanggal_mulai' => now()->addWeeks(2), 'tanggal_selesai' => now()->addWeeks(2)->addDays(4), 'jam_mulai' => '08:00',
            'jam_selesai' => '15:00', 'alamat' => 'Aula (alamat contoh)', 'kuota' => 20, 'syarat' => 'Usia 17-40 tahun.',
            'batas_pendaftaran' => now()->addWeek(), 'bersertifikat' => true, 'status' => StatusPelatihan::Dibuka,
            'dibuat_oleh' => $superAdmin?->id,
        ]);

        // Pencari kerja demo (punya akun) + beberapa pendaftar per kecamatan
        $status = app(StatusPendaftarService::class);
        $akunPencari = User::where('email', 'pencari@poliran.test')->first();
        $k1 = $kecamatan->first();
        if (! $akunPencari->pencariKerja && $k1->desa->isNotEmpty()) {
            $p = PencariKerja::create([
                'user_id' => $akunPencari->id, 'nama' => 'Pencari Kerja Demo', 'nik' => '5302000000000001', 'jenis_kelamin' => 'L',
                'tanggal_lahir' => now()->subYears(24), 'pendidikan' => 'sma', 'no_wa' => '081200000000',
                'kecamatan_id' => $k1->id, 'desa_id' => $k1->desa->first()->id, 'alamat_detail' => 'RT 01 / RW 01',
                'status' => StatusPendaftar::Terdaftar, 'dibuat_oleh' => $akunPencari->id,
            ]);
            $p->kategori()->sync([$bangunan->id]);
            $p->skills()->sync($skill->only(['Tukang Batu'])->values());
            $status->catatAwal($p, $akunPencari, 'Data demo');
        }

        foreach ($kecamatan as $i => $k) {
            if ($k->desa->isEmpty()) {
                continue;
            }
            for ($n = 1; $n <= 3; $n++) {
                $nik = sprintf('53020%02d%09d', $i + 1, $n);
                if (PencariKerja::where('nik', $nik)->exists()) {
                    continue;
                }
                $p = PencariKerja::create([
                    'nama' => "Warga Demo {$k->nama} {$n}", 'nik' => $nik, 'jenis_kelamin' => $n % 2 ? 'L' : 'P',
                    'tanggal_lahir' => now()->subYears(20 + $n * 3), 'pendidikan' => ['smp', 'sma', 's1'][$n - 1],
                    'no_wa' => '08120000000'.$n, 'kecamatan_id' => $k->id, 'desa_id' => $k->desa->first()->id,
                    'alamat_detail' => 'Alamat contoh', 'status' => StatusPendaftar::Terdaftar, 'dibuat_oleh' => $superAdmin?->id,
                ]);
                $p->kategori()->sync([$n === 2 ? $otomotif->id : $bangunan->id]);
                $p->skills()->sync($skill->only($n === 2 ? ['Servis Motor'] : ['Tukang Batu', 'Tukang Kayu'])->values());
                $status->catatAwal($p, $superAdmin, 'Data demo');
            }
        }
    }
}
