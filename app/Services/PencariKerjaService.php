<?php

namespace App\Services;

use App\Enums\StatusPendaftar;
use App\Models\PencariKerja;
use App\Models\User;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;

class PencariKerjaService
{
    public function __construct(private FileService $files, private StatusPendaftarService $status) {}

    /**
     * Buat / ubah data pendaftar.
     *
     * @param  array<string, mixed>  $data  data tervalidasi
     * @param  User|null  $akun  akun pencari kerja (null bila diinput admin untuk warga tanpa akun)
     * @param  User  $oleh  user yang melakukan perubahan
     */
    public function simpan(array $data, ?UploadedFile $cv, ?PencariKerja $pencari, ?User $akun, User $oleh): PencariKerja
    {
        return DB::transaction(function () use ($data, $cv, $pencari, $akun, $oleh) {
            $baru = $pencari === null;
            $atribut = collect($data)->except(['kategori', 'skills', 'cv'])->all();

            if ($baru) {
                $pencari = new PencariKerja([
                    'user_id' => $akun?->id,
                    'status' => StatusPendaftar::Terdaftar,
                    'dibuat_oleh' => $oleh->id,
                ]);
            }

            $pencari->fill($atribut);
            if ($cv) {
                $pencari->cv_path = $this->files->simpanPrivate($cv, 'cv', $pencari->cv_path);
            }
            $pencari->save();

            $pencari->kategori()->sync($data['kategori'] ?? []);
            $pencari->skills()->sync($data['skills'] ?? []);

            if ($baru) {
                $this->status->catatAwal($pencari, $oleh, $akun ? 'Mendaftar sendiri' : 'Diinput oleh admin');
            }

            if ($akun && $akun->name !== $pencari->nama) {
                $akun->update(['name' => $pencari->nama]);
            }

            return $pencari;
        });
    }
}
