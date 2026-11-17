<?php

namespace Database\Seeders;

use App\Models\Desa;
use App\Models\Kecamatan;
use Illuminate\Database\Seeder;

/**
 * Membaca database/data/wilayah_tts.csv
 * Format: kode_kecamatan,nama_kecamatan,kode_desa,nama_desa (baris pertama = judul kolom)
 */
class WilayahSeeder extends Seeder
{
    public function run(): void
    {
        $path = database_path('data/wilayah_tts.csv');
        if (! is_file($path)) {
            $this->command?->warn('File wilayah_tts.csv tidak ditemukan, dilewati.');

            return;
        }

        $handle = fopen($path, 'r');
        fgetcsv($handle, escape: '\\');

        while (($row = fgetcsv($handle, escape: '\\')) !== false) {
            [$kodeKec, $namaKec, $kodeDesa, $namaDesa] = array_pad(array_map('trim', $row), 4, '');
            if ($namaKec === '') {
                continue;
            }

            $kecamatan = Kecamatan::firstOrCreate(['nama' => $namaKec], ['kode' => $kodeKec ?: null]);

            if ($namaDesa !== '') {
                Desa::firstOrCreate(
                    ['kecamatan_id' => $kecamatan->id, 'nama' => $namaDesa],
                    ['kode' => $kodeDesa ?: null],
                );
            }
        }

        fclose($handle);
    }
}
