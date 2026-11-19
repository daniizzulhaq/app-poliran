<?php

namespace App\Enums;

/** Tempat kerja lowongan. Luar TTS & luar negeri rawan calo/TPPO, jadi legalitasnya wajib diperiksa saat survei. */
enum WilayahKerja: string
{
    use Concerns\HasOptions;

    case DalamTts = 'dalam_tts';
    case LuarTts = 'luar_tts';
    case LuarNegeri = 'luar_negeri';

    public function label(): string
    {
        return match ($this) {
            self::DalamTts => 'Dalam Kabupaten TTS',
            self::LuarTts => 'Luar TTS (dalam negeri)',
            self::LuarNegeri => 'Luar negeri',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::DalamTts => 'gray',
            self::LuarTts => 'yellow',
            self::LuarNegeri => 'red',
        };
    }

    public function perluCekLegalitas(): bool
    {
        return $this !== self::DalamTts;
    }

    /** @return array<int, string> hal yang wajib diperiksa petugas sebelum ACC */
    public function daftarPeriksa(): array
    {
        return match ($this) {
            self::DalamTts => [],
            self::LuarTts => [
                'Perusahaan dan alamat tempat kerja jelas serta bisa dihubungi.',
                'Ada kontrak/perjanjian kerja tertulis.',
                'Biaya perjalanan tidak dibebankan atau dipotong dari gaji pekerja.',
                'Pekerja tidak diminta menyerahkan KTP/ijazah asli.',
            ],
            self::LuarNegeri => [
                'Perusahaan penempatan (P3MI) memiliki izin resmi dan nomornya sah di sistem resmi pelindungan pekerja migran (BP2MI).',
                'Ada job order dan perjanjian kerja resmi; pekerja berangkat dengan visa kerja.',
                'Tidak ada biaya yang dibebankan di luar ketentuan resmi.',
                'Pekerja tidak diminta menyerahkan KTP/ijazah asli.',
            ],
        };
    }
}
