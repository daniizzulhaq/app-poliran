<?php

namespace App\Services;

use App\Models\Pengaturan;
use Illuminate\Support\Facades\Cache;

/** Konten halaman awal yang bisa diubah Super Admin (disimpan di tabel pengaturan). */
class PengaturanService
{
    private const CACHE_KEY = 'pengaturan.semua';

    public const DEFAULTS = [
        'hero_judul' => 'Polisi Peduli Pengangguran',
        'hero_teks' => 'Program GRATIS dari Polres Timor Tengah Selatan untuk membantu warga TTS mendapatkan pelatihan dan pekerjaan. Tidak dipungut biaya apa pun.',
        'sambutan_nama' => 'Kristiyan Beorbel Martino, S.H., S.I.K.,M.M. C.ELM.',
        'sambutan_pangkat' => 'AKBP',
        'sambutan_isi' => "Selamat datang di POLIRAN.\n\nMelalui program ini, Polres Timor Tengah Selatan ingin membantu warga yang belum bekerja agar bisa mendapatkan pelatihan dan disalurkan ke pekerjaan yang layak.",
        'sambutan_foto' => null,
        'program_teks' => 'Poliran adalah singkatan dari Polisi Peduli Pengangguran, yaitu program Kepolisian Negara Republik Indonesia (Polri) untuk membantu masyarakat mendapatkan pekerjaan melalui pendataan pencari kerja, pelatihan keterampilan, dan penyaluran kerja bersama dunia usaha serta pemerintah daerah.',
        'kontak_alamat' => 'Polres Timor Tengah Selatan, SoE, Nusa Tenggara Timur',
        'kontak_telepon' => '-',
        'kontak_wa' => '-',
        'kontak_email' => '-',
        'kontak_latitude' => '-9.8606',
        'kontak_longitude' => '124.2836',
    ];

    /** @return array<string, string|null> */
    public function semua(): array
    {
        $tersimpan = Cache::rememberForever(self::CACHE_KEY, fn () => Pengaturan::pluck('nilai', 'kunci')->all());

        return array_merge(self::DEFAULTS, array_filter($tersimpan, fn ($v) => $v !== null && $v !== ''));
    }

    public function get(string $kunci, ?string $default = null): ?string
    {
        return $this->semua()[$kunci] ?? $default;
    }

    /** @param array<string, string|null> $data */
    public function simpan(array $data): void
    {
        foreach ($data as $kunci => $nilai) {
            if (! array_key_exists($kunci, self::DEFAULTS)) {
                continue;
            }
            Pengaturan::updateOrCreate(['kunci' => $kunci], ['nilai' => $nilai]);
        }
        Cache::forget(self::CACHE_KEY);
    }
}
