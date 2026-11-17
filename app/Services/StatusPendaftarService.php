<?php

namespace App\Services;

use App\Enums\StatusPendaftar;
use App\Models\PencariKerja;
use App\Models\User;
use Illuminate\Support\Facades\DB;

/** Satu-satunya pintu perubahan status pendaftar. Setiap perubahan dicatat di riwayat_status. */
class StatusPendaftarService
{
    public function ubah(PencariKerja $pencari, StatusPendaftar $baru, ?User $oleh, ?string $catatan = null): void
    {
        if ($pencari->status === $baru) {
            return;
        }

        DB::transaction(function () use ($pencari, $baru, $oleh, $catatan) {
            $lama = $pencari->status;
            $pencari->update(['status' => $baru]);
            $pencari->riwayatStatus()->create([
                'status_lama' => $lama,
                'status_baru' => $baru,
                'user_id' => $oleh?->id,
                'catatan' => $catatan,
            ]);
        });
    }

    /** Catat status awal (terdaftar) saat pendaftar baru dibuat. */
    public function catatAwal(PencariKerja $pencari, ?User $oleh, ?string $catatan = null): void
    {
        $pencari->riwayatStatus()->create([
            'status_lama' => null,
            'status_baru' => $pencari->status,
            'user_id' => $oleh?->id,
            'catatan' => $catatan,
        ]);
    }
}
