<?php

namespace App\Support;

use App\Enums\StatusPenyaluran;
use App\Models\Instansi;
use App\Models\JobListing;
use App\Models\PencariKerja;

/**
 * Template pesan WhatsApp untuk petugas. Tautan wa.me membuka WhatsApp dengan pesan yang sudah terisi;
 * petugas tetap bisa mengubahnya sebelum mengirim (aplikasi tidak mengirim pesan otomatis).
 */
class PesanWa
{
    private const PENUTUP = 'Program POLIRAN GRATIS. Petugas tidak pernah meminta uang dalam bentuk apa pun.';

    public static function url(?string $nomor, string $pesan): ?string
    {
        $wa = PencariKerja::formatWa($nomor);

        return $wa ? 'https://wa.me/'.$wa.'?text='.rawurlencode($pesan) : null;
    }

    /** Pesan ke pendaftar sesuai tahap penyaluran. */
    public static function penyaluranKePendaftar(PencariKerja $pencari, JobListing $lowongan, StatusPenyaluran $tahap): string
    {
        $kerja = $lowongan->judul.' di '.$lowongan->instansi->nama;
        $isi = match ($tahap) {
            StatusPenyaluran::Merekrut => "Bapak/Ibu kami rekomendasikan untuk lowongan {$kerja} (lokasi: {$lowongan->lokasi_kerja}). Mohon balas pesan ini agar kami jelaskan langkah selanjutnya.",
            StatusPenyaluran::Bersertifikat, StatusPenyaluran::NonBersertifikat => "Kami ingin menyampaikan jadwal dan tempat pelatihan untuk lowongan {$kerja}. Mohon balas pesan ini.",
            StatusPenyaluran::Menyalurkan => "Selamat, Bapak/Ibu diterima bekerja sebagai {$kerja}. Kami akan menghubungi Bapak/Ibu lagi beberapa bulan ke depan untuk menanyakan kabar pekerjaan.",
            StatusPenyaluran::TidakDiterima => "Mohon maaf, untuk lowongan {$kerja} belum berhasil. Data Bapak/Ibu tetap kami simpan untuk lowongan dan pelatihan berikutnya.",
        };

        return self::salam().' Bapak/Ibu '.$pencari->nama.'. Kami petugas POLIRAN Polres Timor Tengah Selatan. '.$isi."\n\n".self::PENUTUP;
    }

    /** Pesan ke instansi tentang pendaftar yang disalurkan. */
    public static function penyaluranKeInstansi(Instansi $instansi, PencariKerja $pencari, JobListing $lowongan): string
    {
        return self::salam().' Bapak/Ibu '.$instansi->penanggung_jawab.' ('.$instansi->nama.'). Kami petugas POLIRAN Polres Timor Tengah Selatan. '
            ."Kami menyalurkan Sdr/i {$pencari->nama} untuk lowongan {$lowongan->judul}. Mohon informasi jadwal wawancara atau mulai kerja. Terima kasih.";
    }

    /** Pesan tindak lanjut ke pekerja yang sudah disalurkan. */
    public static function tindakLanjut(PencariKerja $pencari, JobListing $lowongan, int $bulanKe): string
    {
        return self::salam().' Bapak/Ibu '.$pencari->nama.'. Kami petugas POLIRAN Polres Timor Tengah Selatan. '
            ."Sudah sekitar {$bulanKe} bulan Bapak/Ibu bekerja sebagai {$lowongan->judul} di {$lowongan->instansi->nama}. "
            .'Apakah masih bekerja di sana? Apakah ada kendala (gaji, jam kerja, perlakuan)? Mohon balas pesan ini. Terima kasih.'
            ."\n\n".self::PENUTUP;
    }

    private static function salam(): string
    {
        $jam = (int) now()->format('G');

        return match (true) {
            $jam < 11 => 'Selamat pagi',
            $jam < 15 => 'Selamat siang',
            $jam < 18 => 'Selamat sore',
            default => 'Selamat malam',
        };
    }
}
