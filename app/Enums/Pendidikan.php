<?php

namespace App\Enums;

enum Pendidikan: string
{
    use Concerns\HasOptions;

    case TidakSekolah = 'tidak_sekolah';
    case Sd = 'sd';
    case Smp = 'smp';
    case Sma = 'sma';
    case D3 = 'd3';
    case S1 = 's1';
    case S2 = 's2';

    public function label(): string
    {
        return match ($this) {
            self::TidakSekolah => 'Tidak Sekolah',
            self::Sd => 'SD',
            self::Smp => 'SMP',
            self::Sma => 'SMA/SMK',
            self::D3 => 'D3',
            self::S1 => 'S1',
            self::S2 => 'S2',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::TidakSekolah => 'gray',
            self::Sd => 'gray',
            self::Smp => 'gray',
            self::Sma => 'gray',
            self::D3 => 'gray',
            self::S1 => 'gray',
            self::S2 => 'gray',
        };
    }

    /** Tingkat pendidikan untuk perbandingan (makin besar makin tinggi). */
    public function tingkat(): int
    {
        return array_search($this, self::cases(), true);
    }

    public function memenuhi(?self $minimal): bool
    {
        return $minimal === null || $this->tingkat() >= $minimal->tingkat();
    }
}
