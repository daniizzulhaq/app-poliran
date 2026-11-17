<?php

use App\Enums\Role;
use App\Models\Instansi;
use App\Models\JobListing;
use App\Models\PencariKerja;
use App\Models\User;
use Database\Seeders\DatabaseSeeder;

it('dashboard setiap role bisa dibuka', function () {
    $kec = buatKecamatan();
    PencariKerja::factory()->diKecamatan($kec)->create();
    JobListing::factory()->create();

    $this->actingAs(buatUser(Role::SuperAdmin))->get(route('admin.dashboard'))->assertOk()->assertSee('Pendaftar per Kecamatan');
    $this->actingAs(buatUser(Role::AdminSurvey))->get(route('admin.dashboard'))->assertOk()->assertSee('Antrian Survei Lowongan')->assertDontSee('Pendaftar per Kecamatan');
    $this->actingAs(buatUser(Role::AdminLapangan, [$kec->id]))->get(route('admin.dashboard'))->assertOk()->assertSee('Pendaftar Baru')->assertDontSee('Antrian Survei');
    $this->actingAs(Instansi::factory()->terverifikasi()->create()->user)->get(route('instansi.dashboard'))->assertOk()->assertSee('Status Verifikasi');
    $this->actingAs(buatUser(Role::PencariKerja))->get(route('pencari.dashboard'))->assertOk()->assertSee('Lengkapi Profil');
});

it('super admin bisa membuka laporan dan mengunduh Excel & PDF', function () {
    $kec = buatKecamatan();
    PencariKerja::factory()->diKecamatan($kec)->count(2)->create();
    $this->actingAs(buatUser(Role::SuperAdmin));

    $this->get(route('admin.laporan.index'))->assertOk()->assertSee($kec->nama);
    $this->get(route('admin.laporan.excel'))->assertOk()->assertDownload();
    $this->get(route('admin.laporan.pdf'))->assertOk()->assertHeader('content-type', 'application/pdf');
});

it('selain super admin tidak bisa membuka laporan', function () {
    foreach ([Role::AdminSurvey, Role::AdminLapangan, Role::Instansi, Role::PencariKerja] as $role) {
        $this->actingAs(buatUser($role))->get(route('admin.laporan.index'))->assertForbidden();
        $this->actingAs(buatUser($role))->get(route('admin.laporan.excel'))->assertForbidden();
    }
});

it('halaman error tampil dalam bahasa Indonesia', function () {
    $this->get('/halaman-tidak-ada')->assertNotFound()->assertSee('Halaman Tidak Ditemukan');
    $this->actingAs(buatUser(Role::PencariKerja))->get(route('admin.dashboard'))->assertForbidden()->assertSee('Akses Ditolak');
});

it('header keamanan terpasang', function () {
    $this->get(route('home'))->assertHeader('X-Frame-Options', 'SAMEORIGIN')->assertHeader('X-Content-Type-Options', 'nosniff');
});

it('seeder utama berjalan', function () {
    $this->seed(DatabaseSeeder::class);
    expect(User::where('email', 'superadmin@poliran.test')->first()->hasRole('super_admin'))->toBeTrue();
});
