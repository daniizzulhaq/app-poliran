<?php

namespace Database\Factories;

use App\Enums\StatusLowongan;
use App\Models\Instansi;
use App\Models\Kategori;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\JobListing> */
class JobListingFactory extends Factory
{
    public function definition(): array
    {
        return [
            'instansi_id' => fn () => Instansi::factory()->terverifikasi(),
            'kategori_id' => fn () => Kategori::firstOrCreate(['nama' => 'Umum'])->id,
            'judul' => fake()->jobTitle(),
            'jobdesk' => fake()->sentence(),
            'gaji_min' => 2000000,
            'gaji_max' => 3000000,
            'gaji_nego' => false,
            'tipe_kerja' => 'kontrak',
            'lokasi_kerja' => 'SoE',
            'kuota' => 2,
            'jumlah_tersalur' => 0,
            'batas_tanggal' => now()->addMonth()->toDateString(),
            'status' => StatusLowongan::MenungguSurvey,
        ];
    }

    public function aktif(): static
    {
        return $this->state(fn () => ['status' => StatusLowongan::Aktif, 'disurvei_pada' => now()]);
    }
}
