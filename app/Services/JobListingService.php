<?php

namespace App\Services;

use App\Enums\StatusLowongan;
use App\Models\Instansi;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class JobListingService
{
    /**
     * Buat / ubah lowongan. Setiap simpan dikirim (ulang) ke antrian survei.
     *
     * @param  array<string, mixed>  $data
     */
    public function simpan(Instansi $instansi, array $data, ?JobListing $lowongan = null): JobListing
    {
        return DB::transaction(function () use ($instansi, $data, $lowongan) {
            $atribut = collect($data)->except('skills')->all();
            $atribut['gaji_nego'] = (bool) ($data['gaji_nego'] ?? false);
            $atribut['status'] = StatusLowongan::MenungguSurvey;
            $atribut['alasan_tolak'] = null;
            $atribut['legalitas_diperiksa_pada'] = null;

            $lowongan ??= new JobListing(['instansi_id' => $instansi->id]);
            $lowongan->fill($atribut)->save();
            $lowongan->skills()->sync($data['skills'] ?? []);

            return $lowongan;
        });
    }

    /**
     * Hasil survei. Lowongan luar TTS / luar negeri hanya boleh di-ACC setelah petugas memeriksa legalitasnya
     * (rawan calo dan perdagangan orang).
     */
    public function putuskan(JobListing $lowongan, User $surveyor, bool $diterima, ?string $catatan, ?string $alasanTolak, bool $legalitasDiperiksa = false): void
    {
        $perluCek = $lowongan->wilayah_kerja->perluCekLegalitas();
        if ($diterima && $perluCek && ! $legalitasDiperiksa) {
            throw ValidationException::withMessages([
                'legalitas_diperiksa' => 'Lowongan '.mb_strtolower($lowongan->wilayah_kerja->label()).' hanya bisa di-ACC setelah legalitasnya diperiksa. Centang daftar periksa.',
            ]);
        }

        $lowongan->update([
            'legalitas_diperiksa_pada' => $diterima && $perluCek ? now() : null,
            'status' => $diterima ? StatusLowongan::Aktif : StatusLowongan::Ditolak,
            'catatan_survei' => $catatan,
            'alasan_tolak' => $diterima ? null : $alasanTolak,
            'disurvei_oleh' => $surveyor->id,
            'disurvei_pada' => now(),
        ]);
    }

    /**
     * Lowongan yang didapat petugas langsung dari instansi (jemput bola): petugas sudah memeriksa,
     * jadi lowongan langsung aktif dengan petugas sebagai surveyor.
     *
     * @param  array<string, mixed>  $data
     */
    public function buatOlehPetugas(Instansi $instansi, array $data, User $petugas): JobListing
    {
        return DB::transaction(function () use ($instansi, $data, $petugas) {
            $lowongan = $this->simpan($instansi, collect($data)->except(['instansi_id', 'legalitas_diperiksa', 'catatan_survei'])->all());
            $lowongan->update(['dibuat_oleh' => $petugas->id]);

            $catatan = trim('Diinput petugas (jemput bola). '.($data['catatan_survei'] ?? ''));
            $this->putuskan($lowongan, $petugas, true, $catatan, null, (bool) ($data['legalitas_diperiksa'] ?? false));

            return $lowongan;
        });
    }

    public function tutup(JobListing $lowongan, User $oleh): void
    {
        $lowongan->update([
            'status' => StatusLowongan::Ditutup,
            'ditutup_oleh' => $oleh->id,
            'ditutup_pada' => now(),
        ]);
    }
}
