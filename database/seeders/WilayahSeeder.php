<?php

namespace Database\Seeders;

use App\Services\WilayahImporter;
use Illuminate\Database\Seeder;

/**
 * Kecamatan & desa Kabupaten TTS dari database/data/wilayah_tts.csv
 * (salinan data API wilayah.id, diperbarui dengan `php artisan wilayah:impor --csv`).
 * Format: kode_kecamatan,nama_kecamatan,kode_desa,nama_desa (baris pertama = judul kolom)
 */
class WilayahSeeder extends Seeder
{
    public function run(WilayahImporter $importer): void
    {
        $path = database_path('data/wilayah_tts.csv');
        if (! is_file($path)) {
            $this->command?->warn('File wilayah_tts.csv tidak ditemukan, dilewati.');

            return;
        }

        $importer->simpan($importer->dariCsv($path));
    }
}
