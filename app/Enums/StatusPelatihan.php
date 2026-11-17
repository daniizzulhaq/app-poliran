<?php

namespace App\Enums;

enum StatusPelatihan: string
{
    use Concerns\HasOptions;

    case Draft = 'draft';
    case Dibuka = 'dibuka';
    case Ditutup = 'ditutup';
    case Selesai = 'selesai';

    public function label(): string
    {
        return match ($this) {
            self::Draft => 'Draft',
            self::Dibuka => 'Dibuka',
            self::Ditutup => 'Ditutup',
            self::Selesai => 'Selesai',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Draft => 'gray',
            self::Dibuka => 'green',
            self::Ditutup => 'yellow',
            self::Selesai => 'blue',
        };
    }
}
