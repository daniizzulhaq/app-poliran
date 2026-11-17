<?php

namespace Database\Factories;

use App\Enums\StatusPelatihan;
use App\Models\Kategori;
use App\Models\Kecamatan;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/** @extends Factory<\App\Models\Pelatihan> */
class PelatihanFactory extends Factory
{
    public function definition(): array
    {
        $nama = 'Pelatihan '.fake()->unique()->words(2, true);

        return [
            'kategori_id' => fn () => Kategori::firstOrCreate(['nama' => 'Umum'])->id,
            'kecamatan_id' => fn () => (Kecamatan::first() ?? Kecamatan::create(['nama' => 'Kec '.fake()->unique()->word()]))->id,
            'nama' => $nama,
            'slug' => Str::slug($nama),
            'deskripsi' => fake()->paragraph(),
            'penyelenggara' => 'Polres Timor Tengah Selatan',
            'tanggal_mulai' => now()->addWeeks(2)->toDateString(),
            'jam_mulai' => '08:00',
            'alamat' => 'Aula Polres',
            'kuota' => 10,
            'batas_pendaftaran' => now()->addWeek()->toDateString(),
            'bersertifikat' => true,
            'status' => StatusPelatihan::Dibuka,
        ];
    }
}
