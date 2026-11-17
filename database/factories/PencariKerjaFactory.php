<?php

namespace Database\Factories;

use App\Enums\StatusPendaftar;
use App\Models\Kecamatan;
use Illuminate\Database\Eloquent\Factories\Factory;

/** @extends Factory<\App\Models\PencariKerja> */
class PencariKerjaFactory extends Factory
{
    public function definition(): array
    {
        return [
            'nama' => fake()->name(),
            'nik' => fake()->unique()->numerify('5302############'),
            'jenis_kelamin' => fake()->randomElement(['L', 'P']),
            'tanggal_lahir' => now()->subYears(25)->toDateString(),
            'pendidikan' => 'sma',
            'no_wa' => '081234567890',
            'kecamatan_id' => fn () => (Kecamatan::first() ?? self::kecamatanBaru())->id,
            'desa_id' => fn (array $attr) => Kecamatan::findOrFail($attr['kecamatan_id'])->desa()->firstOrFail()->id,
            'alamat_detail' => 'RT 01 RW 02',
            'status' => StatusPendaftar::Terdaftar,
        ];
    }

    /** Pendaftar di kecamatan tertentu (desa pertama kecamatan itu). */
    public function diKecamatan(Kecamatan $kecamatan): static
    {
        return $this->state(fn () => [
            'kecamatan_id' => $kecamatan->id,
            'desa_id' => $kecamatan->desa()->firstOrFail()->id,
        ]);
    }

    private static function kecamatanBaru(): Kecamatan
    {
        $k = Kecamatan::create(['nama' => 'Kecamatan '.fake()->unique()->word()]);
        $k->desa()->create(['nama' => 'Desa '.fake()->unique()->word()]);

        return $k;
    }
}
