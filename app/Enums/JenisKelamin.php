<?php

namespace App\Enums;

enum JenisKelamin: string
{
    use Concerns\HasOptions;

    case LakiLaki = 'L';
    case Perempuan = 'P';

    public function label(): string
    {
        return match ($this) {
            self::LakiLaki => 'Laki-laki',
            self::Perempuan => 'Perempuan',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::LakiLaki => 'blue',
            self::Perempuan => 'red',
        };
    }
}
