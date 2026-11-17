<?php

namespace App\Enums;

enum JenisDokumen: string
{
    use Concerns\HasOptions;

    case Ktp = 'ktp';
    case Nib = 'nib';

    public function label(): string
    {
        return match ($this) {
            self::Ktp => 'KTP',
            self::Nib => 'NIB',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Ktp => 'gray',
            self::Nib => 'gray',
        };
    }
}
