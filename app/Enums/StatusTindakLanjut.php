<?php

namespace App\Enums;

/** Hasil tindak lanjut pekerja yang sudah disalurkan. */
enum StatusTindakLanjut: string
{
    use Concerns\HasOptions;

    case MasihBekerja = 'masih_bekerja';
    case Berhenti = 'berhenti';
    case TidakTerhubung = 'tidak_terhubung';

    public function label(): string
    {
        return match ($this) {
            self::MasihBekerja => 'Masih bekerja',
            self::Berhenti => 'Berhenti bekerja',
            self::TidakTerhubung => 'Tidak bisa dihubungi',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MasihBekerja => 'green',
            self::Berhenti => 'red',
            self::TidakTerhubung => 'gray',
        };
    }
}
