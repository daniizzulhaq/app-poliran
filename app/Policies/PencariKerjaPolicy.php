<?php

namespace App\Policies;

use App\Models\PencariKerja;
use App\Models\User;

/**
 * Akses data pendaftar:
 * - Super Admin: semua kecamatan
 * - Admin Lapangan: hanya kecamatan tugasnya
 * - Pencari kerja: hanya dirinya sendiri
 */
class PencariKerjaPolicy
{
    public function view(User $user, PencariKerja $pencari): bool
    {
        return $this->dirinya($user, $pencari) || $this->adminBerwenang($user, $pencari);
    }

    public function update(User $user, PencariKerja $pencari): bool
    {
        return $this->dirinya($user, $pencari) || $this->adminBerwenang($user, $pencari);
    }

    public function ubahStatus(User $user, PencariKerja $pencari): bool
    {
        return $this->adminBerwenang($user, $pencari);
    }

    public function lihatCv(User $user, PencariKerja $pencari): bool
    {
        return $this->view($user, $pencari);
    }

    /** Menyalurkan pendaftar ke lowongan. */
    public function salurkan(User $user, PencariKerja $pencari): bool
    {
        return $user->can('kelola-penyaluran') && $user->bolehKelolaKecamatan($pencari->kecamatan_id);
    }

    private function dirinya(User $user, PencariKerja $pencari): bool
    {
        return $pencari->user_id !== null && $pencari->user_id === $user->id;
    }

    private function adminBerwenang(User $user, PencariKerja $pencari): bool
    {
        return $user->can('kelola-pendaftar') && $user->bolehKelolaKecamatan($pencari->kecamatan_id);
    }
}
