<?php

namespace App\Policies;

use App\Enums\StatusInstansi;
use App\Models\Instansi;
use App\Models\User;

class InstansiPolicy
{
    /** Lihat data & berkas (KTP/NIB, selfie): pemilik atau petugas survei. */
    public function view(User $user, Instansi $instansi): bool
    {
        return $user->id === $instansi->user_id || $user->can('survei-instansi');
    }

    /** Pemilik boleh mengubah/mengirim ulang data selama belum terverifikasi. */
    public function update(User $user, Instansi $instansi): bool
    {
        return $user->id === $instansi->user_id && $instansi->status !== StatusInstansi::Terverifikasi;
    }

    public function survei(User $user, Instansi $instansi): bool
    {
        return $user->can('survei-instansi') && $instansi->status === StatusInstansi::MenungguSurvey;
    }
}
