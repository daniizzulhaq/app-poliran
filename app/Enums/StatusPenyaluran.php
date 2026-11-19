<?php

namespace App\Enums;

/**
 * Tahapan penyaluran satu pendaftar ke satu lowongan:
 * Merekrut -> Melatih (Bersertifikat / Non Bersertifikat) -> Menyalurkan.
 * Pelatihan boleh dilewati (Merekrut -> Menyalurkan). Tidak Diterima = batal di tahap mana pun.
 */
enum StatusPenyaluran: string
{
    use Concerns\HasOptions;

    case Merekrut = 'merekrut';
    case Bersertifikat = 'bersertifikat';
    case NonBersertifikat = 'non_bersertifikat';
    case Menyalurkan = 'menyalurkan';
    case TidakDiterima = 'tidak_diterima';

    public function label(): string
    {
        return match ($this) {
            self::Merekrut => 'Merekrut',
            self::Bersertifikat => 'Melatih (Bersertifikat)',
            self::NonBersertifikat => 'Melatih (Non Bersertifikat)',
            self::Menyalurkan => 'Menyalurkan',
            self::TidakDiterima => 'Tidak Diterima',
        };
    }

    /** Teks pilihan pada dropdown "tahap berikutnya". */
    public function labelPilihan(): string
    {
        return match ($this) {
            self::Bersertifikat => 'Melatih - bersertifikat (lembaga formal)',
            self::NonBersertifikat => 'Melatih - non bersertifikat',
            self::Menyalurkan => 'Menyalurkan (diterima kerja)',
            self::TidakDiterima => 'Tidak diterima (batal)',
            default => $this->label(),
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::Merekrut => 'yellow',
            self::Bersertifikat => 'blue',
            self::NonBersertifikat => 'indigo',
            self::Menyalurkan => 'green',
            self::TidakDiterima => 'red',
        };
    }

    /** @return array<int, self> tahap yang boleh dipilih dari tahap ini */
    public function berikutnya(): array
    {
        return match ($this) {
            self::Merekrut => [self::Bersertifikat, self::NonBersertifikat, self::Menyalurkan, self::TidakDiterima],
            self::Bersertifikat, self::NonBersertifikat => [self::Menyalurkan, self::TidakDiterima],
            self::Menyalurkan, self::TidakDiterima => [],
        };
    }

    public function selesai(): bool
    {
        return $this->berikutnya() === [];
    }

    /** @return array<int, self> tahap yang masih berjalan */
    public static function berjalan(): array
    {
        return array_values(array_filter(self::cases(), fn (self $s) => ! $s->selesai()));
    }

    /** Status pendaftar yang sesuai dengan tahap ini (null = tidak mengubah). */
    public function statusPendaftar(): ?StatusPendaftar
    {
        return match ($this) {
            self::Merekrut => StatusPendaftar::Direkrut,
            self::Bersertifikat => StatusPendaftar::Bersertifikat,
            self::NonBersertifikat => StatusPendaftar::NonBersertifikat,
            self::Menyalurkan => StatusPendaftar::Disalurkan,
            self::TidakDiterima => null,
        };
    }
}
