<?php

use App\Enums\Role;
use App\Models\Kategori;
use App\Models\Kecamatan;
use App\Models\Skill;
use App\Models\User;

beforeEach(function () {
    $this->admin = buatUser(Role::SuperAdmin);
});

it('super admin bisa menambah kecamatan dan desa', function () {
    $this->actingAs($this->admin)->post(route('admin.kecamatan.store'), ['nama' => 'Kec Uji'])->assertRedirect();
    $kec = Kecamatan::where('nama', 'Kec Uji')->firstOrFail();

    $this->post(route('admin.desa.store', $kec), ['nama' => 'Desa Uji'])->assertRedirect();
    expect($kec->desa()->count())->toBe(1);
});

it('nama desa boleh sama di kecamatan berbeda, tapi tidak di kecamatan yang sama', function () {
    $a = buatKecamatan('A');
    $b = buatKecamatan('B');
    $this->actingAs($this->admin);

    $this->post(route('admin.desa.store', $a), ['nama' => 'Sama'])->assertSessionHasNoErrors();
    $this->post(route('admin.desa.store', $b), ['nama' => 'Sama'])->assertSessionHasNoErrors();
    $this->post(route('admin.desa.store', $a), ['nama' => 'Sama'])->assertSessionHasErrors('nama');
});

it('super admin bisa kelola kategori dan skill', function () {
    $this->actingAs($this->admin);
    $this->post(route('admin.kategori.store'), ['nama' => 'Perikanan'])->assertRedirect();
    $this->post(route('admin.skill.store'), ['nama' => 'Menyelam'])->assertRedirect();

    expect(Kategori::where('slug', 'perikanan')->exists())->toBeTrue()
        ->and(Skill::where('nama', 'Menyelam')->exists())->toBeTrue();
});

it('endpoint desa mengembalikan desa milik kecamatan', function () {
    $kec = buatKecamatan();
    $this->getJson(route('wilayah.desa', $kec))->assertOk()->assertJsonCount(1);
});

it('seeder membaca CSV wilayah', function () {
    $this->seed(\Database\Seeders\WilayahSeeder::class);
    expect(Kecamatan::count())->toBeGreaterThanOrEqual(2);
});

it('super admin bisa membuat admin lapangan dengan kecamatan', function () {
    $kec = buatKecamatan();
    $this->actingAs($this->admin)->post(route('admin.users.store'), [
        'name' => 'Petugas', 'email' => 'petugas@test.id', 'role' => 'admin_lapangan',
        'kecamatan' => [$kec->id], 'boleh_kelola_pelatihan' => '1', 'is_active' => '1',
        'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
    ])->assertRedirect(route('admin.users.index'));

    $user = User::where('email', 'petugas@test.id')->first();
    expect($user->hasRole('admin_lapangan'))->toBeTrue()
        ->and($user->kecamatanIds())->toBe([$kec->id])
        ->and($user->can('kelola-pelatihan'))->toBeTrue();
});

it('admin lapangan wajib diberi kecamatan', function () {
    $this->actingAs($this->admin)->post(route('admin.users.store'), [
        'name' => 'Petugas', 'email' => 'petugas@test.id', 'role' => 'admin_lapangan', 'is_active' => '1',
        'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors('kecamatan');
});
