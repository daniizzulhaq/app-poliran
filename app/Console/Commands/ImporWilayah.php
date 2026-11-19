<?php

namespace App\Console\Commands;

use App\Services\WilayahImporter;
use Illuminate\Console\Command;
use Throwable;

class ImporWilayah extends Command
{
    protected $signature = 'wilayah:impor {--csv : Simpan juga salinannya ke database/data/wilayah_tts.csv}';

    protected $description = 'Ambil kecamatan & desa Kabupaten TTS dari API wilayah (kode Kemendagri) dan tambahkan yang belum ada';

    public function handle(WilayahImporter $importer): int
    {
        $this->info('Mengambil data wilayah dari '.config('services.wilayah.url').' ...');

        try {
            $data = $importer->dariApi();
        } catch (Throwable $e) {
            $this->error($e->getMessage());

            return self::FAILURE;
        }

        $baru = $importer->simpan($data);
        $jumlahDesa = array_sum(array_map(fn ($k) => count($k['desa']), $data));
        $this->info(count($data)." kecamatan, {$jumlahDesa} desa/kelurahan diterima. Baru ditambahkan: {$baru['kecamatan']} kecamatan, {$baru['desa']} desa.");

        if ($this->option('csv')) {
            $importer->tulisCsv($data, database_path('data/wilayah_tts.csv'));
            $this->info('Salinan disimpan ke database/data/wilayah_tts.csv');
        }

        return self::SUCCESS;
    }
}
