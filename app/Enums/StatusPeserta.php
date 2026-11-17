<?php

namespace App\Enums;

enum StatusPeserta: string
{
    use Concerns\HasOptions;

    case Mendaftar = 'mendaftar';
    case Diterima = 'diterima';
    case Ditolak = 'ditolak';
    case Lulus = 'lulus';
    case TidakLulus = 'tidak_lulus';

    public function label(): string
    {
        return match ($this) {
            self::Mendaftar => 'Mendaftar',
            self::Diterima => 'Diterima',
            self::Ditolak => 'Ditolak',
            self::Lulus => 'Lulus',
            self::TidakLulus => 'Tidak Lulus',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Mendaftar => 'yellow',
            self::Diterima => 'blue',
            self::Ditolak => 'red',
            self::Lulus => 'green',
            self::TidakLulus => 'gray',
        };
    }
}
