<?php

namespace App\Services;

use App\Enums\CaraPersetujuan;
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
            $atribut = collect($data)->except(['kategori', 'skills', 'cv', 'persetujuan', 'persetujuan_cara'])->all();

            if ($baru) {
                $pencari = new PencariKerja([
                    'user_id' => $akun?->id,
                    'status' => StatusPendaftar::Terdaftar,
                    'dibuat_oleh' => $oleh->id,
                ]);
            }

            $pencari->fill($atribut);
            $this->catatPersetujuan($pencari, $data, $baru ? $akun : null);
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

    /**
     * Persetujuan data (UU PDP): pendaftar mandiri sudah mencentang saat membuat akun;
     * pendaftar yang diinput petugas dicatat dari formulir (lisan / tertulis). Tidak pernah ditimpa.
     *
     * @param  array<string, mixed>  $data
     */
    private function catatPersetujuan(PencariKerja $pencari, array $data, ?User $akunBaru): void
    {
        if ($pencari->persetujuan_pada) {
            return;
        }

        if ($akunBaru) {
            $pencari->persetujuan_cara = CaraPersetujuan::Daring;
            $pencari->persetujuan_pada = $akunBaru->created_at ?? now();
        } elseif (! empty($data['persetujuan']) && ! empty($data['persetujuan_cara'])) {
            $pencari->persetujuan_cara = CaraPersetujuan::from($data['persetujuan_cara']);
            $pencari->persetujuan_pada = now();
        }
    }
}
