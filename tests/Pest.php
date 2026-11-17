<?php

use App\Enums\Role;
use App\Models\Desa;
use App\Models\Kecamatan;
use App\Models\User;
use Database\Seeders\RolePermissionSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

pest()->extend(TestCase::class)
    ->use(RefreshDatabase::class)
    ->beforeEach(fn () => $this->seed(RolePermissionSeeder::class))
    ->in('Feature');

/** Buat user dengan role tertentu. Admin Lapangan bisa langsung diberi kecamatan. */
function buatUser(Role $role, array $kecamatanIds = [], array $atribut = []): User
{
    $user = User::factory()->create($atribut);
    $user->assignRole($role->value);
    if ($kecamatanIds) {
        $user->kecamatan()->sync($kecamatanIds);
    }

    return $user;
}

/** Buat kecamatan beserta satu desa. */
function buatKecamatan(?string $nama = null): Kecamatan
{
    $kecamatan = Kecamatan::create(['nama' => $nama ?? 'Kec '.fake()->unique()->word()]);
    Desa::create(['kecamatan_id' => $kecamatan->id, 'nama' => 'Desa '.fake()->unique()->word()]);

    return $kecamatan;
}
