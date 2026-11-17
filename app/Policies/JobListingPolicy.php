<?php

namespace App\Policies;

use App\Enums\StatusLowongan;
use App\Models\JobListing;
use App\Models\User;

/** Lowongan hanya terlihat oleh admin dan instansi pemiliknya. */
class JobListingPolicy
{
    public function view(User $user, JobListing $lowongan): bool
    {
        return $user->can('lihat-lowongan') || $this->pemilik($user, $lowongan);
    }

    /** Hanya instansi terverifikasi yang boleh upload lowongan. */
    public function create(User $user): bool
    {
        return $user->can('kelola-lowongan-sendiri') && (bool) $user->instansi?->isTerverifikasi();
    }

    /** Diubah pemilik selama belum disetujui (menunggu survei / ditolak); perubahan dikirim ulang untuk survei. */
    public function update(User $user, JobListing $lowongan): bool
    {
        return $this->pemilik($user, $lowongan)
            && (bool) $user->instansi?->isTerverifikasi()
            && in_array($lowongan->status, [StatusLowongan::MenungguSurvey, StatusLowongan::Ditolak], true);
    }

    /** Tutup manual oleh instansi pemilik atau admin (Super Admin / Admin Survei). */
    public function tutup(User $user, JobListing $lowongan): bool
    {
        $bolehTutup = in_array($lowongan->status, [StatusLowongan::Aktif, StatusLowongan::MenungguSurvey], true);

        return $bolehTutup && ($this->pemilik($user, $lowongan) || $user->can('survei-lowongan'));
    }

    public function survei(User $user, JobListing $lowongan): bool
    {
        return $user->can('survei-lowongan') && $lowongan->status === StatusLowongan::MenungguSurvey;
    }

    /** Menyalurkan pendaftar ke lowongan ini. */
    public function salurkan(User $user, JobListing $lowongan): bool
    {
        return $user->can('kelola-penyaluran') && $lowongan->isAktif() && $lowongan->sisaKuota() > 0;
    }

    private function pemilik(User $user, JobListing $lowongan): bool
    {
        return $user->can('kelola-lowongan-sendiri') && $user->instansi && $user->instansi->id === $lowongan->instansi_id;
    }
}
