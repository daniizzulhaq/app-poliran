<?php

namespace App\Services;

use App\Enums\StatusInstansi;
use App\Models\Instansi;
use App\Models\User;
use Illuminate\Support\Facades\DB;

class InstansiService
{
    /**
     * Simpan data verifikasi dan (kembali) ajukan untuk disurvei.
     *
     * @param  array<string, mixed>  $data
     */
    public function ajukan(User $user, array $data): Instansi
    {
        return DB::transaction(function () use ($user, $data) {
            $instansi = $user->instansi ?? new Instansi(['user_id' => $user->id]);

            $instansi->fill([
                'nama' => $data['nama'],
                'jenis' => $data['jenis'],
                'penanggung_jawab' => $data['penanggung_jawab'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'alamat' => $data['alamat'],
                'no_wa' => $data['no_wa'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'status' => StatusInstansi::MenungguSurvey,
                'alasan_tolak' => null,
                'diajukan_pada' => now(),
            ]);

            $instansi->save();

            return $instansi;
        });
    }

    /**
     * Instansi yang didatangi langsung oleh petugas (jemput bola): tanpa akun, langsung terverifikasi.
     *
     * @param  array<string, mixed>  $data
     */
    public function buatOlehPetugas(User $petugas, array $data): Instansi
    {
        return DB::transaction(function () use ($petugas, $data) {
            return Instansi::create([
                'user_id' => null,
                'dibuat_oleh' => $petugas->id,
                'nama' => $data['nama'],
                'jenis' => $data['jenis'],
                'penanggung_jawab' => $data['penanggung_jawab'],
                'tanggal_lahir' => $data['tanggal_lahir'],
                'alamat' => $data['alamat'],
                'no_wa' => $data['no_wa'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'status' => StatusInstansi::Terverifikasi,
                'catatan_survei' => $data['catatan_survei'],
                'disurvei_oleh' => $petugas->id,
                'disurvei_pada' => now(),
                'diajukan_pada' => now(),
            ]);
        });
    }

    public function putuskan(Instansi $instansi, User $surveyor, bool $diterima, ?string $catatan, ?string $alasanTolak): void
    {
        $instansi->update([
            'status' => $diterima ? StatusInstansi::Terverifikasi : StatusInstansi::Ditolak,
            'catatan_survei' => $catatan,
            'alasan_tolak' => $diterima ? null : $alasanTolak,
            'disurvei_oleh' => $surveyor->id,
            'disurvei_pada' => now(),
        ]);
    }
}
