<?php

namespace App\Enums;

enum StatusLowongan: string
{
    use Concerns\HasOptions;

    case MenungguSurvey = 'menunggu_survey';
    case Aktif = 'aktif';
    case Ditolak = 'ditolak';
    case Terpenuhi = 'terpenuhi';
    case Ditutup = 'ditutup';
    case Kedaluwarsa = 'kedaluwarsa';

    public function label(): string
    {
        return match ($this) {
            self::MenungguSurvey => 'Menunggu Survei',
            self::Aktif => 'Aktif',
            self::Ditolak => 'Ditolak',
            self::Terpenuhi => 'Terpenuhi',
            self::Ditutup => 'Ditutup',
            self::Kedaluwarsa => 'Kedaluwarsa',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MenungguSurvey => 'yellow',
            self::Aktif => 'green',
            self::Ditolak => 'red',
            self::Terpenuhi => 'blue',
            self::Ditutup => 'gray',
            self::Kedaluwarsa => 'gray',
        };
    }
}
