<?php

namespace App\Services;

use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use RuntimeException;

/**
 * Data kecamatan & desa Kabupaten Timor Tengah Selatan (kode Kemendagri 53.02).
 * Sumber: API wilayah.id, atau salinannya di database/data/wilayah_tts.csv agar instalasi tidak bergantung internet.
 * Impor hanya menambah yang belum ada; nama yang sudah diubah admin tidak ditimpa.
 *
 * @phpstan-type DataWilayah array<int, array{kode: string, nama: string, desa: array<int, array{kode: string, nama: string}>}>
 */
class WilayahImporter
{
    /** @return DataWilayah */
    public function dariApi(): array
    {
        $kabupaten = config('services.wilayah.kabupaten');
        $kecamatan = $this->get("districts/{$kabupaten}.json");

        return array_map(fn (array $k) => [
            'kode' => $k['code'],
            'nama' => $this->rapikanNama($k['name']),
            'desa' => array_map(
                fn (array $d) => ['kode' => $d['code'], 'nama' => $this->rapikanNama($d['name'])],
                $this->get("villages/{$k['code']}.json"),
            ),
        ], $kecamatan);
    }

    /** @return DataWilayah */
    public function dariCsv(string $path): array
    {
        $handle = fopen($path, 'r');
        fgetcsv($handle, escape: '\\');

        $hasil = [];
        while (($row = fgetcsv($handle, escape: '\\')) !== false) {
            [$kodeKec, $namaKec, $kodeDesa, $namaDesa] = array_pad(array_map('trim', $row), 4, '');
            if ($namaKec === '') {
                continue;
            }
            $kunci = $kodeKec !== '' ? $kodeKec : $namaKec;
            $hasil[$kunci] ??= ['kode' => $kodeKec, 'nama' => $namaKec, 'desa' => []];
            if ($namaDesa !== '') {
                $hasil[$kunci]['desa'][] = ['kode' => $kodeDesa, 'nama' => $namaDesa];
            }
        }
        fclose($handle);

        return array_values($hasil);
    }

    /**
     * @param  DataWilayah  $data
     * @return array{kecamatan: int, desa: int} jumlah data baru
     */
    public function simpan(array $data): array
    {
        $baru = ['kecamatan' => 0, 'desa' => 0];

        DB::transaction(function () use ($data, &$baru) {
            foreach ($data as $k) {
                $kecamatan = $this->cari(Kecamatan::query(), $k['kode'], $k['nama']) ?? new Kecamatan(['nama' => $k['nama']]);
                $baru['kecamatan'] += $kecamatan->exists ? 0 : 1;
                $kecamatan->kode = $k['kode'] ?: $kecamatan->kode;
                $kecamatan->save();

                foreach ($k['desa'] as $d) {
                    $desa = $this->cari($kecamatan->desa()->getQuery(), $d['kode'], $d['nama'])
                        ?? new Desa(['kecamatan_id' => $kecamatan->id, 'nama' => $d['nama']]);
                    $baru['desa'] += $desa->exists ? 0 : 1;
                    $desa->kode = $d['kode'] ?: $desa->kode;
                    $desa->save();
                }
            }
        });

        return $baru;
    }

    /** @param  DataWilayah  $data */
    public function tulisCsv(array $data, string $path): void
    {
        $handle = fopen($path, 'w');
        fputcsv($handle, ['kode_kecamatan', 'nama_kecamatan', 'kode_desa', 'nama_desa'], escape: '\\');
        foreach ($data as $k) {
            foreach ($k['desa'] as $d) {
                fputcsv($handle, [$k['kode'], $k['nama'], $d['kode'], $d['nama']], escape: '\\');
            }
        }
        fclose($handle);
    }

    /** Cari berdasarkan kode; bila belum berkode, cocokkan nama (tanpa beda huruf besar/kecil). */
    private function cari($query, string $kode, string $nama)
    {
        if ($kode !== '' && ($ketemu = (clone $query)->where('kode', $kode)->first())) {
            return $ketemu;
        }

        return (clone $query)->whereRaw('LOWER(nama) = ?', [mb_strtolower($nama)])->first();
    }

    /** "KI'E" / "SoE" -> "Ki'e" / "Soe" */
    private function rapikanNama(string $nama): string
    {
        return mb_convert_case(mb_strtolower(trim(preg_replace('/\s+/', ' ', $nama))), MB_CASE_TITLE);
    }

    /** @return array<int, array{code: string, name: string}> */
    private function get(string $path): array
    {
        $response = Http::baseUrl(config('services.wilayah.url'))
            ->acceptJson()
            ->timeout(20)
            ->retry(2, 500, throw: false)
            ->get($path);

        if (! $response->successful() || ! is_array($response->json('data'))) {
            throw new RuntimeException("Gagal mengambil data wilayah ({$path}): HTTP {$response->status()}.");
        }

        return $response->json('data');
    }
}
