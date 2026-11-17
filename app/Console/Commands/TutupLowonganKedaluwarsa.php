<?php

namespace App\Console\Commands;

use App\Models\JobListing;
use Illuminate\Console\Command;

class TutupLowonganKedaluwarsa extends Command
{
    protected $signature = 'lowongan:tutup-kedaluwarsa';

    protected $description = 'Tandai lowongan aktif yang lewat batas tanggal sebagai kedaluwarsa';

    public function handle(): int
    {
        $jumlah = JobListing::tandaiKedaluwarsa();
        $this->info("{$jumlah} lowongan ditandai kedaluwarsa.");

        return self::SUCCESS;
    }
}
