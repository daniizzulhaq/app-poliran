<?php

namespace App\Policies;

use App\Models\PesertaPelatihan;
use App\Models\User;

class PesertaPelatihanPolicy
{
    /** Terima/tolak & isi hasil: admin berwenang atas kecamatan pendaftar. */
    public function kelola(User $user, PesertaPelatihan $peserta): bool
    {
        $peserta->loadMissing('pencariKerja:id,kecamatan_id');

        return $user->can('kelola-peserta-pelatihan') && $user->bolehKelolaKecamatan($peserta->pencariKerja->kecamatan_id);
    }

    /** Sertifikat: pemiliknya sendiri, atau admin berwenang. */
    public function unduhSertifikat(User $user, PesertaPelatihan $peserta): bool
    {
        $peserta->loadMissing('pencariKerja:id,user_id,kecamatan_id');

        return ($peserta->pencariKerja->user_id !== null && $peserta->pencariKerja->user_id === $user->id)
            || $this->kelola($user, $peserta);
    }
}
