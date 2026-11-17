<?php

namespace App\Enums;

enum StatusInstansi: string
{
    use Concerns\HasOptions;

    case MenungguSurvey = 'menunggu_survey';
    case Terverifikasi = 'terverifikasi';
    case Ditolak = 'ditolak';

    public function label(): string
    {
        return match ($this) {
            self::MenungguSurvey => 'Menunggu Survei',
            self::Terverifikasi => 'Terverifikasi',
            self::Ditolak => 'Ditolak',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::MenungguSurvey => 'yellow',
            self::Terverifikasi => 'green',
            self::Ditolak => 'red',
        };
    }
}
