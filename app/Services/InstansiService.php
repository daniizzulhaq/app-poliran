<?php

namespace App\Services;

use App\Enums\StatusInstansi;
use App\Models\Instansi;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class InstansiService
{
    public function __construct(private FileService $files) {}

    /**
     * Simpan data verifikasi dan (kembali) ajukan untuk disurvei.
     *
     * @param  array<string, mixed>  $data
     */
    public function ajukan(User $user, array $data, ?UploadedFile $dokumen, ?UploadedFile $selfie): Instansi
    {
        return DB::transaction(function () use ($user, $data, $dokumen, $selfie) {
            $instansi = $user->instansi ?? new Instansi(['user_id' => $user->id]);

            $instansi->fill([
                'nama' => $data['nama'],
                'jenis' => $data['jenis'],
                'penanggung_jawab' => $data['penanggung_jawab'],
                'alamat' => $data['alamat'],
                'no_wa' => $data['no_wa'],
                'jenis_dokumen' => $data['jenis_dokumen'],
                'nomor_dokumen' => $data['nomor_dokumen'],
                'latitude' => $data['latitude'],
                'longitude' => $data['longitude'],
                'status' => StatusInstansi::MenungguSurvey,
                'alasan_tolak' => null,
                'diajukan_pada' => now(),
            ]);

            if ($dokumen) {
                $instansi->file_dokumen = $this->files->simpanPrivate($dokumen, 'instansi/dokumen', $instansi->file_dokumen);
            }
            if ($selfie) {
                $instansi->file_selfie = $this->files->simpanPrivate($selfie, 'instansi/selfie', $instansi->file_selfie);
            }

            $instansi->save();

            return $instansi;
        });
    }

    /**
     * Instansi yang didatangi langsung oleh petugas (jemput bola): tanpa akun, langsung terverifikasi.
     *
     * @param  array<string, mixed>  $data
     */
    public function buatOlehPetugas(User $petugas, array $data, UploadedFile $dokumen, UploadedFile $selfie): Instansi
    {
        return DB::transaction(function () use ($petugas, $data, $dokumen, $selfie) {
            return Instansi::create([
                'user_id' => null,
                'dibuat_oleh' => $petugas->id,
                'nama' => $data['nama'],
                'jenis' => $data['jenis'],
                'penanggung_jawab' => $data['penanggung_jawab'],
                'alamat' => $data['alamat'],
                'no_wa' => $data['no_wa'],
                'jenis_dokumen' => $data['jenis_dokumen'],
                'nomor_dokumen' => $data['nomor_dokumen'],
                'file_dokumen' => $this->files->simpanPrivate($dokumen, 'instansi/dokumen'),
                'file_selfie' => $this->files->simpanPrivate($selfie, 'instansi/selfie'),
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
