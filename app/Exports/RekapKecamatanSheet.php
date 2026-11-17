<?php

namespace App\Exports;

use App\Enums\StatusPendaftar;
use App\Models\User;
use App\Services\StatistikService;
use Maatwebsite\Excel\Concerns\FromArray;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;

class RekapKecamatanSheet implements FromArray, ShouldAutoSize, WithHeadings, WithTitle
{
    public function __construct(private User $user) {}

    public function title(): string
    {
        return 'Per Kecamatan';
    }

    public function headings(): array
    {
        return ['Kecamatan', ...array_map(fn ($s) => $s->label(), StatusPendaftar::cases()), 'Total'];
    }

    public function array(): array
    {
        $rekap = app(StatistikService::class)->rekapKecamatan($this->user);

        $baris = $rekap->map(fn ($r) => [$r['kecamatan'], ...array_values($r['status']), $r['total']])->all();

        $total = ['TOTAL'];
        foreach (StatusPendaftar::cases() as $s) {
            $total[] = $rekap->sum(fn ($r) => $r['status'][$s->value]);
        }
        $total[] = $rekap->sum('total');
        $baris[] = $total;

        return $baris;
    }
}
