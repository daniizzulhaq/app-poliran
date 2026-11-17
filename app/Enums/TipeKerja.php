<?php

namespace App\Enums;

enum TipeKerja: string
{
    use Concerns\HasOptions;

    case Tetap = 'tetap';
    case Kontrak = 'kontrak';
    case Harian = 'harian';
    case PartTime = 'part_time';

    public function label(): string
    {
        return match ($this) {
            self::Tetap => 'Tetap',
            self::Kontrak => 'Kontrak',
            self::Harian => 'Harian',
            self::PartTime => 'Paruh Waktu (Part-time)',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Tetap => 'blue',
            self::Kontrak => 'purple',
            self::Harian => 'yellow',
            self::PartTime => 'gray',
        };
    }
}
