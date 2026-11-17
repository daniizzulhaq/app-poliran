<?php

namespace App\Services;

use App\Enums\StatusLowongan;
use App\Models\Instansi;
use App\Models\JobListing;
use App\Models\User;
use Illuminate\Support\Facades\DB;

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

            $lowongan ??= new JobListing(['instansi_id' => $instansi->id]);
            $lowongan->fill($atribut)->save();
            $lowongan->skills()->sync($data['skills'] ?? []);

            return $lowongan;
        });
    }

    public function putuskan(JobListing $lowongan, User $surveyor, bool $diterima, ?string $catatan, ?string $alasanTolak): void
    {
        $lowongan->update([
            'status' => $diterima ? StatusLowongan::Aktif : StatusLowongan::Ditolak,
            'catatan_survei' => $catatan,
            'alasan_tolak' => $diterima ? null : $alasanTolak,
            'disurvei_oleh' => $surveyor->id,
            'disurvei_pada' => now(),
        ]);
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
