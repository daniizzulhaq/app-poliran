<?php

namespace App\Policies;

use App\Enums\StatusPenyaluran;
use App\Models\Penyaluran;
use App\Models\User;

class PenyaluranPolicy
{
    /** Hasil penyaluran dicatat admin yang berwenang atas kecamatan pendaftar. */
    public function catatHasil(User $user, Penyaluran $penyaluran): bool
    {
        $penyaluran->loadMissing('pencariKerja:id,kecamatan_id');

        return $user->can('kelola-penyaluran')
            && $penyaluran->status === StatusPenyaluran::Diproses
            && $user->bolehKelolaKecamatan($penyaluran->pencariKerja->kecamatan_id);
    }
}
