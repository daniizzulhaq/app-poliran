<?php

namespace App\Enums;

enum StatusPenyaluran: string
{
    use Concerns\HasOptions;

    case Diproses = 'diproses';
    case Diterima = 'diterima';
    case TidakDiterima = 'tidak_diterima';

    public function label(): string
    {
        return match ($this) {
            self::Diproses => 'Diproses',
            self::Diterima => 'Diterima',
            self::TidakDiterima => 'Tidak Diterima',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Diproses => 'yellow',
            self::Diterima => 'green',
            self::TidakDiterima => 'red',
        };
    }
}
