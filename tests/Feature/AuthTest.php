<?php

use App\Enums\Role;
use App\Models\User;
use Illuminate\Auth\Notifications\ResetPassword;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Notification;

it('bisa masuk dan keluar', function () {
    $user = buatUser(Role::SuperAdmin, atribut: ['email' => 'a@a.test']);

    $this->post(route('login'), ['email' => 'a@a.test', 'password' => 'password'])->assertRedirect(route('dashboard'));
    $this->assertAuthenticatedAs($user);

    $this->post(route('logout'))->assertRedirect(route('home'));
    $this->assertGuest();
});

it('password salah ditolak', function () {
    buatUser(Role::SuperAdmin, atribut: ['email' => 'a@a.test']);
    $this->post(route('login'), ['email' => 'a@a.test', 'password' => 'salah'])->assertSessionHasErrors('email');
    $this->assertGuest();
});

it('pencari kerja bisa daftar akun dengan persetujuan data', function () {
    $this->post(route('register.pencari-kerja'), [
        'name' => 'Budi', 'email' => 'budi@test.id', 'phone' => '081234567890',
        'tanggal_lahir' => today()->subYearsNoOverflow(18)->toDateString(),
        'password' => 'rahasia123', 'password_confirmation' => 'rahasia123', 'persetujuan' => '1',
    ])->assertRedirect();

    $user = User::where('email', 'budi@test.id')->first();
    expect($user->hasRole('pencari_kerja'))->toBeTrue()
        ->and($user->persetujuan_data_at)->not->toBeNull()
        ->and($user->tanggal_lahir->toDateString())->toBe(today()->subYearsNoOverflow(18)->toDateString());

    $this->actingAs($user->fresh())->get(route('pencari.profil.edit'))->assertOk()
        ->assertSee('value="'.$user->tanggal_lahir->toDateString().'"', false);
});

it('daftar tanpa persetujuan ditolak', function () {
    $this->post(route('register.pencari-kerja'), [
        'name' => 'Budi', 'email' => 'budi@test.id', 'phone' => '081234567890',
        'tanggal_lahir' => today()->subYearsNoOverflow(20)->toDateString(),
        'password' => 'rahasia123', 'password_confirmation' => 'rahasia123',
    ])->assertSessionHasErrors('persetujuan');
});

it('pendaftaran akun menolak tanggal lahir yang belum memenuhi syarat', function (?string $tanggalLahir, string $rute) {
    $this->travelTo(Carbon::parse('2026-11-23 12:00:00'));

    $this->post(route($rute), [
        'name' => 'Budi', 'email' => 'budi@test.id', 'phone' => '081234567890',
        'tanggal_lahir' => $tanggalLahir,
        'password' => 'rahasia123', 'password_confirmation' => 'rahasia123', 'persetujuan' => '1',
    ])->assertSessionHasErrors('tanggal_lahir');

    $this->assertGuest();
    $this->assertDatabaseMissing('users', ['email' => 'budi@test.id']);
})->with([
    'belum ulang tahun ke-18' => '2008-11-24',
    'usia 17 tahun' => '2009-11-23',
    'tanggal di masa depan' => '2027-01-01',
    'tanggal tidak valid' => '2026-02-30',
    'tanggal kosong' => null,
    'bukan tanggal' => 'bukan-tanggal',
])->with(['register.pencari-kerja', 'register.instansi']);

it('form pendaftaran menjelaskan syarat usia dan KTP', function () {
    foreach (['register.pencari-kerja', 'register.instansi'] as $rute) {
        $this->get(route($rute))->assertOk()
            ->assertSee('name="tanggal_lahir"', false)
            ->assertSee('max="'.today()->subYearsNoOverflow(18)->toDateString().'"', false)
            ->assertSee('Usia minimal 18 tahun.')
            ->assertSee('sudah memiliki KTP')
            ->assertSee('Tulis nama lengkap sesuai KTP.');
    }
    $this->get(route('register'))->assertOk()->assertSee('berusia minimal 18 tahun');
});

it('instansi bisa daftar akun', function () {
    $this->post(route('register.instansi'), [
        'name' => 'Pak Toko', 'email' => 'toko@test.id', 'phone' => '081234567890',
        'tanggal_lahir' => today()->subYearsNoOverflow(18)->toDateString(),
        'password' => 'rahasia123', 'password_confirmation' => 'rahasia123', 'persetujuan' => '1',
    ])->assertRedirect();

    $user = User::where('email', 'toko@test.id')->first();
    expect($user->hasRole('instansi'))->toBeTrue()
        ->and($user->tanggal_lahir->toDateString())->toBe(today()->subYearsNoOverflow(18)->toDateString());
    $this->actingAs($user)->get(route('instansi.verifikasi.edit'))->assertOk()
        ->assertSee('value="'.$user->tanggal_lahir->toDateString().'"', false);
});

it('lupa password mengirim tautan reset', function () {
    Notification::fake();
    $user = buatUser(Role::PencariKerja);

    $this->post(route('password.email'), ['email' => $user->email])->assertSessionHas('success');
    Notification::assertSentTo($user, ResetPassword::class);
});
