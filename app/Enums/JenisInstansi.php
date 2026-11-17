<?php

namespace App\Enums;

enum JenisInstansi: string
{
    use Concerns\HasOptions;

    case Perusahaan = 'perusahaan';
    case Umkm = 'umkm';
    case Perorangan = 'perorangan';
    case Pemerintah = 'pemerintah';

    public function label(): string
    {
        return match ($this) {
            self::Perusahaan => 'Perusahaan',
            self::Umkm => 'UMKM',
            self::Perorangan => 'Perorangan',
            self::Pemerintah => 'Instansi Pemerintah',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Perusahaan => 'blue',
            self::Umkm => 'green',
            self::Perorangan => 'gray',
            self::Pemerintah => 'purple',
        };
    }
}
