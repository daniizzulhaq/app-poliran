<?php

namespace App\Enums;

enum StatusPendaftar: string
{
    use Concerns\HasOptions;

    case Terdaftar = 'terdaftar';
    case Direkrut = 'direkrut';
    case Dilatih = 'dilatih';
    case Bersertifikat = 'bersertifikat';
    case NonBersertifikat = 'non_bersertifikat';
    case Disalurkan = 'disalurkan';

    public function label(): string
    {
        return match ($this) {
            self::Terdaftar => 'Terdaftar',
            self::Direkrut => 'Direkrut',
            self::Dilatih => 'Dilatih',
            self::Bersertifikat => 'Bersertifikat',
            self::NonBersertifikat => 'Non Bersertifikat',
            self::Disalurkan => 'Disalurkan',
        };
    }

    /** Urutan kemajuan; dipakai agar penyaluran tidak menurunkan status pendaftar. */
    public function tingkat(): int
    {
        return match ($this) {
            self::Terdaftar => 0,
            self::Direkrut => 1,
            self::Dilatih => 2,
            self::NonBersertifikat => 3,
            self::Bersertifikat => 4,
            self::Disalurkan => 5,
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Terdaftar => 'gray',
            self::Direkrut => 'yellow',
            self::Dilatih => 'purple',
            self::Bersertifikat => 'blue',
            self::NonBersertifikat => 'indigo',
            self::Disalurkan => 'green',
        };
    }
}
