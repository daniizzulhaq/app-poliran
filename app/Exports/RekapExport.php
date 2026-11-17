<?php

namespace App\Exports;

use App\Models\User;
use Maatwebsite\Excel\Concerns\Export;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;

/** Rekap Excel: sheet per kecamatan x status + daftar pendaftar. */
class RekapExport implements Export, WithMultipleSheets
{
    /** @param array{status?: string|null, kecamatan_id?: int|null} $filter */
    public function __construct(private User $user, private array $filter = []) {}

    public function sheets(): array
    {
        return [
            new RekapKecamatanSheet($this->user),
            new DaftarPendaftarSheet($this->user, $this->filter),
        ];
    }
}
