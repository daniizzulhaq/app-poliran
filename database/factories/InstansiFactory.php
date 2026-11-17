<?php

namespace Database\Factories;

use App\Enums\Role;
use App\Enums\StatusInstansi;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\Instansi> */
class InstansiFactory extends Factory
{
    public function definition(): array
    {
        return [
            'user_id' => fn () => User::factory()->create()->assignRole(Role::Instansi->value)->id,
            'nama' => fake()->company(),
            'jenis' => 'umkm',
            'penanggung_jawab' => fake()->name(),
            'alamat' => fake()->address(),
            'no_wa' => '081234567890',
            'jenis_dokumen' => 'ktp',
            'nomor_dokumen' => fake()->numerify('5302############'),
            'file_dokumen' => 'instansi/dokumen/contoh.jpg',
            'file_selfie' => 'instansi/selfie/contoh.jpg',
            'latitude' => -9.8606,
            'longitude' => 124.2836,
            'status' => StatusInstansi::MenungguSurvey,
            'diajukan_pada' => now(),
        ];
    }

    public function terverifikasi(): static
    {
        return $this->state(fn () => ['status' => StatusInstansi::Terverifikasi, 'disurvei_pada' => now()]);
    }
}
