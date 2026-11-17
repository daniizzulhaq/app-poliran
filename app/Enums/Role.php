<?php

namespace App\Enums;

enum Role: string
{
    use Concerns\HasOptions;

    case SuperAdmin = 'super_admin';
    case AdminSurvey = 'admin_survey';
    case AdminLapangan = 'admin_lapangan';
    case Instansi = 'instansi';
    case PencariKerja = 'pencari_kerja';

    public function label(): string
    {
        return match ($this) {
            self::SuperAdmin => 'Super Admin',
            self::AdminSurvey => 'Admin Survei',
            self::AdminLapangan => 'Admin Lapangan',
            self::Instansi => 'Instansi / Penyedia Kerja',
            self::PencariKerja => 'Pencari Kerja',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SuperAdmin => 'red',
            self::AdminSurvey => 'purple',
            self::AdminLapangan => 'blue',
            self::Instansi => 'yellow',
            self::PencariKerja => 'green',
        };
    }

    /** @return array<int, self> */
    public static function adminRoles(): array
    {
        return [self::SuperAdmin, self::AdminSurvey, self::AdminLapangan];
    }
}
