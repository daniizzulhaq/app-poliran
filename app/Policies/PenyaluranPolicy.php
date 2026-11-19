<?php

namespace App\Policies;

use App\Enums\StatusPenyaluran;
use App\Models\Penyaluran;
use App\Models\User;

class PenyaluranPolicy
{
    /** Tahap penyaluran diubah admin yang berwenang atas kecamatan pendaftar, selama belum selesai. */
    public function ubahTahap(User $user, Penyaluran $penyaluran): bool
    {
        $penyaluran->loadMissing('pencariKerja:id,kecamatan_id');

        return $user->can('kelola-penyaluran')
            && ! $penyaluran->status->selesai()
            && $user->bolehKelolaKecamatan($penyaluran->pencariKerja->kecamatan_id);
    }

    /** Tindak lanjut pekerja yang sudah disalurkan, oleh admin kecamatan pendaftar. */
    public function tindakLanjut(User $user, Penyaluran $penyaluran): bool
    {
        $penyaluran->loadMissing('pencariKerja:id,kecamatan_id');

        return $user->can('kelola-penyaluran')
            && $penyaluran->status === StatusPenyaluran::Menyalurkan
            && $user->bolehKelolaKecamatan($penyaluran->pencariKerja->kecamatan_id);
    }
}
