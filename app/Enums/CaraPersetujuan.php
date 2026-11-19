<?php

namespace App\Enums;

/** Cara warga memberi persetujuan atas penyimpanan & penggunaan datanya. */
enum CaraPersetujuan: string
{
    use Concerns\HasOptions;

    case Daring = 'daring';
    case Lisan = 'lisan';
    case Tertulis = 'tertulis';

    public function label(): string
    {
        return match ($this) {
            self::Daring => 'Daring (dicentang saat membuat akun)',
            self::Lisan => 'Lisan, disaksikan petugas',
            self::Tertulis => 'Tertulis (formulir bertanda tangan)',
        };
    }

    /** @return array<string, string> pilihan untuk input manual oleh petugas */
    public static function opsiPetugas(): array
    {
        return [self::Lisan->value => self::Lisan->label(), self::Tertulis->value => self::Tertulis->label()];
    }
}
