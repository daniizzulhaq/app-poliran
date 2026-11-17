<?php

namespace App\Exports;

use App\Models\PencariKerja;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromQuery;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithTitle;

/** Daftar pendaftar. NIK tetap disensor. */
class DaftarPendaftarSheet implements FromQuery, ShouldAutoSize, WithHeadings, WithMapping, WithTitle
{
    /** @param array{status?: string|null, kecamatan_id?: int|null} $filter */
    public function __construct(private User $user, private array $filter = []) {}

    public function title(): string
    {
        return 'Daftar Pendaftar';
    }

    public function query(): Builder
    {
        return PencariKerja::visibleTo($this->user)
            ->with(['kecamatan:id,nama', 'desa:id,nama'])
            ->when($this->filter['status'] ?? null, fn ($q, $s) => $q->where('status', $s))
            ->when($this->filter['kecamatan_id'] ?? null, fn ($q, $k) => $q->where('kecamatan_id', $k))
            ->orderBy('kecamatan_id')
            ->orderBy('nama');
    }

    public function headings(): array
    {
        return ['Nama', 'NIK', 'Jenis Kelamin', 'Umur', 'Pendidikan', 'Kecamatan', 'Desa', 'Status', 'Tanggal Daftar'];
    }

    /** @param PencariKerja $p */
    public function map($p): array
    {
        return [
            $p->nama,
            $p->nikTersensor(),
            $p->jenis_kelamin->label(),
            $p->umur(),
            $p->pendidikan->label(),
            $p->kecamatan->nama,
            $p->desa->nama,
            $p->status->label(),
            $p->created_at->format('d/m/Y'),
        ];
    }
}
